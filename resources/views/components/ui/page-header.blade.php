@props([
    'title',
    'description' => null,
    'icon' => null,
    'backUrl' => null,
    'actions' => null
])

<div class="flex flex-col items-start justify-between space-y-4 py-5 sm:flex-row sm:items-center sm:space-y-0 lg:py-6">
    <div class="flex items-center space-x-3">
        @if($backUrl)
            <a href="{{ $backUrl }}" class="btn size-6 sm:size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif

        @if($icon)
            {!! $icon !!}
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 sm:size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        @endif

        <div>
            <h2 class="text-base sm:text-xl font-medium text-slate-700 line-clamp-1 dark:text-navy-50">
                {{ $title }}
            </h2>
            @if($description)
                <p class="text-xs-plus text-slate-500 dark:text-navy-300">{{ $description }}</p>
            @endif
        </div>
    </div>

    @if($actions)
        <div class="flex justify-center space-x-2">
            {{ $actions }}
        </div>
    @endif
</div>
