<?php

namespace App\Listeners;

use App\Http\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Cashier\Events\WebhookReceived;

class StripeWebhookListener
{
    private const PAID_EVENTS = [
        'checkout.session.completed',
        'checkout.session.async_payment_succeeded',
    ];

    /**
     * Create the event listener.
     */
    public function __construct(
        public PaymentService $paymentService,
    )
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if (! in_array($event->payload['type'] ?? null, self::PAID_EVENTS, true)) {
            return;
        }

        $session = $event->payload['data']['object'];

        if (($session['payment_status'] ?? null) !== 'paid') {
            return;
        }

        $this->paymentService->makeAsPaid($session['metadata']['payment_id'] , $session['id']);
    }
}
