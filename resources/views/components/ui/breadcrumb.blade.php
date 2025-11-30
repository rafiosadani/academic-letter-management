<div class="mt-6">
    <ul class="flex flex-wrap items-center space-x-2">

        @foreach ($items as $index => $item)
            <li class="flex items-center space-x-2">

                {{-- Jika bukan item terakhir --}}
                @if($index < count($items) - 1)
                    <a
                            href="{{ $item['url'] ?? '#' }}"
                            class="text-primary transition-colors hover:text-primary-focus
                               dark:text-accent-light dark:hover:text-accent"
                    >
                        {{ $item['label'] }}
                    </a>

                    <!-- Arrow -->
                    <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                    >
                        <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                        />
                    </svg>

                @else
                    {{-- Item terakhir (active) --}}
                    <span>{{ $item['label'] }}</span>
                @endif

            </li>
        @endforeach

    </ul>
</div>
