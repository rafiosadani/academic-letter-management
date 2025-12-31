@props([
    'user',
    'role' => 'User',
])

@php
    $hour = now()->hour;
    $greeting = 'Selamat Malam';
    $icon = 'fa-moon';

    if ($hour >= 5 && $hour < 11) {
        $greeting = 'Selamat Pagi';
        $icon = 'fa-sun';
    } elseif ($hour >= 11 && $hour < 15) {
        $greeting = 'Selamat Siang';
        $icon = 'fa-cloud-sun';
    } elseif ($hour >= 15 && $hour < 18) {
        $greeting = 'Selamat Sore';
        $icon = 'fa-cloud-sun';
    }

    $currentDate = now()->isoFormat('dddd, D MMMM YYYY');
@endphp

<div class="card bg-gradient-to-r from-primary to-accent">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-2 text-white/80 mb-1">
                    <i class="fa-solid {{ $icon }} text-lg"></i>
                    <span class="text-sm font-medium">{{ $currentDate }}</span>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1">
                    {{ $greeting }}, {{ $user->profile->full_name ?? $user->name }}! 👋
                </h2>
                <p class="text-white/90 text-sm">
                    {{ $role }}
                </p>
            </div>

            {{-- Optional: Add illustration or stats --}}
            <div class="hidden sm:block">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-white/20">
                    <i class="fa-solid fa-chart-line text-4xl text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>