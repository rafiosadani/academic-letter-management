<div id="{{ $wrapperId }}" class="flex">
    <button id="{{ $refId }}" class="avatar cursor-pointer {{ $config['button'] }}">
        <img class="rounded-full border border-slate-300 dark:border-navy-500"
             src="{{ auth()->user()->profile?->photo_url ?? asset('assets/images/default.png') }}"
             alt="avatar">
        <span class="absolute right-0 {{ $config['badge'] }} rounded-full {{ $config['border'] }} border-white bg-success dark:border-navy-700"></span>
    </button>

    <div id="{{ $boxId }}" class="popper-root fixed">
        <div class="popper-box w-max min-w-64 max-w-96 rounded-lg border border-slate-150 bg-white shadow-soft dark:border-navy-600 dark:bg-navy-700">

            <!-- Profile Header -->
            <div class="flex items-center space-x-4 rounded-t-lg bg-slate-100 py-2 px-4 dark:bg-navy-800">
                <div class="avatar size-11 shrink-0">
                    <img class="rounded-full"
                         src="{{ auth()->user()->profile?->photo_url ?? asset('assets/images/default.png') }}"
                         alt="avatar">
                </div>
                <div class="whitespace-nowrap pr-4">
                    <a href="/"
                       class="font-medium text-slate-700 hover:text-primary focus:text-primary dark:text-navy-100 dark:hover:text-accent-light dark:focus:text-accent-light"
                       title="{{ auth()->user()->profile?->full_name ?? 'User' }}"
                    >
                        {{ auth()->user()->profile?->short_name ?? 'User' }}
                    </a>
                    <p class="text-xs text-slate-400 dark:text-navy-300">
                        {{ auth()->user()->role?->name ?? 'Member' }}
                    </p>
                </div>
            </div>

            <!-- Profile Menu -->
            <div class="flex flex-col pt-2 pb-5">

                <!-- Profile Link -->
                <a href="{{ route('profile.edit', ['tab' => 'tab-account']) }}"
                   class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-hidden transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-warning text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                            Profile
                        </h2>
                        <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                            Pengaturan profil Anda
                        </div>
                    </div>
                </a>

                <a href="{{ route('profile.edit', ['tab' => 'tab-security']) }}"
                   class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-hidden transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-info text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                            Keamanan Akun
                        </h2>
                        <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                            Kelola kata sandi Anda
                        </div>
                    </div>
                </a>

                <!-- Logout Button -->
                <div class="mt-3 px-4">
                    <button type="button"
                            data-toggle="modal"
                            data-target="#logout-confirm-modal"
                            class="btn h-9 w-full space-x-2 bg-primary text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>