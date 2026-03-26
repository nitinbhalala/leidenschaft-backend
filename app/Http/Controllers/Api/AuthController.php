<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Str::random(64);

        $customer->update([
            'token'            => $token,
            'token_expires_at' => Carbon::now()->addDays(7),
        ]);

        return $this->success([
            'user' => [
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'avatar' => $customer->avatar,
            ],
            'token'            => $token,
            'token_expires_at' => Carbon::now()->addDays(7)->toDateTimeString(),
            'redirect'         => '/'
        ], 'Registration successful! Welcome, ' . $customer->name . '.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return $this->error(
                'These credentials do not match our records.',
                422,
                [
                    'email' => ['Invalid email or password.']
                ]
            );
        }

        $token = Str::random(64);

        $customer->update([
            'token'            => $token,
            'token_expires_at' => Carbon::now()->addDays(7),
        ]);

        return $this->success([
            'user' => [
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'avatar' => $customer->avatar,
            ],
            'token'            => $token,
            'token_expires_at' => Carbon::now()->addDays(7)->toDateTimeString(),
            'redirect'         => '/'
        ], 'Login successful! Welcome back, ' . $customer->name . '.');
    }

    public function logout(Request $request): JsonResponse
    {
        $customer = $request->attributes->get('customer');

        if ($customer) {
            $customer->update([
                'token'            => null,
                'token_expires_at' => null,
            ]);
        }

        return $this->success([
            'redirect' => '/'
        ], 'You have been logged out successfully.');
    }

    public function verifyToken(Request $request): JsonResponse
    {
        $customer = $request->attributes->get('customer');

        if (!$customer) {
            return $this->error('Unauthorized. Please login again.', 401);
        }

        return $this->success([
            'user' => [
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'avatar' => $customer->avatar,
            ],
            'token'            => $customer->token,
            'token_expires_at' => $customer->token_expires_at,
        ], 'Token is valid.');
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $customer = $request->attributes->get('customer');

        if (!$customer) {
            return $this->error('Unauthorized. Please login again.', 401);
        }

        $newToken = Str::random(64);

        $customer->update([
            'token'            => $newToken,
            'token_expires_at' => Carbon::now()->addDays(7),
        ]);

        return $this->success([
            'token'            => $newToken,
            'token_expires_at' => Carbon::now()->addDays(7)->toDateTimeString(),
        ], 'Token refreshed successfully.');
    }
}
