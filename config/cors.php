<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | NOTE: the active CORS layer is App\Http\Middleware\Cors (registered
    | first in App\Http\Kernel::$middleware). This file is NOT wired to any
    | provider/middleware (barryvdh/laravel-cors HandleCors is not registered)
    | and is kept only for reference. Keep the values below in sync with the
    | middleware if this file is ever wired up.
    |
    */

    'paths' => ['api/*'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'Origin', 'X-Auth-Token', 'X-CSRF-TOKEN'],
    'exposed_headers' => [],
    'max_age' => 86400,
    'supports_credentials' => false,

];
