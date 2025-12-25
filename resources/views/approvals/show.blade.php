<x-layouts.app title="Detail Persetujuan">
    <x-ui.breadcrumb
            title="Detail Persetujuan"
            :items="[
            ['label' => 'Transaksi Surat', 'url' => route('approvals.index')],
            ['label' => 'Persetujuan Surat', 'url' => route('approvals.index')],
            ['label' => 'Detail']
        ]"
    />

    <x-ui.page-header
            title="Detail Pengajuan - {{ $letter->letter_type->label() }}"
            :description="'Diajukan pada ' . $letter->created_at_full"
            :backUrl="route('approvals.index')"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </x-slot:icon>
    </x-ui.page-header>

    <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
        {{-- Main Content --}}
        <div class="col-span-12 lg:col-span-8">
            {{-- Letter Info Card --}}
            <div class="card">
                <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-file-lines text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                    {{ $letter->letter_type->label() }}
                                </h4>
                                <p class="text-xs text-slate-400 dark:text-navy-300">
                                    {{ $letter->semester->semester_type }} - {{ $letter->academicYear->year_label }}
                                </p>
                            </div>
                        </div>
                        <span class="badge bg-{{ $letter->status_badge }}/10 text-{{ $letter->status_badge }} text-tiny border border-{{ $letter->status_badge }} inline-flex items-center space-x-1.5 dark:bg-{{ $letter->status_badge }}/15">
                            <i class="{{ $letter->status_icon }} {{ in_array($letter->status, ['in_progress','external_processing']) ? 'animate-spin' : '' }}"></i>
                            <span>{{ $letter->status_label }}</span>
                        </span>
                    </div>
                </div>

                {{-- Student Info --}}
                <div class="p-4 sm:p-5">
                    <div class="mb-5 rounded-xl border border-slate-200 p-4 dark:border-navy-500">
                        <div class="mb-4 flex items-center gap-2">
                            <div class="flex size-6 items-center justify-center rounded-lg bg-primary/10 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-user-graduate text-xs"></i>
                            </div>
                            <h5 class="text-sm font-semibold text-slate-700 dark:text-navy-100">
                                Informasi Mahasiswa
                            </h5>
                        </div>

                        {{-- Content --}}
                        <div class="flex items-start gap-4">
                            <div class="avatar size-16 shrink-0">
                                <img
                                    src="{{ $letter->student->profile->photo_url ?? asset('assets/default.png') }}"
                                    alt="{{ $letter->student->profile->full_name }}"
                                    class="rounded-full object-cover"
                                >
                            </div>

                            {{-- Info --}}
                            <div class="flex-1">
                                <p class="font-medium text-slate-800 dark:text-navy-50">
                                    {{ $letter->student->profile->full_name }}
                                </p>
                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-2 text-xs">
                                    <div class="flex items-center gap-2 text-slate-600 dark:text-navy-200">
                                        <i class="fa-solid fa-id-card fa-fw text-slate-400"></i>
                                        <span>NIM</span>
                                        <span class="font-medium text-slate-700 dark:text-navy-100">
                                            : {{ $letter->student->profile->student_or_employee_id ?? '-' }}
                                        </span>
                                    </div>

                                    {{-- Program Studi --}}
                                    <div class="flex items-center gap-2 text-slate-600 dark:text-navy-200">
                                        <i class="fa-solid fa-graduation-cap fa-fw text-slate-400"></i>
                                        <span>Program Studi</span>
                                        <span class="font-medium text-slate-700 dark:text-navy-100">
                                            : {{ $letter->student->profile->studyProgram?->degree_name ?? '-' }}
                                        </span>
                                    </div>

                                    {{-- TTL --}}
                                    @if($letter->student->profile->place_of_birth || $letter->student->profile->date_of_birth)
                                        <div class="flex items-center gap-2 text-slate-600 dark:text-navy-200">
                                            <i class="fa-solid fa-cake-candles fa-fw text-slate-400"></i>
                                            <span>TTL</span>
                                            <span class="font-medium text-slate-700 dark:text-navy-100">
                                                : {{ $letter->student->profile->place_of_birth ?? '-' }},
                                                {{ $letter->student->profile->date_of_birth
                                                    ? \Carbon\Carbon::parse($letter->student->profile->date_of_birth)->translatedFormat('d F Y')
                                                    : '-' }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Telepon --}}
                                    @if($letter->student->profile->phone)
                                        <div class="flex items-center gap-2 text-slate-600 dark:text-navy-200">
                                            <i class="fa-solid fa-phone fa-fw text-slate-400"></i>
                                            <span>Telepon</span>
                                            <span class="font-medium text-slate-700 dark:text-navy-100">
                                                : {{ $letter->student->profile->phone }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Alamat --}}
                                    @if($letter->student->profile->address)
                                        <div class="flex items-center gap-2 text-slate-600 dark:text-navy-200 sm:col-span-2">
                                            <i class="fa-solid fa-location-dot fa-fw text-slate-400"></i>
                                            <span>Alamat</span>
                                            <span class="font-medium text-slate-700 dark:text-navy-100 truncate">
                                                : {{ $letter->student->profile->address }}
                                            </span>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== INFORMASI ORANG TUA (OPTIONAL) ===== --}}
                    @php
                        $profile = $letter->student->profile;
                        $hasParentInfo =
                            $profile->parent_name ||
                            $profile->parent_nip ||
                            $profile->parent_rank ||
                            $profile->parent_institution;
                    @endphp

                    @if($hasParentInfo)
                        <div class="mb-5 rounded-xl border border-slate-200 p-4 dark:border-navy-500">
                            <div class="mb-4 flex items-center gap-2">
                                <div class="flex size-6 items-center justify-center rounded-lg bg-secondary/10 text-secondary">
                                    <i class="fa-solid fa-user-tie text-xs"></i>
                                </div>
                                <h5 class="text-sm font-semibold text-slate-700 dark:text-navy-100">
                                    Informasi Orang Tua
                                </h5>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-2 text-xs pl-2">
                                @if($profile->parent_name)
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-user fa-fw text-slate-400"></i>
                                        <span>Nama</span>
                                        <span class="font-medium">: {{ $profile->parent_name }}</span>
                                    </div>
                                @endif

                                @if($profile->parent_nip)
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-id-badge fa-fw text-slate-400"></i>
                                        <span>NIP</span>
                                        <span class="font-medium">: {{ $profile->parent_nip }}</span>
                                    </div>
                                @endif

                                @if($profile->parent_rank)
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-ranking-star fa-fw text-slate-400"></i>
                                        <span>Pangkat</span>
                                        <span class="font-medium">: {{ $profile->parent_rank }}</span>
                                    </div>
                                @endif

                                @if($profile->parent_institution)
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-building-columns fa-fw text-slate-400"></i>
                                        <span>Instansi</span>
                                        <span class="font-medium">: {{ $profile->parent_institution }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif


                    {{-- Form Data --}}
                    <div class="mb-5 rounded-lg border border-slate-200 p-4 dark:border-navy-500">
                        <div class="mb-4 flex items-center gap-2">
                            <div class="flex size-6 items-center justify-center rounded-lg bg-success/10 text-success">
                                <i class="fa-solid fa-file-pen text-xs"></i>
                            </div>
                            <h5 class="text-sm font-semibold text-slate-700 dark:text-navy-100">
                                Data Surat
                            </h5>
                        </div>
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

                    {{-- Documents --}}
                    @php
                        $supportingDocs = $letter->documents()->where('category', 'supporting')->get();
//                        $supportingDocs = $letter->documents()->supporting()->get();
                        $generatedDocx = $letter->documents()->where('category', 'generated')->where('type', 'draft')->latest()->first();
                        $finalPdf = $letter->documents()->where('type', 'final')->latest()->first();
                    @endphp

                    {{-- Documents --}}
                    @if($supportingDocs->count() > 0)
                        <div class="mb-5 rounded-lg border border-slate-200 p-4 dark:border-navy-500">
                            <div class="mb-4 flex items-center gap-2">
                                <div class="flex size-6 items-center justify-center rounded-lg bg-warning/10 p-1 text-warning">
                                    <i class="fa-solid fa-paperclip text-xs"></i>
                                </div>
                                <h5 class="text-sm font-semibold text-slate-700 dark:text-navy-100">
                                    Dokumen Pendukung ({{ $letter->documents->count() }})
                                </h5>
                            </div>
                            <div class="space-y-3">
                                <x-document.list
                                    :documents="$supportingDocs"
                                    :can-delete="false"
                                    :title="null"
                                />
                            </div>
                        </div>
                    @endif

                    {{-- Current Approval Info --}}
                    <div class="rounded-lg bg-info/10 border border-info/20 p-4">
                        <h5 class="font-medium text-info mb-2">
                            <i class="fa-solid fa-info-circle mr-2"></i>
                            Step Persetujuan Saat Ini
                        </h5>
                        <div class="space-y-2 text-sm">
                            <p class="text-slate-700 dark:text-navy-100">
                                <strong>Step {{ $approval->step }}:</strong> {{ $approval->step_label }}
                            </p>
                            <p class="text-xs text-slate-600 dark:text-navy-200">
                                Menunggu persetujuan dari:
                                @foreach($approval->required_positions as $index => $position)
                                    {{ \App\Enums\OfficialPosition::from($position)->label() }}{{ $index < count($approval->required_positions) - 1 ? ' atau ' : '' }}
                                @endforeach
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="card mt-4">
                <div class="border-b border-slate-200 p-4 sm:px-5 dark:border-navy-500">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            Timeline Persetujuan
                        </h4>
                    </div>
                </div>
                <div class="p-4 sm:px-5">
                    <ol class="timeline line-space [--size:1.5rem] [--line-color:var(--tw-colors-slate-200)] dark:[--line-color:var(--tw-colors-navy-500)]">
                        @foreach($letter->approvals as $timelineApproval)
                            <li class="timeline-item">
                                <div class="timeline-item-point flex items-center justify-center size-[--size] rounded-full border-1 text-xs font-semibold
                                    @if($timelineApproval->status === 'approved')
                                        border-success bg-success text-white
                                    @elseif($timelineApproval->status === 'rejected')
                                        border-error bg-error text-white
                                    @elseif($timelineApproval->is_active)
                                        border-warning bg-warning text-white animate-pulse
                                    @else
                                        border-slate-300 bg-slate-50 text-slate-500 z-10
                                        dark:border-navy-400 dark:bg-navy-900 dark:text-navy-100
                                    @endif
                                ">
                                    {{ $timelineApproval->step }}
                                </div>
                                <div class="timeline-item-content flex-1 pl-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                                {{ $timelineApproval->step_label }}
                                            </p>
                                            <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                                @if($timelineApproval->assigned_approver_id)
                                                    {{ $timelineApproval->assignedApprover->profile->full_name }}
                                                @else
                                                    Menunggu:
                                                    @foreach($timelineApproval->required_positions as $index => $position)
                                                        {{ \App\Enums\OfficialPosition::from($position)->label() }}{{ $index < count($timelineApproval->required_positions) - 1 ? ' atau ' : '' }}
                                                    @endforeach
                                                @endif
                                            </p>
                                            @if($timelineApproval->approved_at)
                                                <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                                                    {{ $timelineApproval->approved_at_full }}
                                                </p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center gap-1.5 badge px-1 py-1 bg-{{ $timelineApproval->status_badge }}/10 text-{{ $timelineApproval->status_badge }} text-tiny border border-{{ $timelineApproval->status_badge }} dark:bg-{{ $timelineApproval->status_badge }}/15">
                                            <i class="{{ $timelineApproval->status_icon }}"></i>
                                            <span>{{ $timelineApproval->status_label }}</span>
                                        </span>
                                    </div>
                                    @if($timelineApproval->note)
                                        <div class="mt-2 rounded-md bg-slate-100 dark:bg-navy-600 px-2.5 py-2 border-l-2 border-slate-300 dark:border-navy-400">
                                            <div class="flex items-start gap-2">
                                                <i class="fa-solid fa-comment-dots text-[10px] text-slate-400 mt-0.5"></i>
                                                <div class="flex-1 min-w-0 space-y-0.5">
                                                    <p class="text-tiny font-semibold text-slate-500 dark:text-navy-300 uppercase tracking-tight">
                                                        Catatan:
                                                    </p>
                                                    <p class="text-tiny text-slate-600 dark:text-navy-200 leading-normal italic">
                                                        {{ $timelineApproval->note }}
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

        {{-- Sidebar Actions --}}
        <div class="col-span-12 lg:col-span-4">
            <div class="card sticky top-4">
                <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                    <div class="flex item-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg
                            bg-warning/10 text-warning dark:bg-warning/15 dark:text-warning">
                            <i class="fa-solid fa-gavel"></i>
                        </div>
                        <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            Aksi Persetujuan
                        </h4>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    {{-- Back Button --}}
                    <a href="{{ route('approvals.index') }}"
                       class="btn w-full inline-flex items-center justify-center gap-2 border border-slate-300 font-medium text-slate-700 hover:bg-slate-100 active:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Kembali
                    </a>
                    @if($approval->status === 'pending' && $approval->is_active)
                        {{-- STEP 1: Normal Approval (Show Approve/Reject buttons) --}}
                        @if($approval->canManuallyApprove())
                            @if($canApprove)
                                {{-- Approve Button --}}
                                <button
                                        type="button"
                                        data-toggle="modal"
                                        data-target="#approve-modal-{{ $approval->id }}"
                                        class="btn w-full bg-success font-medium text-white hover:bg-success-focus">
                                    <i class="fa-solid fa-check mr-2"></i>
                                    Setujui
                                </button>

                                {{-- Reject Button --}}
                                <button
                                        type="button"
                                        data-toggle="modal"
                                        data-target="#reject-modal-{{ $approval->id }}"
                                        class="btn w-full inline-flex items-center justify-center gap-2 bg-error text-white font-semibold shadow-sm hover:shadow-md hover:bg-error-focus active:bg-error-focus/90">
                                    <i class="fa-solid fa-times mr-2"></i>
                                    Tolak
                                </button>

                                @can('editContent', $approval)
                                    <button
                                            type="button"
                                            data-toggle="modal"
                                            data-target="#edit-content-modal-{{ $approval->id }}"
                                            class="btn w-full inline-flex items-center justify-center gap-2 bg-warning text-white font-semibold shadow-sm hover:shadow-md hover:bg-warning-focus active:bg-warning-focus/90">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Edit Konten
                                    </button>
                                @endcan

                                {{-- Info untuk Step 2: DOCX will be generated --}}
                                @if($approval->step === 2 && $letter->letter_type->isExternal())
                                    <div class="rounded-lg border border-info/20 bg-info/10 p-3">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm dark:bg-navy-700">
                                                <i class="fa-solid fa-circle-info text-info text-lg"></i>
                                            </div>
                                            <div class="flex flex-col min-w-0 text-left">
                                                <span class="text-tiny font-bold uppercase tracking-wider text-info">
                                                    Sistem Otomatis
                                                </span>
                                                <p class="mt-1 text-xs text-slate-600 dark:text-navy-200">
                                                    DOCX akan otomatis di-generate setelah Anda menyetujui tahap ini.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                @endif
                            @else
                                {{-- Cannot approve - no permission --}}
                                <div class="rounded-lg bg-slate-100 dark:bg-navy-600 p-3 text-center">
                                    <i class="fa-solid fa-info-circle text-slate-400 dark:text-navy-300"></i>
                                    <p class="text-xs text-slate-600 dark:text-navy-200 mt-2">
                                        Anda tidak memiliki akses untuk menyetujui step ini
                                    </p>
                                </div>
                            @endif
                        @endif

                        {{-- STEP 3: Upload Step (Show Upload button) --}}
                        {{-- UPLOAD STEP (Step 3 for external) --}}
                        @if($approval->isUploadStep())
                            <button
                                type="button"
                                data-toggle="modal"
                                data-target="#upload-pdf-modal-{{ $approval->letter_request_id }}"
                                class="btn w-full bg-success font-medium text-white hover:bg-success-focus"
                            >
                                <i class="fa-solid fa-upload mr-2"></i>
                                Upload PDF Final
                            </button>

                            <div class="rounded-lg border border-warning/20 bg-warning/10 p-3">
                                <div class="flex items-start space-x-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm dark:bg-navy-700">
                                        <i class="fa-solid fa-file-arrow-up text-lg text-warning"></i>
                                    </div>
                                    <div class="flex flex-col min-w-0 text-left">
                                        <span class="text-tiny font-bold uppercase tracking-wider text-warning">
                                            Perlu Upload PDF
                                        </span>
                                        <p class="mt-1 text-xs text-slate-600 dark:text-navy-200 leading-relaxed">
                                            Silakan upload file PDF yang sudah ditandatangani dari UB Pusat untuk menyelesaikan seluruh proses pengajuan ini.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                    {{-- APPROVED: Show approved status --}}
                    @elseif($approval->status === 'approved')
                        <div class="rounded-lg border border-success/20 bg-success/10 p-3">
                            <div class="flex items-start space-x-3">
                                <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm dark:bg-navy-700">
                                    <i class="fa-solid fa-check-circle text-lg text-success"></i>
                                </div>
                                <div class="flex flex-col min-w-0 text-left">
                                    <span class="text-tiny font-bold uppercase tracking-wider text-success">
                                        Sudah Disetujui
                                    </span>
                                    <p class="mt-1 text-xs font-semibold text-slate-700 dark:text-navy-100 truncate">
                                        {{ $approval->approver?->profile->full_name ?? 'System' }}
                                    </p>
                                    <div class="mt-0.5 flex items-center text-tiny-plus text-slate-600 dark:text-navy-200">
                                        <i class="fa-regular fa-clock mr-1 text-[10px]"></i>
                                        {{ $approval->approved_at_full }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Download DOCX (for step 2 external letters) --}}
                        @if($generatedDocx && $approval->step === 2 && $letter->letter_type->isExternal())
                            <a href="{{ route('letters.download-docx', $letter) }}"
                               class="btn w-full bg-info font-medium text-white hover:bg-info-focus">
                                <i class="fa-solid fa-file-word mr-2"></i>
                                Download DOCX
                            </a>

                            {{-- Info: Instruksi setelah download --}}
                            <div class="rounded-lg border border-info/20 bg-info/10 p-3">
                                <div class="flex items-start space-x-3">
                                    <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm dark:bg-navy-700">
                                        <i class="fa-solid fa-circle-info text-lg text-info"></i>
                                    </div>

                                    {{-- Konten Teks --}}
                                    <div class="flex flex-col min-w-0 text-left">
                                        <span class="text-tiny font-bold uppercase tracking-wider text-info">
                                            Langkah Selanjutnya
                                        </span>
                                        <div class="mt-2 space-y-1 text-xs text-slate-600 dark:text-navy-200">
                                            <div class="flex space-x-1">
                                                <span class="font-semibold text-info">1.</span>
                                                <p>Download file DOCX di atas.</p>
                                            </div>
                                            <div class="flex space-x-1">
                                                <span class="font-semibold text-info">2.</span>
                                                <p>Kirim ke Sistem UB Pusat untuk ditandatangani.</p>
                                            </div>
                                            <div class="flex space-x-1">
                                                <span class="font-semibold text-info">3.</span>
                                                <p>Setelah ditandatangani, upload PDF final di Step 3.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Next Step Button (if same approver) --}}
                        @php
                            $nextApproval = $letter->approvals()
                                ->where('step', '>', $approval->step)
                                ->where('status', 'pending')
                                ->where('is_active', true)
                                ->first();

                            $canApproveNext = $nextApproval ? app(\App\Services\ApprovalService::class)->canUserViewApproval(auth()->user(), $nextApproval) : false;
                        @endphp

                        @if($nextApproval && $canApproveNext)
                            <a href="{{ route('approvals.show', $nextApproval) }}"
                               class="btn w-full bg-primary text-xs-plus font-medium text-white hover:bg-primary-focus">
                                <i class="fa-solid fa-arrow-right mr-2"></i>
                                Lanjut ke Step Berikutnya
                            </a>
                        @endif

                    {{-- REJECTED: Show rejected status --}}
                    @elseif($approval->status === 'rejected')
                        <div class="rounded-lg border border-error/20 bg-error/10 p-3">
                            <div class="flex items-start space-x-3">
                                <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm dark:bg-navy-700">
                                    <i class="fa-solid fa-times-circle text-lg text-error"></i>
                                </div>
                                <div class="flex flex-col min-w-0 text-left">
                                    <span class="text-tiny font-bold uppercase tracking-wider text-error">
                                        Ditolak
                                    </span>
                                    <p class="mt-1 text-xs font-semibold text-slate-700 dark:text-navy-100 truncate">
                                        {{ $approval->approver?->profile->full_name ?? 'System' }}
                                    </p>
                                    <div class="mt-0.5 flex items-center text-tiny text-slate-600 dark:text-navy-200">
                                        <i class="fa-regular fa-clock mr-1 text-[10px]"></i>
                                        {{ $approval->approved_at_full }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Download Final PDF (if completed) --}}
                    @if($finalPdf)
                        <a href="{{ route('letters.download-pdf', $letter) }}"
                           class="btn w-full bg-error font-medium text-white hover:bg-error-focus active:bg-error-focus/90">
                            <i class="fa-solid fa-file-pdf mr-2"></i>
                            Download PDF Final
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modals (only include if needed) --}}
    @if($approval->status === 'pending' && $approval->is_active && $canApprove && $approval->canManuallyApprove())
        @include('approvals.modals._approve', ['approval' => $approval, 'letter' => $letter])
        @include('approvals.modals._reject', ['approval' => $approval, 'letter' => $letter])

        @can('editContent', $approval)
            @include('approvals.modals._edit', ['approval' => $approval, 'letter' => $letter])
        @endcan
    @endif

    {{-- Upload PDF Modal (for upload step) --}}
    @if($approval->isUploadStep() && $approval->status === 'pending' && $approval->is_active)
        @include('approvals.modals._upload-pdf', ['letter' => $letter])
    @endif

    @if(session('open_reject_modal'))
        <div data-open-modal="reject-modal-{{ session('open_reject_modal') }}" class="hidden"></div>
    @endif

    @if(session('open_edit_modal_id'))
        <div data-open-modal="edit-content-modal-{{ session('open_edit_modal_id') }}" class="hidden"></div>
    @endif

    @if(session('open_upload_final_pdf_modal_id'))
        <div data-open-modal="upload-pdf-modal-{{ session('open_upload_final_pdf_modal_id') }}" class="hidden"></div>
    @endif

    <x-slot:scripts>
        <script>
            document.addEventListener('click', function (event) {
                const closeButton = event.target.closest('[data-close-modal]');

                if (closeButton) {
                    const isRejectModal = closeButton.closest('#reject-modal-{{ $approval->id }}');
                    const isEditModal = closeButton.closest('#edit-content-modal-{{ $approval->id }}');
                    const isUploadModal = closeButton.closest('#upload-pdf-modal-{{ $letter->id }}');

                    if (isRejectModal || isEditModal || isUploadModal) {
                        window.location.href = window.location.pathname;
                    }
                }
            });
        </script>
    </x-slot:scripts>
</x-layouts.app>