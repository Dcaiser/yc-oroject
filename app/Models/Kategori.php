<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Kategori extends Model
{
    protected static function booted(): void
    {
        static::saved(function () {
            self::bumpPosCacheVersion('pos:categories:cache_version');
            self::bumpPosCacheVersion('pos:products:cache_version');
        });

        static::deleted(function () {
            self::bumpPosCacheVersion('pos:categories:cache_version');
            self::bumpPosCacheVersion('pos:products:cache_version');
        });
    }

    private static function bumpPosCacheVersion(string $key): void
    {
        $current = (int) Cache::get($key, 1);
        Cache::forever($key, $current + 1);
    }

    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description'];

    /**
     * Get products that belong to this category
     */
    public function products()
    {
        return $this->hasMany(Produk::class, 'category_id');
    }
}
