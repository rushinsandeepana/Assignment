<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelOne extends Model
{
    protected $fillable = [
        'order_id',
        'concession_id',
        'quantity',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_code');
    }
}