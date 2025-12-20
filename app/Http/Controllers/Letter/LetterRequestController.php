<?php

namespace App\Http\Controllers\Letter;

use App\Enums\LetterType;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Letter\StoreLetterRequestRequest;
use App\Http\Requests\Letter\UpdateLetterRequestRequest;
use App\Models\AcademicYear;
use App\Models\LetterRequest;
use App\Models\Semester;
use App\Services\DocumentService;
use App\Services\LetterRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LetterRequestController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected LetterRequestService $letterRequestService,
        protected DocumentService $documentService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', LetterRequest::class);

        $filters = $request->only(['letter_type', 'status', 'search']);

        $letters = $this->letterRequestService->getStudentLetters(
            auth()->user(),
            $filters
        );

        $letterTypes = LetterType::cases();

        return view('letters.index', compact('letters', 'letterTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $this->authorize('create', LetterRequest::class);

        // Validate active semester and academic year exist
        $activeSemester = Semester::where('is_active', true)->first();
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        if (!$activeSemester || !$activeAcademicYear) {
            return redirect()->route('letters.index')
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Tidak dapat mengajukan surat. Semester atau tahun akademik aktif belum diatur. Silakan hubungi admin.',
                    'position' => 'center-top',
                    'duration' => 6000,
                ]);
        }

        $letterTypeValue = $request->query('type');

        if (!$letterTypeValue) {
            return view('letters.select-type', [
                'letterTypes' => LetterType::cases(),
            ]);
        }

        $letterType = LetterType::tryFrom($letterTypeValue);

        if (!$letterType) {
            return redirect()->route('letters.create')
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Jenis surat tidak valid',
                    'position' => 'center-top',
                    'duration' => 3000,
                ]);
        }

        // Check if student can submit this letter type
        $canSubmit = $this->letterRequestService->canSubmitLetterType(auth()->user(), $letterType);

        if (!$canSubmit['can_submit']) {
            return redirect()->route('letters.create')
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => $canSubmit['reason'],
                    'position' => 'center-top',
                    'duration' => 4000,
                ])
                ->with('alert_data', [
                    'type' => 'error',
                    'title' => 'Data Belum Lengkap',
                    'message' => $canSubmit['reason'],
                    'missing_fields' => $canSubmit['missing_fields'],
                ])
                ->with('alert_show_id', 'alert-profile-incomplete');
        }

        $formFields = $letterType->formFields();
        $requiredDocuments = $letterType->requiredDocuments();

        return view('letters.form', compact('letterType', 'formFields', 'requiredDocuments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLetterRequestRequest $request): RedirectResponse
    {
        $this->authorize('create', LetterRequest::class);

        $letterType = LetterType::tryFrom($request->letter_type);

        $formData = [];
        foreach ($letterType->formFields() as $fieldName => $config) {
            $formData[$fieldName] = $request->input($fieldName);
        }

        $data = [
            'letter_type' => $letterType,
            'form_data' => $formData,
        ];

        // Add parent info if SKAK Tunjangan
        if ($letterType === LetterType::SKAK_TUNJANGAN) {
            $data['parent_name'] = $request->parent_name;
            $data['parent_nip'] = $request->parent_nip;
            $data['parent_rank'] = $request->parent_rank;
            $data['parent_institution'] = $request->parent_institution;
            $data['parent_institution_address'] = $request->parent_institution_address;
        }

        $letter = $this->letterRequestService->create($data, auth()->user());

        // Upload documents if provided
        $requiredDocuments = $letterType->requiredDocuments();
        foreach ($requiredDocuments as $key => $config) {
            if ($request->hasFile("documents.{$key}")) {
                $this->documentService->uploadSupporting(
                    $request->file("documents.{$key}"),
                    $letter,
                    auth()->user()
                );
            }
        }

        LogHelper::logSuccess('created', 'letter request', [
            'letter_request_id' => $letter->id,
            'letter_type' => $letterType->value,
            'student_id' => auth()->id(),
        ], $request);

        return redirect()->route('letters.show', $letter)
            ->with('notification_data', [
                'type' => 'success',
                'text' => "Pengajuan {$letterType->label()} berhasil dibuat!",
                'position' => 'center-top',
                'duration' => 3000,
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(LetterRequest $letter): View
    {
        $this->authorize('view', $letter);

        $letter->load([
            'student.profile',
            'semester',
            'academicYear',
            'approvals.assignedApprover.profile',
            'approvals.approvedBy.profile',
            'documents',
            'rejectionHistories.rejectedBy.profile',
        ]);

        return view('letters.show', compact('letter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LetterRequest $letter): View
    {
        $this->authorize('update', $letter);

        $letterType = $letter->letter_type;
        $formFields = $letterType->formFields();
        $requiredDocuments = $letterType->requiredDocuments();

        return view('letters.form', compact('letter', 'letterType', 'formFields', 'requiredDocuments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLetterRequestRequest $request, LetterRequest $letter): RedirectResponse
    {
        $this->authorize('update', $letter);

        $letterType = $letter->letter_type;

        $formData = [];
        foreach ($letterType->formFields() as $fieldName => $config) {
            $formData[$fieldName] = $request->input($fieldName);
        }

        $data = [
            'form_data' => $formData,
        ];

        // Add parent info if SKAK Tunjangan
        if ($letterType === LetterType::SKAK_TUNJANGAN) {
            $data['parent_name'] = $request->parent_name;
            $data['parent_nip'] = $request->parent_nip;
            $data['parent_rank'] = $request->parent_rank;
            $data['parent_institution'] = $request->parent_institution;
            $data['parent_institution_address'] = $request->parent_institution_address;
        }

        $this->letterRequestService->update($letter, $data);

        // Upload new documents if provided
        $requiredDocuments = $letterType->requiredDocuments();
        foreach ($requiredDocuments as $key => $config) {
            if ($request->hasFile("documents.{$key}")) {
                $this->documentService->uploadSupporting(
                    $request->file("documents.{$key}"),
                    $letter,
                    auth()->user()
                );
            }
        }

        LogHelper::logSuccess('updated', 'letter request', [
            'letter_request_id' => $letter->id,
            'letter_type' => $letterType->value,
        ], $request);

        return redirect()->route('letters.show', $letter)
            ->with('notification_data', [
                'type' => 'success',
                'text' => "Pengajuan {$letterType->label()} berhasil diperbarui!",
                'position' => 'center-top',
                'duration' => 3000,
            ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LetterRequest $letter): RedirectResponse
    {
        $this->authorize('delete', $letter);

        $letterType = $letter->letter_type;

        try {
            DB::transaction(function () use ($letter) {
                $letter->delete();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('deleted', 'letter request', [
                'letter_request_id' => $letter->id,
                'letter_type' => $letterType->value,
            ]);

            return redirect()->route('letters.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => "Pengajuan {$letterType->label()} berhasil dihapus!",
                    'position' => 'center-top',
                    'duration' => 3000,
                ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('delete', 'letter request', $e, [
                'letter_request_id' => $letter->id,
            ]);

            return back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Gagal menghapus step. Silakan coba lagi.',
                'position' => 'center-top',
                'duration' => 6000
            ]);
        }
    }

    public function cancel(Request $request, LetterRequest $letter): RedirectResponse
    {
        $this->authorize('cancel', $letter);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $this->letterRequestService->cancel(
            $letter,
            auth()->user(),
            $request->reason
        );

        // LOG SUCCESS
        LogHelper::logSuccess('cancelled', 'letter request', [
            'letter_request_id' => $letter->id,
            'letter_type' => $letter->letter_type->value,
            'reason' => $request->reason
        ], $request);

        return redirect()->route('letters.show', $letter)
            ->with('notification_data', [
                'type' => 'success',
                'text' => "Pengajuan {$letter->letter_type->label()} berhasil dibatalkan!",
                'position' => 'center-top',
                'duration' => 3000,
            ]);
    }
}
