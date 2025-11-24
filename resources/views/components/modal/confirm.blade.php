<div id="{{ $id }}"
     class="modal {{ $transition }} fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden py-6 px-4 sm:px-5"
     role="dialog">

    <div class="modal-overlay absolute inset-0 bg-slate-900/60"></div>

    <div class="modal-content scrollbar-sm relative flex max-w-md flex-col overflow-y-auto rounded-lg bg-white py-6 text-center dark:bg-navy-700">

        <div class="px-4 sm:px-12">
            <h3 class="text-lg text-slate-800 dark:text-navy-50">
                {{ $title }}
            </h3>

            <p class="mt-2 text-slate-500 dark:text-navy-200">
                {{ $message }}
            </p>

            {{ $slot }}
        </div>

        <div class="space-x-3 mt-6">
            <button
                data-close-modal
                type="button"
                class="btn min-w-[7rem] rounded-full border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                {{ $cancelText }}
            </button>

            {{-- Tombol Konfirmasi: Menerima atribut 'form' untuk submission HTML5 --}}
            <button
                type="submit"
                class="btn min-w-[7rem] rounded-full font-medium text-white {{ $confirmClass }}"
                {{ $attributes }}>
                {{ $confirmText }}
            </button>
        </div>
    </div>
</div>