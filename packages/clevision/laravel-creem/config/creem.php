<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Creem API Key
    |--------------------------------------------------------------------------
    |
    | Your Creem API key. You can find this in your Creem dashboard under
    | Settings > API Keys. In test mode, use your test API key.
    |
    */
    'api_key' => env('CREEM_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Creem Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The webhook secret used to verify webhook signatures. You can find this
    | in your Creem dashboard under Developers > Webhooks.
    |
    */
    'webhook_secret' => env('CREEM_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    |
    | When test mode is enabled, requests will be sent to the Creem test API
    | (https://test-api.creem.io). Set CREEM_TEST_MODE=true in your .env
    | file during development and testing.
    |
    */
    'test_mode' => env('CREEM_TEST_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | API Base URLs
    |--------------------------------------------------------------------------
    |
    | The base URLs for the Creem API. You should not need to change these
    | unless Creem updates their API endpoints.
    |
    */
    'api_url' => env('CREEM_API_URL', 'https://api.creem.io'),
    'test_api_url' => env('CREEM_TEST_API_URL', 'https://test-api.creem.io'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Route
    |--------------------------------------------------------------------------
    |
    | The URI where Creem will send webhook events. Register this URL in your
    | Creem dashboard under Developers > Webhooks.
    |
    */
    'webhook_route' => env('CREEM_WEBHOOK_ROUTE', 'creem/webhook'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Route Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware to apply to the webhook route. The VerifyCreemSignature
    | middleware is always applied automatically.
    |
    */
    'webhook_middleware' => [],

    /*
    |--------------------------------------------------------------------------
    | HTTP Timeout
    |--------------------------------------------------------------------------
    |
    | The number of seconds to wait for a response from the Creem API before
    | timing out. Increase this if you experience timeout issues.
    |
    */
    'timeout' => env('CREEM_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automatic retries for failed API requests. Set 'retries' to 0
    | to disable retrying. 'retry_delay' is in milliseconds.
    |
    */
    'retries' => env('CREEM_RETRIES', 3),
    'retry_delay' => env('CREEM_RETRY_DELAY', 500),

];
