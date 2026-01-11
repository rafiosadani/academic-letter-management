<x-layouts.app title="Master Data Program Studi">
    <x-ui.breadcrumb
            title="Data Program Studi"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Program Studi']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <x-ui.table-header
                    title="Data Program Studi"
                    description="Kelola program studi di fakultas"
                    search-placeholder="Cari program studi..."
                    :is-deleted-view="request()->has('view_deleted')"
                    :create-route="route('master.study-programs.create')"
                    create-text="Tambah Program Studi"
                    :index-route="route('master.study-programs.index')"
                    :deleted-count="$studyPrograms->total()"
                    restore-all-modal-id="restore-all-study-programs-modal"
            />

            <div class="card mt-3 p-4">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto border-1 border-slate-200 dark:border-navy-500 rounded-lg">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                        <tr class="text-xs">
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">No</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Kode Program Studi</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Nama Program Studi</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Jenjang</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                {{ request()->has('view_deleted') ? 'Dihapus Oleh' : 'Dibuat Oleh' }}
                            </th>
                            <th class="whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($studyPrograms as $studyProgram)
                            <tr class="text-xs border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    {{ $loop->iteration + ($studyPrograms->currentPage() - 1) * $studyPrograms->perPage() }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span class="text-slate-700 dark:text-navy-100">{{ $studyProgram->code }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span class="text-slate-600 dark:text-navy-100">{{ $studyProgram->name }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-tiny text-center">
{{--                                    {!! $studyProgram->degree_badge !!}--}}
                                    <span class="badge {{ $studyProgram->degree_badge_color }}">{{ $studyProgram->degree }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-xs leading-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col">
                                            <span class="text-slate-700 dark:text-navy-100">
                                                {{ request()->has('view_deleted') ? $studyProgram->deleted_by_name : $studyProgram->created_by_name }}
                                            </span>
                                            <span class="text-slate-700 dark:text-navy-100">
                                                {{ request()->has('view_deleted') ? $studyProgram->deleted_at_formatted : $studyProgram->created_at_formatted }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        @if(request()->has('view_deleted'))
                                            <!-- Restore Button -->
                                            <button type="button"
                                                    data-toggle="modal"
                                                    data-target="#restore-study-program-modal-{{ $studyProgram->id }}"
                                                    class="btn size-8 p-0 text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
                                                    title="Restore">
                                                <i class="fa-solid fa-undo"></i>
                                            </button>

                                            <!-- Force Delete Button -->
                                            <button type="button"
                                                    data-toggle="modal"
                                                    data-target="#force-delete-study-program-modal-{{ $studyProgram->id }}"
                                                    class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                    title="Hapus Permanen">
                                                <i class="fa-solid fa-trash-alt"></i>
                                            </button>
                                        @else
                                            {{-- Detail Button --}}
                                            <a href="{{ route('master.study-programs.show', $studyProgram) }}"
                                               class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25"
                                               title="Detail">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            {{-- Edit Button --}}
                                            <a href="{{ route('master.study-programs.edit', $studyProgram) }}"
                                               class="btn size-8 p-0 text-warning hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
                                               title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            {{-- Delete Button --}}
                                            <button
                                                    type="button"
                                                    data-toggle="modal"
                                                    data-target="#delete-study-program-modal-{{ $studyProgram->id }}"
                                                    class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                    title="Hapus"
                                            >
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td colspan="6"
                                    class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <span class="text-xs-plus">
                                        {{ request()->has('view_deleted')
                                            ? 'Tidak ada program studi yang dihapus.'
                                            : 'Data tidak ditemukan.'
                                        }}
                                    </span>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($studyPrograms->hasPages())
                    <div class="flex justify-center pt-3">
                        {{ $studyPrograms->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Section --}}
    @if(request()->has('view_deleted'))
        {{-- Modal Restore All --}}
        @if($studyPrograms->total() > 0)
            @include('master.study-programs.modals._restore-all')
        @endif

        {{-- Modal Restore Single & Force Delete per Study Program --}}
        @foreach($studyPrograms as $studyProgram)
            @include('master.study-programs.modals._restore', ['studyProgram' => $studyProgram])
            @include('master.study-programs.modals._force-delete', ['studyProgram' => $studyProgram])
        @endforeach
    @else
        {{-- Modal Delete (Soft Delete) per Study Program --}}
        @foreach($studyPrograms as $studyProgram)
            @include('master.study-programs.modals._delete', ['studyProgram' => $studyProgram])
        @endforeach
    @endif
</x-layouts.app>