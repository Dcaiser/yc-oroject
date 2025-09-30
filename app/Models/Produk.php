<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
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
        'satuan',
        'supplier_id'
    ];

    public function prices()
{
    return $this->hasMany(Price::class, 'product_id');
}

}
