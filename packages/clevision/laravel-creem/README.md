# Laravel Creem

[![Latest Version on Packagist](https://img.shields.io/packagist/v/clevision/laravel-creem.svg)](https://packagist.org/packages/clevision/laravel-creem)
[![Tests](https://img.shields.io/github/actions/workflow/status/clevision/laravel-creem/tests.yml?label=tests)](https://github.com/clevision/laravel-creem/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/clevision/laravel-creem.svg)](https://packagist.org/packages/clevision/laravel-creem)
[![PHP Version](https://img.shields.io/packagist/php-v/clevision/laravel-creem.svg)](https://packagist.org/packages/clevision/laravel-creem)
[![License](https://img.shields.io/packagist/l/clevision/laravel-creem.svg)](LICENSE)

The official **Laravel** package for [Creem](https://creem.io) — a Merchant of Record platform for SaaS and digital businesses. Accept one-time payments, manage subscriptions, **create products programmatically**, handle webhooks, validate license keys, and stay globally tax-compliant — all with a clean, Laravel-native API.

---

## Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Quick Start](#quick-start)
5. [Products — One-Time & Subscription](#products--one-time--subscription)
6. [Checkout Sessions](#checkout-sessions)
7. [Subscriptions](#subscriptions)
8. [Customers & Billing Portal](#customers--billing-portal)
9. [Orders (Transactions)](#orders-transactions)
10. [Discounts](#discounts)
11. [License Keys](#license-keys)
12. [Refunds](#refunds)
13. [Webhooks](#webhooks)
14. [Signature Verification](#signature-verification)
15. [Artisan Commands](#artisan-commands)
16. [Demo Dashboard](#demo-dashboard)
17. [Error Handling](#error-handling)
18. [Testing](#testing)
19. [Advanced Usage](#advanced-usage)

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | `^8.1` |
| Laravel | `^10.0 \| ^11.0 \| ^12.0` |
| Guzzle | `^7.0` |

---

## Installation

```bash
composer require clevision/laravel-creem
```

The package auto-discovers its service provider and facade via Laravel's package discovery.

### Publish the config file

```bash
php artisan vendor:publish --tag=creem-config
```

### Add environment variables

```dotenv
CREEM_API_KEY=creem_xxxxxxxxxxxxxxxxxxxx
CREEM_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxx
CREEM_TEST_MODE=true          # false in production
```

---

## Configuration

All options live in `config/creem.php`:

| Key | Env Variable | Default | Description |
|---|---|---|---|
| `api_key` | `CREEM_API_KEY` | — | Your Creem API key |
| `webhook_secret` | `CREEM_WEBHOOK_SECRET` | — | Webhook signing secret |
| `test_mode` | `CREEM_TEST_MODE` | `false` | Use test API endpoint |
| `webhook_route` | `CREEM_WEBHOOK_ROUTE` | `creem/webhook` | URI for webhook endpoint |
| `timeout` | `CREEM_TIMEOUT` | `30` | HTTP request timeout (seconds) |
| `retries` | `CREEM_RETRIES` | `3` | Auto-retry count on 5xx |
| `retry_delay` | `CREEM_RETRY_DELAY` | `500` | Retry delay (milliseconds) |

---

## Quick Start

### 1. Create a checkout session

```php
use Clevision\Creem\Facades\Creem;

public function checkout(Request $request)
{
    $session = Creem::createCheckout([
        'product_id'  => 'prod_xxxxxxxxxxxxxxxx',
        'request_id'  => (string) auth()->id(),   // your internal reference
        'success_url' => route('dashboard'),
        'customer'    => ['email' => auth()->user()->email],
        'metadata'    => [
            'user_id'  => auth()->id(),
            'plan'     => 'pro',
        ],
    ]);

    return redirect($session['checkout_url']);
}
```

Creem handles tax, currency conversion, receipts, and global compliance automatically.

---

## Products — One-Time & Subscription

Products can be created programmatically via the API or through the [Creem Dashboard](https://creem.io/dashboard).

### Create a one-time purchase product

```php
$product = Creem::createProduct([
    'name'         => 'Lifetime Access',
    'description'  => 'Unlimited access forever.',
    'price'        => 4900,       // Amount in cents → $49.00
    'currency'     => 'USD',
    'billing_type' => 'onetime',
    'tax_category' => 'saas',     // saas | digital-goods-service | ebooks
]);

echo $product['id'];   // prod_abc123
```

### Create a subscription (monthly)

```php
$product = Creem::createProduct([
    'name'           => 'Pro Plan',
    'description'    => 'Full access, billed monthly.',
    'price'          => 2900,          // $29.00 / month
    'currency'       => 'USD',
    'billing_type'   => 'recurring',
    'billing_period' => 'every-month', // every-week|every-month|every-3-months|every-year
    'tax_category'   => 'saas',
]);
```

### Create a subscription (yearly)

```php
$product = Creem::createProduct([
    'name'           => 'Pro Plan — Annual',
    'price'          => 29000,         // $290.00 / year
    'currency'       => 'USD',
    'billing_type'   => 'recurring',
    'billing_period' => 'every-year',
]);
```

### `createProduct` parameter reference

| Field | Type | Required | Description |
|---|---|---|---|
| `name` | string | Yes | Product display name |
| `price` | integer | Yes | Price in cents (min 100 = $1.00) |
| `currency` | string | Yes | ISO 4217 code: `USD`, `EUR`, `GBP`, etc. |
| `billing_type` | string | Yes | `onetime` or `recurring` |
| `billing_period` | string | Yes if `recurring` | `every-week`, `every-month`, `every-3-months`, `every-year` |
| `description` | string | No | Short description shown at checkout |
| `image_url` | string | No | Product image URL (PNG/JPG) |
| `tax_mode` | string | No | `inclusive` or `exclusive` (default) |
| `tax_category` | string | No | `saas`, `digital-goods-service`, `ebooks` |
| `default_success_url` | string | No | Default redirect URL after purchase |
| `custom_fields` | array | No | Extra fields collected at checkout (max 3) |
| `abandoned_cart_recovery_enabled` | boolean | No | Enable abandoned cart recovery emails |

---

## Checkout Sessions

```php
```

### 2. Verify the redirect signature

After payment, Creem redirects to your `success_url` with query parameters. Always verify the signature:

```php
use Clevision\Creem\Facades\Creem;

public function success(Request $request)
{
    if (! Creem::verifyRedirectSignature($request->query())) {
        abort(403, 'Invalid signature.');
    }

    $orderId        = $request->query('order_id');
    $subscriptionId = $request->query('subscription_id');
    $customerId     = $request->query('customer_id');

    // Grant access, update database, etc.
}
```

### 3. Handle webhooks

Register the webhook URL in your [Creem Dashboard](https://creem.io/dashboard/developers):

```
https://your-app.com/creem/webhook
```

Run the helper command for step-by-step instructions:

```bash
php artisan creem:webhook-secret
```

Then listen for events in your `EventServiceProvider` (or listeners auto-discovery):

```php
// app/Providers/EventServiceProvider.php

use Clevision\Creem\Events\CheckoutCompleted;
use Clevision\Creem\Events\SubscriptionPaid;
use Clevision\Creem\Events\SubscriptionCanceled;

protected $listen = [
    CheckoutCompleted::class  => [GrantProductAccess::class],
    SubscriptionPaid::class   => [RenewSubscriptionAccess::class],
    SubscriptionCanceled::class => [RevokeProductAccess::class],
];
```

Example listener:

```php
namespace App\Listeners;

use Clevision\Creem\Events\CheckoutCompleted;
use App\Models\User;

class GrantProductAccess
{
    public function handle(CheckoutCompleted $event): void
    {
        $metadata = $event->getMetadata();
        $customer = $event->getCustomer();

        $user = User::find($metadata['user_id'] ?? null);
        $user?->grantAccess($event->getProduct()['id']);
    }
}
```

---

## Facade API Reference

All methods are available via `Creem::` or dependency-injecting `\Clevision\Creem\Creem`.

### Checkout Sessions

```php
Creem::createCheckout(array $params): array
Creem::getCheckout(string $checkoutId): array
```

### Products

```php
Creem::createProduct(array $params): array          // Create one-time or subscription product
Creem::getProduct(string $productId): array
Creem::listProducts(array $params = []): array
```

### Subscriptions

```php
Creem::getSubscription(string $subscriptionId): array
Creem::cancelSubscription(string $subscriptionId): array
Creem::resumeSubscription(string $subscriptionId): array
Creem::updateSubscription(string $subscriptionId, array $params): array
```

### Customers

```php
Creem::getCustomer(string $customerId): array
Creem::listCustomers(array $params = []): array
Creem::createCustomerPortal(string $customerId): array  // Returns billing portal URL
```

### Orders

```php
Creem::getOrder(string $orderId): array
Creem::listOrders(array $params = []): array
```

### Discounts

```php
Creem::getDiscount(string $discountId): array
Creem::listDiscounts(array $params = []): array
```

### License Keys

```php
Creem::getLicenseKey(string $licenseKeyId): array
Creem::validateLicenseKey(string $key, string $instanceName): array
Creem::deactivateLicenseKey(string $key, string $instanceId): array
```

### Refunds

```php
Creem::createRefund(string $orderId, array $params = []): array
```

### Signature Verification

```php
Creem::verifyWebhookSignature(string $rawPayload, string $signature): bool
Creem::verifyRedirectSignature(array $params): bool
```

---

## Webhook Events

All events extend `Clevision\Creem\Events\CreemWebhookEvent` and carry the full raw `$payload` array.

| Event Class | Creem Event Type | When |
|---|---|---|
| `CheckoutCompleted` | `checkout.completed` | Payment received |
| `SubscriptionActive` | `subscription.active` | New subscription created |
| `SubscriptionPaid` | `subscription.paid` | Subscription renewal paid ✅ Use this to grant access |
| `SubscriptionCanceled` | `subscription.canceled` | Canceled immediately |
| `SubscriptionScheduledCancel` | `subscription.scheduled_cancel` | Cancellation at period end |
| `SubscriptionPastDue` | `subscription.past_due` | Payment failed |
| `SubscriptionExpired` | `subscription.expired` | Period ended, no renewal |
| `SubscriptionUpdated` | `subscription.update` | Subscription modified |
| `SubscriptionTrialing` | `subscription.trialing` | Trial started |
| `SubscriptionPaused` | `subscription.paused` | Subscription paused |
| `RefundCreated` | `refund.created` | Refund issued |
| `DisputeCreated` | `dispute.created` | Chargeback/dispute opened |

### Event helper methods

```php
$event->getCustomer();     // array|null
$event->getProduct();      // array|null
$event->getMetadata();     // array
$event->getEventType();    // string
$event->payload;           // full raw array
```

---

## Artisan Commands

### `creem:webhook-secret`

Guides you through registering your webhook and configuring your secret.

```bash
php artisan creem:webhook-secret           # Show setup instructions
php artisan creem:webhook-secret --show    # Print the currently loaded secret
```

### `creem:sync-products`

Fetches all products from Creem and displays them in a table.

```bash
php artisan creem:sync-products               # Fetch & display
php artisan creem:sync-products --cache       # Cache result (default TTL: 1 hour)
php artisan creem:sync-products --ttl=7200    # Cache with custom TTL (seconds)
php artisan creem:sync-products --clear-cache # Clear cached products
```

---

## Middleware

`VerifyCreemSignature` is automatically applied to the webhook route. You can also use it manually:

```php
// Apply via alias
Route::post('/my-custom-webhook', MyWebhookHandler::class)
    ->middleware('creem.verify');
```

---

## Demo Dashboard

The package ships with a full demo dashboard for local development. Visit `/` (or wherever you mount the demo routes).

Features:
- **Connection status** — API key, webhook secret, mode (test / live)
- **Product listing** — All products with price and billing type badges
- **Create product** — Form to create one-time or subscription products with billing period selector
- **Manual checkout** — Paste any product ID to generate a checkout session
- **Quick API explorer** — Raw JSON from products, transactions, customers
- **Webhook HMAC verifier** — Paste payload and signature to validate

---

## Advanced Usage

### Seat-based billing

```php
$session = Creem::createCheckout([
    'product_id' => 'prod_xxx',
    'units'      => 5,    // 5 seats × base price
    'request_id' => auth()->id(),
]);
```

### Pre-fill customer and apply discount

```php
$session = Creem::createCheckout([
    'product_id'    => 'prod_xxx',
    'customer'      => ['email' => auth()->user()->email],
    'discount_code' => 'LAUNCH50',
]);
```

### Custom success URL and metadata

```php
$session = Creem::createCheckout([
    'product_id'  => 'prod_xxx',
    'success_url' => route('onboarding.welcome'),
    'metadata'    => [
        'user_id' => auth()->id(),
        'source'  => 'marketing_banner',
    ],
]);
```

### Customer billing portal

```php
$portal = Creem::createCustomerPortal($customerId);
return redirect($portal['url']);
```

### Low-level HTTP client

```php
use Clevision\Creem\CreemClient;

$client = app(CreemClient::class);
$data   = $client->get('/v1/any-endpoint', ['param' => 'value']);
```

---

## Testing

```bash
# Run the package test suite
cd packages/clevision/laravel-creem
vendor/bin/pest
```

In your application tests, fake the `Creem` facade:

```php
use Clevision\Creem\Facades\Creem;

Creem::shouldReceive('createCheckout')
    ->once()
    ->with(['product_id' => 'prod_xxx'])
    ->andReturn(['checkout_url' => 'https://pay.creem.io/checkout/test']);
```

Or fire webhook events directly:

```php
use Clevision\Creem\Events\CheckoutCompleted;

event(new CheckoutCompleted([
    'eventType' => 'checkout.completed',
    'customer'  => ['email' => 'test@example.com'],
    'metadata'  => ['user_id' => 1],
]));
```

---

## Error Handling

All API errors throw `Clevision\Creem\Exceptions\CreemException`:

```php
use Clevision\Creem\Exceptions\CreemException;
use Clevision\Creem\Facades\Creem;

try {
    $product = Creem::getProduct('prod_invalid');
} catch (CreemException $e) {
    $e->getMessage();   // Human-readable error
    $e->getCode();      // HTTP status code (404, 401, etc.)
    $e->getErrors();    // Validation errors array (if any)
}
```

---

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for recent changes.

---

## Contributing

Pull requests are welcome. Please ensure tests pass and add tests for new features.

---

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.
