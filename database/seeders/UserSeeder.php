<?php

namespace Database\Seeders;

use App\Helpers\CodeGeneration;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersToSeed = [
            [
                'full_name' => 'Administrator Sistem',
                'email' => 'administrator@gmail.com',
                'role' => 'Administrator',
            ],
            [
                'full_name' => 'Kepala Subbagian Akademik',
                'email' => 'kasubbagakademik@gmail.com',
                'role' => 'Kepala Subbagian Akademik',
            ],
            [
                'full_name' => 'Staf Akademik',
                'email' => 'stafakademik@gmail.com',
                'role' => 'Staf Akademik',
            ],
            [
                'full_name' => 'Mahasiswa',
                'email' => 'mahasiswa@gmail.com',
                'role' => 'Mahasiswa',
            ],
        ];

        foreach ($usersToSeed as $data) {
            $user = User::create([
                'code' => (new CodeGeneration(User::class, 'code', 'USR'))->getGeneratedCode(),
                'email' => $data['email'],
                'password' => bcrypt('password'), // Password default
                'status' => 1, // 1 = aktif
            ]);

            $user->profile()->create([
                'full_name' => $data['full_name'],
            ]);

            $user->assignRole($data['role']);

            $this->command->info("   ✅ Created: {$data['full_name']} ({$data['role']})");
        }
        $this->command->info('  🎉 Users seeding completed!');
    }
}
