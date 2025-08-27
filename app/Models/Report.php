<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'total_items',
        'total_transactions',
        'total_income',
        'total_expense',
        'profit_loss',
    ];
}
