<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

class StripeWebhookController extends FatController
{
    public function index()
    {
        $payload = @file_get_contents('php://input');
        $sig     = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $secret  = StripeClientFactory::webhookSecret();

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo 'Bad signature';
            return;
        }

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    break;

                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;

                case 'invoice.payment_failed':
                    $subId = $event->data->object->subscription ?? null;
                    if ($subId) {
                        UserSubscription::markExpiredByStripeSub($subId);
                    }
                    break;

                case 'customer.subscription.deleted':
                    $subId = $event->data->object->id ?? null;
                    if ($subId) {
                        UserSubscription::markCanceledByStripeSub($subId);
                    }
                    break;

                // Add more events if needed (invoice.payment_succeeded etc.)
            }
        } catch (\Throwable $e) {
            // In production: log this
            // error_log('Stripe webhook error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Webhook error';
            return;
        }

        http_response_code(200);
        echo 'ok';
    }

    /**
     * Handle checkout.session.completed (mode = subscription)
     *
     * Creates or updates tbl_user_subscriptions row and marks
     * trial usage on user if applicable.
     */
    private function handleCheckoutSessionCompleted(\Stripe\Checkout\Session $session): void
    {
        if ($session->mode !== 'subscription') {
            return;
        }

        $metadata = $session->metadata ?? new \stdClass();

        $userId   = (int) ($metadata->user_id ?? 0);
        $pkgId    = (int) ($metadata->spackage_id ?? 0);
        $billing  = (string) ($metadata->billing ?? 'monthly');
        $subjects = (string) ($metadata->subject_ids ?? '');
        $isTrial  = ((int) ($metadata->is_trial ?? 0) === 1);

        if ($userId <= 0 || $pkgId <= 0 || empty($session->subscription)) {
            return; // malformed metadata; don't blow up the webhook
        }

        $stripe       = StripeClientFactory::client();
        $subscription = $stripe->subscriptions->retrieve($session->subscription, []);

        // Stripe might attach customer on subscription or session
        $customerId = null;
        if (!empty($subscription->customer)) {
            $customerId = (string) $subscription->customer;
        } elseif (!empty($session->customer)) {
            $customerId = (string) $session->customer;
        }

        // Upsert local subscription
        $usubsId = UserSubscription::upsertFromStripe(
            $userId,
            $pkgId,
            $subjects,
            $billing,
            $subscription,
            $isTrial,
            $customerId
        );

        if ($usubsId > 0 && $isTrial) {
            // Mark that this user has used their trial globally
            SubscriptionTrialService::markTrialUsed($userId);
        }

        // Sync course access (trial or active both get access)
        SubscriptionEnrollment::syncForUser($userId);
    }

    /**
     * Handle customer.subscription.updated
     *
     * Sync Stripe status to local DB.
     */
    private function handleSubscriptionUpdated(\Stripe\Subscription $subscription): void
    {
        UserSubscription::syncStatusFromStripe($subscription);
    }
}
