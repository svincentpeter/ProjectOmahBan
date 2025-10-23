<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Merchant ID
    |--------------------------------------------------------------------------
    |
    | Merchant ID dari Midtrans Dashboard
    |
    */
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    |
    | Client Key digunakan untuk Snap Payment (frontend/JavaScript)
    |
    */
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    |
    | Server Key digunakan untuk API request (backend/PHP)
    | JANGAN share ke publik atau commit ke Git!
    |
    */
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Production Mode
    |--------------------------------------------------------------------------
    |
    | Set to true untuk Production (live payment)
    | Set to false untuk Sandbox (testing)
    |
    */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Sanitized Mode
    |--------------------------------------------------------------------------
    |
    | Auto sanitize input untuk mencegah XSS
    |
    */
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),

    /*
    |--------------------------------------------------------------------------
    | 3D Secure
    |--------------------------------------------------------------------------
    |
    | Enable 3D Secure untuk keamanan kartu kredit
    |
    */
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
];
