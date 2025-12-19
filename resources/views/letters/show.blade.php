<x-layouts.app title="Detail Pengajuan Surat">
    <x-ui.breadcrumb
            title="Detail Pengajuan"
            :items="[
            ['label' => 'Pengajuan Surat', 'url' => route('letters.index')],
            ['label' => 'Detail']
        ]"
    />

    <x-ui.page-header
            title="Detail Pengajuan - {{ $letter->letter_type->label() }}"
            :description="'Diajukan pada ' . $letter->created_at->format('d F Y, H:i') . ' WIB'"
            :backUrl="route('letters.index')"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </x-slot:icon>
    </x-ui.page-header>

    <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">

        {{-- Left: Detail Info --}}
        <div class="col-span-12 lg:col-span-8 space-y-4 sm:space-y-5">

            {{-- Status Badge --}}
            <div class="card">
                <div class="flex items-center justify-between p-4 sm:p-5">
                    <div class="flex items-center space-x-3">
                        <div class="flex size-12 items-center justify-center rounded-lg bg-{{ $letter->status_badge }}/10 text-{{ $letter->status_badge }}">
                            <i class="fa-solid fa-circle-info text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                Status: {{ $letter->status_label }}
                            </h3>
                            <p class="text-xs text-slate-400 dark:text-navy-300 mt-0.5">
                                {{ $letter->letter_type->label() }}
                            </p>
                        </div>
                    </div>
                    <span class="badge bg-{{ $letter->status_badge }}/10 text-{{ $letter->status_badge }} border border-{{ $letter->status_badge }} inline-flex items-center space-x-1.5 dark:bg-{{ $letter->status_badge }}/15">
                        <i class="{{ $letter->status_icon }} {{ in_array($letter->status, ['in_progress','external_processing']) ? 'animate-spin' : '' }}"></i>
                        <span>{{ $letter->status_label }}</span>
                    </span>
                </div>
            </div>

            {{-- Pemohon Info --}}
            <div class="card">
                <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                            Informasi Pemohon
                        </h4>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs text-slate-400 dark:text-navy-300">Nama</p>
                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $letter->student->profile->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 dark:text-navy-300">NIM</p>
                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $letter->student->profile->student_or_employee_id }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 dark:text-navy-300">Program Studi</p>
                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $letter->student->profile->studyProgram->degree_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 dark:text-navy-300">Semester</p>
                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $letter->semester->semester_type }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs text-slate-400 dark:text-navy-300">Tahun Akademik</p>
                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $letter->academicYear->year_label }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Data --}}
            <div class="card">
                <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                            Data Surat
                        </h4>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="space-y-3">
                        @foreach($letter->formatted_data_input as $key => $value)
                            @if($value)
                                <div class="flex flex-col space-y-1 border-b border-slate-200 pb-3 last:border-0 last:pb-0 dark:border-navy-500">
                                    <span class="text-xs text-slate-400 dark:text-navy-300">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                    <span class="text-sm text-slate-700 dark:text-navy-100">{{ $value }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Documents --}}
            @if($letter->documents->count() > 0)
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-warning/10 p-1 text-warning">
                                <i class="fa-solid fa-paperclip"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Dokumen ({{ $letter->documents->count() }})
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        <x-document.list
                            :documents="$letter->documents"
                            :can-delete="false"
                            :title="null"
                        />
                    </div>
                </div>
            @endif

            {{-- Rejection History --}}
            @if($letter->rejectionHistories->count() > 0)
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-error/10 p-1 text-error">
                                <i class="fa-solid fa-exclamation-triangle"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Riwayat Penolakan
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        @foreach($letter->rejectionHistories as $history)
                            <div class="rounded-lg bg-error/10 border border-error/20 p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fa-solid fa-circle-xmark text-error text-lg mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                            Ditolak pada Step {{ $history->step }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-navy-300 mt-1">
                                            Oleh: {{ $history->rejectedBy->profile->full_name }} • {{ $history->rejected_at->format('d M Y, H:i') }} WIB
                                        </p>
                                        <div class="mt-3 rounded-lg bg-white dark:bg-navy-700 p-3 border border-error/20">
                                            <p class="text-xs font-medium text-slate-600 dark:text-navy-200 mb-1">Alasan Penolakan:</p>
                                            <p class="text-sm text-slate-700 dark:text-navy-100">{{ $history->reason }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: Timeline & Actions --}}
        <div class="col-span-12 lg:col-span-4 space-y-4 sm:space-y-5">

            {{-- Actions Card --}}
            <div class="card">
                <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-secondary/10 p-1 text-secondary">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            Aksi
                        </h4>
                    </div>
                </div>

                <div class="p-4 space-y-2">
                    <a href="{{ route('letters.index') }}"
                       class="btn w-full border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        Kembali ke Daftar
                    </a>

                    @if($letter->canBeEditedByStudent())
                        <a href="{{ route('letters.edit', $letter) }}"
                           class="btn w-full bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90">
                            <i class="fa-solid fa-pencil mr-2"></i>
                            Edit Pengajuan
                        </a>
                    @endif

                    @can('cancel', $letter)
                        <button
                            type="button"
                            data-toggle="modal"
                            data-target="#cancel-letter-modal-{{ $letter->id }}"
                            class="btn w-full bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90"
                        >
                            <i class="fa-solid fa-ban mr-2"></i>
                            Batalkan Pengajuan
                        </button>
                    @endcan
                </div>
            </div>

            {{-- Timeline Card --}}
            <div class="card">
                <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            Timeline Persetujuan
                        </h4>
                    </div>
                </div>

                <div class="p-4">
                    <ol class="timeline line-space [--size:1.5rem] [--line-color:var(--tw-colors-slate-200)] dark:[--line-color:var(--tw-colors-navy-500)]">
                        @foreach($letter->approvals as $approval)
                            <li class="timeline-item">
                                <div class="timeline-item-point size-[--size] rounded-full border-2
                                    @if($approval->status === 'approved') border-success bg-success
                                    @elseif($approval->status === 'rejected') border-error bg-error
                                    @elseif($approval->is_active) border-warning bg-warning animate-pulse
                                    @else border-slate-300 bg-white dark:border-navy-400 dark:bg-navy-700
                                    @endif
                                "></div>
                                <div class="timeline-item-content flex-1 pl-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                                {{ $approval->step_label }}
                                            </p>
                                            <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                                @if($approval->assigned_approver_id)
                                                    {{ $approval->assignedApprover->profile->full_name }}
                                                @else
                                                    Menunggu:
                                                    @foreach($approval->required_positions as $index => $position)
                                                        {{ \App\Enums\OfficialPosition::from($position)->label() }}{{ $index < count($approval->required_positions) - 1 ? ' atau ' : '' }}
                                                    @endforeach
                                                @endif
                                            </p>
                                            @if($approval->approved_at)
                                                <p class="text-tiny-plus text-slate-400 dark:text-navy-300 mt-1">
                                                    {{ $approval->approved_at->translatedFormat('d F Y, H:i') }} WIB
                                                </p>
                                            @endif
                                        </div>
                                        <div class="badge rounded-full text-tiny bg-{{ $approval->status_badge }}/10 text-{{ $approval->status_badge }}">
                                            {{ $approval->status_label }}
                                        </div>
                                    </div>
                                    @if($approval->note)
                                        <div class="mt-2 flex flex-col rounded-lg bg-slate-100 dark:bg-navy-600 p-2">
                                            <p class="text-xs text-justify text-slate-600 dark:text-navy-200">
                                                <i class="fa-solid fa-comment-dots mr-1"></i>
                                                {{ $approval->note }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
    {{-- Cancel Modal --}}
    @can('cancel', $letter)
        @include('letters.modals._cancel', ['letter' => $letter])
    @endcan
</x-layouts.app>