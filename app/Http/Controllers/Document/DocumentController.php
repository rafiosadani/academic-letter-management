<?php

namespace App\Http\Controllers\Document;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Document\UploadDocumentRequest;
use App\Models\Document;
use App\Models\LetterRequest;
use App\Services\DocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected DocumentService $documentService,
    ) {}

    public function upload(UploadDocumentRequest $request): JsonResponse
    {
        $this->authorize('upload', Document::class);

        $letter = LetterRequest::findOrFail($request->letter_request_id);
        $uploadedDocuments = [];

        foreach ($request->file('files') as $file) {
            $document = $this->documentService->uploadSupporting(
                $file,
                $letter,
                auth()->user(),
            );

            $uploadedDocuments[] = [
                'id' => $document->id,
                'name' => $document->file_name,
                'size' => $document->file_size_formatted,
                'icon' => $document->icon,
            ];
        }

        LogHelper::logSuccess('uploaded', 'document', [
            'letter_request_id' => $letter->id,
            'document_count' => count($uploadedDocuments),
            'files' => array_column($uploadedDocuments, 'name'),
        ], $request);

        return response()->json([
            'success' => true,
            'message' => count($uploadedDocuments) . ' file(s) berhasil diupload',
            'documents' => $uploadedDocuments,
        ]);
    }

    /**
     * Upload external document (e.g., SKAK from UB Pusat).
     */
    public function uploadExternal(Request $request): RedirectResponse
    {
        $this->authorize('uploadExternal', Document::class);

        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf',
            'letter_request_id' => 'required|exists:letter_requests,id',
        ]);

        $letter = LetterRequest::findOrFail($request->letter_request_id);

        $document = $this->documentService->uploadExternal(
            $request->file('file'),
            $letter,
            auth()->user()
        );

        LogHelper::logSuccess('uploaded', 'external document', [
            'document_id' => $document->id,
            'letter_request_id' => $letter->id,
            'file_name' => $document->file_name,
            'category' => 'external',
        ], $request);

        return redirect()
            ->back()
            ->with('notification_data', [
                'type' => 'success',
                'text' => 'Dokumen external berhasil diupload!',
                'position' => 'center-top',
                'duration' => 3000,
            ]);
    }

    /**
     * Download document.
     */
    public function download(Document $document): StreamedResponse
    {
        $this->authorize('download', $document);

        return $this->documentService->download($document);
    }

    /**
     * Stream document for preview (PDF only).
     */
    public function stream(Document $document): StreamedResponse
    {
        $this->authorize('download', $document);

        return $this->documentService->stream($document);
    }

    /**
     * Delete document (soft delete).
     */
    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        LogHelper::logSuccess('deleted', 'document', [
            'document_id' => $document->id,
            'file_name' => $document->file_name,
            'category' => $document->category,
            'letter_request_id' => $document->letter_request_id,
        ]);

        $this->documentService->delete($document);

        return redirect()
            ->back()
            ->with('notification_data', [
                'type' => 'success',
                'text' => 'Dokumen berhasil dihapus',
                'position' => 'center-top',
                'duration' => 3000,
            ]);
    }

    /**
     * Verify document by hash (public endpoint, no auth).
     */
    public function verify(string $hash): View
    {
        $document = $this->documentService->verifyHash($hash);

        if (!$document) {
            return view('documents.verify', [
                'valid' => false,
                'message' => 'Dokumen tidak ditemukan atau hash tidak valid',
            ]);
        }

        $letter = $document->letterRequest;

        return view('documents.verify', [
            'valid' => true,
            'document' => $document,
            'letter' => $letter,
        ]);
    }
}
