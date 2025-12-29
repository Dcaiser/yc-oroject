<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PosTransactionItem;

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
        'expense_amount', // TAMBAHKAN INI
        'discount', // TAMBAHKAN INI (jika ada)
        'discount_percent', // TAMBAHKAN INI (jika ada)
        'grand_total',
        'payment_received',
        'payment_method', // TAMBAHKAN INI
        'bank_name', // TAMBAHKAN INI
        'account_number', // TAMBAHKAN INI
        'shipping_address', // TAMBAHKAN INI
        'note',
        'balance_due',
        'change_due',
        'status',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'int',
        'shipping_cost' => 'int',
        'tip' => 'int',
        'expense_amount' => 'int',
        'discount' => 'int',
        'discount_percent' => 'float',
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

    // Accessor untuk format mata uang
    public function getFormattedGrandTotalAttribute()
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }

    public function getFormattedPaymentReceivedAttribute()
    {
        return 'Rp ' . number_format($this->payment_received, 0, ',', '.');
    }

    public function getFormattedBalanceDueAttribute()
    {
        return 'Rp ' . number_format($this->balance_due, 0, ',', '.');
    }

    public function getFormattedChangeDueAttribute()
    {
        return 'Rp ' . number_format($this->change_due, 0, ',', '.');
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
