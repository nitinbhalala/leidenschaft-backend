<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($customer);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Welcome, ' . $customer->name . '.',
            'user'    => [
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'avatar' => $customer->avatar,
            ],
            'redirect' => '/',
        ], 201);
    }

    /**
     * Handle user login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return response()->json([
                'success' => false,
                'message' => 'These credentials do not match our records.',
                'errors'  => [
                    'email' => ['Invalid email or password.'],
                ],
            ], 422);
        }

        $request->session()->regenerate();

        $customer = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'Login successful! Welcome back, ' . $customer->name . '.',
            'user'    => [
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'avatar' => $customer->avatar,
            ],
            'redirect' => '/',
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success'  => true,
            'message'  => 'You have been logged out successfully.',
            'redirect' => '/',
        ]);
    }

    public function adminLogin(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (! Auth::guard('admin')->attempt($credentials, $remember)) {
            return response()->json([
                'success' => false,
                'message' => 'These credentials do not match our records.',
                'errors'  => [
                    'email' => ['Invalid email or password.'],
                ],
            ], 422);
        }

        $request->session()->regenerate();

        $user = Auth::guard('admin')->user();

        return response()->json([
            'success' => true,
            'message' => 'Admin login successful! Welcome back, ' . $user->name . '.',
            'user'    => [
                'name'   => $user->name,
                'email'  => $user->email,
            ],
            'redirect' => '/admin',
        ]);
    }
}
