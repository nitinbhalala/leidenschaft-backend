<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        Auth::login($customer);

        return $this->success([
            'user' => [
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'avatar' => $customer->avatar,
            ],
            'redirect' => '/'
        ], 'Registration successful! Welcome, ' . $customer->name . '.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {

            return $this->error(
                'These credentials do not match our records.',
                422,
                [
                    'email' => ['Invalid email or password.']
                ]
            );
        }

        $request->session()->regenerate();

        $customer = Auth::user();

        return $this->success([
            'user' => [
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'avatar' => $customer->avatar,
            ],
            'redirect' => '/'
        ], 'Login successful! Welcome back, ' . $customer->name . '.');
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success([
            'redirect' => '/'
        ], 'You have been logged out successfully.');
    }

    public function adminLogin(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::guard('admin')->attempt($credentials, $remember)) {

            return $this->error(
                'These credentials do not match our records.',
                422,
                [
                    'email' => ['Invalid email or password.']
                ]
            );
        }

        $request->session()->regenerate();

        $user = Auth::guard('admin')->user();

        return $this->success([
            'user' => [
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'redirect' => '/admin'
        ], 'Admin login successful! Welcome back, ' . $user->name . '.');
    }
}
