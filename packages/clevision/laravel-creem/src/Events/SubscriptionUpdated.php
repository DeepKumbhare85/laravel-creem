<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a subscription object is updated.
 */
class SubscriptionUpdated extends CreemWebhookEvent
{
    public function getSubscription(): ?array
    {
        return $this->payload['subscription'] ?? null;
    }
}
