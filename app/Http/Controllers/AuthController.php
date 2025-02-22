<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        $credentials = $request->validated();
        $credentials['password'] = bcrypt($credentials['password']);
        User::create($credentials);
        return response()->noContent();
    }
}
