<?php

use App\Log\LoggerFactory;

return [
    'default' => env('LOG_CHANNEL', 'custom'),
    'channels' => [
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
    ],
];
