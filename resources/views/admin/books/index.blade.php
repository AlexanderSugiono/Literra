<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-serif font-semibold text-xl text-navy leading-tight">
                {{ __('Kelola Buku') }}
            </h2>
            <a href="{{ route('admin.books.create') }}" class="inline-flex items-center px-4 py-2 bg-navy border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90">
                + Tambah Buku
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($books->isEmpty())
                        <p class="text-gray-500">Belum ada buku.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cover</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penulis</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($books as $book)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-12 h-16 object-cover rounded">
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium">{{ $book->title }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $book->author }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $book->category->name }}</td>
                                        <td class="px-4 py-3 text-sm">IDR {{ number_format($book->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $book->stock }}</td>
                                        <td class="px-4 py-3 text-sm space-x-2">
                                            <a href="{{ route('admin.books.edit', $book) }}" class="text-navy hover:opacity-80">Edit</a>
                                            <button type="button" data-name="{{ $book->title }}" data-url="{{ route('admin.books.destroy', $book) }}" class="delete-btn text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-6">
                            {{ $books->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

<script>
document.querySelectorAll('.delete-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var name = this.dataset.name;
        if (!confirm('Yakin ingin menghapus buku "' + name + '"?')) return;

        var row = this.closest('tr');
        var url = this.dataset.url;
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-HTTP-Method-Override': 'DELETE' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                row.remove();
            } else {
                alert(data.message);
            }
        })
        .catch(function() { alert('Terjadi kesalahan saat menghapus.'); });
    });
});
</script>
</x-app-layout>
