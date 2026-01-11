<?php

namespace App\Services;

use App\Enums\LetterType;
use App\Events\LetterRequestSubmitted;
use App\Events\LetterResubmitted;
use App\Helpers\LogHelper;
use App\Models\AcademicYear;
use App\Models\ApprovalFlow;
use App\Models\LetterRequest;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LetterRequestService
{
    /**
     * Create a new letter request.
     */
    public function create(array $data, User $student): LetterRequest
    {
        try {
            $letterRequest = DB::transaction(function () use ($data, $student) {
                // Get active semester and academic year
                $activeSemester = Semester::where('is_active', true)->firstOrFail();
                $activeAcademicYear = AcademicYear::where('is_active', true)->firstOrFail();

                // Update user profile if parent info provided (for SKAK Tunjangan)
                if (isset($data['parent_name'])) {
                    $student->profile->update([
                        'parent_name' => $data['parent_name'],
                        'parent_nip' => $data['parent_nip'],
                        'parent_rank' => $data['parent_rank'],
                        'parent_institution' => $data['parent_institution'],
                        'parent_institution_address' => $data['parent_institution_address'],
                    ]);
                }

                // Create letter request
                $letterRequest = LetterRequest::create([
                    'letter_type' => $data['letter_type'],
                    'student_id' => $student->id,
                    'semester_id' => $activeSemester->id,
                    'academic_year_id' => $activeAcademicYear->id,
                    'data_input' => $data['form_data'],
                    'status' => 'in_progress',
                    'is_editable' => false,
                ]);

                // Create approval steps from approval flow
                $this->createApprovalSteps($letterRequest);

                return $letterRequest;
            });

            // Dispatch event - Notify student + first approver
            event(new LetterRequestSubmitted($letterRequest));

            return $letterRequest;

        } catch (\Exception $e) {
            LogHelper::logError('create', 'letter request', $e, [
                'letter_type' => $data['letter_type']->value ?? $data['letter_type'],
                'student_id' => $student->id,
            ]);

            throw $e;
        }
    }

    /**
     * Create approval steps from approval flow.
     */
    private function createApprovalSteps(LetterRequest $letterRequest): void
    {
        $flows = ApprovalFlow::getFlowForLetter($letterRequest->letter_type);

        foreach ($flows as $flow) {
            // Check if there are multiple positions for this step
            $hasMultiplePositions = count($flow->required_positions) > 1;

            // If multiple positions: assigned_approver_id = NULL (ANY can approve)
            // If single position: try to assign specific approver
            $assignedApprover = null;
            if (!$hasMultiplePositions) {
                $assignedApprover = $flow->currentPejabat();
            }

            $letterRequest->approvals()->create([
                'step' => $flow->step,
                'step_label' => $flow->step_label,
                'required_positions' => $flow->required_positions,
                'assigned_approver_id' => $assignedApprover?->user_id, // NULL if multiple positions
                'flow_snapshot' => $flow->toArray(),
                'status' => 'pending',
                'is_active' => $flow->step === 1,
            ]);
        }
    }

    /**
     * Update letter request (for resubmission or edit).
     */
    public function update(LetterRequest $letterRequest, array $data): LetterRequest
    {
        try {
            return DB::transaction(function () use ($letterRequest, $data) {
                $previousRejectionReason = null;
                if ($letterRequest->status === 'rejected') {
                    $lastRejection = $letterRequest->rejectionHistories()
                        ->latest('rejected_at')
                        ->first();

                    $previousRejectionReason = $lastRejection?->reason;
                }

                // Update parent profile if provided
                if (isset($data['parent_name'])) {
                    $letterRequest->student->profile->update([
                        'parent_name' => $data['parent_name'],
                        'parent_nip' => $data['parent_nip'],
                        'parent_rank' => $data['parent_rank'],
                        'parent_institution' => $data['parent_institution'],
                        'parent_institution_address' => $data['parent_institution_address'],
                    ]);
                }

                // Update letter request data
                $letterRequest->update([
                    'data_input' => $data['form_data'],
                    'status' => 'resubmitted',
                    'is_editable' => false,
                ]);

                // Reset approval steps to first step
                $letterRequest->approvals()->update([
                    'status' => 'pending',
                    'is_active' => false,
                ]);

                $letterRequest->approvals()->where('step', 1)->update([
                    'is_active' => true,
                ]);

                // Dispatch event - Notify first approver about resubmission
                event(new LetterResubmitted($letterRequest, $previousRejectionReason));

                return $letterRequest->fresh();
            });
        } catch (\Exception $e) {
            LogHelper::logError('update', 'letter request', $e, [
                'letter_request_id' => $letterRequest->id,
                'letter_type' => $letterRequest->letter_type->value,
            ]);

            throw $e;
        }
    }

    /**
     * Cancel letter request.
     */
    public function cancel(LetterRequest $letterRequest, User $user, ?string $reason = null): void
    {
        try {
            DB::transaction(function () use ($letterRequest, $user, $reason) {
                $letterRequest->update([
                    'status' => 'cancelled',
                    'is_editable' => false,
                ]);

                $letterRequest->cancellation()->create([
                    'cancelled_by' => $user->id,
                    'reason' => $reason,
                    'cancelled_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            LogHelper::logError('cancel', 'letter request', $e, [
                'letter_request_id' => $letterRequest->id,
                'letter_type' => $letterRequest->letter_type->value,
                'user_id' => $user->id,
            ]);

            throw $e;
        }
    }

    /**
     * Validate profile completeness for letter type.
     */
    public function validateProfileCompleteness(User $student, LetterType $letterType): array
    {
        $profile = $student->profile;

        // Use model methods for validation
        if ($letterType === LetterType::SKAK_TUNJANGAN) {
            $missingFields = $profile->getMissingFieldsForSkakTunjangan();
        } else {
            $missingFields = $profile->getMissingFieldsForBasicLetters();
        }

        return $missingFields;
    }

    /**
     * Get letters for student dashboard.
     */
    public function getStudentLetters(User $student, array $filters = [])
    {
        $query = LetterRequest::forStudent($student->id)
            ->with(['semester', 'academicYear', 'currentApproval'])
            ->latest()
            ->filter($filters);

        return $query->paginate(10)->withQueryString();
    }

    /**
     * Get letters for approver.
     */
    public function getLettersForApprover(User $approver)
    {
        return LetterRequest::whereHas('approvals', function ($query) use ($approver) {
            $query->where('assigned_approver_id', $approver->id)
                ->where('status', 'pending')
                ->where('is_active', true);
        })
            ->with(['student.profile', 'semester', 'currentApproval'])
            ->latest()
            ->paginate(10);
    }

    /**
     * Check if student can submit letter type.
     */
    public function canSubmitLetterType(User $student, LetterType $letterType): array
    {
        // Check profile completeness
        $missingFields = $this->validateProfileCompleteness($student, $letterType);

        if (!empty($missingFields)) {
            return [
                'can_submit' => false,
                'reason' => 'Profil belum lengkap, Silakan update profile terlebih dahulu!',
                'missing_fields' => $missingFields,
            ];
        }

        // Check if approval flow exists
        $flows = ApprovalFlow::getFlowForLetter($letterType);

        if ($flows->isEmpty()) {
            return [
                'can_submit' => false,
                'reason' => 'Alur persetujuan belum dikonfigurasi untuk jenis surat ini',
                'missing_fields' => [],
            ];
        }

        return [
            'can_submit' => true,
            'reason' => null,
            'missing_fields' => [],
        ];
    }
}