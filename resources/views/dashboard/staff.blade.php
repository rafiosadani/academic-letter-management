<x-layouts.app title="Dashboard Staff Akademik" :hasPanel="false">

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            {{-- Welcome Card --}}
            <x-dashboard.welcome-card
                :user="$user"
{{--                image="{{ asset('images/illustrations/drafter.svg') }}"--}}
                color="from-indigo-500 to-indigo-600"
            >
                <x-slot:extraInfo>
                    <div>
                        <span>Mari selesaikan verifikasi berkas pengajuan hari ini.</span>
                    </div>
                    <div>
                        <span>Sistem siap membantu proses administrasi akademik Anda agar <span class="font-semibold">lebih cepat dan transparan.</span></span>
                    </div>
                </x-slot:extraInfo>
                <x-slot:action>
                    <a href="{{ route('approvals.index') }}" class="btn border border-white/10 bg-white/20 text-white hover:bg-white/30">Kelola Persetujuan Surat</a>
                    <a href="{{ route('settings.approval-flows.index') }}" class="btn border border-white/10 bg-white/20 text-white hover:bg-white/30">Pengaturan Alur Persetujuan</a>
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
                        icon="fa-clock"
                        label="Menunggu Persetujuan"
                        :value="$stats['summary']['pending']"
                        :subtitle="$stats['summary']['urgent'] . ' urgent (>3 hari)'"
                        color="warning"
                />

                <x-dashboard.card-stat
                        icon="fa-check-circle"
                        label="Disetujui Hari Ini"
                        :value="$stats['summary']['approved_today']"
                        subtitle="Kerja bagus!"
                        color="success"
                />

                <x-dashboard.card-stat
                        icon="fa-chart-line"
                        label="Total Bulan Ini"
                        :value="$stats['summary']['total_month']"
                        :subtitle="'Tingkat penyelesaian ' . $stats['summary']['success_rate'] . '%'"
                        color="primary"
                />

                <x-dashboard.card-stat
                        icon="fa-hourglass-half"
                        label="Rerata Waktu Proses"
                        :value="number_format($stats['summary']['avg_time'], 1) . ' hari'"
                        subtitle="Waktu rata-rata"
                        color="info"
                />
            </div>

            {{-- Charts Section --}}
            <div class="mt-4 sm:mt-5 lg:mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
                {{-- Priority Breakdown Chart (Radial Bar) --}}
                <x-dashboard.card-chart title="Menunggu Berdasarkan Prioritas" chartId="priority-chart" />

                {{-- Activity Chart (Bar - 7 days) --}}
                <x-dashboard.card-chart title="Aktivitas 7 Hari Terakhir" chartId="activity-chart" />
            </div>

            {{-- Performance Metrics Card --}}
            <div class="mt-4 sm:mt-5 lg:mt-6">
                <h2 class="text-base font-medium tracking-wide text-slate-700 dark:text-navy-100 mb-3">
                    Performa Bulan Ini
                </h2>

                <div class="card grid grid-cols-1 gap-4 p-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-slate-50 p-4 dark:bg-navy-600">
                        <p class="text-xs text-slate-400 dark:text-navy-300">Total Diproses</p>
                        <p class="text-2xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $stats['summary']['total_month'] }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-success/10 p-4">
                        <p class="text-xs text-slate-400 dark:text-navy-300">Disetuji</p>
                        <p class="text-2xl font-semibold text-success">
                            {{ $stats['performance']['approved_month'] }} ({{ $stats['summary']['success_rate'] }}%)
                        </p>
                    </div>
                    <div class="rounded-lg bg-error/10 p-4">
                        <p class="text-xs text-slate-400 dark:text-navy-300">Ditolak</p>
                        <p class="text-2xl font-semibold text-error">
                            {{ $stats['performance']['rejected_month'] }}
                        </p>
                    </div>
                </div>

                {{-- Avg Time Per Step --}}
{{--                @if(!empty($stats['performance']['avg_time_per_step']))--}}
{{--                    <div class="border-t border-slate-200 p-4 dark:border-navy-500">--}}
{{--                        <p class="mb-2 text-xs font-medium text-slate-400 dark:text-navy-300">Rata-rata Waktu Per Step:</p>--}}
{{--                        <div class="flex flex-wrap gap-3">--}}
{{--                            @foreach($stats['performance']['avg_time_per_step'] as $step => $time)--}}
{{--                                <div class="rounded-lg bg-slate-100 px-3 py-2 dark:bg-navy-600">--}}
{{--                                    <span class="text-xs text-slate-600 dark:text-navy-200">{{ ucfirst(str_replace('_', ' ', $step)) }}:</span>--}}
{{--                                    <span class="ml-1 font-semibold text-primary">{{ $time }} hari</span>--}}
{{--                                </div>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endif--}}
            </div>

            {{-- Pending Approvals Table --}}
{{--            <div class="card mt-4 sm:mt-5 lg:mt-6">--}}
{{--                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-navy-500">--}}
{{--                    <h3 class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                        Surat Menunggu Verifikasi--}}
{{--                    </h3>--}}
{{--                </div>--}}

{{--                <div class="scrollbar-sm overflow-x-auto">--}}
{{--                    <table class="w-full text-left">--}}
{{--                        <thead>--}}
{{--                        <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">--}}
{{--                            <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">--}}
{{--                                Mahasiswa--}}
{{--                            </th>--}}
{{--                            <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">--}}
{{--                                Jenis Surat--}}
{{--                            </th>--}}
{{--                            <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">--}}
{{--                                Diajukan--}}
{{--                            </th>--}}
{{--                            <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">--}}
{{--                                Status--}}
{{--                            </th>--}}
{{--                            <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">--}}
{{--                                Aksi--}}
{{--                            </th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                        @forelse($pendingApprovals as $approval)--}}
{{--                            <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">--}}
{{--                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">--}}
{{--                                    <div class="flex items-center space-x-3">--}}
{{--                                        <div class="avatar size-9">--}}
{{--                                            <div class="flex size-full items-center justify-center rounded-full bg-primary/10 text-primary dark:bg-accent-light/15 dark:text-accent-light">--}}
{{--                                                {{ substr($approval->letterRequest->student->profile->full_name, 0, 1) }}--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div>--}}
{{--                                            <p class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                                                {{ $approval->letterRequest->student->profile->full_name }}--}}
{{--                                            </p>--}}
{{--                                            <p class="text-xs text-slate-400">--}}
{{--                                                {{ $approval->letterRequest->student->nim }}--}}
{{--                                            </p>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
{{--                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">--}}
{{--                                <span class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                                    {{ $approval->letterRequest->letter_type->label() }}--}}
{{--                                </span>--}}
{{--                                </td>--}}
{{--                                <td class="whitespace-nowrap px-4 py-3 text-slate-400 dark:text-navy-300 sm:px-5">--}}
{{--                                    {{ $approval->days_waiting }} hari lalu--}}
{{--                                </td>--}}
{{--                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">--}}
{{--                                    @if($approval->priority === 'urgent')--}}
{{--                                        <div class="badge rounded-full bg-error/10 text-error dark:bg-error/15">--}}
{{--                                            Urgent--}}
{{--                                        </div>--}}
{{--                                    @elseif($approval->priority === 'normal')--}}
{{--                                        <div class="badge rounded-full bg-warning/10 text-warning dark:bg-warning/15">--}}
{{--                                            Normal--}}
{{--                                        </div>--}}
{{--                                    @else--}}
{{--                                        <div class="badge rounded-full bg-success/10 text-success dark:bg-success/15">--}}
{{--                                            Recent--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
{{--                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">--}}
{{--                                    <a href="{{ route('approvals.show', $approval) }}"--}}
{{--                                       class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90">--}}
{{--                                        Proses--}}
{{--                                    </a>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @empty--}}
{{--                            <tr>--}}
{{--                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">--}}
{{--                                    🎉 Tidak ada surat pending. Kerja bagus!--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @endforelse--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}

            {{-- Recent Activity --}}
{{--            <div class="card mt-4 sm:mt-5 lg:mt-6">--}}
{{--                <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500">--}}
{{--                    <h3 class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                        📝 Aktivitas Terbaru--}}
{{--                    </h3>--}}
{{--                </div>--}}
{{--                <div class="p-4">--}}
{{--                    <ol class="timeline line-space [--size:1.5rem]">--}}
{{--                        @foreach($recentActivity as $activity)--}}
{{--                            <li class="timeline-item">--}}
{{--                                @if($activity->status === 'approved')--}}
{{--                                    <div class="timeline-item-point rounded-full border-2 border-success bg-success text-white">--}}
{{--                                        <i class="fa fa-check text-xs"></i>--}}
{{--                                    </div>--}}
{{--                                @else--}}
{{--                                    <div class="timeline-item-point rounded-full border-2 border-error bg-error text-white">--}}
{{--                                        <i class="fa fa-times text-xs"></i>--}}
{{--                                    </div>--}}
{{--                                @endif--}}

{{--                                <div class="timeline-item-content flex-1 pl-4 sm:pl-6">--}}
{{--                                    <div class="flex flex-col pb-4 sm:flex-row sm:items-center sm:justify-between sm:pb-2">--}}
{{--                                        <p class="pb-2 text-sm leading-none text-slate-600 dark:text-navy-100 sm:pb-0">--}}
{{--                                            <span class="font-medium">{{ $activity->status === 'approved' ? 'Approved' : 'Rejected' }}</span>--}}
{{--                                            {{ $activity->letterRequest->letter_type->label() }}--}}
{{--                                            untuk <span class="font-medium">{{ $activity->letterRequest->student->profile->full_name }}</span>--}}
{{--                                        </p>--}}
{{--                                        <span class="text-xs text-slate-400">{{ $activity->approved_at->diffForHumans() }}</span>--}}
{{--                                    </div>--}}
{{--                                    @if($activity->note)--}}
{{--                                        <p class="text-xs text-slate-400">Note: {{ $activity->note }}</p>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </li>--}}
{{--                        @endforeach--}}
{{--                    </ol>--}}
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

                // Priority Breakdown Chart (Radial Bar)
                const priorityConfig = {
                    series: [
                        {{ $stats['charts']['priority_breakdown']['urgent'] }},
                        {{ $stats['charts']['priority_breakdown']['normal'] }}
                    ],
                    chart: {
                        type: 'radialBar',
                        height: 280,
                        parentHeightOffset: 0,
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                margin: 15,
                                size: '50%',
                            },
                            dataLabels: {
                                name: {
                                    fontSize: '14px',
                                },
                                value: {
                                    fontSize: '20px',
                                    fontWeight: 600,
                                },
                                total: {
                                    show: true,
                                    label: 'Total Pending',
                                    formatter: function (w) {
                                        return {{ $stats['summary']['pending'] }};
                                    }
                                }
                            },
                            track: {
                                margin: 10,
                            }
                        }
                    },
                    labels: ['Urgent (>3 hari)', 'Normal'],
                    colors: ['#ef4444', '#f59e0b'],
                    legend: {
                        show: true,
                        position: 'bottom',
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            chart: {
                                height: 240
                            }
                        }
                    }]
                };

                const priorityChart = new ApexCharts(
                    document.querySelector("#priority-chart"),
                    priorityConfig
                );
                priorityChart.render();

                // Activity Chart (Bar - 7 days)
                const activityConfig = {
                    series: [
                        {
                            name: 'Disetujui',
                            data: @json($stats['charts']['activity_7days']['approved'])
                        },
                        {
                            name: 'Ditolak',
                            data: @json($stats['charts']['activity_7days']['rejected'])
                        }
                    ],
                    chart: {
                        type: 'bar',
                        height: 280,
                        parentHeightOffset: 0,
                        stacked: true,
                        toolbar: {
                            show: false,
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '50%',
                        }
                    },
                    colors: ['#10b981', '#ef4444'],
                    xaxis: {
                        categories: @json($stats['charts']['activity_7days']['categories']),
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Surat'
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    grid: {
                        padding: {
                            top: 0,
                            bottom: 0,
                        }
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            chart: {
                                height: 240
                            }
                        }
                    }]
                };

                const activityChart = new ApexCharts(
                    document.querySelector("#activity-chart"),
                    activityConfig
                );
                activityChart.render();

                // Real-time polling (every 30 seconds)
                const pollData = () => {
                    fetch('{{ route("dashboard.data") }}')
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('pending-review-count').textContent = data.pending;
                            document.getElementById('disetujui-hari-ini-count').textContent = data.approved_today;
                            document.getElementById('total-bulan-ini-count').textContent = data.total_month;
                            document.getElementById('avg-processing-time-count').textContent = data.avg_time + ' hari';
                        })
                        .catch(err => console.error('Polling error:', err));
                };

                setInterval(pollData, 30000); // 30 seconds
            };

            window.addEventListener("app:mounted", onLoad, { once: true });
        </script>
    </x-slot:scripts>

</x-layouts.app>