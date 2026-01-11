<x-layouts.app :title="isset($approvalFlow) ? 'Edit Alur Persetujuan' : 'Tambah Alur Persetujuan'">
    <x-ui.breadcrumb
            :title="isset($approvalFlow) ? 'Edit Alur' : 'Tambah Alur'"
            :items="[
            ['label' => 'Pengaturan'],
            ['label' => 'Alur Persetujuan', 'url' => route('settings.approval-flows.index')],
            ['label' => isset($approvalFlow) ? 'Edit' : 'Tambah']
        ]"
    />

    <x-ui.page-header
        title="{{ isset($approvalFlow) ? 'Edit Alur Persetujuan - ' . $approvalFlow->letter_type->label() : 'Tambah Alur Persetujuan - ' . $letterTypeLabel }}"
        :description="isset($approvalFlow) ? 'Perbarui konfigurasi alur persetujuan' : 'Buat step persetujuan baru untuk jenis surat'"
        :backUrl="route('settings.approval-flows.index')"
    >
        <x-slot:icon>
            @if(isset($approvalFlow))
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
    <form
        method="POST"
        action="{{ isset($approvalFlow) ? route('settings.approval-flows.update', $approvalFlow) : route('settings.approval-flows.store') }}"
        class="space-y-5 grow flex flex-col"
    >
        @csrf
        @if(isset($approvalFlow))
            @method('PUT')
        @endif

        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6 grow">
            {{-- Main Form --}}
            <div class="col-span-12 lg:col-span-8 space-y-5">
                {{-- Step Info --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-list-ol"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Informasi Step
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        {{-- Info Banner when pre-filled --}}
                        @if(isset($letterTypeReadonly) && $letterTypeReadonly && $selectedLetterType)
                            <div class="rounded-lg bg-primary/10 border border-primary/20 p-3">
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-info-circle text-primary mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-primary">
                                            Menambahkan Step Baru
                                        </p>
                                        <p class="text-xs text-primary/80 mt-1">
                                            Anda sedang menambahkan step untuk
                                            <strong>{{ App\Enums\LetterType::from($selectedLetterType)->label() }}</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Letter Type --}}
                        <x-form.select
                                label="Jenis Surat"
                                name="letter_type"
                                id="letter_type"
                                :options="collect($letterTypes)->mapWithKeys(fn($type) => [$type->value => $type->label()])->toArray()"
                                :value="old('letter_type', $approvalFlow->letter_type->value ?? $selectedLetterType ?? '')"
                                placeholder="Pilih jenis surat"
                                required
                                helper="Pilih jenis surat yang akan diatur alur persetujuannya"
                                :disabled="isset($letterTypeReadonly) && $letterTypeReadonly"
                        />

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            {{-- Step Number --}}
                            <x-form.input
                                    label="Nomor Step"
                                    name="step"
                                    id="step"
                                    type="number"
                                    min="1"
                                    :value="old('step', $approvalFlow->step ?? $nextStep ?? 1)"
                                    required
                                    disabled
                                    :helper="isset($approvalFlow)
                                    ? 'Urutan step dalam alur persetujuan'
                                    : 'Auto-suggest: Step ' . ($nextStep ?? 1)"
                            />

                            {{-- Step Label --}}
                            <x-form.input
                                    label="Label Step"
                                    name="step_label"
                                    type="text"
                                    :value="old('step_label', $approvalFlow->step_label ?? '')"
                                    placeholder="Contoh: Verifikasi Awal"
                                    required
                                    helper="Nama step yang ditampilkan ke user"
                            />
                        </div>

                        {{-- Required Positions (Checkboxes) --}}
                        <div>
                            <label class="block">
                                <span class="font-medium text-slate-600 dark:text-navy-100">
                                    Jabatan yang Diperlukan
                                    <span class="text-error">*</span>
                                </span>
                                <span class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1 block mb-3">
                                    Pilih satu atau lebih jabatan yang bisa menangani step ini
                                </span>

                                @error('required_positions')
                                <div class="alert flex space-x-2 items-center rounded-lg border border-error p-2 mb-3 text-error text-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs">{{ $message }}</p>
                                </div>
                                @enderror

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    @foreach($positions as $position)
                                        <label class="inline-flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox"
                                                   name="required_positions[]"
                                                   value="{{ $position->value }}"
                                                   {{ in_array($position->value, old('required_positions', isset($approvalFlow) ? $approvalFlow->required_positions : [])) ? 'checked' : '' }}
                                                   class="form-checkbox is-basic size-4 rounded border-slate-400/70 checked:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-1">
                                                    <div class="flex items-center justify-center size-5">
                                                        <i class="fa-solid {{ $position->icon() }} text-{{ $position->color() }}"></i>
                                                    </div>
                                                    <span class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                                        {{ $position->label() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Permissions & Actions --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Permission & Aksi
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        {{-- Note for Word Format --}}
                        @if($letterTypeIsExternal)
                            <div class="rounded-lg bg-info/10 border border-info/20 p-3">
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-info-circle text-info mt-0.5"></i>
                                    <p class="text-xs text-info">
                                        <strong>Format Word (External System):</strong>
                                        Permission edit tidak diperlukan karena dokumen diproses di sistem eksternal.
                                    </p>
                                </div>
                            </div>
                        @endif

                        {{-- Checkboxes --}}
                        @if(!$letterTypeIsExternal)
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                {{-- Can Edit Content --}}
                                <label class="inline-flex items-center space-x-2">
                                    <input type="checkbox"
                                           name="can_edit_content"
                                           value="1"
                                           {{ old('can_edit_content', $approvalFlow->can_edit_content ?? false) ? 'checked' : '' }}
                                           class="form-checkbox is-basic size-4 rounded border-slate-400/70 checked:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent">
                                    <span class="text-slate-600 dark:text-navy-100">
                                    Approver boleh edit konten surat (minor corrections)
                                </span>
                                </label>

                                {{-- Is Editable --}}
                                <label class="inline-flex items-center space-x-2">
                                    <input type="checkbox"
                                           name="is_editable"
                                           value="1"
                                           {{ old('is_editable', $approvalFlow->is_editable ?? false) ? 'checked' : '' }}
                                           class="form-checkbox is-basic size-4 rounded border-slate-400/70 checked:bg-warning checked:border-warning hover:border-warning focus:border-warning dark:border-navy-400">
                                    <span class="text-slate-600 dark:text-navy-100">
                                    Mahasiswa boleh edit surat jika ditolak di step ini
                                </span>
                                </label>
                            </div>
                        @endif

                        {{-- Is Final --}}
                        <div class="flex flex-col">
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox"
                                       name="is_final"
                                       value="1"
                                       {{ old('is_final', $approvalFlow->is_final ?? false) ? 'checked' : '' }}
                                       class="form-checkbox is-basic size-4 rounded border-slate-400/70 checked:bg-success checked:border-success hover:border-success focus:border-success dark:border-navy-400"
                                >
                                <span class="text-slate-600 dark:text-navy-100">
                                    <strong>Step Final</strong> {{ !$letterTypeIsExternal ? 'Generate nomor surat & TTE setelah step ini)' : '(Surat Final - Publish)' }}
                                </span>
                            </label>
                            @error('is_final')
                                <span class="text-tiny-plus text-error mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- On Reject Action --}}
                        @if(!$letterTypeIsExternal)
                            <div>
                                <label class="block">
                                <span class="font-medium text-slate-600 dark:text-navy-100">
                                    Aksi Saat Surat Ditolak
                                    <span class="text-error">*</span>
                                </span>
                                    <select name="on_reject"
                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent @error('on_reject') border-error @enderror">
                                        <option value="">Pilih aksi</option>
                                        @foreach($rejectActions as $action)
                                            <option value="{{ $action->value }}"
                                                    {{ old('on_reject', $approvalFlow->on_reject->value ?? '') == $action->value ? 'selected' : '' }}>
                                                {{ $action->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('on_reject')
                                        <span class="text-tiny-plus text-error mt-1 block">{{ $message }}</span>
                                    @enderror
                                    <span class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1 block">
                                    Apa yang terjadi jika surat ditolak di step ini?
                                </span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column - Info --}}
            <div class="col-span-12 lg:col-span-4 space-y-5">
                {{-- Info Card --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                                <i class="fa-solid fa-circle-info text-info"></i>
                            </div>
                            <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                Panduan
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 space-y-3 text-xs text-slate-600 dark:text-navy-200">
                        <p class="text-xs text-slate-500 dark:text-navy-300 mb-3">
                            {{ isset($approvalFlow) ? 'Tips mengubah konfigurasi' : 'Tips konfigurasi alur persetujuan' }}
                        </p>
                        @if(!isset($approvalFlow))
                            @if(isset($letterTypeReadonly) && $letterTypeReadonly)
                                <div class="flex items-start space-x-2">
                                    <i class="fa-solid fa-check text-success mt-0.5"></i>
                                    <p>Nomor step sudah disesuaikan otomatis untuk jenis surat ini</p>
                                </div>
                            @endif
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                                <p>Step diurutkan berdasarkan nomor urut (1, 2, 3, ...)</p>
                            </div>
                        @endif
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                            <p>Hanya boleh ada <strong>1 step final</strong> per jenis surat</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                            <p>Setelah step final, nomor surat akan di-generate otomatis</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                            <p>Edit konten: Approver bisa perbaiki typo tanpa reject ke mahasiswa</p>
                        </div>
                        @if(isset($approvalFlow))
                            <div class="flex items-start space-x-2">
                                <i class="fa-solid fa-exclamation-triangle text-error mt-0.5"></i>
                                <p>Mengubah nomor step dapat mempengaruhi urutan alur persetujuan</p>
                            </div>
                        @endif
                    </div>

                    @if(isset($approvalFlow))
                        <div class="p-4 border-t border-slate-200 dark:border-navy-500">
                            <div class="space-y-2 text-xs text-slate-500 dark:text-navy-300">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>Dibuat: {{ $approvalFlow->created_at_formatted }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Update: {{ $approvalFlow->updated_at_formatted }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Buttons (Sticky Bottom) --}}
        <x-form.sticky-form-actions
                :cancelUrl="route('settings.approval-flows.index')"
                :submitText="isset($approvalFlow) ? 'Update Alur' : 'Simpan Alur'"
                :submitType="isset($approvalFlow) ? 'warning' : 'primary'"
        />
    </form>
</x-layouts.app>