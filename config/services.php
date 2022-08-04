<?php

return [
    'vatsim_uk_core' => [
        'sso_base' => env('CORE_SSO_BASE_URL', 'https://vatsim.uk'),
        'client_id' => env('CORE_SSO_CLIENT_ID'),
        'client_secret' => env('CORE_SSO_CLIENT_SECRET')
    ]
];
