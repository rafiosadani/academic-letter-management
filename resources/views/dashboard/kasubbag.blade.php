<x-layouts.app title="Dashboard Kepala Subbagian Akademik" :hasPanel="false">

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            {{-- Welcome Card --}}
            <x-dashboard.welcome-card
                    :user="$user"
                    {{--            image="{{ asset('images/illustrations/manager.svg') }}"--}}
                    color="from-cyan-500 to-blue-600"
            >
                <x-slot:extraInfo>
                    <div>
                        <span>Pantau alur administrasi akademik dengan mudah.</span>
                    </div>
                    <div>
                        <span>Sistem siap membantu proses administrasi akademik Anda agar <span class="font-semibold">lebih cepat dan transparan.</span></span>
                    </div>
                </x-slot:extraInfo>
                <x-slot:action>
                    <a href="{{ route('approvals.index') }}" class="btn border border-white/10 bg-white/20 text-white hover:bg-white/30">Kelola Persetujuan Surat</a>
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
            <div class="mt-4 sm:mt-5 lg:mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-dashboard.card-stat
                        icon="fa-signature"
                        label="Menunggu Paraf"
                        :value="$stats['summary']['pending']"
                        subtitle="Menunggu persetujuan"
                        color="warning"
                />

                <x-dashboard.card-stat
                        icon="fa-check-double"
                        label="Paraf Hari Ini"
                        :value="$stats['summary']['approved_today']"
                        subtitle="Kerja bagus!"
                        color="success"
                />

                <x-dashboard.card-stat
                        icon="fa-calendar-check"
                        label="Total Bulan Ini"
                        :value="$stats['summary']['total_month']"
                        subtitle="Surat diproses"
                        color="primary"
                />

                <x-dashboard.card-stat
                        icon="fa-chart-pie"
                        label="Tingkat Keberhasilan"
                        :value="$stats['summary']['success_rate'] . '%'"
                        subtitle="Persentase persetujuan"
                        color="info"
                />
            </div>

            {{-- Charts Section --}}
{{--            <div class="mt-4 sm:mt-5 lg:mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">--}}
{{--                --}}{{-- Approval Flow Chart (Funnel) --}}
{{--                <x-dashboard.card-chart title="Approval Flow Fakultas" chartId="flow-chart" />--}}

