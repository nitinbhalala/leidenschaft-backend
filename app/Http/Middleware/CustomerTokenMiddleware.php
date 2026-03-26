<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token not provided.',
                'data'    => ['redirect' => '/login']
            ], 401);
        }

        $customer = Customer::where('token', $token)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid token.',
                'data'    => ['redirect' => '/login']
            ], 401);
        }

        if (Carbon::parse($customer->token_expires_at)->isPast()) {
            $customer->update([
                'token'            => null,
                'token_expires_at' => null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
                'data'    => ['redirect' => '/login']
            ], 401);
        }

        $request->attributes->set('customer', $customer);

        return $next($request);
    }
}
