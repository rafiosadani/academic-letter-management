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
                'full_name' => 'Mukhammad Kholid Mawardi, S.Sos., M.A.B., Ph.D',
                'email' => 'dekanfvub@gmail.com',
                'role' => 'Dekan Fakultas Vokasi',
            ],
            [
                'full_name' => 'Dr. A. Faidlal Rahman, SE.Par., M.Sc., CHE.',
                'email' => 'wdakademikfvub@gmail.com',
                'role' => 'Wakil Dekan Bidang Akademik',
            ],
            [
                'full_name' => 'Wuri Cahya Handaru, S.ST.,M.Ds.',
                'email' => 'kaprodidesgraf@gmail.com',
                'role' => 'Ketua Program Studi',
            ],
            [
                'full_name' => 'Erlangga Setyawan, SP., MM., CODP',
                'email' => 'kaprodiperhotelan@gmail.com',
                'role' => 'Ketua Program Studi',
            ],
            [
                'full_name' => 'Tri Mega Asri, S.Sos.,M.I.Kom',
                'email' => 'kaprodiadmbis@gmail.com',
                'role' => 'Ketua Program Studi',
            ],
            [
                'full_name' => 'Kusairi, SE.,ME.',
                'email' => 'kaprodikeubank@gmail.com',
                'role' => 'Ketua Program Studi',
            ],
            [
                'full_name' => 'Salnan Ratih Asriningtias, ST.,MT, MCF',
                'email' => 'kaproditi@gmail.com',
                'role' => 'Ketua Program Studi',
            ],
            [
                'full_name' => 'Pranatalia Pratami Nugraheni S.AB.',
                'email' => 'kasubbagakademik@gmail.com',
                'role' => 'Kepala Subbagian Akademik',
            ],
            [
                'full_name' => 'Muhammad Fajar Ismail, SE., M.M.',
                'email' => 'stafakademik@gmail.com',
                'role' => 'Staf Akademik',
            ],
            [
                'full_name' => 'Rafio Sadani',
                'email' => 'rafioosadani@gmail.com',
                'role' => 'Mahasiswa',
                'place_of_birth' => 'Ponorogo',
                'date_of_birth' => '2001-12-13',
                'student_or_employee_id' => '213140707111073'
            ],
        ];

        foreach ($usersToSeed as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'code' => (new CodeGeneration(User::class, 'code', 'USR'))->getGeneratedCode(),
                    'password' => bcrypt('password'),
                    'status' => 1,
                ]
            );

            // Profile (update jika sudah ada)
            $user->profile()->updateOrCreate(
                [],
                [
                    'full_name' => $data['full_name'],
                    'place_of_birth' => $data['place_of_birth'] ?? null,
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'student_or_employee_id' => $data['student_or_employee_id'] ?? null,
                ]
            );

            // Role (cek dulu)
            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }

            $this->command->info("  ✅  Created: {$data['full_name']} ({$data['role']})");
        }
        $this->command->info('  🎉 Users seeding completed!');
    }
}
