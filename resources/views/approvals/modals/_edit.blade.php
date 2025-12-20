<div
    class="modal fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
    id="edit-content-modal-{{ $approval->id }}"
    data-open-on-error="{{ session('open_edit_modal_id') == $approval->id ? 'true' : 'false' }}"
    role="dialog"
>
    <div class="modal-overlay absolute inset-0 bg-slate-900/60 transition-opacity duration-300"></div>

    <div class="modal-content relative flex w-full max-w-3xl origin-top flex-col overflow-hidden rounded-lg bg-white dark:bg-navy-700">
        <div class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
            <div class="flex items-center space-x-2">
                <div class="flex size-7 items-center justify-center rounded-lg bg-warning/10 text-warning dark:bg-warning/15">
                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                </div>
                <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                    Edit Konten Surat
                </h4>
            </div>
            <button
                data-close-modal
                class="btn size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25"
            >
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
        <div class="scrollbar-sm overflow-y-auto px-4 py-4 sm:px-5">

            {{-- Info --}}
            <div class="rounded-lg bg-info/10 border border-info/20 p-3 mb-4 text-left">
                <p class="text-xs text-slate-600 dark:text-navy-200">
                    <i class="fa-solid fa-info-circle text-info mr-1"></i>
                    Anda dapat mengedit konten surat sebelum menyetujui. Perubahan akan tersimpan dan mahasiswa akan melihat data yang telah diedit.
                </p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('approvals.edit-content', $approval) }}">
                @csrf

                <div class="space-y-4 text-left">
                    @php
                        $letterType = $letter->letter_type;
                        $formFields = $letterType->formFields();
                        $currentData = $letter->data_input;
                    @endphp

                    @foreach($formFields as $fieldName => $config)
                        <div>
                            <label class="block">
                                <span class="text-xs font-medium text-slate-600 dark:text-navy-100">
                                    {{ $config['label'] }}
                                    @if($config['required'])
                                        <span class="text-error">*</span>
                                    @endif
                                </span>

                                @if($config['type'] === 'select')
                                    <select
                                            name="{{ $fieldName }}"
                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent"
                                            {{ $config['required'] ? 'required' : '' }}
                                    >
                                        <option value="">Pilih {{ $config['label'] }}</option>
                                        @foreach($config['options'] as $optValue => $optLabel)
                                            <option value="{{ $optValue }}" {{ old($fieldName, $currentData[$fieldName] ?? '') == $optValue ? 'selected' : '' }}>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif($config['type'] === 'select_or_other')
                                    <div x-data="{
                                        selectedValue: '{{ old($fieldName, $currentData[$fieldName] ?? '') }}',
                                        isOther: {{ (old($fieldName, $currentData[$fieldName] ?? '') && !in_array(old($fieldName, $currentData[$fieldName] ?? ''), array_keys($config['options']))) ? 'true' : 'false' }}
                                    }">
                                        <label class="block">
                                            <span class="text-xs font-medium text-slate-600 dark:text-navy-100">
                                                {{ $config['label'] }}
                                                @if($config['required'])
                                                    <span class="text-error">*</span>
                                                @endif
                                            </span>

                                            {{-- SELECT --}}
                                            <select
                                                x-show="!isOther"
                                                x-model="selectedValue"
                                                name="{{ $fieldName }}"
                                                class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent"
                                                {{ $config['required'] ? 'required' : '' }}
                                                @change="if (selectedValue === 'Lainnya') { isOther = true; selectedValue = ''; }"
                                            >
                                                <option value="">{{ $config['placeholder'] ?? '-- Pilih --' }}</option>
                                                @foreach($config['options'] as $optValue => $optLabel)
                                                    <option value="{{ $optValue }}">{{ $optLabel }}</option>
                                                @endforeach
                                                <option value="Lainnya">Lainnya</option>
                                            </select>

                                            {{-- OTHER INPUT --}}
                                            <div x-show="isOther" x-cloak class="mt-1.5">
                                                <div class="flex gap-2">
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
                                                    >
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            @error($fieldName)
                                                <span class="text-tiny text-error ms-1 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </label>
                                    </div>

                                @elseif($config['type'] === 'textarea')
                                        <textarea
                                                name="{{ $fieldName }}"
                                                rows="3"
                                                class="form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                placeholder="{{ $config['placeholder'] ?? '' }}"
                                                {{--{{ $config['required'] ? 'required' : '' }}--}}
                                        >{{ old($fieldName, $currentData[$fieldName] ?? '') }}</textarea>

                                @elseif($config['type'] === 'date')
                                    <input
                                            type="date"
                                            name="{{ $fieldName }}"
                                            value="{{ old($fieldName, $currentData[$fieldName] ?? '') }}"
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            {{--{{ $config['required'] ? 'required' : '' }}--}}
                                    >

                                @elseif($config['type'] === 'time')
                                    <input
                                            type="time"
                                            name="{{ $fieldName }}"
                                            value="{{ old($fieldName, $currentData[$fieldName] ?? '') }}"
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            {{--{{ $config['required'] ? 'required' : '' }}--}}
                                    >

                                @else
                                    <input
                                            type="text"
                                            name="{{ $fieldName }}"
                                            value="{{ old($fieldName, $currentData[$fieldName] ?? '') }}"
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="{{ $config['placeholder'] ?? '' }}"
                                            {{--{{ $config['required'] ? 'required' : '' }}--}}
                                    >
                                @endif

                                @if(isset($config['helper']))
                                    <span class="text-tiny text-slate-400 dark:text-navy-300 mt-1 block">
                                        {{ $config['helper'] }}
                                    </span>
                                @endif

                                @error($fieldName)
                                <span class="text-tiny text-error ms-1 mt-1 block">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    @endforeach
                </div>

                {{-- Actions --}}
                <div class="mt-6 flex space-x-2">
                    <button
                            type="button"
                            data-close-modal
                            class="btn flex-1 border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500"
                    >
                        Batal
                    </button>
                    <button
                            type="submit"
                            class="btn flex-1 bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90"
                    >
                        <i class="fa-solid fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>