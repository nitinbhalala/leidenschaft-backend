<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Customer;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends BaseController
{
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (Throwable $e) {
            return redirect("http://localhost:3000/social-callback?error=" . urlencode($e->getMessage()));
        }

        $customer = Customer::where('provider_id', $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($customer) {
            $customer->update([
                'provider_id' => $socialUser->getId(),
                'avatar'      => $customer->avatar ?? $socialUser->getAvatar(),
                'provider'    => $provider,
            ]);
        } else {
            $customer = Customer::create([
                'name'        => $socialUser->getName(),
                'email'       => $socialUser->getEmail(),
                'provider_id' => $socialUser->getId(),
                'avatar'      => $socialUser->getAvatar(),
                'provider'    => $provider,
                'password'    => null,
            ]);
        }

        $token = Str::random(64);

        $customer->update([
            'token'            => $token,
            'token_expires_at' => Carbon::now()->addDays(7),
        ]);

        return redirect("http://localhost:3000/social-callback?token={$token}&expires_at=" . Carbon::now()->addDays(7)->toDateTimeString());
    }

    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404, 'Unsupported social provider.');
        }
    }
}
