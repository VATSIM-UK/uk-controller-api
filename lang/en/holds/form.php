<?php

return [
    'parameters' => [
        'label' => 'Parameters',
    ],
    'description' => [
        'label' => 'Description',
    ],
    'inbound_heading' => [
        'label' => 'Inbound heading',
        'helper' => 'The heading inbound to the holding fix, use 360 for North.',
    ],
    'minimum_altitude' => [
        'label' => 'Minimum altitude',
        'helper' => 'The minimum altitude (in feet) that the published hold covers. Minimum is 1000.',
    ],
    'maximum_altitude' => [
        'label' => 'Maximum altitude',
        'helper' => 'The maximum altitude (in feet) that the published hold covers. Minimum is 2000.',
    ],
    'turn_direction' => [
        'label' => 'Turn direction'
    ],
    'restrictions' => [
        'label' => 'Restrictions'
    ],
    'add_restriction' => [
        'label' => 'Add restriction',
    ],
    'minimum_level' => [
        'label' => 'Minimum Level',
    ],
    'minimum_level_level' => [
        'label' => 'Level',
    ],
    'minimum_level_target' => [
        'label' => 'Target',
        'helper' => 'Which airfields MSL to use when determining the minimum holding level.',
    ],
    'minimum_level_override' => [
        'label' => 'Override',
        'helper' => 'The overriding minimum level, regardless of what the minimum stack level is.',
    ],
    'minimum_level_runway' => [
        'label' => 'Runway',
        'helper' => 'Which runway must be in use for this restriction to apply.',
    ],
    'level_block' => [
        'label' => 'Blocked Level',
    ],
    'level_block_levels' => [
        'label' => 'Blocked Levels',
        'helper' => 'Each item should be one level that is blocked.',
    ],
];
