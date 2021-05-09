<?php

return [
    'access_token' => env('GITHUB_ACCESS_TOKEN', ''),
    'secret' => env('GITHUB_WEBHOOK_SECRET', ''),
    'api' => [
        'org' => env('GITHUB_ISSUE_ORG', ''),
        'repo' => env('GITHUB_ISSUE_REPO_API', ''),
        'label' =>  env('UKSF_LABEL_NAME_API', ''),
    ],
    'plugin' => [
        'org' => env('GITHUB_ISSUE_ORG', ''),
        'url' => env('GITHUB_ISSUE_REPO_PLUGIN', ''),
        'label' =>  env('UKSF_LABEL_NAME_PLUGIN', ''),
    ],
    'latest_release_assets_url' => 'https://github.com/VATSIM-UK/uk-controller-plugin/releases/download',
];
