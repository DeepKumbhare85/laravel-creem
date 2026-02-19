<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a subscription transaction is paid.
 *
 * Use this event (rather than SubscriptionActive) to grant access to your product.
 */
class SubscriptionPaid extends CreemWebhookEvent
{
    public function getSubscription(): ?array
    {
        return $this->payload['subscription'] ?? null;
    }

    public function getOrder(): ?array
    {
        return $this->payload['order'] ?? null;
    }
}
