{{-- Dashboard Page (Tanpa Sidebar Panel) --}}
<x-layouts.app
        title="Master Data Role">

    <x-ui.breadcrumb
            title="Data Role"
            :items="[
                ['label' => 'Master Data'],
                ['label' => 'Role']
            ]"
    />

    <div
            class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6"
    >
        <div class="col-span-12">
            <div class="">
                <div class="card p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100">
                                Data Role
                            </h2>
                            <p class="text-xs-plus">Kelola peran pengguna dan pengaturan akses pada sistem.</p>
                        </div>
                        <div class="flex space-x-4">
                            <div class="table-search-wrapper flex items-center">
                                <label class="block">
                                    <input
                                            class="table-search-input form-input w-0 bg-transparent px-1 text-right transition-all duration-100 placeholder:text-slate-500 dark:placeholder:text-navy-200"
                                            placeholder="Search here..."
                                            type="text"
                                    />
                                </label>
                                <button
                                        class="table-search-toggle btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                                >
                                    <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="size-4.5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                    >
                                        <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <button
                                    data-toggle="modal"
                                    data-target="#create-role-modal"
                                    class="btn space-x-2 bg-primary font-normal text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90"
                            >
                                <i class="fa-solid fa-plus"></i>
                                <span>Tambah Role</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card mt-3 p-4">
                    <div class="is-scrollbar-hidden min-w-full overflow-x-auto border-1 border-slate-200 dark:border-navy-500 rounded-lg">
                        <table class="is-hoverable w-full text-left">
                            <thead>
                            <tr>
                                <th class="whitespace-nowrap rounded-tl-lg bg-slate-200 px-4 py-3 font-semibold text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                    No
                                </th>
                                <th width="50%"
                                    class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                    Nama Role
                                </th>
                                <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                    Dibuat Oleh
                                </th>
                                {{--                                <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold  text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">--}}
                                {{--                                    Action--}}
                                {{--                                </th>--}}
                                <th class="whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-semibold  text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($roles->total() < 1)
                                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                    <td colspan="4" class="whitespace-nowrap px-4 py-3 sm:px-5">
                                        <p class="text-center">
                                            Data tidak ditemukan.
                                        </p>
                                    </td>
                                </tr>
                            @else
                                @foreach($roles as $role)
                                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                        <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                            {{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                            {{ $role->name }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-600 dark:text-navy-100 sm:px-5">
                                            Mon, 12 May - 09:00
                                        </td>
                                        {{--                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-600 dark:text-navy-100 sm:px-5 text-center">--}}
                                        {{--                                        <div class="flex gap-1 items-center">--}}
                                        {{--                                            <a href="#" class="badge badge- bg-primary text-xs text-white shadow-soft shadow-primary/50 dark:bg-accent dark:shadow-accent/50">--}}
                                        {{--                                                <i class="fa-solid fa-eye mr-1 fa-sm"></i> Detail--}}
                                        {{--                                            </a>--}}
                                        {{--                                            |--}}
                                        {{--                                            <a href="#" class="badge bg-warning text-xs text-white shadow-soft shadow-primary/50 dark:bg-accent dark:shadow-accent/50">--}}
                                        {{--                                                <i class="fa-solid fa-pen mr-1 fa-sm"></i> Edit--}}
                                        {{--                                            </a>--}}
                                        {{--                                            |--}}
                                        {{--                                            <a href="#" class="badge bg-error text-xs text-white shadow-soft shadow-primary/50 dark:bg-accent dark:shadow-accent/50">--}}
                                        {{--                                                <i class="fa-solid fa-trash mr-1 fa-sm"></i> Hapus--}}
                                        {{--                                            </a>--}}
                                        {{--                                        </div>--}}
                                        {{--                                    </td>--}}

                                        <td class="px-4 py-3 sm:px-5 text-center">
                                            <div class="role-action-menu inline-flex" id="role-menu-{{ $role->id }}">
                                                <button class="popper-ref btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                    <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="size-5"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                    >
                                                        <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"
                                                        />
                                                    </svg>
                                                </button>
                                                <div class="popper-root">
                                                    <div class="popper-box border rounded-md py-1 bg-white dark:bg-navy-700 border-slate-200 dark:border-navy-500">
                                                        <ul class="flex flex-col items-center text-xs-plus">
                                                            <li class="w-full">
                                                                <a href="#"
                                                                   onclick="editRole('{{ $role->id }}')"
                                                                   class="flex w-full h-8 items-center justify-center px-4
                                                                    font-medium tracking-wide outline-hidden transition-all
                                                                    hover:bg-slate-100 hover:text-slate-800
                                                                    focus:bg-slate-100 focus:text-slate-800
                                                                    dark:hover:bg-navy-600 dark:hover:text-navy-100
                                                                    dark:focus:bg-navy-600 dark:focus:text-navy-100">
                                                                    <i class="fa-solid fa-eye text-accent dark:text-accent-light mr-2"></i>
                                                                    <span>Detail Data</span>
                                                                </a>
                                                            </li>

                                                            <li class="w-full">
                                                                <button
                                                                        type="button"
                                                                        data-toggle="modal"
                                                                        data-target="#edit-role-modal-{{ $role->id }}"
                                                                        data-action="popper-modal-trigger"
                                                                        class="flex w-full h-8 items-center justify-center px-4
                                                                    font-medium tracking-wide outline-hidden transition-all
                                                                    hover:bg-slate-100 hover:text-slate-800
                                                                    focus:bg-slate-100 focus:text-slate-800
                                                                    dark:hover:bg-navy-600 dark:hover:text-navy-100
                                                                    dark:focus:bg-navy-600 dark:focus:text-navy-100">
                                                                    <i class="fa-solid fa-pen-to-square w-4 text-warning dark:text-warning/80 mr-2"></i>
                                                                    <span>Edit Data</span>
                                                                </button>
                                                            </li>

                                                            <li class="w-full">
                                                                <button
                                                                        type="button"
                                                                        data-toggle="modal"
                                                                        data-target="#delete-role-modal-{{ $role->id }}"
                                                                        data-action="popper-modal-trigger"
                                                                        class="flex w-full h-8 items-center justify-center px-4
                                                                    font-medium tracking-wide outline-hidden transition-all
                                                                    hover:bg-slate-100 hover:text-slate-800
                                                                    focus:bg-slate-100 focus:text-slate-800
                                                                    dark:hover:bg-navy-600 dark:hover:text-navy-100
                                                                    dark:focus:bg-navy-600 dark:focus:text-navy-100">
                                                                    <i class="fa-solid fa-trash w-4 text-error dark:text-error/80 mr-3"></i>
                                                                    <span>Hapus Data</span>
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
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
    </div>

    @include('master.roles._modal-create', ['groupedPermissions' => $groupedPermissions])
    @foreach($roles as $role)
        @if($role && $role->id)
            @include('master.roles._modal-edit', [
                'role' => $role,
                'groupedPermissions' => $groupedPermissions
            ])
            @include('master.roles.modals._delete-form', ['role' => $role])
        @endif
    @endforeach

    {{-- Page-Specific JS --}}
    <x-slot:scripts>
        @vite('resources/js/pages/role.js')
        @vite('resources/lineone/js/pages/dashboards-doctor.js')
    </x-slot:scripts>
</x-layouts.app>