<?php
use Stripe\StripeClient;

class StripeClientFactory
{
    private static function val(string $env, string $conf, string $const)
    {
        $v = getenv($env);
        if (!$v) { $v = FatApp::getConfig($conf, FatUtility::VAR_STRING, ''); }
        if (!$v && defined($const)) { $v = constant($const); } // <— fallback to conf/conf.php
        return trim((string)$v);
    }

    public static function client(): StripeClient
    {
        $secret = self::val('STRIPE_SECRET_KEY', 'CONF_STRIPE_SECRET_KEY', 'CONF_STRIPE_SECRET_KEY');
        if ($secret === '') {
            trigger_error('Stripe secret key is empty. (env STRIPE_SECRET_KEY / config CONF_STRIPE_SECRET_KEY / constant CONF_STRIPE_SECRET_KEY)', E_USER_WARNING);
        }
        return new StripeClient($secret);
    }

    public static function publishableKey(): string
    {
        return self::val('STRIPE_PUBLISHABLE_KEY', 'CONF_STRIPE_PUBLISHABLE_KEY', 'CONF_STRIPE_PUBLISHABLE_KEY');
    }

    public static function webhookSecret(): string
    {
        return self::val('STRIPE_WEBHOOK_SECRET', 'CONF_STRIPE_WEBHOOK_SECRET', 'CONF_STRIPE_WEBHOOK_SECRET');
    }
}
