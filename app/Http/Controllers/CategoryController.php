<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Get all categories
    public function index()
    {
        $categories = Category::orderBy("id", "DESC")->get();
        return response()->json([
            "status" => true,
            "message" => "All Categories Retrieved",
            "categories" => $categories
        ], 200);
    }

    // Get Product by CategoryId
    public function getProductsByCategoryId($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "status" => false,
                "message" => "Category Not Found"
            ], 404);
        }
        $products = $category->products()->orderBy("id", "DESC")->get();
        return response()->json([
            "status" => true,
            "message" => "Products Retrieved by Category",
            "products" => $products
        ], 200);
    }
    // Create new category
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|unique:categories,name",
            "description" => "nullable|string",
        ]);
        if($request->hasFile("image_url")){
            $imagePath = $request->file("image_url")->store("categories","public");
            $imageUrl = "/storage/".$imagePath;
        }else{
            $imageUrl = null;
        }

        $category = Category::create([
            "name" => $request->name,
            "description" => $request->description,
            "image_url" => $imageUrl,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Category Created Successfully",
            "category" => $category
        ], 201);
    }

    // Update category
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "status" => false,
                "message" => "Category Not Found"
            ], 404);
        }

        $request->validate([
            "name" => "sometimes|required|string|unique:categories,name," . $id,
            "description" => "nullable|string",
        ]);

        $category->update($request->only(["name", "description"]));

        return response()->json([
            "status" => true,
            "message" => "Category Updated Successfully",
            "category" => $category
        ], 200);
    }
    // Delete category
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "status" => false,


                "message" => "Category Not Found"
            ], 404);
        }
        $category->delete();
        return response()->json([
            "status" => true,
            "message" => "Category Deleted Successfully"
        ], 200);;
    }
}
