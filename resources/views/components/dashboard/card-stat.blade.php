@props([
    'icon' => 'fa-chart-line',
    'label' => 'Label',
    'value' => '0',
    'subtitle' => null,
    'color' => 'primary', // primary, success, warning, danger, info
    'trend' => null, // '+10%' or '-5%'
    'trendUp' => true,
])

@php
    $colorClasses = [
        'primary' => 'bg-primary/10 text-primary dark:bg-accent-light/15 dark:text-accent-light',
        'success' => 'bg-success/10 text-success',
        'warning' => 'bg-warning/10 text-warning',
        'danger' => 'bg-error/10 text-error',
        'info' => 'bg-info/10 text-info',
    ];

    $iconColorClass = $colorClasses[$color] ?? $colorClasses['primary'];
@endphp

<div class="card">
    <div class="p-4">
        <div class="flex items-center justify-between space-x-4">
            {{-- Icon --}}
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg {{ $iconColorClass }}">
                <i class="fa-solid {{ $icon }} text-xl"></i>
            </div>

            {{-- Stats --}}
            <div class="flex flex-col items-end">
                <p class="text-xs+ text-slate-400 dark:text-navy-300">{{ $label }}</p>
                <h3 class="text-2xl font-semibold text-slate-700 dark:text-navy-100" id="{{ Str::slug($label) }}-count">
                    {{ $value }}
                </h3>

                @if($subtitle)
                    <p class="text-xs text-slate-400 dark:text-navy-300 mt-1">{{ $subtitle }}</p>
                @endif

                @if($trend)
                    <div class="mt-1 flex items-center space-x-1">
                        <i class="fa-solid {{ $trendUp ? 'fa-arrow-up text-success' : 'fa-arrow-down text-error' }} text-xs"></i>
                        <span class="text-xs {{ $trendUp ? 'text-success' : 'text-error' }}">{{ $trend }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>