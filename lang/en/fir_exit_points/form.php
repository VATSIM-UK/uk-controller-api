<?php

return [
    'exit_point' => [
        'label' => 'Exit Point',
        'helper' => 'The point at which aircraft exit the FIR',
    ],
    'internal' => [
        'label' => 'Internal Exit Point',
        'helper' => 'Internal exit points are between EGPX and EGTT, where a special intention code (e.g. Scottish specific) may be applicable.',
    ],
    'exit_cone' => [
        'heading' => 'Exit Cone',
        'description' => 'To determine whether an aircraft is actually exiting the FIR at a given point, an "exit cone" is used. If the aircrafts onward heading from the exit fix is between the two values (clockwise), then it is deemed to be exiting the FIR.',
    ],
    'exit_direction_start' => [
        'label' => 'Exit Heading Start',
        'helper' => 'The starting heading for the exit cone.',
    ],
    'exit_direction_end' => [
        'label' => 'Exit Heading End',
        'helper' => 'The end heading for the exit cone.',
    ],
];
