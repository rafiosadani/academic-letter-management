<?php

namespace App\Services;

use App\Enums\ApprovalAction;
use App\Events\ApprovalContentEdited;
use App\Events\ApprovalProcessed;
use App\Events\LetterFinalized;
use App\Helpers\LogHelper;
use App\Models\Approval;
use App\Models\LetterRequest;
use App\Models\RejectionHistory;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalService
{
    public function __construct(
        protected LetterDocxService $docxService,
        protected LetterNumberService $letterNumberService,
        protected LetterPdfService $pdfService,
        protected DocumentService $documentService
    ) {}

    /**
     * Approve current approval step.
     */
    public function approve(Approval $approval, User $approver, ?string $note = null): void
    {
        try {
            DB::transaction(function () use ($approval, $approver, $note) {
                $approval->load('letterRequest.approvals');
                $letter = $approval->letterRequest;

                // Update current approval
                $approval->update([
                    'status' => 'approved',
                    'approved_by' => $approver->id,
                    'approved_at' => now(),
                    'note' => $note,
                    'is_active' => false,
                ]);

                // Generate DOCX if this is the right step (before Upload & Publish) - this is step 2 for external letters
                if ($this->shouldGenerateDocx($approval, $letter)) {
                    LetterDocxService::validateWDData();
                    $this->generateDocx($letter);

                    // Set status to external_processing after DOCX generated
                    $letter->update([
                        'status' => 'external_processing',
                    ]);
                }

                // Check if this is final approval
                if ($approval->is_final) {
                    $this->handleFinalApproval($letter);
                } else {
                    $this->moveToNextStep($letter);
                }

                // Dispatch event - Notify student + next approver
                event(new ApprovalProcessed($approval, 'approved', $note));
            });

            LogHelper::logSuccess('approved', 'approval', [
                'approval_id' => $approval->id,
                'letter_request_id' => $approval->letter_request_id,
                'step' => $approval->step,
                'approver_id' => $approver->id,
                'is_final' => $approval->is_final,
            ]);
        } catch (\Exception $e) {
            LogHelper::logError('approve', 'approval', $e, [
                'approval_id' => $approval->id,
                'letter_request_id' => $approval->letter_request_id,
            ]);

            throw $e;
        }
    }

    /**
     * Check if should generate DOCX at this approval step.
     */
    private function shouldGenerateDocx(Approval $approval, LetterRequest $letter): bool
    {
        if (!$letter->letter_type->isExternal()) {
            return false;
        }

        // Get total steps
        $totalSteps = $letter->approvals()->count();
        $currentStep = $approval->step;

        // Generate at second-to-last step (step 2, before upload step 3)
        return $currentStep === ($totalSteps - 1);
    }

    /**
     * Generate DOCX document for external letters.
     */
    private function generateDocx(LetterRequest $letter): void
    {
        try {
            $physicalPath = $this->docxService->generate($letter);

            $document = $this->documentService->storeGeneratedDocx($physicalPath, $letter);

            // LOG SUCCESS
            LogHelper::logSuccess('generated', 'docx', [
                'letter_request_id' => $letter->id,
                'document_id' => $document->id,
                'file_name' => $document->file_name,
            ]);
        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('generate', 'docx', $e, [
                'letter_request_id' => $letter->id,
            ]);

            Log::error("❌ DOCX Generation Failed for Letter #{$letter->id}");
            Log::error("Error: " . $e->getMessage());
            Log::error("Trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Reject current approval step.
     */
    public function reject(Approval $approval, User $rejector, string $reason): void
    {
        try {
            DB::transaction(function () use ($approval, $rejector, $reason) {
                $letter = $approval->letterRequest;
                $onReject = $approval->on_reject ?? ApprovalAction::TO_STUDENT;

                // Update current approval
                $approval->update([
                    'status' => 'rejected',
                    'approved_by' => $rejector->id,
                    'approved_at' => now(),
                    'note' => $reason,
                    'is_active' => false,
                ]);

                // Create rejection history
                RejectionHistory::create([
                    'letter_request_id' => $letter->id,
                    'approval_id' => $approval->id,
                    'step' => $approval->step,
                    'rejected_by' => $rejector->id,
                    'reason' => $reason,
                    'rejected_at' => now(),
                ]);

                // Handle based on on_reject action
                if ($onReject === ApprovalAction::TO_STUDENT) {
                    $letter->update([
                        'status' => 'rejected',
                        'is_editable' => true,
                    ]);

                    $letter->approvals()->update([
                        'status' => 'pending',
                        'is_active' => false,
                        'approved_by' => null,
                        'approved_at' => null,
                        'note' => null,
                    ]);

                    $letter->approvals()->where('step', 1)->update([
                        'is_active' => true,
                    ]);
                } elseif ($onReject === ApprovalAction::TO_PREVIOUS_STEP) {
                    $previousApproval = $letter->approvals()
                        ->where('step', '<', $approval->step)
                        ->orderBy('step', 'desc')
                        ->first();

                    if ($previousApproval) {
                        $previousApproval->update([
                            'status' => 'pending',
                            'is_active' => true,
                            'approved_by' => null,
                            'approved_at' => null,
                            'note' => null,
                        ]);

                        $letter->update([
                            'status' => 'in_progress',
                        ]);
                    } else {
                        // No previous step, return to student
                        $letter->update([
                            'status' => 'rejected',
                            'is_editable' => true,
                        ]);

                        $letter->approvals()->where('step', 1)->update([
                            'is_active' => true,
                        ]);
                    }
                } else {
                    // Terminate - End process permanently
                    $letter->update([
                        'status' => 'rejected',
                        'is_editable' => false,
                    ]);

                    $letter->approvals()->where('status', 'pending')->update([
                        'is_active' => false,
                    ]);
                }

                // Dispatch event - Notify student
                event(new ApprovalProcessed($approval, 'rejected', $reason));
            });

            LogHelper::logSuccess('rejected', 'approval', [
                'approval_id' => $approval->id,
                'letter_request_id' => $approval->letter_request_id,
                'step' => $approval->step,
                'rejector_id' => $rejector->id,
                'on_reject' => $approval->on_reject?->value,
                'reason' => $reason,
            ]);
        } catch (\Exception $e) {
            LogHelper::logError('reject', 'approval', $e, [
                'approval_id' => $approval->id,
                'letter_request_id' => $approval->letter_request_id,
            ]);

            throw $e;
        }
    }

    /**
     * Edit letter content during approval (if allowed).
     */
    public function editContent(Approval $approval, array $formData, User $editor): void
    {
        try {
            DB::transaction(function () use ($approval, $formData, $editor) {
                $letter = $approval->letterRequest;

                $oldData = $letter->data_input;

                // Update letter data
                $letter->update([
                    'data_input' => $formData,
                ]);

                // Dispatch Event - Notify student that content was edited
                event(new ApprovalContentEdited($approval, $oldData, $formData));
            });

            LogHelper::logSuccess('edited content', 'approval', [
                'approval_id' => $approval->id,
                'letter_request_id' => $approval->letter_request_id,
                'editor_id' => $editor->id,
            ]);
        } catch (\Exception $e) {
            LogHelper::logError('edit content', 'approval', $e, [
                'approval_id' => $approval->id,
                'letter_request_id' => $approval->letter_request_id,
            ]);

            throw $e;
        }
    }

    /**
     * Handle final approval.
     */
    private function handleFinalApproval(LetterRequest $letter): void
    {
        if ($letter->letter_type->isExternal()) {
            // External letters: Status already 'external_processing' from step 2
            // Final step (upload) will be handled by uploadFinalPdf()
            return;
        } else {
            // Internal letters: Generate letter number & PDF
            $letterNumber = $this->letterNumberService->generateNumber($letter->letter_type);

            $letter->update([
                'status' => 'approved',
                'letter_number' => $letterNumber,
            ]);

            LetterPdfService::validateRequiredData();
            $this->generatePdf($letter);

            // Set status to completed after PDF generation
            $letter->update([
                'status' => 'completed',
            ]);

            // Dispatch Event - Notify student that letter is final and ready for download
            $downloadUrl = route('letters.download-pdf', $letter);
            event(new LetterFinalized($letter, $letterNumber, $downloadUrl));
        }
    }

    /**
     * Generate PDF document for internal letters.
     */
    private function generatePdf(LetterRequest $letter): void
    {
        try {
            $result = $this->pdfService->generate($letter, $letter->letter_number);

            $physicalPath = $result['path'];
            $documentHash = $result['hash'];

            $document = $this->documentService->storeGeneratedPdf($physicalPath, $letter, $documentHash);

            // LOG SUCCESS
            LogHelper::logSuccess('generated', 'pdf', [
                'letter_request_id' => $letter->id,
                'document_id' => $document->id,
                'file_name' => $document->file_name,
                'hash' => $documentHash,
            ]);
        } catch (\Exception $e) {
            LogHelper::logError('generate', 'pdf', $e, [
                'letter_request_id' => $letter->id,
            ]);

            Log::error("❌ PDF Generation Failed for Letter #{$letter->id}");
            Log::error("Error: " . $e->getMessage());
            Log::error("Trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Move to next approval step.
     */
    private function moveToNextStep(LetterRequest $letter): void
    {
        $currentStep = $letter->approvals()
            ->where('status', 'approved')
            ->max('step');

        if (!$currentStep) {
            return;
        }

        $nextApproval = $letter->approvals()
            ->where('status', 'pending')
            ->where('is_active', false)
            ->orderBy('step')
            ->first();

        if ($nextApproval) {
            $nextApproval->update([
                'is_active' => true,
            ]);

            // Don't override status if already external_processing
            if ($letter->status !== 'external_processing') {
                $letter->update([
                    'status' => 'in_progress',
                ]);
            }
        }
    }

    /**
     * Send notifications to all stakeholders.
     */
    private function notifyStakeholders(
        LetterRequest $letter,
        Approval $approval,
        string $action,
        User $actor
    ): void {
        LogHelper::logSuccess('notification dispatched via event', 'approval', [
            'letter_request_id' => $letter->id,
            'approval_id' => $approval->id,
            'action' => $action,
            'actor_id' => $actor->id,
            'student_id' => $letter->student_id,
        ]);
    }

    /**
     * Get pending approvals for user.
     */
    public function getPendingApprovalsForUser(User $user, array $filters = [])
    {
        // Get user's official position
        $userPosition = $user->currentOfficialPosition?->position;

        if (!$userPosition) {
            return new LengthAwarePaginator(
                [],
                0,
                15,
                1
            );
        }

        return Approval::where('status', 'pending')
            ->where('is_active', true)
            ->whereJsonContains('required_positions', $userPosition->value)
            ->whereHas('letterRequest')
            ->with([
                'letterRequest.student.profile',
                'letterRequest.semester',
                'letterRequest.academicYear',
            ])
            ->latest('created_at')
            ->filter($filters)
            ->paginate(15)
            ->withQueryString();
    }

    /**
     * Get approved letters by user.
     */
    public function getApprovedByUser(User $user, array $filters = [])
    {
        return Approval::where('approved_by', $user->id)
            ->where('status', 'approved')
            ->whereHas('letterRequest')
            ->with([
                'letterRequest.student.profile',
                'letterRequest.semester',
                'letterRequest.academicYear',
            ])
            ->latest('approved_at')
            ->filter($filters)
            ->paginate(15)
            ->withQueryString();
    }

    /**
     * Check if user can approve this approval.
     */
    public function canUserApprove(User $user, Approval $approval): bool
    {
        if (!$approval->is_active || $approval->status !== 'pending') {
            return false;
        }

        // Check user position
        $userPosition = $user->currentOfficialPosition?->position;

        if (!$userPosition) {
            return false;
        }

        // Check if user position in required_positions
        return in_array($userPosition->value, $approval->required_positions ?? []);
    }

    /**
     * Check if user has permission to view/approve this step
     * (without checking is_active or status)
     */
    public function canUserViewApproval(User $user, Approval $approval): bool
    {
        $userPosition = $user->currentOfficialPosition?->position;

        if (!$userPosition) {
            return false;
        }

        return in_array($userPosition->value, $approval->required_positions ?? []);
    }
}