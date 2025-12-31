<x-layouts.app title="Dashboard Wakil Dekan Akademik" :hasPanel="false">

    {{-- Welcome Card --}}
    <x-dashboard.welcome-card :user="$user" role="Wakil Dekan Bidang Akademik" />

    {{-- Summary Cards --}}
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <x-dashboard.card-stat
                icon="fa-file-signature"
                label="Pending TTE"
                :value="$stats['summary']['pending_tte']"
                color="warning"
        />

        <x-dashboard.card-stat
                icon="fa-check-circle"
                label="TTE Hari Ini"
                :value="$stats['summary']['tte_today']"
                color="success"
        />

        <x-dashboard.card-stat
                icon="fa-calendar-check"
                label="Bulan Ini"
                :value="$stats['summary']['total_month']"
                color="primary"
        />

        <x-dashboard.card-stat
                icon="fa-chart-line"
                label="Total YTD"
                :value="$stats['summary']['ytd_total']"
                color="info"
        />

        <x-dashboard.card-stat
                icon="fa-arrow-trend-up"
                label="Growth"
                :value="($stats['summary']['growth'] >= 0 ? '+' : '') . $stats['summary']['growth'] . '%'"
                :trend="abs($stats['summary']['growth']) . '%'"
                :trendUp="$stats['summary']['growth'] >= 0"
                :color="$stats['summary']['growth'] >= 0 ? 'success' : 'danger'"
        />

        <x-dashboard.card-stat
                icon="fa-hourglass-half"
                label="TAT"
                :value="number_format($stats['summary']['tat'], 1) . ' hari'"
                subtitle="Turnaround Time"
                color="primary"
        />
    </div>

    {{-- Charts Row 1 --}}
    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        {{-- By Prodi Chart --}}
        <x-dashboard.card-chart title="📊 Pengajuan per Program Studi" chartId="prodi-chart" />

        {{-- Trend YoY Chart --}}
        <x-dashboard.card-chart title="📈 Trend Tahun ke Tahun" chartId="trend-chart" />
    </div>

    {{-- Insights Card --}}
    <div class="card mt-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-navy-600 dark:to-navy-700">
        <div class="p-4">
            <h3 class="mb-3 font-semibold text-slate-700 dark:text-navy-100">
                🎯 Insights & Recommendations
            </h3>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="flex items-start space-x-2">
                    <i class="fa-solid fa-exclamation-triangle mt-1 text-warning"></i>
                    <div>
                        <p class="text-xs font-medium text-slate-600 dark:text-navy-200">Bottleneck Terdeteksi</p>
                        <p class="text-sm text-slate-700 dark:text-navy-100">
                            <strong>{{ $insights['bottleneck']['step'] }}</strong> ({{ $insights['bottleneck']['avg_time'] }} hari avg)
                        </p>
                        <p class="text-xs text-slate-500">→ Rekomendasi: Delegate atau tambah approver</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <i class="fa-solid fa-chart-line mt-1 text-primary"></i>
                    <div>
                        <p class="text-xs font-medium text-slate-600 dark:text-navy-200">Trend Pengajuan</p>
                        <p class="text-sm text-slate-700 dark:text-navy-100">
                            <strong>{{ $stats['summary']['growth'] >= 0 ? '+' : '' }}{{ $stats['summary']['growth'] }}%</strong> vs tahun lalu
                        </p>
                        <p class="text-xs text-slate-500">→ {{ $stats['summary']['growth'] >= 0 ? 'Antisipasi peningkatan' : 'Monitor penurunan' }}</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <i class="fa-solid fa-bell mt-1 text-error"></i>
                    <div>
                        <p class="text-xs font-medium text-slate-600 dark:text-navy-200">Urgent Action</p>
                        <p class="text-sm text-slate-700 dark:text-navy-100">
                            <strong>{{ $insights['urgent_count'] }} surat</strong> >5 hari
                        </p>
                        <p class="text-xs text-slate-500">→ Perlu perhatian segera</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending TTE Table --}}
    <div class="card mt-4">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-navy-500">
            <h3 class="font-medium text-slate-700 dark:text-navy-100">
                ⏳ Surat Menunggu TTE
            </h3>
        </div>

        <div class="scrollbar-sm overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Mahasiswa</th>
                    <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Prodi</th>
                    <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Jenis Surat</th>
                    <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Status</th>
                    <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($pendingApprovals as $approval)
                    <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                            <div>
                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                    {{ $approval->letterRequest->student->profile->full_name }}
                                </p>
                                <p class="text-xs text-slate-400">{{ $approval->letterRequest->student->nim }}</p>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                <span class="text-xs text-slate-600 dark:text-navy-200">
                                    {{ $approval->letterRequest->student->program_studi ?? 'N/A' }}
                                </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                <span class="font-medium text-slate-700 dark:text-navy-100">
                                    {{ $approval->letterRequest->letter_type->getLabel() }}
                                </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                            <div class="badge rounded-full bg-success/10 text-success">
                                <i class="fa-solid fa-check-circle mr-1"></i>
                                Paraf ✓
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                            <a href="{{ route('approvals.show', $approval) }}"
                               class="btn bg-primary font-medium text-white hover:bg-primary-focus">
                                Review & TTE
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                            🎉 Tidak ada surat pending TTE
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Scripts --}}
    <x-slot:scripts>
        <script>
            const onLoad = () => {
                // Prodi Chart (Bar)
                const prodiConfig = {
                    series: [{
                        name: 'Jumlah Surat',
                        data: @json($prodiStats['data'])
                    }],
                    chart: {
                        type: 'bar',
                        height: 300,
                        parentHeightOffset: 0,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '60%',
                            dataLabels: { position: 'top' }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -20,
                        style: { fontSize: '12px', colors: ["#304758"] }
                    },
                    colors: ['#4467EF'],
                    xaxis: {
                        categories: @json($prodiStats['labels']),
                        labels: {
                            style: { fontSize: '10px' },
                            rotate: -45
                        }
                    },
                    yaxis: {
                        title: { text: 'Jumlah Surat' }
                    },
                    grid: { padding: { top: 0, bottom: 0 } },
                    responsive: [{
                        breakpoint: 768,
                        options: { chart: { height: 250 } }
                    }]
                };

                new ApexCharts(document.querySelector("#prodi-chart"), prodiConfig).render();

                // Trend Chart (Multi-line)
                const trendConfig = {
                    series: @json($trendData['series']),
                    chart: {
                        type: 'line',
                        height: 300,
                        parentHeightOffset: 0,
                        toolbar: { show: false }
                    },
                    stroke: {
                        width: 3,
                        curve: 'smooth'
                    },
                    colors: ['#94a3b8', '#4467EF'],
                    markers: {
                        size: 4,
                        hover: { size: 6 }
                    },
                    xaxis: {
                        categories: @json($trendData['categories'])
                    },
                    yaxis: {
                        title: { text: 'Jumlah Surat' }
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

                new ApexCharts(document.querySelector("#trend-chart"), trendConfig).render();

                // Polling
                setInterval(() => {
                    fetch('{{ route("dashboard.data") }}')
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('pending-tte-count').textContent = data.pending_tte;
                            document.getElementById('tte-hari-ini-count').textContent = data.tte_today;
                            document.getElementById('bulan-ini-count').textContent = data.total_month;
                            document.getElementById('total-ytd-count').textContent = data.ytd_total;
                            document.getElementById('growth-count').textContent = (data.growth >= 0 ? '+' : '') + data.growth + '%';
                            document.getElementById('tat-count').textContent = data.tat + ' hari';
                        })
                        .catch(err => console.error('Polling error:', err));
                }, 30000);
            };

            window.addEventListener("app:mounted", onLoad, { once: true });
        </script>
    </x-slot:scripts>

</x-layouts.app>