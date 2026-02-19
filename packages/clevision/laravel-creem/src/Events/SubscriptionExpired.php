<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a subscription expires (current_period_end reached without renewal).
 */
class SubscriptionExpired extends CreemWebhookEvent
{
    public function getSubscription(): ?array
    {
        return $this->payload['subscription'] ?? null;
    }
}
