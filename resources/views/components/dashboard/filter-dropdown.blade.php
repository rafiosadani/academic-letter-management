@props([
    'currentFilter' => 'this_month',
])

@php
    $filters = [
        'this_month' => 'Bulan Ini',
        'last_30_days' => '30 Hari Terakhir',
//        'this_semester' => 'Semester Ini',
        'all_time' => 'Semua Waktu',
    ];
@endphp

<div id="dashboard-filter-menu" class="inline-flex">
    <button class="popper-ref btn space-x-2 bg-slate-150 text-xs font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
        <i class="fa-solid fa-filter"></i>
        <span>{{ $filters[$currentFilter] ?? 'Filter' }}</span>
        <i class="fa-solid fa-chevron-down text-tiny"></i>
    </button>

    <div class="popper-root">
        <div class="popper-box rounded-md border border-slate-150 bg-white py-1.5 text-xs font-medium dark:border-navy-500 dark:bg-navy-700">
            <ul>
                @foreach($filters as $key => $label)
                    <li>
                        <a href="{{ request()->fullUrlWithQuery(['filter' => $key]) }}"
                           class="flex h-8 items-center px-3 pr-8 font-medium tracking-wide outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800 focus:bg-slate-100 focus:text-slate-800 dark:hover:bg-navy-600 dark:hover:text-navy-100 dark:focus:bg-navy-600 dark:focus:text-navy-100 {{ $currentFilter === $key ? 'text-primary dark:text-accent-light' : '' }}">
                            @if($currentFilter === $key)
                                <i class="fa-solid fa-check mr-2 text-tiny"></i>
                            @endif
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>