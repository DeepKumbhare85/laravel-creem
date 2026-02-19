<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a new subscription is created and payment was successful.
 *
 * Use only for synchronization — prefer SubscriptionPaid for granting access.
 */
class SubscriptionActive extends CreemWebhookEvent
{
    public function getSubscription(): ?array
    {
        return $this->payload['subscription'] ?? null;
    }
}
