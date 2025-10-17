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
