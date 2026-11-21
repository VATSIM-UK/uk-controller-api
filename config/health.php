<?php

use Spatie\Health\ResultStores\InMemoryHealthResultStore;

return [
    'result_stores' => [
        InMemoryHealthResultStore::class => [],
    ],

    'notifications' => [
        'enabled' => false,
    ],
];
