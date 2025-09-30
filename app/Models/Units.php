<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    protected $table = 'units';
    protected $fillable = ['id','name', 'conversion_to_base'];

        public function produk()
    {
        return $this->hasMany(Produk::class, 'satuan');
    }

}
