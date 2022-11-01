<?php

return [
    'ccams' => [
        'first' => [
            'label' => 'First Squawk in Range',
            'helper' => 'The first squawk in the squawk range',
        ],
        'last' => [
            'label' => 'Last Squawk in Range',
            'helper' => 'The last squawk in the squawk range',
        ],
    ],
    'orcam' => [
        'first' => [
            'label' => 'First Squawk in Range',
            'helper' => 'The first squawk in the squawk range',
        ],
        'last' => [
            'label' => 'Last Squawk in Range',
            'helper' => 'The last squawk in the squawk range',
        ],
        'origin' => [
            'label' => 'Origin Airfield Pattern',
            'helper' => 'The pattern to match the origin airport against. Partial patterns are allowed. E.g. ED, LFR.',
        ],
    ],
    'non_assignable' => [
        'code' => [
            'label' => 'Code',
            'helper' => 'The banned squawk code.',
        ],
        'description' => [
            'label' => 'Description',
        ],
    ],
    'airfield_pairs' => [
        'first' => [
            'label' => 'First Squawk in Range',
            'helper' => 'The first squawk in the squawk range',
        ],
        'last' => [
            'label' => 'Last Squawk in Range',
            'helper' => 'The last squawk in the squawk range',
        ],
        'origin' => [
            'label' => 'Origin Airfield Pattern',
            'helper' => 'The pattern to match the origin airfield against. Partial patterns are allowed. E.g. ED, LFR.',
        ],
        'destination' => [
            'label' => 'Destination Airfield Pattern',
            'helper' => 'The pattern to match the destination airfield against. Partial patterns are allowed. E.g. ED, LFR.',
        ],
    ],
    'unit_discrete' => [
        'first' => [
            'label' => 'First Squawk in Range',
            'helper' => 'The first squawk in the squawk range',
        ],
        'last' => [
            'label' => 'Last Squawk in Range',
            'helper' => 'The last squawk in the squawk range',
        ],
        'unit' => [
            'label' => 'Unit',
            'helper' => 'The unit that owns the range, e.g. EGKK, SCO, THAMES',
        ],
        'rule_type' => [
            'label' => 'Rule',
            'helper' => 'The rule to apply to determine whether or not the range is relevant.',
        ],
        'rule_flight_rules' => [
            'label' => 'Flight Rules',
            'helper' => 'Which flight rules this range applies to.',
        ],
        'rule_unit_type' => [
            'label' => 'Position Type',
            'helper' => 'The position type that can use this squawk. Useful for specifying that squawks are reserved for tower positions etc.',
        ],
    ],
    'unit_discrete_guests' => [
        'primary' => [
            'label' => 'Owning Unit',
            'helper' => 'The unit that owns the range, e.g. EGSS',
        ],
        'guest' => [
            'label' => 'Guest Unit',
            'helper' => 'The unit that inherits the range, e.g. ESSEX',
        ],
    ],
];
