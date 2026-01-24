<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // Get all categories
    public function index()
    {
        $categories = Category::orderBy("id", "ASC")->get();
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
        return response()->json(["status" => true, "products" => $products], 200);
    }
    // Create new category
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|unique:categories,name",
            "description" => "nullable|string",
        ]);
        if ($request->hasFile("image_url")) {
            $imagePath = $request->file("image_url")->store("categories", "public");
            $imageUrl = "/storage/" . $imagePath;
        } else {
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

        $validate = Validator::make($request->all(), [
            "name" => "sometimes|string|unique:categories,name," . $category->id,
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
            ]);

            if ($request->hasFile("image_url")) {

                // delete old image
                if ($category->image_url) {
                    $oldPath = public_path($category->image_url);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                // upload new image
                $imagePath = $request->file("image_url")->store("categories", "public");
                $data["image_url"] = "/storage/" . $imagePath;
            }

            $category->update($data);
            return response()->json([
                "status" => true,
                "message" => "Category Updated Successfully",
                "category" => $category
            ], 200);
        }
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
