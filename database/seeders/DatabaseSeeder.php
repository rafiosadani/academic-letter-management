<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            StudyProgramSeeder::class,
            AcademicYearSeeder::class,
            FacultyOfficialSeeder::class,
            ApprovalFlowSeeder::class,
            LetterNumberConfigSeeder::class,
            SettingSeeder::class,
            LetterRequestSeeder::class
        ]);

        $this->command->info('  🎉 All seeders completed successfully!');
    }
}
