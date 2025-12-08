<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Helpers\CodeGeneration;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::transaction(function () {
            $this->seedPermissions();
            $this->createAdministratorRole();
            $this->createDekanRole();
            $this->createWDAkademikRole();
            $this->createKetuaProgramStudiRole();
            $this->createKasubbagAkademikRole();
            $this->createDosenRole();
            $this->createStaffRole();
            $this->createMahasiswaRole();
        });

        $this->command->info('  🎉 Role & Permissions seeding completed!');
    }

    private function seedPermissions(): void
    {
        foreach (PermissionName::cases() as $permission) {
            Permission::withTrashed()->updateOrCreate(
                ['name' => $permission->value],
                [
                    'guard_name' => 'web',
                    'display_group_name' => $permission->groupName(),
                    'display_name' => $permission->displayName(),
                    'is_editable' => false,
                    'is_deletable' => false,
                ]
            )->restore();
        }
    }

    private function createAdministratorRole(): void
    {
        $administrator = Role::firstOrCreate([
            'name' => 'Administrator',
            'guard_name' => 'web',
        ], [
            'code' => (new CodeGeneration(Role::class, 'code', 'ROL'))->getGeneratedCode(),
            'is_editable' => false,
            'is_deletable' => false,
        ]);

        $administrator->syncPermissions(Permission::all());

        $this->command->info("  ✅  Created: {$administrator->name} " . ($administrator->is_editable ? '(YES)' : '(NO)') . "|" . ($administrator->is_deletable ? '(YES)' : '(NO)'));
    }

    private function createDekanRole(): void
    {
        $dekan = Role::firstOrCreate([
            'name' => 'Dekan Fakultas Vokasi',
            'guard_name' => 'web',
        ], [
            'code' => (new CodeGeneration(Role::class, 'code', 'ROL'))->getGeneratedCode(),
            'is_editable' => true,
            'is_deletable' => true,
        ]);

        $dekan->givePermissionTo([
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

        $this->command->info("  ✅  Created: {$dekan->name} " . ($dekan->is_editable ? '(YES)' : '(NO)') . "|" . ($dekan->is_deletable ? '(YES)' : '(NO)'));
    }

    private function createWDAkademikRole(): void
    {
        $wdAkademik = Role::firstOrCreate([
            'name' => 'Wakil Dekan Bidang Akademik',
            'guard_name' => 'web',
        ], [
            'code' => (new CodeGeneration(Role::class, 'code', 'ROL'))->getGeneratedCode(),
            'is_editable' => true,
            'is_deletable' => true,
        ]);

        $wdAkademik->givePermissionTo([
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

        $this->command->info("  ✅  Created: {$wdAkademik->name} " . ($wdAkademik->is_editable ? '(YES)' : '(NO)') . "|" . ($wdAkademik->is_deletable ? '(YES)' : '(NO)'));
    }

    private function createKetuaProgramStudiRole(): void
    {
        $ketuaProgramStudi = Role::firstOrCreate([
            'name' => 'Ketua Program Studi',
            'guard_name' => 'web',
        ], [
            'code' => (new CodeGeneration(Role::class, 'code', 'ROL'))->getGeneratedCode(),
            'is_editable' => true,
            'is_deletable' => true,
        ]);

        $ketuaProgramStudi->givePermissionTo([
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

        $this->command->info("  ✅  Created: {$ketuaProgramStudi->name} " . ($ketuaProgramStudi->is_editable ? '(YES)' : '(NO)') . "|" . ($ketuaProgramStudi->is_deletable ? '(YES)' : '(NO)'));
    }

    private function createKasubbagAkademikRole(): void
    {
        $kasubbagAkademik = Role::firstOrCreate([
            'name' => 'Kepala Subbagian Akademik',
            'guard_name' => 'web',
        ], [
            'code' => (new CodeGeneration(Role::class, 'code', 'ROL'))->getGeneratedCode(),
            'is_editable' => true,
            'is_deletable' => true,
        ]);

        $kasubbagAkademik->givePermissionTo([
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

        $this->command->info("  ✅  Created: {$kasubbagAkademik->name} " . ($kasubbagAkademik->is_editable ? '(YES)' : '(NO)') . "|" . ($kasubbagAkademik->is_deletable ? '(YES)' : '(NO)'));
    }

    private function createDosenRole(): void
    {
        $dosen = Role::firstOrCreate([
            'name' => 'Dosen',
            'guard_name' => 'web',
        ], [
            'code' => (new CodeGeneration(Role::class, 'code', 'ROL'))->getGeneratedCode(),
            'is_editable' => true,
            'is_deletable' => true,
        ]);

        $dosen->givePermissionTo([
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

        $this->command->info("  ✅  Created: {$dosen->name} " . ($dosen->is_editable ? '(YES)' : '(NO)') . "|" . ($dosen->is_deletable ? '(YES)' : '(NO)'));
    }

    private function createStaffRole(): void
    {
        $staff = Role::firstOrCreate([
            'name' => 'Staf Akademik',
            'guard_name' => 'web',
        ], [
            'code' => (new CodeGeneration(Role::class, 'code', 'ROL'))->getGeneratedCode(),
            'is_editable' => true,
            'is_deletable' => true,
        ]);

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

        $this->command->info("  ✅  Created: {$staff->name} " . ($staff->is_editable ? '(YES)' : '(NO)') . "|" . ($staff->is_deletable ? '(YES)' : '(NO)'));
    }

    private function createMahasiswaRole(): void
    {
        $mhs = Role::firstOrCreate([
            'name' => 'Mahasiswa',
            'guard_name' => 'web',
        ], [
            'code' => (new CodeGeneration(Role::class, 'code', 'ROL'))->getGeneratedCode(),
            'is_editable' => true,
            'is_deletable' => true,
        ]);

        $mhs->givePermissionTo([
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

        $this->command->info("  ✅  Created: {$mhs->name} " . ($mhs->is_editable ? '(YES)' : '(NO)') . "|" . ($mhs->is_deletable ? '(YES)' : '(NO)'));
    }
}
