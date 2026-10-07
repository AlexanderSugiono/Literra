<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Kelola Kategori') }}</h2>
            <button type="button" onclick="openModal()" class="inline-flex items-center px-4 py-2 bg-navy border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90">+ Tambah Kategori</button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="flash-message" class="mb-4 hidden"></div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($categories->isEmpty())
                        <p class="text-gray-500">Belum ada kategori.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="category-table-body" class="divide-y divide-gray-200">
                                @foreach ($categories as $category)
                                    <tr data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-slug="{{ $category->slug }}" data-created="{{ $category->created_at->format('d M Y') }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $category->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->slug }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->created_at->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <button type="button" onclick="openModal({{ $category->id }})" class="text-navy hover:opacity-80">Edit</button>
                                            <button type="button" onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')" class="text-red-600 hover:text-red-900">Hapus</button>
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

    <!-- Modal -->
    <div id="category-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Tambah Kategori</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <form id="category-form" onsubmit="return submitForm(event)">
                <input type="hidden" id="category-id" name="category_id" value="">
                <div class="mb-4">
                    <label for="category-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" id="category-name" name="name" required maxlength="255" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-navy focus:ring-navy">
                    <p id="name-error" class="text-sm text-red-600 mt-1 hidden"></p>
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

function flash(msg, type) {
    var el = document.getElementById('flash-message');
    el.textContent = msg;
    el.className = 'mb-4 px-4 py-3 rounded ' + (type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 'bg-green-100 border border-green-400 text-green-700');
    el.classList.remove('hidden');
    setTimeout(function() { el.classList.add('hidden'); }, 3000);
}

function closeModal() {
    document.getElementById('category-modal').classList.add('hidden');
    document.getElementById('name-error').classList.add('hidden');
}

function openModal(id) {
    var nameInput = document.getElementById('category-name');
    var idInput = document.getElementById('category-id');
    var title = document.getElementById('modal-title');
    var btn = document.getElementById('modal-submit-btn');
    var errEl = document.getElementById('name-error');

    errEl.classList.add('hidden');
    nameInput.classList.remove('border-red-500');

    if (id) {
        var row = document.querySelector('#category-table-body tr[data-id="' + id + '"]');
        nameInput.value = row ? row.dataset.name : '';
        idInput.value = id;
        title.textContent = 'Edit Kategori';
        btn.textContent = 'Perbarui';
    } else {
        nameInput.value = '';
        idInput.value = '';
        title.textContent = 'Tambah Kategori';
        btn.textContent = 'Simpan';
    }

    document.getElementById('category-modal').classList.remove('hidden');
    nameInput.focus();
}

function submitForm(e) {
    e.preventDefault();
    var id = document.getElementById('category-id').value;
    var name = document.getElementById('category-name').value.trim();
    var errEl = document.getElementById('name-error');
    var nameInput = document.getElementById('category-name');

    errEl.classList.add('hidden');
    nameInput.classList.remove('border-red-500');

    var url = id ? '/admin/categories/' + id : '/admin/categories';
    var method = id ? 'PUT' : 'POST';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-HTTP-Method-Override': method
        },
        body: new URLSearchParams({ name: name })
    })
    .then(function(r) {
        return r.json().then(function(data) { return { status: r.status, data: data }; });
    })
    .then(function(result) {
        if (result.status === 422) {
            var msg = result.data.errors && result.data.errors.name ? result.data.errors.name[0] : 'Validasi gagal.';
            errEl.textContent = msg;
            nameInput.classList.add('border-red-500');
            errEl.classList.remove('hidden');
            return;
        }

        if (result.status >= 200 && result.status < 300) {
            var cat = result.data.category;
            if (id) {
                var row = document.querySelector('#category-table-body tr[data-id="' + id + '"]');
                if (row) {
                    row.dataset.name = cat.name;
                    row.dataset.slug = cat.slug;
                    row.cells[0].textContent = cat.name;
                    row.cells[1].textContent = cat.slug;
                    row.cells[2].textContent = cat.created_at;
                    row.querySelector('button:last-child').setAttribute('onclick', "deleteCategory(" + cat.id + ", '" + cat.name.replace(/'/g, "\\'") + "')");
                }
            } else {
                var tbody = document.getElementById('category-table-body');
                var tr = document.createElement('tr');
                tr.dataset.id = cat.id;
                tr.dataset.name = cat.name;
                tr.dataset.slug = cat.slug;
                tr.dataset.created = cat.created_at;
                tr.className = 'divide-y divide-gray-200';
                tr.innerHTML = '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">' + cat.name + '</td>' +
                    '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + cat.slug + '</td>' +
                    '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + cat.created_at + '</td>' +
                    '<td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">' +
                        '<button type="button" onclick="openModal(' + cat.id + ')" class="text-navy hover:opacity-80">Edit</button>' +
                        '<button type="button" onclick="deleteCategory(' + cat.id + ", '" + cat.name.replace(/'/g, "\\'") + "')\" class=\"text-red-600 hover:text-red-900\">Hapus</button>" +
                    '</td>';
                tbody.prepend(tr);
            }

            closeModal();
            flash(result.data.message, 'success');
        }
    })
    .catch(function() { flash('Terjadi kesalahan.', 'error'); });

    return false;
}

function deleteCategory(id, name) {
    if (!confirm('Yakin ingin menghapus kategori "' + name + '"?')) return;
    var row = document.querySelector('#category-table-body tr[data-id="' + id + '"]');

    fetch('/admin/categories/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-HTTP-Method-Override': 'DELETE'
        }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            if (row) row.remove();
            flash(data.message, 'success');
        } else {
            flash(data.message, 'error');
        }
    })
    .catch(function() { flash('Terjadi kesalahan saat menghapus.', 'error'); });
}
</script>
</x-app-layout>
