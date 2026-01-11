<x-layouts.guest title="Register"
                 cardTitle="Welcome To Lineone"
                 cardSubtitle="Please sign up to continue">
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="card mt-5 rounded-lg p-5 lg:p-7">
            <label class="relative flex">
                <input class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:z-10 hover:border-slate-400 focus:z-10 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                       placeholder="Email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       autofocus
                />
                <span class="pointer-events-none absolute z-20 flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="size-5 transition-colors duration-200"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                    >
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                    </svg>
                </span>
            </label>
            @error('email')
                <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
            @enderror

            {{-- Full Name --}}
            <label class="relative mt-4 flex">
                <input
                    class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:z-10 hover:border-slate-400 focus:z-10 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="Nama Lengkap"
                    type="text"
                    name="full_name"
                    value="{{ old('full_name') }}"
                />
                <span class="pointer-events-none absolute z-20 flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="size-4.5  transition-colors duration-200"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>
                </span>
            </label>
            @error('full_name')
                <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
            @enderror

            {{-- NIM --}}
            <label class="relative mt-4 flex">
                <input
                    class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:z-10 hover:border-slate-400 focus:z-10 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                    placeholder="NIM (Nomor Induk Mahasiswa)"
                    type="text"
                    name="student_or_employee_id"
                    value="{{ old('student_or_employee_id') }}"
                    pattern="[0-9]+"
                    title="NIM harus berupa angka"
                />
                <span class="pointer-events-none absolute z-20 flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="size-5 transition-colors duration-200"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"
                        />
                    </svg>
                </span>
            </label>
            @error('student_or_employee_id')
                <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
            @enderror

            {{-- Study Program --}}
            <label class="relative mt-4 flex">
                <select
                    name="study_program_id"
                    class="form-select peer w-full rounded-lg border border-slate-300 bg-white px-3 py-2 pl-9 text-slate-400/70 focus:text-navy-300 [&:not(:focus):has(option:checked:not([value='']))]:text-navy-300  placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent"
                >
                    <option value="" class="">Pilih Program Studi</option>
                    @foreach($studyPrograms as $id => $degreeName)
                        <option value="{{ $id }}" {{ old('study_program_id') == $id ? 'selected' : '' }}>
                            {{ $degreeName }}
                        </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute z-20 flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="size-5 transition-colors duration-200"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                    >
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 14l9-5-9-5-9 5 9 5z"
                        />
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                        />
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"
                        />
                    </svg>
                </span>
            </label>
            @error('study_program_id')
                <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
            @enderror

            {{-- Password --}}
            <label class="mt-4 block">
                <span class="relative flex password-wrapper">
                    <input
                        name="password"
                        class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 pr-10 placeholder:text-slate-400/70 hover:z-10 hover:border-slate-400 focus:z-10 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                        placeholder="Password (min. 8 karakter)"
                        type="password"
                        id="password"
                    />
                    <span class="pointer-events-none absolute z-20 left-0 flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5 transition-colors duration-200"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                    </span>
                    <button
                        type="button"
                        data-toggle-password="true"
                        class="absolute right-0 flex h-full w-10 items-center justify-center text-slate-400 hover:text-primary focus:text-primary dark:text-navy-300 dark:hover:text-accent dark:focus:text-accent z-30"
                        title="Toggle Password Visibility"
                    >
                        <i class="fa fa-eye transition-colors duration-200"></i>
                    </button>
                </span>
                @error('password')
                    <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                @enderror
            </label>

            {{-- Password Confirmation --}}
            <label class="mt-4 block">
                <span class="relative flex password-wrapper">
                    <input
                        name="password_confirmation"
                        class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 pr-10 placeholder:text-slate-400/70 hover:z-10 hover:border-slate-400 focus:z-10 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                        placeholder="Ulangi Password"
                        type="password"
                        id="password_confirmation"
                    />
                    <span class="pointer-events-none absolute z-20 left-0 flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5 transition-colors duration-200"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                    </span>
                    <button
                        type="button"
                        data-toggle-password="true"
                        class="absolute right-0 flex h-full w-10 items-center justify-center text-slate-400 hover:text-primary focus:text-primary dark:text-navy-300 dark:hover:text-accent dark:focus:text-accent z-30"
                        title="Toggle Password Visibility"
                    >
                        <i class="fa fa-eye transition-colors duration-200"></i>
                    </button>
                </span>
                @error('password_confirmation')
                    <span class="text-tiny-plus text-error mt-1 ms-1 block">{{ $message }}</span>
                @enderror
            </label>

{{--            <div class="mt-4 flex items-center space-x-2">--}}
{{--                <input class="form-checkbox is-basic size-5 rounded-sm border-slate-400/70 checked:border-primary checked:bg-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:border-accent dark:checked:bg-accent dark:hover:border-accent dark:focus:border-accent"--}}
{{--                       type="checkbox"/>--}}
{{--                <p class="line-clamp-1">--}}
{{--                    I agree with--}}
{{--                    <a href="#"--}}
{{--                       class="text-slate-400 hover:underline dark:text-navy-300">--}}
{{--                        privacy policy--}}
{{--                    </a>--}}
{{--                </p>--}}
{{--            </div>--}}
            <button type="submit" class="btn mt-5 w-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                Sign Up
            </button>
            <div class="mt-4 text-center text-xs-plus">
                <p class="line-clamp-1">
                    <span>Already have an account? </span>
                    <a class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                       href="{{ route('login') }}"
                    >
                        Sign In
                    </a>
                </p>
            </div>
        </div>
    </form>
</x-layouts.guest>