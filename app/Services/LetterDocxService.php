<?php

namespace App\Services;

use App\Enums\LetterType;
use App\Enums\OfficialPosition;
use App\Models\FacultyOfficial;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class LetterDocxService
{
    public static function validateWDData(): void
    {
        $wd = FacultyOfficial::where('position', OfficialPosition::WAKIL_DEKAN_AKADEMIK)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->with('user.profile')
            ->first();

        if (!$wd || !$wd->user?->profile) {
            throw new \Exception('Data Wakil Dekan tidak ditemukan. Silakan hubungi administrator untuk melengkapi data pejabat.');
        }

        $nip = $wd->user->profile->student_or_employee_id;
        if (empty($nip)) {
            throw new \Exception('NIP Wakil Dekan Bidang Akademik belum diisi. Silakan hubungi administrator untuk melengkapi data di menu Pejabat Fakultas.');
        }

        if (empty($wd->rank)) {
            throw new \Exception('Pangkat/Golongan Wakil Dekan Bidang Akademik belum diisi. Silakan hubungi administrator untuk melengkapi data di menu Pejabat Fakultas.');
        }
    }

    /**
     * Generate DOCX from template for a letter request.
     */
    public function generate(LetterRequest $letter): string
    {
        self::validateWDData();

        $letterType = $letter->letter_type;

        if (!$letterType->isExternal()) {
            throw new \Exception('Pembuatan dokumen DOCX hanya diperuntukkan bagi surat eksternal.');
        }

        $templatePath = $this->getTemplatePath($letter->letter_type);

        if (!file_exists($templatePath)) {
            throw new \Exception("Template tidak ditemukan: {$templatePath}");
        }

        // Load template
        $templateProcessor = new TemplateProcessor($templatePath);

        // Get placeholder values
        $placeholders = $this->getPlaceholderValues($letter);

        // Replace placeholders (except ${nomor} and ${tte})
        foreach ($placeholders as $placeholder => $value) {
            $templateProcessor->setValue($placeholder, $value);
        }

        // Generate filename
        $filename = $this->generateFilename($letter);

        $date = $letter->created_at ?? now();
        $directory = storage_path(sprintf(
            'app/private/documents/letters/%s/%s/generated',
            $date->format('Y'),
            $date->format('m')
        ));

        // Create directory if not exists
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $outputPath = "{$directory}/{$filename}";

        // Save DOCX
        $templateProcessor->saveAs($outputPath);

        return $outputPath;
    }

    /**
     * Format text for official document (Title Case).
     */
    private function formatForDocument(string $text): string
    {
        if (empty($text) || $text === '-') {
            return $text;
        }

        return ucwords(strtolower($text));
    }

    /**
     * Get template path based on letter type.
     */
    private function getTemplatePath(LetterType $letterType): string
    {
        return resource_path("templates/letters/docx/" . $letterType->templateFile());
    }

    /**
     * Get all placeholder values for the letter.
     */
    private function getPlaceholderValues(LetterRequest $letter): array
    {
        $student = $letter->student;
        $profile = $student->profile;

        if (!$profile) {
            throw new \Exception("Profil mahasiswa tidak ditemukan.");
        }

        // Get WD Akademik data
        $wdData = $this->getWakilDekanData();

        // Common values for all letters
        $values = [
            // Student data - FORMATTED
            'student_name' => $this->formatForDocument($profile->full_name),
            'student_nim' => $profile->student_or_employee_id,
            'study_program' => $this->formatForDocument($profile->studyProgram->degree_name ?? '-'),
            'place_birth' => $this->formatForDocument($profile->place_of_birth ?? '-'),
            'date_birth' => $profile->date_of_birth?->translatedFormat('d F Y') ?? '-',

            // Academic data
            'semester' => $this->formatForDocument($letter->semester->semester_type->value ?? '-'),
            'academic_year' => $this->formatForDocument($letter->academicYear->year_label),
            'year_entry' => $this->calculateYearEntry($student),

            // System data
            'letter_date' => now()->translatedFormat('d F Y'),

            // WD Data - FORMATTED
            'wd_name' => $wdData['name'],
            'wd_nip' => $wdData['nip'],
            'wd_rank' => $wdData['rank'],
        ];


        // Letter type specific values
        $additionalValues = match ($letter->letter_type) {
            LetterType::SKAK => [
                'keperluan' => $this->formatForDocument($letter->data_input['keperluan'] ?? '-'),
            ],
            LetterType::SKAK_TUNJANGAN => [
                'parent_name' => $letter->data_input['parent_name'] ?? '-',
                'parent_nip' => $letter->data_input['parent_nip'] ?? '-',
                'parent_rank' => $letter->data_input['parent_rank'] ?? '-',
                'parent_institution' => $this->formatForDocument($letter->data_input['parent_institution'] ?? '-'),
                'parent_address' => $this->formatForDocument($letter->data_input['parent_institution_address'] ?? '-'),
            ],
            default => [],
        };

        return array_merge($values, $additionalValues);
    }

    /**
     * Get current Wakil Dekan Akademik data.
     */
    private function getWakilDekanData(): array
    {
        $wd = FacultyOfficial::where('position', OfficialPosition::WAKIL_DEKAN_AKADEMIK)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->with('user.profile')
            ->first();

        if (!$wd || !$wd->user?->profile) {
            return [
                'name' => 'Dr. A. Faidlal Rahman, SE.Par., M.Sc., CHE.',
                'nip' => '2012018202081001',
                'rank' => 'Pembina Tingkat I / IV b',
            ];
        }

        return [
            'name' => $wd->user->profile->full_name,
            'nip' => $wd->user->profile->student_or_employee_id,
            'rank' => $wd->rank ?? 'Pembina Tingkat I / IV b',
        ];
    }

    /**
     * Calculate year entry from student NIM.
     */
    private function calculateYearEntry($student): string
    {
        $nim = $student->profile->student_or_employee_id;

        if (!$nim || strlen($nim) < 2) {
            return '-';
        }

        $yearCode = substr($nim, 0, 2);

        if (!$yearCode || !is_numeric($yearCode)) {
            return '-';
        }

        $year = 2000 + (int)$yearCode;
        $nextYear = $year + 1;

        return "Ganjil {$year}/{$nextYear}";
    }

    /**
     * Generate filename for DOCX.
     */
    private function generateFilename(LetterRequest $letter): string
    {
        $typeLabel = $letter->letter_type->labelFileName();
        $studentName = $letter->student->profile->full_name;
        $purpose = $letter->data_input['keperluan'] ?? 'Umum';

        // Clean keperluan
        $purpose = preg_replace('/[^\p{L}\p{N}\s\(\)\-]/u', '', $purpose);
        $purpose = trim(preg_replace('/\s+/', ' ', $purpose));

        return "{$typeLabel} {$studentName} ({$purpose}).docx";
    }

    /**
     * Get download URL for generated DOCX.
     */
    public function getDownloadUrl(string $path): string
    {
        return route('letters.download-docx', ['path' => base64_encode($path)]);
    }
}