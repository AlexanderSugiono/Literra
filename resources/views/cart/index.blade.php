<x-app-layout>
<style>
.whisper-shadow { box-shadow: 0 12px 24px -12px rgba(107,114,128,0.04); }
</style>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div id="flash-message" class="mb-4 hidden"></div>

        @if ($cartItems->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6 text-gray-300">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                </div>
                <p class="text-gray-500 text-lg mb-6">Keranjang kamu masih kosong.</p>
                <a href="{{ route('catalog.index') }}" class="inline-block bg-gold text-navy px-8 py-3 font-semibold text-sm rounded-md hover:opacity-90 transition">Jelajahi Katalog</a>
            </div>
        @else
            <header class="mb-12">
                <h1 class="font-serif text-3xl md:text-4xl font-bold text-gray-900 mb-2">Keranjang Belanja</h1>
                <p class="text-gray-500">Lengkapi koleksi Anda dengan kurasi pilihan terbaik dari perpustakaan modern Literra.</p>
            </header>

            <div id="cart-container" class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                {{-- Left: Items --}}
                <div class="lg:col-span-8 space-y-6">
                    {{-- Header Labels (hidden on mobile) --}}
                    <div class="hidden md:grid grid-cols-12 gap-4 pb-4 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-widest font-medium">
                        <div class="col-span-5">Detail Buku</div>
                        <div class="col-span-2 text-center">Jumlah</div>
                        <div class="col-span-2 text-right">Harga</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                        <div class="col-span-1"></div>
                    </div>

                    <div id="cart-items" class="space-y-6">
                        @foreach ($cartItems as $item)
                        <div class="cart-item grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-6 p-6 bg-white rounded-xl border border-gray-200 whisper-shadow" data-cart-id="{{ $item->id }}" data-price="{{ $item->book->price }}" data-stock="{{ $item->book->stock }}">
                            {{-- Cover + Info --}}
                            <div class="md:col-span-5 flex gap-4">
                                <div class="w-20 h-28 flex-shrink-0 bg-gray-100 rounded overflow-hidden shadow-sm border border-gray-200">
                                    <img src="{{ $item->book->cover_url }}" alt="{{ $item->book->title }}" class="w-full h-full object-cover rounded">
                                </div>
                                <div class="flex flex-col justify-center min-w-0">
                                    <h3 class="font-serif text-base font-bold text-gray-900 truncate">{{ $item->book->title }}</h3>
                                    <p class="text-sm text-gray-500 truncate">{{ $item->book->author }}</p>
                                    @if ($item->quantity > $item->book->stock)
                                        <p class="text-xs text-red-600 mt-1">Stok tersisa {{ $item->book->stock }}, kurangi jumlah</p>
                                    @endif
                                    <button type="button" class="cart-remove flex items-center gap-1 text-xs text-red-500 hover:text-red-700 transition-colors w-fit md:hidden mt-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>

                            {{-- Quantity --}}
                            <div class="md:col-span-2 flex md:justify-center items-center">
                                <div class="flex items-center border border-gray-300 rounded-full px-1.5 py-0.5 bg-white">
                                    <button type="button" class="qty-minus p-1 hover:text-navy transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <span class="qty-input px-4 text-sm font-medium" data-value="{{ $item->quantity }}">{{ $item->quantity }}</span>
                                    <button type="button" class="qty-plus p-1 hover:text-navy transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="md:col-span-2 flex md:justify-end items-center">
                                <span class="text-sm text-gray-600">IDR {{ number_format($item->book->price, 0, ',', '.') }}</span>
                            </div>

                            {{-- Subtotal --}}
                            <div class="md:col-span-2 flex items-center justify-end">
                                <span class="subtotal-cell text-sm font-semibold text-gray-900">IDR {{ number_format($item->book->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>

                            {{-- Desktop delete button (separate column) --}}
                            <div class="md:col-span-1 flex items-center justify-end">
                                <button type="button" class="cart-remove hidden md:flex items-center text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 text-navy hover:opacity-80 font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                            Lanjut Belanja
                        </a>
                    </div>
                </div>

                {{-- Right: Summary --}}
                <div class="lg:col-span-4">
                    <div class="lg:sticky lg:top-[80px] bg-gray-50 p-8 rounded-2xl whisper-shadow border border-gray-200">
                        <h2 class="font-serif text-xl font-bold text-gray-900 mb-6">Ringkasan Pesanan</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span id="cart-total">IDR {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="pt-4 border-t border-gray-300 flex justify-between items-center">
                                <span class="font-serif text-lg font-bold text-gray-900">Total</span>
                                <span id="cart-grand-total" class="font-serif text-lg font-bold text-gold">IDR {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="mt-6 w-full block py-4 bg-gold text-navy text-center font-semibold text-sm rounded-lg hover:opacity-90 transition-all active:scale-[0.98]">Lanjut ke Pembayaran</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function flash(msg, type) {
    var el = document.getElementById('flash-message');
    el.textContent = msg;
    el.className = 'mb-4 px-4 py-3 rounded ' + (type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 'bg-green-100 border border-green-400 text-green-700');
    el.classList.remove('hidden');
    setTimeout(function() { el.classList.add('hidden'); }, 3000);
}

function updateCartBadge(count) {
    var badge = document.getElementById('cart-badge');
    if (badge) { badge.textContent = count; badge.classList.toggle('hidden', count == 0); }
}

// Quantity plus/minus
document.querySelectorAll('.qty-plus').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var row = this.closest('.cart-item');
        var input = row.querySelector('.qty-input');
        var cartId = row.dataset.cartId;
        var stock = parseInt(row.dataset.stock);
        var newQty = parseInt(input.dataset.value) + 1;
        if (newQty > stock) { alert('Stok tidak mencukupi. Stok tersedia: ' + stock); return; }
        updateQuantity(row, cartId, newQty);
    });
});

document.querySelectorAll('.qty-minus').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var row = this.closest('.cart-item');
        var input = row.querySelector('.qty-input');
        var cartId = row.dataset.cartId;
        var newQty = parseInt(input.dataset.value) - 1;
        if (newQty < 1) return;
        updateQuantity(row, cartId, newQty);
    });
});

