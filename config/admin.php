<?php

return [

    /*
    | Host the Super Admin is served from (e.g. admin.theweb.com).
    | Leave null locally so /admin/* works on any host.
    */
    'domain' => env('ADMIN_DOMAIN') ?: null,

    /* URL prefix for the admin area. */
    'path' => env('ADMIN_PATH', 'admin'),

    /* Used by seeders only. No default password on purpose. */
    'seed' => [
        'email' => env('ADMIN_SEED_EMAIL', 'admin@theweb.com'),
        'password' => env('ADMIN_SEED_PASSWORD'),
    ],

    /* Payment receipt storage (private disk, never public). */
    'receipts' => [
        'disk' => 'local',
        'directory' => 'receipts',
        'max_kb' => 5120,
        'mimes' => ['jpg', 'jpeg', 'png', 'pdf'],
    ],
];