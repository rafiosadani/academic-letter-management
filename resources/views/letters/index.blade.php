<x-layouts.app title="Pengajuan Surat Saya">
    <x-ui.breadcrumb
            title="Pengajuan Surat"
            :items="[
                ['label' => 'Surat Saya', 'url' => route('letters.index')],
                ['label' => 'Pengajuan Surat Saya']
            ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <x-ui.table-header
                    title="Pengajuan Surat Saya"
                    description="Daftar semua pengajuan surat yang telah Anda ajukan"
                    search-placeholder="Search pengajuan surat..."
                    :create-route="route('letters.create')"
                    create-text="Ajukan Surat Baru"
            />

            {{-- Filters --}}
            <div class="card mt-3 p-4">
                <form method="GET" action="{{ route('letters.index') }}" class="grid grid-cols-1 gap-2 sm:gap-4 sm:grid-cols-3">
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
                    <div>
                        <label class="block">
                            <span class="text-xs-plus font-medium text-slate-600 dark:text-navy-100">Status</span>
                            <select name="status"
                                    class="form-select mt-1 h-8 w-full rounded-lg border border-slate-300 bg-white px-2.5 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent text-xs">
                                <option value="">Semua Status</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                                <option value="external_processing" {{ request('status') === 'external_processing' ? 'selected' : '' }}>Proses Eksternal</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                <option value="resubmitted" {{ request('status') === 'resubmitted' ? 'selected' : '' }}>Diajukan Ulang</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex self-end items-center justify-between sm:justify-start space-x-2">
                        <button type="submit"
                                class="btn w-full sm:w-fit h-8 bg-primary text-xs-plus text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                            <i class="fa-solid fa-filter mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('letters.index') }}"
                           class="btn h-8 bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="card mt-3 p-4">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto border-1 border-slate-200 dark:border-navy-500 rounded-lg">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                        <tr class="text-xs">
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                No
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                Jenis Surat
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                Tanggal Ajukan
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                Status
                            </th>
                            <th class="whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                Aksi
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($letters as $index => $letter)
                            <tr class="text-xs border-y border-transparent {{ !$loop->last ? 'border-b-slate-200 dark:border-b-navy-500' : '' }}">
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center w-8">
                                    {{ $letters->firstItem() + $index }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                            <i class="fa-solid fa-file-lines text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $letter->letter_type->label() }}
                                            </p>
                                            <p class="text-tiny-plus text-slate-400 dark:text-navy-300">
                                                {{ $letter?->semester?->semester_type }} - {{ $letter?->academicYear?->year_label }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex flex-col">
                                        <span class="text-slate-700 dark:text-navy-100">{{ $letter->created_at_formatted }}</span>
                                        <span class="text-tiny-plus text-slate-400 dark:text-navy-300">{{ $letter->created_at_time }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <span class="badge bg-{{ $letter->status_badge }}/10 text-{{ $letter->status_badge }} text-tiny border border-{{ $letter->status_badge }} inline-flex items-center space-x-1.5 dark:bg-{{ $letter->status_badge }}/15">
                                        <i class="{{ $letter->status_icon }} {{ in_array($letter->status, ['in_progress','external_processing']) ? 'animate-spin' : '' }}"></i>
                                        <span>{{ $letter->status_label }}</span>
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        {{-- View Detail --}}
                                        <a href="{{ route('letters.show', $letter) }}"
                                           class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25"
                                           title="Detail">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        {{-- Edit (if editable) --}}
                                        @if($letter->canBeEditedByStudent())
                                            <a href="{{ route('letters.edit', $letter) }}"
                                               class="btn size-8 p-0 text-warning hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
                                               title="Edit">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                        @endif

                                        {{-- Delete --}}
                                        @can('delete', $letter)
                                            <button
                                                    type="button"
                                                    data-toggle="modal"
                                                    data-target="#delete-letter-modal-{{ $letter->id }}"
                                                    class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                    title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endcan

                                        @if($letter->status === 'completed')
                                            <a href="{{ route('letters.download-pdf', $letter) }}"
                                               class="btn size-8 p-0 text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25 dark:bg-success/15"
                                               title="Unduh PDF">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td colspan="5" class="whitespace-nowrap px-4 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-inbox text-4xl text-slate-300 dark:text-navy-400"></i>
                                        <p class="mt-3 text-xs-plus text-slate-400 dark:text-navy-300">
                                            Belum ada pengajuan surat
                                        </p>
                                        <a href="{{ route('letters.create') }}"
                                           class="btn mt-3 bg-primary text-xs-plus text-white hover:bg-primary-focus">
                                            <i class="fa-solid fa-plus mr-2"></i>
                                            Ajukan Surat Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($letters->hasPages())
                    <div class="flex justify-center pt-3">
                        {{ $letters->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Modals --}}
    @foreach($letters as $letter)
        @can('delete', $letter)
            @include('letters.modals._delete', ['letter' => $letter])
        @endcan
    @endforeach
</x-layouts.app>