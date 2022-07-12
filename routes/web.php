<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

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

Route::get('welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Redirect to allow old plugin versions to update to the latest
Route::get('version/latest', fn () => Redirect::to('api/version/latest'));

// Redirects to allow Core to do Core things until updated
Route::match(
    ['get', 'post'],
    'user/{cid}',
    fn ($cid) => Redirect::to('api/user/' . $cid)
);

Route::post('user/{cid}/token', fn ($cid) => Redirect::to(sprintf('api/user/%s/token', $cid)));
Route::delete('token/{tokenId}', fn ($tokenId) => Redirect::to(sprintf('api/token/%s', $tokenId)));

require __DIR__.'/auth.php';
