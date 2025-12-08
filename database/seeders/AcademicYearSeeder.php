<?php

namespace Database\Seeders;

use App\Enums\SemesterType;
use App\Models\AcademicYear;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYears = [
            [
                'year_label' => '2020/2021',
                'start_date' => '2020-08-01',
                'end_date' => '2021-07-31',
                'is_active' => 0,
            ],
            [
                'year_label' => '2021/2022',
                'start_date' => '2021-08-01',
                'end_date' => '2022-07-31',
                'is_active' => 0,
            ],
            [
                'year_label' => '2022/2023',
                'start_date' => '2022-08-01',
                'end_date' => '2023-07-31',
                'is_active' => 0,
            ],
            [
                'year_label' => '2023/2024',
                'start_date' => '2023-08-01',
                'end_date' => '2024-07-31',
                'is_active' => 0,
            ],
            [
                'year_label' => '2024/2025',
                'start_date' => '2024-08-01',
                'end_date' => '2025-07-31',
                'is_active' => 1, // ← AKTIF
            ],
            [
                'year_label' => '2025/2026',
                'start_date' => '2025-08-01',
                'end_date' => '2026-07-31',
                'is_active' => 0,
            ],
        ];

        foreach ($academicYears as $data) {
            $academicYear = AcademicYear::create([
                'code' => 'TA-' . $data['year_label'],
                'year_label' => $data['year_label'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_active' => $data['is_active'],
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            $this->generateSemesters($academicYear, $data['is_active']);

            $this->command->info("  " . "✅  Created: {$academicYear->year_label}" . ($data['is_active'] ? ' [🟢 ACTIVE]' : ''));
        }

        $this->command->info('  🎉 Academic Years seeding completed!');
    }

    private function generateSemesters(AcademicYear $academicYear, bool $setActiveGanjil = false)
    {
        $startDate = Carbon::parse($academicYear->start_date);
        $endDate = Carbon::parse($academicYear->end_date);
        $midDate = $startDate->copy()->addMonths(6);

        // Semester Ganjil
        Semester::create([
            'code' => 'TA-' . $academicYear->year_label . '-' . SemesterType::GANJIL->shortCode(),
            'academic_year_id' => $academicYear->id,
            'semester_type' => SemesterType::GANJIL,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $midDate->copy()->subDay()->format('Y-m-d'),
            'is_active' => $setActiveGanjil ? 1 : 0,
        ]);

        // Semester Genap
        Semester::create([
            'code' => 'TA-' . $academicYear->year_label . '-' . SemesterType::GENAP->shortCode(),
            'academic_year_id' => $academicYear->id,
            'semester_type' => SemesterType::GENAP,
            'start_date' => $midDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'is_active' => 0,
        ]);
    }
}