{{--                --}}{{-- Bottleneck Analysis Chart (Horizontal Bar) --}}
{{--                <x-dashboard.card-chart title="Bottleneck Analysis" chartId="bottleneck-chart" />--}}
{{--            </div>--}}

            {{-- Avg Time Per Step Card --}}
{{--            @if(!empty($stats['performance']['avg_time_per_step']))--}}
{{--                <div class="card mt-4 sm:mt-5 lg:mt-6">--}}
{{--                    <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500">--}}
{{--                        <h3 class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                            ⏱️ Rata-rata Waktu Pemrosesan Per Step--}}
{{--                        </h3>--}}
{{--                    </div>--}}
{{--                    <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-3">--}}
{{--                        @foreach($stats['performance']['avg_time_per_step'] as $step => $time)--}}
{{--                            <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">--}}
{{--                                <p class="mb-1 text-xs font-medium uppercase text-slate-400 dark:text-navy-300">--}}
{{--                                    {{ ucfirst(str_replace('_', ' ', $step)) }}--}}
{{--                                </p>--}}
{{--                                <p class="text-3xl font-bold text-primary dark:text-accent-light">--}}
{{--                                    {{ $time }}--}}
{{--                                    <span class="text-sm font-normal text-slate-400">hari</span>--}}
{{--                                </p>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endif--}}

            {{-- Pending Approvals by Type --}}
{{--            <div class="card mt-4 sm:mt-5 lg:mt-6">--}}
{{--                <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500">--}}
{{--                    <h3 class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                        Surat Menunggu Paraf--}}
{{--                    </h3>--}}
{{--                </div>--}}

{{--                <div class="p-4">--}}
{{--                    @forelse($pendingApprovals as $letterType => $approvals)--}}
{{--                        <div class="mb-4 rounded-lg border border-slate-200 dark:border-navy-500">--}}
{{--                            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-navy-500 dark:bg-navy-600">--}}
{{--                                <div class="flex items-center space-x-2">--}}
{{--                                    <i class="fa-solid fa-folder text-primary"></i>--}}
{{--                                    <h4 class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                                        {{ \App\Enums\LetterType::from($letterType)->label() }}--}}
{{--                                    </h4>--}}
{{--                                    <span class="badge rounded-full bg-warning/10 text-warning">--}}
{{--                                {{ $approvals->count() }} surat--}}
{{--                            </span>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="divide-y divide-slate-200 dark:divide-navy-500">--}}
{{--                                @foreach($approvals as $approval)--}}
{{--                                    @php--}}
{{--                                            $totalHours = $approval->created_at->diffInHours(\Carbon\Carbon::now());--}}
{{--                                            $decimalDays = $totalHours / 24;--}}
{{--                                            $daysWaiting = (int) round($decimalDays);--}}
{{--                                            $daysWaiting = $daysWaiting < 1 ? 0 : $daysWaiting;--}}

{{--                                            $priority = $daysWaiting > 3 ? 'urgent' : ($daysWaiting > 1 ? 'normal' : 'recent');--}}
{{--                                    @endphp--}}
{{--                                    <div class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 dark:hover:bg-navy-600">--}}
{{--                                        <div class="flex-1">--}}
{{--                                            <p class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                                                {{ $approval->letterRequest->student->profile->full_name }}--}}
{{--                                            </p>--}}
{{--                                            <p class="text-xs text-slate-400">--}}
{{--                                                {{ $approval->letterRequest->student->nim }} Diajukan {{ $daysWaiting }} hari lalu--}}
{{--                                            </p>--}}
{{--                                        </div>--}}
{{--                                        <div class="flex items-center space-x-3">--}}
{{--                                            @if($priority === 'urgent')--}}
{{--                                                <span class="badge rounded-full bg-error/10 text-error">Urgent</span>--}}
{{--                                            @elseif($priority === 'normal')--}}
{{--                                                <span class="badge rounded-full bg-warning/10 text-warning">Normal</span>--}}
{{--                                            @else--}}
{{--                                                <span class="badge rounded-full bg-success/10 text-success">Recent</span>--}}
{{--                                            @endif--}}
{{--                                            <a href="{{ route('approvals.show', $approval) }}"--}}
{{--                                               class="badge bg-primary font-medium text-white hover:bg-primary-focus">--}}
{{--                                                Proses--}}
{{--                                            </a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endforeach--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @empty--}}
{{--                        <div class="rounded-lg border border-slate-200 p-8 text-center dark:border-navy-500">--}}
{{--                            <i class="fa-solid fa-check-circle mb-2 text-4xl text-success"></i>--}}
{{--                            <p class="text-slate-400">Semua surat sudah diproses!</p>--}}
{{--                        </div>--}}
{{--                    @endforelse--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
    </div>

    {{-- Scripts --}}
    <x-slot:scripts>
        <script>
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

                // Approval Flow Chart (Custom Funnel-like Bar)
                const flowConfig = {
                    series: [{
                        name: 'Jumlah Surat',
                        data: [
                            {{ $flowMetrics['submitted'] }},
                            {{ $flowMetrics['step1_approved'] }},
                            {{ $flowMetrics['step2_approved'] }},
                            {{ $flowMetrics['step3_approved'] }},
                            {{ $flowMetrics['completed'] }}
                        ]
                    }],
                    chart: {
                        type: 'bar',
                        height: 300,
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false,
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            horizontal: true,
                            distributed: true,
                            barHeight: '70%',
                            dataLabels: {
                                position: 'center'
                            },
                        }
                    },
                    colors: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4'],
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold',
                            colors: ['#fff']
                        },
                        formatter: function(val, opt) {
                            const percentages = [
                                100,
                                {{ $flowMetrics['step1_pass_rate'] }},
                                {{ $flowMetrics['step2_pass_rate'] }},
                                {{ $flowMetrics['step3_pass_rate'] }},
                                {{ $flowMetrics['step3_pass_rate'] }}
                            ];
                            return val + ' (' + percentages[opt.dataPointIndex] + '%)';
                        }
                    },
                    xaxis: {
                        categories: ['Submit', 'Drafter ✓', 'Kasubbag ✓', 'WD ✓', 'Completed'],
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '12px',
                                fontWeight: 500
                            }
                        }
                    },
                    legend: {
                        show: false
                    },
                    grid: {
                        padding: {
                            top: 0,
                            bottom: 0,
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + ' surat';
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            chart: {
                                height: 250
                            }
                        }
                    }]
                };

                const flowChart = new ApexCharts(
                    document.querySelector("#flow-chart"),
                    flowConfig
                );
                flowChart.render();

                // Bottleneck Analysis Chart (Horizontal Bar)
                const bottleneckConfig = {
                    series: [{
                        name: 'Avg Processing Time (days)',
                        data: @json($bottleneckAnalysis['avg_times'])
                    }],
                    chart: {
                        type: 'bar',
                        height: 300,
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false,
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            horizontal: true,
                            barHeight: '60%',
                            dataLabels: {
                                position: 'center'
                            },
                        }
                    },
                    colors: ['#4467EF'],
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val + ' hari';
                        },
                        style: {
                            fontSize: '12px',
                            fontWeight: 'bold',
                            colors: ['#fff']
                        }
                    },
                    xaxis: {
                        categories: @json($bottleneckAnalysis['labels']),
                        title: {
                            text: 'Rata-rata Waktu (Hari)'
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '11px',
                                fontWeight: 500
                            }
                        }
                    },
                    grid: {
                        padding: {
                            top: 0,
                            bottom: 0,
                        }
                    },
                    annotations: {
                        xaxis: [
                            {
                                x: 3,
                                borderColor: '#ef4444',
                                strokeDashArray: 4,
                                label: {
                                    borderColor: '#ef4444',
                                    style: {
                                        color: '#fff',
                                        background: '#ef4444',
                                        fontSize: '10px'
                                    },
                                    text: 'Target: < 3 hari'
                                }
                            }
                        ]
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            chart: {
                                height: 250
                            }
                        }
                    }]
                };

                const bottleneckChart = new ApexCharts(
                    document.querySelector("#bottleneck-chart"),
                    bottleneckConfig
                );
                bottleneckChart.render();

                // Real-time polling
                const pollData = () => {
                    fetch('{{ route("dashboard.data") }}')
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('pending-paraf-count').textContent = data.pending;
                            document.getElementById('paraf-hari-ini-count').textContent = data.approved_today;
                            document.getElementById('total-bulan-ini-count').textContent = data.total_month;
                            document.getElementById('success-rate-count').textContent = data.success_rate + '%';
                        })
                        .catch(err => console.error('Polling error:', err));
                };

                setInterval(pollData, 30000);
            };

            window.addEventListener("app:mounted", onLoad, { once: true });
        </script>
    </x-slot:scripts>

</x-layouts.app>