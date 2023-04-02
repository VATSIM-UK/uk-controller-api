<?php

return [
    'columns' => [
        'airfield' => 'Airfield',
        'description' => 'Description',
        'airlines' => 'Number of Assigned Airlines',
    ],
    'airlines' => [
        'description' => 'Airlines can be assigned to specific terminals based on various parameters.',
        'columns' => [
            'icao' => 'ICAO Code',
            'destination' => 'Origin',
            'callsign' => 'Callsign Slug',
            'priority' => 'Allocation Priority',
        ],
    ],
];
