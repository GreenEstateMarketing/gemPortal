<?php

use App\Models\User;

return [
    'stripe' => [
        'model'  => User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'currency' => env('PAYMENT_DEFAULT_CURRENCY', 'USD'),

    'return_url_after_payment' => env('PAYMENT_RETURN_URL_AFTER_PAYMENT', '/'),
];
