<?php

use App\Http\Controllers\Auth\CoreAuthController;
use Illuminate\Support\Facades\Redirect;
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
    Route::get('/auth/redirect', [CoreAuthController::class, 'redirect'])
        ->name('vatsimuk.redirect');

    Route::get('/auth/callback', [CoreAuthController::class, 'callback'])
        ->name('auth.login.callback');
});

// Redirect to allow old plugin versions to update to the latest
Route::get('version/latest', fn () => Redirect::to('api/version/latest'));

// API Documentation (Swagger UI)
Route::prefix('api/documentation')->group(function () {
    Route::get('', function () {
        return view('api-documentation');
    });

    Route::get('openapi.yaml', function () {
        $specPath = base_path('docs/openapi.yaml');

        if (!file_exists($specPath)) {
            abort(404);
        }

        return response()->file($specPath, [
            'Content-Type' => 'application/x-yaml',
            'Content-Disposition' => 'inline; filename="openapi.yaml"',
        ]);
    });
});
