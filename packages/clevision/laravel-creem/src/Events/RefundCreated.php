<?php

namespace Clevision\Creem\Events;

/**
 * Dispatched when a refund is created by the merchant.
 */
class RefundCreated extends CreemWebhookEvent
{
    public function getRefund(): ?array
    {
        return $this->payload['refund'] ?? null;
    }

    public function getOrder(): ?array
    {
        return $this->payload['order'] ?? null;
    }
}
