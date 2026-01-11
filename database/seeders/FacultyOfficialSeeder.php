<?php

namespace Database\Seeders;

use App\Enums\OfficialPosition;
use App\Models\FacultyOfficial;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacultyOfficialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('  🚀 Starting Faculty Officials seeding...');

        // Get users and study programs
        $users = User::with('profile')->get();
        $studyPrograms = StudyProgram::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  No users found. Please run UserSeeder first!');
            return;
        }

        if ($studyPrograms->isEmpty()) {
            $this->command->warn('⚠️  No study programs found. Please run StudyProgramSeeder first!');
            return;
        }

        // Sample data structure
        $facultyOfficialsData = [
            // ====================================
            // DEKAN (Only 1 active)
            // ====================================
            [
                'position' => OfficialPosition::DEKAN,
                'user_index' => 1, // First user
                'study_program_id' => null,
                'start_date' => '2023-01-01',
                'end_date' => null, // Still active
                'notes' => 'Dekan Fakultas Vokasi periode 2023-sekarang',
            ],

            // ====================================
            // WAKIL DEKAN AKADEMIK (Only 1 active)
            // ====================================
            [
                'position' => OfficialPosition::WAKIL_DEKAN_AKADEMIK,
                'user_index' => 2,
                'study_program_id' => null,
                'start_date' => '2023-01-01',
                'end_date' => null,
                'notes' => 'Wakil Dekan Bidang Akademik periode 2023-sekarang',
            ],

            // ====================================
            // KEPALA PROGRAM STUDI (1 per prodi)
            // ====================================
            [
                'position' => OfficialPosition::KETUA_PROGRAM_STUDI,
                'user_index' => 3,
                'study_program_index' => 0, // First study program
                'start_date' => '2024-01-01',
                'end_date' => null,
                'notes' => 'Kepala Program Studi periode 2024-sekarang',
            ],
            [
                'position' => OfficialPosition::KETUA_PROGRAM_STUDI,
                'user_index' => 4,
                'study_program_index' => 1, // Second study program
                'start_date' => '2024-01-01',
                'end_date' => null,
                'notes' => 'Kepala Program Studi periode 2024-sekarang',
            ],
            [
                'position' => OfficialPosition::KETUA_PROGRAM_STUDI,
                'user_index' => 5,
                'study_program_index' => 2, // Third study program
                'start_date' => '2024-01-01',
                'end_date' => null,
                'notes' => 'Kepala Program Studi periode 2024-sekarang',
            ],
            [
                'position' => OfficialPosition::KETUA_PROGRAM_STUDI,
                'user_index' => 6,
                'study_program_index' => 3, // Third study program
                'start_date' => '2024-01-01',
                'end_date' => null,
                'notes' => 'Kepala Program Studi periode 2024-sekarang',
            ],
            [
                'position' => OfficialPosition::KETUA_PROGRAM_STUDI,
                'user_index' => 7,
                'study_program_index' => 4, // Third study program
                'start_date' => '2024-01-01',
                'end_date' => null,
                'notes' => 'Kepala Program Studi periode 2024-sekarang',
            ],

            // ====================================
            // KASUBBAG AKADEMIK (Multiple people - approvers)
            // ====================================
            [
                'position' => OfficialPosition::KASUBBAG_AKADEMIK,
                'user_index' => 8,
                'study_program_id' => null,
                'start_date' => '2023-06-01',
                'end_date' => null,
                'notes' => 'Kepala Subbagian Akademik, Alumni, Kerjasama, dan Kewirausahaan Mahasiswa periode 2024-sekarang',
            ],

            // ====================================
            // KASUBBAG AKADEMIK (Multiple people - approvers)
            // ====================================
            [
                'position' => OfficialPosition::STAF_AKADEMIK,
                'user_index' => 9,
                'study_program_id' => null,
                'start_date' => '2023-06-01',
                'end_date' => null,
                'notes' => 'Staf Akademik periode 2024-sekarang',
            ],

            // ====================================
            // HISTORICAL DATA (Ended assignments)
            // ====================================
//            [
//                'position' => OfficialPosition::DEKAN,
//                'user_index' => 11,
//                'study_program_id' => null,
//                'start_date' => '2020-01-01',
//                'end_date' => '2022-12-31',
//                'notes' => 'Dekan Fakultas Vokasi periode 2020-2022 (telah berakhir)',
//            ],
//            [
//                'position' => OfficialPosition::WAKIL_DEKAN_AKADEMIK,
//                'user_index' => 12,
//                'study_program_id' => null,
//                'start_date' => '2020-01-01',
//                'end_date' => '2022-12-31',
//                'notes' => 'Wakil Dekan Akademik periode 2020-2022 (telah berakhir)',
//            ],
        ];

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($facultyOfficialsData as $data) {
            // Get user
            if (!isset($data['user_index']) || !isset($users[$data['user_index']])) {
                $this->command->warn("  ⚠️  Skipped: User index {$data['user_index']} not found");
                $skippedCount++;
                continue;
            }

            $user = $users[$data['user_index']];

            // Get study program if needed
            $studyProgramId = null;
            if ($data['position'] === OfficialPosition::KETUA_PROGRAM_STUDI) {
                if (isset($data['study_program_index']) && isset($studyPrograms[$data['study_program_index']])) {
                    $studyProgramId = $studyPrograms[$data['study_program_index']]->id;
                } else {
                    $this->command->warn("  ⚠️  Skipped: Study program index {$data['study_program_index']} not found");
                    $skippedCount++;
                    continue;
                }
            } else {
                $studyProgramId = $data['study_program_id'] ?? null;
            }

            // Check for overlap (validation)
            $hasOverlap = FacultyOfficial::hasOverlap(
                userId: $user->id,
                position: $data['position'],
                startDate: $data['start_date'],
                endDate: $data['end_date']
            );

            if ($hasOverlap) {
                $userName = $user->profile->full_name ?? $user->email;
                $this->command->warn("  ⚠️  Skipped: {$userName} - {$data['position']->label()} (overlap detected)");
                $skippedCount++;
                continue;
            }

            // Create faculty official
            $official = FacultyOfficial::create([
                'user_id' => $user->id,
                'position' => $data['position'],
                'study_program_id' => $studyProgramId,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'notes' => $data['notes'],
            ]);

            $userName = $user->profile->full_name ?? $user->email;
            $positionLabel = $data['position']->label();
            $status = $official->is_active ? '🟢 Active' : '⚪ Ended';

            if ($studyProgramId) {
                $programName = $studyPrograms->firstWhere('id', $studyProgramId)->degree_name ?? '';
                $this->command->info("  ✅  Created: {$userName} - {$positionLabel} ({$programName}) [{$status}]");
            } else {
                $this->command->info("  ✅  Created: {$userName} - {$positionLabel} [{$status}]");
            }

            $createdCount++;
        }

        // Summary
        $this->command->newLine();
        $this->command->info("  📊 Summary:");
        $this->command->info("  ✅  Created: {$createdCount} faculty officials");

        if ($skippedCount > 0) {
            $this->command->warn("  ⚠️  Skipped: {$skippedCount} entries");
        }

        // Statistics
        $activeCount = FacultyOfficial::active()->count();
        $endedCount = FacultyOfficial::ended()->count();

        $this->command->newLine();
        $this->command->info("  📈 Statistics:");
        $this->command->info("  🟢 Active assignments: {$activeCount}");
        $this->command->info("  🔴 Ended assignments: {$endedCount}");

        // Position breakdown
        $this->command->newLine();
        $this->command->info("  📋 Breakdown by Position:");

        foreach (OfficialPosition::cases() as $position) {
            $count = FacultyOfficial::where('position', $position)->active()->count();
            if ($count > 0) {
                $label = $position->label();
                $this->command->info("  🔵 {$label}: {$count}");
            }
        }

        $this->command->newLine();
        $this->command->info('  🎉 Faculty Officials seeding completed!');
    }
}
