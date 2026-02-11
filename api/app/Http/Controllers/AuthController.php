<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('username', $credentials['username'])->first();

        if (! $user) {
            return response()->json([
                'message' => 'Invalid credentials',
                'code' => 'auth.errors.invalidCredentials',
            ], 401);
        }

        if ($user->lockout_enabled) {
            if ($user->locked_until && $user->locked_until->isFuture()) {
                return response()->json([
                    'message' => 'User account is locked',
                    'code' => 'auth.errors.accountLocked',
                    'locked_until' => $user->locked_until,
                ], 423);
            }
        }

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            $user->increment('failed_attempts');

            if ($user->lockout_enabled && $user->failed_attempts >= 5) {
                $user->update([
                    'locked_until' => Carbon::now()->addMinutes(15),
                    'failed_attempts' => 0,
                ]);
            }

            return response()->json([
                'message' => 'Invalid credentials',
                'code' => 'auth.errors.invalidCredentials',
            ], 401);
        }

        $user->update([
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        $user->load([
            'role.permissions',
            'doctor',
            'nurse',
            'worker',
        ]);

        return response()->json([
            'token' => $token,
            'type' => 'Bearer',
            'user' => new UserResource(Auth::guard('api')->user()),
        ]);
    }

    public function profile()
    {
        return response()->json([
            'user' => new UserResource(Auth::guard('api')->user()),
        ]);
    }
}
