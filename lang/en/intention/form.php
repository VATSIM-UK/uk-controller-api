<?php

return [
    'code_spec' => [
        'label' => 'Code Specification',
    ],
    'priority' => [
        'label' => 'Priority',
    ],
        'description' => [
        'label' => 'Description',
        'helper' => 'Description of the intention code.',
    ],
    'order_type' => [
        'label' => 'Type',
        'helper' => 'The method to determine order.',
    ],
    'position' => [
        'label' => 'Position',
        'helper' => 'Integer position in the priority.',
    ],
    'before_after_position' => [
        'label' => 'Position',
        'helper' => 'The position in the order to insert relative to.',
    ],
    'code_type' => [
        'label' => 'Code Type',
        'helper' => 'Can either be a set code, or the last two characters of the airfield identifier.'
    ],
    'single_code' => [
        'label' => 'Single Code',
        'helper' => 'For example, D1.'
    ],
    'conditions' => [
        'conditions' => [
            'label' => 'Conditions',
            'helper' => 'The conditions that must be met for this code to apply.',
        ],
        'arrival_airfields' => [
            'menu_item' => 'Arrival Airfields',
            'repeater_label' => 'Arrival Airfields',
            'label' => 'Airfield',
            'helper' => 'A full arrival airfield ICAO code.',
        ],
        'arrival_airfield_pattern' => [
            'menu_item' => 'Arrival Airfield Pattern',
            'label' => 'Airfield Pattern',
            'helper' => 'A full or partial arrival airfield ICAO code.',
        ],
        'exit_point' => [
            'menu_item' => 'FIR Exit Point',
            'label' => 'Exit Point',
            'helper' => 'The place where the aircraft leaves the FIR. May be internal. In some cases, EGTT positions are shown intention codes based on where the aircraft leaves EGPX.',
        ],
        'maximum_cruising_level' => [
            'menu_item' => 'Maximum Cruising Altitude',
            'label' => 'Maximum Crusing Altitude',
            'helper' => 'The cruising altitude at or below which the aircraft must be cruising.',
        ],
        'cruising_level_above' => [
            'menu_item' => 'Cruising Altitude Above',
            'label' => 'Cruising Altitude Above',
            'helper' => 'The cruising altitude above which the aircraft must be cruising.',
        ],
        'routing_via' => [
            'menu_item' => 'Routing Via',
            'label' => 'Routing Via',
            'helper' => 'A waypoint that the aircraft must be routing via.',
        ],
        'controller_position_starts_with' => [
            'menu_item' => 'Controller Position Starts With',
            'label' => 'Controller Position Starts With',
            'helper' => 'The start of a controller callsign that this code applies to, e.g. EGP to cover all EGPX airfields.',
        ],
        'not' => [
            'menu_item' => 'Not',
        ],
        'any_of' => [
            'menu_item' => 'Any Of',
        ],
        'all_of' => [
            'menu_item' => 'All Of',
        ],
    ],
];
