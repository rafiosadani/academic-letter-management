@props(['title', 'hasPanel', 'panelTitle'])

<x-layouts.base :title="$title" :hasPanel="$hasPanel">

    {{-- Pass styles dari child ke base --}}
    @isset($styles)
        <x-slot:styles>
            {{ $styles }}
        </x-slot:styles>
    @endisset

    <!-- Page Wrapper -->
    <div id="root" class="min-h-100vh cloak flex grow bg-slate-50 dark:bg-navy-900">

        <!-- Sidebar -->
        <x-sidebar.wrapper :hasPanel="$hasPanel">

            {{-- Main Sidebar Slot --}}
            <x-slot name="main">
                <x-sidebar.main>
                    @foreach($mainMenus as $menu)
                        @php
                            // Check if menu is active
                            $isActive = collect((array) ($menu['active'] ?? []))
                                ->contains(fn($pattern) => request()->routeIs($pattern));
                        @endphp

                        <x-sidebar.nav-item
                                :route="$menu['route']"
                                :label="$menu['text']"
                                :active="$isActive"
                                :hasPanel="$menu['hasPanel'] ?? false">
                            {!! $menu['icon'] !!}
                        </x-sidebar.nav-item>
                    @endforeach
                </x-sidebar.main>
            </x-slot>

            {{-- Panel Sidebar Slot (Conditional) --}}
            @if($hasPanel && !empty($currentPanelMenus))
                <x-slot name="panel">
                    <x-sidebar.panel :title="$panelTitle">
                        @foreach($currentPanelMenus as $submenu)
                            @php
                                // Check if submenu is active
                                $isActive = collect((array) ($submenu['active'] ?? []))
                                    ->contains(fn($pattern) => request()->routeIs($pattern));
                            @endphp
                            <li>
                                <a href="{{ $submenu['route'] }}"
                                   class="nav-link flex py-2 text-xs-plus tracking-wide outline-hidden transition-colors duration-300 ease-in-out {{
                                            $isActive
                                            ? 'font-medium text-primary dark:text-accent-light'
                                            : 'text-slate-600 hover:text-slate-800 dark:text-navy-200 dark:hover:text-navy-50'
                                        }}">
                                    {{ $submenu['text'] }}
                                </a>
                            </li>
                        @endforeach
                    </x-sidebar.panel>
                </x-slot>
            @endif

        </x-sidebar.wrapper>

        <!-- App Header Wrapper -->
        <x-header.wrapper :hasPanel="$hasPanel"/>

        <!-- Mobile Searchbar -->
        <div class="mobile-searchbar fixed inset-0 z-[100] hidden flex-col bg-white dark:bg-navy-700">
            <div class="flex items-center space-x-2 bg-slate-100 px-3 pt-2 dark:bg-navy-800">
                <button class="mobile-searchbar-hide btn -ml-1.5 size-7 shrink-0 rounded-full p-0 text-slate-600 hover:bg-slate-300/20 active:bg-slate-300/25 dark:text-navy-100 dark:hover:bg-navy-300/20 dark:active:bg-navy-300/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" stroke-width="1.5"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <input class="mobile-searchbar-input form-input h-8 w-full bg-transparent placeholder-slate-400 dark:placeholder-navy-300"
                       type="text" placeholder="Cari disini...">
            </div>
        </div>

        <!-- Main Content Wrapper -->
        <main class="main-content w-full px-[var(--margin-x)] flex flex-col grow">
            {{ $slot }}
        </main>

    </div>

    {{--  Modal Logout  --}}
    <form id="logout-confirm-modal-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>

    <x-modal.confirm
        id="logout-confirm-modal"
        title="Konfirmasi Logout"
        message="Anda yakin ingin mengakhiri sesi Anda saat ini?"
        confirm-type="danger"
        confirm-text="Logout Sekarang!"
        form="logout-confirm-modal-form"
    />

    {{-- Pass scripts dari child ke base --}}
    @isset($scripts)
        <x-slot:scripts>
            {{ $scripts }}
        </x-slot:scripts>
    @endisset

</x-layouts.base>