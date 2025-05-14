<?php

return [
    'airfield' => [
        'label' => 'Airfield',
        'helper' => 'The airfield with which this area is associated.',
    ],
    'name' => [
        'label' => 'Description',
        'helper' => 'A short description of what the area covers.',
    ],
    'source' => [
        'label' => 'Source',
        'helper' => 'The information source for this area (ID of relevant NOTAM, AIP SUP, etc.).',
    ],
    'coordinates' => [
        'label' => 'Coordinates',
        'helper' => 'The polygon defining this area, in "sline" format. Separate distinct polygons with blank lines.',
    ],
    'start_date' => [
        'label' => 'Start date',
        'helper' => 'The time from which the area should be displayed. If blank, the area will be active immediately.',
    ],
    'end_date' => [
        'label' => 'End date',
        'helper' => 'The time until which the area should be displayed. If blank, the area will be active indefinitely.',
    ],
];
