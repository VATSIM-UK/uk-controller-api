<?php

return [
    'defaults' => [
        'guard' => 'web_admin',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
        'web_admin' => [
            'driver' => 'session',
            'provider' => 'admin',q
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User\User::class,
        ],
        'admin' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User\Admin::class,
        ]
    ]
];
