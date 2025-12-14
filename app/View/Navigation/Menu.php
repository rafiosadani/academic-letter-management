<?php

namespace App\View\Navigation;

use App\Enums\PermissionName;

class Menu
{
    /**
     * Get main menu items
     */
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
        $hasPermission = auth()->user()?->hasPermissionTo(PermissionName::DASHBOARD_VIEW->value) ?? false;

        return [
            'text' => 'Dashboard',
            'route' => route('dashboard'),
            'icon' => self::iconDashboard(),
            'active' => ['dashboard'],
            'hasPanel' => false,
            'authorized' => $hasPermission,
        ];
    }

    private static function masterData(): array
    {
        $submenus = [
            [
                'text' => 'Manajemen Pengguna',
                'route' => route('master.users.index'),
                'active' => ['master.users.*'],
                'icon' => 'fa-user-gear',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_USER_VIEW->value) ?? false,
            ],
            [
                'text' => 'Manajemen Role & Permission',
                'route' => route('master.roles.index'),
                'active' => ['master.roles.*'],
                'icon' => 'fa-shield-halved',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_ROLE_VIEW->value) ?? false,
            ],
            [
                'text' => 'Program Studi',
                'route' => route('master.study-programs.index'),
                'active' => ['master.study-programs.*'],
                'icon' => 'fa-graduation-cap',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_STUDY_PROGRAM_VIEW->value) ?? false,
            ],
            [
                'text' => 'Tahun Akademik',
                'route' => route('master.academic-years.index'),
                'active' => ['master.academic-years.*'],
                'icon' => 'fa-calendar-days',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_ACADEMIC_YEAR_VIEW->value) ?? false,
            ],
            [
                'text' => 'Semester',
                'route' => route('master.semesters.index'),
                'active' => ['master.semesters.*'],
                'icon' => 'fa-layer-group',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_SEMESTER_VIEW->value) ?? false,
            ],
            [
                'text' => 'Penugasan Jabatan',
                'route' => route('master.faculty-officials.index'),
                'active' => ['master.faculty-officials.*'],
                'icon' => 'fa-user-tie',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::MASTER_FACULTY_OFFICIAL_VIEW->value) ?? false,
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
            'route' => route('master.users.index'),
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
                'route' => '#', // TODO: Implement route
                'active' => ['letter.my.create'],
                'icon' => 'fa-plus-circle',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::LETTER_MY_CREATE->value) ?? false,
            ],
            [
                'text' => 'Daftar Pengajuan Saya',
                'route' => '#', // TODO: Implement route
                'active' => ['letter.my.*'],
                'icon' => 'fa-list-alt',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::LETTER_MY_VIEW->value) ?? false,
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
            'icon' => self::iconSuratSaya(),
            'active' => ['letter.my.*'],
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
                'route' => '#', // TODO: Implement route
                'active' => ['letter.incoming.*'],
                'icon' => 'fa-inbox',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::LETTER_INCOMING_VIEW->value) ?? false,
            ],
            [
                'text' => 'Kelola Semua Surat',
                'route' => '#', // TODO: Implement route
                'active' => ['letter.manage.*'],
                'icon' => 'fa-folder-open',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::LETTER_MANAGE_VIEW->value) ?? false,
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
            'icon' => self::iconTransaksiSurat(),
            'active' => ['letter.incoming.*', 'letter.manage.*'],
            'hasPanel' => true,
            'panelTitle' => 'Transaksi Surat',
            'submenu' => $authorizedSubmenus,
            'authorized' => true,
        ];
    }

    private static function notifikasi(): array
    {
        $hasPermission = auth()->user()?->hasPermissionTo(PermissionName::NOTIFICATION_VIEW->value) ?? false;

        return [
            'text' => 'Notifikasi',
            'route' => route('notifications.index'),
            'icon' => self::iconNotifikasi(),
            'active' => ['notifications.index'],
            'hasPanel' => false,
            'authorized' => $hasPermission,
        ];
    }

    private static function laporan(): array
    {
        $submenus = [
            [
                'text' => 'Statistik Surat',
                'route' => '#', // TODO: Implement route
                'active' => ['report.statistic.*'],
                'icon' => 'fa-chart-bar',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::REPORT_STATISTIC_VIEW->value) ?? false,
            ],
            [
                'text' => 'Tracking Riwayat Approval',
                'route' => '#', // TODO: Implement route
                'active' => ['report.tracking.*'],
                'icon' => 'fa-route',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::REPORT_TRACKING_VIEW->value) ?? false,
            ],
            [
                'text' => 'Export Data',
                'route' => '#', // TODO: Implement route
                'active' => ['report.export.*'],
                'icon' => 'fa-file-export',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::REPORT_EXPORT->value) ?? false,
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
            'icon' => self::iconLaporan(),
            'active' => ['report.*'],
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
                'text' => 'Pengaturan Umum',
                'route' => route('settings.general.edit'),
                'active' => ['settings.general.*'],
                'icon' => 'fa-cog',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::SETTINGS_GENERAL_VIEW->value) ?? false,
            ],
            [
                'text' => 'Alur Persetujuan',
                'route' => route('settings.approval-flows.index'),
                'active' => ['settings.approval-flows.*'],
                'icon' => 'fa-code-branch',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::SETTINGS_APPROVAL_FLOW_VIEW->value) ?? false,
            ],
            [
                'text' => 'Konfigurasi Nomor Surat',
                'route' => route('settings.letter-number-configs.index'),
                'active' => ['settings.letter-number-configs.*'],
                'icon' => 'fa-hashtag',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::SETTINGS_LETTER_NUMBER_VIEW->value) ?? false,
            ],
            [
                'text' => 'Pengaturan Notifikasi',
                'route' => route('notifications.settings'),
                'active' => ['notifications.settings'],
                'icon' => 'fa-bell',
                'authorized' => auth()->user()?->hasPermissionTo(PermissionName::SETTINGS_NOTIFICATION_VIEW->value) ?? false,
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
            'route' => route('notifications.settings'),
            'icon' => self::iconPengaturan(),
            'active' => ['settings.*', 'notifications.settings'],
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
            'route' => '#', // TODO: Implement route
            'icon' => self::iconProfile(),
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

    private static function iconSuratSaya(): string
    {
        return <<<'SVG'
            <svg class="size-5.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path fill="currentColor" fill-opacity="0.4" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48z"/>
                <path fill="currentColor" d="M0 176v208c0 35.3 28.7 64 64 64h384c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/>
            </svg>
        SVG;
    }

    private static function iconTransaksiSurat(): string
    {
        return <<<'SVG'
            <svg class="size-7 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill="currentColor" fill-opacity="0.25" d="M21.0001 16.05V18.75C21.0001 20.1 20.1001 21 18.7501 21H6.6001C6.9691 21 7.3471 20.946 7.6981 20.829C7.7971 20.793 7.89609 20.757 7.99509 20.712C8.31009 20.586 8.61611 20.406 8.88611 20.172C8.96711 20.109 9.05711 20.028 9.13811 19.947L9.17409 19.911L15.2941 13.8H18.7501C20.1001 13.8 21.0001 14.7 21.0001 16.05Z"/>
                <path fill="currentColor" fill-opacity="0.5" d="M17.7324 11.361L15.2934 13.8L9.17334 19.9111C9.80333 19.2631 10.1993 18.372 10.1993 17.4V8.70601L12.6384 6.26701C13.5924 5.31301 14.8704 5.31301 15.8244 6.26701L17.7324 8.17501C18.6864 9.12901 18.6864 10.407 17.7324 11.361Z"/>
                <path fill="currentColor" d="M7.95 3H5.25C3.9 3 3 3.9 3 5.25V17.4C3 17.643 3.02699 17.886 3.07199 18.12C3.09899 18.237 3.12599 18.354 3.16199 18.471C3.20699 18.606 3.252 18.741 3.306 18.867V18.885C3.44101 19.146 3.585 19.389 3.756 19.614C3.855 19.731 3.95401 19.839 4.05301 19.947C4.15201 20.055 4.26 20.145 4.377 20.235L4.38601 20.244C4.61101 20.415 4.854 20.559 5.106 20.685C5.25001 20.748 5.385 20.793 5.529 20.838C5.646 20.874 5.76301 20.901 5.88001 20.928C6.11401 20.973 6.357 21 6.6 21C6.969 21 7.347 20.946 7.698 20.829C7.797 20.793 7.89599 20.757 7.99499 20.712C8.30999 20.586 8.61601 20.406 8.88601 20.172C8.96701 20.109 9.05701 20.028 9.13801 19.947L9.17399 19.911C9.80399 19.263 10.2 18.372 10.2 17.4V5.25C10.2 3.9 9.3 3 7.95 3ZM6.6 18.75C5.853 18.75 5.25 18.147 5.25 17.4C5.25 16.653 5.853 16.05 6.6 16.05C7.347 16.05 7.95 16.653 7.95 17.4C7.95 18.147 7.347 18.75 6.6 18.75Z"/>
            </svg>
        SVG;
    }

    private static function iconNotifikasi(): string
    {
        return <<<'SVG'
            <svg class="size-5.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                <path fill="currentColor" fill-opacity="0.5" d="M224 0c-17.7 0-32 14.3-32 32v19.2C119 66 64 130.6 64 208v18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416h384c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8V208c0-77.4-55-142-128-156.8V32c0-17.7-14.3-32-32-32z"/>
                <path fill="currentColor" d="M269.3 493.3c12-12 18.7-28.3 18.7-45.3H160c0 17 6.7 33.3 18.7 45.3S206.3 512 224 512s33.3-6.7 45.3-18.7z"/>
            </svg>
        SVG;
    }

    private static function iconLaporan(): string
    {
        return <<<'SVG'
            <svg class="size-6 shrink-0" viewBox="0 0 448 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 272c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48v160c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V272z" fill="currentColor" fill-opacity="0.25"/>
                <path d="M160 80c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48v352c0 26.5-21.5 48-48 48h-32c-26.5 0-48-21.5-48-48V80z" fill="currentColor" fill-opacity="0.55"/>
                <path d="M368 96h32c26.5 0 48 21.5 48 48v288c0 26.5-21.5 48-48 48h-32c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48z" fill="currentColor"/>
            </svg>
        SVG;
    }

    private static function iconPengaturan(): string
    {
        return <<<'SVG'
            <svg class="size-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-opacity="0.3" fill="currentColor" d="M2 12.947v-1.771c0-1.047.85-1.913 1.899-1.913 1.81 0 2.549-1.288 1.64-2.868a1.919 1.919 0 0 1 .699-2.607l1.729-.996c.79-.474 1.81-.192 2.279.603l.11.192c.9 1.58 2.379 1.58 3.288 0l.11-.192c.47-.795 1.49-1.077 2.279-.603l1.73.996a1.92 1.92 0 0 1 .699 2.607c-.91 1.58-.17 2.868 1.639 2.868 1.04 0 1.899.856 1.899 1.912v1.772c0 1.047-.85 1.912-1.9 1.912-1.808 0-2.548 1.288-1.638 2.869.52.915.21 2.083-.7 2.606l-1.729.997c-.79.473-1.81.191-2.279-.604l-.11-.191c-.9-1.58-2.379-1.58-3.288 0l-.11.19c-.47.796-1.49 1.078-2.279.605l-1.73-.997a1.919 1.919 0 0 1-.699-2.606c.91-1.58.17-2.869-1.639-2.869A1.911 1.911 0 0 1 2 12.947Z"/>
                <path fill="currentColor" d="M11.995 15.332c1.794 0 3.248-1.464 3.248-3.27 0-1.807-1.454-3.272-3.248-3.272-1.794 0-3.248 1.465-3.248 3.271 0 1.807 1.454 3.271 3.248 3.271Z"/>
            </svg>
        SVG;
    }

    private static function iconProfile(): string
    {
        return <<<'SVG'
            <svg class="size-7 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path fill="currentColor" d="M12 3.5a4.25 4.25 0 1 1 0 8.5a4.25 4.25 0 0 1 0-8.5Z" />
                <path fill="currentColor" fill-opacity=".3" d="M4 20c0-3.9 4-6.5 8-6.5s8 2.6 8 6.5v1H4v-1Z" />
            </svg>
        SVG;
    }



}
