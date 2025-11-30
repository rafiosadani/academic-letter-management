@props([
    'groupedPermissions',
    'selectedPermissions' => []
])

<div class="card">
    <div class="border-b border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success dark:bg-success-light/10 dark:text-success-light">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                    Hak Akses (Permissions)
                </h4>
            </div>

            <div class="flex gap-2">
                <button
                        type="button"
                        onclick="checkAllPermissions()"
                        class="btn h-8 rounded-md bg-primary px-3 text-xs font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90"
                >
                    <i class="fa-solid fa-check-double mr-1"></i>
                    Pilih Semua
                </button>
                <button
                        type="button"
                        onclick="uncheckAllPermissions()"
                        class="btn h-8 rounded-md border border-slate-300 px-3 text-xs font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500"
                >
                    <i class="fa-solid fa-xmark mr-1"></i>
                    Hapus Semua
                </button>
            </div>
        </div>
    </div>

    @error('permissions')
    <div class="alert flex space-x-2 items-center rounded-lg border border-error mx-4 mt-4 p-3 text-error text-xs+ sm:mx-5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-tiny-plus">{{ $message }}</p>
    </div>
    @enderror

    <div class="p-4 sm:p-5">
        <div class="space-y-4">
            @foreach($groupedPermissions as $group)
                <div class="border border-slate-200 rounded-lg dark:border-navy-500 overflow-hidden">
                    {{-- Group Header --}}
                    <div class="flex items-center justify-between bg-slate-100 dark:bg-navy-800 px-4 py-3">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-folder text-slate-500 dark:text-navy-300"></i>
                            <h5 class="font-semibold text-slate-700 dark:text-navy-100 uppercase tracking-wide text-sm">
                                {{ $group['group_name'] }}
                            </h5>
                        </div>
                        <label class="inline-flex items-center space-x-2 cursor-pointer">
                            <input
                                    type="checkbox"
                                    onchange="toggleGroupPermissions(this, '{{ Str::slug($group['group_name']) }}')"
                                    class="form-checkbox is-basic h-5 w-5 rounded border-slate-400/70 checked:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:bg-accent dark:checked:border-accent"
                            />
                            <span class="text-xs font-medium text-slate-600 dark:text-navy-100">Pilih Semua</span>
                        </label>
                    </div>

                    @php
                        $availableActions = App\Helpers\PermissionHelper::getAvailableActions($group['entities']);
                    @endphp

                    {{-- Permission Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                            <tr class="border-b border-slate-200 dark:border-navy-500 bg-slate-50 dark:bg-navy-900">
                                <th class="whitespace-nowrap px-4 py-3 text-left font-semibold text-slate-700 dark:text-navy-100 text-xs">
                                    <i class="fa-solid fa-list-ul mr-2"></i>Menu
                                </th>
                                @foreach($availableActions as $action)
                                    <th class="whitespace-nowrap px-3 py-3 text-center font-semibold text-slate-700 dark:text-navy-100 text-xs" style="width: 205px;">
                                        {{ ucfirst($action) }}
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($group['entities'] as $entity)
                                <tr class="border-b border-slate-200 dark:border-navy-500 hover:bg-slate-50 dark:hover:bg-navy-700/50 transition-colors">
                                    <td class="whitespace-nowrap px-4 py-3">
                                            <span class="font-medium text-slate-700 dark:text-navy-100">
                                                {{ $entity['display_name'] }}
                                            </span>
                                    </td>
                                    @foreach($availableActions as $action)
                                        <td class="whitespace-nowrap px-3 py-3 text-center">
                                            @if(isset($entity['actions'][$action]) && $entity['actions'][$action])
                                                <label class="inline-flex items-center justify-center cursor-pointer">
                                                    <input
                                                            type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $entity['actions'][$action]->id }}"
                                                            data-group="{{ Str::slug($group['group_name']) }}"
                                                            {{ in_array($entity['actions'][$action]->id, $selectedPermissions) ? 'checked' : '' }}
                                                            class="form-checkbox is-basic h-5 w-5 rounded border-slate-400/70 checked:bg-success checked:border-success hover:border-success focus:border-success dark:border-navy-400 dark:checked:bg-success dark:checked:border-success"
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