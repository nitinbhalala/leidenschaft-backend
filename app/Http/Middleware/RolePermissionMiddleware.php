<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = $request->attributes->get('admin');

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        if (!$admin->hasPermissionTo($permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. You do not have the required permission: ' . $permission,
            ], 403);
        }

        return $next($request);
    }
}
