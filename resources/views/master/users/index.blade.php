<x-layouts.app title="Master Data User">
    <x-ui.breadcrumb
            title="Data User"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'User']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <x-ui.table-header
                    title="Data User"
                    description="Kelola pengguna dan akses sistem"
                    search-placeholder="Search users..."
                    :is-deleted-view="request()->has('view_deleted')"
                    :create-route="route('master.users.create')"
                    create-text="Tambah User"
                    :index-route="route('master.users.index')"
                    :deleted-count="$users->total()"
                    restore-all-modal-id="restore-all-users-modal"
            />

            <div class="card mt-3 p-4">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto border-1 border-slate-200 dark:border-navy-500 rounded-lg">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                        <tr class="text-xs">
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">User</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Email</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Role</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Status</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                {{ request()->has('view_deleted') ? 'Dihapus Oleh' : 'Dibuat Oleh' }}
                            </th>
                            <th class="whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $user)
                            <tr class="text-xs border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar size-11">
                                            <img
                                                class="rounded-full"
                                                src="{{ $user->profile->photo_url }}"
                                                alt="avatar"
                                            />
                                        </div>
                                        <div>
                                            <p class="text-slate-700 dark:text-navy-100">
                                                {{ $user->profile->full_name ?? 'No Name' }}
                                            </p>
                                            @if($user->profile && $user->profile->student_or_employee_id)
                                                <p class="text-xs text-slate-400 dark:text-navy-300">
                                                    ID: {{ $user->profile->student_or_employee_id }}
                                                </p>
                                            @endif
                                            <p class="text-tiny-plus text-slate-400 dark:text-navy-300">
                                                {{ $user?->code ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span class="text-slate-600 dark:text-navy-100">{{ $user->email }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-tiny text-center">
                                    @if($user->roles->isNotEmpty())
                                        {!! $user->role_badge !!}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <label class="flex flex-col items-center justify-center gap-1">
                                        <input
                                            type="checkbox"
                                            data-original-state="{{ $user->status ? '1' : '0' }}"
                                            data-toggle="modal"
                                            data-target="#confirm-update-status-modal-{{ $user->id }}"
                                            class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:bg-primary checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:bg-accent dark:checked:before:bg-white status-toggle"
                                            {{ $user->status ? 'checked' : '' }}
                                        />
                                        <span class="text-tiny {{ $user->status ? 'text-success' : 'text-warning' }}">
                                            {{ $user->status_text }}
                                        </span>
                                    </label>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-xs leading-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col">
                                            <span class="text-slate-700 dark:text-navy-100">
                                                {{ request()->has('view_deleted') ? $user->deleted_by_name : $user->created_by_name }}
                                            </span>
                                            <span class="text-slate-700 dark:text-navy-100">
                                                {{ request()->has('view_deleted') ? $user->deleted_at_formatted : $user->created_at_formatted }}
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
                                                    data-target="#restore-user-modal-{{ $user->id }}"
                                                    class="btn size-8 p-0 text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25"
                                                    title="Restore">
                                                <i class="fa-solid fa-undo"></i>
                                            </button>

                                            <!-- Force Delete Button -->
                                            <button type="button"
                                                    data-toggle="modal"
                                                    data-target="#force-delete-user-modal-{{ $user->id }}"
                                                    class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                    title="Hapus Permanen">
                                                <i class="fa-solid fa-trash-alt"></i>
                                            </button>
                                        @else
                                            {{-- Detail Button --}}
                                            <a href="{{ route('master.users.show', $user) }}"
                                               class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25"
                                               title="Detail">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            {{-- Edit Button --}}
                                            <a href="{{ route('master.users.edit', $user) }}"
                                               class="btn size-8 p-0 text-warning hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
                                               title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            {{-- Delete Button --}}
                                            <button
                                                type="button"
                                                data-toggle="modal"
                                                data-target="#delete-user-modal-{{ $user->id }}"
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
                                <td colspan="{{ request()->has('view_deleted') ? '6' : '6' }}"
                                    class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <span class="text-xs-plus">
                                        {{ request()->has('view_deleted')
                                            ? 'Tidak ada pengguna yang dihapus.'
                                            : 'Data tidak ditemukan.'
                                        }}
                                    </span>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="flex justify-center pt-3">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Section --}}
    @if(request()->has('view_deleted'))
        {{-- Modal Restore All --}}
        @if($users->total() > 0)
            @include('master.users.modals._restore-all')
        @endif

        {{-- Modal Restore Single & Force Delete per User --}}
        @foreach($users as $user)
            @include('master.users.modals._restore', ['user' => $user])
            @include('master.users.modals._force-delete', ['user' => $user])
        @endforeach
    @else
        {{-- Modal Delete (Soft Delete) per User --}}
        @foreach($users as $user)
            @include('master.users.modals._update-status', ['user' => $user])
            @include('master.users.modals._delete', ['user' => $user])
        @endforeach
    @endif
</x-layouts.app>