<x-layouts.app title="Master Data Role">
    <x-ui.breadcrumb
            title="Data Role"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Role']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">

            <x-ui.table-header
                title="Data Role"
                description="Kelola peran pengguna dan pengaturan akses pada sistem."
                search-placeholder="Search roles..."
                :is-deleted-view="request()->has('view_deleted')"
                :create-route="route('master.roles.create')"
                create-text="Tambah Role"
                :index-route="route('master.roles.index')"
                :deleted-count="$roles->total()"
                restore-all-modal-id="restore-all-roles-modal"
            />

            <div class="card mt-3 p-4">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto border-1 border-slate-200 dark:border-navy-500 rounded-lg">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                        <tr class="text-xs">
                            <th class="whitespace-nowrap rounded-tl-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                No
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                Kode Role
                            </th>
                            <th style="width: 40%;"
                                class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                Nama Role
                            </th>
                            @if(!request()->has('view_deleted'))
                                <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                    Dapat Diubah
                                </th>
                                <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                    Dapat Dihapus
                                </th>
                            @endif
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                {{ request()->has('view_deleted') ? 'Dihapus Oleh' : 'Dibuat Oleh' }}
                            </th>
                            <th class="whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($roles as $role)
                            <tr class="text-xs border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    {{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span>{{ $role->code }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span>{{ $role->name }}</span>
                                </td>
                                @if(!request()->has('view_deleted'))
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                        @if($role->is_editable)
                                            <span class="badge bg-success/10 text-success dark:bg-success/15">Ya</span>
                                        @else
                                            <span class="badge bg-error/10 text-error dark:bg-error/15">Tidak</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                        @if($role->is_deletable)
                                            <span class="badge bg-success/10 text-success dark:bg-success/15">Ya</span>
                                        @else
                                            <span class="badge bg-error/10 text-error dark:bg-error/15">Tidak</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-xs leading-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-500">
                                                {{ request()->has('view_deleted') ? $role->deleted_by_name : $role->created_by_name }}
                                            </span>
                                            <span class="text-slate-500">
                                                {{ request()->has('view_deleted') ? $role->deleted_at_formatted : $role->created_at_formatted }}
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
                                                data-target="#restore-role-modal-{{ $role->id }}"
                                                class="btn size-8 p-0 text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
                                                title="Restore">
                                                    <i class="fa-solid fa-undo"></i>
                                            </button>

                                            <!-- Force Delete Button -->
                                            @if($role->is_deletable)
                                                <button type="button"
                                                    data-toggle="modal"
                                                    data-target="#force-delete-role-modal-{{ $role->id }}"
                                                    class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                    title="Hapus Permanen">
                                                        <i class="fa-solid fa-trash-alt"></i>
                                                </button>
                                            @endif
                                        @else
                                            {{-- Detail Button --}}
                                            <a href="{{ route('master.roles.show', $role) }}"
                                                class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25"
                                                title="Detail">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            {{-- Edit Button --}}
                                            @if($role->is_editable)
                                                <a href="{{ route('master.roles.edit', $role) }}"
                                                    class="btn size-8 p-0 text-warning hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
                                                    title="Edit">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                            @endif

                                            {{-- Delete Button --}}
                                            @if($role->is_deletable)
                                                <button
                                                    type="button"
                                                    data-toggle="modal"
                                                    data-target="#delete-role-modal-{{ $role->id }}"
                                                    class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                    title="Hapus">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td colspan="{{ request()->has('view_deleted') ? '5' : '7' }}"
                                    class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <span class="text-xs-plus">
                                        {{ request()->has('view_deleted')
                                            ? 'Tidak ada role yang dihapus.'
                                            : 'Data tidak ditemukan.'
                                        }}
                                    </span>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($roles->hasPages())
                    <div class="flex justify-center pt-3">
                        {{ $roles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Section --}}
    @if(request()->has('view_deleted'))
        {{-- Modal Restore All --}}
        @if($roles->total() > 0)
            @include('master.roles.modals._restore-all-form')
        @endif

        {{-- Modal Restore Single & Force Delete per Role --}}
        @foreach($roles as $role)
            @include('master.roles.modals._restore-form', ['role' => $role])

            @if($role->is_deletable)
                @include('master.roles.modals._force-delete-form', ['role' => $role])
            @endif
        @endforeach
    @else
        {{-- Modal Delete (Soft Delete) per Role --}}
        @foreach($roles as $role)
            @if($role->is_deletable)
                @include('master.roles.modals._delete-form', ['role' => $role])
            @endif
        @endforeach
    @endif

    <x-slot:scripts>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const btn = document.querySelector('.table-search-toggle');
                const input = document.querySelector('.table-search-input');

                btn.addEventListener('click', () => {
                    // Jika input open (width > 0), fokuskan dan pindah caret ke kanan
                    setTimeout(() => {
                        input.focus();
                        const length = input.value.length;
                        input.setSelectionRange(length, length); // caret ke kanan
                    }, 0); // delay sedikit karena ada transition
                });
            });
        </script>
    </x-slot:scripts>
</x-layouts.app>