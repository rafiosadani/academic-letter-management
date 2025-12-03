<x-layouts.app title="Detail Program Studi: {{ $studyProgram->name }}">
    <x-ui.breadcrumb
            title="Detail Program Studi"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Program Studi', 'url' => route('master.study-programs.index')],
            ['label' => 'Detail']
        ]"
    />

    <x-ui.page-header
            title="Detail Program Studi: {{ $studyProgram->name }}"
            description="Informasi lengkap tentang program studi"
            backUrl="{{ route('master.study-programs.index') }}"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </x-slot:icon>

        <x-slot:actions>
            <a href="{{ route('master.study-programs.edit', $studyProgram) }}"
               class="btn space-x-2 bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90"
            >
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit Program Studi</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
        {{-- Left Column - Main Info --}}
        <div class="col-span-12 lg:col-span-6">
            <div class="card p-4 sm:p-5 space-y-4">
                <div>
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                        Informasi Program Studi
                    </h3>
                </div>

                <div class="space-y-4">
                    {{-- Code --}}
                    <div>
                        <span class="text-xs text-slate-500 dark:text-navy-300">Kode Program Studi</span>
                        <p class="font-medium text-slate-700 dark:text-navy-100">{{ $studyProgram->code }}</p>
                    </div>

                    {{-- Name --}}
                    <div>
                        <span class="text-xs text-slate-500 dark:text-navy-300">Nama Program Studi</span>
                        <p class="font-medium text-slate-700 dark:text-navy-100">{{ $studyProgram->name }}</p>
                    </div>

                    {{-- Degree --}}
                    <div>
                        <span class="text-xs text-slate-500 dark:text-navy-300">Jenjang</span>
                        <div class="mt-1">
                            <span class="badge {{ $studyProgram->degree_badge_color }}">{{ $studyProgram->degree }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 dark:text-navy-300 mb-2">Nama Program Studi:</p>

                        <div class="space-y-1.5">
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-slate-500 dark:text-navy-300 w-24">Format Pendek</span>
                                <span class="text-xs font-medium text-slate-700 dark:text-navy-100">: {{ $studyProgram->degree_name }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-slate-500 dark:text-navy-300 w-24">Format Lengkap</span>
                                <span class="text-xs font-medium text-slate-700 dark:text-navy-100">: {{ $studyProgram->full_degree_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Stats & Timestamps --}}
        <div class="col-span-12 lg:col-span-6 space-y-4 sm:space-y-5">
            {{-- Statistics --}}
            <div class="card p-4 sm:p-5">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                        Statistik
                    </h3>
                </div>

                <div class="space-y-3">
                    @forelse($statistics as $stat)
                        <div class="rounded-lg bg-slate-100 dark:bg-navy-800 px-4 py-3 transition-all hover:bg-slate-150 dark:hover:bg-navy-700">
                            @if($stat['display_type'] === 'count')
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <i class="{{ $stat['icon'] }} {{ $stat['color'] }}"></i>
                                        <span class="text-sm text-slate-600 dark:text-navy-200">{{ $stat['label'] }}</span>
                                    </div>
                                    <span class="text-lg font-semibold {{ $stat['color'] }}">
                                        {{ number_format($stat['count'], 0, ',', '.') }}
                                    </span>
                                </div>
                            @elseif($stat['display_type'] === 'name')
                                <div class="flex items-start space-x-3">
                                    <i class="{{ $stat['icon'] }} {{ $stat['color'] }} mt-0.5"></i>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-sm text-slate-600 dark:text-navy-200 block mb-1">{{ $stat['label'] }}</span>

                                        @if($stat['users']->isNotEmpty())
                                            <div class="space-y-1.5">
                                                @foreach($stat['users'] as $user)
                                                    <div class="flex items-center space-x-2 bg-white dark:bg-navy-900 rounded-lg px-3 py-2">
                                                        {{-- Avatar/Initial --}}
                                                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                                            <span class="text-xs font-semibold">
                                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                                            </span>
                                                        </div>

                                                        {{-- User Info --}}
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100 truncate">
                                                                {{ $user->name }}
                                                            </p>
                                                            @if($user->email)
                                                                <p class="text-xs text-slate-500 dark:text-navy-300 truncate">
                                                                    {{ $user->email }}
                                                                </p>
                                                            @endif
                                                        </div>

                                                        {{-- Badge Active --}}
                                                        <span class="badge rounded-full bg-success/10 text-success text-xs">
                                                            <i class="fa-solid fa-circle text-[6px]"></i>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            {{-- Empty State --}}
                                            <div class="flex items-center space-x-2 text-slate-500 dark:text-navy-300">
                                                <i class="fa-solid fa-circle-info text-xs"></i>
                                                <span class="text-sm italic">{{ $stat['empty_text'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="flex size-16 items-center justify-center rounded-full bg-slate-100 dark:bg-navy-800 mb-3">
                                <i class="fa-solid fa-chart-simple text-2xl text-slate-400 dark:text-navy-300"></i>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-navy-300">
                                Belum ada data statistik
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Optional: Summary --}}
                @if(count($statistics) > 0)
                    @php
                        $totalCount = collect($statistics)
                            ->where('display_type', 'count')
                            ->sum('count');
                        $hasCountStats = collect($statistics)->where('display_type', 'count')->isNotEmpty();
                    @endphp

                    @if($hasCountStats)
                        <div class="mt-4 pt-4 px-0 border-t border-slate-200 dark:border-navy-600">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-700 dark:text-navy-100">
                                    Total (Jumlah)
                                </span>
                                <span class="text-lg font-bold text-slate-800 dark:text-navy-50">
                                    {{ number_format($totalCount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Timestamps --}}
            <div class="card p-4 sm:p-5">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-info/10 text-info">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                        Riwayat Data
                    </h3>
                </div>

                <div class="space-y-3 text-xs">
                    {{-- Created --}}
                    <div class="flex flex-col space-y-1 rounded-lg bg-slate-100 dark:bg-navy-800 px-4 py-3">
                        <div class="flex items-center space-x-2 text-slate-500 dark:text-navy-300">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span>Dibuat</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-700 dark:text-navy-100">{{ $studyProgram->created_by_name }}</span>
                            <span class="text-slate-600 dark:text-navy-200">{{ $studyProgram->created_at_formatted }}</span>
                        </div>
                    </div>

                    {{-- Updated --}}
                    @if($studyProgram->updated_at)
                        <div class="flex flex-col space-y-1 rounded-lg bg-slate-100 dark:bg-navy-800 px-4 py-3">
                            <div class="flex items-center space-x-2 text-slate-500 dark:text-navy-300">
                                <i class="fa-solid fa-edit"></i>
                                <span>Terakhir Diubah</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-700 dark:text-navy-100">{{ $studyProgram->updated_by_name }}</span>
                                <span class="text-slate-600 dark:text-navy-200">{{ $studyProgram->updated_at_formatted }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>