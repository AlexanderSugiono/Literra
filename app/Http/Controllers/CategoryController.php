<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return response()->json([
            'category' => [
                'id'         => $category->id,
                'name'       => $category->name,
                'slug'       => $category->slug,
                'created_at' => $category->created_at->format('d M Y'),
            ],
            'message' => 'Kategori "' . $category->name . '" berhasil ditambahkan.',
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return response()->json([
            'category' => [
                'id'         => $category->id,
                'name'       => $category->name,
                'slug'       => $category->slug,
                'created_at' => $category->created_at->format('d M Y'),
            ],
            'message' => 'Kategori "' . $category->name . '" berhasil diperbarui.',
        ]);
    }

    public function destroy(Category $category)
    {
        if (class_exists(Book::class) && $category->books()->withTrashed()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori "' . $category->name . '" tidak bisa dihapus karena masih terhubung dengan buku (termasuk yang sudah dihapus). Kategori tidak bisa dihapus untuk menjaga riwayat data buku yang pernah ada.',
            ]);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori "' . $category->name . '" berhasil dihapus.',
        ]);
    }
}
