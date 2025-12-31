<x-layouts.app title="Dashboard Administrator" :hasPanel="false">

    {{-- Welcome Card --}}
    <x-dashboard.welcome-card :user="$user" role="System Administrator" />

    {{-- Summary Cards --}}
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <x-dashboard.card-stat icon="fa-users" label="Total Users" :value="$stats['summary']['total_users']" color="primary" />
        <x-dashboard.card-stat icon="fa-file-alt" label="Total Letters" :value="$stats['summary']['total_letters']" color="info" />
        <x-dashboard.card-stat icon="fa-clock" label="Pending" :value="$stats['summary']['pending']" color="warning" />
        <x-dashboard.card-stat icon="fa-calendar-day" label="Today" :value="$stats['summary']['today']" color="success" />
        <x-dashboard.card-stat icon="fa-calendar-check" label="This Month" :value="$stats['summary']['this_month']" color="primary" />
        <x-dashboard.card-stat icon="fa-exclamation-triangle" label="Failed Jobs" :value="$systemHealth['failed_jobs']" :color="$systemHealth['failed_jobs'] > 0 ? 'danger' : 'success'" />
    </div>

    {{-- System Health Card --}}
    <div class="card mt-4">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-navy-500">
            <h3 class="font-medium text-slate-700 dark:text-navy-100">🏥 System Health</h3>
            <button onclick="location.reload()" class="btn size-8 rounded-full p-0 hover:bg-slate-300/20">
                <i class="fa-solid fa-refresh"></i>
            </button>
        </div>
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

    {{-- Charts --}}
    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-dashboard.card-chart title="📊 System Activity (30 Days)" chartId="activity-chart" />
        <x-dashboard.card-chart title="👥 User Distribution" chartId="user-dist-chart" />
    </div>

    {{-- Recent Activity Log --}}
    <div class="card mt-4">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-navy-500">
            <h3 class="font-medium text-slate-700 dark:text-navy-100">📋 Recent Activity Log</h3>
            <div class="flex space-x-2">
                <select class="form-select h-8 rounded-lg border-slate-300 px-3 py-1 text-xs+ dark:border-navy-450 dark:bg-navy-700">
                    <option>All</option>
                    <option>Submit</option>
                    <option>Approve</option>
                    <option>Reject</option>
                </select>
            </div>
        </div>

        <div class="scrollbar-sm max-h-96 overflow-y-auto">
            <table class="w-full text-left text-sm">
                <thead class="sticky top-0 bg-slate-50 dark:bg-navy-800">
                <tr>
                    <th class="px-4 py-2 font-medium text-slate-600 dark:text-navy-200">Time</th>
                    <th class="px-4 py-2 font-medium text-slate-600 dark:text-navy-200">User</th>
                    <th class="px-4 py-2 font-medium text-slate-600 dark:text-navy-200">Action</th>
                    <th class="px-4 py-2 font-medium text-slate-600 dark:text-navy-200">Letter Type</th>
                    <th class="px-4 py-2 font-medium text-slate-600 dark:text-navy-200">Status</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                @foreach($activityLog as $log)
                    <tr class="hover:bg-slate-50 dark:hover:bg-navy-600">
                        <td class="whitespace-nowrap px-4 py-2 text-xs text-slate-400">
                            {{ $log->created_at->diffForHumans() }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-2">
                            <div>
                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                    {{ $log->student->profile->full_name }}
                                </p>
                                <p class="text-xs text-slate-400">{{ $log->student->nim }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                                <span class="badge rounded-full bg-primary/10 text-primary">
                                    Submit
                                </span>
                        </td>
                        <td class="px-4 py-2 text-slate-600 dark:text-navy-200">
                            {{ $log->letter_type->label() }}
                        </td>
                        <td class="px-4 py-2">
                            @if($log->status === 'completed')
                                <span class="badge rounded-full bg-success/10 text-success">Completed</span>
                            @elseif($log->status === 'rejected')
                                <span class="badge rounded-full bg-error/10 text-error">Rejected</span>
                            @else
                                <span class="badge rounded-full bg-warning/10 text-warning">In Progress</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Scripts --}}
    <x-slot:scripts>
        <script>
            const onLoad = () => {
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