<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends BaseController
{
    public function login(LoginRequest $request): JsonResponse
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

        $admin = Auth::guard('admin')->user();

        return $this->success([
            'user' => [
                'id'    => $admin->id,
                'name'  => $admin->name,
                'email' => $admin->email,
            ],
            'redirect' => '/admin/dashboard'
        ], 'Admin login successful! Welcome back, ' . $admin->name . '.');
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success([
            'redirect' => '/admin/login'
        ], 'Admin logged out successfully.');
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required', 'min:6'],
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->old_password, $admin->password)) {
            return $this->error(
                'Old password is incorrect.',
                422,
                [
                    'old_password' => ['The provided password does not match our records.']
                ]
            );
        }

        $admin->update([
            'password' => Hash::make($request->new_password)
        ]);

        return $this->success(
            null,
            'Password changed successfully.'
        );
    }
}
