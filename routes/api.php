<?php

use App\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Route;

// Routes that the average user will use
Route::middleware(['scopes:' . AuthServiceProvider::SCOPE_USER])->group(function () {

    // Default route, just used to check if the API is available and the user is authenticated
    Route::get(
        '/',
        [
            'middleware' => [
                'user.lastlogin',
            ],
            'uses' => 'TeapotController@teapot',
        ]
    );
    
    // Version checking
    Route::get(
        'version/{version:[A-Za-z0-9\.\-]+}/status',
        [
            'middleware' => [
                'user.version',
            ],
            'uses' => 'VersionController@getVersionStatus'
        ]
    );

    // Holds
    Route::get('hold', 'HoldController@getAllHolds');
    Route::get('hold/profile', 'HoldController@getUserHoldProfiles');
    Route::put('hold/profile', 'HoldController@createUserHoldProfile');
    Route::put('hold/profile/{profile_id:\d+}', 'HoldController@updateUserHoldProfile');
    Route::delete('hold/profile/{profile_id:\d+}', 'HoldController@deleteUserHoldProfile');

    // Dependencies
    Route::get('dependency', 'DependencyController@getManifest');
    
    // Squawks
    Route::get('squawk-assignment/{callsign:[A-Za-z0-9\-]{1,10}}', 'SquawkController@getSquawkAssignment');
    Route::put('squawk-assignment/{callsign:[A-Za-z0-9\-]{1,10}}', 'SquawkController@assignSquawk');
    Route::delete('squawk-assignment/{callsign:[A-Za-z0-9\-]{1,10}}', 'SquawkController@deleteSquawkAssignment');
    
    // Regional Pressure
    Route::get('regional-pressure', 'RegionalPressureController@getRegionalPressures');

    // Min Stack Levels
    Route::get('msl/airfield', 'MinStackController@getAirfieldMinStackLevels');
    Route::get('msl/tma', 'MinStackController@getTmaMinStackLevels');
    Route::get('msl/airfield/{icao}', 'MinStackController@getMslForAirfield');
    Route::get('msl/tma/{tma}', 'MinStackController@getMslForTma');

    // Broadcasting
    Route::post('broadcasting/auth', ['uses' => 'BroadcastController@authenticate']);
});

// Routes for user administration
Route::middleware(['scopes:' . AuthServiceProvider::SCOPE_USER_ADMIN])->group(function () {

    // A test route for useradmin access
    Route::get('useradmin', 'TeapotController@teapot');

    // Get user
    Route::get(
        'user/{cid:[0-9]+}',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@getUser',
        ]
    );

    // Create user
    Route::post(
        'user/{cid:[0-9]+}',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@createUser',
        ]
    );

    // Reactivate user account
    Route::put(
        'user/{cid:[0-9]+}/reactivate',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@reactivateUser',
        ]
    );

    // Ban user account
    Route::put(
        'user/{cid:[0-9]+}/ban',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@banUser',
        ]
    );

    // Disable user account
    Route::put(
        'user/{cid:[0-9]+}/disable',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@disableUser',
        ]
    );

    // Create user token
    Route::post(
        'user/{cid:[0-9]+}/token',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@createUserToken',
        ]
    );

    // Delete user token
    Route::delete(
        'token/{tokenId}',
        [
            'uses' => 'UserController@deleteUserToken',
        ]
    );
});

// Routes for user administration
$router->group(['middleware' => 'scopes:' . AuthServiceProvider::SCOPE_VERSION_ADMIN], function () use ($router) {

        // A test route for useradmin access
    Route::get('versionadmin', 'TeapotController@teapot');

    // Routes for returning information about versions
    Route::get('version', 'VersionController@getAllVersions');
    Route::get('version/{version:[A-Za-z0-9\.\-]+}', 'VersionController@getVersion');

    // Route for updating and creating versions
    Route::put('version/{version:[A-Za-z0-9\.\-]+}', 'VersionController@createOrUpdateVersion');
});
