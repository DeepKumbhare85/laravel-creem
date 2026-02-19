<?php

namespace Clevision\Creem;

use Clevision\Creem\Exceptions\CreemException;

/**
 * Main Creem service — wraps CreemClient and adds signature helpers.
 *
 * Accessible via the Creem facade or dependency injection.
 */
class Creem
{
    public function __construct(protected CreemClient $client) {}

    // -------------------------------------------------------------------------
    // Checkout Sessions
    // -------------------------------------------------------------------------

    /**
     * Create a new checkout session and return the checkout_url.
     *
     * @example
     *   $checkout = Creem::createCheckout([
     *       'product_id'  => 'prod_xxx',
     *       'request_id'  => auth()->id(),
     *       'success_url' => route('dashboard'),
     *       'metadata'    => ['user_id' => auth()->id()],
     *   ]);
     *
     *   return redirect($checkout['checkout_url']);
     */
    public function createCheckout(array $params): array
    {
        return $this->client->createCheckout($params);
    }

    /**
     * Retrieve a checkout session by its ID.
     */
    public function getCheckout(string $checkoutId): array
    {
        return $this->client->getCheckout($checkoutId);
    }

    // -------------------------------------------------------------------------
    // Products
    // -------------------------------------------------------------------------

    /**
     * Retrieve a single product by its ID.
     */
    public function getProduct(string $productId): array
    {
        return $this->client->getProduct($productId);
    }

    /**
     * List all products in your Creem account.
     */
    public function listProducts(array $params = []): array
    {
        return $this->client->listProducts($params);
    }

    // -------------------------------------------------------------------------
    // Subscriptions
    // -------------------------------------------------------------------------

    /**
     * Retrieve a subscription by its ID.
     */
    public function getSubscription(string $subscriptionId): array
    {
        return $this->client->getSubscription($subscriptionId);
    }

    /**
     * Cancel a subscription immediately.
     */
    public function cancelSubscription(string $subscriptionId): array
    {
        return $this->client->cancelSubscription($subscriptionId);
    }

    /**
     * Resume a subscription that is scheduled for cancellation.
     */
    public function resumeSubscription(string $subscriptionId): array
    {
        return $this->client->resumeSubscription($subscriptionId);
    }

    /**
     * Update a subscription (e.g. quantity/seat changes).
     */
    public function updateSubscription(string $subscriptionId, array $params): array
    {
        return $this->client->updateSubscription($subscriptionId, $params);
    }

    // -------------------------------------------------------------------------
    // Customers
    // -------------------------------------------------------------------------

    /**
     * Retrieve a customer by their ID.
     */
    public function getCustomer(string $customerId): array
    {
        return $this->client->getCustomer($customerId);
    }

    /**
     * List all customers.
     */
    public function listCustomers(array $params = []): array
    {
        return $this->client->listCustomers($params);
    }

    /**
     * Create a billing portal session for a customer.
     * Returns a URL you can redirect the customer to for self-service billing.
     */
    public function createCustomerPortal(string $customerId): array
    {
        return $this->client->createCustomerPortal($customerId);
    }

    // -------------------------------------------------------------------------
    // Orders
    // -------------------------------------------------------------------------

    /**
     * Retrieve an order by its ID.
     */
    public function getOrder(string $orderId): array
    {
        return $this->client->getOrder($orderId);
    }

    /**
     * List all orders.
     */
    public function listOrders(array $params = []): array
    {
        return $this->client->listOrders($params);
    }

    // -------------------------------------------------------------------------
    // Discounts
    // -------------------------------------------------------------------------

    /**
     * Retrieve a discount by its ID.
     */
    public function getDiscount(string $discountId): array
    {
        return $this->client->getDiscount($discountId);
    }

    /**
     * List all discounts.
     */
    public function listDiscounts(array $params = []): array
    {
        return $this->client->listDiscounts($params);
    }

    // -------------------------------------------------------------------------
    // License Keys
    // -------------------------------------------------------------------------

    /**
     * Retrieve a license key by its ID.
     */
    public function getLicenseKey(string $licenseKeyId): array
    {
        return $this->client->getLicenseKey($licenseKeyId);
    }

    /**
     * Activate / validate a license key.
     */
    public function validateLicenseKey(string $key, string $instanceName): array
    {
        return $this->client->validateLicenseKey($key, $instanceName);
    }

    /**
     * Deactivate a license key instance.
     */
    public function deactivateLicenseKey(string $key, string $instanceId): array
    {
        return $this->client->deactivateLicenseKey($key, $instanceId);
    }

    // -------------------------------------------------------------------------
    // Refunds
    // -------------------------------------------------------------------------

    /**
     * Issue a refund for an order.
     */
    public function createRefund(string $orderId, array $params = []): array
    {
        return $this->client->createRefund($orderId, $params);
    }

    // -------------------------------------------------------------------------
    // Signature Helpers
    // -------------------------------------------------------------------------

    /**
     * Verify the HMAC-SHA256 webhook signature from the `creem-signature` header.
     *
     * @param  string  $rawPayload  Raw request body string
     * @param  string  $signature   Value from the `creem-signature` request header
     */
    public function verifyWebhookSignature(string $rawPayload, string $signature): bool
    {
        $secret = config('creem.webhook_secret');

        if (empty($secret)) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawPayload, $secret);

        return hash_equals($expected, $signature);
    }

    /**
     * Verify the HMAC-SHA256 redirect signature appended to the success_url.
     *
     * Creem signs the redirect query parameters using your API key.
     * Parameters with null values must be excluded before verifying.
     *
     * @param  array  $params  All query parameters from the success URL (including signature)
     */
    public function verifyRedirectSignature(array $params): bool
    {
        $signature = $params['signature'] ?? null;

        if (! $signature) {
            return false;
        }

        unset($params['signature']);

        // Filter null/undefined values, sort, build query string
        $filtered = array_filter($params, fn($v) => ! is_null($v));
        ksort($filtered);
        $query = http_build_query($filtered);

        $expected = hash_hmac('sha256', $query, config('creem.api_key'));

        return hash_equals($expected, $signature);
    }

    /**
     * Access the underlying HTTP client for low-level calls.
     */
    public function getClient(): CreemClient
    {
        return $this->client;
    }
}
