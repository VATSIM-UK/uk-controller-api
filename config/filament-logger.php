<?php

use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\Dependencies\DependencyResource;
use App\Filament\Resources\PluginLogs\PluginLogResource;
use App\Filament\Resources\SquawkAssignments\SquawkAssignmentResource;

return [
    'activity_resource' => ActivityResource::class,

    'resources' => [
        'enabled' => true,
        'log_name' => 'Resource',
        'logger' => \Jacobtims\FilamentLogger\Loggers\ResourceLogger::class,
        'color' => 'success',
        'exclude' => [
            SquawkAssignmentResource::class,
            DependencyResource::class,
            PluginLogResource::class,
        ],
    ],

    'access' => [
        'enabled' => true,
        'logger' => \Jacobtims\FilamentLogger\Loggers\AccessLogger::class,
        'color' => 'danger',
        'log_name' => 'Access',
    ],

    'notifications' => [
        'enabled' => true,
        'logger' => \Jacobtims\FilamentLogger\Loggers\NotificationLogger::class,
        'color' => null,
        'log_name' => 'Notification',
    ],

    'models' => [
        'enabled' => true,
        'log_name' => 'Model',
        'color' => 'warning',
        'logger' => \Jacobtims\FilamentLogger\Loggers\ModelLogger::class,
        'register' => [
            //App\Models\User::class,
        ],
    ],

    'custom' => [
        // [
        //     'log_name' => 'Custom',
        //     'color' => 'primary',
        // ]
    ],
];
