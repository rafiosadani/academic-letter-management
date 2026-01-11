<x-layouts.app :title="isset($academicYear) ? 'Edit Tahun Akademik: ' . $academicYear->year_label : 'Tambah Tahun Akademik Baru'">
    <x-ui.breadcrumb
            :title="isset($academicYear) ? 'Edit Tahun Akademik' : 'Tambah Tahun Akademik'"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Tahun Akademik', 'url' => route('master.academic-years.index')],
            ['label' => isset($academicYear) ? 'Edit' : 'Tambah']
        ]"
    />

    <x-ui.page-header
            :title="isset($academicYear) ? 'Edit Tahun Akademik: ' . $academicYear->year_label : 'Tambah Tahun Akademik Baru'"
            :description="isset($academicYear) ? 'Perbarui informasi tahun akademik' : 'Buat tahun akademik baru untuk sistem'"
            :backUrl="route('master.academic-years.index')"
    >
        <x-slot:icon>
            @if(isset($academicYear))
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            @endif
        </x-slot:icon>
    </x-ui.page-header>

    {{-- FORM --}}
    <form
            method="POST"
            action="{{ isset($academicYear) ? route('master.academic-years.update', $academicYear) : route('master.academic-years.store') }}"
            class="space-y-5 grow flex flex-col"
    >
        @csrf
        @if(isset($academicYear))
            @method('PUT')
        @endif

        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6 grow">
            {{-- Main Form --}}
            <div class="col-span-12 lg:col-span-8">
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-calendar-alt"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Informasi Tahun Akademik
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        {{-- Year Label --}}
                        <x-form.input
                                label="Tahun Akademik"
                                name="year_label"
                                :value="$academicYear->year_label ?? ''"
                                placeholder="2024/2025"
                                required
                                helper="Format: YYYY/YYYY (contoh: 2024/2025)"
                                pattern="\d{4}/\d{4}"
                        />

                        {{-- Date Range --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-form.input
                                    type="date"
                                    label="Tanggal Mulai"
                                    name="start_date"
                                    :value="isset($academicYear) ? $academicYear->start_date->format('Y-m-d') : ''"
                                    required
                                    helper="Biasanya Agustus/September"
                            />

                            <x-form.input
                                    type="date"
                                    label="Tanggal Akhir"
                                    name="end_date"
                                    :value="isset($academicYear) ? $academicYear->end_date->format('Y-m-d') : ''"
                                    required
                                    helper="Biasanya Juli tahun berikutnya"
                            />
                        </div>

{{--                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">--}}
{{--                            <x-form.datepicker--}}
{{--                                label="Tanggal Mulai"--}}
{{--                                name="start_date"--}}
{{--                                placeholder="Pilih tanggal dan waktu"--}}
{{--                                :value="isset($academicYear) ? $academicYear->start_date->format('Y-m-d') : ''"--}}
{{--                                required--}}
{{--                                helper="Biasanya Agustus/September"--}}
{{--                            />--}}

{{--                            <x-form.datepicker--}}
{{--                                    label="Tanggal Akhir"--}}
{{--                                    name="end_date"--}}
{{--                                    placeholder="Pilih tanggal dan waktu"--}}
{{--                                    :value="isset($academicYear) ? $academicYear->end_date->format('Y-m-d') : ''"--}}
{{--                                    required--}}
{{--                                    helper="Biasanya Juli tahun berikutnya"--}}
{{--                            />--}}
{{--                        </div>--}}

                        {{-- Is Active --}}
                        <div class="block">
                            <span class="font-medium text-slate-600 dark:text-navy-100">
                                Set sebagai Tahun Akademik Aktif
                            </span>
                            <div class="mt-2">
                                <label class="inline-flex items-center space-x-2">
                                    <input
                                            type="checkbox"
                                            name="is_active"
                                            value="1"
                                            {{ old('is_active', $academicYear->is_active ?? false) ? 'checked' : '' }}
                                            class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:bg-primary checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:bg-accent dark:checked:before:bg-white"
                                    />
                                    <span class="text-sm text-slate-600 dark:text-navy-200">Aktifkan tahun akademik ini</span>
                                </label>
                            </div>
                            <span class="text-tiny text-slate-500 dark:text-navy-300 mt-1 ms-1 block">
                                <i class="fa-solid fa-info-circle mr-1"></i>
                                Hanya 1 tahun akademik yang bisa aktif. Mengaktifkan ini akan menonaktifkan yang lain.
                            </span>
                        </div>

                        {{-- Info: Semester Auto-Generated --}}
                        <div class="rounded-lg bg-info/10 border border-info/20 p-4">
                            <div class="flex items-start space-x-3">
                                <i class="fa-solid fa-info-circle text-info mt-0.5"></i>
                                <div class="text-xs text-slate-600 dark:text-navy-200">
                                    <p class="font-medium mb-1">Semester akan otomatis dibuat:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Semester Ganjil (6 bulan pertama)</li>
                                        <li>Semester Genap (6 bulan kedua)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Side Info --}}
            <div class="col-span-12 lg:col-span-4 flex flex-col">
                {{-- Help Card --}}
                <div class="card p-4 sm:p-5 space-y-4">
                    <div>
                        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 flex items-center space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning"></i>
                            <span>Panduan Pengisian</span>
                        </h3>
                    </div>

                    <div class="space-y-3 text-xs text-slate-600 dark:text-navy-200">
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-circle text-[6px] mt-1.5 text-slate-400"></i>
                            <p>Tahun akademik harus unik (format: 2024/2025)</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-circle text-[6px] mt-1.5 text-slate-400"></i>
                            <p>Tanggal akhir harus lebih besar dari tanggal mulai</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-circle text-[6px] mt-1.5 text-slate-400"></i>
                            <p>Kode akan digenerate otomatis (TA-2024/2025)</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-circle text-[6px] mt-1.5 text-slate-400"></i>
                            <p>2 semester akan otomatis dibuat saat menyimpan</p>
                        </div>
                    </div>
                </div>

                {{-- Timestamps (Only on Edit) --}}
                @if(isset($academicYear))
                    <div class="card p-4 sm:p-5 space-y-4 mt-4">
                        <div>
                            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                Informasi Tambahan
                            </h3>
                        </div>

                        <div class="space-y-3">
                            {{-- Code --}}
                            <div>
                                <span class="text-xs text-slate-500 dark:text-navy-300">Kode</span>
                                <p class="font-medium text-slate-700 dark:text-navy-100">{{ $academicYear->code }}</p>
                            </div>

                            {{-- Timestamps --}}
                            <div class="pt-3 border-t border-slate-200 dark:border-navy-500 space-y-2 text-xs text-slate-500 dark:text-navy-300">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>Dibuat: {{ $academicYear->created_at_formatted }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Update: {{ $academicYear->updated_at_formatted }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons (Sticky Bottom) --}}
        <x-form.sticky-form-actions
            :cancelUrl="route('master.academic-years.index')"
            :submitText="isset($academicYear) ? 'Update Tahun Akademik' : 'Simpan Tahun Akademik'"
            :submitType="isset($academicYear) ? 'warning' : 'primary'"
        />
    </form>
</x-layouts.app>