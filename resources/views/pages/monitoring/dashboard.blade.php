<x-app-layout>
    @push('styles')
    <style>
    .checkbox-wrapper-25 input[type="checkbox"] {
        background-image: -webkit-linear-gradient(hsla(0,0%,0%,.1), hsla(0,0%,100%,.1)),
                          -webkit-linear-gradient(left, #f66 50%, #6cf 50%);
        background-size: 100% 100%, 200% 100%;
        background-position: 0 0, 15px 0;
        border-radius: 25px;
        box-shadow: inset 0 1px 4px hsla(0,0%,0%,.5),
                    inset 0 0 10px hsla(0,0%,0%,.5),
                    0 0 0 1px hsla(0,0%,0%,.1),
                    0 -1px 2px 2px hsla(0,0%,0%,.25),
                    0 2px 2px 2px hsla(0,0%,100%,.75);
        cursor: pointer;
        height: 25px;
        padding-right: 25px;
        width: 75px;
        -webkit-appearance: none;
        -webkit-transition: .25s;
        appearance: none;
        transition: .25s;
    }

    .checkbox-wrapper-25 input[type="checkbox"]:after {
        background-color: #eee;
        background-image: -webkit-linear-gradient(hsla(0,0%,100%,.1), hsla(0,0%,0%,.1));
        border-radius: 25px;
        box-shadow: inset 0 1px 1px 1px hsla(0,0%,100%,1),
                    inset 0 -1px 1px 1px hsla(0,0%,0%,.25),
                    0 1px 3px 1px hsla(0,0%,0%,.5),
                    0 0 2px hsla(0,0%,0%,.25);
        content: '';
        display: block;
        height: 25px;
        width: 50px;
    }

    .checkbox-wrapper-25 input[type="checkbox"]:checked {
        background-position: 0 0, 35px 0;
        padding-left: 25px;
        padding-right: 0;
    }

    .checkbox-wrapper-25 input[type="checkbox"]:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    </style>
    @endpush
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="waterMonitor()">

        <div x-show="notif" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed bottom-4 right-4 z-50">
            <div class="px-4 py-3 rounded-lg shadow-lg text-sm font-medium" :class="notif?.type === 'error' ? 'bg-red-500 text-white' : 'bg-green-500 text-white'" x-text="notif?.msg"></div>
        </div>

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Monitoring Ketinggian Air</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pemantauan real-time ketinggian air tandon rumah tangga</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="deviceOnline ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="deviceOnline ? 'bg-green-500 animate-pulse' : 'bg-red-500'"></span>
                    <span x-text="!connected ? 'Memuat...' : (deviceOnline ? 'Device Online' : 'Device Offline')"></span>
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
                        <span class="text-xs text-gray-400" x-text="'ID: ' + latest.device_id"></span>
                    </div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100" x-text="latest.tinggi + '%'">--%</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ketinggian Air</div>
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
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="relayOn ? 'bg-green-500/20' : 'bg-gray-500/20'">
                            <svg class="w-5 h-5" :class="relayOn ? 'text-green-500' : 'text-gray-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="checkbox-wrapper-25">
                            <input type="checkbox" x-model="relayOn" @change="toggleRelay()" :disabled="relayLoading">
                        </div>
                    </div>
                    <div class="text-2xl font-bold" :class="relayOn ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'" x-text="relayOn ? 'ON' : 'OFF'">--</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Relay</div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-3">
                <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="modeAuto ? 'bg-blue-500/20' : 'bg-orange-500/20'">
                            <svg class="w-5 h-5" :class="modeAuto ? 'text-blue-500' : 'text-orange-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="checkbox-wrapper-25">
                            <input type="checkbox" x-model="modeAuto" @change="toggleMode()" :disabled="modeLoading">
                        </div>
                    </div>
                    <div class="text-2xl font-bold" :class="modeAuto ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400'" x-text="modeAuto ? 'AUTO' : 'MANUAL'">--</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mode</div>
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
                                     :style="'height: ' + latest.tinggi + '%'">
                                </div>
                            </div>
                            <div class="absolute -top-6 left-0 right-0 text-center text-xs font-medium text-gray-500 dark:text-gray-400">100%</div>
                            <div class="absolute top-1/2 left-0 right-0 text-center text-xs font-bold text-white mix-blend-difference" x-text="latest.tinggi + '%'"></div>
                            <div class="absolute -bottom-6 left-0 right-0 text-center text-xs font-medium text-gray-500 dark:text-gray-400">0%</div>
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
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Device</th>
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ketinggian</th>
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Relay</th>
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mode</th>
                            <th class="text-left py-3 px-5 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in historyData" :key="item.id">
                            <tr class="border-b border-gray-50 dark:border-gray-700/30 hover:bg-gray-50 dark:hover:bg-gray-700/20 transition">
                                <td class="py-3 px-5 text-gray-600 dark:text-gray-400" x-text="index + 1"></td>
                                <td class="py-3 px-5 text-gray-600 dark:text-gray-400" x-text="item.device_id"></td>
                                <td class="py-3 px-5 font-medium text-gray-800 dark:text-gray-100" x-text="item.tinggi + '%'"></td>
                                <td class="py-3 px-5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="badgeClass(item.status)" x-text="item.status"></span>
                                </td>
                                <td class="py-3 px-5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="item.relay ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'" x-text="item.relay ? 'ON' : 'OFF'"></span>
                                </td>
                                <td class="py-3 px-5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="item.mode === 'AUTO' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400'" x-text="item.mode"></span>
                                </td>
                                <td class="py-3 px-5 text-gray-600 dark:text-gray-400" x-text="item.waktu_full"></td>
                            </tr>
                        </template>
                        <tr x-show="historyData.length === 0">
                            <td colspan="7" class="py-8 text-center text-gray-400 dark:text-gray-500">Belum ada data. Pastikan sensor dan MQTT subscriber aktif.</td>
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
                device_id: '{{ $latest ? $latest->device_id : "-" }}',
                tinggi: {{ $latest ? $latest->tinggi : 0 }},
                status: '{{ $latest ? $latest->status : "Tidak Ada Data" }}',
                relay: {{ $latest && $latest->relay ? 'true' : 'false' }},
                mode: '{{ $latest ? $latest->mode : "-" }}',
                waktu: '{{ $latest ? $latest->updated_at->format("d M Y H:i:s") : "-" }}',
            },
            historyData: [
                @foreach($history as $item)
                {
                    id: {{ $item->id }},
                    device_id: '{{ $item->device_id }}',
                    tinggi: {{ $item->tinggi }},
                    status: '{{ $item->status }}',
                    relay: {{ $item->relay ? 'true' : 'false' }},
                    mode: '{{ $item->mode }}',
                    waktu_full: '{{ $item->updated_at->format("d M Y H:i:s") }}',
                },
                @endforeach
            ],
            connected: false,
            deviceOnline: false,
            lastSeen: null,
            chart: null,
            relayLoading: false,
            modeLoading: false,
            relayOn: {{ $latest && $latest->relay ? 'true' : 'false' }},
            modeAuto: {{ $latest && $latest->mode === 'AUTO' ? 'true' : 'false' }},
            notif: null,
            _lastToggle: 0,
            _pendingRelay: null,
            _pendingMode: null,
            _pollInterval: null,

            init() {
                // Check if Chart.js is loaded before initializing chart
                if (typeof Chart !== 'undefined') {
                    this.initChart();
                } else {
                    // Wait for Chart.js to load
                    const checkChart = setInterval(() => {
                        if (typeof Chart !== 'undefined') {
                            clearInterval(checkChart);
                            this.initChart();
                        }
                    }, 100);
                    // Stop checking after 5 seconds
                    setTimeout(() => clearInterval(checkChart), 5000);
                }
                this.fetchData();
                this._startPolling(5000);
            },

            _startPolling(ms) {
                if (this._pollInterval) clearInterval(this._pollInterval);
                this._pollInterval = setInterval(() => this.fetchData(), ms);
            },

            initChart() {
                // Check if Chart.js is available
                if (typeof Chart === 'undefined') {
                    return;
                }
                
                const ctx = document.getElementById('waterChart').getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(103, 191, 255, 0.3)');
                gradient.addColorStop(1, 'rgba(103, 191, 255, 0.0)');

                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.historyData.map(d => d.waktu_full),
                        datasets: [{
                            label: 'Ketinggian Air (%)',
                            data: this.historyData.map(d => d.tinggi),
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
                                    callback: v => v + '%',
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
                                    label: ctx => 'Ketinggian: ' + ctx.parsed.y + '%',
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
                        fetch('/api/water-level/history?limit=20'),
                    ]);
                    
                    const latestData = await latestRes.json();
                    const historyData = await historyRes.json();

                    this.latest.device_id = latestData.device_id;
                    this.latest.tinggi = latestData.tinggi;
                    this.latest.status = latestData.status;
                    this.latest.waktu = latestData.waktu;
                    this.lastSeen = latestData.last_seen ? new Date(latestData.last_seen) : null;
                    this.deviceOnline = this.lastSeen ? (Date.now() - this.lastSeen.getTime()) < 120000 : false;
                    
                    const elapsed = Date.now() - this._lastToggle;

                    if (this._pendingRelay !== null) {
                        if (latestData.relay === this._pendingRelay) {
                            this._pendingRelay = null;
                            this.relayOn = latestData.relay;
                            this.latest.relay = latestData.relay;
                            if (!this._pendingMode) this._startPolling(5000);
                        } else if (elapsed > 30000) {
                            this._pendingRelay = null;
                            this.relayOn = latestData.relay;
                            this.latest.relay = latestData.relay;
                            if (!this._pendingMode) this._startPolling(5000);
                        }
                    } else if (elapsed > 10000) {
                        this.relayOn = latestData.relay;
                        this.latest.relay = latestData.relay;
                    }

                    if (this._pendingMode !== null) {
                        if (latestData.mode === this._pendingMode) {
                            this._pendingMode = null;
                            this.modeAuto = latestData.mode === 'AUTO';
                            this.latest.mode = latestData.mode;
                            if (!this._pendingRelay) this._startPolling(5000);
                        } else if (elapsed > 30000) {
                            this._pendingMode = null;
                            this.modeAuto = latestData.mode === 'AUTO';
                            this.latest.mode = latestData.mode;
                            if (!this._pendingRelay) this._startPolling(5000);
                        }
                    } else if (elapsed > 10000) {
                        this.modeAuto = latestData.mode === 'AUTO';
                        this.latest.mode = latestData.mode;
                    }

                    // Use JSON parse/stringify to avoid Alpine.js reactivity issues
                    this.historyData = JSON.parse(JSON.stringify(historyData));
                    this.connected = true;
                } catch (e) {
                    console.error('fetchData error:', e);
                    this.connected = false;
                    this.deviceOnline = false;
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
                const p = this.latest.tinggi;
                if (p >= 80) return 'bg-green-400/60';
                if (p >= 30) return 'bg-sky-400/60';
                return 'bg-red-400/60';
            },

            showNotif(msg, type = 'success') {
                this.notif = { msg, type };
                setTimeout(() => this.notif = null, 3000);
            },

            async toggleRelay() {
                this.relayLoading = true;
                this._lastToggle = Date.now();
                this.latest.relay = this.relayOn;
                const expectedRelay = this.relayOn;
                this._pendingRelay = expectedRelay;
                this._startPolling(1000);
                try {
                    const res = await fetch('/api/device/relay', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ relay: expectedRelay }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showNotif(data.message);
                    } else {
                        this._pendingRelay = null;
                        this.relayOn = !expectedRelay;
                        this.latest.relay = this.relayOn;
                        this._startPolling(5000);
                        this.showNotif(data.message, 'error');
                    }
                } catch (e) {
                    this._pendingRelay = null;
                    this.relayOn = !expectedRelay;
                    this.latest.relay = this.relayOn;
                    this._startPolling(5000);
                    this.showNotif('Gagal mengirim perintah', 'error');
                }
                this.relayLoading = false;
            },

            async toggleMode() {
                this.modeLoading = true;
                this._lastToggle = Date.now();
                this.latest.mode = this.modeAuto ? 'AUTO' : 'MANUAL';
                const expectedMode = this.latest.mode;
                this._pendingMode = expectedMode;
                this._startPolling(1000);
                try {
                    const res = await fetch('/api/device/mode', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ mode: expectedMode }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showNotif(data.message);
                    } else {
                        this._pendingMode = null;
                        this.modeAuto = !this.modeAuto;
                        this.latest.mode = this.modeAuto ? 'AUTO' : 'MANUAL';
                        this._startPolling(5000);
                        this.showNotif(data.message, 'error');
                    }
                } catch (e) {
                    this._pendingMode = null;
                    this.modeAuto = !this.modeAuto;
                    this.latest.mode = this.modeAuto ? 'AUTO' : 'MANUAL';
                    this._startPolling(5000);
                    this.showNotif('Gagal mengirim perintah', 'error');
                }
                this.modeLoading = false;
            },
        }
    }
    </script>
    @endpush
</x-app-layout>
