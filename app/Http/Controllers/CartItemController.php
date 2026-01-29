<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Cart;

class CartItemController extends Controller
{
    /// Get all cart items(only user's own cart items)
    public function index()
    {
        $user_id = auth()->user()->id;
        $cartItems = CartItem::orderBy('id', 'desc')
            ->whereHas('cart', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->with('product')
            ->get();
        return response()->json([
            'cartItems' => $cartItems
        ], 200);
    }

    // Add item to cart
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = auth()->id();

        // 1️⃣ Get active cart
        $cart = Cart::where('user_id', $userId)->get()->first();

        // 2️⃣ If no active cart → create one
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $userId,
            ]);
        }

        // 3️⃣ Check if product already in cart
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            // ✅ Same cart, increase quantity
            $item->increment('quantity');
        } else {
            // ✅ New product in active cart
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity ?? 1,
                'is_selected' => false
            ]);
        }

        return response()->json($item, 201);
    }
}
