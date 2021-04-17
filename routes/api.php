<?php

use App\Rules\VatsimCallsign;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MiddlewareKeys;

// Routes that the plugin user will use
Route::middleware('plugin.user')->group(function () {
    Route::get(
        '/authorise',
        [
            'middleware' => [
                'user.lastlogin',
            ],
            'uses' => 'TeapotController@normalTeapots',
        ]
    );

    // Departure releases
    Route::post('departure/release/request', 'DepartureReleaseController@makeReleaseRequest');

    // Dependencies
    Route::get('dependency', 'DependencyController@getAllDependencies');
    Route::get('dependency/{id}', 'DependencyController@getDependency')
        ->where('id', '[0-9]+');

    // Events
    Route::get('plugin-events/sync', 'PluginEventsController@getLatestPluginEventId');
    Route::get('plugin-events/recent', 'PluginEventsController@getRecentPluginEvents');

    // Holds
    Route::put('hold/assigned', 'HoldController@assignHold');
    Route::delete('hold/assigned/{callsign}', 'HoldController@deleteAssignedHold')
        ->where('callsign', VatsimCallsign::CALLSIGN_REGEX);

    // Squawks
    Route::get('squawk-assignment/{callsign}', 'SquawkController@getSquawkAssignment')
        ->where('callsign', VatsimCallsign::CALLSIGN_REGEX);
    Route::put('squawk-assignment/{callsign}', 'SquawkController@assignSquawk')
        ->where('callsign', VatsimCallsign::CALLSIGN_REGEX);
    Route::delete('squawk-assignment/{callsign}', 'SquawkController@deleteSquawkAssignment')
        ->where('callsign', VatsimCallsign::CALLSIGN_REGEX);

    // Enroute releases
    Route::post('release/enroute', 'ReleaseController@enrouteRelease');

    // Stands
    Route::put('stand/assignment', 'StandController@createStandAssignment');
    Route::delete('stand/assignment/{callsign}', 'StandController@deleteStandAssignment')
        ->where('callsign', VatsimCallsign::CALLSIGN_REGEX);

    // Notifications
    Route::get('notifications', 'NotificationController@getActiveNotifications');
    Route::get('notifications/unread', 'NotificationController@getUnreadNotifications');
    Route::put('notifications/read/{id}', 'NotificationController@readNotification')
        ->where('id', '[0-9]+');

    // Version checking
    Route::get(
        'version/{version}/status',
        [
            'middleware' => [
                'user.version',
            ],
            'uses' => 'VersionController@getVersionStatus',
        ]
    )->where('version', '[A-Za-z0-9\.\-]+');
});

