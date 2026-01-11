<div class="main-sidebar">
    <div class="flex h-full w-full flex-col items-center border-r border-slate-150 bg-white dark:border-navy-700 dark:bg-navy-800">
        <!-- Application Logo -->
        <div class="flex pt-4">
            <a href="{{ route('dashboard') }}">
                <img class="size-11 transition-transform duration-500 ease-in-out hover:scale-110 hover:rotate-[12deg]"
                     src="{{ setting('site_logo') ? Storage::url(setting('site_logo')) : asset('assets/images/logo/vokasi-ub.png') }}"
                     alt="logo"
                />
            </a>
        </div>

        <!-- Main Sections Links -->
        <div class="is-scrollbar-hidden flex grow flex-col space-y-4 overflow-y-auto pt-6">
            {{ $slot }}
        </div>

        <!-- Bottom Links -->
        <div class="flex flex-col items-center space-y-3 py-3">
            <!-- Profile -->
            <x-ui.profile-dropdown location="sidebar" size="lg"/>
        </div>
    </div>
</div>