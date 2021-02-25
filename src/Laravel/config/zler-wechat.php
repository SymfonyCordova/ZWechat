<?php
return [
    'router' => [
        'prefix' => '/zler/wechat',
    ],
    'gzh' => [
        'app_id' => env('gzh_app_id', ''),
        'app_secret' => env('gzh_app_secret', ''),
        'token' => env('gzh_token', ''),
        'access_token_path' => public_path('vendor/zler-wechat').'/access_token.txt',
        'js_ticket_path' => public_path('vendor/zler-wechat').'/js_ticket.txt',
    ]
];