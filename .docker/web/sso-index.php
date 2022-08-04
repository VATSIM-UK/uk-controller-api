<?php

$uri = urldecode($_SERVER['REQUEST_URI']);
$method = urldecode($_SERVER['REQUEST_METHOD']);

if ($method === 'GET' && str_starts_with($uri, '/oauth/authorize')) {
    header('Location: ' . $_GET['redirect_uri'] . '?code=12345&state=' . $_GET['state']);
} else {
    if ($method === 'POST' && str_starts_with($uri, '/oauth/token')) {
        echo json_encode(
            [
                'access_token' => '123456',
            ]
        );
    } else {
        if ($method === 'GET' && str_starts_with($uri, '/api/user')) {
            echo json_encode(
                [
                    'data' => [
                        'cid' => '1234',
                        'name_full' => 'Test User',
                        'email' => 'test@vatsim.uk',
                        'name_first' => 'Test',
                        'name_last' => 'User',
                    ],
                ]
            );
        }
    }
}
