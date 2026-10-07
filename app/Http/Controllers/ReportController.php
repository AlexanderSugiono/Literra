<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $validStatuses = ['paid', 'completed'];

        // --- a. Daily sales last 30 days ---
        $dailySales = Order::whereIn('status', $validStatuses)
            ->where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $dailyLabels = [];
        $dailyValues = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dailyLabels[] = Carbon::now()->subDays($i)->format('d M');
            $dailyValues[] = (float) ($dailySales[$date] ?? 0);
        }

        // --- b. Revenue this month vs last month ---
        $thisMonth = Order::whereIn('status', $validStatuses)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total_price');

        $lastMonth = Order::whereIn('status', $validStatuses)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->sum('total_price');

        $revenueChange = $lastMonth > 0 ? round(($thisMonth - $lastMonth) / $lastMonth * 100, 1) : ($thisMonth > 0 ? 100 : 0);

        // --- c. Top 5 books ---
        $topBooks = OrderItem::selectRaw('book_id, SUM(quantity) as total_qty, SUM(price * quantity) as total_revenue')
            ->whereHas('order', function ($q) use ($validStatuses) {
                $q->whereIn('status', $validStatuses);
            })
            ->groupBy('book_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $book = Book::withTrashed()->find($item->book_id);
                return [
                    'title'    => $book->title ?? 'Unknown',
                    'qty'      => $item->total_qty,
                    'revenue'  => (float) $item->total_revenue,
                ];
            });

        // --- d. Top 5 categories ---
        $topCategories = OrderItem::selectRaw('books.category_id, SUM(order_items.quantity) as total_qty')
            ->join('books', 'order_items.book_id', '=', 'books.id')
            ->whereHas('order', function ($q) use ($validStatuses) {
                $q->whereIn('status', $validStatuses);
            })
            ->groupBy('books.category_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $cat = Category::find($item->category_id);
                return [
                    'name' => $cat->name ?? 'Unknown',
                    'qty'  => $item->total_qty,
                ];
            });

        // --- e. By payment method ---
        $byMethod = Order::whereIn('status', $validStatuses)
            ->selectRaw('payment_method, COUNT(*) as order_count, SUM(total_price) as total_revenue')
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'method'  => $item->payment_method,
                    'label'   => $item->payment_method === 'online' ? 'Online' : ($item->payment_method === 'cod_instore' ? 'COD' : 'POS'),
                    'count'   => $item->order_count,
                    'revenue' => (float) $item->total_revenue,
                ];
            });

        // --- f. Summary numbers ---
        $totalRevenue = Order::whereIn('status', $validStatuses)->sum('total_price');
        $totalOrders = Order::whereIn('status', $validStatuses)->count();
        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        return view('admin.reports.index', compact(
            'dailyLabels', 'dailyValues',
            'thisMonth', 'lastMonth', 'revenueChange',
            'topBooks', 'topCategories', 'byMethod',
            'totalRevenue', 'totalOrders', 'avgOrderValue'
        ));
    }
}
