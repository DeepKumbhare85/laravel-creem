<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a subscription is scheduled for cancellation at period end.
 *
 * The subscription stays active until current_period_end_date. You can
 * resume the subscription before that date using Creem::resumeSubscription().
 */
class SubscriptionScheduledCancel extends CreemWebhookEvent
{
    public function getSubscription(): ?array
    {
        return $this->payload['subscription'] ?? null;
    }
}
