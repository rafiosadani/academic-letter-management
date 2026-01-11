@php
    $isEdit = isset($letter);
    $formAction = $isEdit ? route('letters.update', $letter) : route('letters.store');
    $formMethod = $isEdit ? 'PUT' : 'POST';
    $pageTitle = $isEdit ? 'Edit Pengajuan Surat' : 'Ajukan Surat Baru';
@endphp

<x-layouts.app :title="$pageTitle">
    <x-ui.breadcrumb
            :title="$pageTitle"
            :items="[
            ['label' => 'Pengajuan Surat', 'url' => route('letters.index')],
            ['label' => $isEdit ? 'Edit' : 'Tambah']
        ]"
    />

    <x-ui.page-header
            :title="$pageTitle . ' - ' . $letterType->label()"
            :description="$letterType->description()"
            :backUrl="$isEdit ? route('letters.show', $letter) : route('letters.create')"
    >
        <x-slot:icon>
            @if($isEdit)
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            @endif
        </x-slot:icon>
    </x-ui.page-header>

    {{-- FORM --}}
    <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-5 grow flex flex-col">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <input type="hidden" name="letter_type" value="{{ $letterType->value }}">

        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6 grow">
            {{-- Main Form --}}
            <div class="col-span-12 space-y-5 sm:hidden">

                {{-- Info Card --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                Panduan
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 space-y-3 text-xs text-slate-600 dark:text-navy-200">
                        <p class="text-xs text-slate-500 dark:text-navy-300 mb-3">
                            {{ $isEdit ? 'Tips mengubah pengajuan surat' : 'Tips mengajukan surat' }}
                        </p>

                        @if(!$isEdit)
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-check text-success mt-0.5"></i>
                                <p>Pastikan semua data yang diisi sudah benar dan lengkap</p>
                            </div>
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-check text-success mt-0.5"></i>
                                <p>Data profil (nama, NIM, prodi) akan otomatis terisi dari sistem</p>
                            </div>
                        @endif

                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                            <p>Isi <strong>Keterangan Tambahan</strong> jika ada informasi penting yang perlu disampaikan</p>
                        </div>

                        @if(count($requiredDocuments) > 0)
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                                <p>Upload dokumen pendukung sesuai format yang diminta</p>
                            </div>
                        @endif

                        @if($letterType === App\Enums\LetterType::SKAK_TUNJANGAN)
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                                <p>Data orang tua akan tersimpan di profil untuk pengajuan selanjutnya</p>
                            </div>
                        @endif

                        @if($isEdit)
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-exclamation-triangle text-error mt-0.5"></i>
                                <p>Perubahan data akan direview ulang oleh pihak terkait</p>
                            </div>
                        @endif
                    </div>

                    @if($isEdit)
                        <div class="p-4 border-t border-slate-200 dark:border-navy-500">
                            <div class="space-y-2 text-xs text-slate-500 dark:text-navy-300">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>Diajukan: {{ $letter->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                @if($letter->updated_at != $letter->created_at)
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-clock"></i>
                                        <span>Update: {{ $letter->updated_at->format('d M Y, H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Status Info (Edit mode only) --}}
                @if($isEdit)
                    <div class="card">
                        <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-{{ $letter->status_badge }}/10 p-1 text-{{ $letter->status_badge }}">
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>
                                <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                    Status Surat
                                </h4>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500 dark:text-navy-300">Status Saat Ini:</span>
                                <div class="badge rounded-full bg-{{ $letter->status_badge }}/10 text-{{ $letter->status_badge }}">
                                    {{ $letter->status_label }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-span-12 lg:col-span-8 space-y-5">

                {{-- Auto-filled Info Banner --}}
                <div class="card">
                    <div class="rounded-lg bg-info/10 border border-info/20 p-4">
                        <div class="flex items-start space-x-3">
                            <i class="fa-solid fa-circle-info text-info text-lg mt-0.5"></i>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-slate-700 dark:text-navy-100">Informasi Otomatis dari Profil</h4>
                                <p class="text-xs text-slate-500 dark:text-navy-300 mt-1">
                                    Data berikut diambil otomatis: <strong>Nama, NIM, Program Studi, Semester, Tahun Akademik</strong>
                                    @if($letterType === App\Enums\LetterType::SKAK || $letterType === App\Enums\LetterType::SKAK_TUNJANGAN)
                                        <strong>, Tempat & Tanggal Lahir</strong>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Data Surat --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Data Surat
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        {{-- Dynamic Form Fields --}}
                        @foreach($formFields as $fieldName => $config)
                            @if($config['type'] === 'student_list')
                                <x-form.student-list-input
                                        :name="$fieldName"
                                        :required="$config['required']"
                                        :value="old($fieldName, $isEdit ? ($letter->data_input[$fieldName] ?? []) : [])"
                                        :studyPrograms="$studyPrograms ?? []"
                                        :helper="$config['helper'] ?? null"
                                        :placeholder="$config['placeholder']"
                                        :min_students="$config['min_students'] ?? 1"
                                        :max_students="$config['max_students'] ?? 50"
                                />
                            @elseif($config['type'] === 'select')
                                <x-form.select
                                        :label="$config['label']"
                                        :name="$fieldName"
                                        :options="$config['options']"
                                        :value="old($fieldName, $isEdit ? ($letter->data_input[$fieldName] ?? '') : ($config['value'] ?? ''))"
                                        :placeholder="$config['placeholder'] ?? '-- Pilih --'"
                                        :required="$config['required']"
                                        :helper="$config['helper'] ?? ''"
                                />
                            @elseif($config['type'] === 'select_or_other')
                                <div x-data="{
                                    selectedValue: '{{ old($fieldName, $isEdit ? ($letter->data_input[$fieldName] ?? '') : ($config['value'] ?? '')) }}',
                                    isOther: {{ (old($fieldName, $isEdit ? ($letter->data_input[$fieldName] ?? '') : ($config['value'] ?? '')) && !in_array(old($fieldName, $isEdit ? ($letter->data_input[$fieldName] ?? '') : ($config['value'] ?? '')), array_keys($config['options']))) ? 'true' : 'false' }}
                                }">
                                    <label class="block">
                                        <span class="text-xs+ font-medium text-slate-600 dark:text-navy-100">
                                            {{ $config['label'] }}
                                            @if($config['required'])
                                                <span class="text-error">*</span>
                                            @endif
                                        </span>
                                        <select
                                                @change="if (selectedValue === 'Lainnya') { isOther = true; selectedValue = ''; } else { isOther = false; }"
                                                x-model="selectedValue"
                                                name="{{ $fieldName }}"
                                                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent"
{{--                                                {{ $config['required'] ? 'required' : '' }}--}}
                                                x-show="!isOther"
                                        >
                                            <option value="">{{ $config['placeholder'] ?? '-- Pilih --' }}</option>
                                            @foreach($config['options'] as $optValue => $optLabel)
                                                <option value="{{ $optValue }}">{{ $optLabel }}</option>
                                            @endforeach
                                        </select>

                                        {{-- Other Input --}}
                                        <div x-show="isOther" x-cloak>
                                            <div class="flex gap-2 mt-1.5">
                                                <input
                                                        type="text"
                                                        name="{{ $fieldName }}"
                                                        x-model="selectedValue"
                                                        class="form-input flex-1 rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                        placeholder="{{ $config['other_placeholder'] ?? 'Masukkan nilai lainnya' }}"
                                                        {{ $config['required'] ? 'required' : '' }}
                                                >
                                                <button
                                                        type="button"
                                                        @click="isOther = false; selectedValue = ''"
                                                        class="btn size-10 rounded-lg p-0 hover:bg-slate-300/20"
                                                        title="Kembali ke pilihan">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
{{--                                            @if(isset($config['other_placeholder']))--}}
{{--                                                <span class="text-tiny text-slate-400 dark:text-navy-300 mt-1 block">--}}
{{--                                                    {{ $config['other_placeholder'] }}--}}
{{--                                                </span>--}}
{{--                                            @endif--}}
                                        </div>

                                        @if(isset($config['helper']))
                                            <span class="text-tiny-plus text-slate-500 dark:text-navy-300 ms-1 mt-1 block">
                                                {{ $config['helper'] }}
                                            </span>
                                        @endif
                                    </label>
                                    @error($fieldName)
                                    <span class="text-tiny-plus text-error mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                            @elseif($config['type'] === 'textarea')
                                <x-form.textarea
                                        :label="$config['label']"
                                        :name="$fieldName"
                                        :value="old($fieldName, $isEdit ? ($letter->data_input[$fieldName] ?? '') : ($config['value'] ?? ''))"
                                        :placeholder="$config['placeholder'] ?? ''"
                                        :rows="$config['rows'] ?? 3"
                                        :required="$config['required']"
                                        :helper="$config['helper'] ?? ''"
                                        :readonly="$config['readonly'] ?? false"
                                />

                            @elseif($config['type'] === 'date')
                                <x-form.input
                                        type="date"
                                        :label="$config['label']"
                                        :name="$fieldName"
                                        :value="old($fieldName, $isEdit ? ($letter->data_input[$fieldName] ?? '') : ($config['value'] ?? ''))"
                                        :required="$config['required']"
                                        :helper="$config['helper'] ?? ''"
                                />

                            @elseif($config['type'] === 'time')
                                <x-form.input
                                        type="time"
                                        :label="$config['label']"
                                        :name="$fieldName"
                                        :value="old($fieldName, $isEdit ? ($letter->data_input[$fieldName] ?? '') : ($config['value'] ?? ''))"
                                        :required="$config['required']"
                                        :helper="$config['helper'] ?? ''"
                                />

                            @else
                                <x-form.input
                                        type="text"
                                        :label="$config['label']"
                                        :name="$fieldName"
                                        :value="old($fieldName, $isEdit ? ($letter->data_input[$fieldName] ?? '') : ($config['value'] ?? ''))"
                                        :placeholder="$config['placeholder'] ?? ''"
                                        :required="$config['required']"
                                        :helper="$config['helper'] ?? ''"
                                        :readonly="$config['readonly'] ?? false"
                                />
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Document Upload (if required) --}}
                @if(count($requiredDocuments) > 0)
                    <div class="card">
                        <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-warning/10 p-1 text-warning">
                                    <i class="fa-solid fa-paperclip"></i>
                                </div>
                                <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                    Dokumen Pendukung
                                </h4>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5 space-y-4">
                            @foreach($requiredDocuments as $docKey => $docConfig)
                                <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">
                                    <div class="mb-3">
                                        <span class="font-medium text-slate-600 dark:text-navy-100">
                                            {{ $docConfig['label'] }}
                                            @if($docConfig['required'] && !$isEdit)
                                                <span class="text-error">*</span>
                                            @endif
                                        </span>
                                        <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                            {{ $docConfig['helper'] }}
                                        </p>
                                    </div>

                                    <div x-data="{ fileName: 'Belum ada file dipilih' }"
                                        class="flex items-stretch min-h-[40px] rounded-lg border border-slate-300 dark:border-navy-450 overflow-hidden hover:border-slate-400 dark:hover:border-navy-400 transition-colors"
                                    >
                                        <label class="flex items-center gap-2 px-4 min-w-[100px] cursor-pointer bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-navy-600 dark:text-navy-100 dark:hover:bg-navy-500 transition-colors">
                                            <i class="fa-solid fa-file-arrow-up text-sm"></i>
                                            <span class="text-xs font-medium">Pilih File</span>

                                            <input
                                                type="file"
                                                name="documents[{{ $docKey }}]"
                                                class="hidden"
                                                accept="{{ collect($docConfig['types'])->map(fn ($t) => '.' . $t)->join(',') }}"
{{--                                                {{ $docConfig['required'] && !$isEdit ? 'required' : '' }}--}}
                                                @change="fileName = $event.target.files[0]?.name ?? 'Belum ada file dipilih'"
                                            >
                                        </label>

                                        {{-- Divider --}}
                                        <div class="w-px bg-slate-300 dark:bg-navy-400"></div>

                                        {{-- File Name Display --}}
                                        <div class="flex flex-1 items-center min-w-0 px-3 py-2">
                                            <span x-text="fileName"
                                                :title="fileName"
                                                class="block w-full truncate text-tiny-plus text-slate-600 dark:text-navy-200 font-mono">
                                            </span>
                                        </div>
                                    </div>

                                    @error('documents.' . $docKey)
                                        <span class="text-tiny-plus text-error mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- Right Column - Info & Actions --}}
            <div class="col-span-12 lg:col-span-4 space-y-5 hidden sm:block">

                {{-- Info Card --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                Panduan
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 space-y-3 text-xs text-slate-600 dark:text-navy-200 text-justify">
                        <p class="text-xs-plus font-medium text-slate-500 dark:text-navy-300 mb-3">
                            {{ $isEdit ? 'Tips mengubah pengajuan surat' : 'Tips mengajukan surat' }}
                        </p>

                        @if(!$isEdit)
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-check text-success mt-0.5"></i>
                                <p>Pastikan semua data yang diisi sudah benar dan lengkap</p>
                            </div>
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-check text-success mt-0.5"></i>
                                <p>Data profil (Nama, NIM, Program Studi) akan otomatis terisi dari sistem</p>
                            </div>
                        @endif

                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                            <p>Isi <strong>Keterangan Tambahan</strong> jika ada informasi penting yang perlu disampaikan</p>
                        </div>

                        @if(count($requiredDocuments) > 0)
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                                <p>Upload dokumen pendukung sesuai format yang diminta</p>
                            </div>
                        @endif

                        @if($letterType === App\Enums\LetterType::SKAK_TUNJANGAN)
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                                <p>Data orang tua akan tersimpan di profil untuk pengajuan selanjutnya</p>
                            </div>
                        @endif

                        @if($isEdit)
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-exclamation-triangle text-error mt-0.5"></i>
                                <p>Perubahan data akan direview ulang oleh pihak terkait</p>
                            </div>
                        @endif
                    </div>

                    @if($isEdit)
                        <div class="p-4 border-t border-slate-200 dark:border-navy-500">
                            <div class="space-y-2 text-xs text-slate-500 dark:text-navy-300">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>Diajukan: {{ $letter->created_at->format('d F Y, H:i') }}</span>
                                </div>
                                @if($letter->updated_at != $letter->created_at)
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-clock"></i>
                                        <span>Update: {{ $letter->updated_at->format('d F Y, H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Status Info (Edit mode only) --}}
                @if($isEdit)
                    <div class="card">
                        <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-{{ $letter->status_badge }}/10 p-1 text-{{ $letter->status_badge }}">
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>
                                <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                    Status Surat
                                </h4>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500 dark:text-navy-300">Status Saat Ini:</span>
                                <span class="badge bg-{{ $letter->status_badge }}/10 text-{{ $letter->status_badge }} text-tiny border border-{{ $letter->status_badge }} inline-flex items-center space-x-1.5 dark:bg-{{ $letter->status_badge }}/15">
                                    <i class="{{ $letter->status_icon }} {{ in_array($letter->status, ['in_progress','external_processing']) ? 'animate-spin' : '' }}"></i>
                                    <span>{{ $letter->status_label }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sticky Action Buttons --}}
        <x-form.sticky-form-actions
                :cancelUrl="$isEdit ? route('letters.show', $letter) : route('letters.create')"
                :submitText="$isEdit ? 'Update Pengajuan' : 'Ajukan Surat'"
                :submitType="$isEdit ? 'warning' : 'primary'"
        />
    </form>
</x-layouts.app>