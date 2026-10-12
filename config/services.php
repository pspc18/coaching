<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'paytm' => [
        'env' => env('PAYTM_ENVIRONMENT'), // values : (staging | production)
        'merchant_id' => env('PAYTM_MERCHANT_ID'),
        'merchant_key' => env('PAYTM_MERCHANT_KEY'),
        'merchant_website' => env('PAYTM_MERCHANT_WEBSITE'),
        'channel' => env('PAYTM_CHANNEL'),
        'industry_type' => env('PAYTM_INDUSTRY_TYPE'),
    ],
    'phonepe' => [
        'env' => env('PHONEPE_ENV', 'UAT'), // UAT or PRODUCTION
        'merchant_id' => env('PHONEPE_MERCHANT_ID', 'M23LDDO4IORWN'),
        'salt_key' => env('PHONEPE_SALT_KEY', '96434309-7796-489d-8924-ab56988a6076'),
        'salt_index' => env('PHONEPE_SALT_INDEX', '1'),
        'client_id' => env('PHONEPE_CLIENT_ID', 'M23LDDO4IORWN_2602151310'),
        'client_secret' => env('PHONEPE_CLIENT_SECRET', 'OWQzYzkwMzQtZWQwMy00ODlhLWE3ZDItZmQwY2M1ZmU5Yzlh'),
        'client_version' => env('PHONEPE_CLIENT_VERSION', '1'),
        'uat_base_url' => env('PHONEPE_UAT_BASE_URL', 'https://api-preprod.phonepe.com/apis/pg-sandbox'),
        'prod_base_url' => env('PHONEPE_PROD_BASE_URL', 'https://api.phonepe.com/apis/pg'),
    ],

];
