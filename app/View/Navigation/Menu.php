<?php

namespace App\View\Navigation;

use App\Enums\PermissionName;

class Menu
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function main(): array
    {
        $menus = [
            self::dashboard(),
            self::masterData(),
            self::suratSaya(),
            self::transaksiSurat(),
            self::notifikasi(),
            self::laporan(),
            self::pengaturan(),
            self::profile(),
        ];

        return collect($menus)
            ->filter(fn($menu) => ($menu['authorized'] ?? true) !== false)
            ->values()
            ->toArray();
    }

    private static function dashboard(): array
    {
        $hasPermission = auth()->user()->hasPermissionTo(PermissionName::DASHBOARD_VIEW->value) ?? false;

        return [
            'text'      => 'Dashboard',
            'route'     => route('dashboard'),
            'icon'      => self::iconDashboard(),
            'active'    => ['dashboard'],
            'hasPanel'  => false,
            'authorized'=> $hasPermission,
        ];
    }

    private static function masterData(): array
    {
        $submenus = [
            [
                'text' => 'Manajemen Pengguna',
                'route' => route('master.users.index'),
                'active' => ['master.users.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_USER_VIEW->value) ?? false,
            ],
            [
                'text' => 'Manajemen Role & Permission',
                'route' => route('master.roles.index'),
                'active' => ['master.roles.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_ROLE_VIEW->value) ?? false,
            ],
            [
                'text' => 'Program Studi',
//                'route' => route('master.prodi.index'),
                'route' => '#',
                'active' => ['master.prodi.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_PRODI_VIEW->value) ?? false,
            ],
            [
                'text' => 'Tahun Akademik & Semester',
//                'route' => route('master.tahun-akademik.index'),
                'route' => '#',
                'active' => ['master.tahun-akademik.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_TAHUN_AKADEMIK_VIEW->value) ?? false,
            ],
            [
                'text' => 'Struktur Organisasi',
//                'route' => route('master.organisasi.index'),
                'route' => '#',
                'active' => ['master.organisasi.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_ORGANISASI_VIEW->value) ?? false,
            ],
        ];

        $authorizedSubmenus = collect($submenus)
            ->filter(fn($sub) => $sub['authorized'])
            ->values()
            ->toArray();

        if (empty($authorizedSubmenus)) {
            return ['authorized' => false];
        }

        return [
            'text' => 'Master Data',
            'route' => route('master.roles.index'),
            'icon' => self::iconMasterData(),
            'active' => ['master.*'],
            'hasPanel' => true,
            'panelTitle' => 'Master Data',
            'submenu' => $authorizedSubmenus,
            'authorized' => true,
        ];
    }

    private static function suratSaya(): array
    {
        $submenus = [
            [
                'text' => 'Ajukan Surat Baru',
//                'route' => route('surat.create'),
                'route' => '#',
                'active' => ['surat.create'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::SURAT_SAYA_CREATE->value) ?? false,
            ],
            [
                'text' => 'Daftar Pengajuan Saya',
//                'route' => route('surat.index'),
                'route' => '#',
                'active' => ['surat.index', 'surat.show'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::SURAT_SAYA_VIEW->value) ?? false,
            ],
        ];

        $authorizedSubmenus = collect($submenus)
            ->filter(fn($sub) => $sub['authorized'])
            ->values()
            ->toArray();

        if (empty($authorizedSubmenus)) {
            return ['authorized' => false];
        }

        return [
            'text' => 'Surat Saya',
            'route' => '#',
            'icon' => '<i class="fa-solid fa-envelope fa-xl"></i>',
            'active' => ['surat.*'],
            'hasPanel' => true,
            'panelTitle' => 'Surat Saya',
            'submenu' => $authorizedSubmenus,
            'authorized' => true,
        ];
    }

    private static function transaksiSurat(): array
    {
        $submenus = [
            [
                'text' => 'Surat Masuk',
//                'route' => route('transaksi.masuk.index'),
                'route' => '#',
                'active' => ['transaksi.masuk.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::SURAT_MASUK_VIEW->value) ?? false,
            ],
            [
                'text' => 'Kelola Semua Surat',
//                'route' => route('transaksi.kelola.index'),
                'route' => '#',
                'active' => ['transaksi.kelola.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::SURAT_KELOLA_VIEW->value) ?? false,
            ],
        ];

        $authorizedSubmenus = collect($submenus)
            ->filter(fn($sub) => $sub['authorized'])
            ->values()
            ->toArray();

        if (empty($authorizedSubmenus)) {
            return ['authorized' => false];
        }

        return [
            'text' => 'Transaksi Surat',
            'route' => '#',
            'icon' => '<i class="fa-solid fa-file-invoice fa-xl"></i>',
            'active' => ['transaksi.*'],
            'hasPanel' => true,
            'panelTitle' => 'Transaksi Surat',
            'submenu' => $authorizedSubmenus,
            'authorized' => true,
        ];
    }

    private static function notifikasi(): array
    {
        $hasPermission = auth()->user()?->hasPermissionTo(PermissionName::NOTIFIKASI_VIEW->value) ?? false;

        return [
            'text' => 'Notifikasi',
//            'route' => route('notifikasi.index'),
            'route' => '#',
            'icon' => '<i class="fa-solid fa-bell fa-xl"></i>',
            'active' => ['notifikasi.*'],
            'hasPanel' => false,
            'authorized' => $hasPermission,
        ];
    }

    private static function laporan(): array
    {
        $submenus = [
            [
                'text' => 'Statistik Surat',
//                'route' => route('laporan.statistik.index'),
                'route' => '#',
                'active' => ['laporan.statistik.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::LAPORAN_STATISTIK_VIEW->value) ?? false,
            ],
            [
                'text' => 'Tracking Riwayat Approval',
//                'route' => route('laporan.tracking.index'),
                'route' => '#',
                'active' => ['laporan.tracking.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::LAPORAN_TRACKING_VIEW->value) ?? false,
            ],
            [
                'text' => 'Export Data',
//                'route' => route('laporan.export.index'),
                'route' => '#',
                'active' => ['laporan.export.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::LAPORAN_EXPORT->value) ?? false,
            ],
        ];

        $authorizedSubmenus = collect($submenus)
            ->filter(fn($sub) => $sub['authorized'])
            ->values()
            ->toArray();

        if (empty($authorizedSubmenus)) {
            return ['authorized' => false];
        }

        return [
            'text' => 'Laporan',
            'route' => '#',
            'icon' => '<i class="fa-solid fa-chart-line fa-xl"></i>',
            'active' => ['laporan.*'],
            'hasPanel' => true,
            'panelTitle' => 'Laporan',
            'submenu' => $authorizedSubmenus,
            'authorized' => true,
        ];
    }

    private static function pengaturan(): array
    {
        $submenus = [
            [
                'text' => 'Identitas Fakultas',
//                'route' => route('pengaturan.identitas.index'),
                'route' => '#',
                'active' => ['pengaturan.identitas.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::PENGATURAN_IDENTITAS_VIEW->value) ?? false,
            ],
            [
                'text' => 'Konfigurasi Nomor Surat',
//                'route' => route('pengaturan.nomor.index'),
                'route' => '#',
                'active' => ['pengaturan.nomor.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::PENGATURAN_NOMOR_SURAT_VIEW->value) ?? false,
            ],
            [
                'text' => 'Alur Persetujuan',
//                'route' => route('pengaturan.alur.index'),
                'route' => '#',
                'active' => ['pengaturan.alur.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::PENGATURAN_ALUR_APPROVAL_VIEW->value) ?? false,
            ],
            [
                'text' => 'Pengaturan Notifikasi',
//                'route' => route('pengaturan.notifikasi.index'),
                'route' => '#',
                'active' => ['pengaturan.notifikasi.*'],
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::PENGATURAN_NOTIFIKASI_VIEW->value) ?? false,
            ],
        ];

        $authorizedSubmenus = collect($submenus)
            ->filter(fn($sub) => $sub['authorized'])
            ->values()
            ->toArray();

        if (empty($authorizedSubmenus)) {
            return ['authorized' => false];
        }

        return [
            'text' => 'Pengaturan',
            'route' => '#',
            'icon' => '<i class="fa-solid fa-cog fa-xl"></i>',
            'active' => ['pengaturan.*'],
            'hasPanel' => true,
            'panelTitle' => 'Pengaturan',
            'submenu' => $authorizedSubmenus,
            'authorized' => true,
        ];
    }

    private static function profile(): array
    {
        $hasPermission = auth()->user()?->hasPermissionTo(PermissionName::PROFILE_VIEW->value) ?? false;

        return [
            'text' => 'Profil Saya',
//            'route' => route('profile.show'),
            'route' => '#',
            'icon' => '<i class="fa-solid fa-user fa-xl"></i>',
            'active' => ['profile.*'],
            'hasPanel' => false,
            'authorized' => $hasPermission,
        ];
    }

    // ==========================================
    // ICON METHODS
    // ==========================================

    private static function iconDashboard(): string
    {
        return <<<'SVG'
        <svg class="size-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path fill="currentColor" fill-opacity=".3" d="M5 14.059c0-1.01 0-1.514.222-1.945.221-.43.632-.724 1.453-1.31l4.163-2.974c.56-.4.842-.601 1.162-.601.32 0 .601.2 1.162.601l4.163 2.974c.821.586 1.232.88 1.453 1.31.222.43.222.935.222 1.945V19c0 .943 0 1.414-.293 1.707C18.414 21 17.943 21 17 21H7c-.943 0-1.414 0-1.707-.293C5 20.414 5 19.943 5 19v-4.94Z"/>
            <path fill="currentColor" d="M3 12.387c0 .267 0 .4.084.441.084.041.19-.04.4-.204l7.288-5.669c.59-.459.885-.688 1.228-.688.343 0 .638.23 1.228.688l7.288 5.669c.21.163.316.245.4.204.084-.04.084-.174.084-.441v-.409c0-.48 0-.72-.102-.928-.101-.208-.291-.355-.67-.65l-7-5.445c-.59-.459-.885-.688-1.228-.688-.343 0-.638.23-1.228.688l-7 5.445c-.379.295-.569.442-.67.65-.102.208-.102.448-.102.928v.409Z"/>
            <path fill="currentColor" d="M11.5 15.5h1A1.5 1.5 0 0 1 14 17v3.5h-4V17a1.5 1.5 0 0 1 1.5-1.5Z"/>
            <path fill="currentColor" d="M17.5 5h-1a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5Z"/>
        </svg>
        SVG;
    }

    private static function iconMasterData(): string
    {
        return <<<'SVG'
        <svg class="size-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.85714 3H4.14286C3.51167 3 3 3.51167 3 4.14286V9.85714C3 10.4883 3.51167 11 4.14286 11H9.85714C10.4883 11 11 10.4883 11 9.85714V4.14286C11 3.51167 10.4883 3 9.85714 3Z" fill="currentColor"/>
            <path d="M9.85714 12.8999H4.14286C3.51167 12.8999 3 13.4116 3 14.0428V19.757C3 20.3882 3.51167 20.8999 4.14286 20.8999H9.85714C10.4883 20.8999 11 20.3882 11 19.757V14.0428C11 13.4116 10.4883 12.8999 9.85714 12.8999Z" fill="currentColor" fill-opacity="0.3"/>
            <path d="M19.757 3H14.0428C13.4116 3 12.8999 3.51167 12.8999 4.14286V9.85714C12.8999 10.4883 13.4116 11 14.0428 11H19.757C20.3882 11 20.8999 10.4883 20.8999 9.85714V4.14286C20.8999 3.51167 20.3882 3 19.757 3Z" fill="currentColor" fill-opacity="0.3"/>
            <path d="M19.757 12.8999H14.0428C13.4116 12.8999 12.8999 13.4116 12.8999 14.0428V19.757C12.8999 20.3882 13.4116 20.8999 14.0428 20.8999H19.757C20.3882 20.8999 20.8999 20.3882 20.8999 19.757V14.0428C20.8999 13.4116 20.3882 12.8999 19.757 12.8999Z" fill="currentColor" fill-opacity="0.3"/>
        </svg>
        SVG;
    }
}
