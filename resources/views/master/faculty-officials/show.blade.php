<x-layouts.app title="Detail Penugasan Jabatan">
    <x-ui.breadcrumb
            title="Detail Penugasan"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Penugasan Jabatan', 'url' => route('master.faculty-officials.index')],
            ['label' => 'Detail']
        ]"
    />

    <x-ui.page-header
            title="Detail Penugasan Jabatan"
            description="Informasi lengkap tentang penugasan jabatan pejabat"
            :backUrl="route('master.faculty-officials.index')"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
            </svg>
        </x-slot:icon>

        <x-slot:actions>
            @can('update', $facultyOfficial)
                <a href="{{ route('master.faculty-officials.edit', $facultyOfficial) }}"
                   class="btn space-x-2 bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Edit Penugasan</span>
                </a>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6 pb-4 sm:pb-6">
        {{-- Left Column - Profile Card --}}
        <div class="col-span-12 lg:col-span-4">
            <div class="card p-4 sm:p-5">
                {{-- Profile Photo --}}
                <div class="flex flex-col items-center text-center">
                    <div class="avatar size-32">
                        <img class="rounded-full border-2 border-slate-200 dark:border-navy-500"
                             src="{{ $facultyOfficial->user->profile->photo_url ?? asset('images/default-avatar.png') }}"
                             alt="avatar">
                    </div>
                    <h3 class="mt-3 text-lg font-medium text-slate-700 dark:text-navy-100">
                        {{ $facultyOfficial->user->profile->full_name ?? $facultyOfficial->user->email }}
                    </h3>
                    @if($facultyOfficial->user->profile && $facultyOfficial->user->profile->student_or_employee_id)
                        <p class="text-xs text-slate-400 dark:text-navy-300">
                            NIP/NIDN: {{ $facultyOfficial->user->profile->student_or_employee_id }}
                        </p>
                    @endif
                    @if($facultyOfficial->user->email)
                        <p class="text-xs text-slate-400 dark:text-navy-300">
                            {{ $facultyOfficial->user->email }}
                        </p>
                    @endif
                    @if($facultyOfficial->rank)
                        <p class="text-xs text-slate-400 dark:text-navy-300">
                            {{ $facultyOfficial->rank }}
                        </p>
                    @endif

                    {{-- Status Badge --}}
                    <div class="mt-3">
                        @if($facultyOfficial->is_active)
                            <span class="badge bg-success/10 text-success dark:bg-success/15">
                                <i class="fa-solid fa-circle-check mr-1"></i>
                                Jabatan Aktif
                            </span>
                        @else
                            <span class="badge bg-slate-150 text-slate-800 dark:bg-navy-500 dark:text-navy-100">
                                <i class="fa-solid fa-circle-xmark mr-1"></i>
                                Jabatan Berakhir
                            </span>
                        @endif
                    </div>

                    {{-- Position Badge --}}
                    <div class="mt-2">
                        <span class="badge bg-{{ $facultyOfficial->position->color() }}/10 text-{{ $facultyOfficial->position->color() }}">
                            <i class="fa-solid {{ $facultyOfficial->position->icon() }} mr-1"></i>
                            {{ $facultyOfficial->position->label() }}
                        </span>
                    </div>
                </div>

                {{-- Timestamps --}}
                <div class="mt-6 pt-4 border-t border-slate-200 dark:border-navy-500 space-y-2 text-xs text-slate-500 dark:text-navy-300">
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-calendar-plus"></i>
                        <span>Dibuat: {{ $facultyOfficial?->created_at_formatted }}</span>
                    </div>
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-clock"></i>
                        <span>Update: {{ $facultyOfficial?->updated_at_formatted }}</span>
                    </div>
                    @if($facultyOfficial?->created_by_name)
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-user"></i>
                            <span>Oleh: {{ $facultyOfficial->created_by_name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column - Details --}}
        <div class="col-span-12 lg:col-span-8 space-y-5">

            {{-- Informasi Jabatan --}}
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                            Informasi Jabatan
                        </h4>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {{-- Position --}}
                        <div class="{{ !$facultyOfficial->studyProgram ? 'md:col-span-2' : '' }}">
                            <span class="text-xs text-slate-500 dark:text-navy-300">Jabatan</span>
                            <div class="flex items-center space-x-2 mt-1">
                                <i class="fa-solid {{ $facultyOfficial->position->icon() }} text-{{ $facultyOfficial->position->color() }} text-md"></i>
                                <p class="text-slate-700 dark:text-navy-100">
                                    {{ $facultyOfficial->position->label() }}
                                </p>
                            </div>
                        </div>

                        {{-- Study Program --}}
                        @if($facultyOfficial->studyProgram && $facultyOfficial->studyProgram->degree_name)
                            <div>
                                <span class="text-xs text-slate-500 dark:text-navy-300">Program Studi</span>
                                <p class="font-medium text-slate-700 dark:text-navy-100 mt-1">
                                    @if($facultyOfficial->studyProgram)
                                        <span class="badge bg-info/10 text-info dark:bg-info/15">
                                        {{ $facultyOfficial->studyProgram->degree_name }}
                                    </span>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        @endif

                        {{-- Start Date --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Tanggal Mulai</span>
                            <p class=" text-slate-700 dark:text-navy-100 mt-1">
                                {{ $facultyOfficial->start_date->translatedFormat('d F Y') }}
                            </p>
                        </div>

                        {{-- End Date --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Tanggal Selesai</span>
                            <p class="text-slate-700 dark:text-navy-100 mt-1">
                                @if($facultyOfficial->end_date)
                                    {{ $facultyOfficial->end_date->translatedFormat('d F Y') }}
                                @else
                                    <span class="badge bg-success/10 text-success">Masih Aktif</span>
                                @endif
                            </p>
                        </div>

                        {{-- Duration --}}
                        <div class="sm:col-span-2">
                            <span class="text-xs text-slate-500 dark:text-navy-300">Periode Jabatan</span>
                            <p class="text-slate-700 dark:text-navy-100 mt-1">
                                {{ $facultyOfficial->period }}
                                @if($facultyOfficial->is_active)
                                    <span class="text-sm font-normal text-slate-400">
                                        ({{ $facultyOfficial->start_date->diffForHumans(null, true) }})
                                    </span>
                                @else
                                    <span class="text-sm font-normal text-slate-400">
                                        ({{ $facultyOfficial->start_date->diffInDays($facultyOfficial->end_date) }} hari / {{ $facultyOfficial->start_date->diffForHumans(null, true) }})
                                    </span>
                                @endif
                            </p>
                        </div>

                        {{-- Notes --}}
                        @if($facultyOfficial->notes)
                            <div class="sm:col-span-2">
                                <span class="text-xs text-slate-500 dark:text-navy-300">Catatan</span>
                                <p class="text-slate-700 dark:text-navy-100 mt-1">
                                    {{ $facultyOfficial->notes }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Informasi Pejabat --}}
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                            Informasi Pejabat
                        </h4>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {{-- Full Name --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Nama Lengkap</span>
                            <p class="text-slate-700 dark:text-navy-100 mt-1">
                                {{ $facultyOfficial->user->profile->full_name ?? $facultyOfficial->user->email }}
                            </p>
                        </div>

                        {{-- Student/Employee ID --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">NIP/NIDN</span>
                            <p class="text-slate-700 dark:text-navy-100 mt-1">
                                {{ $facultyOfficial->user->profile->student_or_employee_id ?? '-' }}
                            </p>
                        </div>

                        {{-- Email --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Email</span>
                            <p class="text-slate-700 dark:text-navy-100 break-all mt-1">
                                {{ $facultyOfficial->user->email }}
                            </p>
                        </div>

                        {{-- Rank --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Pangkat / Golongan</span>
                            <p class="text-slate-700 dark:text-navy-100 break-all mt-1">
                                {{ $facultyOfficial->rank ?? '-' }}
                            </p>
                        </div>

                        {{-- Phone --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">No. Telepon</span>
                            <p class="text-slate-700 dark:text-navy-100 mt-1">
                                {{ $facultyOfficial->user->profile->phone ?? '-' }}
                            </p>
                        </div>

                        {{-- Home Program --}}
                        <div class="sm:col-span-2">
                            <span class="text-xs text-slate-500 dark:text-navy-300">Program Studi (Home)</span>
                            <p class="text-slate-700 dark:text-navy-100 mt-1">
                                @if($facultyOfficial->user->profile && $facultyOfficial->user->profile->studyProgram)
                                    {{ $facultyOfficial->user->profile->studyProgram->degree }} - {{ $facultyOfficial->user->profile->studyProgram->name }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Full Width - Riwayat Jabatan --}}
        <div class="col-span-12">
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                            Riwayat Jabatan
                        </h4>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-navy-300 mt-1 ml-9">
                        Semua penugasan jabatan untuk {{ $facultyOfficial->user->profile->full_name ?? 'user ini' }}
                    </p>
                </div>

                <div class="p-2 sm:p-3">
                    @if($userAssignments->isEmpty())
                        <div class="text-center py-8">
                            <i class="fa-solid fa-inbox text-4xl text-slate-300 dark:text-navy-500 mb-3"></i>
                            <p class="text-slate-500 dark:text-navy-300">Belum ada riwayat jabatan lain.</p>
                        </div>
                    @else
                        {{-- Timeline View --}}
                        <div class="space-y-2">
                            @foreach($userAssignments as $assignment)
                                <div class="flex space-x-4 p-4 {{ $assignment->id == $facultyOfficial->id ? 'rounded-lg bg-primary/5 border border-primary/20' : '' }} border rounded-lg border-slate-200 dark:border-navy-500">
                                    {{-- Timeline Dot --}}
                                    <div class="flex flex-col items-center">
                                        <div class="flex size-10 items-center justify-center rounded-full {{ $assignment->is_active ? 'bg-success text-white' : 'bg-slate-200 dark:bg-navy-500' }}">
                                            @if($assignment->is_active)
                                                <i class="fa-solid fa-circle-check"></i>
                                            @else
                                                <i class="fa-solid fa-circle text-xs"></i>
                                            @endif
                                        </div>
                                        @if(!$loop->last)
                                            <div class="w-px flex-1 bg-slate-200 dark:bg-navy-500 my-2"></div>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <i class="fa-solid {{ $assignment->position->icon() }} text-{{ $assignment->position->color() }}"></i>
                                                    <p class="font-medium text-slate-700 dark:text-navy-100">
                                                        {{ $assignment->position->label() }}
                                                    </p>
                                                    @if($assignment->id == $facultyOfficial->id)
                                                        <span class="badge bg-primary/10 text-primary dark:bg-accent/15 dark:text-accent-light text-tiny">
                                                            Saat Ini
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($assignment->studyProgram)
                                                    <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                                        Program Studi: {{ $assignment->studyProgram->degree_name }}
                                                    </p>
                                                @endif
                                            </div>
                                            @if($assignment->is_active)
                                                <span class="badge bg-success/10 text-success dark:bg-success/15 text-tiny">
                                                    <i class="fa-solid fa-circle-check mr-1"></i>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-slate-150 text-slate-800 dark:bg-navy-500 text-tiny">
                                                    <i class="fa-solid fa-circle-xmark mr-1"></i>
                                                    Berakhir
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-2 flex items-center space-x-2 text-xs text-slate-400 dark:text-navy-300">
                                            <div class="flex items-center space-x-1">
                                                <i class="fa-solid fa-calendar"></i>
                                                <span>{{ $assignment->start_date->translatedFormat('d F Y') }}</span>
                                            </div>
                                            <span>-</span>
                                            <div class="flex items-center space-x-1">
                                                <i class="fa-solid fa-calendar"></i>
                                                <span>{{ $assignment->end_date ? $assignment->end_date->translatedFormat('d F Y') : 'Sekarang' }}</span>
                                            </div>
                                        </div>
                                        @if($assignment->notes)
                                            <p class="mt-2 text-xs text-slate-500 dark:text-navy-300">
                                                <i class="fa-solid fa-note-sticky mr-1"></i>
                                                {{ $assignment->notes }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Footer Total --}}
                        <p class="text-xs text-slate-400 dark:text-navy-300 mt-2 text-right">
                            Total: {{ $userAssignments->count() }} Penugasan Jabatan
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>