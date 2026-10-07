<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Semua Pesanan') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <form method="GET" class="mb-4 grid grid-cols-2 md:grid-cols-5 gap-3">
                <div>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending_payment" {{ request('status') === 'pending_payment' ? 'selected' : '' }}>Menunggu Bayar</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                    </select>
                </div>
                <div>
                    <select name="payment_method" class="w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy text-sm">
                        <option value="">Semua Pembayaran</option>
                        <option value="online" {{ request('payment_method') === 'online' ? 'selected' : '' }}>Online</option>
                        <option value="cod_instore" {{ request('payment_method') === 'cod_instore' ? 'selected' : '' }}>COD</option>
                        <option value="pos_walkin" {{ request('payment_method') === 'pos_walkin' ? 'selected' : '' }}>POS Walk-in</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy text-sm" placeholder="Dari tanggal">
                </div>
                <div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy text-sm" placeholder="Sampai tanggal">
                </div>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode/nama..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy text-sm">
                    <button type="submit" class="px-3 py-2 bg-navy text-white rounded-md text-xs font-semibold hover:opacity-90">Filter</button>
                    @if (request()->anyFilled(['status', 'payment_method', 'date_from', 'date_to', 'search']))
                        <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md text-xs font-semibold hover:bg-gray-300">Reset</a>
                    @endif
                </div>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-mono font-medium">{{ $order->order_code }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $order->user->name ?? 'Walk-in' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $order->kasir->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $order->payment_method === 'online' ? 'Online' : ($order->payment_method === 'cod_instore' ? 'COD' : 'POS') }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $sc = ['pending_payment'=>'bg-yellow-100 text-yellow-800','paid'=>'bg-blue-100 text-blue-800','completed'=>'bg-green-100 text-green-800','cancelled'=>'bg-red-100 text-red-800','expired'=>'bg-gray-100 text-gray-600'];
                                            $sl = ['pending_payment'=>'Menunggu Bayar','paid'=>'Lunas','completed'=>'Selesai','cancelled'=>'Dibatalkan','expired'=>'Kadaluarsa'];
                                        @endphp
                                        <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full {{ $sc[$order->status] ?? 'bg-gray-100' }}">{{ $sl[$order->status] ?? $order->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right">IDR {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="text-navy hover:opacity-80 text-sm">Detail</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada pesanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-6">{{ $orders->links() }}</div>
        </div>
    </div>
</x-app-layout>
