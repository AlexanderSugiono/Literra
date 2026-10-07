<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            $orders = new LengthAwarePaginator([], 0, 10);
            return view('orders.index', compact('orders'));
        }

        $orders = Order::where('user_id', Auth::id())
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak bisa mengakses pesanan milik orang lain.');
        }

        $order->load(['items.book', 'payment', 'coupon']);

        return view('orders.show', compact('order'));
    }
}
