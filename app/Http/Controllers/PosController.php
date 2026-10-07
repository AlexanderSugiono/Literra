<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    private function getCart()
    {
        return session()->get('pos_cart', []);
    }

    private function saveCart($cart)
    {
        session()->put('pos_cart', $cart);
    }

    private function getCoupon()
    {
        return session()->get('pos_coupon');
    }

    private function cartTotals($cart)
    {
        $items = [];
        $total = 0;

        foreach ($cart as $bookId => $qty) {
            $book = Book::find($bookId);
            if (!$book) continue;
            $subtotal = $book->price * $qty;
            $total += $subtotal;
            $items[] = [
                'book_id'           => $book->id,
                'title'             => $book->title,
                'cover_url'         => $book->cover_url,
                'price'             => $book->price,
                'quantity'          => $qty,
                'stock'             => $book->stock,
                'subtotal'          => $subtotal,
                'formatted_price'   => 'IDR ' . number_format($book->price, 0, ',', '.'),
                'formatted_subtotal'=> 'IDR ' . number_format($subtotal, 0, ',', '.'),
            ];
        }

        $couponData = $this->getCoupon();
        $discount = $couponData['discount'] ?? 0;
        $finalTotal = max(0, $total - $discount);

        return [
            'items'             => $items,
            'total'             => $total,
            'formatted_total'   => 'IDR ' . number_format($finalTotal, 0, ',', '.'),
            'count'             => array_sum($cart),
            'discount'          => $discount,
            'formatted_discount'=> $discount > 0 ? 'IDR ' . number_format($discount, 0, ',', '.') : '',
            'formatted_subtotal'=> 'IDR ' . number_format($total, 0, ',', '.'),
        ];
    }

    public function index(Request $request)
    {
        $query = $request->get('q');
        $books = [];

        if ($query) {
            $books = Book::where('title', 'like', "%{$query}%")
                ->orderBy('title')
                ->get();
        }

        $cart = $this->cartTotals($this->getCart());
        $couponData = $this->getCoupon();
        $appliedCoupon = $couponData ? Coupon::find($couponData['id']) : null;

        return view('kasir.pos', compact('books', 'cart', 'query', 'appliedCoupon'));
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'book_id'  => ['required', 'exists:books,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $qty = $validated['quantity'] ?? 1;
        $cart = $this->getCart();
        $currentQty = ($cart[$book->id] ?? 0) + $qty;

        if ($currentQty > $book->stock) {
            return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $book->stock], 422);
        }

        $cart[$book->id] = $currentQty;
        $this->saveCart($cart);

        return response()->json(array_merge(['success' => true], $this->cartTotals($cart)));
    }

    public function updateItem(Request $request)
    {
        $validated = $request->validate([
            'book_id'  => ['required', 'exists:books,id'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $cart = $this->getCart();

        if ($validated['quantity'] === 0) {
            unset($cart[$book->id]);
        } else {
            if ($validated['quantity'] > $book->stock) {
                return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $book->stock], 422);
            }
            $cart[$book->id] = $validated['quantity'];
        }

        $this->saveCart($cart);
        return response()->json(array_merge(['success' => true], $this->cartTotals($cart)));
    }

    public function removeItem(Request $request)
    {
        $validated = $request->validate(['book_id' => ['required', 'exists:books,id']]);
        $cart = $this->getCart();
        unset($cart[$validated['book_id']]);
        $this->saveCart($cart);
        return response()->json(array_merge(['success' => true], $this->cartTotals($cart)));
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate(['code' => ['required', 'string']]);
        $code = strtoupper($validated['code']);

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) return response()->json(['valid' => false, 'message' => 'Kupon tidak ditemukan.'], 422);
        if (!$coupon->is_active) return response()->json(['valid' => false, 'message' => 'Kupon sudah tidak aktif.'], 422);
        if ($coupon->expired_at && Carbon::now()->greaterThan($coupon->expired_at->endOfDay())) {
            return response()->json(['valid' => false, 'message' => 'Kupon sudah kadaluarsa.'], 422);
        }

        $cart = $this->getCart();
        if (empty($cart)) return response()->json(['valid' => false, 'message' => 'Keranjang transaksi kosong.'], 422);

        $total = 0;
        foreach ($cart as $bookId => $qty) {
            $book = Book::find($bookId);
            if ($book) $total += $book->price * $qty;
        }

        if ($coupon->min_purchase && $coupon->min_purchase > 0 && $total < $coupon->min_purchase) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimal belanja Rp ' . number_format($coupon->min_purchase, 0, ',', '.') . ' untuk menggunakan kupon ini.',
            ], 422);
        }

        $discount = $coupon->discount_type === 'percent'
            ? min($total * ($coupon->value / 100), $total)
            : min($coupon->value, $total);

        session()->put('pos_coupon', [
            'id'       => $coupon->id,
            'code'     => $coupon->code,
            'discount' => $discount,
        ]);

        return response()->json(array_merge(
            ['valid' => true, 'code' => $coupon->code],
            $this->cartTotals($cart)
        ));
    }

    public function removeCoupon()
    {
        session()->forget('pos_coupon');
        $cart = $this->cartTotals($this->getCart());
        return response()->json(array_merge(['removed' => true], $cart));
    }

    public function checkout(Request $request)
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang transaksi kosong.'], 422);
        }

        $couponData = $this->getCoupon();
        $discountAmount = 0;
        $couponId = null;

        if ($couponData) {
            $couponRecheck = Coupon::find($couponData['id']);
            if (!$couponRecheck || !$couponRecheck->is_active || ($couponRecheck->expired_at && Carbon::now()->greaterThan($couponRecheck->expired_at->endOfDay()))) {
                session()->forget('pos_coupon');
                return response()->json(['success' => false, 'message' => 'Kupon yang diterapkan sudah tidak valid.'], 422);
            }

            $totalCheck = 0;
            foreach ($cart as $bookId => $qty) {
                $b = Book::find($bookId);
                if ($b) $totalCheck += $b->price * $qty;
            }
            if ($couponRecheck->min_purchase && $couponRecheck->min_purchase > 0 && $totalCheck < $couponRecheck->min_purchase) {
                session()->forget('pos_coupon');
                return response()->json(['success' => false, 'message' => 'Kupon sudah tidak memenuhi minimal belanja.'], 422);
            }

            $discountAmount = $couponData['discount'];
            $couponId = $couponRecheck->id;
        }

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $orderItems = [];

            foreach ($cart as $bookId => $qty) {
                $book = Book::where('id', $bookId)->lockForUpdate()->first();
                if (!$book || $book->stock < $qty) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Stok "' . ($book->title ?? 'buku') . '" tidak mencukupi. Stok tersedia: ' . ($book->stock ?? 0)], 422);
                }
                $totalPrice += $book->price * $qty;
                $orderItems[] = ['book' => $book, 'quantity' => $qty];
            }

            $lastOrder = Order::where('order_code', 'like', 'LIT-' . now()->format('Ymd') . '-%')->orderBy('id', 'desc')->first();
            $seq = $lastOrder ? (int) substr($lastOrder->order_code, -4) + 1 : 1;
            $orderCode = 'LIT-' . now()->format('Ymd') . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'order_code'      => $orderCode,
                'user_id'         => null,
                'kasir_id'        => Auth::id(),
                'payment_method'  => 'pos_walkin',
                'status'          => 'paid',
                'total_price'     => max(0, $totalPrice - $discountAmount),
                'expires_at'      => null,
                'coupon_id'       => $couponId,
                'discount_amount' => $discountAmount,
            ]);

            Payment::create(['order_id' => $order->id, 'method' => 'cash', 'status' => 'paid', 'paid_at' => now()]);

            foreach ($orderItems as $item) {
                $item['book']->decrement('stock', $item['quantity']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id'  => $item['book']->id,
                    'quantity' => $item['quantity'],
                    'price'    => $item['book']->price,
                ]);
            }

            session()->forget('pos_cart');
            session()->forget('pos_coupon');
            DB::commit();

            $response = [
                'success'    => true,
                'order_code' => $orderCode,
                'total'      => max(0, $totalPrice - $discountAmount),
                'formatted_total' => 'IDR ' . number_format(max(0, $totalPrice - $discountAmount), 0, ',', '.'),
                'items'      => $orderItems,
                'paid_at'    => now()->format('d M Y H:i'),
                'kasir'      => Auth::user()->name,
            ];

            if ($couponData) {
                $response['coupon_code'] = $couponData['code'];
                $response['discount'] = $discountAmount;
                $response['formatted_discount'] = 'IDR ' . number_format($discountAmount, 0, ',', '.');
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
