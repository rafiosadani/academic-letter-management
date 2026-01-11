@props(['title', 'hasPanel', 'panelTitle'])

<x-layouts.base title="Error 404 Not Found">
    <!-- Page Wrapper -->
    <div id="root" class="min-h-100vh cloak flex grow bg-slate-50 dark:bg-navy-900">
        <!-- Main Content Wrapper -->
        <main class="grid w-full grow grid-cols-1 place-items-center">
            <div class="max-w-2xl p-6 text-center">
                <div class="w-full max-w-md">
                    <img
                            class="w-full"
                            id="hero-image-light"
                            src="{{ asset('assets/images/illustrations/error-404.svg') }}"
                            alt="image"
                    />
                    <img
                            class="w-full"
                            id="hero-image-dark"
                            src="{{ asset('assets/images/illustrations/error-404-dark.svg') }}"
                            alt="image"
                    />
                </div>
                <p
                        class="pt-4 text-xl font-semibold text-slate-800 dark:text-navy-50"
                >
                    Oops. This Page Not Found.
                </p>
                <p class="pt-2 text-slate-500 dark:text-navy-200">
                    This page you are looking not available. Please back to home
                </p>
                <button
                    class="btn mt-8 h-11 bg-primary text-base font-medium text-white hover:bg-primary-focus hover:shadow-lg hover:shadow-primary/50 focus:bg-primary-focus focus:shadow-lg focus:shadow-primary/50 active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:hover:shadow-accent/50 dark:focus:bg-accent-focus dark:focus:shadow-accent/50 dark:active:bg-accent/90"
                >
                    Back To Home
                </button>
            </div>
        </main>
    </div>

    <x-slot:scripts>
        @vite('resources/lineone/js/pages/pages-error-404-3.js')
    </x-slot:scripts>

</x-layouts.base>