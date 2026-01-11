<x-layouts.app title="Detail Alur Persetujuan">
    <x-ui.breadcrumb
            title="Detail Alur"
            :items="[
            ['label' => 'Pengaturan'],
            ['label' => 'Alur Persetujuan', 'url' => route('settings.approval-flows.index')],
            ['label' => $approvalFlow->letter_type->shortLabel()]
        ]"
    />

    <x-ui.page-header
            title="Alur Persetujuan: {{ $approvalFlow->letter_type->label() }}"
            description="Timeline lengkap proses persetujuan surat"
            :backUrl="route('settings.approval-flows.index')"
    >
        <x-slot:icon>
            <div class="flex size-10 items-center justify-center rounded-lg bg-{{ $approvalFlow->letter_type->color() }}/10 text-{{ $approvalFlow->letter_type->color() }}">
                <i class="fa-solid {{ $approvalFlow->letter_type->icon() }} text-xl"></i>
            </div>
        </x-slot:icon>

        <x-slot:actions>
            @can('create', App\Models\ApprovalFlow::class)
                <a href="{{ route('settings.approval-flows.create', ['letter_type' => $approvalFlow->letter_type->value]) }}"
                   class="btn space-x-2 bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Step</span>
                </a>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
        {{-- Main Column - Timeline --}}
        <div class="col-span-12 lg:col-span-8">
            {{-- Timeline Card --}}
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-route"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Timeline Alur Persetujuan
                            </h4>
                        </div>
                        <span class="badge bg-slate-100 text-slate-600 dark:bg-navy-600 dark:text-navy-100">
                            {{ $allSteps->count() }} Step{{ $allSteps->count() > 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    @if($allSteps->isEmpty())
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <i class="fa-solid fa-inbox text-4xl text-slate-300 dark:text-navy-400 mb-3"></i>
                            <p class="text-slate-600 dark:text-navy-200">
                                Belum ada step untuk jenis surat ini
                            </p>
                            @can('create', App\Models\ApprovalFlow::class)
                                <a href="{{ route('settings.approval-flows.create', ['letter_type' => $approvalFlow->letter_type->value]) }}"
                                   class="btn mt-3 space-x-2 bg-primary font-medium text-white">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Step Pertama</span>
                                </a>
                            @endcan
                        </div>
                    @else
                        {{-- Lineone Timeline --}}
                        <ol class="timeline [--size:2rem]">
                            @foreach($allSteps as $step)
                                <li class="timeline-item">
                                    {{-- Timeline Point --}}
                                    <div class="timeline-item-point rounded-full border-1 border-current bg-white text-{{ $step->letter_type->color() }} dark:bg-navy-700">
                                        <span class="text-sm">{{ $step->step }}</span>
                                    </div>

                                    {{-- Timeline Content --}}
                                    <div class="timeline-item-content flex-1 ml-2 p-4 rounded-lg bg-{{ $step->letter_type->color() }}/5 border border-slate-200 dark:border-navy-500">

                                        {{-- Header: Title + Actions --}}
                                        <div class="flex flex-col justify-between pb-2 sm:flex-row sm:pb-0 sm:items-center gap-2">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <p class="text-base font-medium leading-none text-{{$step->letter_type->color() }}">
                                                        {{ $step->step_label }}
                                                    </p>
                                                    @if($step->is_final)
                                                        <span class="badge bg-success/10 text-success text-tiny">
                                                            <i class="fa-solid fa-flag-checkered mr-1"></i>
                                                            Final
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Action Buttons --}}
                                            <div class="flex items-center space-x-2">
                                                @can('update', $step)
                                                    <a href="{{ route('settings.approval-flows.edit', $step) }}"
                                                       class="btn size-6 p-0 hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
                                                       title="Edit Step">
                                                        <i class="fa-solid fa-pen text-warning"></i>
                                                    </a>
                                                @endcan

                                                @can('delete', $step)
                                                    <button type="button"
                                                            data-toggle="modal"
                                                            data-target="#delete-approval-flow-modal-{{ $step->id }}"
                                                            class="btn size-6 p-0 hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                            title="Delete Step">
                                                        <i class="fa-solid fa-trash-alt text-error"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>

                                        {{-- Details --}}
                                        <div class="mt-2 space-y-2">
                                            {{-- Positions --}}
                                            <div class="flex items-start space-x-2">
                                                <i class="fa-solid fa-users text-xs text-slate-500 dark:text-navy-300 mt-1.5"></i>
                                                <div class="flex-1">
                                                    <span class="text-xs text-slate-500 dark:text-navy-300">Jabatan:</span>
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($step->required_positions as $position)
                                                            @php
                                                                $posEnum = App\Enums\OfficialPosition::from($position);
                                                            @endphp
                                                            <span class="badge bg-{{ $posEnum->color() }}/10 text-{{ $posEnum->color() }} text-tiny">
                                                                <i class="fa-solid text-tiny {{ $posEnum->icon() }} mr-1"></i>
                                                                {{ $posEnum->label() }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Current Pejabat --}}
                                            @php
                                                $allPejabat = $step->allCurrentPejabat();
                                            @endphp
                                            @if($allPejabat->isNotEmpty())
                                                <div class="flex items-start space-x-2">
                                                    <i class="fa-solid fa-user-check text-xs text-success mt-1.5"></i>
                                                    <div class="flex-1">
                                                        <span class="text-xs text-slate-500 dark:text-navy-300">Pejabat Aktif:</span>
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1">
                                                            @foreach($allPejabat as $pejabat)
                                                                <div class="flex items-center space-x-2">
                                                                    <div class="avatar size-6">
                                                                        @if($pejabat->position->isDynamic())
                                                                            <img class="rounded-full" src="{{ asset('assets/images/default.png') }}" alt="avatar">
                                                                        @else
                                                                            <img class="rounded-full" src="{{ $pejabat->user->profile->photo_url }}" alt="avatar">
                                                                        @endif
                                                                    </div>
                                                                    <span class="text-xs text-slate-600 dark:text-navy-100 line-clamp-1">
                                                                        @if($pejabat->position->isDynamic())
                                                                            Ketua Program Studi
                                                                        @else
                                                                            {{ $pejabat->user->profile->full_name ?? $pejabat->user->email }}
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex items-center space-x-2">
                                                    <i class="fa-solid fa-user-slash text-slate-400"></i>
                                                    <span class="text-xs text-slate-400 dark:text-navy-300">
                                                        Belum ada pejabat aktif
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- Permissions --}}
                                            @if($step->can_edit_content || $step->is_editable)
                                                <div class="flex items-start space-x-2 mt-3 pt-2 border-t border-slate-200 dark:border-navy-500">
                                                    <i class="fa-solid fa-gear text-xs text-accent mt-1.5"></i>
                                                    <div class="flex-1">
                                                        <span class="text-xs text-slate-500 dark:text-navy-300">Pengaturan Konten:</span>
                                                        <div class="flex flex-wrap gap-2 mt-1">
                                                            @if($step->can_edit_content)
                                                                <span class="badge bg-info/10 text-info text-tiny">
                                                                    <i class="fa-solid fa-edit mr-1"></i>
                                                                    Boleh Edit Konten
                                                                </span>
                                                            @endif
                                                            @if($step->is_editable)
                                                                <span class="badge bg-warning/10 text-warning text-tiny">
                                                                    <i class="fa-solid fa-user-edit mr-1"></i>
                                                                    Mahasiswa Boleh Edit
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- On Reject --}}
                                            <div class="flex items-start space-x-2 mt-2 pt-2 border-t border-slate-200 dark:border-navy-500">
                                                <i class="fa-solid text-xs {{ $step->on_reject->icon() }} text-{{ $step->on_reject->color() }} mt-1"></i>
                                                <div class="flex-1 pl-1">
                                                    <span class="text-xs text-slate-500 dark:text-navy-300">Aksi Saat Ditolak:</span>
                                                    <p class="text-xs text-slate-600 dark:text-navy-100">
                                                        {{ $step->on_reject->label() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column - Info Cards --}}
        <div class="col-span-12 lg:col-span-4 space-y-5">
            {{-- Summary Card --}}
            <div class="card">
                <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa-solid fa-chart-pie text-primary dark:text-accent"></i>
                        </div>
                        <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            Ringkasan
                        </h4>
                    </div>
                </div>

                <div class="space-y-2 p-4 sm:p-4">
                    {{-- Total Steps --}}
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-navy-100">Total Step:</span>
                        <span class="font-medium text-base text-slate-700 dark:text-navy-100">{{ $allSteps->count() }}</span>
                    </div>

                    {{-- Has Final --}}
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-navy-100">Status:</span>
                        @if($allSteps->where('is_final', true)->isNotEmpty())
                            <span class="badge bg-success/10 text-success">
                                <i class="fa-solid fa-check-circle mr-1"></i>
                                Complete
                            </span>
                        @else
                            <span class="badge bg-warning/10 text-warning">
                                <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                Incomplete
                            </span>
                        @endif
                    </div>

                    {{-- Output Format --}}
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-navy-100">Format Output:</span>
                        <span class="badge  bg-{{ $approvalFlow->letter_type->colorOutputFormat() }}/10 text-{{$approvalFlow->letter_type->colorOutputFormat()}} border border-{{$approvalFlow->letter_type->colorOutputFormat()}}/20">
                            <i class="fa-solid {{ $approvalFlow->letter_type->icon() }} mr-1"></i>
                            {{ strtoupper($approvalFlow->letter_type->outputFormat()) }}
                        </span>
                    </div>

                    {{-- External System --}}
                    @if($approvalFlow->letter_type->isExternal())
                        <div class="mt-3 p-2 rounded-lg bg-info/10 border border-info/20">
                            <p class="text-xs text-info flex items-start space-x-1">
                                <i class="fa-solid fa-info-circle mt-0.5"></i>
                                <span>Menggunakan sistem eksternal universitas</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Card --}}
            <div class="card">
                <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa-solid fa-circle-info text-info"></i>
                        </div>
                        <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            Informasi
                        </h4>
                    </div>
                </div>

                <div class="space-y-3 p-4 text-xs text-slate-600 dark:text-navy-200">
                    <div class="flex items-start space-x-2">
                        <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                        <p>Step diproses secara berurutan dari step 1 hingga final</p>
                    </div>
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
                        <p>Klik <strong>Edit</strong> untuk mengubah konfigurasi step</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                        <p>Hapus step akan otomatis reorder step lainnya</p>
                    </div>
                </div>
            </div>

            {{-- Timestamps --}}
            <div class="card">
                <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa-solid fa-clock text-slate-500"></i>
                        </div>
                        <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            Data Sistem
                        </h4>
                    </div>
                </div>
                <div class="p-4 space-y-2 text-xs text-slate-500 dark:text-navy-300">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-calendar-plus"></i>
                        <span>Dibuat: {{ $approvalFlow->created_at_formatted }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-clock"></i>
                        <span>Update: {{ $approvalFlow->updated_at_formatted }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modals for Each Step --}}
    @foreach($allSteps as $step)
        @include('settings.approval-flows.modals._delete', ['flow' => $step])
    @endforeach
</x-layouts.app>