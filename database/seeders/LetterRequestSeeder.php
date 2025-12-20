<?php

namespace Database\Seeders;

use App\Enums\LetterType;
use App\Models\AcademicYear;
use App\Models\LetterRequest;
use App\Models\Semester;
use App\Models\User;
use App\Services\LetterRequestService;
use Illuminate\Database\Seeder;

class LetterRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('  ✉️ Starting Letter Requests seeding...');

        $letterRequestService = app(LetterRequestService::class);

        // Get active semester and academic year
        $activeSemester = Semester::where('is_active', true)->first();
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        if (!$activeSemester || !$activeAcademicYear) {
            $this->command->error('Please seed AcademicYear and Semester first!');
            return;
        }

        // Get students
        $students = User::role('Mahasiswa')->get();

        if ($students->isEmpty()) {
            $this->command->error('No students found! Please seed users first.');
            return;
        }

        $this->command->info('  ✅  Creating letter requests!');

        // Scenario 1: SKAK - Pending at step 1 (Verifikasi Administrasi)
        $this->createLetterRequest(
            $letterRequestService,
            $students[0] ?? $students->first(),
            LetterType::SKAK,
            $activeSemester,
            $activeAcademicYear,
            [
                'keperluan' => 'Pengurusan Beasiswa',
                'keterangan' => '-'
            ]
        );

        $this->createLetterRequest(
            $letterRequestService,
            $students[0] ?? $students->first(),
            LetterType::PENELITIAN,
            $activeSemester,
            $activeAcademicYear,
            [
                'judul_penelitian' => 'Strategi Adaptif Auditor terhadap Tantangan di Era Digital dalam Menjaga Kualitas Audit dan Etika Profesional pada Kantor Akuntan Publik Moh Wildan & Adi Darmawan Malang',
                'nama_tempat_penelitian' => 'Kantor Akuntan Publik Moh Wildan & Adi Darmawan Malang',
                'alamat_tempat_penelitian' => 'Jl. Raya Blimbing Indah No.46 blok F4, Pandanwangi, Kec. Blimbing, Kota Malang, Jawa Timur 65126',
                'no_hp' => '081228860862',
                'dosen_pembimbing' => 'Kusairi, SE.,ME.',
                'bulan_pelaksanaan' => 'Juli 2025',
                'keterangan' => '-'
            ]
        );

        $this->command->info('  🎉 Letter requests seeding completed!');
    }

    /**
     * Create a letter request.
     */
    private function createLetterRequest(
        LetterRequestService $service,
        User $student,
        LetterType $letterType,
        Semester $semester,
        AcademicYear $academicYear,
        array $formData
    ): LetterRequest {
        $data = [
            'letter_type' => $letterType,
            'semester_id' => $semester->id,
            'academic_year_id' => $academicYear->id,
            'form_data' => $formData,
        ];

        return $service->create($data, $student);
    }
}