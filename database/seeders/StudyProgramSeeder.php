<?php

namespace Database\Seeders;

use App\Helpers\CodeGeneration;
use App\Models\StudyProgram;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studyProgramData = [
            [
                'name' => 'Desain Grafis',
                'degree' => 'D4',
            ],
            [
                'name' => 'Manajemen Perhotelan',
                'degree' => 'D4',
            ],
            [
                'name' => 'Keuangan dan Perbankan',
                'degree' => 'D3',
            ],
            [
                'name' => 'Administrasi Bisnis',
                'degree' => 'D3',
            ],
            [
                'name' => 'Teknologi Informasi',
                'degree' => 'D3',
            ],
        ];

        foreach ($studyProgramData as $data) {
            StudyProgram::create([
                'code' => (new CodeGeneration(StudyProgram::class, 'code', 'PST'))->getGeneratedCode(),
                'name' => $data['name'],
                'degree' => $data['degree']
            ]);
        }
    }
}
