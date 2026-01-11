<x-layouts.app :title="isset($facultyOfficial) ? 'Edit Penugasan Jabatan' : 'Tambah Penugasan Jabatan'">
    <x-ui.breadcrumb
            :title="isset($facultyOfficial) ? 'Edit Penugasan' : 'Tambah Penugasan'"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Penugasan Jabatan', 'url' => route('master.faculty-officials.index')],
            ['label' => isset($facultyOfficial) ? 'Edit' : 'Tambah']
        ]"
    />

    <x-ui.page-header
            :title="isset($facultyOfficial) ? 'Edit Penugasan Jabatan' : 'Tambah Penugasan Jabatan'"
            :description="isset($facultyOfficial) ? 'Perbarui data penugasan jabatan pejabat fakultas' : 'Buat penugasan jabatan baru untuk pejabat fakultas'"
            :backUrl="route('master.faculty-officials.index')"
    >
        <x-slot:icon>
            @if(isset($facultyOfficial))
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
        action="{{ isset($facultyOfficial) ? route('master.faculty-officials.update', $facultyOfficial) : route('master.faculty-officials.store') }}"
        class="space-y-5 grow flex flex-col"
    >
        @csrf
        @if(isset($facultyOfficial))
            @method('PUT')
        @endif

        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6 grow">
            {{-- Main Form --}}
            <div class="col-span-12 lg:col-span-8 space-y-5">
                {{-- Informasi Pejabat --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Informasi Pejabat
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        {{-- User --}}
                        <x-form.select
                                label="Pejabat"
                                name="user_id"
                                :options="$users"
                                :value="$facultyOfficial->user_id ?? ''"
                                placeholder="Pilih pejabat"
                                required
                                helper="Pilih user yang akan menjabat"
                        />

                        {{-- Position --}}
                        <label class="block">
                            <span class="font-medium text-slate-600 dark:text-navy-100">
                                Jabatan
                                <span class="text-error">*</span>
                            </span>
                            <select name="position"
                                    id="position"
                                    required
                                    onchange="toggleStudyProgramField()"
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent @error('position') border-error @enderror">
                                <option value="">Pilih Jabatan</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->value }}"
                                            data-requires-program="{{ $pos->requiresStudyProgram() ? '1' : '0' }}"
                                            {{ old('position', $facultyOfficial->position->value ?? '') == $pos->value ? 'selected' : '' }}>
                                        {{ $pos->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('position')
                            <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                            @enderror
                        </label>

                        {{-- Rank/Golongan (NEW!) --}}
                        <div x-data="{
                            selectedValue: '{{ old('rank', $facultyOfficial->rank ?? '') }}',
                            predefinedRanks: {
                                'Juru Muda / I a': 'Juru Muda / I a',
                                'Juru Muda Tingkat I / I b': 'Juru Muda Tingkat I / I b',
                                'Juru / I c': 'Juru / I c',
                                'Juru Tingkat I / I d': 'Juru Tingkat I / I d',
                                'Pengatur Muda / II a': 'Pengatur Muda / II a',
                                'Pengatur Muda Tingkat I / II b': 'Pengatur Muda Tingkat I / II b',
                                'Pengatur / II c': 'Pengatur / II c',
                                'Pengatur Tingkat I / II d': 'Pengatur Tingkat I / II d',
                                'Penata Muda / III a': 'Penata Muda / III a',
                                'Penata Muda Tingkat I / III b': 'Penata Muda Tingkat I / III b',
                                'Penata / III c': 'Penata / III c',
                                'Penata Tingkat I / III d': 'Penata Tingkat I / III d',
                                'Pembina / IV a': 'Pembina / IV a',
                                'Pembina Tingkat I / IV b': 'Pembina Tingkat I / IV b',
                                'Pembina Utama Muda / IV c': 'Pembina Utama Muda / IV c',
                                'Pembina Utama Madya / IV d': 'Pembina Utama Madya / IV d',
                                'Pembina Utama / IV e': 'Pembina Utama / IV e',
                            },
                            isOther: {{ (old('rank', $facultyOfficial->rank ?? '') && !in_array(old('rank', $facultyOfficial->rank ?? ''), [
                                'Juru Muda / I a',
                                'Juru Muda Tingkat I / I b',
                                'Juru / I c',
                                'Juru Tingkat I / I d',
                                'Pengatur Muda / II a',
                                'Pengatur Muda Tingkat I / II b',
                                'Pengatur / II c',
                                'Pengatur Tingkat I / II d',
                                'Penata Muda / III a',
                                'Penata Muda Tingkat I / III b',
                                'Penata / III c',
                                'Penata Tingkat I / III d',
                                'Pembina / IV a',
                                'Pembina Tingkat I / IV b',
                                'Pembina Utama Muda / IV c',
                                'Pembina Utama Madya / IV d',
                                'Pembina Utama / IV e',
                            ])) ? 'true' : 'false' }},
                            isPredefined() {
                                return this.predefinedRanks.hasOwnProperty(this.selectedValue);
                            }
                        }">
                            <label class="block">
                                <span class="font-medium text-slate-600 dark:text-navy-100">
                                    Pangkat / Golongan
                                </span>

                                {{-- Dropdown Select --}}
                                <select
                                        @change="if (selectedValue === 'Lainnya') { isOther = true; selectedValue = ''; } else { isOther = false; }"
                                        x-model="selectedValue"
                                        name="rank"
                                        class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent @error('rank') border-error @enderror"
                                        x-show="!isOther"
                                >
                                    <option value="">-- Pilih Pangkat / Golongan --</option>

                                    {{-- Golongan I --}}
                                    <optgroup label="Golongan I">
                                        <option value="Juru Muda / I a">Juru Muda / I a</option>
                                        <option value="Juru Muda Tingkat I / I b">Juru Muda Tingkat I / I b</option>
                                        <option value="Juru / I c">Juru / I c</option>
                                        <option value="Juru Tingkat I / I d">Juru Tingkat I / I d</option>
                                    </optgroup>

                                    {{-- Golongan II --}}
                                    <optgroup label="Golongan II">
                                        <option value="Pengatur Muda / II a">Pengatur Muda / II a</option>
                                        <option value="Pengatur Muda Tingkat I / II b">Pengatur Muda Tingkat I / II b</option>
                                        <option value="Pengatur / II c">Pengatur / II c</option>
                                        <option value="Pengatur Tingkat I / II d">Pengatur Tingkat I / II d</option>
                                    </optgroup>

                                    {{-- Golongan III --}}
                                    <optgroup label="Golongan III">
                                        <option value="Penata Muda / III a">Penata Muda / III a</option>
                                        <option value="Penata Muda Tingkat I / III b">Penata Muda Tingkat I / III b</option>
                                        <option value="Penata / III c">Penata / III c</option>
                                        <option value="Penata Tingkat I / III d">Penata Tingkat I / III d</option>
                                    </optgroup>

                                    {{-- Golongan IV --}}
                                    <optgroup label="Golongan IV">
                                        <option value="Pembina / IV a">Pembina / IV a</option>
                                        <option value="Pembina Tingkat I / IV b">Pembina Tingkat I / IV b</option>
                                        <option value="Pembina Utama Muda / IV c">Pembina Utama Muda / IV c</option>
                                        <option value="Pembina Utama Madya / IV d">Pembina Utama Madya / IV d</option>
                                        <option value="Pembina Utama / IV e">Pembina Utama / IV e</option>
                                    </optgroup>
                                </select>

                                {{-- Custom Input --}}
                                <div x-show="isOther" x-cloak>
                                    <div class="flex gap-2 mt-1.5">
                                        <input
                                                type="text"
                                                name="rank"
                                                x-model="selectedValue"
                                                class="form-input flex-1 rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                placeholder="Contoh: Pembina Tingkat I / IV b"
                                        >
                                        <button
                                                type="button"
                                                @click="isOther = false; selectedValue = ''"
                                                class="btn size-10 rounded-lg p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25"
                                                title="Kembali ke pilihan"
                                        >
                                            <i class="fa-solid fa-xmark text-lg"></i>
                                        </button>
                                    </div>
                                    <span class="text-tiny-plus text-slate-500 dark:text-navy-300 ms-1 mt-1 block">
                                        <i class="fa-solid fa-info-circle"></i>
                                        Format: Nama Pangkat / Golongan (contoh: Pembina Tingkat I / IV b)
                                    </span>
                                </div>

                                <span class="text-tiny-plus text-slate-500 dark:text-navy-300 ms-1 mt-1 block">
                                    Pilih dari daftar atau input manual
                                </span>
                            </label>
                            @error('rank')
                                <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Study Program (Conditional) --}}
                        <div id="study_program_field" style="display: none;">
                            <x-form.select
                                    label="Program Studi"
                                    name="study_program_id"
                                    id="study_program_id"
                                    :options="$studyPrograms"
                                    :value="isset($facultyOfficial) && $facultyOfficial->position->requiresStudyProgram() ? $facultyOfficial->study_program_id : ''"
                                    placeholder="Pilih program studi"
                                    helper="Wajib diisi untuk posisi Kepala Program Studi"
                            />
                        </div>
                    </div>
                </div>

                {{-- Periode Jabatan --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Periode Jabatan
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            {{-- Start Date --}}
                            <x-form.input
                                    label="Tanggal Mulai"
                                    name="start_date"
                                    type="date"
                                    :value="isset($facultyOfficial) ? $facultyOfficial->start_date->format('Y-m-d') : ''"
                                    required
                            />

                            {{-- End Date --}}
                            <label class="block">
                                <span class="font-medium text-slate-600 dark:text-navy-100">
                                    Tanggal Selesai
                                </span>
                                <input type="date"
                                       name="end_date"
                                       value="{{ old('end_date', isset($facultyOfficial) ? $facultyOfficial->end_date?->format('Y-m-d') : '') }}"
                                       class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                <span class="text-tiny-plus text-slate-500 dark:text-navy-300 ms-1 mt-1 block">Kosongkan jika masih aktif</span>
                                @error('end_date')
                                <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                        {{-- Notes --}}
                        <x-form.textarea
                                label="Catatan"
                                name="notes"
                                :value="$facultyOfficial->notes ?? ''"
                                placeholder="Catatan tambahan (opsional)"
                                rows="3"
                        />
                    </div>
                </div>
            </div>

            {{-- Right Column - Info --}}
            <div class="col-span-12 lg:col-span-4 space-y-5">
                {{-- Info Card --}}
                <div class="card p-4 sm:p-5">
                    <div>
                        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            <i class="fa-solid fa-circle-info mr-2 text-info"></i>
                            Informasi
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-navy-300 mt-1">
                            Panduan pengisian form
                        </p>
                    </div>

                    <div class="mt-4 space-y-3 text-xs text-slate-600 dark:text-navy-200">
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-check-circle text-success mt-0.5"></i>
                            <p>Pilih pejabat yang akan menjabat</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-check-circle text-success mt-0.5"></i>
                            <p>Tentukan jabatan dan periode yang sesuai</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-check-circle text-success mt-0.5"></i>
                            <p>Untuk Kaprodi, wajib pilih program studi</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-check-circle text-success mt-0.5"></i>
                            <p>Kosongkan tanggal selesai jika masih aktif</p>
                        </div>
                    </div>

                    @if(isset($facultyOfficial))
                        <div class="pt-3 mt-4 border-t border-slate-200 dark:border-navy-500">
                            <div class="space-y-2 text-xs text-slate-500 dark:text-navy-300">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>Dibuat: {{ $facultyOfficial->created_at_formatted }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Update: {{ $facultyOfficial->updated_at_formatted }}</span>
                                </div>
                                @if($facultyOfficial?->created_by_name)
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-user"></i>
                                        <span>Oleh: {{ $facultyOfficial->created_by_name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Buttons (Sticky Bottom) --}}
        <x-form.sticky-form-actions
                :cancelUrl="route('master.faculty-officials.index')"
                :submitText="isset($facultyOfficial) ? 'Update Penugasan' : 'Simpan Penugasan'"
                :submitType="isset($facultyOfficial) ? 'warning' : 'primary'"
        />
    </form>
    <x-slot:scripts>
        <script>
            // Toggle study program field based on position
            function toggleStudyProgramField() {
                const positionSelect = document.getElementById('position');
                const selectedOption = positionSelect.options[positionSelect.selectedIndex];
                const requiresProgram = selectedOption.getAttribute('data-requires-program') === '1';

                const studyProgramField = document.getElementById('study_program_field');
                const studyProgramSelect = document.getElementById('study_program_id');

                if (requiresProgram) {
                    studyProgramField.style.display = 'block';
                    studyProgramSelect.required = true;
                } else {
                    studyProgramField.style.display = 'none';
                    studyProgramSelect.required = false;
                    studyProgramSelect.value = '';
                }
            }

            // Run on page load
            document.addEventListener('DOMContentLoaded', function() {
                toggleStudyProgramField();
            });
        </script>
    </x-slot:scripts>
</x-layouts.app>