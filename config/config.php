<?php


return [

    'default' => env('SHORT_MESSAGE_DRIVER', 'ghasedak'),

    'ghasedak' => [
        'api-key' => env('GHASEDAK_API_KEY')
    ],

    'smsir' => [
        'default' => 'white',
        'white' => [
            'api-key' => env('WHITE_SMSIR_API_KEY'),
            'secret-key' => env('WHITE_SMSIR_SECRET_KEY'),
            'contacts_group_id' => null
        ],
        'mass' => [
            'api-key' => env('MASS_SMSIR_API_KEY'),
            'secret-key' => env('MASS_SMSIR_SECRET_KEY'),
            'line-number' => env('MASS_SMSIR_LINE_NUMBER')
        ]
    ],

    'log' => [
        'channel' => env('SHORT_MESSAGE_LOG', 'stack')
    ]

];
