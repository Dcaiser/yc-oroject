<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_transaction_id',
        'product_id',
        'product_name',
        'qty',
        'unit',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'product_id' => 'int',
        'qty' => 'int',
        'price' => 'int',
        'subtotal' => 'int',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PosTransaction::class, 'pos_transaction_id');
    }
}
