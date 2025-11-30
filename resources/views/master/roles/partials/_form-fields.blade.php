@props([
    'role' => null,
])

<div class="card">
    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
        <div class="flex items-center space-x-2">
            <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <h6 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                Informasi Role
            </h6>
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
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1">
                <p class="text-xs+">
                    Setelah role dibuat, Anda perlu mengatur hak akses (permissions) di bawah ini untuk menentukan apa yang dapat dilakukan oleh role ini.
                </p>
            </div>
        </div>
    </div>
</div>