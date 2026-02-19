<?php

namespace Clevision\Creem\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class CreemWebhookEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly array $payload) {}

    /**
     * Shortcut to nested payload data commonly used across events.
     */
    public function getCustomer(): ?array
    {
        return $this->payload['customer'] ?? null;
    }

    public function getProduct(): ?array
    {
        return $this->payload['product'] ?? null;
    }

    public function getMetadata(): array
    {
        return $this->payload['metadata'] ?? [];
    }

    public function getEventType(): string
    {
        return $this->payload['eventType'] ?? '';
    }
}
