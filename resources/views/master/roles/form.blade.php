<x-layouts.app :title="isset($role) ? 'Edit Role: ' . $role->name : 'Tambah Role Baru'">
    <x-ui.breadcrumb
            :title="isset($role) ? 'Edit Role' : 'Tambah Role'"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Role', 'url' => route('master.roles.index')],
            ['label' => isset($role) ? 'Edit' : 'Tambah']
        ]"
    />

    <x-ui.page-header
            :title="isset($role) ? 'Edit Role: ' . $role->name : 'Tambah Role Baru'"
            :description="isset($role) ? 'Perbarui informasi dan hak akses role' : 'Buat role baru dan atur hak akses untuk pengguna'"
            :backUrl="route('master.roles.index')"
    >
        <x-slot:icon>
            @if(isset($role))
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            @endif
        </x-slot:icon>
    </x-ui.page-header>

    {{-- FORM --}}
    <form
            method="POST"
            action="{{ isset($role) ? route('master.roles.update', $role) : route('master.roles.store') }}"
            class="space-y-5"
    >
        @csrf
        @if(isset($role))
            @method('PUT')
        @endif

        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
            {{-- Left Column - Form Fields --}}
            <div class="col-span-12 lg:col-span-8">
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Informasi Role
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-5">
                        {{-- Nama Role --}}
                        <x-form.input
                                label="Nama Role"
                                name="name"
                                :value="$role->name ?? ''"
                                placeholder="Contoh: Administrator, Manager, Staff"
                                helper="Nama role yang mudah dikenali dan deskriptif"
                        />

                        {{-- Info Box --}}
                        <div class="alert flex items-center space-x-2 rounded-lg bg-info/10 px-4 py-3 text-info dark:bg-info/15">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-xs+">
                                    {{ isset($role) ? 'Update hak akses (permissions) di bawah sesuai kebutuhan role.' : 'Setelah mengisi nama, atur hak akses (permissions) di bawah ini untuk menentukan apa yang dapat dilakukan oleh role ini.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Settings --}}
            <div class="col-span-12 lg:col-span-4">
                <div class="card p-4 sm:p-5 space-y-4">
                    <div>
                        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            Pengaturan Role
                        </h3>
                        <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                            Konfigurasi tambahan untuk role ini
                        </p>
                    </div>

                    <div class="space-y-3">
                        <x-form.checkbox
                                name="is_editable"
                                label="Boleh Diedit"
                                :checked="old('is_editable', $role->is_editable ?? true)"
                                helper="Role ini dapat diubah di kemudian hari"
                        />

                        <x-form.checkbox
                                name="is_deletable"
                                label="Boleh Dihapus"
                                :checked="old('is_deletable', $role->is_deletable ?? true)"
                                helper="Role ini dapat dihapus dari sistem"
                        />
                    </div>

                    @if(isset($role))
                        {{-- Info Timestamps (hanya untuk edit) --}}
                        <div class="pt-3 border-t border-slate-200 dark:border-navy-500">
                            <div class="space-y-2 text-xs text-slate-500 dark:text-navy-300">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>Dibuat: {{ $role->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Update: {{ $role->updated_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Info Tips (hanya untuk create) --}}
                        <div class="pt-3 border-t border-slate-200 dark:border-navy-500">
                            <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-navy-300">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>Role sistem tidak dapat dihapus atau diubah izinnya</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Permissions Section --}}
        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
            <div class="col-span-12">
                @include('master.roles.partials._permission-table', [
                    'groupedPermissions' => $groupedPermissions,
                    'selectedPermissions' => old('permissions', isset($role) ? $role->permissions->pluck('id')->toArray() : [])
                ])
            </div>
        </div>

        {{-- Action Buttons (Sticky Bottom) --}}
        <div class="sticky bottom-0 z-10 bg-slate-50 dark:bg-navy-800 border-t border-slate-200 dark:border-navy-600 py-4 -mx-[var(--margin-x)] px-[var(--margin-x)]">
            <div class="flex items-center justify-end space-x-3">
                <a
                        href="{{ route('master.roles.index') }}"
                        class="btn min-w-[7rem] border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500"
                >
                    <i class="fa-solid fa-xmark mr-2"></i>
                    Batal
                </a>
                <button
                        type="submit"
                        class="btn min-w-[7rem] {{ isset($role) ? 'bg-warning' : 'bg-primary' }} font-medium text-white hover:{{ isset($role) ? 'bg-warning-focus' : 'bg-primary-focus' }} focus:{{ isset($role) ? 'bg-warning-focus' : 'bg-primary-focus' }} active:{{ isset($role) ? 'bg-warning-focus' : 'bg-primary-focus' }}/90"
                >
                    <i class="fa-solid fa-check mr-2"></i>
                    {{ isset($role) ? 'Update Role' : 'Simpan Role' }}
                </button>
            </div>
        </div>
    </form>

    <x-slot:scripts>
        @vite('resources/js/pages/roles/role-form.js')
    </x-slot:scripts>
</x-layouts.app>