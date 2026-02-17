<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Customer extends Model
{
    protected static function booted(): void
    {
        static::saved(function () {
            self::bumpPosCacheVersion('pos:customers:cache_version');
        });

        static::deleted(function () {
            self::bumpPosCacheVersion('pos:customers:cache_version');
        });
    }

    private static function bumpPosCacheVersion(string $key): void
    {
        $current = (int) Cache::get($key, 1);
        Cache::forever($key, $current + 1);
    }

    protected $table = 'customers';
    protected $fillable = ['customer_name', 'phone', 'address', 'shipping_cost'];

    protected $casts = [
        'shipping_cost' => 'int',
    ];

    /**
     * Get all transactions for the customer
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PosTransaction::class, 'customer_id');
    }
}
