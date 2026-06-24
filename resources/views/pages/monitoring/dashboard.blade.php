<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="waterMonitor()">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Monitoring Ketinggian Air</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pemantauan real-time ketinggian air tandon rumah tangga</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="connected ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="connected ? 'bg-green-500 animate-pulse' : 'bg-red-500'"></span>
                    <span x-text="connected ? 'Terhubung' : 'Memuat...'"></span>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6 mb-8">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-sky-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100" x-text="latest.tinggi_air + ' cm'">-- cm</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ketinggian Air</div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-violet-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100" x-text="latest.persen + '%'">--%</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Persentase Pengisian</div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="statusClass(latest.status)">
                            <svg class="w-5 h-5" :class="statusIconClass(latest.status)" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold" :class="statusTextClass(latest.status)" x-text="latest.status || '--'">--</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Status Air</div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="latest.waktu || '--'">--</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update Terakhir</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6 mb-8">
            <div class="col-span-12 xl:col-span-8">
                <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Grafik Ketinggian Air</h2>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Real-time (50 data terakhir)</div>
                    </div>
                    <div class="h-72">
                        <canvas id="waterChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-span-12 xl:col-span-4">
                <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl p-5 h-full flex flex-col">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Visualisasi Tandon</h2>
                    <div class="flex-1 flex items-center justify-center">
                        <div class="relative w-40 h-64">
                            <div class="absolute inset-0 border-2 border-gray-300 dark:border-gray-600 rounded-b-2xl rounded-t-lg overflow-hidden bg-gray-50 dark:bg-gray-700/30">
                                <div class="absolute bottom-0 left-0 right-0 transition-all duration-700 ease-out rounded-b-xl"
                                     :class="waterColor()"
                                     :style="'height: ' + latest.persen + '%'">
                                </div>
                            </div>
                            <div class="absolute -top-6 left-0 right-0 text-center text-xs font-medium text-gray-500 dark:text-gray-400">100 cm</div>
                            <div class="absolute top-1/2 left-0 right-0 text-center text-xs font-bold text-white mix-blend-difference" x-text="latest.persen + '%'"></div>
                            <div class="absolute -bottom-6 left-0 right-0 text-center text-xs font-medium text-gray-500 dark:text-gray-400">0 cm</div>
                        </div>
                    </div>
                    <div class="mt-6 space-y-2">
                        <div class="flex items-center text-sm">
                            <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                            <span class="text-gray-600 dark:text-gray-400">Penuh (≥ 80%)</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                            <span class="text-gray-600 dark:text-gray-400">Sedang (30–79%)</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                            <span class="text-gray-600 dark:text-gray-400">Rendah (&lt; 30%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Log Data Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700/60">
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ketinggian Air</th>
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Persen</th>
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in historyData" :key="item.id">
                            <tr class="border-b border-gray-50 dark:border-gray-700/30 hover:bg-gray-50 dark:hover:bg-gray-700/20 transition">
                                <td class="py-3 px-5 text-gray-600 dark:text-gray-400" x-text="index + 1"></td>
                                <td class="py-3 px-5 font-medium text-gray-800 dark:text-gray-100" x-text="item.tinggi_air + ' cm'"></td>
                                <td class="py-3 px-5 text-gray-600 dark:text-gray-400" x-text="item.persen + '%'"></td>
                                <td class="py-3 px-5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="badgeClass(item.status)" x-text="item.status"></span>
                                </td>
                                <td class="py-3 px-5 text-gray-600 dark:text-gray-400" x-text="item.waktu_full"></td>
                            </tr>
                        </template>
                        <tr x-show="historyData.length === 0">
                            <td colspan="5" class="py-8 text-center text-gray-400 dark:text-gray-500">Belum ada data. Pastikan sensor dan MQTT subscriber aktif.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
    function waterMonitor() {
        return {
            latest: {
                tinggi_air: {{ $latest ? $latest->tinggi_air : 0 }},
                persen: {{ $latest ? \App\Models\WaterLevel::hitungPersen($latest->tinggi_air) : 0 }},
                status: '{{ $latest ? $latest->status : "Tidak Ada Data" }}',
                waktu: '{{ $latest ? $latest->updated_at->format("d M Y H:i:s") : "-" }}',
            },
            historyData: [
                @foreach($history as $item)
                {
                    id: {{ $item->id }},
                    tinggi_air: {{ $item->tinggi_air }},
                    persen: {{ \App\Models\WaterLevel::hitungPersen($item->tinggi_air) }},
                    status: '{{ $item->status }}',
                    waktu_full: '{{ $item->updated_at->format("d M Y H:i:s") }}',
                },
                @endforeach
            ],
            connected: false,
            chart: null,

            init() {
                this.initChart();
                this.fetchData();
                setInterval(() => this.fetchData(), 5000);
            },

            initChart() {
                const ctx = document.getElementById('waterChart').getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(103, 191, 255, 0.3)');
                gradient.addColorStop(1, 'rgba(103, 191, 255, 0.0)');

                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.historyData.map(d => d.waktu),
                        datasets: [{
                            label: 'Ketinggian Air (cm)',
                            data: this.historyData.map(d => d.tinggi_air),
                            fill: true,
                            backgroundColor: gradient,
                            borderColor: 'rgba(103, 191, 255, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 2,
                            pointBackgroundColor: 'rgba(103, 191, 255, 1)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                grid: { color: 'rgba(156, 163, 175, 0.15)' },
                                ticks: {
                                    callback: v => v + ' cm',
                                    color: 'rgba(156, 163, 175, 0.8)',
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    maxTicksLimit: 10,
                                    color: 'rgba(156, 163, 175, 0.8)',
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#d1d5db',
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: ctx => 'Ketinggian: ' + ctx.parsed.y + ' cm',
                                }
                            }
                        }
                    }
                });
            },

            async fetchData() {
                try {
                    const [latestRes, historyRes] = await Promise.all([
                        fetch('/api/water-level/latest'),
                        fetch('/api/water-level/history?limit=50'),
                    ]);
                    const latestData = await latestRes.json();
                    const historyData = await historyRes.json();

                    this.latest = latestData;
                    this.historyData = historyData;
                    this.connected = true;

                    if (this.chart) {
                        this.chart.data.labels = historyData.map(d => d.waktu);
                        this.chart.data.datasets[0].data = historyData.map(d => d.tinggi_air);
                        this.chart.update('none');
                    }
                } catch (e) {
                    this.connected = false;
                }
            },

            statusClass(status) {
                if (status === 'Penuh') return 'bg-green-500/20';
                if (status === 'Sedang') return 'bg-yellow-500/20';
                return 'bg-red-500/20';
            },
            statusIconClass(status) {
                if (status === 'Penuh') return 'text-green-500';
                if (status === 'Sedang') return 'text-yellow-500';
                return 'text-red-500';
            },
            statusTextClass(status) {
                if (status === 'Penuh') return 'text-green-600 dark:text-green-400';
                if (status === 'Sedang') return 'text-yellow-600 dark:text-yellow-400';
                return 'text-red-600 dark:text-red-400';
            },
            badgeClass(status) {
                if (status === 'Penuh') return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
                if (status === 'Sedang') return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
                return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
            },
            waterColor() {
                const p = this.latest.persen;
                if (p >= 80) return 'bg-green-400/60';
                if (p >= 30) return 'bg-sky-400/60';
                return 'bg-red-400/60';
            },
        }
    }
    </script>
    @endpush
</x-app-layout>
