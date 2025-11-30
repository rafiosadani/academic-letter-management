<div id="{{ $id }}"
     class="modal {{ $transition }} fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
     role="dialog"
     {{ $attributes->only(['data-open-on-error']) }}
>

    <div class="modal-overlay absolute inset-0 bg-slate-900/60"></div>

    <div class="modal-content relative flex w-full {{ $sizeClass }} origin-top flex-col overflow-hidden rounded-lg bg-white dark:bg-navy-700">

        <div class="flex items-center justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                {{ $title }}
            </h3>

            <button
                data-close-modal
                type="button"
                class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Menggunakan $htmlMethod yang sudah ditentukan di class --}}
        <form method="{{ $htmlMethod }}" action="{{ $action }}" {{ $attributes->except(['scrollable', 'size']) }}
            class="flex flex-col flex-1 min-h-0 overflow-hidden"
        >
            {{-- Tambahkan CSRF dan Method Spoofing jika method bukan GET --}}
            @if($method !== 'GET')
                @csrf
                @if($method !== 'POST')
                    @method($method)
                @endif
            @endif

            <div class="{{ $scrollable ? 'scrollbar-sm overflow-y-auto' : 'overflow-visible' }} flex-1 px-4 py-4 sm:px-5">
                {{ $slot }}
            </div>

            <div class="border-t border-slate-200 px-4 py-3 text-right dark:border-navy-500 sm:px-5">
                <div class="space-x-2">
                    <button
                        data-close-modal
                        type="button"
                        class="btn min-w-[7rem] rounded-full border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                        {{ $cancelText }}
                    </button>

                    <button
                        type="submit"
                        class="btn min-w-[7rem] rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                        {{ $submitText }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>