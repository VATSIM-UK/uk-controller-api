<?php

return [
    'fieldset_identifiers' => [
        'label' => 'Identifiers',
    ],
    'code' => [
        'label' => 'Code',
        'helper' => 'The ICAO code, e.g. EGLL',
    ],
    'latitude' => [
        'label' => 'Latitude',
        'helper' => 'In decimal degrees',
    ],
    'longitude' => [
        'label' => 'Longitude',
        'helper' => 'In decimal degrees',
    ],
    'elevation' => [
        'label' => 'Elevation (ft)',
        'helper' => 'Above mean sea level',
    ],
    'wake_scheme' => [
        'label' => 'Wake Turbulence Scheme',
        'helper' => 'Which scheme to use for departure and arrival sequencing. e.g. UK, RECAT',
    ],
    'fieldset_altimetry' => [
        'label' => 'Altimetry',
    ],
    'transition_altitude' => [
        'label' => 'Transition Altitude (ft)',
        'helper' => 'Above mean sea level',
    ],
    'standard_high' => [
        'label' => 'Stand pressure is high pressure',
        'helper' => 'A QNH of 1013 may be considered high or low pressure, depending on the airfield in question',
    ],
    'fieldset_handoff' => [
        'label' => 'handoff',
    ],
];
