<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('POS - Point of Sale') }}</h2>
    </x-slot>

    <style>
        .qty-input { padding: 1px 2px !important; }
    </style>

    <div class="py-6">
        <div id="alert-area" class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4 hidden"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                {{-- Left: search + results --}}
                <div class="lg:col-span-3">
                    <form id="search-form" class="mb-4">
                        <div class="flex gap-2">
                            <input type="text" id="search-q" name="q" value="{{ $query ?? '' }}" placeholder="Cari judul buku..." autofocus class="w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy">
                            <button type="submit" class="px-4 py-2 bg-navy text-white rounded-md hover:opacity-90 font-semibold text-sm">Cari</button>
                        </div>
                    </form>

                    <div id="search-results" class="space-y-2">
                        @forelse ($books as $book)
                            <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                                <img src="{{ $book->cover_url }}" alt="" class="w-10 h-14 object-cover rounded">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $book->title }}</p>
                                    <p class="text-xs text-gray-500">Stok: {{ $book->stock }}</p>
                                    <p class="text-sm font-bold text-navy">IDR {{ number_format($book->price, 0, ',', '.') }}</p>
                                </div>
                                @if ($book->stock > 0)
                                    <button type="button" class="pos-add btn-sm px-3 py-1.5 bg-navy text-white rounded-md text-xs font-semibold hover:opacity-90" data-book-id="{{ $book->id }}">+ Tambah</button>
                                @else
                                    <span class="text-xs text-gray-400">Stok Habis</span>
                                @endif
                            </div>
                        @empty
                            @if ($query)
                                <p class="text-gray-500 text-sm text-center py-8">Tidak ada buku ditemukan untuk "{{ $query }}".</p>
                            @else
                                <p class="text-gray-400 text-sm text-center py-8">Ketik judul buku untuk mencari.</p>
                            @endif
                        @endforelse
                    </div>
                </div>

                {{-- Right: cart --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 h-full flex flex-col">
                        <div class="px-4 py-3 bg-gray-50 border-b">
                            <h3 class="font-semibold text-gray-900">Transaksi <span id="cart-count-badge" class="ml-1 text-sm font-normal text-gray-500">({{ $cart['count'] }} item)</span></h3>
                        </div>

                        <div id="cart-items" class="flex-1 overflow-y-auto p-3 space-y-2" style="max-height: 60vh;">
                            @forelse ($cart['items'] as $item)
                                <div class="cart-item flex items-center gap-2 p-2 bg-gray-50 rounded" data-book-id="{{ $item['book_id'] }}">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium truncate">{{ $item['title'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $item['formatted_price'] }}</p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" class="qty-dec px-1.5 py-0.5 border rounded text-xs hover:bg-gray-200">−</button>
                                        <input type="number" value="{{ $item['quantity'] }}" min="0" max="{{ $item['stock'] }}" class="qty-input w-10 text-center border rounded text-xs" readonly style="padding: 1px 2px;">
                                        <button type="button" class="qty-inc px-1.5 py-0.5 border rounded text-xs hover:bg-gray-200">+</button>
                                    </div>
                                    <p class="text-xs font-semibold w-20 text-right">{{ $item['formatted_subtotal'] }}</p>
                                    <button type="button" class="cart-remove text-red-500 hover:text-red-700 text-xs ml-1">&times;</button>
                                </div>
                            @empty
                                <p id="empty-cart-msg" class="text-gray-400 text-xs text-center py-8">Belum ada item.</p>
                            @endforelse
                        </div>

                        <div class="px-4 py-2 border-t">
                            <div class="flex gap-1">
                                <input type="text" id="pos-coupon-input" placeholder="Kode kupon..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy text-xs uppercase">
                                <button type="button" id="pos-apply-coupon" class="px-2 py-1 bg-navy text-white rounded-md text-xs font-semibold hover:opacity-90">Terapkan</button>
                            </div>
                            <div id="pos-coupon-feedback" class="mt-1 text-xs hidden"></div>
                            @if ($appliedCoupon)
                                <div id="pos-applied-coupon" class="mt-1 flex items-center justify-between bg-green-50 px-2 py-1 rounded text-xs">
                                    <span class="text-green-700">Kupon: <strong>{{ $appliedCoupon->code }}</strong></span>
                                    <button type="button" id="pos-remove-coupon" class="text-red-600 hover:text-red-900 text-xs">&times; Hapus</button>
                                </div>
                            @endif
                        </div>
                        <div class="border-t px-4 py-3 bg-gray-50">
                            <div id="pos-subtotal-row" class="flex justify-between items-center text-sm mb-1 {{ $cart['discount'] > 0 ? '' : 'hidden' }}">
                                <span class="text-gray-600">Subtotal</span>
                                <span id="pos-subtotal-display" class="text-gray-900">{{ $cart['formatted_subtotal'] }}</span>
                            </div>
                            <div id="pos-discount-row" class="flex justify-between items-center text-sm mb-1 {{ $cart['discount'] > 0 ? '' : 'hidden' }}">
                                <span class="text-green-600">Diskon</span>
                                <span id="pos-discount-display" class="text-green-600">- {{ $cart['formatted_discount'] }}</span>
                            </div>
                            <div class="flex justify-between items-center text-base font-bold mb-3">
                                <span>Total</span>
                                <span id="cart-total" class="text-navy">{{ $cart['formatted_total'] }}</span>
                            </div>
                            <button id="checkout-btn" type="button" class="w-full py-2.5 bg-green-600 text-white rounded-md font-semibold text-sm hover:bg-green-500 transition {{ empty($cart['items']) ? 'opacity-50 cursor-not-allowed' : '' }}" {{ empty($cart['items']) ? 'disabled' : '' }}>Proses Transaksi (IDR {{ number_format($cart['total'], 0, ',', '.') }})</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Receipt modal --}}
    <div id="receipt-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Transaksi Berhasil</h3>
            <p class="text-2xl font-mono font-bold text-navy mb-4" id="receipt-code"></p>

            <div id="receipt-items" class="text-left text-sm border-t border-b py-3 mb-4 space-y-1"></div>

            <div id="receipt-coupon-row" class="hidden text-sm mb-2">
                <div class="flex justify-between text-green-600"><span>Diskon (<span id="receipt-coupon-code"></span>)</span><span id="receipt-discount-amount"></span></div>
            </div>
            <div class="flex justify-between font-bold text-base mb-4">
                <span>Total</span>
                <span id="receipt-total" class="text-navy"></span>
            </div>
            <p class="text-xs text-gray-500 mb-4" id="receipt-meta"></p>

            <button type="button" onclick="location.href='{{ route('kasir.pos') }}'" class="w-full py-2 bg-navy text-white rounded-md font-semibold text-sm hover:opacity-90">Transaksi Baru</button>
        </div>
    </div>

