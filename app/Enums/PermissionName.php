<?php

namespace App\Enums;

enum PermissionName: string
{
    // Dashboard
    case DASHBOARD_VIEW = 'dashboard.view';

    // Master Data - User Management
    case MASTER_USER_VIEW = 'master.user.view';
    case MASTER_USER_CREATE = 'master.user.create';
    case MASTER_USER_UPDATE = 'master.user.update';
    case MASTER_USER_DELETE = 'master.user.delete';

    // Master Data - Role Management
    case MASTER_ROLE_VIEW = 'master.role.view';
    case MASTER_ROLE_CREATE = 'master.role.create';
    case MASTER_ROLE_UPDATE = 'master.role.update';
    case MASTER_ROLE_DELETE = 'master.role.delete';

    // Master Data - Program Studi
    case MASTER_PRODI_VIEW = 'master.prodi.view';
    case MASTER_PRODI_CREATE = 'master.prodi.create';
    case MASTER_PRODI_UPDATE = 'master.prodi.update';
    case MASTER_PRODI_DELETE = 'master.prodi.delete';

    // Master Data - Tahun Akademik
    case MASTER_TAHUN_AKADEMIK_VIEW = 'master.tahun_akademik.view';
    case MASTER_TAHUN_AKADEMIK_CREATE = 'master.tahun_akademik.create';
    case MASTER_TAHUN_AKADEMIK_UPDATE = 'master.tahun_akademik.update';
    case MASTER_TAHUN_AKADEMIK_DELETE = 'master.tahun_akademik.delete';

    // Master Data - Struktur Organisasi
    case MASTER_ORGANISASI_VIEW = 'master.organisasi.view';
    case MASTER_ORGANISASI_CREATE = 'master.organisasi.create';
    case MASTER_ORGANISASI_UPDATE = 'master.organisasi.update';
    case MASTER_ORGANISASI_DELETE = 'master.organisasi.delete';

    // Faculty Officials Permissions
    case MASTER_FACULTY_OFFICIAL_VIEW = 'master.faculty_official.view';
    case MASTER_FACULTY_OFFICIAL_CREATE = 'master.faculty_official.create';
    case MASTER_FACULTY_OFFICIAL_UPDATE = 'master.faculty_official.update';
    case MASTER_FACULTY_OFFICIAL_DELETE = 'master.faculty_official.delete';

    // Surat Saya (Mahasiswa)
    case SURAT_SAYA_VIEW = 'surat.saya.view';
    case SURAT_SAYA_CREATE = 'surat.saya.create';

    // Transaksi Surat
    case SURAT_MASUK_VIEW = 'surat.masuk.view';
    case SURAT_KELOLA_VIEW = 'surat.kelola.view';
    case SURAT_KELOLA_UPDATE = 'surat.kelola.update';
    case SURAT_APPROVE = 'surat.approve';
    case SURAT_REJECT = 'surat.reject';

    // Notifikasi
    case NOTIFIKASI_VIEW = 'notifikasi.view';

    // Laporan
    case LAPORAN_STATISTIK_VIEW = 'laporan.statistik.view';
    case LAPORAN_TRACKING_VIEW = 'laporan.tracking.view';
    case LAPORAN_EXPORT = 'laporan.export';

    // Pengaturan
    case PENGATURAN_IDENTITAS_VIEW = 'pengaturan.identitas.view';
    case PENGATURAN_IDENTITAS_UPDATE = 'pengaturan.identitas.update';
    case PENGATURAN_NOMOR_SURAT_VIEW = 'pengaturan.nomor_surat.view';
    case PENGATURAN_NOMOR_SURAT_UPDATE = 'pengaturan.nomor_surat.update';
    case PENGATURAN_ALUR_APPROVAL_VIEW = 'pengaturan.alur_approval.view';
    case PENGATURAN_ALUR_APPROVAL_UPDATE = 'pengaturan.alur_approval.update';
    case PENGATURAN_NOTIFIKASI_VIEW = 'pengaturan.notifikasi.view';
    case PENGATURAN_NOTIFIKASI_UPDATE = 'pengaturan.notifikasi.update';

    // Approval Flow Management
    case APPROVAL_FLOW_VIEW = 'settings.approval_flow.view';
    case APPROVAL_FLOW_CREATE = 'settings.approval_flow.create';
    case APPROVAL_FLOW_UPDATE = 'settings.approval_flow.update';
    case APPROVAL_FLOW_DELETE = 'settings.approval_flow.delete';

    // Profile
    case PROFILE_VIEW = 'profile.view';
    case PROFILE_UPDATE = 'profile.update';

    public function groupName(): string
    {
        $main = explode('.', $this->value)[0]; // master, surat, laporan, dll.

        return match ($main) {
            'dashboard'  => 'Dashboard',
            'master'     => 'Master Data',
            'surat'      => 'Surat',
            'notifikasi' => 'Notifikasi',
            'laporan'    => 'Laporan',
            'pengaturan' => 'Pengaturan',
            'setting'    => 'Setting',
            'profile'    => 'Profile',
            default      => ucfirst($main)
        };
    }

    public function displayName(): string
    {
        $parts = explode('.', $this->value);
        $parts = array_map(fn ($p) => ucfirst(str_replace('_', ' ', $p)), $parts);

        return implode(' ', $parts); // contoh: master.user.update => "Master User Update"
    }
}
