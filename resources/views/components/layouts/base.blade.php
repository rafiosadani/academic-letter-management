<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags  -->
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }} - Layanan Akademik FV UB</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>

    <!-- Dark Mode Script -->
    <script>
        /**
         * THIS SCRIPT REQUIRED FOR PREVENT FLICKERING IN SOME BROWSERS
         */
        localStorage.getItem("dark-mode") === "dark" &&
        document.documentElement.classList.add("dark");
    </script>

    <!-- Global CSS -->
    @vite('resources/css/app.css')
{{--    @vite('resources/lineone/css/app.css')--}}

    <!-- Additional Styles -->
    {!! $styles ?? '' !!}
</head>

<body class="{{ $bodyClass ?? 'is-header-blur' }}
    @if(!$hasPanel) no-panel @endif
    @if($hasPanel) js-panel-default-open @endif
">

{{-- App Preloader --}}
<x-ui.preloader/>

{{-- Main Content --}}
{{ $slot }}

{{--  Global JS  --}}
@vite('resources/js/app.js')

{{-- Third-party JS --}}
@vite('resources/lineone/js/app.js')
@vite('resources/lineone/js/libs/components.js')

{{-- ============================= --}}
{{--   RENDER MODAL ALERT DINAMIS   --}}
{{-- ============================= --}}
@if(session()->has('modal_alert'))
    @php $m = session('modal_alert'); @endphp
    <x-modal.alert
            id="{{ $m['id'] }}"
            type="{{ $m['type'] }}"
            title="{{ $m['title'] }}"
            message="{{ $m['message'] }}"
            buttonText="{{ $m['buttonText'] }}"
            :showButton="$m['showButton']"
    />
@endif

{{-- ============================= --}}
{{--     FLASH DATA UNTUK JS       --}}
{{-- ============================= --}}
@if(session()->has('alert_show_id'))
    <div id="session-alert-data"
         class="hidden"
         data-json="{{ json_encode(['alert_show_id' => session('alert_show_id')]) }}">
    </div>
@endif

{{-- Toastify dengan Session Flash Data --}}
@if(session()->has('notification_data'))
    <div id="session-notification-data"
         class="hidden"
         data-json="{{ json_encode(session('notification_data')) }}">
    </div>
@endif

{{-- Additional Scripts --}}
{!! $scripts ?? '' !!}

</body>
</html>
