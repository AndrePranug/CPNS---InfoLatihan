<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pembayaran
    |--------------------------------------------------------------------------
    |
    | Fitur pembayaran dibangun provider-agnostic. Selama gateway belum
    | didaftarkan, biarkan PAYMENT_ENABLED=false agar paket berbayar ditolak
    | dengan pesan yang jelas. Isi PAYMENT_GATEWAY dengan midtrans/xendit
    | setelah akun dan adapter tersedia.
    |
    */

    'enabled' => env('PAYMENT_ENABLED', false),

    'gateway' => env('PAYMENT_GATEWAY', 'none'),

    'currency' => env('PAYMENT_CURRENCY', 'IDR'),
];
