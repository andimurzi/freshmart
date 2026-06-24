<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'price', 'stock', 'unit', 'image',
        'is_featured', 'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'price'       => 'integer',
        'stock'       => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /** Scope: hanya produk aktif. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** URL gambar produk (file seed di public/images, upload di public/uploads). */
    public function getImageUrlAttribute(): string
    {
        return asset($this->image ?: 'images/products/default.svg');
    }

    /** Harga berformat Rupiah, mis. "Rp 35.000". */
    public function getPriceLabelAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
