<div id="{{ $id }}"
     class="modal fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
     role="dialog">

{{--    <div class="modal-overlay absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>--}}
    <div class="modal-overlay absolute inset-0 bg-slate-900/60"></div>

    <div class="modal-content scrollbar-sm relative flex w-full max-w-xl flex-col items-center overflow-y-auto rounded-lg bg-white px-4 py-10 text-center dark:bg-navy-700 sm:px-5">

        {!! $getDisplayIcon() !!}

        <div class="mt-4">
            <h2 class="text-2xl text-slate-700 dark:text-navy-100">
                {{ $title }}
            </h2>

            @if($message)
                <p class="mt-2 text-slate-500 dark:text-navy-200">
                    {{ $message }}
                </p>
            @endif

            {{ $slot }}

            @if ($showButton)
                <button
                    data-close-modal
                    type="button"
                    class="btn mt-6 font-medium text-white {{ $config['buttonClass'] }}">
                    {{ $buttonText }}
                </button>
            @endif
        </div>
    </div>
</div>