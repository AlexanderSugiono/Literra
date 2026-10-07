<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'          => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'discount_type' => ['required', 'in:percent,fixed'],
            'value'         => ['required', 'numeric', 'min:0'],
            'min_purchase'  => ['nullable', 'numeric', 'min:0'],
            'expired_at'    => ['nullable', 'date'],
            'is_active'     => ['boolean'],
        ]);

        if ($validated['discount_type'] === 'percent' && ($validated['value'] < 1 || $validated['value'] > 100)) {
            return response()->json(['message' => 'Diskon persen harus antara 1-100.'], 422);
        }
        if ($validated['discount_type'] === 'fixed' && $validated['value'] <= 0) {
            return response()->json(['message' => 'Diskon nominal harus lebih dari 0.'], 422);
        }

        $validated['code'] = strtoupper($validated['code']);

        $coupon = Coupon::create($validated);

        return response()->json([
            'coupon' => $this->couponData($coupon),
            'message' => 'Kupon "' . $coupon->code . '" berhasil ditambahkan.',
        ]);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code'          => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($coupon->id)],
            'discount_type' => ['required', 'in:percent,fixed'],
            'value'         => ['required', 'numeric', 'min:0'],
            'min_purchase'  => ['nullable', 'numeric', 'min:0'],
            'expired_at'    => ['nullable', 'date'],
            'is_active'     => ['boolean'],
        ]);

        if ($validated['discount_type'] === 'percent' && ($validated['value'] < 1 || $validated['value'] > 100)) {
            return response()->json(['message' => 'Diskon persen harus antara 1-100.'], 422);
        }
        if ($validated['discount_type'] === 'fixed' && $validated['value'] <= 0) {
            return response()->json(['message' => 'Diskon nominal harus lebih dari 0.'], 422);
        }

        $validated['code'] = strtoupper($validated['code']);

        $coupon->update($validated);

        return response()->json([
            'coupon' => $this->couponData($coupon->fresh()),
            'message' => 'Kupon "' . $coupon->code . '" berhasil diperbarui.',
        ]);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return response()->json(['success' => true]);
    }

    private function couponData(Coupon $c): array
    {
        return [
            'id'               => $c->id,
            'code'             => $c->code,
            'discount_type'    => $c->discount_type,
            'value'            => (float) $c->value,
            'min_purchase'     => (float) ($c->min_purchase ?? 0),
            'expired_at'       => $c->expired_at ? $c->expired_at->format('d M Y') : '-',
            'is_active'        => $c->is_active,
            'is_valid'         => $c->isValid(),
            'display_value'    => $c->discount_type === 'percent' ? $c->value . '%' : 'IDR ' . number_format($c->value, 0, ',', '.'),
            'formatted_min'    => $c->min_purchase && $c->min_purchase > 0 ? 'IDR ' . number_format($c->min_purchase, 0, ',', '.') : '-',
        ];
    }
}
