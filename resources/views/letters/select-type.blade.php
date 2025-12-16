<x-layouts.app title="Pilih Jenis Surat">
    <x-ui.breadcrumb
            title="Ajukan Surat Baru"
            :items="[
            ['label' => 'Pengajuan Surat', 'url' => route('letters.index')],
            ['label' => 'Pilih Jenis Surat']
        ]"
    />

    <x-ui.page-header
            title="Ajukan Surat Baru"
            description="Pilih jenis surat yang ingin Anda ajukan. Pastikan profil Anda sudah lengkap sebelum mengajukan surat."
            :backUrl="route('letters.index')"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </x-slot:icon>
    </x-ui.page-header>

    {{-- Info Banner --}}
    <div class="card mb-5 p-4 bg-info/5 border border-info/20">
        <div class="flex items-start space-x-3">
            <i class="fa-solid fa-circle-info text-info text-lg mt-0.5"></i>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">
                    Informasi Penting
                </h3>
                <ul class="space-y-1 text-xs text-slate-600 dark:text-navy-200">
                    <li class="flex items-center space-x-2">
                        <i class="fa-solid fa-check text-success text-tiny"></i>
                        <span>Pastikan <strong>profil Anda sudah lengkap</strong> (tempat & tanggal lahir)</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fa-solid fa-check text-success text-tiny"></i>
                        <span>Siapkan <strong>dokumen pendukung</strong> jika diperlukan</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fa-solid fa-check text-success text-tiny"></i>
                        <span>Isi form dengan <strong>data yang benar dan lengkap</strong></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Letter Types Grid --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6">
        @foreach($letterTypes as $type)
            <div class="card group hover:shadow-xl transition-all duration-200 hover:scale-[1.02]">
                <div class="flex flex-col h-full p-5">

                    {{-- Icon & Badge --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex size-14 items-center justify-center rounded-xl bg-primary/10 dark:bg-accent-light/15 group-hover:bg-primary/20 transition-colors">
                            <i class="fa-solid fa-file-lines text-3xl text-primary dark:text-accent-light"></i>
                        </div>
                        <span class="badge rounded-full bg-info/10 text-info dark:bg-info/15 text-tiny px-2.5 py-1">
                            {{ $type->shortLabel() }}
                        </span>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-2">
                        {{ $type->label() }}
                    </h3>

                    {{-- Description --}}
                    <p class="text-xs text-justify text-slate-400 dark:text-navy-300 line-clamp-3 mb-4">
                        {{ $type->description() }}
                    </p>

                    {{-- Requirements Info --}}
                    <div class="mb-4 space-y-1">
                        @if($type === App\Enums\LetterType::SKAK || $type === App\Enums\LetterType::SKAK_TUNJANGAN)
                            <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-navy-300">
                                <i class="fa-solid fa-check-circle text-success text-tiny"></i>
                                <span>Tempat & tanggal lahir diperlukan</span>
                            </div>
                        @endif
                        @if($type === App\Enums\LetterType::SKAK_TUNJANGAN)
                            <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-navy-300">
                                <i class="fa-solid fa-check-circle text-success text-tiny"></i>
                                <span>Data orang tua diperlukan</span>
                            </div>
                        @endif
                        @if(count($type->requiredDocuments()) > 0)
                            <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-navy-300">
                                <i class="fa-solid fa-paperclip text-warning text-tiny"></i>
                                <span>Dokumen pendukung diperlukan</span>
                            </div>
                        @endif
                    </div>

{{--                    <div class="flex-grow"></div>--}}

                    {{-- Action Button --}}
                    <a href="{{ route('letters.create', ['type' => $type->value]) }}"
                       class="btn mt-auto w-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                        <i class="fa-solid fa-arrow-right mr-2"></i>
                        Ajukan Surat
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    @if(session()->has('alert_data'))
        @php
            $alertData = session('alert_data');
        @endphp

        <x-modal.alert
                id="alert-profile-incomplete"
                :type="$alertData['type'] ?? 'info'"
                :title="$alertData['title'] ?? 'Pemberitahuan'"
                :message="$alertData['message'] ?? ''"
        >
            @if(!empty($alertData['missing_fields']))
                <div class="mt-3 rounded-lg bg-error/10 dark:bg-error/15 p-3 text-left">
                    <p class="text-xs font-medium text-error mb-1">
                        Data yang wajib dilengkapi:
                    </p>
                    <div class="mt-3 space-y-2">
                        @foreach($alertData['missing_fields'] as $field)
                            <div class="flex items-start space-x-2 text-xs text-slate-600 dark:text-navy-200">
                                <i class="fa-solid fa-circle-exclamation text-error mt-0.5"></i>
                                <p>{{ $field }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <a href="#"
                   class="btn mt-3 inline-flex items-center space-x-2 text-xs-plus text-white bg-primary hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                    <i class="fa-solid fa-user-pen"></i>
                    <span>Update Profile</span>
                </a>
            @endif
        </x-modal.alert>
    @endif
</x-layouts.app>