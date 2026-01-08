<?php

namespace App\Services;

use App\Helpers\LogHelper;
use App\Models\Document;
use App\Models\LetterNumberConfig;
use App\Models\LetterRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{
    public function uploadSupporting(
        UploadedFile $file,
        LetterRequest $letter,
        User $user
    ): Document {
        $fileData = $this->storeFile($file, $letter, 'supporting');

        return Document::create([
            'letter_request_id' => $letter->id,
            'uploaded_by' => $user->id,
            'category' => 'supporting',
            'type' => null,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $fileData['path'],
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'hash' => null,
            'file_hash' => null,
        ]);
    }

    /**
     * Upload external final document (e.g., SKAK from UB Pusat).
     */
    public function uploadExternal(
        UploadedFile $file,
        LetterRequest $letter,
        string $letterNumber,
        User $user
    ): Document {
        $filename = $this->generatePdfFilename($letter, $letterNumber);
        $fileData = $this->storeFile($file, $letter, 'external', $filename);

        return Document::create([
            'letter_request_id' => $letter->id,
            'uploaded_by' => $user->id,
            'category' => 'external',
            'type' => 'final',
            'file_name' => $filename,
            'file_path' => $fileData['path'],
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'hash' => null,
            'file_hash' => null,
        ]);
    }

    public function storeGeneratedDocx(
        string $filePath,
        LetterRequest $letter,
    ): Document {
        if (!file_exists($filePath)) {
            throw new \Exception("File DOCX tidak ditemukan di lokasi penyimpanan: {$filePath}");
        }

        $fileSize = filesize($filePath);

        // Extract relative path from physical path
        // Example: /var/www/storage/app/documents/... -> documents/...
        $storagePath = str_replace(storage_path('app/private'), '', $filePath);

        $filename = basename($filePath);

        return Document::create([
            'letter_request_id' => $letter->id,
            'uploaded_by' => null,
            'category' => 'generated',
            'type' => 'draft',
            'file_name' => $filename,
            'file_path' => $storagePath,
            'file_size' => $fileSize,
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'hash' => null,
            'file_hash' => null,
        ]);
    }

    public function storeGeneratedPdf(
        string $filePath,
        LetterRequest $letter,
        string $hash,
        ?string $fileHash = null
    ): Document {
        if (!file_exists($filePath)) {
            throw new \Exception("File PDF tidak ditemukan di lokasi penyimpanan: {$filePath}");
        }

        $fileSize = filesize($filePath);

        // Extract relative path (remove storage/app/private/ prefix)
        $storagePath = str_replace(storage_path('app/private') . '/', '', $filePath);

        $filename = basename($filePath);

        return Document::create([
            'letter_request_id' => $letter->id,
            'uploaded_by' => null,
            'category' => 'generated',
            'type' => 'final',
            'file_name' => $filename,
            'file_path' => $storagePath,
            'file_size' => $fileSize,
            'mime_type' => 'application/pdf',
            'hash' => $hash,
            'file_hash' => $fileHash,
        ]);
    }

    /**
     * Download document.
     */
    public function download(Document $document): StreamedResponse
    {
        if (!$document->fileExists()) {
            abort(404, 'File not found in storage');
        }

        return Storage::download($document->file_path, $document->file_name);
    }

    /**
     * Stream document (for preview).
     */
    public function stream(Document $document): StreamedResponse
    {
        if (!$document->fileExists()) {
            abort(404, 'File not found in storage');
        }

        return Storage::response($document->file_path);
    }

    /**
     * Delete document (soft delete).
     */
    public function delete(Document $document): bool
    {
        try {
            if ($document->file_path && Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }

            return $document->forceDelete();
        } catch (\Exception $e) {
            LogHelper::logError('delete', 'document', $e, [
                'document_id' => $document->id,
                'file_path' => $document->file_path,
                'error' => $e->getMessage(),
            ]);

            // Force delete DB record anyway
            return $document->forceDelete();
        }
    }

    /**
     * Verify document by hash.
     */
    public function verifyHash(string $hash): ?Document
    {
        return Document::where('hash', $hash)
            ->with([
                'letterRequest.student.profile.studyProgram',
                'letterRequest.semester',
                'letterRequest.academicYear',
                'letterRequest.approvals' => function ($query) {
                    $query->where('status', 'approved')
                        ->orderBy('step')
                        ->with(['approver.profile']);
                }
            ])
            ->first();
    }

    /**
     * Store uploaded file to storage.
     *
     * @return array ['path' => string, 'filename' => string]
     */
    private function storeFile(
        UploadedFile $file,
        LetterRequest $letter,
        ?string $category = null,
        ?string $customFileName = null
    ): array {
        $extension = $file->getClientOriginalExtension();
        $filename = $customFileName ?? Str::uuid() . '.' . $extension;
        $directory = $this->generateStoragePath($letter, $category);

        $path = $file->storeAs($directory, $filename);

        return [
            'path' => $path,
            'filename' => $filename,
        ];
    }

    /**
     * Generate storage path based on letter and date.
     * Format: documents/letters/{year}/{month}
     */
    private function generateStoragePath(LetterRequest $letter, ?string $category = null): string
    {
        $date = $letter->created_at ?? now();
        $basePath = sprintf(
            'documents/letters/%s/%s',
            $date->format('Y'),
            $date->format('m')
        );

        // Add subfolder based on category
        if ($category === 'supporting') {
            return $basePath . '/supporting';
        } elseif ($category === 'generated') {
            return $basePath . '/generated';
        } elseif ($category === 'external') {
            return $basePath . '/final';
        }

        return $basePath;
    }

    /**
     * Generate PDF filename with letter number prefix.
     */
    private function generatePdfFilename(LetterRequest $letter, string $letterNumber): string
    {
        // Get padding from config
        $letterNumberConfig = LetterNumberConfig::where('letter_type', $letter->letter_type->value)->first();
        $padding = $letterNumberConfig?->padding ?? 5;
        $counter = substr($letterNumber, 0, $padding);

        // Get components
        $typeLabel = $letter->letter_type ? $letter->letter_type->labelFileName() : 'Surat';
        $studentName = $letter->student->profile->full_name;
        $purpose = $this->cleanFilename($letter->data_input['keperluan'] ?? 'Umum');

        return "{$counter} {$typeLabel} {$studentName} ({$purpose}).pdf";
    }

    private function cleanFilename(string $text): string
    {
        // Remove special chars except spaces, parentheses, hyphen
        $text = preg_replace('/[^\p{L}\p{N}\s\(\)\-]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}