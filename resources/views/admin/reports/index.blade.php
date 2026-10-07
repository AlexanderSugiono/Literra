<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Laporan & Analytics') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Summary cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">IDR {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500">Total Order</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalOrders }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500">Rata-rata Nilai Order</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">IDR {{ number_format($avgOrderValue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500">Bulan Ini vs Bulan Lalu</p>
                    <p class="text-2xl font-bold mt-1 {{ $revenueChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $revenueChange >= 0 ? '↑' : '↓' }} {{ abs($revenueChange) }}%
                    </p>
                    <p class="text-xs text-gray-400 mt-1">IDR {{ number_format($thisMonth, 0, ',', '.') }} / IDR {{ number_format($lastMonth, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Charts row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-4">Penjualan 30 Hari Terakhir</h3>
                    <canvas id="salesChart" height="200"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-4">Metode Pembayaran</h3>
                    <canvas id="paymentChart" height="200"></canvas>
                </div>
            </div>

            {{-- Top books & categories --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-5 py-4 border-b">
                        <h3 class="font-semibold text-gray-900">5 Buku Terlaris</h3>
                    </div>
                    <div class="p-5">
                        <table class="min-w-full text-sm">
                            <thead><tr class="text-left text-xs text-gray-500 uppercase"><th class="pb-2">Buku</th><th class="pb-2 text-center">Terjual</th><th class="pb-2 text-right">Pendapatan</th></tr></thead>
                            <tbody>
                                @forelse ($topBooks as $b)
                                    <tr class="border-t"><td class="py-2">{{ $b['title'] }}</td><td class="py-2 text-center">{{ $b['qty'] }}</td><td class="py-2 text-right">IDR {{ number_format($b['revenue'], 0, ',', '.') }}</td></tr>
                                @empty
                                    <tr><td colspan="3" class="py-4 text-center text-gray-400">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-5 py-4 border-b">
                        <h3 class="font-semibold text-gray-900">5 Kategori Terlaris</h3>
                    </div>
                    <div class="p-5">
                        <table class="min-w-full text-sm">
                            <thead><tr class="text-left text-xs text-gray-500 uppercase"><th class="pb-2">Kategori</th><th class="pb-2 text-center">Terjual</th></tr></thead>
                            <tbody>
                                @forelse ($topCategories as $c)
                                    <tr class="border-t"><td class="py-2">{{ $c['name'] }}</td><td class="py-2 text-center">{{ $c['qty'] }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="py-4 text-center text-gray-400">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales line chart
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: 'Penjualan',
                data: @json($dailyValues),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79,70,229,0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: function(v) { return 'IDR ' + v.toLocaleString('id-ID'); } } } }
        }
    });

    // Payment method pie chart
    new Chart(document.getElementById('paymentChart'), {
        type: 'doughnut',
        data: {
            labels: @json($byMethod->pluck('label')),
            datasets: [{
                data: @json($byMethod->pluck('revenue')),
                backgroundColor: ['#4f46e5', '#eab308', '#22c55e'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: { callbacks: { label: function(c) { var v = c.parsed || 0; return c.label + ': IDR ' + v.toLocaleString('id-ID'); } } }
            }
        }
    });
});
</script>
</x-app-layout>
