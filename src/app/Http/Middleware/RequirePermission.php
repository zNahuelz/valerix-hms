<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RequirePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }
        foreach ($permissions as $permission) {
            if ($request->user()->can($permission) || $request->user()->can('sys.admin')) {
                return $next($request);
            }
        }

        Session::flash('error', __('common.unauthorized'));

        return redirect()->route('dashboard');
    }
}
