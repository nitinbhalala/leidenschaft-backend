<?php

namespace App\Http\Middleware;

use App\Models\UserToken;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token not provided.',
                'data'    => ['redirect' => '/admin/login']
            ], 401);
        }

        $userToken = UserToken::where('token', $token)
            ->with('user')
            ->first();

        if (!$userToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid token.',
                'data'    => ['redirect' => '/admin/login']
            ], 401);
        }

        if (Carbon::parse($userToken->token_expires_at)->isPast()) {
            $userToken->delete();

            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
                'data'    => ['redirect' => '/admin/login']
            ], 401);
        }

        $request->attributes->set('admin', $userToken->user);

        return $next($request);
    }
}