<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function showAlert(msg, type) {
    var el = document.getElementById('alert-area');
    el.className = 'max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4 px-4 py-3 rounded ' + (type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 'bg-green-100 border border-green-400 text-green-700');
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(function() { el.classList.add('hidden'); }, 4000);
}

function renderCart(data) {
    var container = document.getElementById('cart-items');
    var totalEl = document.getElementById('cart-total');
    var btn = document.getElementById('checkout-btn');

    document.getElementById('cart-count-badge').textContent = '(' + data.count + ' item)';

    if (data.items.length === 0) {
        container.innerHTML = '<p id="empty-cart-msg" class="text-gray-400 text-xs text-center py-8">Belum ada item.</p>';
        totalEl.textContent = 'IDR 0';
        btn.textContent = 'Proses Transaksi (IDR 0)';
        btn.disabled = true;
        btn.className = 'w-full py-2.5 bg-green-600 text-white rounded-md font-semibold text-sm opacity-50 cursor-not-allowed';
        return;
    }

    var html = '';
    data.items.forEach(function(item) {
        var qty = Number(item.quantity) || 0;
        html += '<div class="cart-item flex items-center gap-2 p-2 bg-gray-50 rounded" data-book-id="' + item.book_id + '">' +
            '<div class="flex-1 min-w-0"><p class="text-xs font-medium truncate">' + item.title + '</p><p class="text-xs text-gray-500">' + item.formatted_price + '</p></div>' +
            '<div class="flex items-center gap-1">' +
                '<button type="button" class="qty-dec px-1.5 py-0.5 border rounded text-xs hover:bg-gray-200">−</button>' +
                '<input type="number" value="' + qty + '" min="0" max="' + Number(item.stock) + '" class="qty-input w-10 text-center border rounded text-xs" readonly>' +
                '<button type="button" class="qty-inc px-1.5 py-0.5 border rounded text-xs hover:bg-gray-200">+</button>' +
            '</div>' +
            '<p class="text-xs font-semibold w-20 text-right">' + item.formatted_subtotal + '</p>' +
            '<button type="button" class="cart-remove text-red-500 hover:text-red-700 text-xs ml-1">&times;</button>' +
        '</div>';
    });
    container.innerHTML = html;
    totalEl.textContent = data.formatted_total;
    btn.textContent = 'Proses Transaksi (' + data.formatted_total + ')';
    btn.disabled = false;
    btn.className = 'w-full py-2.5 bg-green-600 text-white rounded-md font-semibold text-sm hover:bg-green-500 transition';
    attachCartEvents();

    // Update coupon/total breakdown
    var subRow = document.getElementById('pos-subtotal-row');
    var discRow = document.getElementById('pos-discount-row');
    var subDisp = document.getElementById('pos-subtotal-display');
    var discDisp = document.getElementById('pos-discount-display');
    if (data.discount > 0) {
        subRow.classList.remove('hidden');
        discRow.classList.remove('hidden');
        subDisp.textContent = data.formatted_subtotal;
        discDisp.textContent = '- ' + data.formatted_discount;
    } else {
        subRow.classList.add('hidden');
        discRow.classList.add('hidden');
    }
}

