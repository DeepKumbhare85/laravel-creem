<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a subscription payment fails and the subscription is past due.
 *
 * Creem will automatically retry the payment. If retries are exhausted the
 * subscription moves to canceled.
 */
class SubscriptionPastDue extends CreemWebhookEvent
{
    public function getSubscription(): ?array
    {
        return $this->payload['subscription'] ?? null;
    }
}
