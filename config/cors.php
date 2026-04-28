<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://leidenschaft-admin.vercel.app',
        'https://leidenschaft-frontend.vercel.app',
        'http://localhost:3000',
        'http://localhost:3001',
        'http://192.168.29.152:3000',
        'http://192.168.29.152:3001',
        'https://inquiries-equation-camcorder-gates.trycloudflare.com'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
