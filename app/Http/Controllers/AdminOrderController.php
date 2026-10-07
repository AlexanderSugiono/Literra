<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'kasir', 'items')
            ->orderBy('created_at', 'desc');

        if ($status = $request->status) {
            $query->where('status', $status);
        }
        if ($paymentMethod = $request->payment_method) {
            $query->where('payment_method', $paymentMethod);
        }
        if ($dateFrom = $request->date_from) {
            $query->whereDate('created_at', '>=', Carbon::parse($dateFrom));
        }
        if ($dateTo = $request->date_to) {
            $query->whereDate('created_at', '<=', Carbon::parse($dateTo));
        }
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'kasir', 'items.book', 'payment', 'coupon']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:paid,completed,cancelled'],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $order->status;

        if ($newStatus === $oldStatus) {
            return back()->with('error', 'Status pesanan sudah ' . $oldStatus . '.');
        }

        if ($oldStatus === 'completed') {
            return back()->with('error', 'Pesanan yang sudah selesai tidak bisa diubah statusnya.');
        }

        if ($oldStatus === 'cancelled' || $oldStatus === 'expired') {
            return back()->with('error', 'Pesanan yang sudah dibatalkan/kadaluarsa tidak bisa diubah.');
        }

        DB::beginTransaction();
        try {
            if ($newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    $item->book()->increment('stock', $item->quantity);
                }
                if ($order->payment) {
                    $order->payment->update(['status' => 'cancelled']);
                }
            }

            if ($newStatus === 'completed') {
                if ($order->payment && $order->payment->status !== 'paid') {
                    $order->payment->update(['status' => 'paid', 'paid_at' => Carbon::now()]);
                }
            }

            $order->update(['status' => $newStatus]);

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Status pesanan ' . $order->order_code . ' diubah menjadi ' . $newStatus . '.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}
