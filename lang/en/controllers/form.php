<?php

return [
    'callsign' => [
        'label' => 'Callsign',
        'helper' => 'e.g. EGLL_S_TWR'
    ],
    'frequency' => [
        'label' => 'Frequency',
        'helper' => 'The full, six digit, frequency. E.g. 129.425'
    ],
    'requests_departure_releases' => [
        'label' => 'Request departure releases',
        'helper' => 'Can this controller send a departure release request to another controller',
    ],
    'receives_departure_releases' => [
        'label' => 'Receive departure releases',
        'helper' => 'Can this controller receive a departure release request from another controller',
    ],
    'sends_prenotes' => [
        'label' => 'Send prenotes',
        'helper' => 'Can this controller send a prenote message to another controller',
    ],
    'receives_prenotes' => [
        'label' => 'Receive prenotes',
        'helper' => 'Can this controller receive a prenote message from another controller',
    ],
];
