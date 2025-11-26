<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrator
        $administrator = User::create([
            'name' => 'Administrator',
            'email' => 'administrator@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $administrator->assignRole('Administrator');

        // Kepala Subbagian Akademik
        $staff = User::create([
            'name' => 'Kepala Subbagian Akademik',
            'email' => 'kasubbagakademik@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $staff->assignRole('Kepala Subbagian Akademik');

        // Staf Akademik
        $staff = User::create([
            'name' => 'Staf Akademik',
            'email' => 'stafakademik@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $staff->assignRole('Staf Akademik');

        // Mahasiswa
        $mahasiswa = User::create([
            'name' => 'Mahasiswa',
            'email' => 'mahasiswa@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $mahasiswa->assignRole('Mahasiswa');
    }
}
