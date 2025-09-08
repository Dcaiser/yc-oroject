<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stockin extends Model
{
    protected $table = 'stock_in';
    protected $fillable = ['id','product_id','stock_qty','prices','satuan','total_price',];


public function produk()
{
    return $this->belongsTo(Produk::class, 'product_id');
}
}
