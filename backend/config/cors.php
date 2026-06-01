<?php declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Inertia apps are same-origin by default — CORS is only required for
    | standalone API consumers (mobile apps, third-party clients).
    |
    | Set CORS_ALLOWED_ORIGINS in production to your exact domain(s).
    | Multiple origins: "https://app.example.com,https://api.example.com"
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(
        explode(',', (string) env('CORS_ALLOWED_ORIGINS', env('APP_URL', '')))
    ),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
