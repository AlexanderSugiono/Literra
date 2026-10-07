<x-app-layout>
{{-- Skip header slot, full-width hero instead --}}

<style>
.whisper-shadow { box-shadow: 0 12px 24px -10px rgba(0,0,0,0.04); }
.book-radius { border-radius: 4px 12px 12px 4px; }
.hero-glow { background-image: radial-gradient(circle at 70% 30%, rgba(254,214,91,0.08) 0%, transparent 70%); }
html { scroll-behavior: smooth; }
</style>
<script>
window.addEventListener('DOMContentLoaded', function() {
    @if (request()->anyFilled(['search', 'category', 'sort']))
        var el = document.getElementById('semua-buku');
        if (el) el.scrollIntoView({ behavior: 'smooth' });
    @endif
});
</script>

<style>
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>

{{-- HERO SECTION: Featured Books Slider / Welcome --}}
@if ($featured->isNotEmpty())
<section id="hero-wrapper" class="bg-[#0A192F] text-white relative overflow-hidden hero-glow" style="min-height: 520px;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 h-full">
        <div class="grid md:grid-cols-2 gap-12 items-center h-full">
            <div class="relative z-10 flex flex-col" style="min-height: 380px;">
                <span class="inline-block px-3 py-1 bg-gold text-navy text-xs font-semibold rounded-full mb-6 tracking-widest uppercase w-fit">Edisi Kolektor</span>

                <div id="hero-slider-text" class="flex-1 flex flex-col justify-between">
                @foreach ($featured as $i => $fb)
                    @if ($fb->book)
                    <div class="slide-text flex flex-col justify-between {{ $i === 0 ? '' : 'hidden' }}" data-index="{{ $i }}" style="min-height: 300px;">
                        <div>
                            <h1 class="font-serif text-3xl md:text-5xl font-bold mb-4 leading-tight">{{ $fb->book->title }}</h1>
                            <p class="text-white/70 text-sm md:text-base mb-4 max-w-lg line-clamp-3">{{ optional($fb->book)->description ?? 'Koleksi eksklusif dari Literra.' }}</p>
                            <p class="text-gold text-xl md:text-2xl font-bold mb-6">IDR {{ number_format($fb->book->price, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('catalog.show', $fb->book) }}" class="inline-block bg-gold text-navy px-6 py-3 font-semibold text-sm transition-all active:scale-95 rounded-md hover:opacity-90">Lihat Detail</a>
                            <a href="{{ route('catalog.index') }}#semua-buku" class="inline-block border border-white/30 hover:bg-white/10 px-6 py-3 font-semibold text-sm transition-all active:scale-95 rounded-md">Jelajahi Buku</a>
                        </div>
                    </div>
                    @endif
                @endforeach
                </div>
            </div>
            <div class="relative flex justify-center items-center">
                <div class="absolute -inset-10 bg-gold/5 blur-3xl rounded-full"></div>
                <div id="hero-slider-images" class="relative">
                @foreach ($featured as $i => $fb)
                    @if ($fb->book)
                    <div class="slide-img {{ $i === 0 ? '' : 'hidden' }}" data-index="{{ $i }}">
                        <div class="absolute -right-4 -top-4 bg-gold text-navy px-4 py-2 font-bold text-sm rounded shadow-xl z-20">IDR {{ number_format($fb->book->price, 0, ',', '.') }}</div>
                        <img src="{{ $fb->book->cover_url }}" alt="{{ $fb->book->title }}" class="w-[240px] md:w-[320px] h-[360px] md:h-[480px] object-cover book-radius shadow-2xl">
                        <div class="absolute inset-0 border border-white/10 book-radius pointer-events-none"></div>
                    </div>
                    @endif
                @endforeach
                </div>
            </div>
        </div>
        {{-- Slider controls --}}
        <div class="flex justify-center gap-4 mt-8">
            <button id="slide-prev" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">&lsaquo;</button>
            <div class="flex gap-2 items-center">
                @foreach ($featured as $i => $fb)
                <button class="slide-dot w-2.5 h-2.5 rounded-full {{ $i === 0 ? 'bg-gold' : 'bg-white/30' }} transition" data-index="{{ $i }}"></button>
                @endforeach
            </div>
            <button id="slide-next" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">&rsaquo;</button>
        </div>
    </div>
</section>
@else
{{-- Minimal hero when no featured books --}}
<section class="bg-[#0A192F] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
        <span class="inline-block px-3 py-1 bg-gold text-navy text-xs font-semibold rounded-full mb-6 tracking-widest uppercase">Literra</span>
        <h1 class="font-serif text-3xl md:text-5xl font-bold mb-6">Membuka Jendela Dunia Melalui Literasi</h1>
        <p class="text-white/70 text-lg max-w-2xl mx-auto">Eksplorasi koleksi terkurasi kami untuk para pembaca yang menghargai kualitas.</p>
        <a href="{{ route('catalog.index') }}#semua-buku" class="mt-8 inline-block bg-gold text-navy px-8 py-4 font-semibold text-sm rounded-md hover:opacity-90 transition-all">Jelajahi Buku</a>
    </div>
</section>
@endif

{{-- FILTER BAR (sticky below navbar, navy) --}}
<div class="sticky top-[64px] z-40 bg-[#0A192F] border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-1.5">
        <form method="GET" action="{{ route('catalog.index') }}" class="flex flex-col md:flex-row gap-2 items-center">
            <div class="relative flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari penulis, judul, atau kategori..." class="w-full bg-white/10 border border-white/20 focus:border-gold outline-none rounded-lg px-3 py-1.5 pl-8 text-xs text-white placeholder-white/50 transition-all">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div class="flex gap-1.5 w-full md:w-auto">
                <select name="category" class="bg-white/10 border border-white/20 rounded-lg px-2 py-1.5 text-xs text-white focus:border-gold outline-none w-full md:w-auto">
                    <option value="" class="text-gray-800">Semua Kategori</option>
                    @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }} class="text-gray-800">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="sort" class="bg-white/10 border border-white/20 rounded-lg px-2 py-1.5 text-xs text-white focus:border-gold outline-none w-full md:w-auto">
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }} class="text-gray-800">Terbaru</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }} class="text-gray-800">Harga: Termurah</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }} class="text-gray-800">Harga: Termahal</option>
                </select>
                <button type="submit" class="px-3 py-1.5 bg-gold text-navy rounded-lg font-semibold text-xs hover:opacity-90 transition whitespace-nowrap">Cari</button>
                @if (request()->anyFilled(['search', 'category', 'sort']) && request('sort') !== 'newest')
                    <a href="{{ route('catalog.index') }}" class="px-3 py-1.5 border border-white/20 text-white/70 rounded-lg text-xs hover:bg-white/10 transition">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- MAIN COLLECTION --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16" id="semua-buku">

    @if ($books->isEmpty())
        <div class="text-center py-20">
            <p class="text-gray-500 text-lg">Tidak ada buku ditemukan.</p>
            @if (request()->anyFilled(['search', 'category']))
            <a href="{{ route('catalog.index') }}" class="mt-4 inline-block text-navy hover:opacity-80 font-medium">&larr; Reset filter</a>
            @endif
        </div>
    @else

    {{-- Section Header --}}
    <div class="flex justify-between items-end mb-12">
        <div>
            <h2 class="font-serif text-4xl font-bold text-gray-900 mb-2">Semua Buku</h2>
            <div class="h-1 w-20 bg-gold rounded-full"></div>
        </div>
        <p class="text-sm text-gray-500 uppercase tracking-wider">{{ $books->total() }} Judul Tersedia</p>
    </div>

    {{-- Featured / Highlight Book (buku pertama dari hasil query) --}}
    {{-- TODO: Ganti dengan logic "buku pilihan" yang sesungguhnya (misalnya bestseller atau admin pick) --}}
    @php $first = $books->first(); @endphp
    @if ($first)
    <div class="grid lg:grid-cols-12 gap-6 mb-16">
        <div class="lg:col-span-8 bg-white p-8 md:p-12 border border-gray-200 whisper-shadow flex flex-col md:flex-row gap-12 items-center rounded-lg">
            <div class="flex-shrink-0 relative group">
                <img src="{{ $first->cover_url }}" alt="{{ $first->title }}" class="w-[200px] md:w-[240px] h-[300px] md:h-[360px] object-cover book-radius shadow-xl">
                <span class="absolute top-4 left-4 bg-gold text-navy text-[10px] font-bold px-2 py-1 rounded tracking-tighter">PILIHAN KAMI</span>
            </div>
            <div class="flex flex-col justify-center">
                <h3 class="font-serif text-2xl font-bold text-gray-900 mb-2">{{ $first->title }}</h3>
                <p class="text-gray-500 text-sm mb-6 italic">Oleh {{ $first->author }}</p>
                <p class="text-gray-600 text-sm mb-8 leading-relaxed">{{ Str::limit($first->description ?? 'Koleksi pilihan dari Literra.', 180) }}</p>
                <div class="flex items-center gap-6 flex-wrap">
                    <span class="text-2xl font-bold text-navy">IDR {{ number_format($first->price, 0, ',', '.') }}</span>
                    @auth
                        @if (Auth::user()->role === 'customer' && $first->stock > 0)
                            <button type="button" data-book-id="{{ $first->id }}" data-stock="{{ $first->stock }}" class="cart-add-btn bg-gold text-navy hover:opacity-90 px-6 py-3 font-semibold text-sm transition-all active:scale-95 flex items-center gap-2 rounded-md">+ Keranjang</button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="bg-gold text-navy hover:opacity-90 px-6 py-3 font-semibold text-sm transition-all active:scale-95 rounded-md">+ Keranjang</a>
                    @endauth
                    <a href="{{ route('catalog.show', $first) }}" class="text-navy hover:opacity-80 font-medium text-sm">Lihat Detail &rarr;</a>
                </div>
            </div>
        </div>
        <div class="lg:col-span-4 bg-[#0A192F] text-white p-8 flex flex-col justify-between items-start rounded-lg">
            <div>
                <h4 class="font-serif text-xl font-bold text-gold mb-4">Pilihan Editor</h4>
                <p class="text-white/70 text-sm mb-6 italic">"{{ Str::limit($first->description ?? 'Setiap halaman adalah sebuah penemuan baru.', 100) }}"</p>
            </div>
            <a href="{{ route('catalog.show', $first) }}" class="text-sm font-medium flex items-center gap-2 hover:translate-x-1 transition-transform text-gold">Lihat Semua Rekomendasi &rarr;</a>
        </div>
    </div>
    @endif

    {{-- Book Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-16">
        @foreach ($books as $book)
        <div class="group relative">
            <div class="relative overflow-hidden mb-5 aspect-[2/3] bg-gray-100 shadow-sm border border-gray-200 rounded-lg">
                <a href="{{ route('catalog.show', $book) }}">
                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                </a>
                {{-- Wishlist button (customer only) --}}
                @auth
                @if (Auth::user()->role === 'customer')
                @php $inWishlist = $wishlistIds->contains($book->id); @endphp
                <button type="button" data-book-id="{{ $book->id }}" data-in-wishlist="{{ $inWishlist ? 'true' : 'false' }}" class="wishlist-btn absolute top-3 right-3 w-9 h-9 rounded-full bg-white/80 backdrop-blur flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:scale-110">
                    <svg class="wishlist-icon w-5 h-5 {{ $inWishlist ? 'text-red-500' : 'text-gray-400' }}" viewBox="0 0 24 24" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
                @endif
                @else
                <a href="{{ route('login') }}" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/80 backdrop-blur flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </a>
                @endauth
                {{-- Quick view overlay on hover --}}
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent translate-y-full group-hover:translate-y-0 transition-transform">
                    <a href="{{ route('catalog.show', $book) }}" class="block w-full py-2 bg-gold text-navy text-center text-xs font-semibold rounded active:scale-95 transition-all">Quick View</a>
                </div>
                {{-- Stock badge --}}
                @if ($book->stock === 0)
                <span class="absolute top-3 left-3 bg-gray-900/80 text-white text-[10px] font-bold px-2 py-1 rounded">HABIS</span>
                @endif
            </div>
            <h4 class="font-serif text-lg font-semibold text-gray-900 mb-1 group-hover:text-navy transition-colors truncate">{{ $book->title }}</h4>
            <p class="text-sm text-gray-500 mb-3">{{ $book->author }}</p>
            <div class="flex justify-between items-center">
                <span class="font-semibold text-sm text-navy">IDR {{ number_format($book->price, 0, ',', '.') }}</span>
                @auth
                @if (Auth::user()->role === 'customer' && $book->stock > 0)
                    <button type="button" data-book-id="{{ $book->id }}" data-stock="{{ $book->stock }}" class="cart-add-btn bg-gold text-navy hover:opacity-90 text-xs font-semibold px-3 py-1.5 rounded-md flex items-center gap-1 transition-all active:scale-95" title="Tambah ke Keranjang">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Keranjang
                    </button>
                @endif
                @elseif ($book->stock > 0)
                    <a href="{{ route('login') }}" class="bg-gray-200 text-gray-600 hover:bg-gray-300 text-xs font-semibold px-3 py-1.5 rounded-md flex items-center gap-1 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Keranjang
                    </a>
                @endauth
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-16">
        {{ $books->links() }}
    </div>
    @endif
</main>

{{-- Footer --}}
<footer class="bg-[#0A192F] text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="md:col-span-1">
            <h3 class="font-serif text-xl font-bold text-gold mb-4">Literra</h3>
            <p class="text-white/60 text-sm leading-relaxed mb-6">Kurasi intelektual untuk jiwa yang haus akan pengetahuan.</p>
        </div>
        <div><h4 class="text-xs uppercase tracking-widest text-white/50 mb-4">Informasi</h4><div class="flex flex-col gap-2 text-sm text-white/70"><a href="#" class="hover:text-gold transition-colors">Tentang Kami</a><a href="#" class="hover:text-gold transition-colors">Lokasi Toko</a></div></div>
        <div><h4 class="text-xs uppercase tracking-widest text-white/50 mb-4">Layanan</h4><div class="flex flex-col gap-2 text-sm text-white/70"><a href="#" class="hover:text-gold transition-colors">Hubungi Kami</a><a href="#" class="hover:text-gold transition-colors">FAQ</a></div></div>
        <div><h4 class="text-xs uppercase tracking-widest text-white/50 mb-4">Legal</h4><div class="flex flex-col gap-2 text-sm text-white/70"><a href="#" class="hover:text-gold transition-colors">Syarat & Ketentuan</a><a href="#" class="hover:text-gold transition-colors">Kebijakan Privasi</a></div></div>
    </div>
    <div class="border-t border-white/10 py-6 text-center text-sm text-white/40">© {{ date('Y') }} Literra. Curating Intellectual Depth.</div>
</footer>

{{-- SLIDER SCRIPT --}}
@if ($featured->count() > 1)
<script>
(function(){
    var current = 0, total = {{ $featured->count() }}, timer;
    function goTo(i) {
        document.querySelectorAll('.slide-text').forEach(function(e,j){ e.classList.toggle('hidden',j!==i); });
        document.querySelectorAll('.slide-img').forEach(function(e,j){ e.classList.toggle('hidden',j!==i); });
        document.querySelectorAll('.slide-dot').forEach(function(d,j){ d.classList.toggle('bg-gold',j===i); d.classList.toggle('bg-white/30',j!==i); });
        current = i;
    }
    function next() { goTo((current+1)%total); }
    function prev() { goTo((current-1+total)%total); }
    document.getElementById('slide-next').addEventListener('click', function(){ clearInterval(timer); next(); timer=setInterval(next,5000); });
    document.getElementById('slide-prev').addEventListener('click', function(){ clearInterval(timer); prev(); timer=setInterval(next,5000); });
    document.querySelectorAll('.slide-dot').forEach(function(d){ d.addEventListener('click', function(){ clearInterval(timer); goTo(parseInt(this.dataset.index)); timer=setInterval(next,5000); }); });
    timer = setInterval(next, 5000);
})();
</script>
@endif

{{-- AJAX SCRIPTS (preserved) --}}
<script>
function updateCartBadge(count) {
    var badge = document.getElementById('cart-badge');
    if (badge) { badge.textContent = count; badge.classList.toggle('hidden', count == 0); }
}

document.querySelectorAll('.wishlist-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var bookId = this.dataset.bookId, icon = this.querySelector('.wishlist-icon');
        this.style.opacity = '0.5';
        fetch('{{ route('wishlist.toggle') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ book_id: parseInt(bookId) })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var inWish = data.in_wishlist;
            this.dataset.inWishlist = inWish ? 'true' : 'false';
            icon.setAttribute('fill', inWish ? 'currentColor' : 'none');
            icon.classList.toggle('text-red-500', inWish);
            icon.classList.toggle('text-gray-400', !inWish);
        }.bind(this))
        .catch(function() { alert('Gagal mengubah wishlist.'); })
        .finally(function() { this.style.opacity = '1'; }.bind(this));
    });
});

document.querySelectorAll('.cart-add-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.stopPropagation(); e.preventDefault();
        var bookId = this.dataset.bookId, original = this.textContent;
        this.textContent = '...'; this.disabled = true;
        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: JSON.stringify({ book_id: parseInt(bookId) })
        })
        .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
        .then(function(result) {
            if (result.status === 422) { alert(result.data.message); }
            else if (result.data.success) { btn.textContent = 'Ditambahkan!'; updateCartBadge(result.data.cart_count); setTimeout(function() { btn.textContent = original; }, 2000); }
        })
        .catch(function() { alert('Gagal menambah keranjang.'); })
        .finally(function() { btn.disabled = false; });
    });
});


</script>
</x-app-layout>
