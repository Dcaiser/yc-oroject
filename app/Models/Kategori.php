<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description'];

    /**
     * Get products that belong to this category
     */
    public function products()
    {
        return $this->hasMany(Produk::class, 'category_id');
    }
}
