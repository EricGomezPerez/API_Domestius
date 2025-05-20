<?php
// auth-laravel-api/config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'v1/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['https://domestius2.vercel.app'], // Específicamente tu frontend Angular
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Cambiar a true para permitir cookies
];