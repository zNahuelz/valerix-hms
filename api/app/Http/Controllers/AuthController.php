<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = auth('api')->user()->load([
            'role.permissions',
            'doctor',
            'nurse',
            'worker'
        ]);

        return response()->json([
            'token' => $token,
            'type' => 'Bearer',
            'user' => new UserResource(auth()->user()),
        ]);
    }

    //TODO: Invalid token returns HTTP 500. (Disable redirect to /login)
    public function profile()
    {
        return response()->json([
            'user' => new UserResource(auth()->user()),
        ]);
    }

    public function test()
    {
        return response()->json('OK');
    }
}
