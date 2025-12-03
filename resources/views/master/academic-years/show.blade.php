<x-layouts.app title="Detail Tahun Akademik: {{ $academicYear->year_label }}">
    <x-ui.breadcrumb
            title="Detail Tahun Akademik"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Tahun Akademik', 'url' => route('master.academic-years.index')],
            ['label' => 'Detail']
        ]"
    />

    <x-ui.page-header
            title="Detail Tahun Akademik: {{ $academicYear->year_label }}"
            description="Informasi lengkap tentang tahun akademik dan semester"
            backUrl="{{ route('master.academic-years.index') }}"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </x-slot:icon>

        <x-slot:actions>
            <a
                    href="{{ route('master.academic-years.edit', $academicYear) }}"
                    class="btn space-x-2 bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90"
            >
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Tahun Akademik</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
        {{-- Left Column - Main Info --}}
        <div class="col-span-12 lg:col-span-6">
            <div class="card p-4 sm:p-5 space-y-4">
                <div>
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                        Informasi Tahun Akademik
                    </h3>
                </div>

                <div class="space-y-4">
                    {{-- Code --}}
                    <div>
                        <span class="text-xs text-slate-500 dark:text-navy-300">Kode</span>
                        <p class="font-medium text-slate-700 dark:text-navy-100">{{ $academicYear->code }}</p>
                    </div>

                    {{-- Year Label --}}
                    <div>
                        <span class="text-xs text-slate-500 dark:text-navy-300">Tahun Akademik</span>
                        <p class="font-medium text-slate-700 dark:text-navy-100">{{ $academicYear->year_label }}</p>
                    </div>

                    {{-- Period --}}
                    <div>
                        <span class="text-xs text-slate-500 dark:text-navy-300">Periode</span>
                        <p class="text-sm text-slate-600 dark:text-navy-200">{{ $academicYear->period_text }}</p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <span class="text-xs text-slate-500 dark:text-navy-300">Status</span>
                        <div class="mt-1">
                            {!! $academicYear->status_badge !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Semesters & Timestamps --}}
        <div class="col-span-12 lg:col-span-6 space-y-4 sm:space-y-5">
            {{-- Semesters --}}
            <div class="card p-4 sm:p-5">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                        Semester
                    </h3>
                </div>

                @if($academicYear->semesters->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-sm text-slate-500 dark:text-navy-300">Tidak ada semester</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($academicYear->semesters as $semester)
                            <div class="flex items-center justify-between rounded-lg bg-slate-100 dark:bg-navy-800 px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <div>
                                        {!! $semester->semester_badge !!}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                            Semester {{ $semester->semester_type->label() }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-navy-300">
                                            {{ $semester->period_text }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    {!! $semester->status_badge !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Timestamps --}}
            <div class="card p-4 sm:p-5">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-info/10 text-info">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                        Riwayat Data
                    </h3>
                </div>

                <div class="space-y-3 text-xs">
                    {{-- Created --}}
                    <div class="flex flex-col space-y-1 rounded-lg bg-slate-100 dark:bg-navy-800 px-4 py-3">
                        <div class="flex items-center space-x-2 text-slate-500 dark:text-navy-300">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span>Dibuat</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-700 dark:text-navy-100">{{ $academicYear->created_by_name }}</span>
                            <span class="text-slate-600 dark:text-navy-200">{{ $academicYear->created_at_formatted }}</span>
                        </div>
                    </div>

                    {{-- Updated --}}
                    @if($academicYear->updated_at)
                        <div class="flex flex-col space-y-1 rounded-lg bg-slate-100 dark:bg-navy-800 px-4 py-3">
                            <div class="flex items-center space-x-2 text-slate-500 dark:text-navy-300">
                                <i class="fa-solid fa-edit"></i>
                                <span>Terakhir Diubah</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-700 dark:text-navy-100">{{ $academicYear->updated_by_name }}</span>
                                <span class="text-slate-600 dark:text-navy-200">{{ $academicYear->updated_at_formatted }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>