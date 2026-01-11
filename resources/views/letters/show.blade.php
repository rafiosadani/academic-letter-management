<x-layouts.app title="Detail Pengajuan Surat">
    <x-ui.breadcrumb
            title="Detail Pengajuan"
            :items="[
            ['label' => 'Surat Saya', 'url' => route('letters.index')],
            ['label' => 'Pengajuan Surat Saya', 'url' => route('letters.index')],
            ['label' => 'Detail']
        ]"
    />

    <x-ui.page-header
            title="Detail Pengajuan - {{ $letter->letter_type->label() }}"
            :description="'Diajukan pada ' . $letter->created_at_full"
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
                    <span class="badge bg-{{ $letter->status_badge }}/10 text-{{ $letter->status_badge }} text-tiny border border-{{ $letter->status_badge }} inline-flex items-center space-x-1.5 dark:bg-{{ $letter->status_badge }}/15">
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
                        <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
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
                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $letter->student->profile->studyProgram->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 dark:text-navy-300">Semester</p>
                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $letter->semester->semester_type ?? '-' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs text-slate-400 dark:text-navy-300">Tahun Akademik</p>
                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">{{ $letter->academicYear->year_label ?? '-' }}</p>
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
                        <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
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

            @if($letter->hasStudentList())
                <div class="card mt-5">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                    Daftar Mahasiswa
                                </h4>
                            </div>
                            <span class="text-xs font-medium text-slate-700 dark:text-navy-100 dark:border-navy-500">
                                Total: {{ count($letter->student_list) }} Orang
                            </span>
                        </div>
                    </div>
                    <div class="p-4 sm:p-5">
                        <div class="is-scrollbar-hidden min-w-full overflow-x-auto rounded-lg border border-slate-300 dark:border-navy-500">
                            <table class="w-full text-left">
                                <thead>
                                <tr class="bg-slate-200 dark:bg-navy-800">
                                    <th class="w-12 border-r border-slate-300 px-4 py-3 text-xs font-medium text-slate-800 dark:border-navy-500 dark:text-navy-100 text-center">
                                        No
                                    </th>
                                    <th class="border-r border-slate-300 px-4 py-3 text-xs font-medium text-slate-800 dark:border-navy-500 dark:text-navy-100">
                                        Nama Mahasiswa
                                    </th>
                                    <th class="border-r border-slate-300 px-4 py-3 text-xs font-medium text-slate-800 dark:border-navy-500 dark:text-navy-100">
                                        NIM
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-slate-800 dark:border-navy-500 dark:text-navy-100">
                                        Program Studi
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                                @foreach($letter->student_list as $index => $student)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-navy-800/50">
                                        <td class="border-r border-slate-200 px-4 py-3 text-center text-sm text-slate-700 dark:border-navy-500 dark:text-navy-100">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="border-r border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-navy-500 dark:text-navy-100">
                                            {{ $student['name'] ?? '-' }}
                                        </td>
                                        <td class="border-r border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-navy-500 dark:text-navy-100">
                                            {{ $student['nim'] ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-700 dark:text-navy-100">
                                            {{ $student['program'] ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Dokumen Pendukung --}}
            @php
                $supportingDocs = $letter->documents()->where('category', 'supporting')->get();
            @endphp

            @if($supportingDocs->count() > 0)
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-warning/10 p-1 text-warning">
                                <i class="fa-solid fa-paperclip"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Dokumen Pendukung ({{ $supportingDocs->count() }})
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        <x-document.list
                                :documents="$supportingDocs"
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
                            <div class="rounded-lg border border-error/30 bg-slate-50/50 p-3 shadow-sm dark:border-error/30 dark:bg-navy-700">
                                <div class="flex items-start space-x-3">
                                    {{-- Ikon Box Putih/Sleek --}}
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-error/10 dark:bg-error/20">
                                        <i class="fa-solid fa-ban text-sm text-error"></i>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs font-bold text-slate-700 dark:text-navy-100">
                                                Penolakan Step {{ $history->step }}
                                            </p>
                                            <span class="text-tiny-plus font-medium text-slate-400 dark:text-navy-300">
                                                {{ $history->rejected_at_full }}
                                            </span>
                                        </div>

                                        {{-- Info Penolak --}}
                                        <p class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1">
                                            Oleh: <span class="font-medium text-slate-600 dark:text-navy-200">{{ $history->rejectedBy->profile->full_name }}</span>
                                        </p>

                                        {{-- Alasan Penolakan: Lebih Minimalis --}}
                                        <div class="mt-2.5 rounded-md border-l-2 border-error/30 bg-error/5 px-2.5 py-2 dark:bg-error/10">
                                            <p class="text-[10px] font-bold uppercase tracking-tight text-error/80">Alasan:</p>
                                            <p class="mt-0.5 text-tiny-plus italic leading-relaxed text-slate-600 dark:text-navy-200">
                                                "{{ $history->reason }}"
                                            </p>
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

                <div class="p-4 sm:p-5 space-y-2">
                    <a href="{{ route('letters.index') }}"
                       class="btn w-full inline-flex items-center justify-center gap-2 border border-slate-300 font-medium text-slate-700 hover:bg-slate-100 active:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
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
                                class="btn w-full bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90">
                            <i class="fa-solid fa-ban mr-2"></i>
                            Batalkan Pengajuan
                        </button>
                    @endcan
                </div>
            </div>

            {{-- Download Surat (ONLY for letters completed status!) --}}
            @if($letter->status === 'completed')
                @php
                    $finalPdf = $letter->documents()->where('type', 'final')->latest()->first();
                @endphp

                @if($finalPdf)
                    <div class="card">
                        <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success">
                                    <i class="fa-solid fa-download"></i>
                                </div>
                                <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                    Download Surat
                                </h4>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5">
                            <div class="rounded-lg border border-success/20 bg-success/10 p-3 mb-2">
                                <div class="flex items-start space-x-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm dark:bg-navy-700">
                                        <i class="fa-solid fa-check-double text-lg text-success"></i>
                                    </div>
                                    <div class="flex flex-col min-w-0 text-left">
                                        <span class="text-tiny font-bold uppercase tracking-wider text-success">
                                            Proses Selesai
                                        </span>
                                        <p class="mt-1 text-xs font-semibold text-slate-700 dark:text-navy-100 leading-normal">
                                            Surat Anda telah selesai diproses dan siap diunduh!
                                        </p>
                                        <div class="mt-1 flex items-center text-[11px] text-slate-500 dark:text-navy-300">
                                            <i class="fa-solid fa-circle-info mr-1 text-[10px]"></i>
                                            Silakan cek bagian dokumen di bawah.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('letters.download-pdf', $letter) }}"
                               class="btn w-full bg-error text-xs-plus font-medium text-white hover:bg-error-focus active:bg-error-focus/90">
                                <i class="fa-solid fa-file-pdf mr-2"></i>
                                Download PDF
                            </a>

                            <p class="text-tiny text-slate-400 dark:text-navy-300 mt-2 text-center">
                                File: {{ $finalPdf->file_name }}
                            </p>
                        </div>
                    </div>
                @endif
            @endif

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

                <div class="p-4 sm:p-5">
                    <ol class="timeline line-space [--size:1.5rem] [--line-color:var(--tw-colors-slate-200)] dark:[--line-color:var(--tw-colors-navy-500)]">
                        @foreach($letter->approvals as $approval)
                            <li class="timeline-item">
                                <div class="timeline-item-point flex items-center justify-center size-[--size] rounded-full border-1 text-xs font-semibold
                                    @if($approval->status === 'approved')
                                        border-success bg-success text-white
                                    @elseif($approval->status === 'rejected')
                                        border-error bg-error text-white
                                    @elseif($approval->is_active)
                                        border-warning bg-warning text-white animate-pulse
                                    @else
                                        border-slate-300 bg-slate-50 text-slate-500 z-10
                                        dark:border-navy-400 dark:bg-navy-900 dark:text-navy-100
                                    @endif
                                ">
                                    {{ $approval->step }}
                                </div>
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
                                                <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                                    {{ $approval->approved_at_full }}
                                                </p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center gap-1.5 badge px-1 py-1 bg-{{ $approval->status_badge }}/10 text-{{ $approval->status_badge }} text-tiny border border-{{ $approval->status_badge }} dark:bg-{{ $approval->status_badge }}/15">
                                            <i class="{{ $approval->status_icon }}"></i>
                                            <span>{{ $approval->status_label }}</span>
                                        </span>
                                    </div>
                                    @if($approval->note)
                                        <div class="mt-2 rounded-md bg-slate-100 dark:bg-navy-600 px-2.5 py-2 border-l-2 border-slate-300 dark:border-navy-400">
                                            <div class="flex items-start gap-2">
                                                <i class="fa-solid fa-comment-dots text-[10px] text-slate-400 mt-0.5"></i>
                                                <div class="flex-1 min-w-0 space-y-0.5">
                                                    <p class="text-tiny font-semibold text-slate-500 dark:text-navy-300 uppercase tracking-tight">
                                                        Catatan:
                                                    </p>
                                                    <p class="text-tiny text-slate-600 dark:text-navy-200 leading-normal italic">
                                                        {{ $approval->note }}
                                                    </p>
                                                </div>
                                            </div>
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