function attachCartEvents() {
    document.querySelectorAll('.qty-inc').forEach(function(b) {
        b.addEventListener('click', function() {
            var row = this.closest('.cart-item');
            var bookId = row.dataset.bookId;
            var input = row.querySelector('.qty-input');
            var newQty = parseInt(input.value) + 1;
            updateCartItem(bookId, newQty);
        });
    });
    document.querySelectorAll('.qty-dec').forEach(function(b) {
        b.addEventListener('click', function() {
            var row = this.closest('.cart-item');
            var bookId = row.dataset.bookId;
            var input = row.querySelector('.qty-input');
            var newQty = parseInt(input.value) - 1;
            updateCartItem(bookId, newQty);
        });
    });
    document.querySelectorAll('.cart-remove').forEach(function(b) {
        b.addEventListener('click', function() {
            var bookId = this.closest('.cart-item').dataset.bookId;
            fetch('{{ route('kasir.pos.remove') }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
                body: JSON.stringify({ book_id: parseInt(bookId) })
            })
            .then(function(r) { return r.json(); })
            .then(function(d) { if (d.success) renderCart(d); else showAlert(d.message, 'error'); })
            .catch(function() { showAlert('Gagal menghapus item.', 'error'); });
        });
    });
}

function updateCartItem(bookId, qty) {
    fetch('{{ route('kasir.pos.update') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
        body: JSON.stringify({ book_id: parseInt(bookId), quantity: qty })
    })
    .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
    .then(function(result) {
        if (result.status === 422) { showAlert(result.data.message, 'error'); return; }
        if (result.data.success) renderCart(result.data);
    })
    .catch(function() { showAlert('Gagal mengupdate.', 'error'); });
}

// Search AJAX
document.getElementById('search-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var q = document.getElementById('search-q').value;
    fetch('{{ route('kasir.pos') }}?q=' + encodeURIComponent(q), {
        headers: { 'Accept': 'text/html' }
    })
    .then(function(r) { return r.text(); })
    .then(function(html) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');
        var results = doc.getElementById('search-results');
        document.getElementById('search-results').innerHTML = results.innerHTML;
        attachAddButtons();
    });
});

function attachAddButtons() {
    document.querySelectorAll('.pos-add').forEach(function(b) {
        b.addEventListener('click', function() {
            var bookId = this.dataset.bookId;
            fetch('{{ route('kasir.pos.add') }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
                body: JSON.stringify({ book_id: parseInt(bookId) })
            })
            .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
            .then(function(result) {
                if (result.status === 422) { showAlert(result.data.message, 'error'); return; }
                if (result.data.success) renderCart(result.data);
            })
            .catch(function() { showAlert('Gagal menambah item.', 'error'); });
        });
    });
}

// Coupon apply
document.getElementById('pos-apply-coupon').addEventListener('click', applyPosCoupon);
document.getElementById('pos-coupon-input').addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); applyPosCoupon(); } });

