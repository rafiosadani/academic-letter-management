@if ($paginator->hasPages())
    <div class="flex justify-end">
        <ol class="pagination space-x-1.5 flex items-center">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span
                        class="flex size-8 items-center justify-center rounded-lg bg-slate-150 text-slate-400 dark:bg-navy-500 dark:text-navy-300 cursor-not-allowed"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="flex size-8 items-center justify-center rounded-lg bg-slate-150 text-slate-500 transition-colors hover:bg-slate-300 dark:bg-navy-500 dark:text-navy-200 dark:hover:bg-navy-450">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)

                {{-- Dots --}}
                @if (is_string($element))
                    <li>
                        <span class="text-slate-500 dark:text-navy-200">...</span>
                    </li>
                @endif

                {{-- Array of links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)

                        {{-- Active Page --}}
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span
                                        class="flex h-8 min-w-[2rem] items-center justify-center rounded-lg bg-primary px-3 leading-tight text-xs-plus text-white dark:bg-accent">
                                    {{ $page }}
                                </span>
                            </li>

                            {{-- Normal Page --}}
                        @else
                            <li>
                                <a href="{{ $url }}"
                                   class="flex h-8 min-w-[2rem] items-center justify-center rounded-lg bg-slate-150 px-3 leading-tight text-xs-plus transition-colors hover:bg-slate-300 dark:bg-navy-500 dark:hover:bg-navy-450">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif

                    @endforeach
                @endif

            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="flex size-8 items-center justify-center rounded-lg bg-slate-150 text-slate-500 transition-colors hover:bg-slate-300 dark:bg-navy-500 dark:text-navy-200 dark:hover:bg-navy-450">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </li>
            @else
                <li>
                    <span
                            class="flex size-8 items-center justify-center rounded-lg bg-slate-150 text-slate-400 dark:bg-navy-500 dark:text-navy-300 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </li>
            @endif

        </ol>
    </div>
@endif
