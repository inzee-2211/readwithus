<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

class StripeWebhookController extends FatController
{
    public function index()
    {
        $payload = @file_get_contents('php://input');
        $sig = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $secret = StripeClientFactory::webhookSecret();

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo 'Bad signature';
            exit;
        }

        switch ($event->type) {
            case 'invoice.payment_failed':
                $subId = $event->data->object->subscription ?? null;
                if ($subId) UserSubscription::markExpiredByStripeSub($subId);
                break;

            case 'customer.subscription.deleted':
                $subId = $event->data->object->id ?? null;
                if ($subId) UserSubscription::markCanceledByStripeSub($subId);
                break;

            // add other events if you want
        }

        http_response_code(200);
        echo 'ok';
    }
}
