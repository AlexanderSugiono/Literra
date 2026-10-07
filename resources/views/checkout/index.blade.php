<x-app-layout>
<style>
.whisper-shadow { box-shadow: 0 12px 24px rgba(107,114,128,0.04); }
.active-payment-card { border-color: #0A192F; background-color: #ffffff; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
</style>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        @if (!empty($stockIssues))
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                <p class="font-semibold">Stok beberapa buku tidak mencukupi:</p>
                <ul class="list-disc ml-5 mt-1 text-sm">
                    @foreach ($stockIssues as $issue)
                        <li>"{{ $issue->book->title }}" — stok tersisa {{ $issue->book->stock }}, kamu memesan {{ $issue->quantity }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('cart.index') }}" class="mt-2 inline-block text-navy hover:opacity-80 font-medium text-sm">&larr; Kembali ke keranjang</a>
            </div>
        @endif

        <h1 class="font-serif text-3xl md:text-4xl font-bold text-gray-900 mb-12">Checkout</h1>

        <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

            {{-- Left: Main Content --}}
            <div class="lg:col-span-8 space-y-12">

                {{-- Payment Method --}}
                <section>
                    <div class="flex items-center gap-4 mb-8">
                        <span class="w-8 h-8 rounded-full bg-navy text-white flex items-center justify-center font-bold text-sm">1</span>
                        <h2 class="font-serif text-xl font-bold text-gray-900">Metode Pembayaran</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div id="payment-online" class="border-2 border-navy p-8 rounded-xl cursor-pointer transition-all duration-300 active-payment-card" onclick="selectPayment('online')">
                            <input type="radio" name="payment_method" value="online" class="hidden" checked>
                            <div class="flex justify-between items-start mb-6">
                                <div class="p-3 bg-gold rounded-lg">
                                    <svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </div>
                                <div class="radio-indicator w-5 h-5 rounded-full border-2 border-navy flex items-center justify-center">
                                    <div class="w-2.5 h-2.5 rounded-full bg-navy"></div>
                                </div>
                            </div>
                            <h3 class="font-bold text-lg mb-2">Bayar Online</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">Bayar sekarang, langsung aktif, tunjukkan kode pesanan saat ambil buku di toko.</p>
                        </div>
                        <div id="payment-cod" class="border-2 border-gray-200 p-8 rounded-xl cursor-pointer transition-all duration-300 hover:border-navy/30" onclick="selectPayment('cod')">
                            <input type="radio" name="payment_method" value="cod_instore" class="hidden">
                            <div class="flex justify-between items-start mb-6">
                                <div class="p-3 bg-gray-100 rounded-lg">
                                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div class="radio-indicator w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                    <div class="w-2.5 h-2.5 rounded-full bg-navy opacity-0 transition-opacity"></div>
                                </div>
                            </div>
                            <h3 class="font-bold text-lg mb-2">Bayar di Toko (COD)</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">Pesan sekarang, bayar saat ambil buku di toko dalam 24 jam.</p>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-4" />
                </section>

                {{-- Order Summary --}}
                <section>
                    <div class="flex items-center gap-4 mb-8">
                        <span class="w-8 h-8 rounded-full bg-navy text-white flex items-center justify-center font-bold text-sm">2</span>
                        <h2 class="font-serif text-xl font-bold text-gray-900">Ringkasan Pesanan</h2>
                    </div>
                    <div class="bg-white rounded-xl whisper-shadow overflow-hidden border border-gray-200">
                        <div class="divide-y divide-gray-200">
                            @foreach ($cartItems as $item)
                            <div class="p-6 md:p-8 flex gap-6 md:gap-8">
                                <div class="w-20 h-28 md:w-28 md:h-36 flex-shrink-0 bg-gray-100 rounded overflow-hidden shadow-sm border border-gray-200">
                                    <img src="{{ $item->book->cover_url }}" alt="{{ $item->book->title }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex flex-col justify-between py-1 flex-grow min-w-0">
                                    <div>
                                        <h4 class="font-serif font-bold text-lg text-gray-900">{{ $item->book->title }}</h4>
                                        <p class="text-gray-500 text-sm">Oleh {{ $item->book->author }}</p>
                                    </div>
                                    <div class="flex items-center justify-between mt-4">
                                        <span class="text-sm text-gray-500">Jumlah: {{ $item->quantity }}</span>
                                        <span class="font-bold text-gray-900">IDR {{ number_format($item->book->price * $item->quantity, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- Info Banner --}}
                <div class="bg-gold/20 border border-gold p-6 rounded-lg flex gap-4">
                    <svg class="w-5 h-5 text-gold shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        <strong>Informasi Penting:</strong> Literra saat ini hanya melayani pengambilan di toko (Click-and-Collect). Pesanan Anda tidak akan dikirim ke alamat rumah.
                    </p>
                </div>
            </div>

            {{-- Right: Sidebar --}}
            <div class="lg:col-span-4">
                <div class="lg:sticky lg:top-[80px] space-y-6">

                    {{-- Coupon Card --}}
                    <div class="bg-white p-6 rounded-xl whisper-shadow border border-gray-200">
                        <label class="block font-bold text-sm mb-3" for="coupon-input">Kode Kupon</label>
                        <div class="flex gap-2">
                            <input type="text" id="coupon-input" placeholder="Masukkan kode" class="flex-grow border-gray-300 border rounded-lg px-4 py-2.5 focus:ring-navy focus:border-navy transition-all text-sm uppercase">
                            <button type="button" id="apply-coupon-btn" class="bg-gold text-navy px-5 py-2.5 rounded-lg font-bold text-sm hover:opacity-90 transition-all">Terapkan</button>
                        </div>
                        <div id="coupon-feedback" class="mt-2 text-xs hidden"></div>
                        @if ($appliedCoupon)
                            <div id="applied-coupon-row" class="mt-3 flex items-center justify-between bg-green-50 px-3 py-2 rounded text-sm">
                                <span class="text-green-700">Kupon: <strong>{{ $appliedCoupon->code }}</strong></span>
                                <button type="button" id="remove-coupon-btn" class="text-red-600 hover:text-red-900 text-xs">&times; Hapus</button>
                            </div>
                        @endif
                    </div>

                    {{-- Order Total Card (Navy) --}}
                    <div class="bg-[#0A192F] text-white p-8 rounded-xl shadow-xl">
                        <h3 class="font-serif text-xl mb-8 pb-4 border-b border-white/10">Ringkasan Biaya</h3>
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center text-white/70">
                                <span class="text-sm">Subtotal</span>
                                <span id="subtotal-display" class="font-medium">IDR {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div id="discount-row" class="flex justify-between items-center text-gold {{ $discount > 0 ? '' : 'hidden' }}">
                                <span class="text-sm font-bold">Diskon</span>
                                <span id="discount-display" class="font-bold">- IDR {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mb-10">
                            <span class="text-lg font-bold">Total Akhir</span>
                            <span id="final-total-display" class="text-2xl font-bold text-gold">IDR {{ number_format($finalTotal, 0, ',', '.') }}</span>
                        </div>
                        <button type="submit" class="w-full bg-gold text-navy py-5 rounded-lg font-bold text-lg hover:opacity-90 active:scale-[0.98] transition-all shadow-lg flex items-center justify-center gap-3 {{ !empty($stockIssues) ? 'opacity-50 cursor-not-allowed' : '' }}" {{ !empty($stockIssues) ? 'disabled' : '' }}>
                            Buat Pesanan
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>

<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function selectPayment(method) {
    var onlineCard = document.getElementById('payment-online');
    var codCard = document.getElementById('payment-cod');
    var radioOnline = onlineCard.querySelector('input[value="online"]');
    var radioCod = codCard.querySelector('input[value="cod_instore"]');

    onlineCard.classList.remove('active-payment-card', 'border-navy');
    onlineCard.classList.add('border-gray-200');
    codCard.classList.remove('active-payment-card', 'border-navy');
    codCard.classList.add('border-gray-200');

    onlineCard.querySelector('.radio-indicator div').classList.add('opacity-0');
    codCard.querySelector('.radio-indicator div').classList.add('opacity-0');
    onlineCard.querySelector('.radio-indicator').classList.remove('border-navy');
    codCard.querySelector('.radio-indicator').classList.remove('border-navy');

    if (method === 'online') {
        radioOnline.checked = true;
        onlineCard.classList.add('active-payment-card', 'border-navy');
        onlineCard.classList.remove('border-gray-200');
        onlineCard.querySelector('.radio-indicator').classList.add('border-navy');
        onlineCard.querySelector('.radio-indicator div').classList.remove('opacity-0');
    } else {
        radioCod.checked = true;
        codCard.classList.add('active-payment-card', 'border-navy');
        codCard.classList.remove('border-gray-200');
        codCard.querySelector('.radio-indicator').classList.add('border-navy');
        codCard.querySelector('.radio-indicator div').classList.remove('opacity-0');
    }
}

// Coupon
document.getElementById('apply-coupon-btn').addEventListener('click', applyCoupon);
document.getElementById('coupon-input').addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); applyCoupon(); } });

