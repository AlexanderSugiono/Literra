<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Cari Pesanan') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <form id="search-form" class="flex gap-2">
                    <input type="text" id="search-input" placeholder="Ketik kode pesanan, nama, atau email customer..." autofocus class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy">
                    <button type="submit" class="px-5 py-2 bg-navy text-white rounded-md hover:opacity-90 font-semibold text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari
                    </button>
                    <button type="button" id="scan-btn" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-500 font-semibold text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9V7a2 2 0 012-2h2m0 12H5a2 2 0 01-2-2v-2m14-8h2a2 2 0 012 2v2m0 8v2a2 2 0 01-2 2h-2"/></svg>
                        Scan QR
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-1">Ketik kode pesanan, nama, atau email customer, lalu tekan Enter atau klik Cari. Atau klik "Scan QR" untuk memindai kode pesanan.</p>
            </div>

            <div id="search-results" class="space-y-3">
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <p class="text-gray-400">Ketik kata kunci dan tekan Cari untuk menampilkan hasil.</p>
                </div>
            </div>
        </div>
    </div>

<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var searchInput = document.getElementById('search-input');
var searchForm = document.getElementById('search-form');
var resultsDiv = document.getElementById('search-results');

function escHtml(str) {
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

searchForm.addEventListener('submit', function(e) {
    e.preventDefault();
    var q = searchInput.value.trim();
    if (q.length < 2) {
        resultsDiv.innerHTML = '<div class="bg-white rounded-lg shadow-sm p-12 text-center"><p class="text-gray-500">Masukkan minimal 2 karakter untuk mencari.</p></div>';
        return;
    }
    doSearch(q);
});

function doSearch(q) {
    resultsDiv.innerHTML = '<div class="bg-white rounded-lg shadow-sm p-12 text-center"><p class="text-gray-500">Mencari...</p></div>';

    fetch('{{ route('kasir.orders.search') }}?q=' + encodeURIComponent(q), {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.length === 0) {
            resultsDiv.innerHTML = '<div class="bg-white rounded-lg shadow-sm p-12 text-center"><p class="text-gray-500">Tidak ada pesanan ditemukan.</p></div>';
            return;
        }
        var html = '';
        data.forEach(function(order) {
            var statusBadge = {
                'pending_payment': '<span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu Bayar</span>',
                'paid': '<span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Lunas</span>',
                'completed': '<span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>',
                'cancelled': '<span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Dibatalkan</span>',
                'expired': '<span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Kadaluarsa</span>',
            };
            html += '<a href="{{ url('/kasir/orders') }}/' + order.id + '" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:border-indigo-300 transition">' +
                '<div class="flex items-center justify-between">' +
                    '<div>' +
                        '<p class="font-mono font-bold text-navy">' + escHtml(order.order_code) + '</p>' +
                        '<p class="text-sm text-gray-600 mt-0.5">' + escHtml(order.customer_name) + '</p>' +
                        '<p class="text-xs text-gray-400">' + escHtml(order.customer_email) + '</p>' +
                    '</div>' +
                    '<div class="text-right">' +
                        '<p class="text-sm font-semibold">' + order.formatted_total + '</p>' +
                        '<p class="mt-1">' + (statusBadge[order.status] || order.status) + '</p>' +
                    '</div>' +
                '</div>' +
                '<div class="flex gap-4 mt-2 text-xs text-gray-500">' +
                    '<span>Pembayaran: ' + (order.payment_method === 'online' ? 'Online' : order.payment_method === 'cod_instore' ? 'COD' : order.payment_method) + '</span>' +
                    '<span>' + order.created_at + '</span>' +
                '</div>' +
            '</a>';
        });
        resultsDiv.innerHTML = html;
    })
    .catch(function() {
        resultsDiv.innerHTML = '<div class="bg-white rounded-lg shadow-sm p-12 text-center"><p class="text-red-500">Terjadi kesalahan.</p></div>';
    });
}
</script>

{{-- QR Scan Modal --}}
<div id="scan-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/70">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Pindai QR Code</h3>
            <button type="button" id="scan-close" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div id="scan-error" class="hidden mb-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm"></div>
        <div class="aspect-square bg-black rounded-lg overflow-hidden relative mb-3">
            <video id="scan-video" autoplay playsinline class="absolute inset-0 w-full h-full object-cover"></video>
            <canvas id="scan-canvas" class="hidden"></canvas>
            <div id="scan-overlay" class="absolute inset-0 flex items-center justify-center">
                <div class="w-48 h-48 border-2 border-white/60 rounded-lg"></div>
            </div>
        </div>
        <p class="text-sm text-gray-500 text-center">Arahkan QR Code ke tengah kotak</p>
        <div class="flex justify-center mt-4">
            <button type="button" id="scan-cancel" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold text-sm hover:bg-gray-300">Batal</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
(function() {
    var scanModal = document.getElementById('scan-modal');
    var scanVideo = document.getElementById('scan-video');
    var scanCanvas = document.getElementById('scan-canvas');
    var scanError = document.getElementById('scan-error');
    var scanStream = null;
    var scanInterval = null;

    function stopScan() {
        if (scanInterval) { clearInterval(scanInterval); scanInterval = null; }
        if (scanStream) { scanStream.getTracks().forEach(function(t) { t.stop(); }); scanStream = null; }
        scanVideo.srcObject = null;
    }

    document.getElementById('scan-btn').addEventListener('click', function() {
        scanError.classList.add('hidden');
        scanModal.classList.remove('hidden');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function(stream) {
                scanStream = stream;
                scanVideo.srcObject = stream;
                scanVideo.play();

                scanCanvas.width = scanVideo.videoWidth || 640;
                scanCanvas.height = scanVideo.videoHeight || 480;

                scanInterval = setInterval(function() {
                    var ctx = scanCanvas.getContext('2d');
                    scanCanvas.width = scanVideo.videoWidth || 640;
                    scanCanvas.height = scanVideo.videoHeight || 480;
                    ctx.drawImage(scanVideo, 0, 0, scanCanvas.width, scanCanvas.height);
                    var imageData = ctx.getImageData(0, 0, scanCanvas.width, scanCanvas.height);
                    var code = jsQR(imageData.data, imageData.width, imageData.height);
                    if (code && code.data && code.data.length > 0) {
                        stopScan();
                        scanModal.classList.add('hidden');
                        searchInput.value = code.data;
                        doSearch(code.data);
                    }
                }, 500);
            })
            .catch(function(err) {
                scanError.textContent = 'Tidak dapat mengakses kamera. ' + (err.name === 'NotAllowedError' ? 'Izin kamera ditolak.' : err.message) + ' Silakan ketik manual sebagai alternatif.';
                scanError.classList.remove('hidden');
            });
        } else {
            scanError.textContent = 'Browser tidak mendukung akses kamera. Silakan ketik manual.';
            scanError.classList.remove('hidden');
        }
    });

    function closeScan() {
        stopScan();
        scanModal.classList.add('hidden');
    }

    document.getElementById('scan-close').addEventListener('click', closeScan);
    document.getElementById('scan-cancel').addEventListener('click', closeScan);
    scanModal.addEventListener('click', function(e) { if (e.target === scanModal) closeScan(); });
})();
</script>
</x-app-layout>
