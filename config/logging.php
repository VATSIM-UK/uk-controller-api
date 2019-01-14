<?php

use App\Log\LoggerFactory;

return [
    'default' => env('LOG_CHANNEL', 'stack'),
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['bugsnag', 'custom'],
        ],
        'custom' => [
            'driver' => 'custom',
            'via' => LoggerFactory::class,
            'name' => 'UKCP',
        ],
        'null' => [
            'driver' => 'custom',
            'via' => \App\Log\NullLoggerFactory::class,
            'name' => 'NULL',
        ],
        'bugsnag' => [
            'driver' => 'bugsnag'
        ]
    ],
];