function applyCoupon() {
    var code = document.getElementById('coupon-input').value.trim();
    if (!code) return;
    var btn = document.getElementById('apply-coupon-btn');
    var fb = document.getElementById('coupon-feedback');
    btn.disabled = true;
    btn.textContent = '...';

    fetch('{{ route('checkout.apply-coupon') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ code: code })
    })
    .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
    .then(function(result) {
        fb.classList.remove('hidden');
        if (result.status === 422 || !result.data.valid) {
            fb.className = 'mt-2 text-xs text-red-600';
            fb.textContent = result.data.message || 'Kupon tidak valid.';
            return;
        }
        fb.className = 'mt-2 text-xs text-green-600';
        fb.textContent = '';
        document.getElementById('coupon-input').value = '';
        document.getElementById('discount-row').classList.remove('hidden');
        document.getElementById('discount-display').textContent = '- ' + result.data.formatted_discount;
        document.getElementById('final-total-display').textContent = result.data.formatted_total;

        var existingRow = document.getElementById('applied-coupon-row');
        if (existingRow) existingRow.remove();

        var row = document.createElement('div');
        row.id = 'applied-coupon-row';
        row.className = 'mt-3 flex items-center justify-between bg-green-50 px-3 py-2 rounded text-sm';
        row.innerHTML = '<span class="text-green-700">Kupon: <strong>' + result.data.code + '</strong></span>' +
            '<button type="button" class="remove-coupon-btn text-red-600 hover:text-red-900 text-xs">&times; Hapus</button>';
        fb.parentNode.insertBefore(row, fb.nextSibling);
        row.querySelector('.remove-coupon-btn').addEventListener('click', removeCoupon);
    })
    .catch(function() { fb.className = 'mt-2 text-xs text-red-600'; fb.textContent = 'Terjadi kesalahan.'; })
    .finally(function() { btn.disabled = false; btn.textContent = 'Terapkan'; });
}

function removeCoupon() {
    fetch('{{ route('checkout.remove-coupon') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function() {
        var row = document.getElementById('applied-coupon-row');
        if (row) row.remove();
        document.getElementById('discount-row').classList.add('hidden');
        document.getElementById('final-total-display').textContent = document.getElementById('subtotal-display').textContent;
        var fb = document.getElementById('coupon-feedback');
        fb.className = 'mt-2 text-xs text-gray-500';
        fb.textContent = 'Kupon dihapus.';
        fb.classList.remove('hidden');
    });
}

var existingRemoveBtn = document.getElementById('remove-coupon-btn');
if (existingRemoveBtn) {
    existingRemoveBtn.addEventListener('click', removeCoupon);
}
</script>
</x-app-layout>
