<?php

use App\Log\LoggerFactory;
use App\Log\NullLoggerFactory;

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
        'null_logger' => [
            'driver' => 'custom',
            'via' => NullLoggerFactory::class,
            'name' => 'NullLogger',
        ],
        'bugsnag' => [
            'driver' => 'bugsnag'
        ]
    ],
];
