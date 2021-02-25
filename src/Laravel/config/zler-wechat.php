<?php
return [
    'router' => [
        'prefix' => '/zler/wechat',
    ],
    'gzh' => [
        'app_id' => env('gzh_app_id', ''),
        'app_secret' => env('gzh_app_secret', ''),
        'token' => env('gzh_token', ''),
        'access_token_path' => '',
        'js_ticket_path' => ''
    ]
];