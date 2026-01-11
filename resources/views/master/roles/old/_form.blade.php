@php
    $roleData = $role ?? [];
@endphp

<div class="space-y-4">
    <!-- Field Nama Role -->
    <label class="block">
        <span class="text-slate-600 dark:text-navy-100">Nama Role</span>
        <input
                type="text"
                name="name"
                value="{{ old('name', $roleData->name ?? '') }}"
                placeholder="Masukkan nama role (e.g., Admin, User, Manager)"
                class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
        />
        @error('name')
        <span class="text-tiny-plus text-error mt-2 ms-1">{{ $message }}</span>
        @enderror
    </label>

    <hr class="border-slate-200 dark:border-navy-500">

    <div class="grid grid-cols-2 gap-4 mt-2">
        <div>
            <label class="inline-flex items-center space-x-2">
                <input
                        type="checkbox"
                        name="is_editable"
                        value="1"
                        {{ old('is_editable', $roleData->is_editable ?? false) ? 'checked' : '' }}
                        class="form-checkbox is-basic h-5 w-5 rounded border-slate-400/70 checked:bg-primary checked:border-primary dark:border-navy-400 dark:checked:bg-accent dark:checked:border-accent"
                />
                <span class="text-slate-600 dark:text-navy-100 text-sm">Boleh Diedit</span>
            </label>
        </div>

        <div>
            <label class="inline-flex items-center space-x-2">
                <input
                        type="checkbox"
                        name="is_deletable"
                        value="1"
                        {{ old('is_deletable', $roleData->is_deletable ?? false) ? 'checked' : '' }}
                        class="form-checkbox is-basic h-5 w-5 rounded border-slate-400/70 checked:bg-primary checked:border-primary dark:border-navy-400 dark:checked:bg-accent dark:checked:border-accent"
                />
                <span class="text-slate-600 dark:text-navy-100 text-sm">Boleh Dihapus</span>
            </label>
        </div>
    </div>

    <hr class="border-slate-200 dark:border-navy-500">

    <div>
        <div class="flex items-center justify-between mb-4">
            <h6 class="text-sm text-slate-700 dark:text-navy-100">
                Hak Akses (Permissions)
            </h6>
            <div class="flex gap-2">
                <button
                        type="button"
                        onclick="checkAllPermissions()"
                        class="btn h-8 rounded-md bg-primary px-3 text-xs font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90"
                >
                    Pilih Semua
                </button>
                <button
                        type="button"
                        onclick="uncheckAllPermissions()"
                        class="btn h-8 rounded-md border border-slate-300 px-3 text-xs font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500"
                >
                    Hapus Semua
                </button>
            </div>
        </div>

        @error('permissions')
        <div class="alert flex space-x-2 items-center rounded-lg border border-error p-2 text-error text-tiny-plus sm:px-5 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>{{ $message }}</p>
        </div>
        @enderror

        <!-- Permission Groups -->
        <div class="space-y-4">
            @foreach($groupedPermissions as $group)
                <div class="card p-0 sm:p-5 border-1 border-slate-200 dark:border-navy-500">
                    <!-- Group Header -->
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-200 dark:border-navy-500">
                        <h4 class="text-xs text-slate-700 dark:text-navy-100 uppercase tracking-wide">
                            {{ $group['group_name'] }}
                        </h4>
                        <label class="inline-flex items-center space-x-2">
                            <input
                                    type="checkbox"
                                    onchange="toggleGroupPermissions(this, '{{ Str::slug($group['group_name']) }}')"
                                    class="form-checkbox is-basic h-5 w-5 rounded border-slate-400/70 checked:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent"
                            />
                            <span class="text-xs text-slate-600 dark:text-navy-100 font-medium">Pilih Semua</span>
                        </label>
                    </div>

                    @php
                        $availableActions = App\Helpers\PermissionHelper::getAvailableActions($group['entities']);
                    @endphp

                            <!-- Permission Table -->
                    <div class="overflow-x-auto border-1 border-slate-200 dark:border-navy-500 rounded-lg">
                        <table class="w-full text-left text-xs">
                            <thead>
                            <tr class="border-b border-slate-200 dark:border-navy-500">
                                <th class="whitespace-nowrap px-3 py-3 font-semibold bg-slate-200 dark:bg-navy-800 text-slate-800 dark:text-navy-100 lg:px-4 text-xs">
                                    Menu
                                </th>
                                @foreach($availableActions as $action)
                                    <th style="width: 175px;" class="whitespace-nowrap px-3 py-3 font-semibold bg-slate-200 dark:bg-navy-800 text-slate-800 dark:text-navy-100 text-center text-xs">
                                        {{ ucfirst($action) }}
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($group['entities'] as $entity)
                                <tr class="border-b border-slate-200 dark:border-navy-500 hover:bg-slate-50 dark:hover:bg-navy-600">
                                    <td class="whitespace-nowrap px-3 py-2 lg:px-4">
                                            <span class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $entity['display_name'] }}
                                            </span>
                                    </td>
                                    @foreach($availableActions as $action)
                                        <td class="whitespace-nowrap px-3 py-3 text-center">
                                            @if($entity['actions'][$action])
                                                <label class="inline-flex items-center justify-center">
                                                    <input
                                                            type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $entity['actions'][$action]->id }}"
                                                            data-group="{{ Str::slug($group['group_name']) }}"
                                                            {{ in_array($entity['actions'][$action]->id, $rolePermissionIds) ? 'checked' : '' }}
                                                            class="form-checkbox is-basic h-5 w-5 rounded border-slate-400/70 checked:bg-success checked:border-success hover:border-success focus:border-success dark:border-navy-400 dark:checked:bg-success dark:checked:border-success dark:hover:border-success dark:focus:border-success"
                                                    />
                                                </label>
                                            @else
                                                <span class="text-slate-300 dark:text-navy-500">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>