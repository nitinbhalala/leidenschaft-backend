<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\LoginRequest;
use App\Models\UserToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminAuthController extends BaseController
{
    public function login(LoginRequest $request): JsonResponse
    {
        $admin = User::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return $this->error(
                'These credentials do not match our records.',
                422,
                ['email' => ['Invalid email or password.']]
            );
        }

        $token     = Str::random(64);
        $expiresAt = Carbon::now()->addDays(7);

        $admin->tokens()->create([
            'token'            => $token,
            'token_expires_at' => $expiresAt,
            'device_name'      => $request->header('User-Agent'),
            'ip_address'       => $request->ip(),
        ]);

        return $this->success([
            'user' => [
                'id'    => $admin->id,
                'name'  => $admin->name,
                'email' => $admin->email,
            ],
            'token'            => $token,
            'token_expires_at' => $expiresAt->toDateTimeString(),
            'redirect'         => '/admin/dashboard',
        ], 'Admin login successful! Welcome back, ' . $admin->name . '.');
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if ($token) {
            UserToken::where('token', $token)->delete();
        }

        return $this->success(['redirect' => '/admin/login'], 'Admin logged out successfully.');
    }

    public function verifyToken(Request $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        if (!$admin) {
            return $this->error('Unauthorized. Please login again.', 401);
        }

        $token     = $request->bearerToken();
        $userToken = UserToken::where('token', $token)->first();

        return $this->success([
            'user' => [
                'id'    => $admin->id,
                'name'  => $admin->name,
                'email' => $admin->email,
            ],
            'token'            => $token,
            'token_expires_at' => $userToken?->token_expires_at,
        ], 'Token is valid.');
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        if (!$admin) {
            return $this->error('Unauthorized. Please login again.', 401);
        }

        $oldToken  = $request->bearerToken();
        $newToken  = Str::random(64);
        $expiresAt = Carbon::now()->addDays(7);

        UserToken::where('token', $oldToken)->update([
            'token'            => $newToken,
            'token_expires_at' => $expiresAt,
        ]);

        return $this->success([
            'token'            => $newToken,
            'token_expires_at' => $expiresAt->toDateTimeString(),
        ], 'Token refreshed successfully.');
    }

    public function activeSessions(Request $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        if (!$admin) {
            return $this->error('Unauthorized.', 401);
        }

        $sessions = $admin->tokens()
            ->where('token_expires_at', '>', Carbon::now())
            ->select('id', 'device_name', 'ip_address', 'created_at', 'token_expires_at')
            ->orderByDesc('created_at')
            ->get();

        return $this->success(['sessions' => $sessions], 'Active sessions.');
    }

    public function revokeSession(Request $request, int $tokenId): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        if (!$admin) {
            return $this->error('Unauthorized.', 401);
        }

        $admin->tokens()->where('id', $tokenId)->delete();

        return $this->success([], 'Session revoked.');
    }
}
