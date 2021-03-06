<?php

return [

    'default' => env('SHORT_MESSAGE_DRIVER', 'ghasedak'),

    /**
     * api-key: create it from https://developers.ghasedak.io/panel/apikeys
     * line-number: preferred line number to send sms from it.
     */
    'ghasedak' => [
        'base_url' => env("GHASEDAK_BASE_URL", "https://api.ghasedak.me"),
        'api-key' => env('GHASEDAK_API_KEY'),
        'line-number' => env('GHASEDAK_LINE_NUMBER'),
    ],

    'rahyab' => [
        'username' => env('RAHYAB_USERNAME'),
        'password' => env('RAHYAB_PASSWORD'),
        'from' => env('RAHYAB_FROM'),
    ],

    'smsir' => [
        'default' => 'white',
        'white' => [
            'api-key' => env('WHITE_SMSIR_API_KEY'),
            'secret-key' => env('WHITE_SMSIR_SECRET_KEY'),
            'contacts_group_id' => null,
        ],
        'mass' => [
            'api-key' => env('MASS_SMSIR_API_KEY'),
            'secret-key' => env('MASS_SMSIR_SECRET_KEY'),
            'line-number' => env('MASS_SMSIR_LINE_NUMBER'),
        ],
    ],

    'log' => [
        'channel' => env('SHORT_MESSAGE_LOG', 'stack'),
    ],

];
