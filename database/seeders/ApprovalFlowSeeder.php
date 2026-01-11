<?php

namespace Database\Seeders;

use App\Enums\ApprovalAction;
use App\Enums\LetterType;
use App\Enums\OfficialPosition;
use App\Models\ApprovalFlow;
use Illuminate\Database\Seeder;

class ApprovalFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('  🚀 Starting Approval Flow seeding...');

        // Define approval flows for each letter type
        $flows = [
            // ====================================
            // SKAK (Word → External System) - 3 STEPS
            // ====================================
            [
                'letter_type' => LetterType::SKAK,
                'step' => 1,
                'step_label' => 'Verifikasi Awal',
                'required_positions' => [OfficialPosition::STAF_AKADEMIK->value],
                'can_edit_content' => true,
                'is_editable' => true,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::SKAK,
                'step' => 2,
                'step_label' => 'Generate Draft Word',
                'required_positions' => [OfficialPosition::STAF_AKADEMIK->value],
                'can_edit_content' => false,
                'is_editable' => false,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::SKAK,
                'step' => 3,
                'step_label' => 'Upload & Publish PDF Final',
                'required_positions' => [OfficialPosition::STAF_AKADEMIK->value],
                'can_edit_content' => false,
                'is_editable' => false,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => true,
            ],

            // ====================================
            // SKAK TUNJANGAN (Word → External System) - 3 STEPS
            // ====================================
            [
                'letter_type' => LetterType::SKAK_TUNJANGAN,
                'step' => 1,
                'step_label' => 'Verifikasi Awal',
                'required_positions' => [OfficialPosition::STAF_AKADEMIK->value],
                'can_edit_content' => true,
                'is_editable' => true,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::SKAK_TUNJANGAN,
                'step' => 2,
                'step_label' => 'Generate Draft Word',
                'required_positions' => [OfficialPosition::STAF_AKADEMIK->value],
                'can_edit_content' => false,
                'is_editable' => false,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::SKAK_TUNJANGAN,
                'step' => 3,
                'step_label' => 'Upload & Publish PDF Final',
                'required_positions' => [OfficialPosition::STAF_AKADEMIK->value],
                'can_edit_content' => false,
                'is_editable' => false,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => true,
            ],

            // ====================================
            // SURAT PENELITIAN (PDF Auto-generate) - 4 STEPS
            // ====================================
            [
                'letter_type' => LetterType::PENELITIAN,
                'step' => 1,
                'step_label' => 'Verifikasi Drafter',
                'required_positions' => [OfficialPosition::STAF_AKADEMIK->value],
                'can_edit_content' => true,
                'is_editable' => true,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::PENELITIAN,
                'step' => 2,
                'step_label' => 'Verifikasi Paraf',
                'required_positions' => [OfficialPosition::KASUBBAG_AKADEMIK->value],
                'can_edit_content' => false,
                'is_editable' => true,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::PENELITIAN,
                'step' => 3,
                'step_label' => 'Validasi TTE',
                'required_positions' => [OfficialPosition::WAKIL_DEKAN_AKADEMIK->value],
                'can_edit_content' => false,
                'is_editable' => false,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::PENELITIAN,
                'step' => 4,
                'step_label' => 'Verifikasi Penerbitan',
                'required_positions' => [
                    OfficialPosition::KASUBBAG_AKADEMIK->value,
                    OfficialPosition::STAF_AKADEMIK->value
                ],
                'can_edit_content' => false,
                'is_editable' => false,
                'on_reject' => ApprovalAction::TO_PREVIOUS_STEP,
                'is_final' => true,
            ],

            // ====================================
            // DISPENSASI PERKULIAHAN (PDF Auto-generate) - 4 STEPS
            // ====================================
            [
                'letter_type' => LetterType::DISPENSASI_KULIAH,
                'step' => 1,
                'step_label' => 'Verifikasi Drafter',
                'required_positions' => [OfficialPosition::STAF_AKADEMIK->value],
                'can_edit_content' => true,
                'is_editable' => true,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::DISPENSASI_KULIAH,
                'step' => 2,
                'step_label' => 'Verifikasi Paraf',
                'required_positions' => [OfficialPosition::KASUBBAG_AKADEMIK->value],
                'can_edit_content' => false,
                'is_editable' => true,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::DISPENSASI_KULIAH,
                'step' => 3,
                'step_label' => 'Validasi TTE',
                'required_positions' => [OfficialPosition::WAKIL_DEKAN_AKADEMIK->value],
                'can_edit_content' => false,
                'is_editable' => false,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::DISPENSASI_KULIAH,
                'step' => 4,
                'step_label' => 'Verifikasi Penerbitan',
                'required_positions' => [
                    OfficialPosition::KASUBBAG_AKADEMIK->value,
                    OfficialPosition::STAF_AKADEMIK->value
                ],
                'can_edit_content' => false,
                'is_editable' => false,
                'on_reject' => ApprovalAction::TO_PREVIOUS_STEP,
                'is_final' => true,
            ],

            // ====================================
            // DISPENSASI MAHASISWA (PDF Auto-generate) - 4 STEPS
            // ====================================
            [
                'letter_type' => LetterType::DISPENSASI_MAHASISWA,
                'step' => 1,
                'step_label' => 'Verifikasi Drafter',
                'required_positions' => [OfficialPosition::STAF_AKADEMIK->value],
                'can_edit_content' => true,
                'is_editable' => true,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::DISPENSASI_MAHASISWA,
                'step' => 2,
                'step_label' => 'Verifikasi Paraf',
                'required_positions' => [OfficialPosition::KASUBBAG_AKADEMIK->value],
                'can_edit_content' => false,
                'is_editable' => true,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::DISPENSASI_MAHASISWA,
                'step' => 3,
                'step_label' => 'Validasi TTE',
                'required_positions' => [OfficialPosition::WAKIL_DEKAN_AKADEMIK->value],
                'can_edit_content' => false,
                'is_editable' => false,
                'on_reject' => ApprovalAction::TO_STUDENT,
                'is_final' => false,
            ],
            [
                'letter_type' => LetterType::DISPENSASI_MAHASISWA,
                'step' => 4,
                'step_label' => 'Verifikasi Penerbitan',
                'required_positions' => [
                    OfficialPosition::KASUBBAG_AKADEMIK->value,
                    OfficialPosition::STAF_AKADEMIK->value
                ],
                'can_edit_content' => false,
                'is_editable' => false,
                'on_reject' => ApprovalAction::TO_PREVIOUS_STEP,
                'is_final' => true,
            ],
        ];

        $createdCount = 0;

        foreach ($flows as $flowData) {
            $flow = ApprovalFlow::create($flowData);

            $letterTypeLabel = $flow->letter_type->shortLabel();
            $this->command->info("  ✅  Created: [{$letterTypeLabel}] Step {$flow->step}: {$flow->step_label}");

            $createdCount++;
        }

        // Summary
        $this->command->newLine();
        $this->command->info("  📊 Summary:");
        $this->command->info("  ✅  Created: {$createdCount} approval flow steps");

        // Breakdown by letter type
        $this->command->newLine();
        $this->command->info("  📋 Breakdown by Letter Type:");

        foreach (LetterType::cases() as $letterType) {
            $count = ApprovalFlow::where('letter_type', $letterType)->count();
            $label = $letterType->shortLabel();
            $this->command->info("  🔵 {$label}: {$count} steps");
        }

        $this->command->newLine();
        $this->command->info('  🎉 Approval Flow seeding completed!');
    }
}
