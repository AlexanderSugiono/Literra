<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirOrderController extends Controller
{
    public function index()
    {
        return view('kasir.orders.index');
    }

    public function search(Request $request)
    {
        $q = $request->get('q');

        $orders = Order::with('user')
            ->where('payment_method', '!=', 'pos_walkin')
            ->where(function ($query) use ($q) {
                $query->where('order_code', 'like', "%{$q}%")
                      ->orWhereHas('user', function ($uq) use ($q) {
                          $uq->where('name', 'like', "%{$q}%")
                             ->orWhere('email', 'like', "%{$q}%");
                      });
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($order) {
                return [
                    'id'              => $order->id,
                    'order_code'      => $order->order_code,
                    'customer_name'   => $order->user ? $order->user->name : 'Walk-in',
                    'customer_email'  => $order->user ? $order->user->email : '-',
                    'payment_method'  => $order->payment_method,
                    'status'          => $order->status,
                    'total_price'     => $order->total_price,
                    'formatted_total' => 'IDR ' . number_format($order->total_price, 0, ',', '.'),
                    'created_at'      => $order->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json($orders);
    }

    public function show(Order $order)
    {
        if ($order->payment_method === 'pos_walkin') {
            abort(404);
        }

        $order->load(['user', 'items.book', 'payment']);
        return view('kasir.orders.show', compact('order'));
    }

    public function process(Order $order)
    {
        if ($order->payment_method === 'pos_walkin') {
            return response()->json(['message' => 'Pesanan ini bukan pesanan online.'], 400);
        }

        if ($order->status === 'completed') {
            return response()->json(['message' => 'Pesanan ini sudah selesai sebelumnya.'], 422);
        }

        if (in_array($order->status, ['cancelled', 'expired'])) {
            return response()->json(['message' => 'Pesanan ini sudah ' . ($order->status === 'cancelled' ? 'dibatalkan' : 'kadaluarsa') . '.'], 422);
        }

        if ($order->status === 'pending_payment') {
            if ($order->expires_at && Carbon::now()->greaterThan($order->expires_at)) {
                return response()->json(['message' => 'Pesanan ini sudah kadaluarsa (melewati batas waktu pembayaran).'], 422);
            }
        }

        if (!in_array($order->status, ['paid', 'pending_payment'])) {
            return response()->json(['message' => 'Status pesanan tidak dapat diproses.'], 422);
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status'   => 'completed',
                'kasir_id' => Auth::id(),
            ]);

            if ($order->payment) {
                $order->payment->update([
                    'status'  => 'paid',
                    'paid_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success'        => true,
                'message'        => 'Pesanan ' . $order->order_code . ' berhasil diproses.',
                'order_code'     => $order->order_code,
                'new_status'     => 'completed',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memproses: ' . $e->getMessage()], 500);
        }
    }
}