function applyPosCoupon() {
    var code = document.getElementById('pos-coupon-input').value.trim();
    if (!code) return;
    var btn = document.getElementById('pos-apply-coupon');
    var fb = document.getElementById('pos-coupon-feedback');
    btn.disabled = true; btn.textContent = '...';

    fetch('{{ route('kasir.pos.apply-coupon') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
        body: JSON.stringify({ code: code })
    })
    .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
    .then(function(result) {
        fb.classList.remove('hidden');
        if (result.status === 422 || !result.data.valid) {
            fb.className = 'mt-1 text-xs text-red-600';
            fb.textContent = result.data.message || 'Kupon tidak valid.';
            return;
        }
        fb.className = 'mt-1 text-xs text-green-600';
        fb.textContent = '';
        document.getElementById('pos-coupon-input').value = '';

        var existingRow = document.getElementById('pos-applied-coupon');
        if (existingRow) existingRow.remove();
        var row = document.createElement('div');
        row.id = 'pos-applied-coupon';
        row.className = 'mt-1 flex items-center justify-between bg-green-50 px-2 py-1 rounded text-xs';
        row.innerHTML = '<span class="text-green-700">Kupon: <strong>' + result.data.code + '</strong></span><button type="button" class="pos-remove-coupon-btn text-red-600 hover:text-red-900 text-xs">&times; Hapus</button>';
        fb.parentNode.insertBefore(row, fb.nextSibling);
        row.querySelector('.pos-remove-coupon-btn').addEventListener('click', removePosCoupon);

        document.getElementById('pos-subtotal-row').classList.remove('hidden');
        document.getElementById('pos-discount-row').classList.remove('hidden');
        document.getElementById('pos-subtotal-display').textContent = result.data.formatted_subtotal;
        document.getElementById('pos-discount-display').textContent = '- ' + result.data.formatted_discount;
        document.getElementById('cart-total').textContent = result.data.formatted_total;
        var coBtn = document.getElementById('checkout-btn');
        coBtn.textContent = 'Proses Transaksi (' + result.data.formatted_total + ')';
        coBtn.disabled = false;
        coBtn.className = 'w-full py-2.5 bg-green-600 text-white rounded-md font-semibold text-sm hover:bg-green-500 transition';
    })
    .catch(function() { fb.className = 'mt-1 text-xs text-red-600'; fb.textContent = 'Terjadi kesalahan.'; })
    .finally(function() { btn.disabled = false; btn.textContent = 'Terapkan'; });
}

function removePosCoupon() {
    fetch('{{ route('kasir.pos.remove-coupon') }}', {
        method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.removed) renderCart(data);
        var row = document.getElementById('pos-applied-coupon');
        if (row) row.remove();
        document.getElementById('pos-subtotal-row').classList.add('hidden');
        document.getElementById('pos-discount-row').classList.add('hidden');
        document.getElementById('pos-coupon-feedback').className = 'mt-1 text-xs text-gray-500';
        document.getElementById('pos-coupon-feedback').textContent = 'Kupon dihapus.';
        document.getElementById('pos-coupon-feedback').classList.remove('hidden');
    });
}

var existingRemove = document.getElementById('pos-remove-coupon');
if (existingRemove) existingRemove.addEventListener('click', removePosCoupon);

// Checkout
document.getElementById('checkout-btn').addEventListener('click', function() {
    if (this.disabled) return;
    this.textContent = 'Memproses...';
    this.disabled = true;

    fetch('{{ route('kasir.pos.checkout') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
    .then(function(result) {
        if (result.status === 422 || !result.data.success) {
            showAlert(result.data.message, 'error');
            return;
        }
        var d = result.data;
        document.getElementById('receipt-code').textContent = d.order_code;
        var itemsHtml = '';
        d.items.forEach(function(item) {
            itemsHtml += '<div class="flex justify-between text-xs"><span>' + item.book.title + ' x' + item.quantity + '</span><span>IDR ' + item.book.price.toLocaleString('id-ID') + '</span></div>';
        });
        document.getElementById('receipt-items').innerHTML = itemsHtml;
        document.getElementById('receipt-total').textContent = d.formatted_total;
        document.getElementById('receipt-meta').textContent = 'Kasir: ' + d.kasir + ' | ' + d.paid_at;

        var couponRow = document.getElementById('receipt-coupon-row');
        if (d.coupon_code) {
            couponRow.classList.remove('hidden');
            document.getElementById('receipt-coupon-code').textContent = d.coupon_code;
            document.getElementById('receipt-discount-amount').textContent = '- ' + d.formatted_discount;
        } else {
            couponRow.classList.add('hidden');
        }

        document.getElementById('receipt-modal').classList.remove('hidden');
        renderCart({ items: [], total: 0, formatted_total: 'IDR 0', count: 0, discount: 0, formatted_subtotal: 'IDR 0', formatted_discount: '' });
    })
    .catch(function() { showAlert('Gagal memproses transaksi.', 'error'); })
    .finally(function() {
        this.textContent = 'Proses Transaksi';
        this.disabled = false;
    }.bind(this));
});

attachCartEvents();
attachAddButtons();
</script>
</x-app-layout>
