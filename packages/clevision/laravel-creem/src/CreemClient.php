<?php

namespace Clevision\Creem;

use Clevision\Creem\Exceptions\CreemException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class CreemClient
{
    protected GuzzleClient $http;

    protected string $baseUrl;

    public function __construct(
        protected string $apiKey,
        protected bool $testMode = false,
        protected int $timeout = 30,
        protected int $retries = 3,
        protected int $retryDelay = 500,
    ) {
        $this->baseUrl = $testMode
            ? rtrim(config('creem.test_api_url', 'https://test-api.creem.io'), '/')
            : rtrim(config('creem.api_url', 'https://api.creem.io'), '/');

        $this->http = new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'timeout'  => $this->timeout,
            'handler'  => $this->buildHandlerStack(),
            'headers'  => [
                'x-api-key'    => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Checkout Sessions
    // -------------------------------------------------------------------------

    /**
     * Create a new checkout session.
     *
     * @param  array  $params  See https://docs.creem.io/api-reference/endpoint/create-checkout
     */
    public function createCheckout(array $params): array
    {
        return $this->post('/v1/checkouts', $params);
    }

    /**
     * Retrieve a checkout session by ID.
     */
    public function getCheckout(string $checkoutId): array
    {
        return $this->get('/v1/checkouts', ['checkout_id' => $checkoutId]);
    }

    // -------------------------------------------------------------------------
    // Products
    // -------------------------------------------------------------------------

    /**
     * Retrieve a single product by ID.
     */
    public function getProduct(string $productId): array
    {
        return $this->get('/v1/products', ['product_id' => $productId]);
    }

    /**
     * List all products.
     */
    public function listProducts(array $params = []): array
    {
        return $this->get('/v1/products/search', $params);
    }

    // -------------------------------------------------------------------------
    // Subscriptions
    // -------------------------------------------------------------------------

    /**
     * Retrieve a subscription by ID.
     */
    public function getSubscription(string $subscriptionId): array
    {
        return $this->get('/v1/subscriptions', ['subscription_id' => $subscriptionId]);
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(string $subscriptionId): array
    {
        return $this->post("/v1/subscriptions/{$subscriptionId}/cancel");
    }

    /**
     * Resume a scheduled-cancel subscription.
     */
    public function resumeSubscription(string $subscriptionId): array
    {
        return $this->post("/v1/subscriptions/{$subscriptionId}/resume");
    }

    /**
     * Update a subscription (e.g. quantity / plan change).
     */
    public function updateSubscription(string $subscriptionId, array $params): array
    {
        return $this->post("/v1/subscriptions/{$subscriptionId}", $params);
    }

    // -------------------------------------------------------------------------
    // Customers
    // -------------------------------------------------------------------------

    /**
     * Retrieve a customer by ID or email.
     */
    public function getCustomer(string $customerId): array
    {
        return $this->get('/v1/customers', ['customer_id' => $customerId]);
    }

    /**
     * List all customers.
     */
    public function listCustomers(array $params = []): array
    {
        return $this->get('/v1/customers/list', $params);
    }

    /**
     * Create a customer portal session (billing portal link).
     */
    public function createCustomerPortal(string $customerId): array
    {
        return $this->post('/v1/customers/billing', ['customer_id' => $customerId]);
    }

    // -------------------------------------------------------------------------
    // Orders
    // -------------------------------------------------------------------------

    /**
     * Retrieve a transaction/order by ID.
     */
    public function getOrder(string $orderId): array
    {
        return $this->get('/v1/transactions', ['transaction_id' => $orderId]);
    }

    /**
     * List all transactions (orders).
     */
    public function listOrders(array $params = []): array
    {
        return $this->get('/v1/transactions/search', $params);
    }

    // -------------------------------------------------------------------------
    // Discounts
    // -------------------------------------------------------------------------

    /**
     * Retrieve a discount by ID or code.
     */
    public function getDiscount(string $discountId): array
    {
        return $this->get('/v1/discounts', ['discount_id' => $discountId]);
    }

    /**
     * List all discounts.
     */
    public function listDiscounts(array $params = []): array
    {
        return $this->get('/v1/discounts/search', $params);
    }

    // -------------------------------------------------------------------------
    // License Keys
    // -------------------------------------------------------------------------

    /**
     * Retrieve a license key by ID.
     */
    public function getLicenseKey(string $licenseKeyId): array
    {
        return $this->get("/v1/license-keys/{$licenseKeyId}");
    }

    /**
     * Validate a license key instance.
     */
    public function validateLicenseKey(string $key, string $instanceId): array
    {
        return $this->post('/v1/licenses/validate', [
            'key'         => $key,
            'instance_id' => $instanceId,
        ]);
    }

    /**
     * Deactivate a license key instance.
     */
    public function deactivateLicenseKey(string $key, string $instanceId): array
    {
        return $this->post('/v1/licenses/deactivate', [
            'key'         => $key,
            'instance_id' => $instanceId,
        ]);
    }

    // -------------------------------------------------------------------------
    // Refunds
    // -------------------------------------------------------------------------

    /**
     * Create a refund for an order.
     */
    public function createRefund(string $orderId, array $params = []): array
    {
        return $this->post('/v1/refunds', array_merge(['order_id' => $orderId], $params));
    }

    // -------------------------------------------------------------------------
    // HTTP Helpers
    // -------------------------------------------------------------------------

    public function get(string $uri, array $query = []): array
    {
        try {
            $response = $this->http->get($uri, ['query' => $query]);

            return $this->decode($response);
        } catch (ClientException | ServerException $e) {
            $this->handleGuzzleException($e);
        }
    }

    public function post(string $uri, array $body = []): array
    {
        try {
            $response = $this->http->post($uri, ['json' => $body]);

            return $this->decode($response);
        } catch (ClientException | ServerException $e) {
            $this->handleGuzzleException($e);
        }
    }

    public function patch(string $uri, array $body = []): array
    {
        try {
            $response = $this->http->patch($uri, ['json' => $body]);

            return $this->decode($response);
        } catch (ClientException | ServerException $e) {
            $this->handleGuzzleException($e);
        }
    }

    protected function decode(Response $response): array
    {
        $body = (string) $response->getBody();

        return json_decode($body, true) ?? [];
    }

    protected function handleGuzzleException(ClientException|ServerException $e): never
    {
        $response = $e->getResponse();
        $body     = json_decode((string) $response->getBody(), true) ?? [];

        throw CreemException::fromResponse($response->getStatusCode(), $body);
    }

    protected function buildHandlerStack(): HandlerStack
    {
        $stack = HandlerStack::create();

        if ($this->retries > 0) {
            $stack->push(Middleware::retry(
                function (int $retries, Request $request, ?Response $response, ?\Throwable $exception) {
                    if ($retries >= $this->retries) {
                        return false;
                    }
                    // Retry on server errors and connection timeouts
                    if ($exception instanceof \GuzzleHttp\Exception\ConnectException) {
                        return true;
                    }
                    if ($response && $response->getStatusCode() >= 500) {
                        return true;
                    }

                    return false;
                },
                fn(int $retries) => $this->retryDelay * $retries,
            ));
        }

        return $stack;
    }
}
