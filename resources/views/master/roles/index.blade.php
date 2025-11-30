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
{{--            <div class="card p-4">--}}
{{--                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:items-center sm:justify-between w-full">--}}
{{--                    <div>--}}
{{--                        <h2 class="text-sm sm:text-base font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100">--}}
{{--                            {{ request()->has('view_deleted') ? 'Data Role - Deleted Records' : 'Data Role' }}--}}
{{--                        </h2>--}}
{{--                        <p class="text-tiny-plus sm:text-xs+">--}}
{{--                            {{ request()->has('view_deleted')--}}
{{--                                ? 'Kelola role yang telah dihapus.'--}}
{{--                                : 'Kelola peran pengguna dan pengaturan akses pada sistem.'--}}
{{--                            }}--}}
{{--                        </p>--}}
{{--                    </div>--}}
{{--                    <div class="flex flex-col space-y-3 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-2">--}}
{{--                        <!-- Search Form -->--}}
{{--                        <form method="GET" class="w-full sm:w-80">--}}
{{--                            @if(request()->has('view_deleted'))--}}
{{--                                <input type="hidden" name="view_deleted" value="1">--}}
{{--                            @endif--}}
{{--                            <label class="relative flex">--}}
{{--                                <input--}}
{{--                                        name="search"--}}
{{--                                        value="{{ request('search') }}"--}}
{{--                                        class="form-input peer h-8 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 text-tiny sm:text-xs placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"--}}
{{--                                        placeholder="Search roles..."--}}
{{--                                        type="text"--}}
{{--                                />--}}
{{--                                <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 transition-colors duration-200" fill="currentColor" viewBox="0 0 24 24">--}}
{{--                                        <path d="M3.316 13.781l.73-.171-.73.171zm0-5.457l.73.171-.73-.171zm15.473 0l.73-.171-.73.171zm0 5.457l.73.171-.73-.171zm-5.008 5.008l-.171-.73.171.73zm-5.457 0l-.171.73.171-.73zm0-15.473l-.171-.73.171.73zm5.457 0l.171-.73-.171.73zM20.47 21.53a.75.75 0 101.06-1.06l-1.06 1.06zM4.046 13.61a11.198 11.198 0 010-5.115l-1.46-.342a12.698 12.698 0 000 5.8l1.46-.343zm14.013-5.115a11.196 11.196 0 010 5.115l1.46.342a12.698 12.698 0 000-5.8l-1.46.343zm-4.45 9.564a11.196 11.196 0 01-5.114 0l-.342 1.46c1.907.448 3.892.448 5.8 0l-.343-1.46zM8.496 4.046a11.198 11.198 0 015.115 0l.342-1.46a12.698 12.698 0 00-5.8 0l.343 1.46zm0 14.013a5.97 5.97 0 01-4.45-4.45l-1.46.343a7.47 7.47 0 005.568 5.568l.342-1.46zm5.457 1.46a7.47 7.47 0 005.568-5.567l-1.46-.342a5.97 5.97 0 01-4.45 4.45l.342 1.46zM13.61 4.046a5.97 5.97 0 014.45 4.45l1.46-.343a7.47 7.47 0 00-5.568-5.567l-.342 1.46zm-5.457-1.46a7.47 7.47 0 00-5.567 5.567l1.46.342a5.97 5.97 0 014.45-4.45l-.343-1.46zm8.652 15.28l3.665 3.664 1.06-1.06-3.665-3.665-1.06 1.06z"/>--}}
{{--                                    </svg>--}}
{{--                                </span>--}}
{{--                            </label>--}}
{{--                        </form>--}}

{{--                        --}}{{--                        <form method="GET">--}}
{{--                        --}}{{--                            <div class="table-search-wrapper flex items-center">--}}
{{--                        --}}{{--                                <label class="block">--}}
{{--                        --}}{{--                                    <input--}}
{{--                        --}}{{--                                            name="search"--}}
{{--                        --}}{{--                                            value="{{ request('search') }}"--}}
{{--                        --}}{{--                                            class="table-search-input form-input {{ request('search') ? 'w-40' : 'w-0' }} bg-transparent px-1 text-right transition-all duration-100 placeholder:text-slate-500 dark:placeholder:text-navy-200"--}}
{{--                        --}}{{--                                            placeholder="Search here..."--}}
{{--                        --}}{{--                                            type="text"--}}
{{--                        --}}{{--                                            onkeydown="if(event.key === 'Enter'){ this.form.submit(); }"--}}
{{--                        --}}{{--                                    />--}}
{{--                        --}}{{--                                </label>--}}
{{--                        --}}{{--                                <button--}}
{{--                        --}}{{--                                        type="button"--}}
{{--                        --}}{{--                                        class="table-search-toggle btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"--}}
{{--                        --}}{{--                                >--}}
{{--                        --}}{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                        --}}{{--                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />--}}
{{--                        --}}{{--                                    </svg>--}}
{{--                        --}}{{--                                </button>--}}
{{--                        --}}{{--                            </div>--}}
{{--                        --}}{{--                        </form>--}}
{{--                        <!-- Action Buttons -->--}}
{{--                        @if(request()->has('view_deleted'))--}}
{{--                            <a href="{{ route('master.roles.index') }}"--}}
{{--                               class="btn w-full sm:w-auto justify-center bg-slate-500 font-normal text-white hover:bg-slate-600 focus:bg-slate-600 active:bg-slate-600/90">--}}
{{--                                <i class="fa-solid fa-list mr-2 text-tiny sm:text-xs"></i>--}}
{{--                                <span class="text-tiny sm:text-xs">Lihat Semua</span>--}}
{{--                            </a>--}}
{{--                            @if($roles->total() > 0)--}}
{{--                                <button type="button"--}}
{{--                                    data-toggle="modal"--}}
{{--                                    data-target="#restore-all-roles-modal"--}}
{{--                                    class="btn w-full sm:w-auto justify-center bg-success font-normal text-white hover:bg-success/90 focus:bg-success/90 active:bg-success/80">--}}
{{--                                        <i class="fa-solid fa-undo mr-2 text-tiny sm:text-xs"></i>--}}
{{--                                        <span class="text-tiny sm:text-xs">Restore All</span>--}}
{{--                                </button>--}}
{{--                            @endif--}}
{{--                        @else--}}
{{--                            <a href="{{ route('master.roles.index', ['view_deleted' => 1]) }}"--}}
{{--                               class="btn w-full sm:w-auto justify-center bg-slate-500 font-normal text-white hover:bg-slate-600 focus:bg-slate-600 active:bg-slate-600/90">--}}
{{--                                <i class="fa-solid fa-trash-alt mr-2 text-tiny sm:text-xs"></i>--}}
{{--                                <span class="text-tiny sm:text-xs">Deleted Records</span>--}}
{{--                            </a>--}}
{{--                            <a href="{{ route('master.roles.create') }}"--}}
{{--                               class="btn w-full sm:w-auto justify-center bg-primary font-normal text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">--}}
{{--                                <i class="fa-solid fa-plus mr-2 text-tiny sm:text-xs"></i>--}}
{{--                                <span class="text-tiny sm:text-xs">Tambah Role</span>--}}
{{--                            </a>--}}
{{--                        @endif--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

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
                                        <div>
                                            <div class="avatar size-8 shadow rounded-md">
                                                <img
                                                    class="mask is-squircle"
                                                    src="{{ asset('assets/images/default.png') }}"
                                                    alt="avatar-{{ $role->create_by_name }}"
                                                />
                                            </div>
                                        </div>
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
                                {{--                                <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-600 dark:text-navy-100 sm:px-5 text-center">--}}
                                {{--                                    <div class="flex gap-1 items-center justify-center">--}}
                                {{--                                        <a href="{{ route('master.roles.show', $role) }}"--}}
                                {{--                                            class="badge bg-info text-xs text-white shadow-primary/50 dark:bg-info dark:shadow-accent/50 transition-all duration-200 hover:bg-info/90 hover:shadow-lg focus:scale-105 active:scale-95">--}}
                                {{--                                            <i class="fa-solid fa-eye mr-1 fa-sm"></i> Detail--}}
                                {{--                                        </a>--}}
                                {{--                                        --}}
                                {{--                                        @if($role->is_editable)--}}
                                {{--                                            |--}}
                                {{--                                            <a href="{{ route('master.roles.edit', $role) }}"--}}
                                {{--                                               class="badge bg-warning text-xs text-white shadow-primary/50 dark:bg-warning dark:shadow-accent/50 transition-all duration-200 hover:bg-warning/90 hover:shadow-lg focus:scale-105 active:scale-95">--}}
                                {{--                                                <i class="fa-solid fa-pen-to-square mr-1 fa-sm"></i> Edit--}}
                                {{--                                            </a>--}}
                                {{--                                        @endif--}}

                                {{--                                        @if($role->is_deletable)--}}
                                {{--                                        |--}}
                                {{--                                        <button--}}
                                {{--                                            type="button"--}}
                                {{--                                            data-toggle="modal"--}}
                                {{--                                            data-target="#delete-role-modal-{{ $role->id }}"--}}
                                {{--                                            class="badge bg-error text-xs text-white shadow-primary/50 dark:bg-error dark:shadow-accent/50 transition-all duration-200 hover:bg-error/90 hover:shadow-lg focus:scale-105 active:scale-95 cursor-pointer">--}}
                                {{--                                            <i class="fa-solid fa-trash mr-1 fa-sm"></i> Hapus--}}
                                {{--                                        </button>--}}
                                {{--                                        @endif--}}
                                {{--                                    </div>--}}
                                {{--                                </td>--}}
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