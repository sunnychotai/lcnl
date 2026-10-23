<?php

namespace App\Libraries;

use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeService
{
    public StripeClient $client;

    public function __construct()
    {
        $secret = config('Stripe')->secretKey;

        // The SDK refuses an empty key outright; without one, calls fail with an
        // authentication error instead, which callers already catch.
        $this->client = new StripeClient($secret !== '' ? $secret : []);
    }

    public function createCheckoutSession(array $params, array $opts = []): Session
    {
        return $this->client->checkout->sessions->create($params, $opts);
    }

    public function retrieveCheckoutSession(string $id): Session
    {
        return $this->client->checkout->sessions->retrieve($id);
    }

    /** Link to a payment in the Stripe dashboard, for admins. */
    public static function dashboardPaymentUrl(string $paymentIntentId): string
    {
        $test = str_starts_with(config('Stripe')->secretKey, 'sk_test_');

        return 'https://dashboard.stripe.com/' . ($test ? 'test/' : '') . 'payments/' . rawurlencode($paymentIntentId);
    }
}
