<?php

use Clevision\Creem\Facades\Creem;

it('verifies a valid webhook signature', function () {
    $secret  = config('creem.webhook_secret');
    $payload = '{"eventType":"checkout.completed","customer":{"email":"test@example.com"}}';
    $sig     = hash_hmac('sha256', $payload, $secret);

    expect(Creem::verifyWebhookSignature($payload, $sig))->toBeTrue();
});

it('rejects an invalid webhook signature', function () {
    $payload = '{"eventType":"checkout.completed"}';

    expect(Creem::verifyWebhookSignature($payload, 'bad_signature'))->toBeFalse();
});

it('verifies a valid redirect signature', function () {
    $apiKey = config('creem.api_key');

    $params = [
        'checkout_id' => 'ch_123',
        'order_id'    => 'ord_456',
        'product_id'  => 'prod_789',
        'customer_id' => 'cust_001',
    ];

    ksort($params);
    $query     = http_build_query($params);
    $signature = hash_hmac('sha256', $query, $apiKey);

    $params['signature'] = $signature;

    expect(Creem::verifyRedirectSignature($params))->toBeTrue();
});

it('rejects a redirect signature with missing params', function () {
    $params = [
        'checkout_id' => 'ch_123',
        'product_id'  => 'prod_789',
        'signature'   => 'bad_signature',
    ];

    expect(Creem::verifyRedirectSignature($params))->toBeFalse();
});

it('excludes null values from redirect signature verification', function () {
    $apiKey = config('creem.api_key');

    // subscription_id is null for one-time payments — should be excluded
    $params = [
        'checkout_id'     => 'ch_123',
        'order_id'        => 'ord_456',
        'product_id'      => 'prod_789',
        'subscription_id' => null,   // excluded from signing
    ];

    $filtered = array_filter($params, fn($v) => ! is_null($v));
    ksort($filtered);
    $query     = http_build_query($filtered);
    $signature = hash_hmac('sha256', $query, $apiKey);

    $params['signature'] = $signature;

    expect(Creem::verifyRedirectSignature($params))->toBeTrue();
});
