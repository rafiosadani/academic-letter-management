<a href="{{ $route }}"
   @if($hasPanel) data-has-panel="true" @endif
   data-tooltip="{{ $label }}"
   data-placement="right"
   class="tooltip-main-sidebar flex size-11 items-center justify-center rounded-lg {{ $classes }}">
    {{--    {!! $icon !!}--}}
    {{ $slot }}
</a>