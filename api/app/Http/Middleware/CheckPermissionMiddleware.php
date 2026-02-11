<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'code' => 'auth.errors.unauthenticated',
            ], 401);
        }

        $user = Auth::user();
        $user->loadMissing('role.permissions');

        $userPermissions = $user->role
            ->permissions
            ->pluck('key')
            ->toArray();

        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions, true)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Forbidden. Missing required permission.',
            'code' => 'auth.errors.missingPermission',
        ], 403);
    }
}
