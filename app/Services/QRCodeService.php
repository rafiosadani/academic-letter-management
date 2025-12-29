<?php

namespace App\Services;

use App\Helpers\LogHelper;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    public function generateForLetter(string $data, int $letterId, int $size = 250): string
    {
        try {
            $qrCode = new QrCode(
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Medium,
                size: $size,
                margin: 2,
                roundBlockSizeMode: RoundBlockSizeMode::Enlarge,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255, 127)
            );


            $writer = new PngWriter();

            $logoPath = $this->getLogoPath();
            $logo = null;

            // Add logo if available
            if ($logoPath && file_exists($logoPath)) {
                $logoSize = (int)($size * 0.15); // 20% of QR size
                $logo = new Logo(
                    path: $logoPath,
                    resizeToWidth: $logoSize,
                    resizeToHeight: $logoSize,
                    punchoutBackground: true
                );
            }

            $result = $writer->write(
                qrCode: $qrCode,
                logo: $logo,
            );

            $filename = "letter_{$letterId}_" . now()->format('YmdHis') . ".png";
            $storagePath = "qrcodes/{$filename}";

            // Create directory if not exists
            $directory = storage_path('app/qrcodes');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save file
            file_put_contents(
                storage_path("app/{$storagePath}"),
                $result->getString()
            );

            // LOG SUCCESS
            LogHelper::logSuccess('generated', 'qrcode', [
                'letter_id' => $letterId,
                'file_path' => $storagePath,
                'size' => $size,
                'has_logo' => $logoPath !== null,
            ]);

            // Return data URI for PDF embedding
            return $result->getDataUri();
        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('generate', 'qrcode', $e, [
                'letter_id' => $letterId,
                'data_length' => strlen($data),
            ]);

            // Throw exception - PDF generation should fail if QR fails
            throw new \Exception("Failed to generate QR code: " . $e->getMessage());
        }

    }

    /**
     * Get logo path for QR code center.
     * Priority:
     * 1. Settings (header_logo)
     * 2. Default logo (public/images/logo-ub.png)
     * 3. Null (no logo)
     */
    private function getLogoPath(): ?string
    {
        $logoSetting = setting('header_logo');

        if ($logoSetting) {
            $path = public_path('storage/' . $logoSetting);
            if (file_exists($path)) {
                return $path;
            }
        }

        // Fallback to default logo if exists
        $defaultPath = public_path('assets/images/logo-ub.png');
        if (file_exists($defaultPath)) {
            return $defaultPath;
        }

        LogHelper::logWarning('qrcode', [
            'message' => 'QR Code logo not found. QR will be generated without logo.',
            'checked_paths' => [
                'setting' => $logoSetting ? public_path('storage/' . $logoSetting) : null,
                'default' => $defaultPath,
            ],
        ]);

        return null;
    }

    /**
     * Delete QR code files for a specific letter.
     */
    public function delete(int $letterId): void
    {
        try {
            $files = Storage::files('qrcodes');
            $deletedCount = 0;

            foreach ($files as $file) {
                if (str_contains($file, "letter_{$letterId}_")) {
                    Storage::delete($file);
                    $deletedCount++;
                }
            }

            // LOG SUCCESS
            LogHelper::logSuccess('deleted', 'qrcode', [
                'letter_id' => $letterId,
                'files_deleted' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete QR code for letter {$letterId}: " . $e->getMessage());
        }
    }

    /**
     * Generate QR code from physical file path (for testing).
     *
     * @param string $data Data to encode
     * @param string $outputPath Full path where to save PNG
     * @param int $size QR size
     * @return string File path
     */
    public function generateToFile(string $data, string $outputPath, int $size = 250): string
    {
        try {
            $logoPath = $this->getLogoPath();

            $qrCode = new QrCode(
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: $size,
                margin: 2,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );

            $writer = new PngWriter();
            $logo = null;

            if ($logoPath && file_exists($logoPath)) {
                $logoSize = (int)($size * 0.15);
                $logo = new Logo(
                    path: $logoPath,
                    resizeToWidth: $logoSize,
                    resizeToHeight: $logoSize,
                    punchoutBackground: true
                );
            }

            $result = $writer->write(
                qrCode: $qrCode,
                logo: $logo
            );

            // Create directory if needed
            $directory = dirname($outputPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($outputPath, $result->getString());

            // LOG SUCCESS
            LogHelper::logSuccess('generated', 'qrcode_file', [
                'output_path' => $outputPath,
                'size' => $size,
                'has_logo' => $logoPath !== null,
            ]);

            return $outputPath;

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('generate', 'qrcode_file', $e, [
                'output_path' => $outputPath,
            ]);

            throw $e;
        }
    }
}