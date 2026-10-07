<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-navy leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('admin.books.index') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
                    <div class="flex items-center gap-4">
                        <div class="shrink-0 w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Kelola Buku</h3>
                            <p class="text-sm text-gray-500 mt-1">Tambah, edit, dan hapus data buku</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="block p-6 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-indigo-300 transition">
                    <div class="flex items-center gap-4">
                        <div class="shrink-0 w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Kelola Kategori</h3>
                            <p class="text-sm text-gray-500 mt-1">Tambah, edit, dan hapus kategori buku</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
