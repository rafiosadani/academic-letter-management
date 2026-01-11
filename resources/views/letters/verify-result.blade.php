<x-layouts.guest>
    <div class="min-h-screen flex items-center justify-center bg-slate-100 dark:bg-navy-900 p-4">
        <div class="card max-w-2xl w-full">
            <div class="p-6 sm:p-8">
                @if($valid)
                    {{-- Valid Document --}}
                    <div class="text-center mb-6">
                        <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-success/10">
                            <i class="fa-solid fa-check-circle text-3xl text-success"></i>
                        </div>
                        <h2 class="mt-4 text-2xl font-bold text-slate-800 dark:text-navy-50">
                            Dokumen Valid
                        </h2>
                        <p class="mt-2 text-slate-600 dark:text-navy-200">
                            {{ $message }}
                        </p>
                    </div>

                    {{-- Document Details --}}
                    <div class="space-y-4">
                        <div class="rounded-lg bg-slate-50 dark:bg-navy-600 p-4">
                            <h3 class="font-semibold text-slate-700 dark:text-navy-100 mb-3">
                                Informasi Surat
                            </h3>
                            <dl class="space-y-2 text-sm">
                                <div class="flex">
                                    <dt class="w-32 text-slate-600 dark:text-navy-300">Jenis Surat:</dt>
                                    <dd class="text-slate-800 dark:text-navy-50">{{ $letter->letter_type->label() }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-32 text-slate-600 dark:text-navy-300">Nomor:</dt>
                                    <dd class="text-slate-800 dark:text-navy-50">{{ $letter->letter_number }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-32 text-slate-600 dark:text-navy-300">Mahasiswa:</dt>
                                    <dd class="text-slate-800 dark:text-navy-50">{{ $student->profile->full_name }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-32 text-slate-600 dark:text-navy-300">NIM:</dt>
                                    <dd class="text-slate-800 dark:text-navy-50">{{ $student->profile->student_or_employee_id }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-32 text-slate-600 dark:text-navy-300">Diverifikasi:</dt>
                                    <dd class="text-slate-800 dark:text-navy-50">{{ $verified_at }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="rounded-lg bg-info/10 border border-info/20 p-4">
                            <p class="text-xs text-slate-600 dark:text-navy-200">
                                <i class="fa-solid fa-shield-check text-info mr-2"></i>
                                Dokumen ini sah dan telah diverifikasi oleh sistem. Hash dokumen cocok dengan database.
                            </p>
                        </div>
                    </div>
                @else
                    {{-- Invalid Document --}}
                    <div class="text-center mb-6">
                        <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-error/10">
                            <i class="fa-solid fa-times-circle text-3xl text-error"></i>
                        </div>
                        <h2 class="mt-4 text-2xl font-bold text-slate-800 dark:text-navy-50">
                            Dokumen Tidak Valid
                        </h2>
                        <p class="mt-2 text-slate-600 dark:text-navy-200">
                            {{ $message }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-warning/10 border border-warning/20 p-4">
                        <p class="text-xs text-slate-600 dark:text-navy-200">
                            <i class="fa-solid fa-exclamation-triangle text-warning mr-2"></i>
                            Dokumen ini mungkin palsu atau telah dimodifikasi. Harap hubungi Fakultas Vokasi untuk verifikasi lebih lanjut.
                        </p>
                    </div>
                @endif

                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="btn bg-primary text-white">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>