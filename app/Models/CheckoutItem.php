<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CartItem;

class CheckoutItem extends Model
{
    protected $fillable = ['cart_item_id'];

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }
}
