<?php

namespace Tests\Support\Libraries;

use App\Libraries\StripeService;
use RuntimeException;
use Stripe\Checkout\Session;

/**
 * In-memory stand-in for Stripe Checkout, so payment tests never touch Stripe.
 */
class FakeStripeService extends StripeService
{
    /** @var array<string, array> Checkout Sessions by id, as Stripe would return them. */
    public array $sessions = [];

    /** @var list<array> The params of every create call. */
    public array $created = [];

    public bool $failCreates = false;

    public function __construct()
    {
        // No real client: every call goes through the overrides below.
    }

    public function createCheckoutSession(array $params, array $opts = []): Session
    {
        if ($this->failCreates) {
            throw new RuntimeException('Stripe is unavailable');
        }

        $this->created[] = $params;
        $id = 'cs_test_' . count($this->created);

        $this->sessions[$id] = [
            'id'             => $id,
            'object'         => 'checkout.session',
            'status'         => 'open',
            'payment_status' => 'unpaid',
            'url'            => 'https://checkout.stripe.test/' . $id,
            'amount_total'   => 3500,
            'currency'       => 'gbp',
            'payment_intent' => null,
            'metadata'       => $params['metadata'] ?? [],
        ];

        return Session::constructFrom($this->sessions[$id]);
    }

    public function retrieveCheckoutSession(string $id): Session
    {
        if (! isset($this->sessions[$id])) {
            throw new RuntimeException('No such checkout.session: ' . $id);
        }

        return Session::constructFrom($this->sessions[$id]);
    }

    /** Pay a session, as the payer would on Stripe's page. Returns it as Stripe would send it. */
    public function complete(string $id, int $amount = 3500): array
    {
        $this->sessions[$id] = array_merge($this->sessions[$id], [
            'status'         => 'complete',
            'payment_status' => 'paid',
            'amount_total'   => $amount,
            'payment_intent' => 'pi_test_' . substr($id, 8),
        ]);

        return $this->sessions[$id];
    }

    public function expire(string $id): array
    {
        $this->sessions[$id]['status'] = 'expired';

        return $this->sessions[$id];
    }
}
