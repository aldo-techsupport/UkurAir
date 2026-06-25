<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Riwayat Data</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Log seluruh data ketinggian air tandon</p>
            </div>
            <a href="{{ route('monitoring') }}" class="btn bg-violet-500 hover:bg-violet-600 text-white mt-4 sm:mt-0">
                <svg class="w-4 h-4 fill-current shrink-0 mr-2" viewBox="0 0 16 16"><path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm4 9H9v3H7V9H4V7h3V4h2v3h3v2z"/></svg>
                Dashboard
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">
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
                        @forelse($data as $index => $item)
                        <tr class="border-b border-gray-50 dark:border-gray-700/30 hover:bg-gray-50 dark:hover:bg-gray-700/20 transition">
                            <td class="py-3 px-5 text-gray-600 dark:text-gray-400">{{ ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}</td>
                            <td class="py-3 px-5 text-gray-600 dark:text-gray-400">{{ $item->device_id }}</td>
                            <td class="py-3 px-5 font-medium text-gray-800 dark:text-gray-100">{{ $item->tinggi }}%</td>
                            <td class="py-3 px-5">
                                @php
                                    $badgeColor = match($item->status) {
                                        'Penuh' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        'Sedang' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        default => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">{{ $item->status }}</span>
                            </td>
                            <td class="py-3 px-5">
                                @php
                                    $relayColor = $item->relay
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $relayColor }}">{{ $item->relay ? 'ON' : 'OFF' }}</span>
                            </td>
                            <td class="py-3 px-5">
                                @php
                                    $modeColor = $item->mode === 'AUTO'
                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
                                        : 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400';
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $modeColor }}">{{ $item->mode }}</span>
                            </td>
                            <td class="py-3 px-5 text-gray-600 dark:text-gray-400">{{ $item->updated_at->format('d M Y H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400 dark:text-gray-500">Belum ada data tersimpan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($data->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60">
                {{ $data->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
