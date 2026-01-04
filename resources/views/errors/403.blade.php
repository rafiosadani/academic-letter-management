<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-navy-900">
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-lg w-full">
        <div class="text-center">
            {{-- Icon --}}
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <div class="flex size-32 items-center justify-center rounded-full bg-error/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-20 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Title --}}
            <h1 class="text-4xl font-bold text-slate-800 dark:text-navy-50 mb-2">
                403
            </h1>
            <h2 class="text-xl font-semibold text-slate-700 dark:text-navy-100 mb-4">
                Akses Ditolak
            </h2>

            {{-- Message --}}
            <div class="bg-white dark:bg-navy-700 rounded-lg shadow-soft p-6 mb-6">
                <p class="text-slate-600 dark:text-navy-200 mb-4">
                    Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
                </p>

                @if($exception->getMessage())
                    <div class="bg-error/10 border border-error/20 rounded-lg p-3 mb-4">
                        <p class="text-sm text-error font-medium">
                            {{ $exception->getMessage() }}
                        </p>
                    </div>
                @endif

                <div class="text-sm text-slate-500 dark:text-navy-300 space-y-2">
                    <p class="flex items-center justify-center">
                        <i class="fa-solid fa-circle-info mr-2 text-info"></i>
                        Kemungkinan penyebab:
                    </p>
                    <ul class="text-center list-disc list-inside space-y-1 max-w-md mx-auto">
                        <li>Role Anda tidak memiliki permission untuk halaman ini</li>
                        <li>Anda mencoba mengakses resource yang tidak diizinkan</li>
                        <li>Permission Anda telah diubah oleh administrator</li>
                    </ul>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button onclick="window.history.back()"
                        class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Kembali
                </button>

                <a href="{{ route('dashboard') }}"
                   class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                    <i class="fa-solid fa-home mr-2"></i>
                    Ke Dashboard
                </a>
            </div>

            {{-- Help Text --}}
            <div class="mt-6 text-sm text-slate-500 dark:text-navy-300">
                <p>
                    Jika Anda yakin ini adalah kesalahan, silakan hubungi administrator sistem.
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>