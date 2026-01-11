<?php

namespace Database\Seeders;

use App\Models\LetterNumberConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LetterNumberConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('  🔢 Starting Letter Number Config seeding...');

        $configs = [
            [
                'letter_type' => 'penelitian',
                'prefix' => 'UN10.F1601',
                'code' => 'LL',
                'padding' => 3
            ],
            [
                'letter_type' => 'dispensasi_kuliah',
                'prefix' => 'UN10.F1601',
                'code' => 'DK',
                'padding' => 3
            ],
            [
                'letter_type' => 'dispensasi_mahasiswa',
                'prefix' => 'UN10.F1601',
                'code' => 'DM',
                'padding' => 3
            ],
        ];

        foreach ($configs as $config) {
            LetterNumberConfig::updateOrCreate(
                ['letter_type' => $config['letter_type']],
                $config
            );

            $letterType = \App\Enums\LetterType::from($config['letter_type']);
            $this->command->info("  ✅  {$letterType->label()} ({$config['code']}) configured");
        }

        $this->command->info('  🎉 Letter Number Config seeding completed!');
    }
}
