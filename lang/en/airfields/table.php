<?php

return [
    'columns' => [
        'code' => 'ICAO Code',
        'transition' => 'Transition Altitude',
        'runways' => 'Runways',
        'top_down' => 'Top-down Order'
    ],
    'controller_positions' => [
        'columns' => [
            'order' => [
                'label' => '#',
            ],
            'callsign' => [
                'label' => 'Callsign',
            ],
            'frequency' => [
                'label' => 'Frequency',
            ],
        ],
        'attach_form' => [
            'insert_after' => [
                'label' => 'Insert After',
                'helper' => 'Position in the order to insert after, if not specified, will be inserted on the end.',
            ],
        ],
        'move_up_action' => [
            'label' => 'Move Up',
        ],
        'move_down_action' => [
            'label' => 'Move Down',
        ],
        'attach_action' => [
            'label' => 'Add Controller',
            'modal_heading' => 'Add Controller To Top-down',
            'modal_button' => 'Add Controller',
        ],
        'detach_action' => [
            'label' => 'Remove',
        ],
        'table' => [
            'title' => 'Controller Precedence',
            'description' => 'The order in which controllers take responsibility for controlling this airfield. This also determines the default handoff order for APP and above.',
        ],
    ],
];
