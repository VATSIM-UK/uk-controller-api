<?php

use App\Providers\AuthServiceProvider;

// Routes that the average user will use
$router->group(['middleware' => 'scopes:' . AuthServiceProvider::SCOPE_USER], function () use ($router) {

    // Default route, just used to check if the API is available and the user is authenticated
    $router->get(
        '/',
        [
            'middleware' => [
                'user.lastlogin',
            ],
            'uses' => 'TeapotController@teapot',
        ]
    );
    
    // Version checking
    $router->get(
        'version/{version:[A-Za-z0-9\.\-]+}/status',
        [
            'middleware' => [
                'user.version',
            ],
            'uses' => 'VersionController@getVersionStatus'
        ]
    );

    // Holds
    $router->get('hold', 'HoldController@getAllHolds');
    $router->get('hold/profile', 'HoldController@getGenericHoldProfiles');
    $router->get('hold/profile/user', 'HoldController@getUserHoldProfiles');
    $router->put('hold/profile/user', 'HoldController@createUserHoldProfile');
    $router->put('hold/profile/user/{profile_id:\d+}', 'HoldController@updateUserHoldProfile');
    $router->delete('hold/profile/user/{profile_id:\d+}', 'HoldController@deleteUserHoldProfile');

    // Dependencies
    $router->get('dependency', 'DependencyController@getManifest');
    
    // Squawks
    $router->get('squawk-assignment/{callsign:[A-Za-z0-9\-]{1,10}}', 'SquawkController@getSquawkAssignment');
    $router->put('squawk-assignment/{callsign:[A-Za-z0-9\-]{1,10}}', 'SquawkController@assignSquawk');
    $router->delete('squawk-assignment/{callsign:[A-Za-z0-9\-]{1,10}}', 'SquawkController@deleteSquawkAssignment');
    
    // Regional Pressure
    $router->get('regional-pressure', 'RegionalPressureController@getRegionalPressures');
});

// Routes for user administration
$router->group(['middleware' => 'scopes:' . AuthServiceProvider::SCOPE_USER_ADMIN], function () use ($router) {

    // A test route for useradmin access
    $router->get('useradmin', 'TeapotController@teapot');

    // Get user
    $router->get(
        'user/{cid:[0-9]+}',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@getUser',
        ]
    );

    // Create user
    $router->post(
        'user/{cid:[0-9]+}',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@createUser',
        ]
    );

    // Reactivate user account
    $router->put(
        'user/{cid:[0-9]+}/reactivate',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@reactivateUser',
        ]
    );

    // Ban user account
    $router->put(
        'user/{cid:[0-9]+}/ban',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@banUser',
        ]
    );

    // Disable user account
    $router->put(
        'user/{cid:[0-9]+}/disable',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@disableUser',
        ]
    );

    // Create user token
    $router->post(
        'user/{cid:[0-9]+}/token',
        [
            'middleware' => 'vatsim.cid',
            'uses' => 'UserController@createUserToken',
        ]
    );

    // Delete user token
    $router->delete(
        'token/{tokenId}',
        [
            'uses' => 'UserController@deleteUserToken',
        ]
    );
});

// Routes for user administration
$router->group(['middleware' => 'scopes:' . AuthServiceProvider::SCOPE_VERSION_ADMIN], function () use ($router) {

        // A test route for useradmin access
    $router->get('versionadmin', 'TeapotController@teapot');

    // Routes for returning information about versions
    $router->get('version', 'VersionController@getAllVersions');
    $router->get('version/{version:[A-Za-z0-9\.\-]+}', 'VersionController@getVersion');

    // Route for updating and creating versions
    $router->put('version/{version:[A-Za-z0-9\.\-]+}', 'VersionController@createOrUpdateVersion');
});
