<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a dispute (chargeback) is created by the customer.
 */
class DisputeCreated extends CreemWebhookEvent
{
    public function getDispute(): ?array
    {
        return $this->payload['dispute'] ?? null;
    }

    public function getOrder(): ?array
    {
        return $this->payload['order'] ?? null;
    }
}
