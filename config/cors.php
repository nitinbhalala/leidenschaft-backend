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
        'https://0ea5-2405-201-2024-aa28-105c-a8ee-1939-11f1.ngrok-free.app'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
