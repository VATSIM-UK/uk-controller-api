<?php

return [
    'columns' => [
        'airfield' => 'Airfield',
        'terminal' => 'Terminal',
        'identifier' => 'Identifier',
        'max_wtc' => 'Max UK WTC',
        'max_size' => 'Max Aircraft Size',
        'airlines' => 'Airlines',
        'used' => 'Used for Allocation',
        'priority' => 'Allocation Priority',
    ],
    'airlines' => [
        'description' => 'Airlines can be assigned to specific stands based on various parameters. See the allocation guide
        for more details.',
        'columns' => [
            'icao' => 'ICAO Code',
            'aircraft' => 'Aircraft Type',
            'destination' => 'Origin',
            'full_callsign' => 'Callsign',
            'callsign_slug' => 'Callsign Slug',
            'priority' => 'Allocation Priority',
            'not_before' => 'Not Before [UTC]',
        ],
    ],
    'paired' => [
        'description' => 'Stands that are paired cannot be simultaneously assigned to aircraft. ' .
            'Note, this does not prevent aircraft from spawning up on a stand!',
        'columns' => [
            'id' => 'Id',
            'airfield' => 'Airfield',
            'identifier' => 'Identifier',
        ],
    ],
];
