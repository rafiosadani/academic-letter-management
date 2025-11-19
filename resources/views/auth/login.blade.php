<x-layouts.guest title="Login Page"
                 cardTitle="Welcome Back"
                 cardSubtitle="Please sign in to continue">
    <div class="card mt-5 rounded-lg p-5 lg:p-7">
        <label class="block">
            <span>Username:</span>
            <span class="relative mt-1.5 flex">
                    <input class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:z-10 hover:border-slate-400 focus:z-10 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                           placeholder="Enter Username"
                           type="text"
                    />
                    <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
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
        </label>
        <label class="mt-4 block">
            <span>Password:</span>
            <span class="relative mt-1.5 flex">
                <input
                        class="form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:z-10 hover:border-slate-400 focus:z-10 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                        placeholder="Enter Password"
                        type="password"
                />
                <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
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
              </span>
        </label>
        <div class="mt-4 flex items-center justify-between space-x-2">
            <label class="inline-flex items-center space-x-2">
                <input
                        class="form-checkbox is-basic size-5 rounded-sm border-slate-400/70 checked:border-primary checked:bg-primary hover:border-primary focus:border-primary dark:border-navy-400 dark:checked:border-accent dark:checked:bg-accent dark:hover:border-accent dark:focus:border-accent"
                        type="checkbox"
                />
                <span class="line-clamp-1">Remember me</span>
            </label>
            <a href="#"
               class="text-xs text-slate-400 transition-colors line-clamp-1 hover:text-slate-800 focus:text-slate-800 dark:text-navy-300 dark:hover:text-navy-100 dark:focus:text-navy-100"
            >
                Forgot Password?
            </a>
        </div>
        <button class="btn mt-5 w-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
            Sign In
        </button>
        <div class="mt-4 text-center text-xs-plus">
            <p class="line-clamp-1">
                <span>Dont have Account?</span>
                <a href="pages-singup-1.html"
                   class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                >
                    Create account
                </a>
            </p>
        </div>
    </div>
</x-layouts.guest>