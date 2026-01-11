@props(['hasPanel' => false])

<div class="sidebar print:hidden">

    <!-- Main Sidebar -->
    {{ $main }}

    <!-- Sidebar Panel (Conditional) -->
    @if($hasPanel)
        {{ $panel ?? '' }}
    @endif

</div>