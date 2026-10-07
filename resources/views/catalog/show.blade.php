<x-app-layout>
{{-- Skip header slot, use inline heading --}}

<style>
.whisper-shadow { box-shadow: 0 12px 24px -10px rgba(107,114,128,0.15); }
.book-cover-shadow { box-shadow: 15px 15px 30px -10px rgba(13,28,50,0.25); }
.editorial-border { border-bottom: 1px solid rgba(117,119,126,0.1); }
.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label { color: #eab308 !important; }
</style>

<div id="book-alerts" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6"></div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-16 space-y-16">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('catalog.index') }}" class="hover:text-gold transition-colors">Katalog</a>
        <span class="text-xs">/</span>
        <span class="text-gray-700">{{ $book->category->name }}</span>
        <span class="text-xs">/</span>
        <span class="text-gray-900 font-medium">{{ $book->title }}</span>
    </nav>

    {{-- Hero Detail --}}
    <section class="grid grid-cols-1 md:grid-cols-12 gap-12 lg:gap-24 items-stretch">
        <div class="md:col-span-5 lg:col-span-4 flex flex-col justify-center">
            <div class="relative group">
                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full max-w-[400px] aspect-[2/3] object-cover rounded book-cover-shadow transition-transform duration-500 group-hover:scale-[1.02]">
                @if ($book->stock > 0)
                    <div class="absolute top-4 left-4">
                        <span class="inline-block bg-gold text-navy text-xs font-semibold px-3 py-1 rounded-full shadow-sm uppercase tracking-widest">Tersedia</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="md:col-span-7 lg:col-span-8 flex flex-col">
            <div class="flex flex-col justify-center h-full space-y-6 bg-white p-6 md:p-8 rounded-lg shadow-xl">
                <div class="space-y-2">
                    <span class="text-gold font-semibold text-xs uppercase tracking-[0.2em]">{{ $book->category->name }}</span>
                    <h1 class="font-serif text-3xl md:text-5xl font-bold text-gray-900">{{ $book->title }}</h1>
                    <p class="text-gray-500 text-lg italic">oleh <span class="text-gray-800 font-semibold not-italic">{{ $book->author }}</span></p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex text-gold">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= round($book->average_rating))
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endif
                        @endfor
                    </div>
                    <span class="font-semibold text-gray-800">{{ $book->average_rating }}</span>
                    <span class="text-gray-400 text-sm">({{ $book->reviews_count }} ulasan)</span>
                </div>

                <div class="space-y-1">
                    <div class="text-gold font-serif text-3xl font-bold">IDR {{ number_format($book->price, 0, ',', '.') }}</div>
                    <div class="flex items-center gap-2 text-sm font-medium {{ $book->stock > 0 ? 'text-green-700' : 'text-red-600' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $book->stock > 0 ? 'Stok tersedia: ' . $book->stock : 'Stok habis' }}
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    @auth
                        @if (Auth::user()->role === 'customer')
                            @if ($book->stock > 0)
                                <button type="button" id="cart-add-btn" data-book-id="{{ $book->id }}" data-stock="{{ $book->stock }}" class="cart-add-btn bg-gold text-navy hover:opacity-90 transition-all duration-300 px-10 py-4 font-semibold text-sm flex items-center justify-center gap-3 group">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                    TAMBAH KE KERANJANG
                                </button>
                            @else
                                <button type="button" disabled class="px-10 py-4 bg-gray-400 text-white rounded font-semibold text-sm cursor-not-allowed">STOK HABIS</button>
                            @endif
                        @endif
                    @else
                        @if ($book->stock > 0)
                            <a href="{{ route('login') }}" class="bg-gold text-navy hover:opacity-90 transition-all px-10 py-4 font-semibold text-sm inline-flex items-center justify-center gap-3 rounded">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                TAMBAH KE KERANJANG
                            </a>
                        @else
                            <button type="button" disabled class="px-10 py-4 bg-gray-400 text-white rounded font-semibold text-sm cursor-not-allowed">STOK HABIS</button>
                        @endif
                    @endauth
                    @auth
                        @if (Auth::user()->role === 'customer')
                            <button type="button" id="wishlist-btn" data-book-id="{{ $book->id }}" data-in-wishlist="{{ $inWishlist ? 'true' : 'false' }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 transition-all px-8 py-4 font-semibold text-sm flex items-center justify-center gap-3 rounded {{ $inWishlist ? 'bg-red-50 border-red-300 text-red-600' : '' }}">
                                <svg class="w-5 h-5" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                {{ $inWishlist ? 'SUDAH DI WISHLIST' : 'WISHLIST' }}
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 transition-all px-8 py-4 font-semibold text-sm inline-flex items-center justify-center gap-3 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            WISHLIST
                        </a>
                    @endauth
                </div>

                <div class="pt-8 border-t border-gray-200 grid grid-cols-3 gap-6 text-sm">
                    @if ($book->isbn)
                    <div><span class="text-gray-400 text-xs">ISBN</span><div class="font-medium">{{ $book->isbn }}</div></div>
                    @endif
                    @if ($book->publisher)
                    <div><span class="text-gray-400 text-xs">Penerbit</span><div class="font-medium">{{ $book->publisher }}</div></div>
                    @endif
                    <div><span class="text-gray-400 text-xs">Kategori</span><div class="font-medium">{{ $book->category->name }}</div></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Synopsis --}}
    @if ($book->description)
    <section class="max-w-3xl mx-auto space-y-8">
        <h2 class="font-serif text-2xl font-bold text-gray-900 editorial-border pb-4">Tentang Buku Ini</h2>
        <div class="text-gray-600 leading-loose space-y-4">
            <p>{{ $book->description }}</p>
        </div>
    </section>
    @endif

    {{-- Reviews --}}
    <section class="space-y-12">
        <div class="flex flex-col md:flex-row justify-between items-end gap-6 editorial-border pb-4">
            <h2 class="font-serif text-2xl font-bold text-gray-900">Ulasan Pembeli</h2>
            <div class="flex items-center gap-4 bg-gray-100 px-6 py-3 rounded-full">
                <div class="text-xl font-bold text-gold">{{ $book->average_rating }}</div>
                <div class="flex flex-col">
                    <div class="flex text-gold scale-75 origin-left">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= round($book->average_rating) ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span class="text-xs text-gray-400">Berdasarkan {{ $book->reviews_count }} ulasan</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- Review Form --}}
            <div class="lg:col-span-1">
                @auth
                    <div id="review-form-wrapper" class="bg-gray-50 p-6 rounded-lg space-y-4">
                        <h3 class="font-serif text-lg font-bold">{{ $userReview ? 'Ubah Ulasan Anda' : 'Tulis Ulasan' }}</h3>
                        <div id="review-form-alert"></div>
                        <form id="review-form" class="space-y-4">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <input type="hidden" name="review_id" value="{{ $userReview->id ?? '' }}">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                <div class="star-rating flex flex-row-reverse justify-end gap-1">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" id="review-star{{ $i }}" value="{{ $i }}" {{ old('rating', $userReview->rating ?? '') == $i ? 'checked' : '' }} required class="hidden">
                                        <label for="review-star{{ $i }}" class="text-2xl cursor-pointer text-gray-300 hover:text-yellow-500 transition">★</label>
                                    @endfor
                                </div>
                                <p id="rating-error" class="text-sm text-red-600 mt-1 hidden">Rating wajib diisi.</p>
                            </div>

                            <div>
                                <label for="comment-input" class="block text-sm font-medium text-gray-700 mb-1">Pesan Anda</label>
                                <textarea id="comment-input" name="comment" rows="4" class="w-full border-gray-300 rounded-lg resize-none focus:border-navy focus:ring-navy">{{ old('comment', $userReview->comment ?? '') }}</textarea>
                            </div>

                            <button type="submit" class="w-full bg-navy text-white py-3 font-semibold text-sm rounded-lg hover:opacity-90 transition-colors">{{ $userReview ? 'PERBARUI ULASAN' : 'KIRIM ULASAN' }}</button>
                        </form>
                    </div>
                @else
                    <div class="bg-gray-50 p-6 rounded-lg text-center">
                        <p class="text-gray-600"><a href="{{ route('login') }}" class="text-navy hover:opacity-80 font-medium">Login</a> untuk memberikan ulasan.</p>
                    </div>
                @endauth
            </div>

            {{-- Review List --}}
            <div class="lg:col-span-2 space-y-8" id="reviews-list">
                @forelse ($book->reviews as $review)
                    <div class="review-item space-y-3" data-review-id="{{ $review->id }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $review->user->name }}</h4>
                                <div class="flex text-gold text-sm mt-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 italic">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        @if ($review->comment)
                            <p class="text-gray-600 leading-relaxed italic">"{{ $review->comment }}"</p>
                        @endif
                        <div class="flex justify-end">
                            @auth
                                @if ($review->user_id === Auth::id())
                                    <button type="button" data-review-id="{{ $review->id }}" class="delete-review text-xs text-red-500 hover:text-red-700">Hapus</button>
                                @endif
                            @endauth
                        </div>
                        <hr class="border-gray-200">
                    </div>
                @empty
                    <p id="no-reviews-msg" class="text-gray-400 text-sm">Belum ada ulasan untuk buku ini. Jadilah yang pertama!</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Buku Serupa --}}
    @if ($similarBooks->isNotEmpty())
    <section class="space-y-10">
        <h2 class="font-serif text-2xl font-bold text-gray-900">Buku Serupa</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-10">
            @foreach ($similarBooks as $sb)
            <a href="{{ route('catalog.show', $sb) }}" class="group space-y-4">
                <div class="aspect-[2/3] overflow-hidden bg-gray-100 rounded whisper-shadow">
                    <img src="{{ $sb->cover_url }}" alt="{{ $sb->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                </div>
                <div class="space-y-1">
                    <h4 class="font-serif text-base font-bold text-gray-800 group-hover:text-gold transition-colors">{{ $sb->title }}</h4>
                    <p class="text-xs text-gray-400">{{ $sb->author }}</p>
                    <p class="font-semibold text-sm text-gold">IDR {{ number_format($sb->price, 0, ',', '.') }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <div class="text-center">
        <a href="{{ route('catalog.index') }}" class="text-sm text-navy hover:opacity-80 font-medium">&larr; Kembali ke katalog</a>
    </div>
</main>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const bookId = {{ $book->id }};

function updateCartBadge(count) {
    var badge = document.getElementById('cart-badge');
    if (badge) { badge.textContent = count; badge.classList.toggle('hidden', count == 0); }
}

function showAlert(message, type) {
    const container = document.getElementById('book-alerts');
    const div = document.createElement('div');
    div.className = 'mb-4 px-4 py-3 rounded ' + (type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 'bg-green-100 border border-green-400 text-green-700');
    div.textContent = message;
    container.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

function buildReviewHtml(review, starStr) {
    var stars = '';
    for (var i = 1; i <= 5; i++) {
        var filled = i <= review.rating;
        stars += '<svg class="w-4 h-4 ' + (filled ? 'fill-current' : 'text-gray-300 fill-current') + '" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
    }
    var html = '<div class="review-item space-y-3" data-review-id="' + review.id + '">' +
        '<div class="flex justify-between items-start">' +
            '<div><h4 class="font-bold text-gray-800">' + review.user_name + '</h4><div class="flex text-gold text-sm mt-1">' + stars + '</div></div>' +
            '<span class="text-xs text-gray-400 italic">baru saja</span>' +
        '</div>' +
        (review.comment ? '<p class="text-gray-600 leading-relaxed italic">"' + escHtml(review.comment) + '"</p>' : '') +
        (review.can_delete ? '<div class="flex justify-end"><button type="button" data-review-id="' + review.id + '" class="delete-review text-xs text-red-500 hover:text-red-700">Hapus</button></div>' : '') +
        '<hr class="border-gray-200"></div>';
    return html;
}

function escHtml(str) { var d = document.createElement('div'); d.textContent = str; return d.innerHTML; }

function attachDeleteHandlers() {
    document.querySelectorAll('.delete-review').forEach(function(btn) {
        btn.removeEventListener('click', handleDelete);
        btn.addEventListener('click', handleDelete);
    });
}

function handleDelete() {
    if (!confirm('Hapus ulasan ini?')) return;
    var btn = this, reviewId = btn.dataset.reviewId;
    btn.style.opacity = '0.5';
    fetch('{{ url('/reviews') }}/' + reviewId, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-HTTP-Method-Override': 'DELETE' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        showAlert(data.message, 'success');
        var el = document.querySelector('.review-item[data-review-id="' + reviewId + '"]');
        if (el) el.remove();
        updateRatingDisplay(data.average_rating, data.reviews_count);
        if (document.querySelectorAll('.review-item').length === 0) {
            document.getElementById('reviews-list').innerHTML = '<p id="no-reviews-msg" class="text-gray-400 text-sm">Belum ada ulasan untuk buku ini. Jadilah yang pertama!</p>';
        }
        var wrapper = document.getElementById('review-form-wrapper');
        if (wrapper) {
            wrapper.querySelector('h3').textContent = 'Tulis Ulasan';
            wrapper.querySelector('button[type="submit"]').textContent = 'KIRIM ULASAN';
            document.querySelectorAll('#review-form input[name="rating"]').forEach(function(r) { r.checked = false; });
            document.getElementById('comment-input').value = '';
        }
    })
    .catch(function() { showAlert('Gagal menghapus review.', 'error'); })
    .finally(function() { btn.style.opacity = '1'; });
}

function updateRatingDisplay(avg, count) {
    var rd = document.querySelector('.flex.items-center.gap-4 .font-semibold');
    var rc = document.querySelector('.flex.items-center.gap-4 .text-gray-400');
    if (rd) rd.textContent = avg;
    if (rc) rc.textContent = '(' + count + ' ulasan)';
}

// --- Wishlist ---
var wlBtn = document.getElementById('wishlist-btn');
if (wlBtn) {
    wlBtn.addEventListener('click', function() {
        this.style.opacity = '0.5';
        fetch('{{ route('wishlist.toggle') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ book_id: bookId })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var inWish = data.in_wishlist;
            this.dataset.inWishlist = inWish ? 'true' : 'false';
            var svg = this.querySelector('svg');
            svg.setAttribute('fill', inWish ? 'currentColor' : 'none');
            this.textContent = inWish ? 'SUDAH DI WISHLIST' : 'WISHLIST';
            this.classList.toggle('bg-red-50', inWish);
            this.classList.toggle('border-red-300', inWish);
            this.classList.toggle('text-red-600', inWish);
            this.prepend(svg);
        }.bind(this))
        .catch(function() { showAlert('Gagal mengubah wishlist.', 'error'); })
        .finally(function() { this.style.opacity = '1'; }.bind(this));
    });
}

