<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Detail Pesanan') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Kode Pesanan</p>
                            <p class="text-2xl font-bold font-mono text-navy">{{ $order->order_code }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-blue-100 text-blue-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'expired' => 'bg-gray-100 text-gray-600',
                            ];
                            $statusLabels = [
                                'pending_payment' => 'Menunggu Bayar',
                                'paid' => 'Lunas',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                                'expired' => 'Kadaluarsa',
                            ];
                        @endphp
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                        <div class="hidden md:block" id="qrcode"></div>
                    </div>

                    @if ($order->status === 'pending_payment' && $order->expires_at)
                        <div class="mb-6 px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                            Berlaku hingga: <strong>{{ $order->expires_at->format('d M Y H:i') }}</strong> — Silakan datang ke toko untuk membayar.
                        </div>
                    @endif

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg text-sm">
                        <div>
                            <p class="text-gray-500">Tanggal Pesan</p>
                            <p class="font-medium">{{ $order->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Pembayaran</p>
                            <p class="font-medium">{{ $order->payment_method === 'online' ? 'Bayar Online' : 'Bayar di Toko' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Status Pembayaran</p>
                            <p class="font-medium">{{ $order->payment->status === 'paid' ? 'Lunas' : 'Belum Dibayar' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Total</p>
                            <p class="font-bold text-navy">IDR {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if ($order->coupon)
                        <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm">
                            Kupon: <strong>{{ $order->coupon->code }}</strong> — potongan <strong>IDR {{ number_format($order->discount_amount, 0, ',', '.') }}</strong>
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

                    <div class="mt-6 flex justify-center md:hidden" id="qrcode-mobile"></div>

                    <div class="mt-8">
                        <a href="{{ route('orders.index') }}" class="text-sm text-navy hover:opacity-80">&larr; Kembali ke daftar pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
new QRCode(document.getElementById('qrcode'), { text: '{{ $order->order_code }}', width: 120, height: 120 });
if (document.getElementById('qrcode-mobile')) {
    new QRCode(document.getElementById('qrcode-mobile'), { text: '{{ $order->order_code }}', width: 200, height: 200 });
}
</script>
</x-app-layout>
