<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = collect(PermissionName::cases())
            ->map(fn($permission) => [
                'name' => $permission->value,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

        Permission::insert($permissions);

        $this->createAdministratorRole();
        $this->createStaffRole();
        $this->createMahasiswaRole();
    }

    private function createAdministratorRole(): void
    {
        $administrator = Role::create(['name' => 'Administrator']);
        $administrator->givePermissionTo(Permission::all());
    }

    private function createStaffRole(): void
    {
        $staff = Role::create(['name' => 'Staf Akademik']);
        $staff->givePermissionTo([
            PermissionName::DASHBOARD_VIEW->value,

            // Transaksi Surat
            PermissionName::SURAT_MASUK_VIEW->value,
            PermissionName::SURAT_KELOLA_VIEW->value,
            PermissionName::SURAT_KELOLA_UPDATE->value,
            PermissionName::SURAT_APPROVE->value,
            PermissionName::SURAT_REJECT->value,

            // Notifikasi
            PermissionName::NOTIFIKASI_VIEW->value,

            // Laporan
            PermissionName::LAPORAN_STATISTIK_VIEW->value,
            PermissionName::LAPORAN_TRACKING_VIEW->value,

            // Profile
            PermissionName::PROFILE_VIEW->value,
            PermissionName::PROFILE_UPDATE->value,
        ]);
    }

    private function createMahasiswaRole(): void
    {
        $mahasiswa = Role::create(['name' => 'Mahasiswa']);
        $mahasiswa->givePermissionTo([
            PermissionName::DASHBOARD_VIEW->value,

            // Surat Saya
            PermissionName::SURAT_SAYA_VIEW->value,
            PermissionName::SURAT_SAYA_CREATE->value,

            // Notifikasi
            PermissionName::NOTIFIKASI_VIEW->value,

            // Profile
            PermissionName::PROFILE_VIEW->value,
            PermissionName::PROFILE_UPDATE->value,
        ]);
    }
}
