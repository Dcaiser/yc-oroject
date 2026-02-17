<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Price extends Model
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

    protected $table = "prices";
        protected $fillable = [
        'product_id',
        'customer_type',
        'price',
        ];
}
