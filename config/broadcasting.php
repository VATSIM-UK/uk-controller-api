<?php

return [
    'default' => env('BROADCAST_DRIVER', null),
    'connections' => [
        'redis' => [
            'driver' => 'redis',
            'client' => 'predis',
            'default' => [
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => env('REDIS_DATABASE', 0),
            ],
        ],
    ],
];
