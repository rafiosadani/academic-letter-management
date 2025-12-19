<x-layouts.app :title="isset($user) ? 'Edit User: ' . $user->profile->full_name : 'Tambah User Baru'">
    <x-ui.breadcrumb
            :title="isset($user) ? 'Edit User' : 'Tambah User'"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'User', 'url' => route('master.users.index')],
            ['label' => isset($user) ? 'Edit' : 'Tambah']
        ]"
    />

    <x-ui.page-header
            :title="isset($user) ? 'Edit User: ' . ($user->profile->full_name ?? 'User') : 'Tambah User Baru'"
            :description="isset($user) ? 'Perbarui informasi user dan akses sistem' : 'Buat user baru dengan akses sesuai role'"
            :backUrl="route('master.users.index')"
    >
        <x-slot:icon>
            @if(isset($user))
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            @endif
        </x-slot:icon>
    </x-ui.page-header>

    {{-- FORM --}}
    <form
        method="POST"
        action="{{ isset($user) ? route('master.users.update', $user) : route('master.users.store') }}"
        enctype="multipart/form-data"
        class="space-y-5"
    >
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        @if(isset($user))
            <input type="hidden" name="oldPhoto" value="{{ $user->profile->photo ?? '' }}">
        @endif

        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
            {{-- Left Column - Main Info --}}
            <div class="col-span-12 lg:col-span-8 space-y-5">

                {{-- Account Information --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-user-lock"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Informasi Akun
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        {{-- Email --}}
                        <x-form.input
                                label="Email"
                                name="email"
                                type="email"
                                :value="$user->email ?? ''"
                                placeholder="contoh@email.com"
                                helper="Email akan digunakan untuk login"
                                required
                        />

                        {{-- Password --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="font-medium text-slate-600 dark:text-navy-100">
                                    Password
                                    @if(!isset($user))
                                        <span class="text-error">*</span>
                                    @endif
                                </span>
                                <div class="relative mt-1.5 password-wrapper">
                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        placeholder="{{ isset($user) ? 'Kosongkan jika tidak ingin ubah' : 'Minimal 8 karakter' }}"
                                        {{ !isset($user) ? 'required' : '' }}
                                        class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pr-10 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                    />
                                    <button
                                        type="button"
                                        data-toggle-password="true"
                                        class="absolute right-0 top-0 flex h-full w-10 items-center justify-center text-slate-400 hover:text-slate-600 dark:text-navy-300 dark:hover:text-navy-100"
                                        title="Toggle Password Visibility"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                                @enderror
                                @if(!isset($user))
                                    <span class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1 ms-1 block">Minimal 8 karakter</span>
                                @endif
                            </label>

                            <label class="block">
                                <span class="font-medium text-slate-600 dark:text-navy-100">
                                    Konfirmasi Password
                                    @if(!isset($user))
                                        <span class="text-error">*</span>
                                    @endif
                                </span>
                                <div class="relative mt-1.5 password-wrapper">
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        placeholder="Ketik ulang password"
                                        {{ !isset($user) ? 'required' : '' }}
                                        class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pr-10 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                    />
                                    <button
                                        type="button"
                                        data-toggle-password="true"
                                        class="absolute right-0 top-0 flex h-full w-10 items-center justify-center text-slate-400 hover:text-slate-600 dark:text-navy-300 dark:hover:text-navy-100"
                                        title="Toggle Password Visibility"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Profile Information --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Informasi Profil
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        {{-- Full Name --}}
                        <x-form.input
                            label="Nama Lengkap"
                            name="full_name"
                            :value="$user->profile->full_name ?? ''"
                            placeholder="Nama lengkap user"
                            required
                        />

                        {{-- Student/Employee ID & Phone --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-form.input
                                label="NIM/NIP"
                                name="student_or_employee_id"
                                :value="$user->profile->student_or_employee_id ?? ''"
                                placeholder="Contoh: 21011012345"
                                helper="Nomor Induk Mahasiswa atau Pegawai"
                                required
                            />

                            <x-form.input
                                label="No. Telepon"
                                name="phone"
                                type="tel"
                                :value="$user->profile->phone ?? ''"
                                placeholder="08123456789"
                            />
                        </div>

                        {{-- Program Studi --}}
                        <x-form.select
                                id="studyProgram"
                                label="Program Studi"
                                name="study_program_id"
                                :options="$studyPrograms"
                                :value="$user->profile->study_program_id ?? ''"
                                placeholder="Pilih program studi"
                        />

                        {{-- Address --}}
                        <x-form.textarea
                                label="Alamat"
                                name="address"
                                :value="$user->profile->address ?? ''"
                                placeholder="Alamat lengkap..."
                                rows="3"
                        />
                    </div>
                </div>
            </div>

            {{-- Right Column - Settings --}}
            <div class="col-span-12 lg:col-span-4 space-y-5">

                {{-- Photo Upload --}}
                <div class="card flex flex-col items-center p-4 sm:p-5">
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-3">
                        Foto Profil
                    </h3>

                    {{-- Current Photo Preview --}}
                    @if(isset($user) && $user->profile && $user->profile->photo)
                        <div class="mb-3">
                            <img
                                    src="{{ $user->profile->photo_url }}"
                                    alt="Current Photo"
                                    class="mx-auto size-32 rounded-full object-cover border-2 border-slate-200 dark:border-navy-500"
                            />
                            <p class="text-center text-xs text-slate-400 dark:text-navy-300 mt-2">Foto saat ini</p>
                        </div>
                    @endif

                    <x-form.file
                            label="Upload Foto Baru"
                            name="photo"
                            accept="image/*"
                            showPreview
                            centered
                            preview-variant="square"
                            helper="Max 2MB (JPG, PNG)"
                    />
                </div>

                {{-- Role & Status --}}
                <div class="card p-4 sm:p-5 space-y-4">
                    <div>
                        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            Akses & Status
                        </h3>
                        <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">
                            Atur role dan status user
                        </p>
                    </div>

                    {{-- Role --}}
                    <x-form.select
                            label="Role"
                            name="role_id"
                            :options="$roles->pluck('name', 'id')->toArray()"
                            :value="isset($user) && $user->roles->isNotEmpty() ? $user->roles->first()->id : ''"
                            placeholder="Pilih role"
                            required
                            helper="Menentukan hak akses user"
                    />

                    {{-- Status --}}
                    <label class="block">
                        <span class="font-medium text-slate-600 dark:text-navy-100">
                            Status User
                            <span class="text-error">*</span>
                        </span>
                        <div class="mt-2 flex gap-4">
                            <label class="inline-flex items-center space-x-1">
                                <input
                                    type="radio"
                                    name="status"
                                    value="1"
                                    {{ old('status', $user->status ?? 1) == 1 ? 'checked' : '' }}
                                    class="form-radio is-basic size-5 rounded-full border-slate-400/70 checked:bg-success checked:border-success hover:border-success focus:border-success dark:border-navy-400"
                                />
                                <span class="text-slate-600 dark:text-navy-100">Aktif</span>
                            </label>
                            <label class="inline-flex items-center space-x-1">
                                <input
                                        type="radio"
                                        name="status"
                                        value="0"
                                        {{ old('status', $user->status ?? 1) == 0 ? 'checked' : '' }}
                                        class="form-radio is-basic size-5 rounded-full border-slate-400/70 checked:bg-warning checked:border-warning hover:border-warning focus:border-warning dark:border-navy-400"
                                />
                                <span class="text-slate-600 dark:text-navy-100">Nonaktif</span>
                            </label>
                        </div>
                        @error('status')
                        <span class="text-tiny+ text-error mt-1 block">{{ $message }}</span>
                        @enderror
                    </label>

                    @if(isset($user))
                        <div class="pt-3 border-t border-slate-200 dark:border-navy-500">
                            <div class="space-y-2 text-xs text-slate-500 dark:text-navy-300">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>Dibuat: {{ $user->created_at_formatted }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Update: {{ $user->updated_at_formatted }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Buttons (Sticky Bottom) --}}
        <x-form.sticky-form-actions
            :cancelUrl="route('master.users.index')"
            :submitText="isset($user) ? 'Update User' : 'Simpan User'"
            :submitType="isset($user) ? 'warning' : 'primary'"
        />
    </form>
</x-layouts.app>