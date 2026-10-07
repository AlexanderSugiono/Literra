<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'value',
        'min_purchase',
        'expired_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value'        => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'expired_at'   => 'date',
            'is_active'    => 'boolean',
        ];
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expired_at && Carbon::now()->greaterThan($this->expired_at->endOfDay())) return false;
        return true;
    }
}
