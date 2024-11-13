<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

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
}
