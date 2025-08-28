<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    // Tentukan field yang boleh diisi
    protected $table = "activities";
    protected $fillable = [
        'user',
        'action',
        'model',
        'record_id',
    ];
}
