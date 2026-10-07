<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Buku Unggulan') }}</h2>
            <button type="button" onclick="openModal()" class="inline-flex items-center px-4 py-2 bg-navy border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90">+ Tambah Buku</button>
        </div>
    </x-slot>

    <div class="py-12"><div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div id="flash-message" class="mb-4 hidden"></div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6">
            @if ($featured->isEmpty())
                <p class="text-gray-500">Belum ada buku unggulan.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead><tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Urutan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cover</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktif</th>
                        <th class="px-4 py-3"></th>
                    </tr></thead>
                    <tbody id="fb-table" class="divide-y divide-gray-200">
                        @foreach ($featured as $fb)
                            <tr data-id="{{ $fb->id }}" data-book-id="{{ $fb->book_id }}" data-display-order="{{ $fb->display_order }}" data-is-active="{{ $fb->is_active ? 'true' : 'false' }}">
                                <td class="px-4 py-3 text-sm">{{ $fb->display_order }}</td>
                                <td class="px-4 py-3"><img src="{{ $fb->book->cover_url }}" alt="" class="w-10 h-14 object-cover rounded"></td>
                                <td class="px-4 py-3 text-sm font-medium">{{ $fb->book->title }}</td>
                                <td class="px-4 py-3"><span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full {{ $fb->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $fb->is_active ? 'Aktif' : 'Tidak' }}</span></td>
                                <td class="px-4 py-3 text-sm space-x-1">
                                    <button onclick="moveUp({{ $fb->id }})" class="text-gray-500 hover:text-navy" title="Naik">&uarr;</button>
                                    <button onclick="moveDown({{ $fb->id }})" class="text-gray-500 hover:text-navy" title="Turun">&darr;</button>
                                    <button onclick="openModal({{ $fb->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                    <button onclick="deleteFb({{ $fb->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div></div>
    </div></div>

    <div id="fb-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Tambah Buku Unggulan</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <form id="fb-form" onsubmit="return submitForm(event)">
                <input type="hidden" id="fb-id" value="">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buku</label>
                    <select id="fb-book-id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy">
                        <option value="">-- Pilih Buku --</option>
                        @foreach ($books as $b)
                            <option value="{{ $b->id }}">{{ $b->title }} ({{ $b->author }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                    <input type="number" id="fb-order" value="0" min="1" class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy">
                </div>
                <div class="mb-4 flex items-center">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="fb-active" checked class="rounded border-gray-300 text-navy shadow-sm focus:ring-navy">
                        <span class="text-sm text-gray-700">Aktif</span>
                    </label>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Batal</button>
                    <button type="submit" id="modal-submit-btn" class="px-4 py-2 bg-navy text-white rounded-md text-sm font-semibold hover:opacity-90">Simpan</button>
                </div>
            </form>
        </div>
    </div>

<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function flash(m, t) { var e=document.getElementById('flash-message'); e.textContent=m; e.className='mb-4 px-4 py-3 rounded '+(t==='error'?'bg-red-100 border border-red-400 text-red-700':'bg-green-100 border border-green-400 text-green-700'); e.classList.remove('hidden'); setTimeout(function(){e.classList.add('hidden')},3000); }
function closeModal() { document.getElementById('fb-modal').classList.add('hidden'); }

function openModal(id) {
    document.getElementById('fb-id').value = id||'';
    document.getElementById('modal-title').textContent = id?'Edit Buku Unggulan':'Tambah Buku Unggulan';
    document.getElementById('modal-submit-btn').textContent = id?'Perbarui':'Simpan';
    if (id) {
        var r = document.querySelector('#fb-table tr[data-id="'+id+'"]');
        document.getElementById('fb-book-id').value = r.dataset.bookId;
        document.getElementById('fb-order').value = r.dataset.displayOrder;
        document.getElementById('fb-active').checked = r.dataset.isActive==='true';
    } else {
        document.getElementById('fb-book-id').value = '';
        document.getElementById('fb-order').value = {{ $nextOrder }};
        document.getElementById('fb-active').checked = true;
    }
    document.getElementById('fb-modal').classList.remove('hidden');
}

function submitForm(e) {
    e.preventDefault();
    var id=document.getElementById('fb-id').value, url=id?'/admin/featured-books/'+id:'/admin/featured-books';
    var data = new URLSearchParams();
    data.append('book_id', document.getElementById('fb-book-id').value);
    data.append('display_order', document.getElementById('fb-order').value);
    data.append('is_active', document.getElementById('fb-active').checked?'1':'0');
    fetch(url,{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:data})
    .then(function(r){return r.json().then(function(d){return{status:r.status,data:d}})})
    .then(function(r){
        if(r.status===422){
            var msg=r.data.errors?.display_order?r.data.errors.display_order[0]:(r.data.message||'Validasi gagal.');
            flash(msg,'error');return;
        }
        var d=r.data.featured;
        var tbody=document.getElementById('fb-table');
        var row=tbody?tbody.querySelector('tr[data-id="'+d.id+'"]'):null;
        if(row){row.outerHTML=buildRow(d);}
        else{tbody.insertAdjacentHTML('afterbegin',buildRow(d));}
        closeModal();flash(r.data.message,'success');
    }).catch(function(){flash('Terjadi kesalahan.','error');});
    return false;
}

function buildRow(d) {
    var active=d.is_active?'<span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Aktif</span>':'<span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Tidak</span>';
    return '<tr data-id="'+d.id+'" data-book-id="'+d.book_id+'" data-display-order="'+d.display_order+'" data-is-active="'+(d.is_active?'true':'false')+'">'+
        '<td class="px-4 py-3 text-sm">'+d.display_order+'</td>'+
        '<td class="px-4 py-3"><img src="'+d.book_cover+'" alt="" class="w-10 h-14 object-cover rounded"></td>'+
        '<td class="px-4 py-3 text-sm font-medium">'+d.book_title+'</td>'+
        '<td class="px-4 py-3">'+active+'</td>'+
        '<td class="px-4 py-3 text-sm space-x-1">'+
            '<button onclick="moveUp('+d.id+')" class="text-gray-500 hover:text-navy" title="Naik">&uarr;</button>'+
            '<button onclick="moveDown('+d.id+')" class="text-gray-500 hover:text-navy" title="Turun">&darr;</button>'+
            '<button onclick="openModal('+d.id+')" class="text-indigo-600 hover:text-indigo-900">Edit</button>'+
            '<button onclick="deleteFb('+d.id+')" class="text-red-600 hover:text-red-900">Hapus</button>'+
        '</td></tr>';
}

function deleteFb(id) { if(!confirm('Hapus buku ini dari slider?'))return;
    fetch('/admin/featured-books/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'}})
    .then(function(r){return r.json()}).then(function(d){var e=document.querySelector('#fb-table tr[data-id="'+id+'"]');if(e)e.remove();flash('Dihapus.','success');})
    .catch(function(){flash('Gagal.','error');});
}

function moveUp(id) {
    fetch('/admin/featured-books/'+id+'/move-up',{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'}})
    .then(function(r){return r.json().then(function(d){return{status:r.status,data:d}})})
    .then(function(r){if(r.status===422){alert(r.data.message);return;}location.reload();})
    .catch(function(){flash('Gagal.','error');});
}
function moveDown(id) {
    fetch('/admin/featured-books/'+id+'/move-down',{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'}})
    .then(function(r){return r.json().then(function(d){return{status:r.status,data:d}})})
    .then(function(r){if(r.status===422){alert(r.data.message);return;}location.reload();})
    .catch(function(){flash('Gagal.','error');});
}
</script>
</x-app-layout>
