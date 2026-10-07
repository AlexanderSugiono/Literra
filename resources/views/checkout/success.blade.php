<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Pesanan Berhasil') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h1 class="font-serif text-2xl font-bold text-navy mb-2">Pesanan Berhasil Dibuat!</h1>
                    <p class="text-gray-600 mb-6">Simpan kode pesanan berikut untuk referensi Anda.</p>

                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <p class="text-sm text-gray-500 mb-1">Kode Pesanan</p>
                        <p class="text-3xl font-bold text-navy tracking-wider">{{ $order->order_code }}</p>
                        @if ($order->coupon)
                            <div class="mt-2 text-sm"><span class="text-gray-500">Kupon:</span> <span class="font-medium text-green-600">{{ $order->coupon->code }}</span> <span class="text-gray-400">(potongan IDR {{ number_format($order->discount_amount, 0, ',', '.') }})</span></div>
                        @endif
                        <p class="text-sm text-gray-500 mt-2">Status: <span class="font-medium text-{{ $order->status === 'paid' ? 'green' : 'yellow' }}-600">{{ $order->status === 'paid' ? 'Lunas' : 'Menunggu Pembayaran' }}</span></p>
                        <div class="mt-4 flex justify-center" id="qrcode"></div>
                        <p class="text-xs text-gray-400 mt-2">Pindai QR Code ini untuk mempercepat proses pengambilan di toko</p>
                    </div>

                    <div class="bg-{{ $order->payment_method === 'online' ? 'green' : 'yellow' }}-50 rounded-lg p-4 mb-6 text-left text-sm">
                        @if ($order->payment_method === 'online')
                            <p class="text-green-800">✓ Pesanan sudah dibayar, silakan datang ke toko untuk mengambil buku Anda dengan menunjukkan kode pesanan ini kepada kasir.</p>
                        @else
                            <p class="text-yellow-800">⏳ Silakan datang ke toko dalam <strong>24 jam</strong> untuk melakukan pembayaran dan mengambil buku Anda. Tunjukkan kode pesanan ini kepada kasir.</p>
                        @endif
                    </div>

                    <div class="border-t pt-6 flex justify-center gap-4">
                        <a href="{{ route('orders.show', $order) }}" class="inline-block px-6 py-2 bg-navy text-white rounded-md font-semibold text-sm hover:opacity-90 transition">Lihat Pesanan Saya</a>
                        <a href="{{ route('catalog.index') }}" class="inline-block text-navy hover:opacity-80 font-medium text-sm leading-8">&larr; Kembali ke Katalog</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
new QRCode(document.getElementById('qrcode'), {
    text: '{{ $order->order_code }}',
    width: 200,
    height: 200,
});
</script>
</x-app-layout>
