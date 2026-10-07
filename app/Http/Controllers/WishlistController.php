<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            $wishlists = collect();
            return view('wishlist.index', compact('wishlists'));
        }

        $softDeletedIds = Book::onlyTrashed()->pluck('id');
        if ($softDeletedIds->isNotEmpty()) {
            $user->wishlists()->whereIn('book_id', $softDeletedIds)->delete();
        }

        $wishlists = $user->wishlists()->whereHas('book')->with('book.category')->latest()->get();
        return view('wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
        ]);

        $existing = Wishlist::where('user_id', $request->user()->id)
            ->where('book_id', $validated['book_id'])
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['in_wishlist' => false]);
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'book_id' => $validated['book_id'],
        ]);

        return response()->json(['in_wishlist' => true]);
    }
}
