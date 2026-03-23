<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Customer;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends BaseController
{
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->stateless()->redirect();  // 👈 add stateless()
    }

    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();  // 👈 add stateless()
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ], 401);
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

        // Generate Sanctum token instead of Auth::login()  👈
        $token = $customer->createToken('auth_token')->plainTextToken;

        // Redirect to React app with token in URL  👈
        return redirect("http://localhost:3000/social-callback?token={$token}");
    }

    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404, 'Unsupported social provider.');
        }
    }
}
