<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $admin = $request->attributes->get('admin');

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        foreach ($roles as $role) {
            if ($admin->hasRole($role)) {
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Forbidden. Required role: ' . implode(' or ', $roles),
        ], 403);
    }
}
