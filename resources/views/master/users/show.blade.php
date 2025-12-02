<x-layouts.app title="Detail User: {{ $user->profile->full_name ?? 'User' }}">
    <x-ui.breadcrumb
            title="Detail User"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'User', 'url' => route('master.users.index')],
            ['label' => 'Detail']
        ]"
    />

    <x-ui.page-header
            title="Detail User"
            description="Informasi lengkap tentang user"
            :backUrl="route('master.users.index')"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </x-slot:icon>

        <x-slot:actions>
            <a href="{{ route('master.users.edit', $user) }}"
                class="btn space-x-2 bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90"
            >
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit User</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6 pb-4 sm:pb-6">
        {{-- Left Column - Profile Card --}}
        <div class="col-span-12 lg:col-span-4">
            <div class="card p-4 sm:p-5">
                {{-- Profile Photo --}}
                <div class="flex flex-col items-center text-center">
                    <div class="avatar size-32">
                        <img class="rounded-full border-2 border-slate-200 dark:border-navy-500"
                            src="{{ $user->profile->photo_url }}"
                            alt="avatar"
                        />
                    </div>
                    <h3 class="mt-3 text-lg font-medium text-slate-700 dark:text-navy-100">
                        {{ $user->profile->full_name ?? 'No Name' }}
                    </h3>
                    @if($user->profile && $user->profile->student_or_employee_id)
                        <p class="text-xs text-slate-400 dark:text-navy-300">
                            ID: {{ $user->profile->student_or_employee_id }}
                        </p>
                    @endif
                    @if($user->email)
                        <p class="text-xs text-slate-400 dark:text-navy-300">
                            {{ $user->email }}
                        </p>
                    @endif

                    {{-- Status Badge --}}
                    @if($user->status)
                        <div class="mt-3">
                            {!! $user->status_badge !!}
                        </div>
                    @endif

                    {{-- Role Badge --}}
                    @if($user->roles->isNotEmpty())
                        <div class="mt-2">
                            <span class="badge {{ $user->role_badge_class }}">
                                <i class="fa-solid fa-user-shield mr-1"></i>
                                {{ $user->roles->first()->name }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Timestamps --}}
                <div class="mt-6 pt-4 border-t border-slate-200 dark:border-navy-500 space-y-2 text-xs text-slate-500 dark:text-navy-300">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-calendar-plus"></i>
                        <span>Terdaftar: {{ $user->created_at_formatted }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-clock"></i>
                        <span>Terakhir Update: {{ $user->updated_at_formatted }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Details --}}
        <div class="col-span-12 lg:col-span-8 space-y-5">

            {{-- Profile Details --}}
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                            Informasi Profil
                        </h4>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {{-- Full Name --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Nama Lengkap</span>
                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                {{ $user->profile->full_name ?? '-' }}
                            </p>
                        </div>

                        {{-- Student/Employee ID --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">NIM/NIP</span>
                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                {{ $user->profile->student_or_employee_id ?? '-' }}
                            </p>
                        </div>

                        {{-- Phone --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">No. Telepon</span>
                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                {{ $user->profile->phone ?? '-' }}
                            </p>
                        </div>

                        {{-- Program --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Program Studi</span>
                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                @if($user->profile && $user->profile->study_program_id)
                                    {{ $user->profile?->studyProgram->degree }} - {{ $user->profile?->studyProgram->name }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>

                        {{-- Address --}}
                        <div class="sm:col-span-2">
                            <span class="text-xs text-slate-500 dark:text-navy-300">Alamat</span>
                            <p class="font-medium text-slate-700 dark:text-navy-100">
                                {{ $user->profile->address ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Account Details --}}
            <div class="card">
                <div class="border-b border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                    <div class="flex items-center space-x-2">
                        <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa-solid fa-user-lock"></i>
                        </div>
                        <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                            Informasi Akun
                        </h4>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {{-- Email --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Email</span>
                            <p class="font-medium text-slate-700 dark:text-navy-100 break-all">
                                {{ $user->email }}
                            </p>
                        </div>

                        {{-- Status --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Status</span>
                            <div class="mt-1">
                                {!! $user->status_badge !!}
                            </div>
                        </div>

                        {{-- Role --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Role</span>
                            <div class="mt-1">
                                {!! $user->role_badge !!}
                            </div>
                        </div>

                        {{-- Email Verified --}}
                        <div>
                            <span class="text-xs text-slate-500 dark:text-navy-300">Verifikasi Email</span>
                            <div class="mt-1">
                                @if($user->email_verified_at)
                                    <span class="badge bg-success/10 text-success">
                                        <i class="fa-solid fa-circle-check mr-1"></i>
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="badge bg-warning/10 text-warning">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                        Belum Terverifikasi
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-12">
            @if($user->roles->isNotEmpty())
                <div class="card">
                    <div class="border-b border-slate-200 px-4 py-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Hak Akses
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        @php
                            $role = $user->roles->first();
                            $permissions = $role->permissions;
                        @endphp
                        @if($permissions->isEmpty())
                            {{-- Tampilan jika Role tidak memiliki Permissions --}}
                            <div class="text-center py-8">
                                <i class="fa-solid fa-shield-xmark text-4xl text-slate-300 dark:text-navy-500 mb-3"></i>
                                <p class="text-slate-500 dark:text-navy-300">Role ini belum memiliki permission.</p>
                            </div>
                        @else
                            {{-- Tampilan Permissions yang Dikelompokkan --}}
                            <div class="space-y-4">
                                @foreach($groupedPermissions as $group)
                                    @php
                                        $groupPermissions = $permissions->filter(function($perm) use ($group) {
                                            return $perm->display_group_name === $group['group_name'];
                                        });
                                        $count = $groupPermissions->count();
                                        $gridCols = match(true) {
                                            $count === 1 => 'grid-cols-1',
                                            $count === 2 => 'grid-cols-2',
                                            default => 'grid-cols-3', // 3 atau lebih akan menggunakan 3 kolom
                                        };

                                        $responsiveGridCols = "grid-cols-1 sm:{$gridCols} md:{$gridCols} lg:{$gridCols}";
                                    @endphp

                                    @if($groupPermissions->isNotEmpty())
                                        <div class="border border-slate-200 rounded-lg dark:border-navy-500 overflow-hidden">
                                            {{-- Header Group --}}
                                            <div class="bg-slate-100 dark:bg-navy-800 px-4 py-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fa-solid fa-folder text-slate-500 dark:text-navy-300"></i>
                                                        <span class="text-slate-700 dark:text-navy-100 uppercase text-tiny-plus">
                                                            {{ $group['group_name'] }}
                                                        </span>
                                                    </div>
                                                    <span class="badge bg-primary/10 text-primary px-2 py-0.5 text-tiny-plus">
                                                        {{ $groupPermissions->count() }} Akses
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Daftar Permissions dalam Group --}}
                                            <div class="p-4">
                                                <div class="grid gap-2 {{ $responsiveGridCols }}">
                                                    @foreach($groupPermissions as $permission)
                                                        <div class="flex items-center space-x-2 rounded-md bg-success/5 px-3 py-2 border border-success/20">
                                                            <i class="fa-solid fa-check text-success text-tiny-plus"></i>
                                                            <span class="text-tiny text-slate-700 dark:text-navy-100">
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

                            {{-- Footer Total Permissions --}}
                            <p class="text-xs text-slate-400 dark:text-navy-300 mt-4 text-right">
                                Total: {{ $permissions->count() }} permissions yang diberikan
                            </p>

                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>