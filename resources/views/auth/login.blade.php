<x-layouts.guest title="Login Page"
                 cardTitle="Welcome Back"
                 cardSubtitle="Please sign in to continue">
    <form method="POST" action="{{ route('login.perform') }}">
        @csrf
        <div class="card mt-5 rounded-lg p-5 lg:p-7">
            <label class="block">
                <span>Email:</span>
                <span class="relative mt-1.5 flex">
                    <input
                        name="email"
                        value="{{ old('email') }}"
                        class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:z-10 hover:border-slate-400 focus:z-10 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                        placeholder="Enter Username"
                        type="email"
                    />
                    <span class="pointer-events-none absolute z-20 flex h-full w-10 items-center justify-center text-slate-400  peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="size-5 transition-colors duration-200"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                    </span>
                </span>
                @error('email')
                    <p class="mt-2 ms-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </label>
            <label class="mt-4 block">
                <span>Password:</span>
                <span class="relative mt-1.5 flex password-wrapper">
                    <input
                        name="password"
                        class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 pr-10 placeholder:text-slate-400/70 hover:z-10 hover:border-slate-400 focus:z-10 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                        placeholder="Enter Password"
                        type="password"
                    />
                    <span class="pointer-events-none absolute left-0 flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
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
                <p class="mt-2 ms-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </label>
            <div class="mt-4 flex items-center justify-between space-x-2">
                <label class="inline-flex items-center space-x-2">
                    <input
                        name="remember"
                        value="1"
                        type="checkbox"
                        class="form-checkbox is-basic size-5 rounded-sm border-slate-400/70 checked:border-primary checked:bg-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:border-accent dark:checked:bg-accent dark:hover:border-accent dark:focus:border-accent"
                        {{ old('remember') ? 'checked' : '' }}
                    />
                    <span class="line-clamp-1">Remember me</span>
                </label>
                <a href="#"
                   class="text-xs text-slate-400 transition-colors line-clamp-1 hover:text-slate-800 focus:text-slate-800 dark:text-navy-300 dark:hover:text-navy-100 dark:focus:text-navy-100"
                >
                    Forgot Password?
                </a>
            </div>
            <button
                type="submit"
                class="btn mt-5 w-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90"
            >
                Sign In
            </button>

            <div class="mt-4 text-center text-xs-plus">
                <p class="line-clamp-1">
                    <span>Dont have Account?</span>
                    <a href="{{ route('register') }}"
                       class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                    >
                        Create account
                    </a>
                </p>
            </div>
        </div>
    </form>

    {{--  Modal Alert  --}}
    <x-modal.alert
        id="alert-logout-success"
        type="success"
        title="Sesi Berakhir"
        message="Anda telah berhasil keluar dari sistem. Silakan masuk kembali."
        button-text="OK"
        :show-button="true"
    />
</x-layouts.guest>