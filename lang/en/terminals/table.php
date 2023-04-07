<?php

return [
    'columns' => [
        'airfield' => 'Airfield',
        'description' => 'Description',
        'airlines' => 'Number of Assigned Airlines',
    ],
    'airlines' => [
        'description' => 'Airlines can be assigned to specific terminals based on various parameters. The allocation ' .
            'tries to match by Callsign Slug, then by Destination (always taking priority into account), then any ' .
            'stand for that airline (preferring ones without specific callsigns or destinations).',
        'columns' => [
            'icao' => 'ICAO Code',
            'destination' => 'Origin',
            'callsign' => 'Callsign Slug',
            'priority' => 'Allocation Priority',
        ],
    ],
];
