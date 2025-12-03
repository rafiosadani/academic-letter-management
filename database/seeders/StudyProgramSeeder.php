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
            $code = $this->generateUniqueCode($data['degree'], $data['name']);

            StudyProgram::create([
                'code' => $code,
                'name' => $data['name'],
                'degree' => $data['degree']
            ]);
        }
    }

    private function generateUniqueCode(string $degree, string $name): string
    {
        // Get first 3 characters from name (alphanumeric only)
        $namePrefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($name, 0, 3)));
        $namePrefix = str_pad($namePrefix, 3, 'X'); // Pad with X if less than 3 chars

        // Get counter
        $counter = StudyProgram::withTrashed()
                ->where('code', 'like', "{$degree}-{$namePrefix}-%")
                ->count() + 1;

        return sprintf('%s-%s-%03d', $degree, $namePrefix, $counter);
    }
}
