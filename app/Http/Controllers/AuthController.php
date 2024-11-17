<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully.',
            'data' => UserResource::make($user)
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $credentials = $request->validated();

        User::create($credentials);

        return response()->json([
            'success' => true,
            'message' => 'Registered successfully.',
            'data' => null
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect email or password.',
                'data' => null
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'Login successfully.',
            'data' => UserResource::make($user)
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
            'data' => null
        ]);
    }
}
