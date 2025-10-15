<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paystack Public Key
    |--------------------------------------------------------------------------
    |
    | Your Paystack public key from https://dashboard.paystack.com/#/settings/developer
    |
    */
    'public_key' => env('PAYSTACK_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Paystack Secret Key
    |--------------------------------------------------------------------------
    |
    | Your Paystack secret key from https://dashboard.paystack.com/#/settings/developer
    |
    */
    'secret_key' => env('PAYSTACK_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Paystack Payment URL
    |--------------------------------------------------------------------------
    |
    | Paystack API base URL
    |
    */
    'payment_url' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),

    /*
    |--------------------------------------------------------------------------
    | Merchant Email
    |--------------------------------------------------------------------------
    |
    | The merchant email address
    |
    */
    'merchant_email' => env('PAYSTACK_MERCHANT_EMAIL'),

];
