<?php

namespace Clevision\Creem\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array createCheckout(array $params)
 * @method static array getCheckout(string $checkoutId)
 * @method static array getProduct(string $productId)
 * @method static array listProducts(array $params = [])
 * @method static array getSubscription(string $subscriptionId)
 * @method static array cancelSubscription(string $subscriptionId)
 * @method static array resumeSubscription(string $subscriptionId)
 * @method static array updateSubscription(string $subscriptionId, array $params)
 * @method static array getCustomer(string $customerId)
 * @method static array listCustomers(array $params = [])
 * @method static array createCustomerPortal(string $customerId)
 * @method static array getOrder(string $orderId)
 * @method static array listOrders(array $params = [])
 * @method static array getDiscount(string $discountId)
 * @method static array listDiscounts(array $params = [])
 * @method static array getLicenseKey(string $licenseKeyId)
 * @method static array validateLicenseKey(string $key, string $instanceName)
 * @method static array deactivateLicenseKey(string $key, string $instanceId)
 * @method static array createRefund(string $orderId, array $params = [])
 * @method static bool  verifyWebhookSignature(string $rawPayload, string $signature)
 * @method static bool  verifyRedirectSignature(array $params)
 * @method static \Clevision\Creem\CreemClient getClient()
 *
 * @see \Clevision\Creem\Creem
 */
class Creem extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Clevision\Creem\Creem::class;
    }
}
