<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen</title>

    {{-- Tailwind CSS CDN for standalone page --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

<div class="max-w-2xl w-full">

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-8 {{ $valid ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-red-500 to-red-600' }} text-white">
            <div class="flex items-center justify-center space-x-3">
                <i class="fa-solid {{ $valid ? 'fa-check-circle' : 'fa-times-circle' }} text-5xl"></i>
                <div>
                    <h1 class="text-3xl font-bold">
                        {{ $valid ? 'DOKUMEN VALID' : 'DOKUMEN TIDAK VALID' }}
                    </h1>
                    <p class="text-green-100 mt-1">
                        {{ $valid ? 'Dokumen terverifikasi di sistem' : 'Dokumen tidak ditemukan' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="px-6 py-8">

            @if($valid)
                {{-- Valid Document Info --}}
                <div class="space-y-6">

                    {{-- Document Info --}}
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fa-solid fa-file-pdf text-red-500 mr-2"></i>
                            Informasi Dokumen
                        </h2>

                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama File:</span>
                                <span class="font-medium text-gray-900">{{ $document->file_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kategori:</span>
                                <span class="font-medium text-gray-900">{{ $document->category_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ukuran File:</span>
                                <span class="font-medium text-gray-900">{{ $document->file_size_formatted }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Upload:</span>
                                <span class="font-medium text-gray-900">{{ $document->created_at->format('d F Y, H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>

                    {{-- Letter Info --}}
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fa-solid fa-envelope text-blue-500 mr-2"></i>
                            Informasi Surat
                        </h2>

                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            @if($letter->number)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nomor Surat:</span>
                                    <span class="font-medium text-gray-900">{{ $letter->number }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between">
                                <span class="text-gray-600">Jenis Surat:</span>
                                <span class="font-medium text-gray-900">{{ $letter->letter_type->label() ?? 'N/A' }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Pemohon:</span>
                                <span class="font-medium text-gray-900">{{ $letter->student->profile->full_name ?? 'N/A' }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($letter->status ?? 'N/A') }}
                                    </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal Pengajuan:</span>
                                <span class="font-medium text-gray-900">{{ $letter->created_at->format('d F Y') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Security Notice --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fa-solid fa-shield-halved text-blue-500 text-xl mr-3 mt-0.5"></i>
                            <div>
                                <h3 class="font-semibold text-blue-900 mb-1">Dokumen Terverifikasi</h3>
                                <p class="text-sm text-blue-700">
                                    Dokumen ini telah diverifikasi dan terdaftar di sistem
                                    Fakultas Vokasi Universitas Brawijaya. Hash verifikasi:
                                </p>
                                <code class="block mt-2 text-xs bg-blue-100 px-2 py-1 rounded text-blue-800 break-all">
                                    {{ $document->hash }}
                                </code>
                            </div>
                        </div>
                    </div>

                </div>

            @else
                {{-- Invalid Document --}}
                <div class="text-center py-8">
                    <i class="fa-solid fa-exclamation-triangle text-6xl text-red-400 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Dokumen Tidak Ditemukan</h3>
                    <p class="text-gray-600 mb-6">
                        {{ $message ?? 'Hash verifikasi tidak valid atau dokumen telah dihapus dari sistem.' }}
                    </p>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-left">
                        <div class="flex items-start">
                            <i class="fa-solid fa-lightbulb text-yellow-500 text-xl mr-3 mt-0.5"></i>
                            <div>
                                <h4 class="font-semibold text-yellow-900 mb-1">Kemungkinan Penyebab:</h4>
                                <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                                    <li>QR Code rusak atau tidak terbaca dengan baik</li>
                                    <li>Dokumen adalah palsu atau tidak resmi</li>
                                    <li>Dokumen telah dihapus dari sistem</li>
                                    <li>Link verifikasi salah atau expired</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>
                        <i class="fa-solid fa-graduation-cap mr-2"></i>
                        Fakultas Vokasi - Universitas Brawijaya
                    </span>
                <span>
                        {{ now()->format('Y') }}
                    </span>
            </div>
        </div>

    </div>

    {{-- Back Button --}}
    <div class="text-center mt-6">
        <a href="/" class="text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Kembali ke Beranda
        </a>
    </div>

</div>

</body>
</html>