{{--<a href="{{ $route }}"--}}
{{--   @if($hasPanel) data-has-panel="true" @endif--}}
{{--   data-tooltip="{{ $label }}"--}}
{{--   data-placement="right"--}}
{{--   class="tooltip-main-sidebar flex size-11 items-center justify-center rounded-lg {{ $classes }}"--}}
{{-->--}}
{{--   {{ $slot }}--}}
{{--</a>--}}

@props([
    'route' => '#',
    'label' => '',
    'active' => false,
    'hasPanel' => false,
])

@php
   $classes = $active
       ? 'bg-primary/10 text-primary dark:bg-accent-light/10 dark:text-accent-light'
       : 'text-slate-500 hover:bg-slate-150 hover:text-slate-800 dark:text-navy-200 dark:hover:bg-navy-500/20 dark:hover:text-navy-50';
@endphp

<a href="{{ $route }}"
   @if($hasPanel) data-has-panel="true" @endif
   data-tooltip="{{ $label }}"
   data-placement="right"
   data-nav-link="main" {{-- TAMBAHKAN INI --}}
   class="tooltip-main-sidebar flex size-11 items-center justify-center rounded-lg outline-hidden transition-all duration-200 {{ $classes }}"
>
   {{ $slot }}
</a>