<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Stripe extends BaseConfig
{
    public string $secretKey;
    public string $publishableKey;
    public string $webhookSecret;
    public string $lifePriceId;

    /** £35 one-off price on the "Chopda Pujan 2026" product. */
    public string $chopdaPriceId;

    public function __construct()
    {
        parent::__construct();

        $this->secretKey = (string) env('stripe.secretKey', '');
        $this->publishableKey = (string) env('stripe.publishableKey', '');
        $this->webhookSecret = (string) env('stripe.webhookSecret', '');
        $this->lifePriceId = (string) env('stripe.lifePriceId', '');
        $this->chopdaPriceId = (string) env('stripe.chopdaPriceId', '');
    }
}
