{{--
    Notification Dropdown Component - FIXED VERSION
    Place this in your navbar/header
    Width: Mobile full, Tablet 384px (sm:w-96), Desktop 448px (md:w-[28rem])
--}}

<div x-data="{ showNotifications: false }" class="relative flex">
    <button
            @click="showNotifications = !showNotifications"
            class="btn relative size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-slate-500 dark:text-navy-100" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.375 17.556h-6.75m6.75 0H21l-1.58-1.562a2.254 2.254 0 01-.67-1.596v-3.51a6.612 6.612 0 00-1.238-3.85 6.744 6.744 0 00-3.262-2.437v-.379c0-.59-.237-1.154-.659-1.571A2.265 2.265 0 0012 2c-.597 0-1.169.234-1.591.65a2.208 2.208 0 00-.659 1.572v.38c-2.621.915-4.5 3.385-4.5 6.287v3.51c0 .598-.24 1.172-.67 1.595L3 17.556h12.375zm0 0v1.11c0 .885-.356 1.733-.989 2.358A3.397 3.397 0 0112 22a3.397 3.397 0 01-2.386-.976 3.313 3.313 0 01-.989-2.357v-1.111h6.75z" />
        </svg>

        <span id="notification-badge" class="absolute -top-px -right-px flex size-3 items-center justify-center" style="display: none;">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-secondary opacity-80"></span>
            <span class="inline-flex size-2 rounded-full bg-secondary"></span>
        </span>
    </button>

    {{-- Dropdown Panel --}}
    <div
            x-show="showNotifications"
            @click.outside="showNotifications = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute right-0 top-full mt-3.5 w-[calc(100vw-1rem)] rounded-lg border border-slate-150 bg-white shadow-soft dark:border-navy-800 dark:bg-navy-700 dark:shadow-soft-dark sm:w-96 md:w-[28rem] z-50"
            style="display: none;"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between rounded-t-lg bg-slate-100 px-3 py-2 dark:bg-navy-800">
            <div class="flex items-center space-x-2">
                <h3 class="font-medium text-slate-700 dark:text-navy-100">
                    Notifikasi
                </h3>
                <div id="notification-count-badge" class="badge h-5 rounded-full bg-primary/10 px-1.5 text-primary dark:bg-accent-light/15 dark:text-accent-light">
                    0
                </div>
            </div>

            <a href="{{ route('notifications.settings') }}" class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </a>
        </div>

        {{-- Notification List --}}
        <div id="notification-list" class="max-h-[calc(100vh-6rem)] overflow-y-auto">
            <div class="flex flex-col items-center justify-center py-8">
                <i class="fa-solid fa-bell-slash text-4xl text-slate-300 dark:text-navy-400"></i>
                <p class="mt-3 text-sm text-slate-500 dark:text-navy-300">Tidak ada notifikasi</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="rounded-b-lg border-t border-slate-150 bg-slate-100 px-3 py-2 text-center dark:border-navy-600 dark:bg-navy-800">
            <a href="{{ route('notifications.index') }}" class="text-xs-plus font-medium text-primary hover:text-primary-focus dark:text-accent-light dark:hover:text-accent">
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>
</div>