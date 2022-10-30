<?php

return [
    'assignments' => [
        'columns' => [
            'callsign' => 'Callsign',
            'code' => 'Code',
            'type' => 'Assignment Type',
        ],
    ],
    'ccams' => [
        'columns' => [
            'first' => 'First Squawk in Range',
            'last' => 'Last Squawk in Range',
        ],
    ],
    'orcam' => [
        'columns' => [
            'first' => 'First Squawk in Range',
            'last' => 'Last Squawk in Range',
            'origin' => 'Origin Airfield Pattern',
        ],
    ],
    'non_assignable' => [
        'columns' => [
            'code' => 'Code',
            'description' => 'Description',
        ],
    ],
    'airfield_pairs' => [
        'columns' => [
            'first' => 'First Squawk in Range',
            'last' => 'Last Squawk in Range',
            'origin' => 'Origin Airfield Pattern',
            'destination' => 'Destination Airfield Pattern',
        ],
    ],
];
