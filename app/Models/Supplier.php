<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'npwp',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Supplier $supplier) {
            if (empty($supplier->supplier_code)) {
                $last = static::orderByDesc('id')->value('supplier_code');

                $nextNumber = 1;
                if ($last && preg_match('/^(SUP-)?(\d{4,})$/', $last, $matches)) {
                    $nextNumber = ((int) $matches[2]) + 1;
                }

                $supplier->supplier_code = 'SUP-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}


