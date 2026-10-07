<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Kelola Kupon') }}</h2>
            <button type="button" onclick="openModal()" class="inline-flex items-center px-4 py-2 bg-navy border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90">+ Tambah Kupon</button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="flash-message" class="mb-4 hidden"></div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($coupons->isEmpty())
                        <p class="text-gray-500">Belum ada kupon.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diskon</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min. Belanja</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berlaku</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="coupon-table" class="divide-y divide-gray-200">
                                @foreach ($coupons as $c)
                                    <tr data-id="{{ $c->id }}" data-code="{{ $c->code }}" data-discount-type="{{ $c->discount_type }}" data-value="{{ $c->value }}" data-min-purchase="{{ $c->min_purchase ?? 0 }}" data-expired-at="{{ $c->expired_at ? $c->expired_at->format('Y-m-d') : '' }}" data-is-active="{{ $c->is_active ? 'true' : 'false' }}">
                                        <td class="px-4 py-3 text-sm font-mono font-bold">{{ $c->code }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $c->discount_type === 'percent' ? $c->value . '%' : 'IDR ' . number_format($c->value, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $c->min_purchase && $c->min_purchase > 0 ? 'IDR ' . number_format($c->min_purchase, 0, ',', '.') : '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $c->expired_at ? $c->expired_at->format('d M Y') : '-' }}</td>
                                        <td class="px-4 py-3"><span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full {{ $c->isValid() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $c->isValid() ? 'Aktif' : 'Tidak Aktif' }}</span></td>
                                        <td class="px-4 py-3 text-sm space-x-2">
                                            <button type="button" onclick="openModal({{ $c->id }})" class="text-navy hover:opacity-80">Edit</button>
                                            <button type="button" onclick="deleteCoupon({{ $c->id }}, '{{ addslashes($c->code) }}')" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div id="coupon-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Tambah Kupon</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <form id="coupon-form" onsubmit="return submitForm(event)">
                <input type="hidden" id="coupon-id" value="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Kupon</label>
                        <input type="text" id="c-code" required maxlength="50" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy uppercase">
                        <p id="code-error" class="text-sm text-red-600 mt-1 hidden"></p>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Diskon</label>
                        <select id="c-discount-type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy">
                            <option value="percent">Persen (%)</option>
                            <option value="fixed">Nominal (IDR)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Diskon</label>
                        <input type="number" id="c-value" step="0.01" min="0" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy">
                        <p id="value-error" class="text-sm text-red-600 mt-1 hidden"></p>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min. Belanja (opsional)</label>
                        <input type="number" id="c-min-purchase" step="0.01" min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Hingga (opsional)</label>
                        <input type="date" id="c-expired-at" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy">
                    </div>
                    <div class="mb-3 flex items-center">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="c-is-active" checked class="rounded border-gray-300 text-navy shadow-sm focus:ring-navy">
                            <span class="text-sm text-gray-700">Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Batal</button>
                    <button type="submit" id="modal-submit-btn" class="px-4 py-2 bg-navy text-white rounded-md text-sm font-semibold hover:opacity-90">Simpan</button>
                </div>
            </form>
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

function closeModal() {
    document.getElementById('coupon-modal').classList.add('hidden');
    document.querySelectorAll('#coupon-form .error-message, #code-error, #value-error').forEach(function(e) { e.classList.add('hidden'); });
}

function openModal(id) {
    var title = document.getElementById('modal-title');
    var btn = document.getElementById('modal-submit-btn');
    document.getElementById('coupon-id').value = id || '';

    document.querySelectorAll('#coupon-form .error-message, #code-error, #value-error').forEach(function(e) { e.classList.add('hidden'); });

    if (id) {
        var row = document.querySelector('#coupon-table tr[data-id="' + id + '"]');
        document.getElementById('c-code').value = row.dataset.code;
        document.getElementById('c-discount-type').value = row.dataset.discountType;
        document.getElementById('c-value').value = row.dataset.value;
        document.getElementById('c-min-purchase').value = row.dataset.minPurchase;
        document.getElementById('c-expired-at').value = row.dataset.expiredAt;
        document.getElementById('c-is-active').checked = row.dataset.isActive === 'true';
        title.textContent = 'Edit Kupon';
        btn.textContent = 'Perbarui';
    } else {
        document.getElementById('c-code').value = '';
        document.getElementById('c-discount-type').value = 'percent';
        document.getElementById('c-value').value = '';
        document.getElementById('c-min-purchase').value = '';
        document.getElementById('c-expired-at').value = '';
        document.getElementById('c-is-active').checked = true;
        title.textContent = 'Tambah Kupon';
        btn.textContent = 'Simpan';
    }
    document.getElementById('coupon-modal').classList.remove('hidden');
    document.getElementById('c-code').focus();
}

function submitForm(e) {
    e.preventDefault();
    var id = document.getElementById('coupon-id').value;
    var url = id ? '/admin/coupons/' + id : '/admin/coupons';
    var method = id ? 'PUT' : 'POST';

    var data = new URLSearchParams();
    data.append('code', document.getElementById('c-code').value.toUpperCase());
    data.append('discount_type', document.getElementById('c-discount-type').value);
    data.append('value', document.getElementById('c-value').value);
    data.append('min_purchase', document.getElementById('c-min-purchase').value || '0');
    data.append('expired_at', document.getElementById('c-expired-at').value || '');
    data.append('is_active', document.getElementById('c-is-active').checked ? '1' : '0');

    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-HTTP-Method-Override': method },
        body: data
    })
    .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
    .then(function(result) {
        if (result.status === 422) {
            var msg = result.data.errors?.code ? result.data.errors.code[0] : (result.data.message || 'Validasi gagal.');
            flash(msg, 'error');
            return;
        }
        var cp = result.data.coupon;
        var tbody = document.getElementById('coupon-table');
        var row = tbody ? tbody.querySelector('tr[data-id="' + cp.id + '"]') : null;

        var activeBadge = cp.is_valid ? '<span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Aktif</span>' : '<span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Tidak Aktif</span>';
        var html = '<td class="px-4 py-3 text-sm font-mono font-bold">' + cp.code + '</td>' +
            '<td class="px-4 py-3 text-sm">' + cp.display_value + '</td>' +
            '<td class="px-4 py-3 text-sm">' + cp.formatted_min + '</td>' +
            '<td class="px-4 py-3 text-sm">' + cp.expired_at + '</td>' +
            '<td class="px-4 py-3">' + activeBadge + '</td>' +
            '<td class="px-4 py-3 text-sm space-x-2">' +
                '<button type="button" onclick="openModal(' + cp.id + ')" class="text-navy hover:opacity-80">Edit</button>' +
                '<button type="button" onclick="deleteCoupon(' + cp.id + ", '" + cp.code.replace(/'/g, "\\'") + "')\" class=\"text-red-600 hover:text-red-900\">Hapus</button>" +
            '</td>';

        if (row) {
            row.innerHTML = html;
        } else {
            var tr = document.createElement('tr');
            tr.dataset.id = cp.id;
            tr.dataset.code = cp.code;
            tr.dataset.discountType = cp.discount_type;
            tr.dataset.value = cp.value;
            tr.dataset.minPurchase = cp.min_purchase;
            tr.dataset.expiredAt = cp.expired_at;
            tr.dataset.isActive = cp.is_active ? 'true' : 'false';
            tr.innerHTML = html;
            if (tbody) tbody.prepend(tr);
        }

        closeModal();
        flash(result.data.message, 'success');
    })
    .catch(function() { flash('Terjadi kesalahan.', 'error'); });
    return false;
}

function deleteCoupon(id, code) {
    if (!confirm('Yakin ingin menghapus kupon "' + code + '"?')) return;
    fetch('/admin/coupons/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-HTTP-Method-Override': 'DELETE' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var row = document.querySelector('#coupon-table tr[data-id="' + id + '"]');
        if (row) row.remove();
        flash('Kupon "' + code + '" berhasil dihapus.', 'success');
    })
    .catch(function() { flash('Gagal menghapus.', 'error'); });
}
</script>
</x-app-layout>
