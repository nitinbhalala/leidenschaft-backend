<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends BaseController
{
    public function redirect(string $provider, \Illuminate\Http\Request $request)
    {
        $this->validateProvider($provider);

        $frontendUrl = rtrim($request->header('Origin', $request->header('Referer', '')), '/');

        return Socialite::driver($provider)
            ->stateless()
            ->with(['state' => $frontendUrl])
            ->redirect();
    }

    public function callback(string $provider, \Illuminate\Http\Request $request)
    {
        $this->validateProvider($provider);

        $frontendUrl = rtrim($request->input('state', ''), '/');

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (Throwable $e) {
            return redirect($frontendUrl . '?error=' . urlencode($e->getMessage()));
        }

        $customer = Customer::where('provider_id', $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($customer) {
            $avatarPath = $customer->getRawOriginal('avatar') ?? $this->saveAvatarFromUrl($socialUser->getAvatar());
            $customer->update([
                'provider_id' => $socialUser->getId(),
                'avatar'      => $avatarPath,
                'provider'    => $provider,
            ]);
        } else {
            $customer = Customer::create([
                'name'        => $socialUser->getName(),
                'email'       => $socialUser->getEmail(),
                'provider_id' => $socialUser->getId(),
                'avatar'      => $this->saveAvatarFromUrl($socialUser->getAvatar()),
                'provider'    => $provider,
                'password'    => null,
            ]);
        }

        $token = Str::random(64);

        $customer->update([
            'token'            => $token,
            'token_expires_at' => Carbon::now()->addDays(7),
        ]);

        $refreshToken     = Str::random(128);
        $refreshExpiresAt = Carbon::now()->addDays(30);

        $customer->update([
            'refresh_token'            => $refreshToken,
            'refresh_token_expires_at' => $refreshExpiresAt,
        ]);

        $customer->refresh();

        $payload = base64_encode(json_encode([
            'id'                       => $customer->id,
            'name'                     => $customer->name,
            'email'                    => $customer->email,
            'avatar'                   => $customer->avatar,
            'provider'                 => $customer->provider,
            'token'                    => $token,
            'token_expires_at'         => Carbon::now()->addDays(7)->toDateTimeString(),
            'refresh_token'            => $refreshToken,
            'refresh_token_expires_at' => $refreshExpiresAt->toDateTimeString(),
        ]));

        return redirect($frontendUrl . '?data=' . $payload);
    }

    private function saveAvatarFromUrl(?string $url): ?string
    {
        if (!$url) return null;

        try {
            $response = Http::get($url);
            if (!$response->successful()) return null;

            $extension = 'jpg';
            $path      = 'avatars/' . Str::uuid() . '.' . $extension;
            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (Throwable) {
            return null;
        }
    }

    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404, 'Unsupported social provider.');
        }
    }
}