function updateQuantity(row, cartId, qty) {
    fetch('{{ url('/cart') }}/' + cartId + '/quantity', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ quantity: qty })
    })
    .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
    .then(function(result) {
        if (result.status === 422) { alert(result.data.message); return; }
        var data = result.data;
        row.querySelector('.qty-input').dataset.value = qty;
        row.querySelector('.qty-input').textContent = qty;
        row.querySelector('.subtotal-cell').textContent = data.formatted_subtotal;
        document.getElementById('cart-total').textContent = data.formatted_total;
        var grandTotal = document.getElementById('cart-grand-total');
        if (grandTotal) grandTotal.textContent = data.formatted_total;
        updateCartBadge(data.cart_count);
    })
    .catch(function() { flash('Gagal mengupdate quantity.', 'error'); });
}

// Remove
document.querySelectorAll('.cart-remove').forEach(function(btn) {
    btn.addEventListener('click', function() {
        if (!confirm('Hapus item ini dari keranjang?')) return;
        var row = this.closest('.cart-item');
        var cartId = row.dataset.cartId;

        fetch('{{ url('/cart') }}/' + cartId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.is_empty) {
                document.getElementById('cart-container').outerHTML =
                    '<div class="flex flex-col items-center justify-center py-20 text-center">' +
                        '<div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6 text-gray-300">' +
                            '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>' +
                        '</div>' +
                        '<p class="text-gray-500 text-lg mb-6">Keranjang kamu masih kosong.</p>' +
                        '<a href="{{ route('catalog.index') }}" class="inline-block bg-gold text-navy px-8 py-3 font-semibold text-sm rounded-md hover:opacity-90 transition">Jelajahi Katalog</a>' +
                    '</div>';
            } else {
                row.remove();
                document.getElementById('cart-total').textContent = data.formatted_total;
                var grandTotal = document.getElementById('cart-grand-total');
                if (grandTotal) grandTotal.textContent = data.formatted_total;
            }
            updateCartBadge(data.cart_count);
            flash(data.message, 'success');
        })
        .catch(function() { flash('Gagal menghapus item.', 'error'); });
    });
});
</script>
</x-app-layout>
