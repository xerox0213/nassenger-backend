<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
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
        $message = 'User retrieved successfully.';
        $data = new UserResource($request->user());

        return ApiResponseHelper::jsonSuccess($message, $data);
    }

    public function register(RegisterRequest $request)
    {
        $credentials = $request->validated();

        User::create($credentials);

        $message = 'Registered successfully.';
        $data = null;
        $status = 201;

        return ApiResponseHelper::jsonSuccess($message, $data, $status);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            $message = 'Incorrect email or password.';
            $errors = null;
            $status = 401;

            return ApiResponseHelper::jsonError($message, $errors, $status);
        }

        $message = 'Login successfully.';
        $data = new UserResource(Auth::user());

        return ApiResponseHelper::jsonSuccess($message, $data);
    }
}
