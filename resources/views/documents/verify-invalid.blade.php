<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Tidak Valid - Verifikasi Gagal</title>
    <link rel="icon" type="image/png" href="{{ setting('favicon') ? Storage::url(setting('favicon')) : asset('assets/images/favicon.png') }}"/>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-50">
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">

        {{-- Error Icon --}}
        <div class="text-center">
            <div class="mx-auto h-24 w-24 bg-error/10 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-exclamation-triangle text-4xl text-error"></i>
            </div>
            <h2 class="mt-6 text-3xl font-bold text-slate-900">
                Dokumen Tidak Valid
            </h2>
            <p class="mt-2 text-sm text-slate-600">
                Verifikasi dokumen gagal
            </p>
        </div>

        {{-- Error Message --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="bg-error/10 border border-error/20 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-times-circle text-error"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-error">
                            {{ $error }}
                        </h3>
                        <div class="mt-2 text-sm text-error/80">
                            <p>Hash dokumen yang Anda cari tidak ditemukan dalam sistem kami.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hash Info --}}
            <div class="mt-4 p-4 bg-slate-50 rounded-lg">
                <p class="text-xs text-slate-500 mb-1">Hash yang dicari:</p>
                <p class="text-sm font-mono text-slate-700 break-all">{{ $hash }}</p>
            </div>

            {{-- Possible Reasons --}}
            <div class="mt-4">
                <p class="text-sm font-semibold text-slate-700 mb-2">Kemungkinan Penyebab:</p>
                <ul class="text-sm text-slate-600 space-y-1 list-disc list-inside">
                    <li>QR code rusak atau tidak terbaca dengan benar</li>
                    <li>Link verifikasi sudah tidak berlaku</li>
                    <li>Dokumen belum dipublikasi</li>
                    <li>Hash telah berubah karena dokumen dimodifikasi</li>
                </ul>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex flex-col space-y-2">
                <button onclick="window.history.back()"
                        class="btn bg-slate-600 text-white hover:bg-slate-700 w-full">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Kembali
                </button>
                <a href="mailto:vokasi@ub.ac.id"
                   class="btn bg-slate-150 text-slate-800 hover:bg-slate-200 w-full text-center">
                    <i class="fa-solid fa-envelope mr-2"></i>
                    Hubungi Admin
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center text-xs text-slate-500">
            <p>Fakultas Vokasi - Universitas Brawijaya</p>
            <p class="mt-1">vokasi@ub.ac.id | +6341 551611</p>
        </div>

    </div>
</div>
</body>
</html>