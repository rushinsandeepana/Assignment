<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{

    protected $fillable = [
        'order_code',
        'amount',
        'kitchen_date',
        'kitchen_time',
        'status'
    ];

    public function levelOne()
    {
        return $this->hasMany(LevelOne::class, 'order_id', 'order_code');
    }
}