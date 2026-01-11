<x-layouts.app title="Edit Profile">
    <x-ui.breadcrumb
            title="Edit Profile"
            :items="[
            ['label' => 'Profile'],
            ['label' => 'Edit']
        ]"
    />

    <x-ui.page-header
            title="Edit Profile"
            description="Kelola informasi profil dan keamanan akun Anda"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </x-slot:icon>
    </x-ui.page-header>

    <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
        {{-- Left Sidebar - Tab Navigation --}}
        <div class="col-span-12 lg:col-span-4">
            <div class="card p-4 sm:p-5 sticky top-18">
                {{-- User Info --}}
                <div class="flex items-center space-x-4 pb-4 border-b border-slate-200 dark:border-navy-500">
                    <div class="avatar size-14 border border-slate-200 rounded-full">
                        <img
                            class="rounded-full"
                            src="{{ $user->profile->photo_url }}"
                            alt="avatar"
                        />
                    </div>
                    <div>
                        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                            {{ auth()->user()->profile?->short_name ?? 'User' }}
                        </h3>
                        @if(filled(auth()->user()->profile?->student_or_employee_id))
                            <p class="text-tiny-plus text-slate-400 dark:text-navy-300">
                                {{ auth()->user()->profile?->student_or_employee_id ?? '' }}
                            </p>
                        @endif
                        <p class="text-xs text-slate-400 dark:text-navy-300">
                            {{ auth()->user()->role?->name ?? 'Member' }}
                        </p>
                    </div>
                </div>

                {{-- Tab Menu --}}
                <ul class="mt-4 space-y-1.5 font-inter font-medium">
                    {{-- Tab 1: Profil Saya --}}
                    <li>
                        <button
                            type="button"
                            data-tab-target="tab-account"
                            class="profile-tab-btn active bg-primary text-white dark:bg-accent flex w-full items-center space-x-2 rounded-lg px-4 py-2.5 tracking-wide outline-hidden transition-all"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <span>Profil Saya</span>
                        </button>
                    </li>

                    {{-- Tab 2: Keamanan Akun --}}
                    <li>
                        <button
                            type="button"
                            data-tab-target="tab-security"
                            class="profile-tab-btn flex w-full items-center space-x-2 rounded-lg px-4 py-2.5 tracking-wide outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-navy-600 dark:hover:text-navy-100"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                />
                            </svg>
                            <span>Keamanan Akun</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Right Content - Tab Content --}}
        <div class="col-span-12 lg:col-span-8">
            {{-- Account Profile --}}
            <div id="tab-account" class="profile-tab-content">
                <form
                    method="POST"
                    action="{{ route('profile.update') }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="oldPhoto" value="{{ $user->profile->photo }}">

                    <div class="card">
                        <div class="flex flex-col items-center space-y-4 border-b border-slate-200 p-4 dark:border-navy-500 sm:flex-row sm:justify-between sm:space-y-0 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Perbarui Profil
                            </h2>
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('profile.edit', ['tab' => 'tab-account']) }}"
                                    class="btn min-w-[7rem] rounded-full border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90"
                                >
                                    <i class="fa-solid fa-xmark mr-2"></i>
                                    Batal
                                </a>
                                <button
                                    type="submit"
                                    class="btn min-w-[7rem] rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90"
                                >
                                    <i class="fa-solid fa-check mr-2"></i>
                                    Simpan
                                </button>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5">
                            {{-- Avatar Section --}}
                            <div class="flex flex-col">
                                <span class="text-base font-medium text-slate-600 dark:text-navy-100">Foto Profil</span>
                                <div class="avatar my-1.5 size-20">
                                    <img
                                        id="avatar-preview"
                                        class="mask is-squircle"
                                        src="{{ $user->profile->photo_url }}"
                                        alt="avatar"
                                    />
                                    <div class="absolute -bottom-1 -right-1 flex items-center justify-center rounded-full bg-white dark:bg-navy-700">
                                        <label for="photo-upload" class="btn size-6 rounded-full border border-slate-200 p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:border-navy-500 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25 cursor-pointer">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="size-3.5"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </label>
                                        <input
                                            type="file"
                                            id="photo-upload"
                                            name="photo"
                                            accept="image/*"
                                            class="hidden"
                                            onchange="previewAvatar(event)"
                                        />
                                    </div>
                                </div>
                                @error('photo')
                                    <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                                @enderror
                                <span class="text-tiny-plus text-slate-400 dark:text-navy-300 mt-1 ms-1 block">Format: JPG atau PNG. Maksimal 2MB.</span>
                            </div>

                            <div class="my-4 h-px bg-slate-200 dark:bg-navy-500"></div>

                            {{-- Personal Information --}}
                            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 mb-4">
                                Biodata Diri
                            </h3>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                {{-- Full Name --}}
                                <x-form.input
                                    label="Nama Lengkap"
                                    name="full_name"
                                    :value="$user->profile->full_name"
                                    placeholder="Masukkan nama lengkap"
                                />

                                {{-- Email --}}
                                <x-form.input
                                    label="Email"
                                    name="email"
                                    type="email"
                                    :value="$user->email"
                                    placeholder="email@example.com"
                                />

                                {{-- Place of Birth --}}
                                <x-form.input
                                    label="Tempat Lahir"
                                    name="place_of_birth"
                                    :value="$user->profile->place_of_birth"
                                    placeholder="Contoh: Malang"
                                />

                                {{-- Date of Birth --}}
                                <x-form.input
                                    label="Tanggal Lahir"
                                    name="date_of_birth"
                                    type="date"
                                    :value="$user->profile->date_of_birth ? $user->profile->date_of_birth->format('Y-m-d') : ''"
                                />

                                @php
                                    $isMahasiswa = auth()->user()->hasRole('Mahasiswa');
                                @endphp

                                {{-- NIM (Readonly) --}}
                                <x-form.input
                                        :label="$isMahasiswa ? 'Nomor Induk Mahasiswa (NIM)' : 'NIP / NIK'"
                                        name="student_or_employee_id"
                                        :value="$user->profile->student_or_employee_id"
                                        :disabled="$isMahasiswa"
                                        :helper="$isMahasiswa ? 'NIM sudah diverifikasi dan tidak dapat diubah' : 'Pastikan NIP/NIK sesuai dengan data kepegawaian'"
                                />

                                {{-- Phone --}}
                                <x-form.input
                                        label="No. Telepon"
                                        name="phone"
                                        type="tel"
                                        :value="$user->profile->phone"
                                        placeholder="08123456789"
                                />

                                {{-- Study Program --}}
                                @if(auth()->user()->hasRole('Mahasiswa'))
                                    <div class="sm:col-span-2">
                                        <x-form.select
                                                label="Program Studi"
                                                name="study_program_id"
                                                :options="$studyPrograms"
                                                :value="$user->profile->study_program_id"
                                                placeholder="Pilih program studi"
                                                disabled
                                                helper="Program studi hanya dapat diubah melalui Bagian Akademik/Administrator"
                                                required
                                        />
                                        <input type="hidden" name="study_program_id" value="{{ $user->profile->study_program_id }}">
                                    </div>
                                @endif

                                {{-- Address --}}
                                <div class="sm:col-span-2">
                                    <x-form.textarea
                                            label="Alamat"
                                            name="address"
                                            :value="$user->profile->address"
                                            placeholder="Contoh: Jl Veteran No 12 - 14, Ketawanggede, Malang, Jawa Timur, Indonesia"
                                            rows="3"
                                    />
                                </div>
                            </div>

                            @if(auth()->user()->hasRole('Mahasiswa'))
                                <div class="my-4 h-px bg-slate-200 dark:bg-navy-500"></div>

                                {{-- Parent Information --}}
                                <div class="mb-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                            Informasi Orang Tua / Wali
                                        </h3>
                                        <div class="shrink-0">
                                            {{-- Status Kelengkapan Data --}}
                                            @if(!$user->profile->isCompleteForSkakTunjangan())
                                                <span class="badge bg-warning/10 text-warning text-tiny font-medium">
                                                    <i class="fa-solid fa-exclamation-triangle mr-1"></i> Belum Lengkap
                                                </span>
                                            @else
                                                <span class="badge bg-success/10 text-success text-tiny font-medium">
                                                    <i class="fa-solid fa-check-circle mr-1"></i> Data Lengkap
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-justify text-slate-400 dark:text-navy-300 mt-1">
                                        Data ini wajib diisi untuk keperluan pembuatan Surat Keterangan Aktif Kuliah Persyaratan Tunjangan Orang Tua
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    {{-- Parent Name --}}
                                    <x-form.input
                                            label="Nama Orang Tua"
                                            name="parent_name"
                                            :value="$user->profile->parent_name"
                                            placeholder="Nama lengkap orang tua (tanpa gelar)"
                                    />

                                    {{-- Parent NIP --}}
                                    <x-form.input
                                            label="NIP / No. Pegawai Orang Tua"
                                            name="parent_nip"
                                            :value="$user->profile->parent_nip"
                                            placeholder="Contoh: 19901234..."
                                    />

                                    {{-- Parent Rank --}}
                                    <x-form.input
                                            label="Pangkat / Golongan Orang Tua"
                                            name="parent_rank"
                                            :value="$user->profile->parent_rank"
                                            placeholder="Contoh: Penata Muda Tk. I / III.b"
                                    />

                                    {{-- Parent Institution --}}
                                    <x-form.input
                                            label="Nama Instansi Kerja"
                                            name="parent_institution"
                                            :value="$user->profile->parent_institution"
                                            placeholder="Contoh: Dinas Pendidikan Kota Malang"
                                    />

                                    {{-- Parent Institution Address --}}
                                    <div class="sm:col-span-2">
                                        <x-form.textarea
                                                label="Alamat Instansi Kerja"
                                                name="parent_institution_address"
                                                :value="$user->profile->parent_institution_address"
                                                placeholder="Alamat lengkap instansi tempat orang tua bekerja..."
                                                rows="2"
                                        />
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- Keamanan Akun (Ubah Password)  --}}
            <div id="tab-security" class="profile-tab-content hidden">
                <form
                    method="POST"
                    action="{{ route('profile.password.update') }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="card">
                        <div class="flex flex-col items-center space-y-4 border-b border-slate-200 p-4 dark:border-navy-500 sm:flex-row sm:justify-between sm:space-y-0 sm:px-5">
                            <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                                Ubah Password
                            </h2>
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('profile.edit', ['tab' => 'tab-security']) }}"
                                    type="button"
                                    onclick="window.location.reload()"
                                    class="btn min-w-[7rem] rounded-full border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90"
                                >
                                    <i class="fa-solid fa-xmark mr-2"></i>
                                    Batal
                                </a>
                                <button
                                    type="submit"
                                    class="btn min-w-[7rem] rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90"
                                >
                                    <i class="fa-solid fa-check mr-2"></i>
                                    Simpan
                                </button>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5">
                            <div class="mb-5 flex space-x-3 rounded-lg bg-info/10 p-3 dark:bg-info/15">
                                <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-info text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-justify text-slate-600 dark:text-navy-100">
                                    Pastikan kata sandi Anda kuat dan sulit ditebak. Jangan gunakan tanggal lahir atau nama prodi. Disarankan untuk memperbarui kata sandi secara berkala.
                                </p>
                            </div>
                            <div class="space-y-4">
                                {{-- Current Password --}}
                                <label class="block">
                                    <span class="font-medium text-slate-600 dark:text-navy-100">
                                        Password Saat Ini
                                        <span class="text-error">*</span>
                                    </span>
                                    <div class="relative mt-1.5 password-wrapper">
                                        <input
                                                type="password"
                                                name="current_password"
                                                id="current_password"
                                                placeholder="Masukkan password saat ini"
                                                class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pr-10 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        />
                                        <button
                                                type="button"
                                                data-toggle-password="true"
                                                class="absolute right-0 top-0 flex h-full w-10 items-center justify-center text-slate-400 hover:text-slate-600 dark:text-navy-300 dark:hover:text-navy-100"
                                                title="Toggle Password Visibility"
                                        >
                                            <i class="fa fa-eye transition-colors duration-200"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                    <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                                    @enderror
                                </label>

                                {{-- New Password --}}
                                <label class="block">
                                    <span class="font-medium text-slate-600 dark:text-navy-100">
                                        Password Baru
                                        <span class="text-error">*</span>
                                    </span>
                                    <div class="relative mt-1.5 password-wrapper">
                                        <input
                                            type="password"
                                            name="new_password"
                                            id="new_password"
                                            placeholder="Masukkan password baru (min. 8 karakter)"
                                            class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pr-10 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        />
                                        <button
                                            type="button"
                                            data-toggle-password="true"
                                            class="absolute right-0 top-0 flex h-full w-10 items-center justify-center text-slate-400 hover:text-slate-600 dark:text-navy-300 dark:hover:text-navy-100"
                                            title="Toggle Password Visibility"
                                        >
                                            <i class="fa fa-eye transition-colors duration-200"></i>
                                        </button>
                                    </div>
                                    @error('new_password')
                                    <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                                    @enderror
                                    @if(!$errors->has('new_password'))
                                        <span class="text-tiny-plus text-justify text-slate-400 dark:text-navy-300 mt-1 ms-1 block">
                                            Password harus minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan simbol
                                        </span>
                                    @endif
                                </label>

                                {{-- Confirm New Password --}}
                                <label class="block">
                                    <span class="font-medium text-slate-600 dark:text-navy-100">
                                        Konfirmasi Password Baru
                                        <span class="text-error">*</span>
                                    </span>
                                    <div class="relative mt-1.5 password-wrapper">
                                        <input
                                            type="password"
                                            name="new_password_confirmation"
                                            id="new_password_confirmation"
                                            placeholder="Ketik ulang password baru"
                                            class="form-input w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pr-10 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        />
                                        <button
                                            type="button"
                                            data-toggle-password="true"
                                            class="absolute right-0 top-0 flex h-full w-10 items-center justify-center text-slate-400 hover:text-slate-600 dark:text-navy-300 dark:hover:text-navy-100"
                                            title="Toggle Password Visibility"
                                        >
                                            <i class="fa fa-eye transition-colors duration-200"></i>
                                        </button>
                                    </div>
                                    @error('new_password_confirmation')
                                        <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Custom Scripts for Tabs & Avatar Preview --}}
    <x-slot:scripts>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ===================================
                // TAB SWITCHING FUNCTIONALITY
                // ===================================
                const tabButtons = document.querySelectorAll('.profile-tab-btn');
                const tabContents = document.querySelectorAll('.profile-tab-content');

                // ==========================================
                // 2. LOGIKA PENENTUAN TAB AKTIF
                // ==========================================
                let activeTabId = "{{ $activeTab }}";

                @if(session('active_tab'))
                    activeTabId = "{{ session('active_tab') }}";
                @endif

                @if($errors->has('current_password') || $errors->has('new_password') || $errors->has('new_password_confirmation'))
                    activeTabId = 'tab-security';
                @endif

                @if($errors->has('full_name') || $errors->has('email') || $errors->has('photo'))
                    activeTabId = 'tab-account';
                @endif

                // ==========================================
                // 3. FUNGSI UNTUK MENGAKTIFKAN TAB
                // ==========================================
                function activateTab(targetId) {
                    tabButtons.forEach(btn => {
                        const isTarget = btn.getAttribute('data-tab-target') === targetId;

                        if (isTarget) {
                            // Style saat Aktif
                            btn.classList.add('active', 'bg-primary', 'text-white', 'dark:bg-accent');
                            btn.classList.remove('hover:bg-slate-100', 'hover:text-slate-800', 'dark:hover:bg-navy-600', 'dark:hover:text-navy-100');
                        } else {
                            // Style saat Tidak Aktif
                            btn.classList.remove('active', 'bg-primary', 'text-white', 'dark:bg-accent');
                            btn.classList.add('hover:bg-slate-100', 'hover:text-slate-800', 'dark:hover:bg-navy-600', 'dark:hover:text-navy-100');
                        }
                    });

                    // Update Tampilan Konten Tab
                    tabContents.forEach(content => {
                        if (content.id === targetId) {
                            content.classList.remove('hidden');
                        } else {
                            content.classList.add('hidden');
                        }
                    });
                }

                // ==========================================
                // 4. JALANKAN SAAT HALAMAN DIBUKA
                // ==========================================
                activateTab(activeTabId);

                // ==========================================
                // 5. EVENT LISTENER KLIK MANUAL
                // ==========================================
                tabButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-tab-target');
                        activateTab(targetId);
                    });
                });
            });

            // ===================================
            // AVATAR PREVIEW
            // ===================================
            function previewAvatar(event) {
                const input = event.target;
                const preview = document.getElementById('avatar-preview');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    </x-slot:scripts>
</x-layouts.app>