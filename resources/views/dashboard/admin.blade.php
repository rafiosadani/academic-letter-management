<x-layouts.app title="Dashboard Administrator" :hasPanel="false">

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            {{-- Welcome Card --}}
            <x-dashboard.welcome-card
                :user="$user"
{{--                    image="{{ asset('images/illustrations/admin.svg') }}"--}}
                    color="from-slate-700 to-slate-800"
            >
                <x-slot:extraInfo>
                    <div>
                        <span>Kesehatan sistem dan manajemen data dalam kendali Anda.</span>
                    </div>
                    <div>
                        Seluruh komponen sistem dalam <span class="font-semibold">kondisi optimal!</span>
                    </div>
                </x-slot:extraInfo>
                <x-slot:action>
                    <a href="{{ route('master.users.index') }}"
                       class="btn border border-white/10 bg-white/20 text-white hover:bg-white/30">
                        Manajemen Data Akademik
                    </a>
                    <a href="{{ route('settings.general.edit') }}"
                       class="btn border border-white/10 bg-white/20 text-white hover:bg-white/30">
                        Pengaturan Aplikasi
                    </a>
                </x-slot:action>
            </x-dashboard.welcome-card>

            {{-- Filter Dashboard --}}
            <div class="mt-4 sm:mt-5 lg:mt-6 flex items-center justify-between">
                <h2 class="text-base font-medium text-slate-700 dark:text-navy-100">
                    Dashboard Overview
                </h2>
                <x-dashboard.filter-dropdown :currentFilter="$filter ?? 'this_month'" />
            </div>

            {{-- Summary Cards --}}
            <div class="mt-4 sm:mt-5 lg:mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
                <x-dashboard.card-stat icon="fa-users" label="Total Users" :value="$stats['summary']['total_users']" color="primary" />
                <x-dashboard.card-stat icon="fa-file-alt" label="Total Letters" :value="$stats['summary']['total_letters']" color="info" />
                <x-dashboard.card-stat icon="fa-clock" label="Pending" :value="$stats['summary']['pending']" color="warning" />
                <x-dashboard.card-stat icon="fa-calendar-day" label="Today" :value="$stats['summary']['today']" color="success" />
                <x-dashboard.card-stat icon="fa-calendar-check" label="This Month" :value="$stats['summary']['this_month']" color="primary" />
                <x-dashboard.card-stat icon="fa-exclamation-triangle" label="Failed Jobs" :value="$systemHealth['failed_jobs']" :color="$systemHealth['failed_jobs'] > 0 ? 'danger' : 'success'" />
            </div>

            {{-- System Health Card --}}
            <div class="mt-4 sm:mt-5 lg:mt-6">
                <div class="flex h-8 items-center justify-between">
                    <h2 class="text-base font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        System Health
                    </h2>
                    <button
                            onclick="location.reload()"
                            class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                            title="Refresh Status"
                    >
                        <i class="fa-solid fa-refresh"></i>
                    </button>
                </div>
                <div class="card mt-3">
                    <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $systemHealth['queue_status'] === 'running' ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }}">
                                <i class="fa-solid fa-server"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Queue Worker</p>
                                <p class="font-medium {{ $systemHealth['queue_status'] === 'running' ? 'text-success' : 'text-error' }}">
                                    {{ ucfirst($systemHealth['queue_status']) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $systemHealth['scheduler_status'] === 'active' ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }}">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Scheduler</p>
                                <p class="font-medium {{ $systemHealth['scheduler_status'] === 'active' ? 'text-success' : 'text-error' }}">
                                    {{ ucfirst($systemHealth['scheduler_status']) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $systemHealth['database_status'] === 'healthy' ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }}">
                                <i class="fa-solid fa-database"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Database</p>
                                <p class="font-medium {{ $systemHealth['database_status'] === 'healthy' ? 'text-success' : 'text-error' }}">
                                    {{ ucfirst($systemHealth['database_status']) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $systemHealth['storage_used_percent'] < 80 ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }}">
                                <i class="fa-solid fa-hard-drive"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Storage</p>
                                <p class="font-medium {{ $systemHealth['storage_used_percent'] < 80 ? 'text-success' : 'text-warning' }}">
                                    {{ $systemHealth['storage_used_percent'] }}% used
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($systemHealth['failed_jobs'] > 0)
                        <div class="border-t border-slate-200 bg-error/5 px-4 py-3 dark:border-navy-500">
                            <div class="flex items-center space-x-2 text-error">
                                <i class="fa-solid fa-exclamation-circle"></i>
                                <p class="text-sm">
                                    <strong>{{ $systemHealth['failed_jobs'] }} failed jobs</strong> detected.
                                    <a href="#" class="underline">View Details →</a>
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Charts --}}
            <div class="mt-4 sm:mt-5 lg:mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <x-dashboard.card-chart title="System Activity (30 Days)" chartId="activity-chart" />
                <x-dashboard.card-chart title="User Distribution" chartId="user-dist-chart" />
            </div>

            {{-- Recent Activity Log --}}
            <div id="activity-log-section" class="mt-4 sm:mt-5 lg:mt-6">
                <div class="flex h-12 items-center justify-between">
                    <h2 class="text-base font-medium tracking-wide text-slate-700 dark:text-navy-100">
                        Recent Activity Log
                    </h2>

                    {{-- Filter Dropdown di Kanan dengan Style Tombol Tetap --}}
                    <div id="activity-log-filter-menu" class="inline-flex">
                        <button class="popper-ref btn h-8 space-x-2 rounded-lg bg-slate-150 px-3 text-xs font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                            <i class="fa-solid fa-filter text-tiny"></i>
                            <span>
                                @switch(request('action'))
                                    @case('submit') Submit @break
                                    @case('approve') Approve @break
                                    @case('reject') Reject @break
                                    @case('cancel') Cancel @break
                                    @default All Actions
                                @endswitch
                            </span>
                            <i class="fa-solid fa-chevron-down text-tiny-plus"></i>
                        </button>

                        <div class="popper-root">
                            <div class="popper-box rounded-md border border-slate-150 bg-white py-1.5 font-inter dark:border-navy-500 dark:bg-navy-700 shadow-soft">
                                <ul style="min-width: 160px;">
                                    @php
                                        $actionList = [
                                            null => 'All Actions',
                                            'submit' => 'Submit',
                                            'approve' => 'Approve',
                                            'reject' => 'Reject',
                                            'cancel' => 'Cancel'
                                        ];
                                    @endphp

                                    @foreach($actionList as $key => $label)
                                        <li>
                                            <a href="{{ request()->fullUrlWithQuery(['action' => $key]) . '#activity-log-section' }}"
                                               class="flex h-8 items-center px-3 pr-8 text-xs+ font-medium tracking-wide outline-hidden transition-all hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-navy-600 {{ request('action') === $key || (is_null($key) && !request('action')) ? 'text-primary dark:text-accent-light' : '' }}">
                                                <div class="flex w-5 items-center">
                                                    @if(request('action') === $key || (is_null($key) && !request('action')))
                                                        <i class="fa-solid fa-check text-xs"></i>
                                                    @endif
                                                </div>
                                                {{ $label }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="is-scrollbar-hidden max-h-96 overflow-y-auto min-w-full overflow-x-auto border border-slate-200 dark:border-navy-500 rounded-lg">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                        <tr class="text-xs">
                            {{-- Tambahkan sticky top-0 dan z-10 pada setiap th --}}
                            <th class="sticky top-0 z-10 whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                Waktu Aktivitas
                            </th>
                            <th class="sticky top-0 z-10 whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                User / Mahasiswa
                            </th>
                            <th class="sticky top-0 z-10 whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                Aksi
                            </th>
                            <th class="sticky top-0 z-10 whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                Jenis Surat
                            </th>
                            <th class="sticky top-0 z-10 whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                Status Akhir
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($activityLog as $log)
                            <tr class="text-xs border-y border-transparent {{ !$loop->last ? 'border-b-slate-200 dark:border-b-navy-500' : '' }}">

                                {{-- 1. Waktu Aktivitas --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">
                                            {{ $log->created_at_full }}
                                        </span>
                                        <span class="text-tiny text-slate-400 dark:text-navy-300 italic">
                                            ({{ $log->created_at->diffForHumans() }})
                                        </span>
                                    </div>
                                </td>

                                {{-- 2. User --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar size-9 shrink-0">
                                            <img src="{{ $log->student->profile->photo_url ?? asset('images/default-avatar.png') }}" class="rounded-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-tiny-plus font-medium text-slate-700 dark:text-navy-100 leading-tight">
                                                {{ $log->student->profile->full_name ?? 'User' }}
                                            </p>
                                            <p class="text-tiny font-medium text-slate-500 dark:text-navy-300">
                                                {{ $log->student->profile->student_or_employee_id }}
                                            </p>

                                            <p class="text-tiny text-primary dark:text-accent-light">
                                                {{ $log->student->profile->studyProgram->degree_name ?? 'Program Studi' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- 3. Aksi (Kolom Baru) --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    @php
                                        $statusMap = [
                                            'in_progress'         => ['label' => 'New Submit', 'color' => 'primary'],
                                            'resubmitted'         => ['label' => 'Re-submitted', 'color' => 'info'],
                                            'approved'            => ['label' => 'Approved', 'color' => 'success'],
                                            'external_processing' => ['label' => 'Sent to SKAK', 'color' => 'secondary'],
                                            'completed'           => ['label' => 'Published', 'color' => 'success'],
                                            'rejected'            => ['label' => 'Rejected', 'color' => 'error'],
                                            'cancelled'           => ['label' => 'Cancelled', 'color' => 'warning'],
                                        ];

                                        $currentAction = $statusMap[$log->status] ?? ['label' => 'Activity', 'color' => 'slate'];
                                    @endphp

                                    <span class="badge rounded-full bg-{{ $currentAction['color'] }}/10 text-{{ $currentAction['color'] }}">
                                        {{ $currentAction['label'] }}
                                    </span>
                                </td>

                                {{-- 4. Jenis Surat --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span class="text-slate-700 dark:text-navy-100 font-medium">
                                        {{ $log->letter_type->label() }}
                                    </span>
                                </td>

                                {{-- 5. Status Akhir --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <span class="badge bg-{{ $log->status_badge }}/10 text-{{ $log->status_badge }} text-tiny border border-{{ $log->status_badge }} inline-flex items-center space-x-1.5">
                                        <i class="{{ $log->status_icon }} {{ in_array($log->status, ['in_progress','external_processing']) ? 'animate-spin' : '' }}"></i>
                                        <span>{{ $log->status_label }}</span>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="whitespace-nowrap px-4 py-12 text-center text-slate-400">Belum ada aktivitas.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <x-slot:scripts>
        <script>
            function updateQueryParam(key, value) {
                const url = new URL(window.location.href);
                if (value === '') {
                    url.searchParams.delete(key);
                } else {
                    url.searchParams.set(key, value);
                }
                return url.toString();
            }

            const onLoad = () => {
                // ============================================
                // FILTER DROPDOWN - POPPER.JS INITIALIZATION
                // ============================================
                const dropdownConfig = {
                    placement: "bottom-end",
                    modifiers: [
                        {
                            name: "offset",
                            options: {
                                offset: [0, 4],
                            },
                        },
                    ],
                };

                new Popper(
                    "#dashboard-filter-menu",
                    ".popper-ref",
                    ".popper-root",
                    dropdownConfig
                );

                new Popper(
                    "#activity-log-filter-menu",
                    ".popper-ref",
                    ".popper-root",
                    dropdownConfig
                );

                // System Activity Chart (Area)
                const activityConfig = {
                    series: @json($usageData['series']),
                    chart: {
                        type: 'area',
                        height: 300,
                        parentHeightOffset: 0,
                        toolbar: { show: false },
                        zoom: { enabled: false }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            opacityFrom: 0.4,
                            opacityTo: 0.1
                        }
                    },
                    colors: ['#4467EF', '#10b981'],
                    xaxis: {
                        categories: @json($usageData['categories']),
                        labels: {
                            rotate: -45,
                            style: { fontSize: '10px' }
                        }
                    },
                    yaxis: {
                        title: { text: 'Count' }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    },
                    grid: { padding: { top: 0, bottom: 0 } },
                    responsive: [{
                        breakpoint: 768,
                        options: { chart: { height: 250 } }
                    }]
                };

                new ApexCharts(document.querySelector("#activity-chart"), activityConfig).render();

                // User Distribution Chart (Pie)
                const userDistConfig = {
                    series: @json($userDistribution['data']),
                    chart: {
                        type: 'donut',
                        height: 300,
                        parentHeightOffset: 0
                    },
                    labels: @json($userDistribution['labels']),
                    colors: ['#4467EF', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    legend: {
                        position: 'bottom'
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '60%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Users',
                                        fontSize: '14px',
                                        fontWeight: 600
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return Math.round(val) + '%';
                        }
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: { chart: { height: 250 } }
                    }]
                };

                new ApexCharts(document.querySelector("#user-dist-chart"), userDistConfig).render();

                // Enhanced polling with system health
                setInterval(() => {
                    fetch('{{ route("dashboard.data") }}')
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('total-users-count').textContent = data.total_users;
                            document.getElementById('total-letters-count').textContent = data.total_letters;
                            document.getElementById('pending-count').textContent = data.pending;
                            document.getElementById('today-count').textContent = data.today;
                            document.getElementById('this-month-count').textContent = data.this_month;
                            document.getElementById('failed-jobs-count').textContent = data.failed_jobs;
                        })
                        .catch(err => console.error('Polling error:', err));
                }, 30000);
            };

            window.addEventListener("app:mounted", onLoad, { once: true });
        </script>
    </x-slot:scripts>

</x-layouts.app>