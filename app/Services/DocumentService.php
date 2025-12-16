<?php

namespace App\Services;

use App\Models\Document;
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
        $fileData = $this->storeFile($file, $letter);

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
        ]);
    }

    /**
     * Upload external final document (e.g., SKAK from UB Pusat).
     */
    public function uploadExternal(
        UploadedFile $file,
        LetterRequest $letter,
        User $user
    ): Document {
        $fileData = $this->storeFile($file, $letter);
        $hash = $this->generateHash($letter, $fileData['path']);

        return Document::create([
            'letter_request_id' => $letter->id,
            'uploaded_by' => $user->id,
            'category' => 'external',
            'type' => 'final',
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $fileData['path'],
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'hash' => $hash,
        ]);
    }

    /**
     * Store generated PDF (system-generated).
     */
    public function storeGenerated(
        string $pdfContent,
        LetterRequest $letter,
        string $type = 'final'
    ): Document {
        $fileName = $this->generateFileName($letter, $type);
        $filePath = $this->generateStoragePath($letter) . '/' . $fileName;

        // Store file
        Storage::put($filePath, $pdfContent);

        // Generate hash for final documents
        $hash = $type === 'final' ? $this->generateHash($letter, $filePath) : null;

        return Document::create([
            'letter_request_id' => $letter->id,
            'uploaded_by' => null, // System generated
            'category' => 'generated',
            'type' => $type,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => strlen($pdfContent),
            'mime_type' => 'application/pdf',
            'hash' => $hash,
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

        return Storage::download(
            $document->file_path,
            $document->download_name
        );
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
        return $document->delete();
    }

    /**
     * Verify document by hash.
     */
    public function verifyHash(string $hash): ?Document
    {
        return Document::where('hash', $hash)
            ->whereNotNull('hash')
            ->first();
    }

    /**
     * Store uploaded file to storage.
     *
     * @return array ['path' => string, 'filename' => string]
     */
    private function storeFile(UploadedFile $file, LetterRequest $letter): array
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $directory = $this->generateStoragePath($letter);

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
    private function generateStoragePath(LetterRequest $letter): string
    {
        $date = $letter->created_at ?? now();
        return sprintf(
            'documents/letters/%s/%s',
            $date->format('Y'),
            $date->format('m')
        );
    }

    /**
     * Generate filename for system-generated documents.
     */
    private function generateFileName(LetterRequest $letter, string $type): string
    {
        $prefix = $type === 'draft' ? 'draft' : 'final';
        $uuid = Str::uuid();
        return sprintf('%s_%s.pdf', $prefix, $uuid);
    }

    /**
     * Generate hash for document verification.
     */
    private function generateHash(LetterRequest $letter, string $filePath): string
    {
        $data = implode('|', [
            $letter->id,
            $letter->letter_number ?? 'pending',
            $letter->student_id,
            now()->timestamp,
            $filePath,
            config('app.key'),
        ]);

        return hash('sha256', $data);
    }
}