<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireOrders extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Expire COD orders that have passed their payment deadline and restore stock';

    public function handle()
    {
        $expiredOrders = Order::where('status', 'pending_payment')
            ->where('expires_at', '<=', Carbon::now())
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No orders to expire.');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($expiredOrders as $order) {
            DB::beginTransaction();
            try {
                foreach ($order->items as $item) {
                    $item->book()->increment('stock', $item->quantity);
                }

                $order->update(['status' => 'expired']);

                if ($order->payment) {
                    $order->payment->update(['status' => 'expired']);
                }

                DB::commit();
                $count++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Failed to expire order ' . $order->order_code . ': ' . $e->getMessage());
            }
        }

        $this->info($count . ' order(s) marked as expired and stock restored.');
        return Command::SUCCESS;
    }
}
