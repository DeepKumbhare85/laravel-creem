<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a subscription is canceled (immediately).
 *
 * Revoke access to the product for the associated customer.
 */
class SubscriptionCanceled extends CreemWebhookEvent
{
    public function getSubscription(): ?array
    {
        return $this->payload['subscription'] ?? null;
    }
}