// Routes for user administration
Route::middleware('admin.user')->group(function () {

    // A test route for useradmin access
    Route::get('useradmin', 'TeapotController@normalTeapots');

    // Get user
    Route::get(
        'user/{cid}',
        [
            'middleware' => MiddlewareKeys::VATSIM_CID,
            'uses' => 'UserController@getUser',
        ]
    )->where('cid', '[0-9]+');

    // Create user
    Route::post(
        'user/{cid}',
        [
            'middleware' => MiddlewareKeys::VATSIM_CID,
            'uses' => 'UserController@createUser',
        ]
    )->where('cid', '[0-9]+');

    // Reactivate user account
    Route::put(
        'user/{cid}/reactivate',
        [
            'middleware' => MiddlewareKeys::VATSIM_CID,
            'uses' => 'UserController@reactivateUser',
        ]
    )->where('cid', '[0-9]+');

    // Ban user account
    Route::put(
        'user/{cid}/ban',
        [
            'middleware' => MiddlewareKeys::VATSIM_CID,
            'uses' => 'UserController@banUser',
        ]
    )->where('cid', '[0-9]+');

    // Disable user account
    Route::put(
        'user/{cid}/disable',
        [
            'middleware' => MiddlewareKeys::VATSIM_CID,
            'uses' => 'UserController@disableUser',
        ]
    )->where('cid', '[0-9]+');

    // Create user token
    Route::post(
        'user/{cid}/token',
        [
            'middleware' => MiddlewareKeys::VATSIM_CID,
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
Route::middleware('admin.version')->group(function () {
    // A test route for useradmin access
    Route::get('versionadmin', 'TeapotController@normalTeapots');

    // Routes for returning information about versions
    Route::get('version', 'VersionController@getAllVersions');
    Route::get('version/{version}', 'VersionController@getVersion')
        ->where('version', '[A-Za-z0-9\.\-]+');

    // Route for updating and creating versions
    Route::put('version/{version}', 'VersionController@createOrUpdateVersion')
        ->where('version', '[A-Za-z0-9\.\-]+');
});

// Routes for dependency administration
Route::middleware('admin.dependency')->group(function () {

    // Initial altitudes and sids
    Route::middleware('dependency.update:DEPENDENCY_PRENOTE,DEPENDENCY_INITIAL_ALTITUDES,DEPENDENCY_SID_HANDOFF')
        ->group(
            function () {
                Route::delete('sid/{id}', 'SidController@deleteSid')
                    ->where('sid', 'd+');
                Route::put('sid', 'SidController@createSid');
                Route::put('sid/{id}', 'SidController@updateSid')
                    ->where('sid', 'd+');
            }
        );
});

// Routes for data management.
Route::middleware('admin.data')->group(function () {
    Route::get('dataadmin', 'TeapotController@normalTeapots');

    Route::prefix('admin')->group(function () {
        Route::get('/airfields', 'Admin\\StandAdminController@getAirfields');
        Route::post('/airfields/{airfield:code}/stands', 'Admin\\StandAdminController@createNewStand');
        Route::get('/airfields/{airfield:code}/stands', 'Admin\\StandAdminController@getStandsForAirfield');
        Route::get('/airfields/{airfield:code}/stands/{stand}', 'Admin\\StandAdminController@getStandDetails');
        Route::put('/airfields/{airfield:code}/stands/{stand}', 'Admin\\StandAdminController@modifyStand');
        Route::delete('/airfields/{airfield:code}/stands/{stand}', 'Admin\\StandAdminController@deleteStand');

        Route::get('/navaids', 'Admin\\NavaidAdminController@getNavaids');
        Route::get('/navaids/{navaid}', 'Admin\\NavaidAdminController@getNavaid');
        Route::put('/navaids/{navaid}', 'Admin\\NavaidAdminController@modifyNavaid');
        Route::delete('/navaids/{navaid}', 'Admin\\NavaidAdminController@deleteNavaid');
        Route::post('/navaids', 'Admin\\NavaidAdminController@createNavaid');

        Route::get('/stand-types', 'Admin\\StandAdminController@getTypes');
    });
});

Route::middleware('admin.github')->group(function () {
    Route::post('github', 'GithubController@processGithubWebhook');
});

// Routes that can be hit by anybody at all, mostly login and informational routes
Route::middleware('public')->group(function () {
    Route::get('/', function () {
        return response()->json(['message' => 'Welcome to the UK Controller Plugin API']);
    });

    // Aircraft
    Route::get('aircraft', 'AircraftController@getAllAircraft');
    Route::get('wake-category', 'AircraftController@getAllWakeCategories');
    Route::get('wake-category/dependency', 'AircraftController@getWakeCategoriesDependency');
    Route::get('wake-category/recat/dependency', 'AircraftController@getRecatCategoriesDependency');

    // Initial altitudes and sids
    Route::get('sid', 'SidController@getAllSids');
    Route::get('sid/{id}', 'SidController@getSid')
        ->where('id', '[0-9]+');
    Route::get('initial-altitude', 'SidController@getInitialAltitudeDependency');
    Route::get('handoffs', 'SidController@getSidHandoffsDependency');

    // Controller positions
    Route::get('controller', 'ControllerPositionController@getAllControllers');
    Route::get('controller-positions', 'ControllerPositionController@getControllerPositionsDependency');

    // Airfields
    Route::get('airfield', 'AirfieldController@getAllAirfields');
    Route::get('airfield/dependency', 'AirfieldController@getAirfieldDependency');
    Route::get('airfield-ownership', 'AirfieldController@getAirfieldOwnershipDependency');

    // Departures
    Route::get('departure/intervals/sid-groups/dependency', 'DepartureController@getDepartureSidIntervalGroupsDependency');

    // Holds
    Route::get('hold', 'HoldController@getAllHolds');
    Route::get('hold/assigned', 'HoldController@getAssignedHolds');

    // Handoffs
    Route::get('handoff', 'HandoffController@getAllHandoffs');

    // Prenotes
    Route::get('prenote', 'PrenoteController@getAllPrenotes');

    // Regional Pressure
    Route::get('regional-pressure', 'RegionalPressureController@getRegionalPressures');
    Route::get('altimeter-setting-region', 'RegionalPressureController@getAltimeterSettingRegions');

    // Minimum stack levels
    Route::get('msl', 'MinStackController@getAllMinStackLevels');
    Route::get('msl/airfield', 'MinStackController@getAirfieldMinStackLevels');
    Route::get('msl/tma', 'MinStackController@getTmaMinStackLevels');
    Route::get('msl/airfield/{icao}', 'MinStackController@getMslForAirfield')
        ->where('icao', '[A-Z]{4}');
    Route::get('msl/tma/{tma}', 'MinStackController@getMslForTma')
        ->where('tma', '[A-Z]{4}');

    // Standard Route Document
    Route::get('srd/route/search', 'SrdController@searchRoutes');

    // Navaids
    Route::get('navaid/dependency', 'NavaidController');

    // Enroute releases
    Route::get('release/enroute/types', 'ReleaseController@enrouteReleaseTypeDependency');

    // Sids
    Route::get('sid/dependency', 'SidController@getSidsDependency');

    // Stands
    Route::get('stand/status', 'StandController@getAirfieldStandStatus');
    Route::get('stand/dependency', 'StandController@getStandsDependency');
    Route::get('stand/assignment', 'StandController@getStandAssignments');
    Route::get('stand/assignment/{callsign}', 'StandController@getStandAssignmentForAircraft')
        ->where('callsign', VatsimCallsign::CALLSIGN_REGEX);

    // Wake categories
    Route::get('wake-schemes/dependency', 'WakeController@getWakeSchemesDependency');

    // Admin login
    Route::prefix('admin')->group(function () {
        Route::post('login', 'UserController@adminLogin');
    });
});
