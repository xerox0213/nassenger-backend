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
        return UserResource::make($request->user());
    }

    public function register(RegisterRequest $request)
    {
        $credentials = $request->validated();

        User::create($credentials);

        return response()->json(null, 204);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Incorrect email or password',
            ], 401);
        }

        return new UserResource(Auth::user());
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(null, 204);
    }
}
