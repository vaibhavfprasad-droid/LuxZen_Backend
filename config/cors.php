<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // IMPORTANT: Change '*' to the actual URL of your frontend website.
    // The browser will NOT allow a wildcard when 'supports_credentials' is true.
    // Use the URL from your frontend dev server (e.g., http://localhost:3000 for React,
    // http://localhost:8080 for Vue, or http://localhost:19006 for Expo Web)
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    //  CRITICAL: This MUST be true to allow Sanctum's cookie-based authentication.
    'supports_credentials' => true, // <== CHANGE THIS

];