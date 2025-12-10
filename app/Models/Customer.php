<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
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
