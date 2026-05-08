<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Customer;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends BaseController
{
    private function setSocialConfigFromDB(string $provider): void
    {
        $settings = Setting::whereIn('key', [
            "{$provider}_client_id",
            "{$provider}_client_secret",
            "{$provider}_redirect_uri",
        ])->pluck('value', 'key');

        if ($settings->isEmpty()) return;

        config([
            "services.$provider.client_id"     => $settings["{$provider}_client_id"] ?? null,
            "services.$provider.client_secret" => $settings["{$provider}_client_secret"] ?? null,
            "services.$provider.redirect"      => $settings["{$provider}_redirect_uri"] ?? null,
        ]);

        // Fix: Force Facebook to use a stable API version that supports email scope
        if ($provider === 'facebook') {
            config([
                'services.facebook.version' => 'v19.0',
            ]);
        }
    }

    public function redirect(string $provider, \Illuminate\Http\Request $request)
    {
        $this->validateProvider($provider);

        $this->setSocialConfigFromDB($provider);

        $frontendUrl = rtrim(
            $request->header('Origin', $request->header('Referer', '')),
            '/'
        );

        $driver = Socialite::driver($provider)->stateless();

        if ($provider === 'facebook') {
            $driver->scopes(['public_profile'])->fields(['id', 'name', 'email', 'picture']);
        }

        return $driver
            ->with([
                'state' => base64_encode($frontendUrl)
            ])
            ->redirect();
    }

    public function callback(string $provider, \Illuminate\Http\Request $request)
    {
        $this->setSocialConfigFromDB($provider);

        $frontendUrl = base64_decode($request->input('state', ''));

        $frontendUrl = rtrim($frontendUrl, '/');

        try {
            $driver = Socialite::driver($provider)->stateless();

            if ($provider === 'facebook') {
                $driver->scopes(['public_profile'])->fields(['id', 'name', 'email', 'picture']);
            }

            $socialUser = $driver->user();
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
            'phone'                    => $customer->phone,
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
