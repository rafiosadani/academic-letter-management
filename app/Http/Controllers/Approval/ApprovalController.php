<?php

namespace App\Http\Controllers\Approval;

use App\Enums\LetterType;
use App\Enums\OfficialPosition;
use App\Http\Controllers\Controller;
use App\Http\Requests\Approval\ApproveRequest;
use App\Http\Requests\Approval\EditContentRequest;
use App\Http\Requests\Approval\RejectRequest;
use App\Models\Approval;
use App\Models\FacultyOfficial;
use App\Services\ApprovalService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ApprovalService $approvalService
    ) {}

    /**
     * Display approval dashboard.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Approval::class);

        $filters = $request->only(['letter_type', 'status', 'search']);

        $tab = $request->query('tab', 'pending');

        if ($tab === 'pending') {
            $approvals = $this->approvalService->getPendingApprovalsForUser(auth()->user(), $filters);
        } else {
            $approvals = $this->approvalService->getApprovedByUser(auth()->user());
        }

        $letterTypes = LetterType::cases();

        return view('approvals.index', compact('approvals', 'letterTypes', 'tab'));
    }

    /**
     * Display approval detail.
     */
    public function show(Approval $approval): View
    {
        $this->authorize('view', $approval);

        $approval->load([
            'letterRequest.student.profile',
            'letterRequest.semester',
            'letterRequest.academicYear',
            'letterRequest.documents',
            'letterRequest.approvals',
            'assignedApprover.profile',
        ]);

        $letter = $approval->letterRequest;
        $canApprove = $this->approvalService->canUserApprove(auth()->user(), $approval);

        return view('approvals.show', compact('approval', 'letter', 'canApprove'));
    }

    /**
     * Approve the approval.
     */
    public function approve(ApproveRequest $request, Approval $approval): RedirectResponse
    {
        $this->authorize('approve', $approval);

        try {
            $this->approvalService->approve(
                $approval,
                auth()->user(),
                $request->note
            );

            return redirect()
                ->route('approvals.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => 'Pengajuan berhasil disetujui!',
                    'position' => 'center-top',
                    'duration' => 3000,
                ]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Gagal menyetujui pengajuan: ' . $e->getMessage(),
                    'position' => 'center-top',
                    'duration' => 5000,
                ]);
        }
    }

    /**
     * Reject the approval.
     */
    public function reject(RejectRequest $request, Approval $approval): RedirectResponse
    {
        $this->authorize('reject', $approval);

        try {
            $this->approvalService->reject(
                $approval,
                auth()->user(),
                $request->reason
            );

            return redirect()
                ->route('approvals.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => 'Pengajuan telah ditolak.',
                    'position' => 'center-top',
                    'duration' => 3000,
                ]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Gagal menolak pengajuan: ' . $e->getMessage(),
                    'position' => 'center-top',
                    'duration' => 5000,
                ]);
        }
    }

    /**
     * Edit letter content during approval.
     */
    public function editContent(EditContentRequest $request, Approval $approval): RedirectResponse
    {
        $this->authorize('editContent', $approval);

        try {
            $letterType = $approval->letterRequest->letter_type;
            $formData = [];

            // Extract form data based on letter type
            foreach ($letterType->formFields() as $fieldName => $config) {
                $formData[$fieldName] = $request->input($fieldName);
            }

            $this->approvalService->editContent(
                $approval,
                $formData,
                auth()->user()
            );

            return redirect()
                ->back()
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => 'Konten surat berhasil diperbarui!',
                    'position' => 'center-top',
                    'duration' => 3000,
                ]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Gagal memperbarui konten: ' . $e->getMessage(),
                    'position' => 'center-top',
                    'duration' => 5000,
                ]);
        }
    }
}
