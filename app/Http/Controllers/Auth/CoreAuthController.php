<?php

namespace App\Http\Controllers\Auth;

use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class CoreAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('vatsimuk')->redirect();
    }

    public function callback()
    {
        try {
            $socialiteUser = Socialite::driver('vatsimuk')->user();
        } catch (InvalidStateException) {
            abort(401);
        }

        $user = User::updateOrCreate(
            [
                'id' => $socialiteUser->getId(),
            ],
            [
                'first_name' => $socialiteUser->first_name,
                'last_name' => $socialiteUser->last_name,
                'last_login' => Carbon::now(),
            ]
        );

        Auth::login($user);

        return redirect()->route('filament.pages.dashboard');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }
}
