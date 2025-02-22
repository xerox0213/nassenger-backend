<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        $credentials = $request->validated();
        $credentials['password'] = bcrypt($credentials['password']);
        User::create($credentials);
        return response()->noContent();
    }

    public function login(LoginRequest $request) {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            session()->regenerate();
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Incorrect email or password'
        ], 401);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->noContent();
    }
}
