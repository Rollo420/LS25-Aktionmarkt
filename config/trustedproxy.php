<?php

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

return [
    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Here you may specify the proxies that should be trusted by the application.
    | The `*` value will trust the calling IP address which is useful for
    | single proxy setups in Docker or cloud environments.
    |
    */
    'proxies' => env('TRUSTED_PROXIES', '*'),

    /*
    |--------------------------------------------------------------------------
    | Trusted Headers
    |--------------------------------------------------------------------------
    |
    | Which headers should be used to detect the client IP and protocol.
    |
    */
    // Combine the individual X-Forwarded constants (Symfony's Request doesn't
    // expose a HEADER_X_FORWARDED_ALL constant in this environment).
    'headers' => (
        SymfonyRequest::HEADER_X_FORWARDED_FOR |
        SymfonyRequest::HEADER_X_FORWARDED_HOST |
        SymfonyRequest::HEADER_X_FORWARDED_PROTO |
        SymfonyRequest::HEADER_X_FORWARDED_PORT |
        SymfonyRequest::HEADER_X_FORWARDED_PREFIX
    ),
];
