<x-app-layout>
<style>
.book-card-shadow { box-shadow: 0 4px 20px -8px rgba(0,0,0,0.08); }
</style>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div id="flash-message" class="mb-4 hidden"></div>

        {{-- Header --}}
        <div class="mb-10">
            <h1 class="font-serif text-3xl md:text-4xl font-bold text-gray-900 mb-2">Wishlist Saya</h1>
            <p class="text-gray-500">{{ $wishlists->count() }} Judul Tersimpan</p>
        </div>

        @if ($wishlists->isEmpty())
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6 text-gray-300">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h2 class="font-serif text-2xl font-bold text-gray-800 mb-4">Wishlist kamu masih kosong</h2>
                <p class="text-gray-500 mb-10 max-w-md">Mulai jelajahi koleksi kurasi kami dan temukan buku yang menginspirasi Anda selanjutnya.</p>
                <a href="{{ route('catalog.index') }}" class="inline-block bg-gold text-navy px-10 py-3 font-semibold text-sm rounded-md hover:opacity-90 transition uppercase tracking-widest">Jelajahi Katalog</a>
            </div>
        @else
            {{-- Grid --}}
            <div id="wishlist-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($wishlists as $wish)
                <div class="wishlist-card bg-white rounded-xl book-card-shadow overflow-hidden group flex flex-col h-full transition-transform duration-300 hover:-translate-y-1" data-book-id="{{ $wish->book_id }}">
                    {{-- Cover --}}
                    <div class="relative aspect-[2/3] overflow-hidden bg-gray-100">
                        <a href="{{ route('catalog.show', $wish->book) }}">
                            <img src="{{ $wish->book->cover_url }}" alt="{{ $wish->book->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </a>
                        {{-- Stock badge (bottom left) --}}
                        <div class="absolute bottom-3 left-3 z-10">
                            @if ($wish->book->stock > 0)
                                <span class="inline-block bg-green-500/90 text-white text-[11px] font-bold px-2 py-1 rounded uppercase tracking-wider">Tersedia</span>
                            @else
                                <span class="inline-block bg-red-500/90 text-white text-[11px] font-bold px-2 py-1 rounded uppercase tracking-wider">Stok Habis</span>
                            @endif
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-serif text-base font-bold text-gray-900 leading-tight truncate">{{ $wish->book->title }}</h3>
                        <p class="text-sm text-gray-500 italic mt-1">{{ $wish->book->author }}</p>
                        <div class="mt-auto pt-3">
                            <p class="font-bold text-gold text-base mb-3">IDR {{ number_format($wish->book->price, 0, ',', '.') }}</p>
                            @if ($wish->book->stock > 0)
                                <button type="button" data-book-id="{{ $wish->book->id }}" data-stock="{{ $wish->book->stock }}" class="wl-cart-add w-full bg-navy text-white py-2.5 px-4 rounded text-xs font-semibold hover:opacity-90 transition-all flex items-center justify-center gap-2 active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                    + Keranjang
                                </button>
                            @else
                                <button type="button" disabled class="w-full bg-gray-200 text-gray-400 py-2.5 px-4 rounded text-xs font-semibold cursor-not-allowed flex items-center justify-center gap-2">Stok Habis</button>
                            @endif
                            <button type="button" data-book-id="{{ $wish->book_id }}" class="wishlist-remove mt-2 w-full border border-gray-300 text-gray-600 hover:text-red-600 hover:border-red-300 py-2 px-4 rounded text-xs font-medium transition-all flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus dari Wishlist
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
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

document.querySelectorAll('.wishlist-remove').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var card = this.closest('.wishlist-card');
        var bookId = this.dataset.bookId;
        this.style.opacity = '0.5';

        fetch('{{ route('wishlist.toggle') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ book_id: parseInt(bookId) })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (card) card.remove();
            flash('Buku berhasil dihapus dari wishlist.', 'success');

            if (document.querySelectorAll('.wishlist-card').length === 0) {
                document.getElementById('wishlist-grid').outerHTML =
                    '<div class="flex flex-col items-center justify-center py-20 text-center">' +
                        '<div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6 text-gray-300">' +
                            '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>' +
                        '</div>' +
                        '<h2 class="font-serif text-2xl font-bold text-gray-800 mb-4">Wishlist kamu masih kosong</h2>' +
                        '<p class="text-gray-500 mb-10 max-w-md">Mulai jelajahi koleksi kurasi kami dan temukan buku yang menginspirasi Anda selanjutnya.</p>' +
                        '<a href="{{ route('catalog.index') }}" class="inline-block bg-gold text-navy px-10 py-3 font-semibold text-sm rounded-md hover:opacity-90 transition uppercase tracking-widest">Jelajahi Katalog</a>' +
                    '</div>';
            }
        })
        .catch(function() { flash('Gagal menghapus wishlist.', 'error'); })
        .finally(function() { btn.style.opacity = '1'; });
    });
});

document.querySelectorAll('.wl-cart-add').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var bookId = this.dataset.bookId;
        var original = this.textContent;
        this.textContent = '...';
        this.disabled = true;

        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ book_id: parseInt(bookId) })
        })
        .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
        .then(function(result) {
            if (result.status === 422) { alert(result.data.message); }
            else if (result.data.success) {
                btn.textContent = 'Ditambahkan!';
                updateCartBadge(result.data.cart_count);
                setTimeout(function() { btn.textContent = '+ Keranjang'; btn.disabled = false; }, 2000);
                return;
            }
            btn.textContent = original;
            btn.disabled = false;
        })
        .catch(function() { alert('Gagal menambah keranjang.'); btn.textContent = original; btn.disabled = false; });
    });
});
</script>
</x-app-layout>
