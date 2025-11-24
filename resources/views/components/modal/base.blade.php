<div id="{{ $id }}"
     class="modal fixed inset-0 z-[100] flex flex-col justify-center overflow-hidden px-4 py-6 sm:px-5 {{ $positionClass }}"
     role="dialog"
     aria-labelledby="{{ $id }}-title"
     aria-hidden="true">

    <!-- Overlay -->
    <div class="modal-overlay absolute inset-0 {{ $backdropClass }}"></div>

    <!-- Modal Content -->
    <div class="modal-content scrollbar-sm relative flex {{ $sizeClass }} w-full flex-col overflow-y-auto rounded-lg bg-white dark:bg-navy-700 {{ $attributes->get('class') }}">

        <!-- Header (Optional) -->
        @if($title || $closeButton)
            <div class="flex items-center justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                @if($title)
                    <h3 id="{{ $id }}-title" class="text-base font-medium text-slate-700 dark:text-navy-100">
                        {{ $title }}
                    </h3>
                @endif

                @if($closeButton)
                    <button
                            data-close-modal
                            type="button"
                            class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                            aria-label="Close modal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
        @endif

        <!-- Body -->
        <div class="px-4 py-4 sm:px-5">
            {{ $slot }}
        </div>

        <!-- Footer (Optional) -->
        @isset($footer)
            <div class="border-t border-slate-200 px-4 py-3 dark:border-navy-500 sm:px-5">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>