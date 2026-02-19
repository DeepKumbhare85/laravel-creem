<?php

use Clevision\Creem\Events\CheckoutCompleted;
use Clevision\Creem\Events\SubscriptionCanceled;
use Clevision\Creem\Events\SubscriptionPaid;
use Clevision\Creem\Events\DisputeCreated;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

function makeWebhookRequest(array $payload): array
{
    $raw    = json_encode($payload);
    $secret = config('creem.webhook_secret');
    $sig    = hash_hmac('sha256', $raw, $secret);

    return [$raw, $sig];
}

it('dispatches CheckoutCompleted event on checkout.completed webhook', function () {
    $payload = [
        'eventType' => 'checkout.completed',
        'customer'  => ['email' => 'buyer@example.com'],
        'product'   => ['id' => 'prod_123'],
    ];

    [$raw, $sig] = makeWebhookRequest($payload);

    $response = test()->postJson(
        route('creem.webhook'),
        $payload,
        ['creem-signature' => $sig, 'Content-Type' => 'application/json'],
    );

    $response->assertOk()->assertJson(['status' => 'ok']);
    Event::assertDispatched(CheckoutCompleted::class, fn($e) => $e->getCustomer()['email'] === 'buyer@example.com');
});

it('dispatches SubscriptionPaid event on subscription.paid webhook', function () {
    $payload = [
        'eventType'    => 'subscription.paid',
        'subscription' => ['id' => 'sub_abc'],
    ];

    [$raw, $sig] = makeWebhookRequest($payload);

    $response = test()->postJson(
        route('creem.webhook'),
        $payload,
        ['creem-signature' => $sig],
    );

    $response->assertOk();
    Event::assertDispatched(SubscriptionPaid::class);
});

it('dispatches SubscriptionCanceled on subscription.canceled webhook', function () {
    $payload = ['eventType' => 'subscription.canceled', 'subscription' => ['id' => 'sub_xyz']];

    [$raw, $sig] = makeWebhookRequest($payload);

    test()->postJson(route('creem.webhook'), $payload, ['creem-signature' => $sig])->assertOk();

    Event::assertDispatched(SubscriptionCanceled::class);
});

it('dispatches DisputeCreated on dispute.created webhook', function () {
    $payload = ['eventType' => 'dispute.created', 'dispute' => ['id' => 'dis_001']];

    [$raw, $sig] = makeWebhookRequest($payload);

    test()->postJson(route('creem.webhook'), $payload, ['creem-signature' => $sig])->assertOk();

    Event::assertDispatched(DisputeCreated::class);
});

it('returns 401 when creem-signature header is missing', function () {
    test()->postJson(route('creem.webhook'), ['eventType' => 'checkout.completed'])
        ->assertStatus(401);
});

it('returns 401 when creem-signature is invalid', function () {
    test()->postJson(
        route('creem.webhook'),
        ['eventType' => 'checkout.completed'],
        ['creem-signature' => 'invalid_signature'],
    )->assertStatus(401);
});

it('returns 200 ignored for unknown event types', function () {
    $payload = ['eventType' => 'unknown.event'];

    [$raw, $sig] = makeWebhookRequest($payload);

    test()->postJson(route('creem.webhook'), $payload, ['creem-signature' => $sig])
        ->assertOk()
        ->assertJson(['status' => 'unhandled']);
});
