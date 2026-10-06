<?php

return [

    'admin' => [

        'name' => env('ADMIN_PAYMENT_NAME', 'Marche Moderne'),

        'methods' => [
            [
                'provider' => 'airtel_money',
                'name' => 'Airtel Money',
                'transfer_number' => env('ADMIN_AIRTEL_NUMBER', ''),
            ],

            [
                'provider' => 'moov_money',
                'name' => 'Moov Money',
                'transfer_number' => env('ADMIN_MOOV_NUMBER', ''),
            ],
        ],

    ],

];
