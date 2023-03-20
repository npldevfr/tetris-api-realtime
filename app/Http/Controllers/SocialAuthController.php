<?php

namespace App\Http\Controllers;

use App\Enums\Provider;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(Provider $provider) {
        return Socialite::driver($provider->value)->redirect();
    }

    public function authenticate(Provider $provider) {

        // create a new user if one doesn't already exist and return the user and token
        $socialUser = Socialite::driver($provider->value)->user();
        $user = User::firstOrCreate(
            ['provider_id' => $socialUser->getId()],
            [
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'avatar_url' => $socialUser->getAvatar(),
                'provider_name' => $provider->value,
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);



    }
}
