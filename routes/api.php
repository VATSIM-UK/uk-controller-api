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
        'version/{version}/status',
        [
            'middleware' => [
                'user.version',
            ],
            'uses' => 'VersionController@getVersionStatus'
        ]
    )->where('version', '[A-Za-z0-9\.\-]+');

    // Holds
    Route::get('hold', 'HoldController@getAllHolds');
    Route::get('hold/profile', 'HoldController@getUserHoldProfiles');
    Route::put('hold/profile', 'HoldController@createUserHoldProfile');
    Route::put('hold/profile/{profile_id}', 'HoldController@updateUserHoldProfile')
        ->where('profile_id', '\d+');
    Route::delete('hold/profile/{profile_id}', 'HoldController@deleteUserHoldProfile')
        ->where('profile_id', '\d+');

    // Dependencies
    Route::get('dependency', 'DependencyController@getManifest');
    
    // Squawks
    Route::get('squawk-assignment/{callsign}', 'SquawkController@getSquawkAssignment')
        ->where('callsign', '[A-Za-z0-9\-]{1,10}');
    Route::put('squawk-assignment/{callsign}', 'SquawkController@assignSquawk')
        ->where('callsign', '[A-Za-z0-9\-]{1,10}');
    Route::delete('squawk-assignment/{callsign}', 'SquawkController@deleteSquawkAssignment')
        ->where('callsign', '[A-Za-z0-9\-]{1,10}');
    
    // Regional Pressure
    Route::get('regional-pressure', 'RegionalPressureController@getRegionalPressures');

    // Min Stack Levels
    Route::get('msl/airfield', 'MinStackController@getAirfieldMinStackLevels');
    Route::get('msl/tma', 'MinStackController@getTmaMinStackLevels');
    Route::get('msl/airfield/{icao}', 'MinStackController@getMslForAirfield')
        ->where('icao', '[A-Z]{4}');
    Route::get('msl/tma/{tma}', 'MinStackController@getMslForTma')
        ->where('tma', '[A-Z]{4}');

    // Initial Altitudes
    Route::get('initial-altitude', 'SidController@getInitialAltitudeDependency');
    Route::get('sid', 'SidController@getAllSids');
    Route::get('sid/{id}', 'SidController@getSid')
        ->where('sid', 'd+');
    Route::delete('sid/{id}', 'SidController@deleteSid')
        ->where('sid', 'd+');
    Route::put('sid', 'SidController@createSid');
    Route::put('sid/{id}', 'SidController@updateSid')
        ->where('sid', 'd+');

    // Broadcasting
    Route::post('broadcasting/auth', ['uses' => 'BroadcastController@authenticate']);
});

// Routes for user administration
Route::middleware(['scopes:' . AuthServiceProvider::SCOPE_USER_ADMIN])->group(function () {

    // A test route for useradmin access
    Route::get('useradmin', 'TeapotController@teapot');

    // Get user
    Route::get(
        'user/{cid}',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@getUser',
        ]
    )->where('cid', '[0-9]+');

    // Create user
    Route::post(
        'user/{cid}',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@createUser',
        ]
    )->where('cid', '[0-9]+');

    // Reactivate user account
    Route::put(
        'user/{cid}/reactivate',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@reactivateUser',
        ]
    )->where('cid', '[0-9]+');

    // Ban user account
    Route::put(
        'user/{cid}/ban',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@banUser',
        ]
    )->where('cid', '[0-9]+');

    // Disable user account
    Route::put(
        'user/{cid}/disable',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@disableUser',
        ]
    )->where('cid', '[0-9]+');

    // Create user token
    Route::post(
        'user/{cid}/token',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@createUserToken',
        ]
    )->where('cid', '[0-9]+');

    // Delete user token
    Route::delete(
        'token/{tokenId}',
        [
            'uses' => 'UserController@deleteUserToken',
        ]
    );
});

// Routes for user administration
Route::middleware(['scopes:' . AuthServiceProvider::SCOPE_VERSION_ADMIN])->group(function () {
    // A test route for useradmin access
    Route::get('versionadmin', 'TeapotController@teapot');

    // Routes for returning information about versions
    Route::get('version', 'VersionController@getAllVersions');
    Route::get('version/{version}', 'VersionController@getVersion')
        ->where('version', '[A-Za-z0-9\.\-]+');

    // Route for updating and creating versions
    Route::put('version/{version}', 'VersionController@createOrUpdateVersion')
        ->where('version', '[A-Za-z0-9\.\-]+');
});
