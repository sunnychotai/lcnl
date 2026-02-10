<?php

namespace App\Libraries;

use Stripe\StripeClient;

class StripeService
{
    public StripeClient $client;

    public function __construct()
    {
        $secret = config('Stripe')->secretKey;
        $this->client = new StripeClient($secret);
    }
}
