<x-layouts.app title="Dashboard Persetujuan">
    <x-ui.breadcrumb
            title="Dashboard Persetujuan"
            :items="[
                ['label' => 'Transaksi Surat', 'url' => route('approvals.index')],
                ['label' => 'Persetujuan Surat']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <x-ui.table-header
                    title="Dashboard Persetujuan Surat"
                    description="Kelola persetujuan pengajuan surat mahasiswa"
                    search-placeholder="Search persetujuan surat..."
            />

            {{-- Filters --}}
            <div class="card mt-3 p-4">
                <form method="GET" action="{{ route('approvals.index') }}" class="grid grid-cols-1 gap-2 sm:gap-4 sm:grid-cols-3">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    {{-- Letter Type Filter --}}
                    <div>
                        <label class="block">
                            <span class="text-xs-plus font-medium text-slate-600 dark:text-navy-100">Jenis Surat</span>
                            <select name="letter_type"
                                    class="form-select mt-1 h-8 w-full rounded-lg border border-slate-300 bg-white px-2.5 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent text-xs">
                                <option value="">Semua Jenis Surat</option>
                                @foreach($letterTypes as $type)
                                    <option value="{{ $type->value }}" {{ request('letter_type') == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    {{-- Status Filter --}}
{{--                    <div>--}}
{{--                        <label class="block">--}}
{{--                            <span class="text-xs-plus font-medium text-slate-600 dark:text-navy-100">Status</span>--}}
{{--                            <select name="status"--}}
{{--                                    class="form-select mt-1 h-8 w-full rounded-lg border border-slate-300 bg-white px-2.5 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent text-xs">--}}
{{--                                <option value="">Semua Status</option>--}}
{{--                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>--}}
{{--                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>--}}
{{--                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>--}}
{{--                                <option value="skipped" {{ request('status') === 'skipped' ? 'selected' : '' }}>Dilewati</option>--}}
{{--                                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasikan</option>--}}
{{--                            </select>--}}
{{--                        </label>--}}
{{--                    </div>--}}

                    {{-- Actions --}}
                    <div class="flex self-end items-center justify-between sm:justify-start space-x-2">
                        <button type="submit"
                                class="btn w-full sm:w-fit h-8 bg-primary text-xs-plus text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                            <i class="fa-solid fa-filter mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('approvals.index', ['tab' => $tab]) }}"
                           class="btn h-8 bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    </div>
                </form>
            </div>

            <div class="card mt-3 p-4">
                <div id="approval-tabs" class="tabs flex flex-col">
                    <div class="is-scrollbar-hidden overflow-x-auto">
                        <div class="tabs-list flex px-2 py-2 gap-2 border border-slate-200 dark:border-navy-500 rounded-lg bg-slate-100 text-slate-600 dark:bg-navy-800 dark:text-navy-200">
                            <a href="{{ route('approvals.index', ['tab' => 'pending']) }}"
                               class="tab btn shrink-0 space-x-2 px-3 py-2 font-medium text-xs ring-1 transition-all duration-300
                                    {{ $tab === 'pending'
                                        ? 'bg-warning/10 text-warning ring-warning/30 shadow-sm dark:bg-warning/15 dark:text-warning dark:ring-warning/40'
                                        : 'ring-slate-300 text-slate-600 hover:bg-white/70 hover:text-slate-800 dark:ring-navy-400 dark:text-navy-300 dark:hover:bg-navy-700/70 dark:hover:text-navy-100'
                                    }}
                               ">
                                <i class="fa-solid fa-clock"></i>
                                <span>Menunggu Saya</span>
                            </a>
                            <a href="{{ route('approvals.index', ['tab' => 'approved']) }}"
                               class="tab btn shrink-0 space-x-2 px-3 py-1.5 font-medium text-xs ring-1 transition-all duration-300
                                   {{ $tab === 'approved'
                                        ? 'bg-success/10 text-success ring-success/30 shadow-sm dark:bg-success/15 dark:text-success dark:ring-success/40'
                                        : 'ring-slate-300 text-slate-600 hover:bg-white/70 hover:text-slate-800 dark:ring-navy-400 dark:text-navy-300 dark:hover:bg-navy-700/70 dark:hover:text-navy-100'
                                   }}
                               ">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>Sudah Disetujui</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 p-4">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto border-1 border-slate-200 dark:border-navy-500 rounded-lg">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                        <tr class="text-xs">
                            <th class="whitespace-nowrap bg-slate-200 px-3 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-4 text-center">
                                No
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-3 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-4">
                                Mahasiswa
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-3 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-4">
                                Jenis Surat
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-3 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-4">
                                Step Saat Ini
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-3 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-4">
                                Tanggal Ajukan
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-3 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-4 text-center">
                                Status
                            </th>
                            <th class="whitespace-nowrap rounded-tr-lg bg-slate-200 px-3 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-4 text-center">
                                Aksi
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($approvals as $index => $approval)
                            @php
                                $letter = $approval->letterRequest;
                                $student = $letter->student;
                            @endphp
                            <tr class="border-y border-transparent {{ !$loop->last ? 'border-b-slate-200 dark:border-b-navy-500' : '' }} text-xs">
                                <td class="whitespace-nowrap px-3 py-3 lg:px-4 text-center">
                                    {{ $approvals->firstItem() + $index }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 lg:px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar size-10 shrink-0">
                                            <img
                                                    src="{{ $student->profile->photo_url ?? asset('images/default-avatar.png') }}"
                                                    alt="{{ $student->profile->full_name }}"
                                                    class="rounded-full object-cover"
                                            >
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $student->profile->full_name }}
                                            </p>
                                            <p class="text-tiny text-slate-400 dark:text-navy-300">
                                                {{ $student->profile->student_or_employee_id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 lg:px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex size-8 items-center justify-center rounded-lg bg-primary/10 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                            <i class="fa-solid fa-file-lines text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $letter->letter_type->label() }}
                                            </p>
                                            <p class="text-tiny text-slate-400 dark:text-navy-300">
                                                {{ $letter->semester->semester_type }} - {{ $letter->academicYear->year_label }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 lg:px-4">
                                    <span class="text-slate-700 dark:text-navy-100">
                                        {{ $approval->step_label }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 lg:px-4">
                                    <div class="flex flex-col">
                                        <span class="text-slate-700 dark:text-navy-100">{{ $letter->created_at_formatted }}</span>
                                        <span class="text-tiny text-slate-400 dark:text-navy-300">{{ $letter->created_at_time }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-center lg:px-4">
                                    <span class="inline-flex items-center gap-1.5
                                        badge
                                        bg-{{ $approval->status_badge }}/10
                                        text-{{ $approval->status_badge }}
                                        text-tiny
                                        border border-{{ $approval->status_badge }}
                                        dark:bg-{{ $approval->status_badge }}/15">

                                        <i class="{{ $approval->status_icon }}"></i>
                                        <span>{{ $approval->status_label }}</span>
                                    </span>
                                </td>


                                <td class="whitespace-nowrap px-3 py-3 text-center lg:px-4">
                                    <a href="{{ route('approvals.show', $approval) }}"
                                       class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25"
                                       title="Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-8 text-center lg:px-4">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-inbox text-4xl text-slate-300 dark:text-navy-400"></i>
                                        <p class="mt-3 text-xs text-slate-400 dark:text-navy-300">
                                            {{ $tab === 'pending' ? 'Tidak ada pengajuan yang menunggu persetujuan' : 'Belum ada pengajuan yang disetujui' }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($approvals->hasPages())
                    <div class="flex justify-center mt-4">
                        {{ $approvals->appends(['tab' => $tab])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>