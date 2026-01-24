<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller


{
    // Get all products
    public function index()
    {
        $products = Product::orderBy("id", "DESC")->get();
        return response()->json([
            "status" => true,
            "message" => "All Products Retrieved",
            "products" => $products
        ], 200);
    }

    // Create new product
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "name" => "required|string",
            "description" => "nullable|string",
            "price" => "required|numeric|min:0",
            "quantity" => "required|integer|min:0",
            "discount" => "nullable|numeric|min:0|max:100",
            "category_id" => "required|exists:categories,id",
            "stock" => "nullable|in:in_stock,out_of_stock,incoming",
            "status" => "nullable|in:favorite,unfavorite",
        ]);
        if ($validate->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Validation Error",
                "errors" => $validate->errors()
            ], 422);
        } else {
            if ($request->hasFile("image_url")) {
                $imagePath = $request->file("image_url")->store("products", "public");
                $imageUrl = "/storage/" . $imagePath;
            } else {
                $imageUrl = $request->image_url;
            }
            $product = Product::create([
                "name" => $request->name,
                "description" => $request->description,
                "price" => $request->price,
                "quantity" => $request->quantity,
                "discount" => $request->discount,
                "category_id" => $request->category_id,
                "stock" => $request->stock,
                "status" => $request->status,
                "image_url" => $imageUrl
            ]);
            return response()->json([
                "status" => true,
                "message" => "Product Created Successfully",
                "product" => $product
            ], 201);
        }
    }
    // Select only favorite products
    public function favoriteProducts()
    {
        $favorites = Product::where('status', 'favorite')->orderBy("id", "DESC")->get();
        return response()->json([
            "status" => true,
            "message" => "Favorite Products Retrieved",
            "products" => $favorites
        ], 200);
    }
    // Toggle favorite status
    public function toggleFavorite($product_id)
    {
        $product_id = Product::find($product_id);
        if (!$product_id) {
            return response()->json([
                "status" => false,
                "message" => "Product Not Found"
            ], 404);
        }
        if ($product_id->status === 'favorite') {
            $product_id->status = 'unfavorite';
        } else {
            $product_id->status = 'favorite';
        }
        $product_id->save();
        return response()->json([
            "status" => true,
            "message" => "Product Status Updated Successfully",
            "product" => $product_id
        ], 200);
    }
    // Check Stock product
    public function checkStock()
    {
        $stock = Product::where("stock", "in_stock")->get();
        if (!$stock) {
            return response()->json([
                "status" => false,
                "message" => "Product Not Found"
            ], 404);
        }
        return response()->json([
            "status" => true,
            "message" => "In Stock Products Retrieved",
            "products" => $stock
        ], 200);
    }
    // Search data of products
    public function search($text)
    {
        $product = Product::orderBy('name', 'ASC')->where('name', 'LIKE', "%{$text}%")
            ->orWhere('description', 'LIKE', "%{$text}%")
            ->orWhere('price', 'LIKE', "%{$text}%")
            ->get();

        return response()->json([
            "status" => true,
            "message" => "Search Results Retrieved",
            "products" => $product
        ], 200);
    }
    // Sort product by price
    public function sortByPriceASC()
    {
        $products = Product::orderBy('price', 'ASC')->get();
        return response()->json([
            "status" => true,
            "message" => "Products Sorted by Price",
            "products" => $products
        ], 200);
    }
    // Sort product by price
    public function sortByPriceDESC()
    {
        $products = Product::orderBy('price', 'DESC')->get();
        return response()->json([
            "status" => true,
            "message" => "Products Sorted by Price",
            "products" => $products
        ], 200);
    }
    // Update product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                "status" => false,
                "message" => "Product Not Found"
            ], 404);
        }
        $validate = Validator::make($request->all(), [
            "name" => "sometimes|required|string",
            "description" => "nullable|string",
            "price" => "sometimes|required|numeric|min:0",
            "quantity" => "sometimes|required|integer|min:0",
            "discount" => "nullable|numeric|min:0|max:100",
            "category_id" => "sometimes|required|exists:categories,id",
            "stock" => "nullable|in:in_stock,out_of_stock,incoming",
            "status" => "nullable|in:favorite,unfavorite",
        ]);
        if ($validate->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Validation Error",
                "errors" => $validate->errors()
            ], 422);
        } else {
            $data = $request->only([
                "name",
                "description",
                "price",
                "quantity",
                "discount",
                "category_id",
                "stock",
                "status",
            ]);

            if ($request->hasFile("image_url")) {

                // delete old image
                if ($product->image_url) {
                    $oldPath = public_path($product->image_url);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                // upload new image
                $imagePath = $request->file("image_url")->store("products", "public");
                $data["image_url"] = "/storage/" . $imagePath;
            }

            $product->update($data);


            return response()->json([
                "status" => true,
                "message" => "Product Updated Successfully",
                "product" => $product
            ], 200);
        }
    }
    // Delete product
    public function destroy(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                "status" => false,
                "message" => "Product Not Found"
            ], 404);
        }
        $image = $product->image_url;
        $imageName = basename($image);
        $imagePath = storage_path("app/public/products/" . $imageName);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }
        $product->delete();
        return response()->json([
            "status" => true,
            "message" => "Product Deleted Successfully"
        ], 200);
    }
}
