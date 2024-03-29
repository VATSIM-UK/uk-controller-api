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
    'unit_discrete' => [
        'columns' => [
            'unit' => 'Owning Unit',
            'first' => 'First Squawk in Range',
            'last' => 'Last Squawk in Range',
        ],
    ],
    'unit_discrete_guests' => [
        'columns' => [
            'primary_unit' => 'Owning Unit',
            'guest_unit' => 'Guest Unit',
        ],
    ],
    'unit_conspicuity' => [
        'columns' => [
            'unit' => 'Owning Unit',
            'code' => 'Squawk Code',
        ],
    ],
];
