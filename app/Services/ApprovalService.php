<?php

namespace App\Services;

use App\Enums\ApprovalAction;
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
        protected LetterNumberService $letterNumberService,
        protected LetterDocxService $docxService
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

                // Generate DOCX if this is the right step (before Upload & Publish)
                if ($this->shouldGenerateDocx($approval, $letter)) {
                    LetterDocxService::validateWDData();
                    $this->generateDocx($letter);
                }

                // Check if this is final approval
                if ($approval->is_final) {
                    $this->handleFinalApproval($letter);
                } else {
                    $this->moveToNextStep($letter);
                }

                // Send notifications
                $this->notifyStakeholders($letter, $approval, 'approved', $approver);
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
                    // Return to student for revision
                    $letter->update([
                        'status' => 'rejected',
                        'is_editable' => true,
                    ]);

                    // Reset all approvals to pending
                    $letter->approvals()->update([
                        'status' => 'pending',
                        'is_active' => false,
                        'approved_by' => null,
                        'approved_at' => null,
                        'note' => null,
                    ]);

                    // Activate first step
                    $letter->approvals()->where('step', 1)->update([
                        'is_active' => true,
                    ]);
                } elseif ($onReject === ApprovalAction::TO_PREVIOUS_STEP) {
                    // Return to previous step
                    $previousApproval = $letter->approvals()
                        ->where('step', '<', $approval->step)
                        ->orderBy('step', 'desc')
                        ->first();

                    if ($previousApproval) {
                        // Reset previous step to pending
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

                    // Deactivate all remaining approvals
                    $letter->approvals()->where('status', 'pending')->update([
                        'is_active' => false,
                    ]);
                }

                // Send notifications
                $this->notifyStakeholders($letter, $approval, 'rejected', $rejector);
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

                // Update letter data
                $letter->update([
                    'data_input' => $formData,
                ]);
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
        // Check if letter type is external (SKAK)
        if ($letter->letter_type->isExternal()) {
            $letter->update([
                'status' => 'external_processing',
            ]);
        } else {
            // Generate PDF
        }
    }

    /**
     * Check if should generate DOCX at this approval step.
     * DOCX ONLY for external letters (SKAK) at step before Upload & Publish.
     * Internal letters will generate PDF directly (Phase 5B-2).
     */
    private function shouldGenerateDocx(Approval $approval, LetterRequest $letter): bool
    {
        // Only external letters need DOCX
        if (!$letter->letter_type->isExternal()) {
            return false;
        }

        // For external letters (SKAK), generate DOCX at step before Upload & Publish
        // Get total steps
        $totalSteps = $letter->approvals()->count();
        $currentStep = $approval->step;

        // Generate if this is second-to-last step
        // (Next step will be Upload & Publish PDF Final)
        return $currentStep === ($totalSteps - 1);
    }

    /**
     * Generate DOCX document for approved letter.
     */
    private function generateDocx(LetterRequest $letter): void
    {
        try {
            $filePath = $this->docxService->generate($letter);

            // Save to documents table
            $letter->documents()->create([
                'category' => 'generated',
                'file_name' => basename($filePath),
                'file_path' => $filePath,
                'file_size' => filesize(storage_path("app/{$filePath}")),
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'uploaded_by' => null, // System generated
            ]);

            LogHelper::logSuccess('generated', 'docx', [
                'letter_request_id' => $letter->id,
                'file_path' => $filePath,
            ]);
        } catch (\Exception $e) {
            LogHelper::logError('generate', 'docx', $e, [
                'letter_request_id' => $letter->id,
            ]);

            Log::error("❌ DOCX Generation Failed for Letter #{$letter->id}");
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

            $letter->update([
                'status' => 'in_progress',
            ]);
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
        // TODO: Implement notification logic in Phase 5D
        // For now, just log
        LogHelper::logSuccess('notification queued', 'approval', [
            'letter_request_id' => $letter->id,
            'approval_id' => $approval->id,
            'action' => $action,
            'actor_id' => $actor->id,
            'student_id' => $letter->student_id,
        ]);

        // Notifications to send:
        // 1. Student (always)
        // 2. Next approver (if exists)
        // 3. Previous approver (for tracking)
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

        // Get approvals where:
        // 1. Status = pending
        // 2. is_active = true
        // 3. User position matches required_positions
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
        // Must be active and pending
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

}