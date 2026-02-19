<?php

namespace Clevision\Creem\Http\Controllers;

use Clevision\Creem\Events\CheckoutCompleted;
use Clevision\Creem\Events\DisputeCreated;
use Clevision\Creem\Events\RefundCreated;
use Clevision\Creem\Events\SubscriptionActive;
use Clevision\Creem\Events\SubscriptionCanceled;
use Clevision\Creem\Events\SubscriptionExpired;
use Clevision\Creem\Events\SubscriptionPaid;
use Clevision\Creem\Events\SubscriptionPastDue;
use Clevision\Creem\Events\SubscriptionPaused;
use Clevision\Creem\Events\SubscriptionScheduledCancel;
use Clevision\Creem\Events\SubscriptionTrialing;
use Clevision\Creem\Events\SubscriptionUpdated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Map of Creem event types to their Laravel Event classes.
     */
    protected array $eventMap = [
        'checkout.completed'           => CheckoutCompleted::class,
        'subscription.active'          => SubscriptionActive::class,
        'subscription.paid'            => SubscriptionPaid::class,
        'subscription.canceled'        => SubscriptionCanceled::class,
        'subscription.scheduled_cancel' => SubscriptionScheduledCancel::class,
        'subscription.past_due'        => SubscriptionPastDue::class,
        'subscription.expired'         => SubscriptionExpired::class,
        'subscription.update'          => SubscriptionUpdated::class,
        'subscription.trialing'        => SubscriptionTrialing::class,
        'subscription.paused'          => SubscriptionPaused::class,
        'refund.created'               => RefundCreated::class,
        'dispute.created'              => DisputeCreated::class,
    ];

    /**
     * Handle an incoming Creem webhook request.
     *
     * The VerifyCreemSignature middleware has already verified the signature
     * before this controller is reached.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $payload   = $request->json()->all();
        $eventType = $payload['eventType'] ?? null;

        if (! $eventType) {
            Log::warning('[Creem] Webhook received without an eventType.', $payload);

            return response()->json(['status' => 'ignored'], 200);
        }

        if (! isset($this->eventMap[$eventType])) {
            Log::info("[Creem] Unhandled webhook event type: {$eventType}");

            return response()->json(['status' => 'unhandled'], 200);
        }

        $eventClass = $this->eventMap[$eventType];

        event(new $eventClass($payload));

        Log::info("[Creem] Dispatched event for: {$eventType}");

        return response()->json(['status' => 'ok'], 200);
    }
}
