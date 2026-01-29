<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    // Get user's active cart
    public function getActiveCart(Request $request)
    {
        $userId = auth()->id();
        $cart = Cart::where('user_id', $userId)->with('cartItems')->get()->first();
        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'No active cart found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'cart' => $cart
        ]);
    }
    public function store(Request $request)
    {
        $user_id = auth()->user()->id;
        $cart = Cart::create([
            'user_id' => $user_id
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Cart created successfully',
            'cart' => $cart
        ], 201);
    }
}
