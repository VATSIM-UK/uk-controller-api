<?php

return [
    'columns' => [
        'airfield' => 'Airfield',
        'runway' => 'Runway',
        'identifier' => 'Identifier',
        'initial_altitude' => 'Initial Altitude',
    ],
    'prenotes' => [
        'columns' => [
            'description' => 'Prenote',
            'controllers' => 'Controllers',
        ],
        'attach_action' => [
            'trigger_button' => 'Assign Prenote',
            'confirm_button' => 'Assign',
            'modal_heading' => 'Assign prenote',
        ],
        'detach_action' => [
            'trigger_button' => 'Unassign Prenote',
            'confirm_button' => 'Unassign',
            'modal_heading' => 'Unassign prenote ":prenote"',
        ],
    ],
];
