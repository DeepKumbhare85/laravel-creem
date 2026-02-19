<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a subscription is paused.
 */
class SubscriptionPaused extends CreemWebhookEvent
{
    public function getSubscription(): ?array
    {
        return $this->payload['subscription'] ?? null;
    }
}
