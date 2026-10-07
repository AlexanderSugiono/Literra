<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-navy leading-tight">{{ __('Edit Buku') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <x-input-label for="category_id" :value="__('Kategori')" />
                                <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-navy focus:ring-navy">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="title" :value="__('Judul Buku')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $book->title)" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="author" :value="__('Penulis')" />
                                <x-text-input id="author" class="block mt-1 w-full" type="text" name="author" :value="old('author', $book->author)" required />
                                <x-input-error :messages="$errors->get('author')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="publisher" :value="__('Penerbit')" />
                                <x-text-input id="publisher" class="block mt-1 w-full" type="text" name="publisher" :value="old('publisher', $book->publisher)" />
                                <x-input-error :messages="$errors->get('publisher')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="isbn" :value="__('ISBN')" />
                                <x-text-input id="isbn" class="block mt-1 w-full" type="text" name="isbn" :value="old('isbn', $book->isbn)" />
                                <x-input-error :messages="$errors->get('isbn')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="price" :value="__('Harga (IDR)')" />
                                <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price', $book->price)" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="stock" :value="__('Stok')" />
                                <x-text-input id="stock" class="block mt-1 w-full" type="number" name="stock" :value="old('stock', $book->stock)" required />
                                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Deskripsi')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-navy focus:ring-navy">{{ old('description', $book->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="cover_image" :value="__('Cover Buku')" />
                            @if ($book->cover_image)
                                <div class="mb-2">
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-24 h-32 object-cover rounded border">
                                    <p class="text-xs text-gray-500 mt-1">Cover saat ini. Upload file baru untuk mengganti.</p>
                                </div>
                            @endif
                            <input id="cover_image" type="file" name="cover_image" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gold/10 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <p class="text-xs text-gray-500 mt-1">Maksimal 2MB. Format: JPEG, PNG, JPG, GIF, WebP.</p>
                            <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Perbarui') }}</x-primary-button>
                            <a href="{{ route('admin.books.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
