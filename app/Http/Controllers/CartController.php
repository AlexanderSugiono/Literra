<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            $cartItems = collect();
            $total = 0;
            return view('cart.index', compact('cartItems', 'total'));
        }

        $softDeletedIds = Book::onlyTrashed()->pluck('id');
        if ($softDeletedIds->isNotEmpty()) {
            $user->carts()->whereIn('book_id', $softDeletedIds)->delete();
        }

        $cartItems = $user->carts()->whereHas('book')->with('book.category')->get();
        $total = $cartItems->sum(function ($item) {
            return $item->book->price * $item->quantity;
        });
        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'book_id'  => ['required', 'exists:books,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $quantity = $validated['quantity'] ?? 1;

        $existing = Cart::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->first();

        $newQty = $existing ? $existing->quantity + $quantity : $quantity;

        if ($newQty > $book->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $book->stock,
            ], 422);
        }

        if ($existing) {
            $existing->update(['quantity' => $newQty]);
        } else {
            Cart::create([
                'user_id'  => Auth::id(),
                'book_id'  => $book->id,
                'quantity' => $quantity,
            ]);
        }

        $cartCount = Auth::user()->carts()->sum('quantity');

        return response()->json([
            'success'    => true,
            'message'    => $book->title . ' ditambahkan ke keranjang.',
            'cart_count' => $cartCount,
        ]);
    }

    public function updateQuantity(Request $request, Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if (!$cart->book || $cart->book->trashed()) {
            $cart->delete();
            return response()->json(['success' => false, 'message' => 'Buku ini sudah tidak tersedia dan telah dihapus dari keranjang.'], 422);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($validated['quantity'] > $cart->book->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $cart->book->stock,
            ], 422);
        }

        $cart->update(['quantity' => $validated['quantity']]);

        $cartItems = Auth::user()->carts()->whereHas('book')->with('book')->get();
        $total = $cartItems->sum(function ($item) {
            return $item->book->price * $item->quantity;
        });

        return response()->json([
            'success'          => true,
            'subtotal'         => $cart->book->price * $cart->quantity,
            'total'            => $total,
            'cart_count'       => $cartItems->sum('quantity'),
            'formatted_subtotal' => 'IDR ' . number_format($cart->book->price * $cart->quantity, 0, ',', '.'),
            'formatted_total'    => 'IDR ' . number_format($total, 0, ',', '.'),
        ]);
    }

    public function remove(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $cart->delete();

        $cartItems = Auth::user()->carts()->whereHas('book')->with('book')->get();
        $total = $cartItems->sum(function ($item) {
            return $item->book->price * $item->quantity;
        });

        return response()->json([
            'success'           => true,
            'message'           => 'Item dihapus dari keranjang.',
            'total'             => $total,
            'cart_count'        => $cartItems->sum('quantity'),
            'formatted_total'   => 'IDR ' . number_format($total, 0, ',', '.'),
            'is_empty'          => $cartItems->isEmpty(),
        ]);
    }
}
