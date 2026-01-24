<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use ErlandMuchasaj\LaravelFileUploader\FileUploader;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

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
                    "message" => "Invalid Credencials"
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

    // Logout user currently authenticated
    public function logoutCurrent(Request $request)
    {
        $user = $request->user()->currentAccessToken()->delete();
        return response()->json([
            "status" => true,
            "message" => "User Logged Out (Currently) Successfully"
        ], 200);
    }

    // Logout user currently authenticated
    public function logoutAll(Request $request)
    {
        $user = $request->user()->tokens()->delete();
        return response()->json([
            "status" => true,
            "message" => "User Logged Out (All) Successfully"
        ], 200);
    }

    // Get user profile
    public function profile()
    {
        $user = auth()->user();
        return response()->json([
            "status" => true,
            "message" => "User Profile Retrieved Successfully",
            "user" => $user
        ], 200);
    }

    // Update user profile
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                "status" => false,
                "message" => "User unauthenticated"
            ], 404);
        } else {
            $validate = Validator::make($request->all(), [
                "name" => "sometimes|string|max:255",
                "email" => "sometimes|email|unique:users,email," . $user->id,
            ]);
            if (!$validate->fails()) {
                if ($request->hasFile('avatar')) {
                    $oldImage = substr($user->avatar, 9);
                    Storage::disk("public")->delete($oldImage);
                    $response = FileUploader::store($request->file('avatar'), ["disk" => "public"]);
                    $avatar = "/storage/" . $response["path"];
                    $user->avatar = $avatar;
                }
                if ($request->has('name')) {
                    $user->name = $request->name;
                }
                if ($request->has('email')) {
                    $user->email = $request->email;
                }
                if ($request->has('password')) {
                    $user->password = $request->password;
                }
                $user->save();
                return response()->json([
                    "status" => true,
                    "message" => "User Profile Updated Successfully",
                    "user" => $user
                ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Validation Error",
                    "errors" => $validate->errors()
                ], 422);
            }
        }
    }

    
}
