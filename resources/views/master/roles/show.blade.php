<x-layouts.app title="Detail Role: {{ $role->name }}">
    <x-ui.breadcrumb
            title="Detail Role"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Role', 'url' => route('master.roles.index')],
            ['label' => 'Detail']
        ]"
    />

    <x-ui.page-header
            title="Detail Role: {{ $role->name }}"
            description="Informasi lengkap tentang role dan hak aksesnya"
            backUrl="{{ route('master.roles.index') }}"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </x-slot:icon>

        <x-slot:actions>
            @if($role->is_editable)
<a
                href="{{ route('master.roles.edit', $role) }}"
                class="btn space-x-2 bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90"
                >
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Role</span>
                </a>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
        {{-- Left Column - Info --}}
        <div class="col-span-12 lg:col-span-4">
            <div class="card p-4 sm:p-5 space-y-4">
                <div>
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                        Informasi Role
                    </h3>
                </div>

                <div class="space-y-3">
                    <div>
                        <span class="text-xs text-slate-500 dark:text-navy-300">Nama Role</span>
                        <p class="font-medium text-slate-700 dark:text-navy-100">{{ $role->name }}</p>
                    </div>

                    @if($role->description)
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Deskripsi</span>
                            <p class="text-sm text-slate-600 dark:text-navy-200">{{ $role->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Editable</span>
                            <p>
                                @if ($role->is_editable)
                                    <span class="badge bg-success/10 text-success dark:bg-success/15">Ya</span>
                                @else
                                    <span class="badge bg-slate-150 text-slate-800 dark:bg-navy-500">Tidak</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Deletable</span>
                            <p>
                                @if($role->is_deletable)
                                    <span class="badge bg-success/10 text-success dark:bg-success/15">Ya</span>
                                @else
                                    <span class="badge bg-slate-150 text-slate-800 dark:bg-navy-500">Tidak</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-200 dark:border-navy-500 space-y-2 text-xs text-slate-500 dark:text-navy-300">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span>Dibuat: {{ $role->created_at_formatted }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-clock"></i>
                            <span>Terakhir Update: {{ $role->updated_at_formatted }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>{{ $role->permissions->count() }} Permissions</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Permissions --}}
        <div class="col-span-12 lg:col-span-8">
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                            Hak Akses (Permissions)
                        </h4>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    @if($role->permissions->isEmpty())
                        <div class="text-center py-8">
                            <i class="fa-solid fa-shield-xmark text-4xl text-slate-300 dark:text-navy-500 mb-3"></i>
                            <p class="text-slate-500 dark:text-navy-300">Role ini belum memiliki permission</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($groupedPermissions as $group)
                                @php
                                    $groupPermissions = $role->permissions->filter(function($perm) use ($group) {
                                        return $perm->display_group_name === $group['group_name'];
                                    });
                                @endphp

{{--                                @if($groupPermissions->isNotEmpty())--}}
{{--                                    <div class="border border-slate-200 rounded-lg dark:border-navy-500 overflow-hidden">--}}
{{--                                        <div class="bg-slate-100 dark:bg-navy-800 px-4 py-3">--}}
{{--                                            <div class="flex items-center space-x-2">--}}
{{--                                                <i class="fa-solid fa-folder text-slate-500 dark:text-navy-300"></i>--}}
{{--                                                <h5 class="font-semibold text-slate-700 dark:text-navy-100 uppercase text-sm">--}}
{{--                                                    {{ $group['group_name'] }}--}}
{{--                                                </h5>--}}
{{--                                                <span class="badge bg-primary/10 text-primary text-xs">--}}
{{--                                                {{ $groupPermissions->count() }}--}}
{{--                                            </span>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="p-4">--}}
{{--                                            <div class="flex flex-wrap gap-2">--}}
{{--                                                @foreach($groupPermissions as $permission)--}}
{{--                                                    <span class="badge bg-success/10 text-success">--}}
{{--                                                    <i class="fa-solid fa-check mr-1"></i>--}}
{{--                                                    {{ $permission->display_name }}--}}
{{--                                                </span>--}}
{{--                                                @endforeach--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endif--}}

                                @if($groupPermissions->isNotEmpty())
                                    <div class="border border-slate-200 rounded-lg dark:border-navy-500 overflow-hidden">
                                        <div class="bg-slate-100 dark:bg-navy-800 px-4 py-3">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fa-solid fa-folder text-slate-500 dark:text-navy-300"></i>
                                                    <h5 class="font-semibold text-slate-700 dark:text-navy-100 uppercase text-sm">
                                                        {{ $group['group_name'] }}
                                                    </h5>
                                                </div>
                                                <span class="badge bg-primary/10 text-primary text-xs px-2 py-0.5">
                                                    {{ $groupPermissions->count() }} Akses
                                                </span>
                                            </div>
                                        </div>

                                        <div class="p-4">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-2">
                                                @foreach($groupPermissions as $permission)
                                                    <div class="flex items-center space-x-2 rounded-md bg-success/5 px-3 py-2 border border-success/20">
                                                        <i class="fa-solid fa-check text-success text-sm"></i>
                                                        <span class="text-xs text-slate-700 dark:text-navy-100">
                                                            {{ $permission->display_name }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>