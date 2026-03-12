<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends BaseController
{
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {

            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $e) {

            return redirect('/')->with('error', 'Authentication with ' . ucfirst($provider) . ' failed. Please try again.');
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

        Auth::login($customer, true);

        return redirect('/')->with('success', 'Login with ' . ucfirst($provider) . ' was successful!');
    }

    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404, 'Unsupported social provider.');
        }
    }
}
