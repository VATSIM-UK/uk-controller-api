<?php

use App\Http\Controllers\Auth\CoreAuthController;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('filament.auth.login');
    });
});

Route::middleware('guest')->group(function () {
    Route::get('/auth/redirect', function () {
        return Socialite::driver('vatsimuk')->redirect();
    })->name('vatsimuk.redirect');

    Route::get('/auth/callback', [CoreAuthController::class, 'callback'])
        ->name('auth.login.callback');
});
