<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhereDate('expired_at', '>=', Carbon::today());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('promo.index', compact('coupons'));
    }
}
