<x-layouts.app title="Dashboard Mahasiswa" :hasPanel="false">

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            {{-- Welcome Card --}}
            <x-dashboard.welcome-card :user="$user" role="Mahasiswa" />

            {{-- Summary Cards --}}
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-dashboard.card-stat
                        icon="fa-file-alt"
                        label="Total Pengajuan"
                        :value="$stats['summary']['total']"
                        :subtitle="'+' . $stats['summary']['this_month'] . ' bulan ini'"
                        color="primary"
                />

                <x-dashboard.card-stat
                        icon="fa-clock"
                        label="Dalam Proses"
                        :value="$stats['summary']['in_progress']"
                        :subtitle="'Avg ' . $stats['summary']['avg_processing_time'] . ' hari'"
                        color="warning"
                />

                <x-dashboard.card-stat
                        icon="fa-check-circle"
                        label="Selesai"
                        :value="$stats['summary']['completed']"
                        :subtitle="$stats['summary']['success_rate'] . '% success rate'"
                        color="success"
                />

                <x-dashboard.card-stat
                        icon="fa-times-circle"
                        label="Ditolak"
                        :value="$stats['summary']['rejected']"
                        subtitle="Review & ajukan lagi"
                        color="danger"
                />
            </div>

            {{-- Charts Section --}}
            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                {{-- Status Distribution Chart --}}
                <x-dashboard.card-chart title="Status Pengajuan" chartId="status-chart" />

                {{-- By Letter Type Chart --}}
                <x-dashboard.card-chart title="Jenis Surat" chartId="jenis-chart" />
            </div>

            {{-- Recent Submissions Table --}}
            <div class="card mt-4">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-navy-500">
                    <h3 class="font-medium text-slate-700 dark:text-navy-100">
                        Pengajuan Terbaru
                    </h3>
                    <a href="{{ route('letters.index') }}" class="text-xs+ font-medium text-primary hover:text-primary-focus dark:text-accent-light dark:hover:text-accent">
                        Lihat Semua →
                    </a>
                </div>

                <div class="scrollbar-sm overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                        <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                            <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                Jenis Surat
                            </th>
                            <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                Status
                            </th>
                            <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                Tanggal
                            </th>
                            <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                                Aksi
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recentLetters as $letter)
                            <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-file-alt text-slate-400"></i>
                                        <span class="font-medium text-slate-700 dark:text-navy-100">
                                        {{ $letter->letter_type->label() }}
                                    </span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    @if($letter->status === 'completed')
                                        <div class="badge rounded-full bg-success/10 text-success dark:bg-success/15">
                                            <i class="fa-solid fa-check-circle mr-1"></i>
                                            Selesai
                                        </div>
                                    @elseif($letter->status === 'rejected')
                                        <div class="badge rounded-full bg-error/10 text-error dark:bg-error/15">
                                            <i class="fa-solid fa-times-circle mr-1"></i>
                                            Ditolak
                                        </div>
                                    @else
                                        <div class="badge rounded-full bg-warning/10 text-warning dark:bg-warning/15">
                                            <i class="fa-solid fa-clock mr-1"></i>
                                            Proses
                                        </div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-400 dark:text-navy-300 sm:px-5">
                                    {{ $letter->created_at->diffForHumans() }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('letters.show', $letter) }}"
                                           class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if($letter->status === 'completed')
                                            <a href="{{ route('letters.download-pdf', $letter) }}"
                                               class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25">
                                                <i class="fa-solid fa-download text-success"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada pengajuan surat
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Timeline In Progress --}}
            @if($inProgressLetters->isNotEmpty())
                <div class="card mt-4">
                    <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500">
                        <h3 class="font-medium text-slate-700 dark:text-navy-100">
                            Surat Dalam Proses
                        </h3>
                    </div>

                    <div class="p-4 space-y-4">
                        @foreach($inProgressLetters as $letter)
                            <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">
                                <div class="mb-3 flex items-center justify-between">
                                    <h4 class="font-medium text-slate-700 dark:text-navy-100">
                                        {{ $letter->letter_type->label() }}
                                    </h4>
                                    <span class="text-xs text-slate-400">{{ $letter->created_at->format('d M Y') }}</span>
                                </div>

                                {{-- Timeline Steps --}}
                                <ol class="timeline line-space [--size:1.5rem]">
                                    @foreach($letter->approvals as $approval)
                                        <li class="timeline-item">
                                            @if($approval->status === 'approved')
                                                <div class="timeline-item-point rounded-full border-2 border-success bg-success text-white dark:border-success dark:bg-success">
                                                    <i class="fa fa-check text-xs"></i>
                                                </div>
                                            @elseif($approval->status === 'rejected')
                                                <div class="timeline-item-point rounded-full border-2 border-error bg-error text-white">
                                                    <i class="fa fa-times text-xs"></i>
                                                </div>
                                            @elseif($approval->is_active)
                                                <div class="timeline-item-point rounded-full border-2 border-warning bg-warning text-white">
                                                    <div class="h-1.5 w-1.5 rounded-full bg-current"></div>
                                                </div>
                                            @else
                                                <div class="timeline-item-point rounded-full border-2 border-slate-300 bg-slate-100 dark:border-navy-400 dark:bg-navy-600">
                                                    <div class="h-1.5 w-1.5 rounded-full bg-current"></div>
                                                </div>
                                            @endif

                                            <div class="timeline-item-content flex-1 pl-4 sm:pl-6">
                                                <div class="flex flex-col pb-4 sm:flex-row sm:items-center sm:justify-between sm:pb-2">
                                                    <p class="pb-2 font-medium leading-none text-slate-600 dark:text-navy-100 sm:pb-0">
                                                        {{ $approval->step_label }}
                                                    </p>
                                                    <span class="text-xs text-slate-400">
                                                @if($approval->status === 'approved')
                                                            {{ $approval->approved_at->diffForHumans() }}
                                                        @elseif($approval->is_active)
                                                            Sedang diproses... ({{ $approval->created_at->diffForHumans() }})
                                                        @endif
                                            </span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Quick Actions --}}
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('letters.create') }}"
                   class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Ajukan Surat Baru
                </a>
                <a href="{{ route('letters.index') }}"
                   class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                    <i class="fa-solid fa-list mr-2"></i>
                    Semua Pengajuan
                </a>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <x-slot:scripts>
        <script>
            const onLoad = () => {
                // Status Distribution Chart (Donut)
                const statusConfig = {
                    series: [
                        {{ $stats['charts']['status_distribution']['in_progress'] }},
                        {{ $stats['charts']['status_distribution']['completed'] }},
                        {{ $stats['charts']['status_distribution']['rejected'] }}
                    ],
                    chart: {
                        type: 'donut',
                        height: 280,
                        parentHeightOffset: 0,
                    },
                    labels: ['Dalam Proses', 'Selesai', 'Ditolak'],
                    colors: ['#f59e0b', '#10b981', '#ef4444'],
                    legend: {
                        position: 'bottom',
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Surat',
                                        fontSize: '14px',
                                        fontWeight: 600,
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return Math.round(val) + '%';
                        },
                        dropShadow: {
                            enabled: false
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

                const statusChart = new ApexCharts(
                    document.querySelector("#status-chart"),
                    statusConfig
                );
                statusChart.render();

                const jenisData = [
                    {{ $stats['charts']['by_type']['skak'] ?? 0 }},
                    {{ $stats['charts']['by_type']['skak_tunjangan'] ?? 0 }},
                    {{ $stats['charts']['by_type']['penelitian'] ?? 0 }},
                    {{ $stats['charts']['by_type']['dispensasi_kuliah'] ?? 0 }},
                    {{ $stats['charts']['by_type']['dispensasi_mahasiswa'] ?? 0 }}
                ];

                const maxValue = Math.max(...jenisData);

                // Jenis Surat Chart (Bar)
                const jenisConfig = {
                    series: [{
                        name: 'Jumlah',
                        data: jenisData,
                    }],
                    chart: {
                        type: 'bar',
                        height: 280,
                        parentHeightOffset: 0,
                        sparkline: { enabled: false },
                        toolbar: {
                            show: false,
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '50%',
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -25,
                        position: 'top',
                        style: {
                            fontSize: '11px',
                            colors: ["#304758"]
                        }
                    },
                    colors: ['#4467EF'],
                    xaxis: {
                        categories: ['SKAK', 'SKAK Tunjangan', 'Penelitian', 'Disp. Kuliah', 'Disp. Mhs'],
                        labels: {
                            style: {
                                fontSize: '11px'
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: maxValue + 1,
                        labels: {
                            formatter: val => Math.round(val)
                        },
                        title: {
                            text: 'Jumlah Surat'
                        }
                    },
                    grid: {
                        padding: {
                            top: 0,
                            bottom: 0,
                            right: 0,
                            left: 0
                        }
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            chart: {
                                height: 240
                            },
                            xaxis: {
                                labels: {
                                    rotate: -45
                                }
                            }
                        }
                    }]
                };

                const jenisChart = new ApexCharts(
                    document.querySelector("#jenis-chart"),
                    jenisConfig
                );
                jenisChart.render();

                // Real-time polling (every 30 seconds) - Only update summary cards
                const pollData = () => {
                    fetch('{{ route("dashboard.data") }}')
                        .then(res => res.json())
                        .then(data => {
                            // Update counter values
                            document.getElementById('total-pengajuan-count').textContent = data.total;
                            document.getElementById('dalam-proses-count').textContent = data.in_progress;
                            document.getElementById('selesai-count').textContent = data.completed;
                            document.getElementById('ditolak-count').textContent = data.rejected;
                        })
                        .catch(err => console.error('Polling error:', err));
                };

                // Start polling
                setInterval(pollData, 30000); // 30 seconds
            };

            window.addEventListener("app:mounted", onLoad, { once: true });
        </script>
    </x-slot:scripts>

</x-layouts.app>