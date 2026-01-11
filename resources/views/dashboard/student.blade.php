<x-layouts.app title="Dashboard Mahasiswa" :hasPanel="false">

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            {{-- Welcome Card --}}
            <x-dashboard.welcome-card
                    :user="$user"
{{--                    image="{{ asset('images/illustrations/student.svg') }}"--}}
                    subtitle="Have a nice day!"
                    color="from-blue-500 to-blue-600"
            >
                <x-slot:extraInfo>
                    <div>
                        <span>Semangat belajarnya hari ini! Butuh surat akademik?</span>
                    </div>
                    <div>
                        <span>Sistem siap membantu proses administrasi akademik Anda agar <span class="font-semibold">lebih cepat dan transparan.</span></span>
                    </div>
                </x-slot:extraInfo>
                <x-slot:action>
                    <a href="{{ route('letters.index') }}" class="btn border border-white/10 bg-white/20 text-white hover:bg-white/30">Daftar Semua Pengajuan</a>
                    <a href="{{ route('letters.create') }}" class="btn border border-white/10 bg-white/20 text-white hover:bg-white/30">Ajukan Surat Baru</a>
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
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-dashboard.card-stat
                        icon="fa-file-alt"
                        label="Total Pengajuan"
                        :value="$stats['summary']['total']"
{{--                        :subtitle="'+' . $stats['summary']['this_month'] . ' bulan ini'"--}}
                        color="primary"
                />

                <x-dashboard.card-stat
                        icon="fa-clock"
                        label="Dalam Proses"
                        :value="$stats['summary']['in_progress']"
{{--                        :subtitle="'Avg ' . $stats['summary']['avg_processing_time'] . ' hari'"--}}
                        color="warning"
                />

                <x-dashboard.card-stat
                        icon="fa-check-circle"
                        label="Selesai"
                        :value="$stats['summary']['completed']"
{{--                        :subtitle="$stats['summary']['success_rate'] . '% success rate'"--}}
                        color="success"
                />

                <x-dashboard.card-stat
                        icon="fa-times-circle"
                        label="Ditolak"
                        :value="$stats['summary']['rejected']"
{{--                        subtitle="Review & ajukan lagi"--}}
                        color="danger"
                />
            </div>

            {{-- Charts Section --}}
            <div class="mt-4 sm:mt-5 lg:mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
                {{-- Status Distribution Chart --}}
                <x-dashboard.card-chart title="Status Pengajuan" chartId="status-chart" />

                {{-- By Letter Type Chart --}}
                <x-dashboard.card-chart title="Pengajuan Berdasarkan Jenis Surat" chartId="jenis-chart" />
            </div>

            {{-- Recent Submissions Table --}}
{{--            <div class="mt-4 sm:mt-5 lg:mt-6">--}}
{{--                <div class="flex h-12 items-center justify-between">--}}
{{--                    <h2 class="text-base font-medium tracking-wide text-slate-700 dark:text-navy-100">--}}
{{--                        Log Aktivitas Terbaru--}}
{{--                    </h2>--}}
{{--                </div>--}}

