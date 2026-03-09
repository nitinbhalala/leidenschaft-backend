<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the social provider's authentication page.
     */
    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback from the social provider.
     */
    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $e) {
            return redirect('/')->with('error', 'Authentication with ' . ucfirst($provider) . ' failed. Please try again.');
        }

        // Find customer by provider ID or email
        $customer = Customer::where('provider_id', $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($customer) {
            // Update provider ID and avatar if missing
            $customer->update([
                'provider_id' => $socialUser->getId(),
                'avatar'      => $customer->avatar ?? $socialUser->getAvatar(),
                'provider'    => $provider,
            ]);
        } else {
            // Create a new customer
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

    /**
     * Validate that the provider is supported.
     */
    private function validateProvider(string $provider): void
    {
        if (! in_array($provider, ['google', 'facebook'])) {
            abort(404, 'Unsupported social provider.');
        }
    }
}
