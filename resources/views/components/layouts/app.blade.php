@props(['title', 'hasPanel', 'panelTitle'])

<x-layouts.base :title="$title">

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

                    <!-- Dashboard Menu -->
                    <x-sidebar.nav-item
                            route="{{ route('dashboard') }}"
                            label="Dashboard"
                            :active="request()->routeIs('dashboard')"
                            :hasPanel="false">
                        <svg
                                class="size-7"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                        >
                            <path
                                    fill="currentColor"
                                    fill-opacity=".3"
                                    d="M5 14.059c0-1.01 0-1.514.222-1.945.221-.43.632-.724 1.453-1.31l4.163-2.974c.56-.4.842-.601 1.162-.601.32 0 .601.2 1.162.601l4.163 2.974c.821.586 1.232.88 1.453 1.31.222.43.222.935.222 1.945V19c0 .943 0 1.414-.293 1.707C18.414 21 17.943 21 17 21H7c-.943 0-1.414 0-1.707-.293C5 20.414 5 19.943 5 19v-4.94Z"
                            />
                            <path
                                    fill="currentColor"
                                    d="M3 12.387c0 .267 0 .4.084.441.084.041.19-.04.4-.204l7.288-5.669c.59-.459.885-.688 1.228-.688.343 0 .638.23 1.228.688l7.288 5.669c.21.163.316.245.4.204.084-.04.084-.174.084-.441v-.409c0-.48 0-.72-.102-.928-.101-.208-.291-.355-.67-.65l-7-5.445c-.59-.459-.885-.688-1.228-.688-.343 0-.638.23-1.228.688l-7 5.445c-.379.295-.569.442-.67.65-.102.208-.102.448-.102.928v.409Z"
                            />
                            <path
                                    fill="currentColor"
                                    d="M11.5 15.5h1A1.5 1.5 0 0 1 14 17v3.5h-4V17a1.5 1.5 0 0 1 1.5-1.5Z"
                            />
                            <path
                                    fill="currentColor"
                                    d="M17.5 5h-1a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5Z"
                            />
                        </svg>
                    </x-sidebar.nav-item>

                    <!-- Master Data Menu -->
                    <x-sidebar.nav-item
                            {{--                            route="{{ route('master.index') }}"--}}
                            route="/"
                            label="Master Data"
                            :active="request()->routeIs('master.*')"
                            :hasPanel="true">
                        <svg class="size-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.85714 3H4.14286C3.51167 3 3 3.51167 3 4.14286V9.85714C3 10.4883 3.51167 11 4.14286 11H9.85714C10.4883 11 11 10.4883 11 9.85714V4.14286C11 3.51167 10.4883 3 9.85714 3Z"
                                  fill="currentColor"/>
                            <path d="M9.85714 12.8999H4.14286C3.51167 12.8999 3 13.4116 3 14.0428V19.757C3 20.3882 3.51167 20.8999 4.14286 20.8999H9.85714C10.4883 20.8999 11 20.3882 11 19.757V14.0428C11 13.4116 10.4883 12.8999 9.85714 12.8999Z"
                                  fill="currentColor" fill-opacity="0.3"/>
                            <path d="M19.757 3H14.0428C13.4116 3 12.8999 3.51167 12.8999 4.14286V9.85714C12.8999 10.4883 13.4116 11 14.0428 11H19.757C20.3882 11 20.8999 10.4883 20.8999 9.85714V4.14286C20.8999 3.51167 20.3882 3 19.757 3Z"
                                  fill="currentColor" fill-opacity="0.3"/>
                            <path d="M19.757 12.8999H14.0428C13.4116 12.8999 12.8999 13.4116 12.8999 14.0428V19.757C12.8999 20.3882 13.4116 20.8999 14.0428 20.8999H19.757C20.3882 20.8999 20.8999 20.3882 20.8999 19.757V14.0428C20.8999 13.4116 20.3882 12.8999 19.757 12.8999Z"
                                  fill="currentColor" fill-opacity="0.3"/>
                        </svg>
                    </x-sidebar.nav-item>

                    <!-- Pengajuan Surat Menu -->
                    <x-sidebar.nav-item
                            {{--                            route="{{ route('surat.index') }}"--}}
                            route="/"
                            label="Pengajuan Surat"
                            :active="request()->routeIs('surat.*')"
                            :hasPanel="false">
                        <i class="fa-solid fa-envelope fa-xl"></i>
                        {{--                        <svg class="size-8 flex items-center" viewBox="0 0 24 24" fill="none"--}}
                        {{--                             xmlns="http://www.w3.org/2000/svg">--}}
                        {{--                            <path fill="currentColor" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>--}}
                        {{--                            <path fill="currentColor" fill-opacity="0.3" fill-rule="evenodd"--}}
                        {{--                                  d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"--}}
                        {{--                                  clip-rule="evenodd"/>--}}
                        {{--                        </svg>--}}
                    </x-sidebar.nav-item>

                </x-sidebar.main>
            </x-slot>

            {{-- Panel Sidebar Slot (Conditional) --}}
            @if($hasPanel)
                <x-slot name="panel">
                    <x-sidebar.panel :title="$panelTitle">
                        {{ $panel ?? '' }}
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
        <main class="main-content w-full px-[var(--margin-x)] pb-8">
            {{ $slot }}
        </main>

    </div>

    {{-- Pass scripts dari child ke base --}}
    @isset($scripts)
        <x-slot:styles>
            {{ $scripts }}
        </x-slot:styles>
    @endisset

</x-layouts.base>