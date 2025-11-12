<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stockout extends Model
{
    protected $table = 'stock_out';
    protected $fillable = ['product_name','customer_name', 'costumer_type', 'stock_qty', 'prices', 'satuan', 'shipping_cost', 'total_price', 'payment_received', 'note'];
}
