<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => 'api',
        'passwords' => 'admins',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [

        // Untuk panel admin berbasis session (jika nanti ada)
        'web' => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],

        // Untuk API JWT
        'api' => [
            'driver'   => 'jwt',
            'provider' => 'admins',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [

        'admins' => [
            'provider' => 'admins',
            'table'    => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire'   => 60,
            'throttle' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
