<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    public function index()
    {
        $cart = auth()->user()->cart;
        return response()->json([
            'cart' => $cart
        ], 200);
    }

    public function show(){
        $cart=Cart::orderBy('id','desc')->with('user')->get();
        return response()->json([
            'cart' => $cart
        ], 200);
    }

    public function store(Request $request)
    {
        $user_id = $request->user()->id;

        $cart = Cart::create([
            'user_id' => $user_id,
        ], 201);
        return response()->json([
            'message' => 'Items added to cart successfully',
            'cart' => $cart,
        ]);
    }
}
