<?php

return [
    'runway' => [
        'label' => 'Airfield and Runway',
        'helper' => 'Which runway this SID is for.',
    ],
    'identifier' => [
        'label' => 'Identifier',
        'helper' => 'The SIDs identifier, e.g. BADIM1X. Do not put deprecation markings, e.g. "#" here, the plugin handles these separately. Must be unique for a given runway.',
    ],
    'initial_altitude' => [
        'label' => 'Initial Altitude',
        'helper' => 'The initial altitude for the departure in feet.',
    ],
    'initial_heading' => [
        'label' => 'Initial Heading',
        'helper' => 'The initial heading for the departure. North should be entered as 360.',
    ],
    'handoff' => [
        'label' => 'Handoff Order',
        'helper' => 'The primary handoff order for the departure next frequency indicator.',
    ],
];
