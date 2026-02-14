<?php

use App\Http\Controllers\MissedApproachController;
use App\Http\Controllers\PluginLogController;
use App\Http\Controllers\PrenoteMessageController;
use App\Rules\VatsimCallsign;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MiddlewareKeys;

// Routes that the plugin user will use
Route::middleware('api')
    ->name('api.')
    ->group(
        function () {
            Route::middleware('plugin.user')
                ->name('plugin.')
                ->group(
                    function () {
                        Route::get(
                            '/authorise',
                            [
                                'middleware' => [
                                    'user.lastlogin',
                                ],
                                'uses' => 'TeapotController@normalTeapots',
                            ]
                        );

                        // Dependencies
                        Route::prefix('dependency')
                            ->name('dependency.')
                            ->group(function () {
                                Route::get('{id}', 'DependencyController@getDependency')
                                    ->name('get')
                                    ->where('id', '[0-9]+');

                                Route::get('', 'DependencyController@getAllDependencies')
                                    ->name('getall');
                            });

                        // Departure releases
                        Route::post('departure/release/request', 'DepartureReleaseController@makeReleaseRequest');
                        Route::patch(
                            'departure/release/request/{departureReleaseRequest}/approve',
                            'DepartureReleaseController@approveReleaseRequest'
                        );
                        Route::patch(
                            'departure/release/request/{departureReleaseRequest}/reject',
                            'DepartureReleaseController@rejectReleaseRequest'
                        );
                        Route::patch(
                            'departure/release/request/{departureReleaseRequest}/acknowledge',
                            'DepartureReleaseController@acknowledgeReleaseRequest'
                        );
                        Route::delete(
                            'departure/release/request/{departureReleaseRequest}',
                            'DepartureReleaseController@cancelReleaseRequest'
                        );

                        // Events
                        Route::get('plugin-events/sync', 'PluginEventsController@getLatestPluginEventId');
                        Route::get('plugin-events/recent', 'PluginEventsController@getRecentPluginEvents');

                        // Logs
                        Route::prefix('plugin')->group(
                            function () {
                                Route::prefix('logs')->controller(PluginLogController::class)->group(
                                    function () {
                                        Route::post('', 'createPluginLogEntry');
                                    }
                                );
                            }
                        );

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
                        Route::post('stand/assignment/requestauto', 'StandController@requestAutomaticStandAssignment');
                        Route::delete('stand/assignment/{callsign}', 'StandController@deleteStandAssignment')
                            ->where('callsign', VatsimCallsign::CALLSIGN_REGEX);

                        // Notifications
                        Route::get('notifications', 'NotificationController@getActiveNotifications');
                        Route::get('notifications/unread', 'NotificationController@getUnreadNotifications');
                        Route::put('notifications/read/{id}', 'NotificationController@readNotification')
                            ->where('id', '[0-9]+');

                        // Prenotes
                        Route::prefix('prenotes')->group(function () {
                            Route::prefix('messages')->group(function () {
                                Route::post('', [PrenoteMessageController::class, 'create']);
                                Route::prefix('{prenoteMessage}')->group(function () {
                                    Route::patch('acknowledge', [PrenoteMessageController::class, 'acknowledge']);
                                    Route::delete('', [PrenoteMessageController::class, 'delete']);
                                });
                            });
                        });

                        // Missed approaches
                        Route::prefix('missed-approaches')->group(function () {
                            Route::post('', [MissedApproachController::class, 'create'])
                                ->middleware(MiddlewareKeys::CONTROLLING_LIVE);
                            Route::patch(
                                '{missedApproachNotification}',
                                [MissedApproachController::class, 'acknowledge']
                            )
                                ->middleware(MiddlewareKeys::CONTROLLING_LIVE);
                        });
                    }
                );


            // Routes for user administration
            Route::middleware('admin.user')->group(
                function () {
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
                            'uses' => 'UserController@createUserWithPluginConfig',
                        ]
                    )->where('cid', '[0-9]+');

                    // Create without config
                    Route::post(
                        'user',
                        [
                            'uses' => 'UserController@createUser',
                        ]
                    );

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

                    // Get unread notifications for a user
                    Route::get(
                        'user/{cid}/notifications/unread',
                        [
                            'middleware' => MiddlewareKeys::VATSIM_CID,
                            'uses' => 'UserController@getUnreadNotificationsForUser',
                        ]
                    )->where('cid', '[0-9]+');

                    // Mark a notification as ready for a user
                    Route::put(
                        'user/{cid}/notifications/read/{notificationId}',
                        [
                            'middleware' => MiddlewareKeys::VATSIM_CID,
                            'uses' => 'UserController@markNotificationAsReadForUser',
                        ]
                    )->where('cid', '[0-9]+')->where('notificationId', '[0-9]+');
                    ;
                }
            );

            Route::middleware('admin.github')->group(
                function () {
                    Route::post('github', 'GithubController@processGithubWebhook');
                    Route::post('version', 'VersionController@createNewPluginVersion');
                }
            );

            // Routes that can be hit by anybody at all, mostly login and informational routes
            Route::middleware('public')
                ->name('public.')
                ->group(
                    function () {
                        Route::get(
                            '/',
                            function () {
                                return response()->json(['message' => 'Welcome to the UK Controller Plugin API']);
                            }
                        );

                        // Aircraft
                        Route::get('aircraft', 'AircraftController@getAllAircraft');
                        Route::get('wake-category', 'AircraftController@getAllWakeCategories');

                        // Controller positions
                        Route::get('controller', 'ControllerPositionController@getAllControllers');
                        Route::get(
                            'controller-positions-v2',
                            'ControllerPositionController@getControllerPositionsDependency'
                        );

                        // Airfields
                        Route::get('airfield', 'AirfieldController@getAllAirfields');
                        Route::get('airfield/dependency', 'AirfieldController@getAirfieldDependency');

                        // Departures
                        Route::get(
                            'departure/intervals/sid-groups/dependency',
                            'DepartureController@getDepartureSidIntervalGroupsDependency'
                        );

                        // Flight rules
                        Route::get('flight-rules/dependency', 'FlightRulesController@getFlightRulesDependency');

                        // Holds
                        Route::prefix('hold')->group(function () {
                            Route::get('', 'HoldController@getAllHolds');
                            Route::get('assigned', 'HoldController@getAssignedHolds');
                            Route::get('proximity', 'HoldController@getProximityHolds');
                        });

                        // Handoffs
                        Route::get('handoffs/dependency', 'HandoffController@getHandoffsV2Dependency');

                        // Prenotes
                        Route::get('prenotes/dependency', 'PrenoteController@getPrenotesV2Dependency');

                        // Regional Pressure
                        Route::get('regional-pressure', 'RegionalPressureController@getRegionalPressures');
                        Route::get('altimeter-setting-region', 'RegionalPressureController@getAltimeterSettingRegions');

                        // Metars
                        Route::get('metar', 'MetarController@getAllMetars');

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

                        Route::get('smr-areas', 'SmrAreaController@getFormattedSmrAreas');

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

                        // Getting version
                        Route::prefix('version')->group(function () {
                            Route::get('latest', 'VersionController@getLatestVersion');
                            Route::get('{version:version}', 'VersionController@getVersion');
                        });
                    }
                );
        }
    );
