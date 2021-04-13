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
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY', 'ukcpwebsocket'),
            'secret' => env('PUSHER_APP_SECRET', 'ukcpsecret'),
            'app_id' => env('PUSHER_APP_ID', 'ukcpwebsocket'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'encrypted' =>  env('APP_ENV') !== 'local',
                'host' => env('WEBSOCKET_BROADCAST_HOST', '127.0.0.1'),
                'port' => 6001,
                'scheme' => env('WEBSOCKET_ENV', 'https'),
                'curl_options' => [
                    CURLOPT_SSL_VERIFYHOST => env('APP_ENV') !== 'local' ? 2 : 0,
                    CURLOPT_SSL_VERIFYPEER => env('APP_ENV') !== 'local' ? 1 : 0,
                ],
            ],
        ],
        'plugin-events' => [
            'driver' => 'plugin-events',
        ]
    ],
];
