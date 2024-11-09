<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $credentials = $request->validated();

        User::create($credentials);

        return response()->json([
            'success' => true,
            'data' => null
        ], 201);
    }
}
