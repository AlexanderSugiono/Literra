<x-app-layout>
<style>
.status-pill { padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em; }
.order-card-hover:hover { box-shadow: 0 12px 24px -10px rgba(0,0,0,0.04); transform: translateY(-2px); }
</style>

<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="mb-8 flex items-center space-x-2 text-sm text-gray-400">
            <a href="{{ route('catalog.index') }}" class="hover:text-navy transition-colors">Beranda</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-800">Pesanan Saya</span>
        </nav>

        <header class="mb-12">
            <h1 class="font-serif text-3xl md:text-4xl font-bold text-gray-900 mb-2">Pesanan Saya</h1>
            <p class="text-gray-500 max-w-2xl">Lihat semua riwayat pesanan dan status pengambilan buku Anda di Literra.</p>
        </header>

        @if ($orders->isEmpty())
            {{-- Empty State --}}
            <div class="text-center py-24 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300">
                <div class="max-w-md mx-auto">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm bg-white">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h3 class="font-serif text-2xl font-bold text-gray-800 mb-3">Kamu belum memiliki riwayat pesanan</h3>
                    <p class="text-gray-500 mb-8">Mulailah menjelajahi koleksi buku kurasi kami dan temukan bacaan Anda berikutnya hari ini.</p>
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center px-8 py-4 bg-navy text-white rounded-lg font-bold hover:opacity-90 transition-all active:scale-95">
                        Mulai Belanja
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        @else
            @php
                $statusColors = [
                    'pending_payment' => 'bg-gold/30 text-navy',
                    'paid' => 'bg-blue-100 text-blue-800',
                    'completed' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    'expired' => 'bg-gray-200 text-gray-500',
                ];
                $statusLabels = [
                    'pending_payment' => 'Menunggu Bayar',
                    'paid' => 'Lunas',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                    'expired' => 'Kadaluarsa',
                ];
                $methodLabels = [
                    'online' => 'Online',
                    'cod_instore' => 'COD',
                    'pos_walkin' => 'POS',
                ];
            @endphp

            {{-- Desktop Table --}}
            <div class="hidden lg:block bg-white rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-8 py-5 text-xs text-gray-500 uppercase tracking-wider font-medium">Order ID</th>
                            <th class="px-6 py-5 text-xs text-gray-500 uppercase tracking-wider font-medium">Tanggal</th>
                            <th class="px-6 py-5 text-xs text-gray-500 uppercase tracking-wider font-medium">Pembayaran</th>
                            <th class="px-6 py-5 text-xs text-gray-500 uppercase tracking-wider font-medium">Status</th>
                            <th class="px-6 py-5 text-xs text-gray-500 uppercase tracking-wider font-medium text-right">Total</th>
                            <th class="px-8 py-5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-8 py-6">
                                <span class="font-medium text-gray-900 font-mono">{{ $order->order_code }}</span>
                                <div class="text-xs text-gray-400 mt-1">{{ $order->items_count }} Buku</div>
                            </td>
                            <td class="px-6 py-6 text-sm text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-6 text-sm text-gray-600">{{ $methodLabels[$order->payment_method] ?? $order->payment_method }}</td>
                            <td class="px-6 py-6">
                                <span class="status-pill inline-block {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                            </td>
                            <td class="px-6 py-6 font-semibold text-gray-900 text-right">IDR {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('orders.show', $order) }}" class="text-navy font-medium text-sm border-b border-navy/20 hover:border-navy transition-all">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="lg:hidden space-y-4">
                @foreach ($orders as $order)
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm order-card-hover transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Order ID</div>
                            <div class="font-bold text-gray-900 font-mono">{{ $order->order_code }}</div>
                        </div>
                        <span class="status-pill {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Tanggal</div>
                            <div class="text-sm">{{ $order->created_at->format('d M Y') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total</div>
                            <div class="font-bold text-gray-900">IDR {{ number_format($order->total_price, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <span class="text-xs text-gray-400">{{ $order->items_count }} Buku &bull; {{ $methodLabels[$order->payment_method] ?? $order->payment_method }}</span>
                        <a href="{{ route('orders.show', $order) }}" class="text-navy font-medium text-sm border-b-2 border-gold">Lihat Detail</a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>
