<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a checkout session is completed (payment received).
 *
 * Payload contains: customer, product, order, subscription, metadata, custom_fields.
 */
class CheckoutCompleted extends CreemWebhookEvent
{
    public function getOrder(): ?array
    {
        return $this->payload['order'] ?? null;
    }

    public function getSubscription(): ?array
    {
        return $this->payload['subscription'] ?? null;
    }
}
