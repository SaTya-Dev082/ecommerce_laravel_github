<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use ErlandMuchasaj\LaravelFileUploader\FileUploader;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    // Get/index all users
    public function index()
    {
        $users = User::orderBy("id", "DESC")->get();
        return response()->json([
            "status" => true,
            "message" => "All Users Retrieved",
            "users" => $users
        ], 200);
    }

    // Register a new user
    public function register(Request $request)
    {
        // Validation logic here
        $validate = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|confirmed|min:8",
        ]);
        if (!$validate->fails()) {
            if ($request->hasFile('avatar')) {
                $response = FileUploader::store($request->file('avatar'), ["disk" => "public"]);
                $avatarPath = "/storage/" . $response['path'];
            } else {
                $avatarPath = null;
            }
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => $request->password,
                "avatar" => $avatarPath,
            ]);
            $token = $user->createToken($request->name)->plainTextToken;
            return response()->json([
                "status" => true,
                "message" => "User Registered Successfully",
                "token" => $token,
                "Token Type" => "Bearer",
                "user" => $user,
            ], 201);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Validation Error",
                "errors" => $validate->errors()
            ], 422);
        }
    }

    // Login user
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required|string|min:8"
        ]);
        if (!$validate->fails()) {
            $credencials = $request->only("email", "password");
            if (!auth()->attempt($credencials)) {
                return response()->json([
                    "status" => false,
                    "message" => "Invalid Login Credencials"
                ], 401);
            }
            $user = auth()->user();
            $token = $user->createToken($user->name)->plainTextToken;
            return response()->json([
                "status" => true,
                "message" => "User Logged In Successfully",
                "token" => $token,
                "Token Type" => "Bearer",
                "user" => $user,
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Validation Error",
                "error" => $validate->errors()
            ], 422);
        }
    }

    // Logout user

    // Get user profile

    // Update user profile

}
