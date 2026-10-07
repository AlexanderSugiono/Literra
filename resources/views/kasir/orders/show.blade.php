<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Detail Pesanan') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div id="alert-area" class="mb-4 hidden"></div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @php
                        $statusLabels = ['pending_payment' => 'Menunggu Bayar', 'paid' => 'Lunas', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan', 'expired' => 'Kadaluarsa'];
                        $statusColors = ['pending_payment' => 'bg-yellow-100 text-yellow-800', 'paid' => 'bg-blue-100 text-blue-800', 'completed' => 'bg-green-100 text-green-800', 'cancelled' => 'bg-red-100 text-red-800', 'expired' => 'bg-gray-100 text-gray-600'];
                    @endphp

                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Kode Pesanan</p>
                            <p class="text-2xl font-bold font-mono text-navy">{{ $order->order_code }}</p>
                        </div>
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg text-sm">
                        <div>
                            <p class="text-gray-500">Customer</p>
                            <p class="font-medium">{{ $order->user->name ?? 'Walk-in' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tanggal Pesan</p>
                            <p class="font-medium">{{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Pembayaran</p>
                            <p class="font-medium">{{ $order->payment_method === 'online' ? 'Bayar Online' : ($order->payment_method === 'cod_instore' ? 'Bayar di Toko' : $order->payment_method) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Total</p>
                            <p class="font-bold text-navy">IDR {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if ($order->status === 'pending_payment' && $order->expires_at)
                        <div class="mb-6 px-4 py-3 rounded-lg text-sm {{ Carbon\Carbon::now()->greaterThan($order->expires_at) ? 'bg-red-50 border border-red-200 text-red-700' : 'bg-yellow-50 border border-yellow-200 text-yellow-800' }}">
                            {{ Carbon\Carbon::now()->greaterThan($order->expires_at) ? 'Pesanan ini sudah kadaluarsa.' : 'Berlaku hingga: ' . $order->expires_at->format('d M Y H:i') }}
                        </div>
                    @endif

                    <h3 class="font-semibold text-gray-900 mb-3">Item Pesanan</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->book->title }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-right">IDR {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">IDR {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if ($order->coupon)
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-sm text-gray-600 text-right">Subtotal</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">IDR {{ number_format($order->total_price + $order->discount_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-sm text-green-600 text-right">Diskon ({{ $order->coupon->code }})</td>
                                    <td class="px-4 py-2 text-sm text-green-600 text-right">- IDR {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-sm font-semibold text-gray-900 text-right border-t">Total</td>
                                    <td class="px-4 py-3 text-sm font-bold text-navy text-right border-t">IDR {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        @else
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">Total</td>
                                    <td class="px-4 py-3 text-sm font-bold text-navy text-right">IDR {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>

                    @if ($order->status === 'paid')
                        <div class="mt-6">
                            <button type="button" id="process-btn" class="px-6 py-3 bg-green-600 text-white rounded-md font-semibold text-sm hover:bg-green-500 transition">Konfirmasi Serahkan Buku</button>
                        </div>
                    @elseif ($order->status === 'pending_payment' && $order->expires_at && !Carbon\Carbon::now()->greaterThan($order->expires_at))
                        <div class="mt-6">
                            <button type="button" id="process-btn" class="px-6 py-3 bg-green-600 text-white rounded-md font-semibold text-sm hover:bg-green-500 transition">Proses Pembayaran &amp; Serahkan Buku</button>
                        </div>
                    @elseif ($order->status === 'pending_payment' && (!$order->expires_at || Carbon\Carbon::now()->greaterThan($order->expires_at)))
                        <div class="mt-6 px-4 py-3 bg-gray-100 rounded-lg text-sm text-gray-600">Pesanan sudah kadaluarsa, tidak dapat diproses.</div>
                    @else
                        <div class="mt-6 px-4 py-3 bg-gray-100 rounded-lg text-sm text-gray-600">Pesanan ini sudah {{ $statusLabels[$order->status] ?? $order->status }}.</div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('kasir.orders.index') }}" class="text-sm text-navy hover:opacity-80">&larr; Kembali ke pencarian</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@if ($order->status === 'paid' || ($order->status === 'pending_payment' && $order->expires_at && !Carbon\Carbon::now()->greaterThan($order->expires_at)))
<script>
document.getElementById('process-btn').addEventListener('click', function() {
    if (!confirm('Proses pesanan ini?')) return;
    var btn = this;
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    fetch('{{ route('kasir.orders.process', $order) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
    .then(function(result) {
        if (result.status === 422 || result.status === 400 || !result.data.success) {
            showAlert(result.data.message, 'error');
            btn.disabled = false;
            btn.textContent = 'Coba Lagi';
            return;
        }
        showAlert(result.data.message, 'success');
        btn.outerHTML = '<div class="px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded text-sm">Pesanan selesai diproses.</div>';
    })
    .catch(function() { showAlert('Gagal memproses.', 'error'); btn.disabled = false; btn.textContent = 'Coba Lagi'; });
});

function showAlert(msg, type) {
    var el = document.getElementById('alert-area');
    el.className = 'mb-4 px-4 py-3 rounded ' + (type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 'bg-green-100 border border-green-400 text-green-700');
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(function() { el.classList.add('hidden'); }, 5000);
}
</script>
@endif
</x-app-layout>
