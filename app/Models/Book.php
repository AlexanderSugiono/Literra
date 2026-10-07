<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'author',
        'publisher',
        'isbn',
        'price',
        'stock',
        'description',
        'cover_image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getCoverUrlAttribute(): string
    {
        if (!$this->cover_image) {
            return 'https://via.placeholder.com/150x200?text=No+Cover';
        }

        $path = $this->cover_image;
        if (str_starts_with($path, 'covers/')) {
            $path = substr($path, 7);
        }

        return asset('covers/' . $path);
    }
}
