<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Master switch
    |--------------------------------------------------------------------------
    | Matikan/aktifkan WA tanpa mengubah kode lain.
    */
    'enabled' => env('FONTEE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Credentials & Endpoint
    |--------------------------------------------------------------------------
    */
    'api_key' => env('FONTEE_API_KEY'),
    'channel_id' => env('FONTEE_CHANNEL_ID'),
    'base_url' => env('FONTEE_BASE_URL', 'https://api.fontee.id'),

    /*
    |--------------------------------------------------------------------------
    | Timeout & Retry (punya 2 nama untuk kompatibilitas)
    |--------------------------------------------------------------------------
    */
    'timeout' => (int) env('FONTEE_TIMEOUT', 30),
    'timeout_seconds' => (int) env('FONTEE_TIMEOUT', 30), // alias utk kode lama
    'retry_attempts' => (int) env('FONTEE_RETRY_ATTEMPTS', 3),
    'retry_delay_ms' => (int) env('FONTEE_RETRY_DELAY_MS', 1000),
    'retry_delay_seconds' => (int) env('FONTEE_RETRY_DELAY_SECONDS', 60),

    /*
    |--------------------------------------------------------------------------
    | Type: whatsapp | sms (jika nanti perlu SMS)
    |--------------------------------------------------------------------------
    */
    'type' => env('FONTEE_NOTIFICATION_TYPE', 'whatsapp'),
];
