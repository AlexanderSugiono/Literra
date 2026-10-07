<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\FeaturedBook;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeaturedBookController extends Controller
{
    public function index()
    {
        $featured = FeaturedBook::with('book')->orderBy('display_order')->get();
        $books = Book::orderBy('title')->get();
        $nextOrder = max(1, (FeaturedBook::max('display_order') ?? 0) + 1);
        return view('admin.featured-books.index', compact('featured', 'books', 'nextOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id'       => ['required', 'exists:books,id', Rule::unique('featured_books', 'book_id')],
            'display_order' => ['required', 'integer', 'min:1', Rule::unique('featured_books', 'display_order')],
            'is_active'     => ['boolean'],
        ]);

        $fb = FeaturedBook::create([
            'book_id'       => $validated['book_id'],
            'display_order' => $validated['display_order'],
            'is_active'     => $request->boolean('is_active', true),
        ]);

        $fb->load('book');

        return response()->json([
            'featured' => $this->data($fb),
            'message'  => 'Buku berhasil ditambahkan ke slider.',
        ]);
    }

    public function update(Request $request, FeaturedBook $featuredBook)
    {
        $validated = $request->validate([
            'book_id'       => ['required', 'exists:books,id', Rule::unique('featured_books', 'book_id')->ignore($featuredBook->id)],
            'display_order' => ['required', 'integer', 'min:1', Rule::unique('featured_books', 'display_order')->ignore($featuredBook->id)],
            'is_active'     => ['boolean'],
        ]);

        $featuredBook->update([
            'book_id'       => $validated['book_id'],
            'display_order' => $validated['display_order'],
            'is_active'     => $request->boolean('is_active', true),
        ]);

        $featuredBook->fresh()->load('book');

        return response()->json([
            'featured' => $this->data($featuredBook),
            'message'  => 'Featured book berhasil diperbarui.',
        ]);
    }

    public function destroy(FeaturedBook $featuredBook)
    {
        $featuredBook->delete();
        return response()->json(['success' => true]);
    }

    public function moveUp(FeaturedBook $featuredBook)
    {
        $prev = FeaturedBook::where('display_order', '<', $featuredBook->display_order)
            ->orderBy('display_order', 'desc')->first();
        if (!$prev) return response()->json(['message' => 'Sudah di urutan teratas.'], 422);

        $tmp = $featuredBook->display_order;
        $featuredBook->update(['display_order' => $prev->display_order]);
        $prev->update(['display_order' => $tmp]);

        return response()->json(['success' => true]);
    }

    public function moveDown(FeaturedBook $featuredBook)
    {
        $next = FeaturedBook::where('display_order', '>', $featuredBook->display_order)
            ->orderBy('display_order')->first();
        if (!$next) return response()->json(['message' => 'Sudah di urutan terbawah.'], 422);

        $tmp = $featuredBook->display_order;
        $featuredBook->update(['display_order' => $next->display_order]);
        $next->update(['display_order' => $tmp]);

        return response()->json(['success' => true]);
    }

    private function data(FeaturedBook $fb): array
    {
        return [
            'id'             => $fb->id,
            'book_id'        => $fb->book_id,
            'display_order'  => $fb->display_order,
            'is_active'      => $fb->is_active,
            'book_title'     => $fb->book ? $fb->book->title : '-',
            'book_cover'     => $fb->book ? $fb->book->cover_url : '',
        ];
    }
}
