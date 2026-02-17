<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Produk extends Model
{
    protected static function booted(): void
    {
        static::saved(function () {
            self::bumpPosCacheVersion('pos:products:cache_version');
        });

        static::deleted(function () {
            self::bumpPosCacheVersion('pos:products:cache_version');
        });
    }

    private static function bumpPosCacheVersion(string $key): void
    {
        $current = (int) Cache::get($key, 1);
        Cache::forever($key, $current + 1);
    }

    public function category()
    {
        return $this->belongsTo(Kategori::class, 'category_id');
    }

    protected $table = 'products';

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'id',
        'name',
        'price',
        'category_id',
        'sku',
        'stock_quantity',
        'description',
        'image_path',
        'satuan',
        'supplier_id'
    ];

    protected $appends = ['image_url'];

    public function prices()
{
    return $this->hasMany(Price::class, 'product_id');
}
    public function units()
    {
        return $this->belongsTo(Units::class, 'satuan');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }


}
