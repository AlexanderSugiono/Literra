<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $softDeletedIds = Book::onlyTrashed()->pluck('id');
        if ($softDeletedIds->isNotEmpty()) {
            $removed = $user->carts()->whereIn('book_id', $softDeletedIds)->delete();
            if ($removed) {
                return redirect()->route('cart.index')->with('error', 'Beberapa buku di keranjang sudah tidak tersedia dan telah dihapus. Silakan periksa kembali keranjang Anda.');
            }
        }

        $cartItems = $user->carts()->whereHas('book')->with('book')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kamu kosong, silakan tambahkan buku terlebih dahulu.');
        }

        $stockIssues = [];
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->book->stock) {
                $stockIssues[] = $item;
            }
        }

        $total = $cartItems->sum(function ($item) {
            return $item->book->price * $item->quantity;
        });

        $couponData = session('checkout_coupon');
        $discount = $couponData['discount'] ?? 0;
        $finalTotal = max(0, $total - $discount);
        $appliedCoupon = $couponData ? Coupon::find($couponData['id']) : null;

        return view('checkout.index', compact('cartItems', 'total', 'stockIssues', 'discount', 'finalTotal', 'appliedCoupon'));
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate(['code' => ['required', 'string']]);

        $coupon = Coupon::where('code', strtoupper($validated['code']))->first();

        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Kupon tidak ditemukan.'], 422);
        }

        if (!$coupon->is_active) {
            return response()->json(['valid' => false, 'message' => 'Kupon sudah tidak aktif.'], 422);
        }

        if ($coupon->expired_at && Carbon::now()->greaterThan($coupon->expired_at->endOfDay())) {
            return response()->json(['valid' => false, 'message' => 'Kupon sudah kadaluarsa.'], 422);
        }

        $cartItems = Auth::user()->carts()->with('book')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['valid' => false, 'message' => 'Keranjang kosong.'], 422);
        }

        $total = $cartItems->sum(function ($item) {
            return $item->book->price * $item->quantity;
        });

        if ($coupon->min_purchase && $coupon->min_purchase > 0 && $total < $coupon->min_purchase) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimal belanja Rp ' . number_format($coupon->min_purchase, 0, ',', '.') . ' untuk menggunakan kupon ini.',
            ], 422);
        }

        $discount = $coupon->discount_type === 'percent'
            ? min($total * ($coupon->value / 100), $total)
            : min($coupon->value, $total);

        session()->put('checkout_coupon', [
            'id'       => $coupon->id,
            'code'     => $coupon->code,
            'discount' => $discount,
        ]);

        return response()->json([
            'valid'          => true,
            'code'           => $coupon->code,
            'discount'       => $discount,
            'formatted_discount' => 'IDR ' . number_format($discount, 0, ',', '.'),
            'total'          => $total - $discount,
            'formatted_total' => 'IDR ' . number_format($total - $discount, 0, ',', '.'),
        ]);
    }

    public function removeCoupon()
    {
        session()->forget('checkout_coupon');
        return response()->json(['removed' => true]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:online,cod_instore'],
        ]);

        $user = Auth::user();

        $softDeletedIds = Book::onlyTrashed()->pluck('id');
        if ($softDeletedIds->isNotEmpty()) {
            $user->carts()->whereIn('book_id', $softDeletedIds)->delete();
        }

        $cartItems = $user->carts()->whereHas('book')->with('book')->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang kamu sudah kosong.');
        }

        $couponData = session('checkout_coupon');
        $discountAmount = 0;
        $couponId = null;

        if ($couponData) {
            $coupon = Coupon::find($couponData['id']);
            if (!$coupon || !$coupon->is_active || ($coupon->expired_at && Carbon::now()->greaterThan($coupon->expired_at->endOfDay()))) {
                session()->forget('checkout_coupon');
                return back()->with('error', 'Kupon yang diterapkan sudah tidak valid. Silakan coba lagi.');
            }

            $totalCheck = $cartItems->sum(fn($i) => $i->book->price * $i->quantity);
            if ($coupon->min_purchase && $coupon->min_purchase > 0 && $totalCheck < $coupon->min_purchase) {
                session()->forget('checkout_coupon');
                return back()->with('error', 'Kupon sudah tidak memenuhi minimal belanja. Silakan coba lagi.');
            }

            $discountAmount = $couponData['discount'];
            $couponId = $coupon->id;
        }

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $orderItems = [];

            foreach ($cartItems as $cart) {
                $book = Book::where('id', $cart->book_id)->lockForUpdate()->first();
                if (!$book) {
                    DB::rollBack();
                    return back()->with('error', '"' . ($cart->book->title ?? 'Buku') . '" sudah tidak tersedia lagi dan telah dihapus dari keranjang Anda.');
                }
                if ($book->stock < $cart->quantity) {
                    DB::rollBack();
                    return back()->with('error', 'Stok buku "' . $cart->book->title . '" tidak mencukupi. Stok tersedia: ' . ($book->stock ?? 0));
                }
                $totalPrice += $book->price * $cart->quantity;
                $orderItems[] = ['book' => $book, 'quantity' => $cart->quantity];
            }

            $lastOrder = Order::where('order_code', 'like', 'LIT-' . now()->format('Ymd') . '-%')
                ->orderBy('id', 'desc')->first();
            $seq = $lastOrder ? (int) substr($lastOrder->order_code, -4) + 1 : 1;
            $orderCode = 'LIT-' . now()->format('Ymd') . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            $status = $validated['payment_method'] === 'online' ? 'paid' : 'pending_payment';
            $expiresAt = $validated['payment_method'] === 'cod_instore' ? now()->addHours(24) : null;

            $order = Order::create([
                'order_code'      => $orderCode,
                'user_id'         => $user->id,
                'kasir_id'        => null,
                'payment_method'  => $validated['payment_method'],
                'status'          => $status,
                'total_price'     => max(0, $totalPrice - $discountAmount),
                'expires_at'      => $expiresAt,
                'coupon_id'       => $couponId,
                'discount_amount' => $discountAmount,
            ]);

            foreach ($orderItems as $item) {
                $item['book']->decrement('stock', $item['quantity']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id'  => $item['book']->id,
                    'quantity' => $item['quantity'],
                    'price'    => $item['book']->price,
                ]);
            }

            if ($validated['payment_method'] === 'online') {
                Payment::create(['order_id' => $order->id, 'method' => 'online', 'status' => 'paid', 'paid_at' => now()]);
            } else {
                Payment::create(['order_id' => $order->id, 'method' => 'cod_instore', 'status' => 'pending', 'paid_at' => null]);
            }

            $user->carts()->delete();
            session()->forget('checkout_coupon');
            DB::commit();

            return redirect()->route('checkout.success', $orderCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function success($order_code)
    {
        $order = Order::where('order_code', $order_code)
            ->where('user_id', Auth::id())
            ->with(['payment', 'coupon'])
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}
