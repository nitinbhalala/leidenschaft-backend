<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token              = Str::random(64);
        $refreshToken       = Str::random(128);
        $tokenExpiresAt     = Carbon::now()->addDay();
        $refreshExpiresAt   = Carbon::now()->addDays(30);

        $customer->update([
            'token'                    => $token,
            'token_expires_at'         => $tokenExpiresAt,
            'refresh_token'            => $refreshToken,
            'refresh_token_expires_at' => $refreshExpiresAt,
        ]);

        return $this->success([
            'user' => [
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'phone'  => $customer->phone,
                'avatar' => $customer->avatar,
            ],
            'token'                    => $token,
            'token_expires_at'         => $tokenExpiresAt->toDateTimeString(),
            'refresh_token'            => $refreshToken,
            'refresh_token_expires_at' => $refreshExpiresAt->toDateTimeString(),
            'redirect'                 => '/'
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

        $token              = Str::random(64);
        $refreshToken       = Str::random(128);
        $tokenExpiresAt   = Carbon::now()->addMinute();
        $refreshExpiresAt = Carbon::now()->addMinutes(2);

        $customer->update([
            'token'                    => $token,
            'token_expires_at'         => $tokenExpiresAt,
            'refresh_token'            => $refreshToken,
            'refresh_token_expires_at' => $refreshExpiresAt,
        ]);

        return $this->success([
            'user' => [
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'avatar' => $customer->avatar ? Storage::disk('public')->url($customer->avatar) : null,
                'phone'  => $customer->phone,
            ],
            'token'                    => $token,
            'token_expires_at'         => $tokenExpiresAt->toDateTimeString(),
            'refresh_token'            => $refreshToken,
            'refresh_token_expires_at' => $refreshExpiresAt->toDateTimeString(),
            'redirect'                 => '/'
        ], 'Login successful! Welcome back, ' . $customer->name . '.');
    }

    public function logout(Request $request): JsonResponse
    {
        $customer = $request->attributes->get('customer');

        if ($customer) {
            $customer->update([
                'token'                    => null,
                'token_expires_at'         => null,
                'refresh_token'            => null,
                'refresh_token_expires_at' => null,
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

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $customer = $request->attributes->get('customer');

        if (!$customer) {
            return $this->error('Unauthorized. Please login again.', 401);
        }

        try {
            $updateData = [
                'name'  => $request->name,
                'phone' => $request->phone,
            ];

            if ($request->hasFile('avatar')) {
                $oldAvatar = $customer->getRawOriginal('avatar');
                if ($oldAvatar) {
                    Storage::disk('public')->delete($oldAvatar);
                }

                $path = $request->file('avatar')->store('avatars', 'public');
                $updateData['avatar'] = $path;
            }

            $customer->update($updateData);

            return $this->success([
                'id'     => $customer->id,
                'name'   => $customer->name,
                'email'  => $customer->email,
                'phone'  => $customer->phone,
                'avatar' => $customer->avatar ? Storage::disk('public')->url($customer->avatar) : null,
            ], 'Profile updated successfully.');
        } catch (\Exception $e) {
            return $this->error('Error updating profile.', 500, $e->getMessage());
        }
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return $this->error('Unauthorized. Refresh token is required as bearer token.', 401);
        }

        $customer = Customer::where('refresh_token', $bearerToken)->first();

        if (!$customer) {
            return $this->error('Unauthorized. Invalid refresh token.', 401);
        }

        if (!$customer->refresh_token_expires_at || Carbon::parse($customer->refresh_token_expires_at)->isPast()) {
            $customer->update([
                'token'                    => null,
                'token_expires_at'         => null,
                'refresh_token'            => null,
                'refresh_token_expires_at' => null,
            ]);

            return $this->error('Session expired. Please login again.', 401);
        }

        $newToken           = Str::random(64);
        $newRefreshToken    = Str::random(128);
        $tokenExpiresAt     = Carbon::now()->addDay();
        $refreshExpiresAt   = Carbon::now()->addDays(30);

        $customer->update([
            'token'                    => $newToken,
            'token_expires_at'         => $tokenExpiresAt,
            'refresh_token'            => $newRefreshToken,
            'refresh_token_expires_at' => $refreshExpiresAt,
        ]);

        return $this->success([
            'token'                    => $newToken,
            'token_expires_at'         => $tokenExpiresAt->toDateTimeString(),
            'refresh_token'            => $newRefreshToken,
            'refresh_token_expires_at' => $refreshExpiresAt->toDateTimeString(),
        ], 'Token refreshed successfully.');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return $this->error(null, 'No account found with this email address.', 401);
        }

        $resetToken = Str::random(64);
        $expiresAt  = Carbon::now()->addMinutes(60);

        $customer->update([
            'password_reset_token'      => $resetToken,
            'password_reset_expires_at' => $expiresAt,
        ]);

        $frontendUrl = rtrim($request->header('Origin', $request->header('Referer', '')), '/');
        $resetLink   = $frontendUrl . '/?reset-password-token=' . $resetToken . '&email=' . urlencode($customer->email);

        Mail::to($customer->email)->send(new ForgotPasswordMail($customer->name, $resetLink));

        return $this->success(null, 'A password reset link has been sent to your email. Please check your inbox.');
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'                 => 'required|string',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $customer = Customer::where('email', $request->email)
            ->where('password_reset_token', $request->token)
            ->first();

        if (!$customer) {
            return $this->error('Invalid or expired password reset link.', 422);
        }

        if (!$customer->password_reset_expires_at || Carbon::parse($customer->password_reset_expires_at)->isPast()) {
            $customer->update([
                'password_reset_token'      => null,
                'password_reset_expires_at' => null,
            ]);

            return $this->error('Password reset link has expired. Please request a new one.', 422);
        }

        $customer->update([
            'password'                  => Hash::make($request->password),
            'password_reset_token'      => null,
            'password_reset_expires_at' => null,
            'token'                     => null,
            'token_expires_at'          => null,
            'refresh_token'             => null,
            'refresh_token_expires_at'  => null,
        ]);

        return $this->success(null, 'Password reset successfully. Please login with your new password.');
    }
}
