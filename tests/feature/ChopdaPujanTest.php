<?php

namespace Tests\Feature;

use App\Models\ChopdaRegistrationModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use Tests\Support\Libraries\FakeStripeService;

/**
 * @internal
 */
final class ChopdaPujanTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // The app's older migrations use MySQL-only SQL, so build just the tables
    // these tests touch on the in-memory SQLite test database.
    protected $migrate = false;
    protected $refresh = false;

    private const ADMIN = ['isAdminLoggedIn' => true, 'admin_role' => 'EVENTS', 'admin_name' => 'Test Admin'];

    private const WEBHOOK_SECRET = 'whsec_test';

    private FakeStripeService $stripe;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF is covered sitewide; these tests are about the controllers.
        $filters = config('Filters');
        unset($filters->globals['before']['csrf']);
        Factories::injectMock('config', 'Filters', $filters);

        // Card and bank transfer both on offer, against a fake Stripe, whatever
        // the local .env says.
        $this->setConfig('ChopdaPujan', ['cardPayments' => true, 'bankTransfer' => true]);

        $stripeConfig                = config('Stripe');
        $stripeConfig->secretKey     = 'sk_test_fake';
        $stripeConfig->chopdaPriceId = 'price_test_chopda';
        $stripeConfig->webhookSecret = self::WEBHOOK_SECRET;
        Factories::injectMock('config', 'Stripe', $stripeConfig);

        $this->stripe = new FakeStripeService();
        Services::injectMock('stripe', $this->stripe);

        $forge = \Config\Database::forge($this->DBGroup);

        foreach (['chopda_registrations', 'email_queue', 'stripe_webhook_events'] as $table) {
            $forge->dropTable($table, true);
        }

        require_once APPPATH . 'Database/Migrations/2026-10-23-120000_CreateChopdaRegistrations.php';
        (new \App\Database\Migrations\CreateChopdaRegistrations($forge))->up();
        require_once APPPATH . 'Database/Migrations/2026-10-24-090000_AddCardPaymentToChopdaRegistrations.php';
        (new \App\Database\Migrations\AddCardPaymentToChopdaRegistrations($forge))->up();

        $text = ['type' => 'TEXT', 'null' => true];

        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'to_email', 'to_name', 'subject', 'type', 'related_id', 'body_html', 'body_text', 'priority',
            'status', 'created_at', 'updated_at',
        ], $text));
        $forge->addKey('id', true);
        $forge->createTable('email_queue');

        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'stripe_event_id', 'event_type', 'api_version', 'livemode', 'payload_json', 'signature_header',
            'received_at', 'processed_at', 'process_status', 'process_error',
        ], $text));
        $forge->addKey('id', true);
        $forge->addUniqueKey('stripe_event_id');
        $forge->createTable('stripe_webhook_events');
    }

    protected function tearDown(): void
    {
        Factories::reset('config');
        Services::reset();

        parent::tearDown();
    }

    private function registration(array $overrides = []): int
    {
        return (new ChopdaRegistrationModel())->insert(array_merge([
            'registration_ref'  => 'CP26-' . strtoupper(bin2hex(random_bytes(3))),
            'first_name'        => 'Asha',
            'last_name'         => 'Chotai',
            'email'             => 'asha@example.com',
            'phone'             => '07700900123',
            'num_attendees'     => 3,
            'payment_reference' => 'CP26ACHOTAI',
            'payment_timing'    => 'will_pay',
            'payment_method'    => 'bank',
            'pay_token'         => bin2hex(random_bytes(24)),
            'amount'            => 35,
            'privacy_consent'   => 1,
            'status'            => 'registered',
        ], $overrides), true);
    }

    private function form(array $overrides = []): array
    {
        return array_merge([
            'form_time'         => time() - 30,
            'website'           => '',
            'first_name'        => 'Asha',
            'last_name'         => 'Chotai',
            'email'             => 'asha@example.com',
            'phone'             => '07700900123',
            'num_attendees'     => '4',
            'payment_method'    => 'bank',
            'payment_reference' => 'cp26 achotai',
            'payment_timing'    => 'will_pay',
            'privacy_consent'   => '1',
        ], $overrides);
    }

    private function queued(string $type): int
    {
        return $this->db->table('email_queue')->where('type', $type)->countAllResults();
    }

    private function find(int $id): array
    {
        return (new ChopdaRegistrationModel())->find($id);
    }

    private function setConfig(string $class, array $values): void
    {
        $config = config($class);
        foreach ($values as $key => $value) {
            $config->{$key} = $value;
        }
        Factories::injectMock('config', $class, $config);
    }

    /** Post a signed event to the webhook, as Stripe would. */
    private function webhook(string $type, array $session, ?string $eventId = null)
    {
        $payload = json_encode([
            'id'          => $eventId ?? 'evt_' . bin2hex(random_bytes(6)),
            'object'      => 'event',
            'type'        => $type,
            'api_version' => '2025-01-27.acacia',
            'livemode'    => false,
            'data'        => ['object' => $session],
        ]);
        $time = time();

        return $this->withHeaders([
            'Stripe-Signature' => 't=' . $time . ',v1=' . hash_hmac('sha256', $time . '.' . $payload, self::WEBHOOK_SECRET),
        ])->withBody($payload)->post('stripe/webhook');
    }

    /** A card registration with an open Checkout, as if the registrant had just been sent to Stripe. */
    private function cardRegistration(): array
    {
        $id = $this->registration(['payment_method' => 'card', 'payment_timing' => '']);
        (new \App\Services\ChopdaPaymentService())->checkoutUrl($this->find($id));

        return $this->find($id);
    }

    public function testRegisteringSavesOnePoojaAndEmailsBankDetails(): void
    {
        $this->post('chopda-pujan', $this->form())->assertRedirectTo(site_url('chopda-pujan/thank-you'));

        $row = (new ChopdaRegistrationModel())->first();
        $this->assertSame('4', (string) $row['num_attendees']);
        $this->assertSame('35.00', number_format((float) $row['amount'], 2, '.', ''));
        $this->assertSame('CP26 ACHOTAI', $row['payment_reference']);
        $this->assertSame('registered', $row['status']);

        $this->assertSame(1, $this->queued('chopda_registration_confirmation'));
        $this->assertSame(1, $this->queued('chopda_registration_notification'));

        $body = $this->db->table('email_queue')->where('type', 'chopda_registration_confirmation')->get()->getRowArray()['body_html'];
        $this->assertStringContainsString('21497995', $body);
        $this->assertStringContainsString('40-23-13', $body);
        $this->assertStringContainsString('Only 2 Yajmans can sit in 1 pooja', $body);
    }

    public function testAttendeesMustBeAtLeastOne(): void
    {
        $this->post('chopda-pujan', $this->form(['num_attendees' => '0']))->assertRedirect();

        $this->assertSame(0, (new ChopdaRegistrationModel())->countAllResults());
        $this->assertArrayHasKey('num_attendees', session('errors'));
    }

    public function testRegistrationClosesAfterTheClosingDate(): void
    {
        $config              = config('ChopdaPujan');
        $config->closingDate = '2020-01-01';
        Factories::injectMock('config', 'ChopdaPujan', $config);

        $this->get('chopda-pujan')->assertSee('Registration has closed');

        $this->post('chopda-pujan', $this->form())->assertRedirectTo(site_url('chopda-pujan'));
        $this->assertSame(0, (new ChopdaRegistrationModel())->countAllResults());
    }

    public function testThankYouPageNeedsTheRegisteringSession(): void
    {
        $id = $this->registration();

        $this->get('chopda-pujan/thank-you')->assertRedirectTo(site_url('chopda-pujan'));

        $result = $this->withSession(['chopda_registration_id' => $id])->get('chopda-pujan/thank-you');
        $result->assertOK();
        $result->assertSee('CP26ACHOTAI');
        $result->assertSee('21497995');
    }

    public function testAdminPagesAreForAdminAndEventsRoles(): void
    {
        $this->get('admin/chopda-pujan')->assertRedirect();

        $this->withSession(['isAdminLoggedIn' => true, 'admin_role' => 'MEMBERSHIP'])
            ->get('admin/chopda-pujan')
            ->assertRedirectTo('/admin/system/dashboard');

        $result = $this->withSession(self::ADMIN)->get('admin/chopda-pujan');
        $result->assertOK();
        $result->assertSee('Chopda Pujan Registrations');
    }

    public function testMarkingPaidConfirmsThePlaceOnlyOnce(): void
    {
        $id = $this->registration();

        $this->withSession(self::ADMIN)->post("admin/chopda-pujan/payment/{$id}")->assertRedirect();
        $row = (new ChopdaRegistrationModel())->find($id);
        $this->assertSame('paid', $row['status']);
        $this->assertSame('Test Admin', $row['payment_marked_by']);
        $this->assertSame(1, $this->queued('chopda_payment_received'));

        // Unpaid, then paid again: no second confirmation.
        $this->withSession(self::ADMIN)->post("admin/chopda-pujan/payment/{$id}");
        $this->withSession(self::ADMIN)->post("admin/chopda-pujan/payment/{$id}");
        $this->assertSame(1, $this->queued('chopda_payment_received'));
    }

    public function testReminderOnlyForUnpaidRegistrations(): void
    {
        $unpaid = $this->registration();
        $paid   = $this->registration(['status' => 'paid', 'payment_received' => 1, 'payment_received_at' => date('Y-m-d H:i:s')]);

        $this->withSession(self::ADMIN)->post("admin/chopda-pujan/remind/{$unpaid}");
        $this->withSession(self::ADMIN)->post("admin/chopda-pujan/remind/{$paid}");

        $this->assertSame(1, $this->queued('chopda_payment_reminder'));
        $this->assertSame('1', (string) (new ChopdaRegistrationModel())->find($unpaid)['reminder_count']);
    }

    /* ---------------- Card payments ---------------- */

    public function testCardRegistrationGoesStraightToStripeCheckout(): void
    {
        $result = $this->post('chopda-pujan', $this->form(['payment_method' => 'card']));
        $result->assertRedirectTo('https://checkout.stripe.test/cs_test_1');

        $row = (new ChopdaRegistrationModel())->first();
        $this->assertSame('card', $row['payment_method']);
        $this->assertSame($row['registration_ref'], $row['payment_reference']);
        $this->assertSame('cs_test_1', $row['stripe_checkout_session_id']);
        $this->assertSame('registered', $row['status']);

        $params = $this->stripe->created[0];
        $this->assertSame('price_test_chopda', $params['line_items'][0]['price']);
        $this->assertSame('chopda_pujan', $params['metadata']['purpose']);
        $this->assertSame((string) $row['id'], $params['metadata']['registration_id']);
        $this->assertSame('asha@example.com', $params['customer_email']);

        // Organisers hear about it now; the registrant only once it is paid or abandoned.
        $this->assertSame(1, $this->queued('chopda_registration_notification'));
        $this->assertSame(0, $this->queued('chopda_registration_confirmation'));
    }

    public function testReturningFromCheckoutConfirmsThePlaceAndTheWebhookDoesNotRepeatIt(): void
    {
        $registration = $this->cardRegistration();
        $session      = $this->stripe->complete('cs_test_1');

        $this->get('chopda-pujan/paid?session_id=cs_test_1')->assertRedirectTo(site_url('chopda-pujan/thank-you'));

        $row = $this->find($registration['id']);
        $this->assertSame('paid', $row['status']);
        $this->assertSame('Stripe', $row['payment_marked_by']);
        $this->assertSame('pi_test_1', $row['stripe_payment_intent_id']);
        $this->assertSame(1, $this->queued('chopda_payment_received'));

        $this->webhook('checkout.session.completed', $session)->assertStatus(200);
        $this->assertSame(1, $this->queued('chopda_payment_received'));
    }

    public function testWebhookAloneConfirmsACardPayment(): void
    {
        $registration = $this->cardRegistration();
        $session      = $this->stripe->complete('cs_test_1');

        $this->webhook('checkout.session.completed', $session, 'evt_paid')->assertStatus(200);

        $this->assertSame('paid', $this->find($registration['id'])['status']);
        $this->assertSame(1, $this->queued('chopda_payment_received'));
        $this->assertSame('ok', $this->db->table('stripe_webhook_events')->get()->getRowArray()['process_status']);

        // Stripe resends the same event: ignored.
        $this->webhook('checkout.session.completed', $session, 'evt_paid')->assertStatus(200);
        $this->assertSame(1, $this->queued('chopda_payment_received'));
    }

    public function testWebhookRejectsABadSignature(): void
    {
        $this->withHeaders(['Stripe-Signature' => 't=1,v1=nonsense'])
            ->withBody('{"id":"evt_x"}')
            ->post('stripe/webhook')
            ->assertStatus(400);
    }

    public function testAWrongAmountIsFlaggedForAdminsNotConfirmed(): void
    {
        $registration = $this->cardRegistration();
        $this->webhook('checkout.session.completed', $this->stripe->complete('cs_test_1', 350));

        $row = $this->find($registration['id']);
        $this->assertSame('0', (string) $row['payment_received']);
        $this->assertStringContainsString('does not match', $row['admin_notes']);
        $this->assertSame(0, $this->queued('chopda_payment_received'));
    }

    public function testExpiredCheckoutEmailsAPayLinkOnce(): void
    {
        $registration = $this->cardRegistration();
        $session      = $this->stripe->expire('cs_test_1');

        $this->webhook('checkout.session.expired', $session)->assertStatus(200);
        $this->webhook('checkout.session.expired', $session)->assertStatus(200);

        $this->assertSame(1, $this->queued('chopda_registration_confirmation'));
        $body = $this->db->table('email_queue')->where('type', 'chopda_registration_confirmation')->get()->getRowArray()['body_html'];
        $this->assertStringContainsString('chopda-pujan/pay/' . $registration['pay_token'], $body);
        $this->assertStringContainsString('was not completed', $body);
    }

    public function testPayLinkReusesTheOpenCheckout(): void
    {
        $registration = $this->cardRegistration();

        $this->get('chopda-pujan/pay/' . $registration['pay_token'])->assertRedirectTo('https://checkout.stripe.test/cs_test_1');
        $this->assertCount(1, $this->stripe->created);

        // Once that one expires, a fresh Checkout is opened.
        $this->stripe->expire('cs_test_1');
        $this->get('chopda-pujan/pay/' . $registration['pay_token'])->assertRedirectTo('https://checkout.stripe.test/cs_test_2');
        $this->assertSame('cs_test_2', $this->find($registration['id'])['stripe_checkout_session_id']);
    }

    public function testPayLinkForAPaidOrUnknownRegistrationDoesNotCharge(): void
    {
        $paid = $this->registration(['status' => 'paid', 'payment_received' => 1, 'payment_received_at' => date('Y-m-d H:i:s')]);

        $this->get('chopda-pujan/pay/' . $this->find($paid)['pay_token'])->assertRedirectTo(site_url('chopda-pujan/thank-you'));
        $this->get('chopda-pujan/pay/' . str_repeat('ab', 24))->assertRedirectTo(site_url('chopda-pujan'));
        $this->assertCount(0, $this->stripe->created);
    }

    public function testIfStripeIsDownTheRegistrationIsKeptAndThePayerTold(): void
    {
        $this->stripe->failCreates = true;

        $this->post('chopda-pujan', $this->form(['payment_method' => 'card']))
            ->assertRedirectTo(site_url('chopda-pujan/thank-you'));

        $this->assertSame(1, (new ChopdaRegistrationModel())->countAllResults());
        $this->assertStringContainsString('You have not been charged', session('error'));
    }

    public function testThankYouPageOffersCardWhenPaymentNotCompleted(): void
    {
        $registration = $this->cardRegistration();

        $result = $this->withSession(['chopda_registration_id' => $registration['id']])->get('chopda-pujan/thank-you');
        $result->assertSee('Your payment has not been completed');
        $result->assertSee('chopda-pujan/pay/' . $registration['pay_token']);
        $result->assertSee('21497995'); // bank transfer still offered
    }

    /* ---------------- Switching methods ---------------- */

    public function testBankTransferCanBeSwitchedOff(): void
    {
        $this->setConfig('ChopdaPujan', ['bankTransfer' => false]);

        $page = $this->get('chopda-pujan');
        $page->assertDontSee('Payment reference');
        $page->assertDontSee('id="method_bank"');

        // Card is the only way, whatever is posted.
        $this->post('chopda-pujan', $this->form(['payment_method' => 'bank']))
            ->assertRedirectTo('https://checkout.stripe.test/cs_test_1');
        $this->assertSame('card', (new ChopdaRegistrationModel())->first()['payment_method']);
    }

    public function testBankTransferComesBackWhileStripeIsNotConfigured(): void
    {
        $this->setConfig('ChopdaPujan', ['bankTransfer' => false]);
        $this->setConfig('Stripe', ['chopdaPriceId' => '']);

        $this->get('chopda-pujan')->assertSee('Payment reference');

        $this->post('chopda-pujan', $this->form(['payment_method' => 'card']))
            ->assertRedirectTo(site_url('chopda-pujan/thank-you'));
        $this->assertSame('bank', (new ChopdaRegistrationModel())->first()['payment_method']);
        $this->assertCount(0, $this->stripe->created);
    }

    public function testChoosingAPaymentMethodIsRequiredWhenBothAreOffered(): void
    {
        $this->post('chopda-pujan', $this->form(['payment_method' => '']))->assertRedirect();

        $this->assertSame(0, (new ChopdaRegistrationModel())->countAllResults());
        $this->assertArrayHasKey('payment_method', session('errors'));
    }
}
