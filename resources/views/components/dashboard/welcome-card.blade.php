@props([
    'user' => 'User',
    'image' => asset('assets/images/illustrations/doctor.svg'), // Default image
    'subtitle' => 'Have a nice day at work',
    'color' => 'from-blue-500 to-blue-600'
])

@php
    $hour = now()->hour;
    $greeting = match(true) {
        $hour >= 5 && $hour < 11 => 'Selamat pagi',
        $hour >= 11 && $hour < 15 => 'Selamat siang',
        $hour >= 15 && $hour < 18 => 'Selamat sore',
        default => 'Selamat malam',
    };
    $userName = $user->profile->full_name ?? $user->name;
    $displayTitle = $title ?? "$greeting, <span class='font-semibold'>$userName</span>";
@endphp

<div class="card mt-12 bg-gradient-to-r {{ $color }} p-5 sm:mt-0 sm:flex-row">
    {{-- Gambar Ilustrasi --}}
    <div class="flex justify-center sm:order-last">
        <img class="-mt-16 h-40 sm:mt-0 lg:h-44 object-contain" src="{{ $image }}" alt="illustration" />
    </div>

    {{-- Konten Teks --}}
    <div class="mt-2 flex-1 items-center pt-2 text-center text-white sm:mt-0 sm:text-left">
        <h3 class="text-xl">
            {!! $displayTitle !!}
        </h3>

        <p class="mt-2 leading-relaxed text-white/90">
            {{ $subtitle }}
        </p>

        {{-- Slot untuk Info Tambahan (Bisa teks progres, dsb) --}}
        @if(isset($extraInfo))
            <div class="flex flex-col mt-1">
                {{ $extraInfo }}
            </div>
        @endif

        {{-- Slot untuk Tombol-Tombol Akses --}}
        @if(isset($action))
            <div class="mt-6 flex flex-wrap justify-center gap-2 sm:justify-start">
                {{ $action }}
            </div>
        @endif
    </div>
</div>