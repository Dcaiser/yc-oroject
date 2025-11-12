<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'reference',
        'customer_name',
        'customer_type',
        'subtotal',
        'shipping_cost',
        'tip',
        'grand_total',
        'payment_received',
        'balance_due',
        'change_due',
        'status',
        'note',
    ];

    protected $casts = [
        'subtotal' => 'int',
        'shipping_cost' => 'int',
        'tip' => 'int',
        'grand_total' => 'int',
        'payment_received' => 'int',
        'balance_due' => 'int',
        'change_due' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PosTransactionItem::class);
    }
}
