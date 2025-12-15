<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Driver
    |--------------------------------------------------------------------------
    |
    | Pilih driver untuk mengirim notifikasi WhatsApp:
    | - 'baileys': Menggunakan Baileys Node.js service (gratis, self-hosted)
    | - 'fontee': Menggunakan Fontee API (berbayar, lebih stabil)
    | - 'disabled': Nonaktifkan notifikasi WhatsApp
    |
    */
    'driver' => env('WHATSAPP_DRIVER', 'baileys'),

    /*
    |--------------------------------------------------------------------------
    | Baileys Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Baileys Node.js service.
    |
    */
    'baileys' => [
        'base_url' => env('BAILEYS_SERVICE_URL', 'http://localhost:3001'),
        'api_key' => env('BAILEYS_API_KEY', 'omahban-wa-secret-2024'),
        'timeout' => env('BAILEYS_TIMEOUT', 30),
        'retry_attempts' => env('BAILEYS_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Owner Phone Number
    |--------------------------------------------------------------------------
    |
    | Nomor WhatsApp owner untuk menerima notifikasi penting.
    | Format: 62xxx (tanpa +)
    |
    */
    'owner_phone' => env('WHATSAPP_OWNER_PHONE', '6282227863969'),

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        // Kirim notifikasi untuk stok rendah
        'low_stock' => env('WHATSAPP_NOTIFY_LOW_STOCK', true),
        
        // Kirim notifikasi untuk transaksi besar (di atas threshold)
        'large_transaction' => env('WHATSAPP_NOTIFY_LARGE_TRANSACTION', true),
        'large_transaction_threshold' => env('WHATSAPP_LARGE_TRANSACTION_THRESHOLD', 1000000),
        
        // Kirim laporan harian
        'daily_report' => env('WHATSAPP_NOTIFY_DAILY_REPORT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bot Signature / Footer
    |--------------------------------------------------------------------------
    |
    | Signature yang muncul di setiap pesan bot sebagai penanda.
    | Anda bisa custom ini sesuai kebutuhan.
    |
    */
    'signature' => [
        // Nama bot yang muncul di footer
        'bot_name' => env('WHATSAPP_BOT_NAME', '🤖 Bot Omah Ban POS'),
        
        // Garis pembatas sebelum signature
        'divider' => '━━━━━━━━━━━━━━━━━━━━━',
        
        // Teks footer (bisa pakai {bot_name} sebagai placeholder)
        'footer' => env('WHATSAPP_BOT_FOOTER', 'Pesan ini dikirim otomatis oleh {bot_name}. Jangan balas pesan ini.'),
        
        // Tampilkan timestamp
        'show_timestamp' => true,
        
        // Format timestamp
        'timestamp_format' => 'd M Y, H:i',
    ],
];

