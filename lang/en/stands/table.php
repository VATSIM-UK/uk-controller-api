<?php

return [
    'columns' => [
        'id' => 'Id',
        'airfield' => 'Airfield',
        'terminal' => 'Terminal',
        'identifier' => 'Identifier',
        'airlines' => 'Airlines',
        'used' => 'Used for Allocation',
        'priority' => 'Allocation Priority',
        'allocation' => 'Allocated To',
    ],
    'airlines' => [
        'description' => 'Airlines can be assigned to specific stands based on various parameters.',
        'columns' => [
            'icao' => 'ICAO Code',
            'destination' => 'Destination',
            'callsign' => 'Callsign Slug',
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
