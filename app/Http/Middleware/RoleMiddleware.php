<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Kalau route: role:admin,kasir
        // $roles = ['admin', 'kasir']
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'status' => false,
                'message' => 'Forbidden. Wrong role.'
            ], 403);
        }

        return $next($request);
    }
}