{{--                <div class="is-scrollbar-hidden overflow-y-auto min-w-full overflow-x-auto border border-slate-200 dark:border-navy-500 rounded-lg">--}}
{{--                    <table class="is-hoverable w-full text-left">--}}
{{--                        <thead>--}}
{{--                        <tr class="text-xs">--}}
{{--                            <th class="sticky top-0 z-10 whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">--}}
{{--                                Jenis Surat--}}
{{--                            </th>--}}
{{--                            <th class="sticky top-0 z-10 whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">--}}
{{--                                Status--}}
{{--                            </th>--}}
{{--                            <th class="sticky top-0 z-10 whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">--}}
{{--                                Waktu Pengajuan--}}
{{--                            </th>--}}
{{--                            <th class="sticky top-0 z-10 whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">--}}
{{--                                Aksi--}}
{{--                            </th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                        @forelse($recentLetters as $letter)--}}
{{--                            <tr class="text-xs border-y border-transparent {{ !$loop->last ? 'border-b-slate-200 dark:border-b-navy-500' : '' }}">--}}
{{--                                --}}{{-- 1. Jenis Surat --}}
{{--                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">--}}
{{--                                    <div class="flex items-center space-x-3">--}}
{{--                                        <div class="flex size-8 items-center justify-center rounded-lg bg-slate-100 dark:bg-navy-600">--}}
{{--                                            <i class="fa-solid fa-file-lines text-slate-500 dark:text-navy-200"></i>--}}
{{--                                        </div>--}}
{{--                                        <span class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                                {{ $letter->letter_type->label() }}--}}
{{--                            </span>--}}
{{--                                    </div>--}}
{{--                                </td>--}}

{{--                                --}}{{-- 2. Status --}}
{{--                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">--}}
{{--                                    @if($letter->status === 'completed')--}}
{{--                                        <span class="badge rounded-full bg-success/10 text-success text-tiny border border-success/20 inline-flex items-center space-x-1.5">--}}
{{--                                <i class="fa-solid fa-check-circle"></i>--}}
{{--                                <span>Selesai</span>--}}
{{--                            </span>--}}
{{--                                    @elseif($letter->status === 'rejected')--}}
{{--                                        <span class="badge rounded-full bg-error/10 text-error text-tiny border border-error/20 inline-flex items-center space-x-1.5">--}}
{{--                                <i class="fa-solid fa-circle-xmark"></i>--}}
{{--                                <span>Ditolak</span>--}}
{{--                            </span>--}}
{{--                                    @else--}}
{{--                                        <span class="badge rounded-full bg-warning/10 text-warning text-tiny border border-warning/20 inline-flex items-center space-x-1.5">--}}
{{--                                <i class="fa-solid fa-circle-notch animate-spin"></i>--}}
{{--                                <span>Proses</span>--}}
{{--                            </span>--}}
{{--                                    @endif--}}
{{--                                </td>--}}

{{--                                --}}{{-- 3. Tanggal --}}
{{--                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">--}}
{{--                                    <div class="flex flex-col">--}}
{{--                            <span class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                                {{ $letter->created_at->translatedFormat('d M Y') }}--}}
{{--                            </span>--}}
{{--                                        <span class="text-tiny text-slate-400 dark:text-navy-300 italic">--}}
{{--                                {{ $letter->created_at->diffForHumans() }}--}}
{{--                            </span>--}}
{{--                                    </div>--}}
{{--                                </td>--}}

{{--                                --}}{{-- 4. Tombol Aksi --}}
{{--                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">--}}
{{--                                    <div class="flex justify-center space-x-2">--}}
{{--                                        <a href="{{ route('letters.show', $letter) }}"--}}
{{--                                           class="btn size-7 rounded-full p-0 bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-navy-500 dark:text-navy-100 dark:hover:bg-navy-450"--}}
{{--                                           title="Lihat Detail">--}}
{{--                                            <i class="fa-solid fa-eye text-tiny"></i>--}}
{{--                                        </a>--}}
{{--                                        @if($letter->status === 'completed')--}}
{{--                                            <a href="{{ route('letters.download-pdf', $letter) }}"--}}
{{--                                               class="btn size-7 rounded-full p-0 bg-success/10 text-success hover:bg-success/20 dark:bg-success/15"--}}
{{--                                               title="Unduh PDF">--}}
{{--                                                <i class="fa-solid fa-download text-tiny"></i>--}}
{{--                                            </a>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @empty--}}
{{--                            <tr>--}}
{{--                                <td colspan="4" class="px-4 py-12 text-center">--}}
{{--                                    <div class="flex flex-col items-center">--}}
{{--                                        <i class="fa-solid fa-inbox text-4xl text-slate-200"></i>--}}
{{--                                        <p class="mt-2 text-slate-400">Belum ada pengajuan surat.</p>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @endforelse--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}{{-- Timeline In Progress --}}
{{--            @if($inProgressLetters->isNotEmpty())--}}
{{--                <div class="card mt-4">--}}
{{--                    <div class="border-b border-slate-200 px-4 py-3 dark:border-navy-500">--}}
{{--                        <h3 class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                            Surat Dalam Proses--}}
{{--                        </h3>--}}
{{--                    </div>--}}

{{--                    <div class="p-4 space-y-4">--}}
{{--                        @foreach($inProgressLetters as $letter)--}}
{{--                            <div class="rounded-lg border border-slate-200 p-4 dark:border-navy-500">--}}
{{--                                <div class="mb-3 flex items-center justify-between">--}}
{{--                                    <h4 class="font-medium text-slate-700 dark:text-navy-100">--}}
{{--                                        {{ $letter->letter_type->label() }}--}}
{{--                                    </h4>--}}
{{--                                    <span class="text-xs text-slate-400">{{ $letter->created_at->format('d M Y') }}</span>--}}
{{--                                </div>--}}

{{--                                --}}{{-- Timeline Steps --}}
{{--                                <ol class="timeline line-space [--size:1.5rem]">--}}
{{--                                    @foreach($letter->approvals as $approval)--}}
{{--                                        <li class="timeline-item">--}}
{{--                                            @if($approval->status === 'approved')--}}
{{--                                                <div class="timeline-item-point rounded-full border-2 border-success bg-success text-white dark:border-success dark:bg-success">--}}
{{--                                                    <i class="fa fa-check text-xs"></i>--}}
{{--                                                </div>--}}
{{--                                            @elseif($approval->status === 'rejected')--}}
{{--                                                <div class="timeline-item-point rounded-full border-2 border-error bg-error text-white">--}}
{{--                                                    <i class="fa fa-times text-xs"></i>--}}
{{--                                                </div>--}}
{{--                                            @elseif($approval->is_active)--}}
{{--                                                <div class="timeline-item-point rounded-full border-2 border-warning bg-warning text-white">--}}
{{--                                                    <div class="h-1.5 w-1.5 rounded-full bg-current"></div>--}}
{{--                                                </div>--}}
{{--                                            @else--}}
{{--                                                <div class="timeline-item-point rounded-full border-2 border-slate-300 bg-slate-100 dark:border-navy-400 dark:bg-navy-600">--}}
{{--                                                    <div class="h-1.5 w-1.5 rounded-full bg-current"></div>--}}
{{--                                                </div>--}}
{{--                                            @endif--}}

{{--                                            <div class="timeline-item-content flex-1 pl-4 sm:pl-6">--}}
{{--                                                <div class="flex flex-col pb-4 sm:flex-row sm:items-center sm:justify-between sm:pb-2">--}}
{{--                                                    <p class="pb-2 font-medium leading-none text-slate-600 dark:text-navy-100 sm:pb-0">--}}
{{--                                                        {{ $approval->step_label }}--}}
{{--                                                    </p>--}}
{{--                                                    <span class="text-xs text-slate-400">--}}
{{--                                                @if($approval->status === 'approved')--}}
{{--                                                            {{ $approval->approved_at->diffForHumans() }}--}}
{{--                                                        @elseif($approval->is_active)--}}
{{--                                                            Sedang diproses... ({{ $approval->created_at->diffForHumans() }})--}}
{{--                                                        @endif--}}
{{--                                            </span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                    @endforeach--}}
{{--                                </ol>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endif--}}
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