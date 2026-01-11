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
use App\Models\StudyProgram;
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
        $user = auth()->user();
        $filters = $request->only(['letter_type', 'status', 'search']);

        $isAdmin = $user->hasRole('Administrator');

        if ($isAdmin) {
            $tab = 'all';
            $approvals = Approval::with([
                'letterRequest.student.profile',
                'letterRequest.semester',
                'letterRequest.academicYear',
            ])
                ->latest('created_at')
                ->filter($filters)
                ->paginate(15)
                ->withQueryString();
        } else {
            $tab = $request->query('tab', 'pending');

            if ($tab === 'pending') {
                $approvals = $this->approvalService->getPendingApprovalsForUser(auth()->user(), $filters);
            } else {
                $approvals = $this->approvalService->getApprovedByUser(auth()->user());
            }
        }
        $letterTypes = LetterType::cases();

        return view('approvals.index', compact('approvals', 'letterTypes', 'tab', 'isAdmin'));
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
        $studyPrograms = StudyProgram::getFormattedNames();

        return view('approvals.show', compact('approval', 'letter', 'canApprove', 'studyPrograms'));
    }

    /**
     * Approve the approval.
     */
    public function approve(ApproveRequest $request, Approval $approval): RedirectResponse
    {
        $this->authorize('approve', $approval);

        try {
            $approval->load('letterRequest.student.profile', 'letterRequest.approvals');
            $letter = $approval->letterRequest;

            $this->approvalService->approve(
                $approval,
                auth()->user(),
                $request->note
            );

            $studentName = $letter->student->profile->full_name;
            $nim = $letter->student->profile->student_or_employee_id;
            $letterTypeLabel = $letter->letter_type->label();

            $successMessage = "Step {$approval->step} ({$approval->step_label}) berhasil disetujui! Pengajuan {$letterTypeLabel} dari {$studentName} (NIM: {$nim}).";

            return redirect()
                ->route('approvals.show', $approval)
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => $successMessage,
                    'position' => 'center-top',
                    'duration' => 5000,
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
            $approval->load('letterRequest.student.profile');
            $letter = $approval->letterRequest;

            $this->approvalService->reject(
                $approval,
                auth()->user(),
                $request->reason
            );

            $studentName = $letter->student->profile->full_name;
            $nim = $letter->student->profile->student_or_employee_id;
            $letterTypeLabel = $letter->letter_type->label();
            $rejectMessage = "Step {$approval->step} ({$approval->step_label}) ditolak! Pengajuan {$letterTypeLabel} dari {$studentName} (NIM: {$nim}) telah dikembalikan.";

            return redirect()
                ->route('approvals.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => $rejectMessage,
                    'position' => 'center-top',
                    'duration' => 5000,
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
            $approval->load('letterRequest.student.profile');
            $letter = $approval->letterRequest;

            $letterType = $letter->letter_type;
            $formData = [];

            foreach ($letterType->formFields() as $fieldName => $config) {
                if ($config['type'] === 'student_list') {
                    $jsonString = $request->input($fieldName);
                    $formData[$fieldName] = json_decode($jsonString, true);
                } else {
                    $formData[$fieldName] = $request->input($fieldName);
                }
            }

            $this->approvalService->editContent(
                $approval,
                $formData,
                auth()->user()
            );

            $studentName = $letter->student->profile->full_name;
            $nim = $letter->student->profile->student_or_employee_id;
            $letterTypeLabel = $letterType->label();
            $editMessage = "Konten {$letterTypeLabel} Pengajuan dari {$studentName} (NIM: {$nim}) berhasil diperbarui!";

            return redirect()
                ->back()
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => $editMessage,
                    'position' => 'center-top',
                    'duration' => 5000,
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
