<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
