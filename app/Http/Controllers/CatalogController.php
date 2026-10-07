<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\FeaturedBook;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->category) {
            $query->where('category_id', $categoryId);
        }

        $sort = $request->sort;
        if ($sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $books = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $wishlistIds = collect();
        if (auth()->check()) {
            $wishlistIds = auth()->user()->wishlists()->pluck('book_id');
        }

        $featured = FeaturedBook::with('book')
            ->where('is_active', true)
            ->whereHas('book')
            ->orderBy('display_order')
            ->get();

        return view('catalog.index', compact('books', 'categories', 'wishlistIds', 'featured'));
    }

    public function show(Book $book)
    {
        $book->load(['category', 'reviews.user']);

        $userReview = null;
        $inWishlist = false;
        if (auth()->check()) {
            $userReview = $book->reviews->where('user_id', auth()->id())->first();
            $inWishlist = auth()->user()->wishlists()->where('book_id', $book->id)->exists();
        }

        $similarBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->where('stock', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        return view('catalog.show', compact('book', 'userReview', 'inWishlist', 'similarBooks'));
    }
}
