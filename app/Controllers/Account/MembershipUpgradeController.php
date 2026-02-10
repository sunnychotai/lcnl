<?php

namespace App\Controllers\Account;

use App\Controllers\BaseController;
use App\Libraries\StripeService;
use App\Models\MemberModel;
use App\Models\MemberAuditLogModel;
use App\Models\MembershipPaymentModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class MembershipUpgradeController extends BaseController
{
    public function checkout(): RedirectResponse
    {
        $memberId = (int) session()->get('member_id');
        if (!$memberId) {
            return redirect()->to('/login')->with('error', 'Please log in.');
        }

        $memberModel = new MemberModel();
        $member = $memberModel->find($memberId);

        if (!$member) {
            return redirect()->to('account/dashboard')->with('error', 'Member not found.');
        }

        // ✅ Only STANDARD members can upgrade
        $currentType = strtoupper((string) ($member['membership_type'] ?? 'Standard'));
        if ($currentType === 'LIFE') {
            return redirect()->to('account/dashboard')->with('success', 'You already have Life Membership.');
        }

        $amountMinor = 75; // £75.00
        $currency = 'GBP';

        // Create an internal payment record first
        $paymentModel = new MembershipPaymentModel();
        $idempotencyKey = 'life_upgrade_' . $memberId . '_' . bin2hex(random_bytes(8));

        $paymentId = $paymentModel->insert([
            'member_id' => $memberId,
            'provider' => 'stripe',
            'purpose' => 'life_upgrade',
            'status' => 'initiated',
            'amount_minor' => $amountMinor,
            'currency' => $currency,
            'idempotency_key' => $idempotencyKey,
        ], true);

        $stripe = new StripeService();
        $stripeConfig = config('Stripe');

        $baseUrl = rtrim((string) config('App')->baseURL, '/');

        // Create Checkout Session
        $session = $stripe->client->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [
                [
                    'price' => $stripeConfig->lifePriceId,
                    'quantity' => 1,
                ]
            ],
            'success_url' => $baseUrl . '/account/membership/upgrade/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $baseUrl . '/account/membership/upgrade/cancel',
            'customer_email' => $member['email'] ?? null, // optional, nice-to-have
            'metadata' => [
                'member_id' => (string) $memberId,
                'payment_id' => (string) $paymentId,
                'purpose' => 'life_upgrade',
            ],
        ], [
            // Optional: Stripe idempotency at API level too
            'idempotency_key' => $idempotencyKey,
        ]);

        // Save session id
        $paymentModel->update($paymentId, [
            'stripe_checkout_session_id' => $session->id,
        ]);

        // Audit: Stripe checkout initiated
        $auditModel = new MemberAuditLogModel();

        $auditModel->insert([
            'member_id' => $memberId,
            'type' => 'PAYMENT',
            'field_name' => 'stripe_checkout',
            'old_value' => null,
            'new_value' => $session->id,
            'description' => 'Stripe checkout session created for Life Membership upgrade',
            'changed_by' => 0, // system
            'changed_at' => date('Y-m-d H:i:s'),
        ]);


        return redirect()->to($session->url);
    }

    public function success()
    {
        // UX only — do NOT upgrade here.
        $sessionId = (string) $this->request->getGet('session_id');

        return view('/membership/upgrade_success', [
            'session_id' => $sessionId,
        ]);
    }

    public function cancel()
    {
        return view('/membership/upgrade_cancel');
    }
}
