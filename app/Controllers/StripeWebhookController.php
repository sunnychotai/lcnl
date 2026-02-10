<?php

namespace App\Controllers;

use App\Models\StripeWebhookEventModel;
use App\Models\MembershipPaymentModel;
use App\Models\MembershipModel;
use App\Models\MemberAuditLogModel;
use CodeIgniter\HTTP\ResponseInterface;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\EmailQueueModel;
use App\Models\MemberModel;


class StripeWebhookController extends BaseController
{
    public function handle(): ResponseInterface
    {
        $stripeConfig = config('Stripe');
        $endpointSecret = $stripeConfig->webhookSecret;

        $payload = $this->request->getBody(); // raw body (important)
        $sigHeader = $this->request->getHeaderLine('Stripe-Signature');

        if (!$endpointSecret) {
            log_message('error', 'Stripe webhook secret missing.');
            return $this->response->setStatusCode(500)->setBody('Webhook secret missing');
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return $this->response->setStatusCode(400)->setBody('Invalid payload');
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return $this->response->setStatusCode(400)->setBody('Invalid signature');
        }

        $eventModel = new StripeWebhookEventModel();

        // ✅ Store event first (dedupe via unique stripe_event_id)
        $receivedAt = date('Y-m-d H:i:s');
        try {
            $eventModel->insert([
                'stripe_event_id' => $event->id,
                'event_type' => $event->type,
                'api_version' => $event->api_version ?? null,
                'livemode' => !empty($event->livemode) ? 1 : 0,
                'payload_json' => $payload,
                'signature_header' => $sigHeader,
                'received_at' => $receivedAt,
                'process_status' => null,
            ]);
        } catch (\Throwable $t) {
            // Likely duplicate event_id
            // Return 200 so Stripe stops retrying (idempotent)
            return $this->response->setStatusCode(200)->setBody('Duplicate ignored');
        }

        // Process supported events
        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    $eventModel->where('stripe_event_id', $event->id)->set([
                        'processed_at' => date('Y-m-d H:i:s'),
                        'process_status' => 'ok',
                    ])->update();
                    break;

                // Optional: handle refund -> downgrade, if your policy requires
                // case 'charge.refunded':
                // case 'refund.updated':
                //     ...
                //     break;

                default:
                    // ignore other events
                    $eventModel->where('stripe_event_id', $event->id)->set([
                        'processed_at' => date('Y-m-d H:i:s'),
                        'process_status' => 'ignored',
                    ])->update();
                    break;
            }

            return $this->response->setStatusCode(200)->setBody('OK');
        } catch (\Throwable $e) {
            $eventModel->where('stripe_event_id', $event->id)->set([
                'processed_at' => date('Y-m-d H:i:s'),
                'process_status' => 'error',
                'process_error' => $e->getMessage(),
            ])->update();

            // Return 500 so Stripe retries later
            return $this->response->setStatusCode(500)->setBody('Processing error');
        }
    }

    private function handleCheckoutSessionCompleted($sessionObject): void
    {
        // Stripe Checkout Session object:
        // - id
        // - payment_status
        // - payment_intent
        // - customer
        // - metadata
        $checkoutSessionId = (string) ($sessionObject->id ?? '');
        $paymentStatus = (string) ($sessionObject->payment_status ?? '');
        $paymentIntentId = (string) ($sessionObject->payment_intent ?? '');
        $customerId = (string) ($sessionObject->customer ?? '');

        // Only fulfill if payment is actually paid
        if ($paymentStatus !== 'paid') {
            return;
        }

        $paymentModel = new MembershipPaymentModel();
        $paymentRow = $paymentModel->where('stripe_checkout_session_id', $checkoutSessionId)->first();

        if (!$paymentRow) {
            // no matching internal payment; log and stop
            log_message('error', 'No membership_payment found for checkout_session_id=' . $checkoutSessionId);
            return;
        }

        // ✅ Idempotency: if already paid, do nothing
        if (($paymentRow['status'] ?? '') === 'paid') {
            return;
        }

        $memberId = (int) $paymentRow['member_id'];

        // Update payment record
        $paymentModel->update((int) $paymentRow['id'], [
            'status' => 'paid',
            'stripe_payment_intent_id' => $paymentIntentId ?: ($paymentRow['stripe_payment_intent_id'] ?? null),
            'stripe_customer_id' => $customerId ?: ($paymentRow['stripe_customer_id'] ?? null),
            'paid_at' => date('Y-m-d H:i:s'),
        ]);

        $membershipModel = new MembershipModel();

        // Find the active membership row
        $currentMembership = $membershipModel
            ->where('member_id', $memberId)
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->first();

        if (!$currentMembership) {
            log_message('error', 'No active membership found for member_id=' . $memberId);
            return;
        }

        // Update STANDARD → LIFE
        $membershipModel->update($currentMembership['id'], [
            'membership_type' => 'Life',
            'notes' => 'Upgraded to LIFE via Stripe. Payment ID ' . $paymentRow['id'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);


        $auditModel = new MemberAuditLogModel();

        $auditModel->insert([
            'member_id' => $memberId,
            'type' => 'MEMBERSHIP',
            'field_name' => 'membership_type',
            'old_value' => $currentMembership['membership_type'], // STANDARD
            'new_value' => 'LIFE',
            'description' => 'Membership upgraded from STANDARD to LIFE via Stripe payment',
            'changed_by' => 0, // system / Stripe webhook
            'changed_at' => date('Y-m-d H:i:s'),
        ]);


        // ========================================
// SEND LIFE MEMBERSHIP CONFIRMATION EMAIL
// ========================================


        // ========================================
// SEND LIFE MEMBERSHIP CONFIRMATION EMAIL
// ========================================

        $memberModel = new MemberModel();
        $member = $memberModel->find($memberId);

        if (!$member || empty($member['email'])) {
            log_message(
                'error',
                'StripeWebhook: unable to send life membership email (member missing or email empty) member_id=' . $memberId
            );
            return;
        }

        $memberEmailHtml = view('emails/life_membership_confirmed', [
            'first_name' => $member['first_name'],
            'last_name' => $member['last_name'],
            'email' => $member['email'],
            'payment_reference' => $paymentRow['stripe_payment_intent_id'] ?? 'Stripe',
            'membership_type' => 'LIFE',
            'dashboard_url' => base_url('account/dashboard'),
        ]);

        $emailQueue = new EmailQueueModel();

        $insertId = $emailQueue->enqueue([
            'to_email' => $member['email'],
            'to_name' => trim($member['first_name'] . ' ' . $member['last_name']),
            'subject' => 'Your LCNL Life Membership is now active',
            'body_html' => $memberEmailHtml,
            'body_text' => strip_tags($memberEmailHtml),
            'priority' => 1,
            'type' => 'life_membership',
            'related_id' => (int) $memberId,
        ]);

        if (!$insertId) {
            log_message(
                'error',
                'StripeWebhook: failed to enqueue life membership email member_id=' . $memberId .
                ' errors=' . json_encode($emailQueue->errors())
            );
            return;
        }

        // Mark email sent for idempotency
        $paymentModel->update((int) $paymentRow['id'], [
            'life_confirmation_email_sent_at' => date('Y-m-d H:i:s'),
        ]);









        // Optional: send confirmation email here (using your EmailService/queue)
    }
}
