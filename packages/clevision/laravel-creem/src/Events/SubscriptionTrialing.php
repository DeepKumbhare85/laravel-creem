<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a subscription starts a trial period.
 */
class SubscriptionTrialing extends CreemWebhookEvent
{
    public function getSubscription(): ?array
    {
        return $this->payload['subscription'] ?? null;
    }

    public function getTrialEndsAt(): ?string
    {
        return $this->payload['subscription']['trial_end'] ?? null;
    }
}
