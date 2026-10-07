<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedBook extends Model
{
    protected $fillable = [
        'book_id',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