// --- Cart add ---
var cartBtn = document.getElementById('cart-add-btn');
if (cartBtn) {
    cartBtn.addEventListener('click', function() {
        var original = this.textContent;
        this.textContent = '...';
        this.disabled = true;
        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ book_id: bookId })
        })
        .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
        .then(function(result) {
            if (result.status === 422) { alert(result.data.message); }
            else if (result.data.success) {
                cartBtn.textContent = 'Ditambahkan!';
                updateCartBadge(result.data.cart_count);
                setTimeout(function() { cartBtn.textContent = 'TAMBAH KE KERANJANG'; }, 2000);
            }
        })
        .catch(function() { showAlert('Gagal menambah keranjang.', 'error'); })
        .finally(function() { cartBtn.disabled = false; });
    });
}

// --- Review submit ---
document.getElementById('review-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    var ratingInput = this.querySelector('input[name="rating"]:checked');
    if (!ratingInput) { document.getElementById('rating-error').classList.remove('hidden'); btn.disabled = false; return; }
    document.getElementById('rating-error').classList.add('hidden');
    var formData = new FormData(this);
    formData.append('book_id', bookId);
    fetch('{{ route('reviews.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData
    })
    .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
    .then(function(result) {
        if (result.status === 422) {
            var msg = result.data.errors?.rating ? result.data.errors.rating[0] : 'Validasi gagal.';
            document.getElementById('review-form-alert').innerHTML = '<div class="mb-3 px-3 py-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm">' + msg + '</div>';
            return;
        }
        document.getElementById('review-form-alert').innerHTML = '';
        showAlert(result.data.message, 'success');
        var existing = document.querySelector('.review-item[data-review-id="' + result.data.review.id + '"]');
        var starStr = '';
        for (var i = 1; i <= 5; i++) {
            var filled = i <= result.data.review.rating;
            starStr += '<svg class="w-4 h-4 ' + (filled ? 'fill-current' : 'text-gray-300 fill-current') + '" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
        }
        if (existing) { existing.outerHTML = buildReviewHtml(result.data.review, starStr); }
        else {
            document.getElementById('no-reviews-msg')?.remove();
            document.getElementById('reviews-list').insertAdjacentHTML('beforeend', buildReviewHtml(result.data.review, starStr));
        }
        attachDeleteHandlers();
        updateRatingDisplay(result.data.average_rating, result.data.reviews_count);
        document.querySelectorAll('#review-form input[name="rating"]').forEach(function(r) { r.checked = r.value == result.data.review.rating; });
        document.getElementById('comment-input').value = result.data.review.comment || '';
        btn.textContent = 'PERBARUI ULASAN';
        document.querySelector('#review-form-wrapper h3').textContent = 'Ubah Ulasan Anda';
    })
    .catch(function() { showAlert('Gagal menyimpan review.', 'error'); })
    .finally(function() { btn.disabled = false; });
});

attachDeleteHandlers();
</script>
</x-app-layout>
