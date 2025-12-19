<x-layouts.app title="Penugasan Jabatan">
    <x-ui.breadcrumb
            title="Penugasan Jabatan"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Penugasan Jabatan']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <x-ui.table-header
                    title="Penugasan Jabatan"
                    description="Kelola masa jabatan pejabat fakultas"
                    search-placeholder="Cari nama pejabat..."
                    :is-deleted-view="request()->has('view_deleted')"
                    :create-route="route('master.faculty-officials.create')"
                    create-text="Tambah Penugasan"
                    :index-route="route('master.faculty-officials.index')"
                    :deleted-count="$facultyOfficials->total()"
                    restore-all-modal-id="restore-all-faculty-officials-modal"
            />

            {{-- Filters --}}
            <div class="card mt-3 p-4">
                <form method="GET" action="{{ route('master.faculty-officials.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @if(request()->has('view_deleted'))
                        <input type="hidden" name="view_deleted" value="1">
                    @endif
                    {{-- Position Filter --}}
                    <div>
                        <label class="block">
                            <span class="text-xs-plus font-medium text-slate-600 dark:text-navy-100">Jabatan</span>
                            <select name="position"
                                    class="form-select mt-1 h-8 w-full rounded-lg border border-slate-300 bg-white px-2.5 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent text-xs">
                                <option value="">Semua Jabatan</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->value }}" {{ request('position') == $pos->value ? 'selected' : '' }}>
                                        {{ $pos->label() }}
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
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>Berakhir</option>
                            </select>
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                                class="btn h-8 bg-primary text-xs-plus text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">
                            <i class="fa-solid fa-filter mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('master.faculty-officials.index', request()->has('view_deleted') ? ['view_deleted' => 1] : []) }}"
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
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Pejabat</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Jabatan</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Periode</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Status</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                {{ request()->has('view_deleted') ? 'Dihapus Oleh' : 'Dibuat Oleh' }}
                            </th>
                            <th class="whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($facultyOfficials as $official)
                            <tr class="text-xs border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar size-11">
                                            <img class="rounded-full"
                                                 src="{{ $official->user->profile->photo_url ?? asset('images/default-avatar.png') }}"
                                                 alt="avatar">
                                        </div>
                                        <div>
                                            <p class="text-slate-700 dark:text-navy-100">
                                                {{ $official->user->profile->full_name ?? $official->user->email }}
                                            </p>
                                            <p class="text-tiny-plus text-slate-400 dark:text-navy-300">
                                                {{ $official->user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex flex-col items-start gap-2">
                                        <div class="flex items-center space-x-2">
                                            <i class="fa-solid {{ $official->position->icon() }} text-{{ $official->position->color() }}"></i>
                                            <span class="text-slate-600 dark:text-navy-100">{{ $official->position->label() }}</span>
                                        </div>
                                        @if($official->studyProgram)
                                            <span class="badge {{ $official->studyProgram->degree_badge_color }} text-tiny">
                                                {{ $official->studyProgram->degree_name }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-xs leading-4">
                                    <div class="flex flex-col">
                                        <span class="text-slate-700 dark:text-navy-100">{{ $official->start_date->format('d M Y') }}</span>
                                        <span class="text-slate-400">s/d {{ $official->end_date ? $official->end_date->format('d M Y') : 'Sekarang' }}</span>
                                    </div>
{{--                                    <span class="text-slate-700 dark:text-navy-100">{{ $official->period }}</span>--}}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    {!! $official?->status_badge !!}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-xs leading-4">
                                    <div class="flex flex-col">
                                            <span class="text-slate-700 dark:text-navy-100">
                                                {{ request()->has('view_deleted') ? $official->deleted_by_name : $official->created_by_name }}
                                            </span>
                                        <span class="text-slate-400">
                                                {{ request()->has('view_deleted') ? $official->deleted_at_formatted : $official->created_at_formatted }}
                                            </span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        @if(request()->has('view_deleted'))
                                            {{-- Restore Button --}}
                                            <button type="button"
                                                    data-toggle="modal"
                                                    data-target="#restore-faculty-official-modal-{{ $official->id }}"
                                                    class="btn size-8 p-0 text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
                                                    title="Restore">
                                                <i class="fa-solid fa-undo"></i>
                                            </button>

                                            {{-- Force Delete Button --}}
                                            <button type="button"
                                                    data-toggle="modal"
                                                    data-target="#force-delete-faculty-official-modal-{{ $official->id }}"
                                                    class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                    title="Hapus Permanen">
                                                <i class="fa-solid fa-trash-alt"></i>
                                            </button>
                                        @else
                                            {{-- Detail Button --}}
                                            <a href="{{ route('master.faculty-officials.show', $official) }}"
                                               class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25"
                                               title="Detail">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            {{-- Edit Button --}}
                                            <a href="{{ route('master.faculty-officials.edit', $official) }}"
                                               class="btn size-8 p-0 text-warning hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
                                               title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            {{-- Delete Button --}}
                                            <button type="button"
                                                    data-toggle="modal"
                                                    data-target="#delete-faculty-official-modal-{{ $official->id }}"
                                                    class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                    title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td colspan="6" class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                        <span class="text-xs-plus">
                                            {{ request()->has('view_deleted')
                                                ? 'Tidak ada penugasan jabatan yang dihapus.'
                                                : 'Data tidak ditemukan.'
                                            }}
                                        </span>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($facultyOfficials->hasPages())
                    <div class="flex justify-center pt-3">
                        {{ $facultyOfficials->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Section --}}
    @if(request()->has('view_deleted'))
        {{-- Modal Restore All --}}
        @if($facultyOfficials->total() > 0)
            @include('master.faculty-officials.modals._restore-all')
        @endif

        {{-- Modal Restore Single & Force Delete per Official --}}
        @foreach($facultyOfficials as $official)
            @include('master.faculty-officials.modals._restore', ['official' => $official])
            @include('master.faculty-officials.modals._force-delete', ['official' => $official])
        @endforeach
    @else
        {{-- Modal Delete (Soft Delete) per Official --}}
        @foreach($facultyOfficials as $official)
            @include('master.faculty-officials.modals._delete', ['official' => $official])
        @endforeach
    @endif
</x-layouts.app>