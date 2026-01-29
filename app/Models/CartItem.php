<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\CheckoutItem;


class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'is_selected'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // public function checkoutItem()
    // {
    //     return $this->hasOne(CheckoutItem::class);
    // }
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }
}
