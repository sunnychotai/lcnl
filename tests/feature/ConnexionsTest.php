<?php

namespace Tests\Feature;

use App\Models\ConnexionsAttendeeModel;
use App\Models\ConnexionsBookingModel;
use App\Services\ConnexionsBookingService;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use Tests\Support\Libraries\FakeStripeService;

/**
 * @internal
 */
final class ConnexionsTest extends CIUnitTestCase
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

        // The form allows three posts a minute per IP, and the limit would
        // carry from one test to the next.
        Services::injectMock('throttler', new class () {
            public function check(string $key, int $capacity, int $seconds, int $cost = 1): bool
            {
                return true;
            }
        });

        // Fixed dates, whatever today is: booking open.
        $this->setConfig('Connexions', [
            'eventDate'    => '2099-12-16',
            'closingDate'  => '2099-12-14',
            'capacity'     => ['girl' => 50, 'boy' => 50],
            'notifyEmails' => ['info@lcnl.org'],
        ]);
        $this->setConfig('Stripe', [
            'secretKey'         => 'sk_test_fake',
            'connexionsPriceId' => 'price_test_connexions',
            'webhookSecret'     => self::WEBHOOK_SECRET,
        ]);

        $this->stripe = new FakeStripeService();
        Services::injectMock('stripe', $this->stripe);

        $forge = \Config\Database::forge($this->DBGroup);

        foreach (['connexions_attendees', 'connexions_bookings', 'email_queue', 'stripe_webhook_events'] as $table) {
            $forge->dropTable($table, true);
        }

        require_once APPPATH . 'Database/Migrations/2026-11-25-120000_CreateConnexionsBookings.php';
        (new \App\Database\Migrations\CreateConnexionsBookings($forge))->up();

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

    private function setConfig(string $class, array $values): void
    {
        $config = config($class);
        foreach ($values as $key => $value) {
            $config->{$key} = $value;
        }
        Factories::injectMock('config', $class, $config);
    }

    private function attendee(string $first, string $last, string $email): array
    {
        return ['first_name' => $first, 'last_name' => $last, 'email' => $email, 'phone' => '07700 900' . random_int(100, 999)];
    }

    /** The form as a booker coming themselves with one friend of each: 2 girls, 1 boy. */
    private function form(array $overrides = []): array
    {
        return array_replace_recursive([
            'form_time'       => time() - 30,
            'website'         => '',
            'first_name'      => 'Priya',
            'last_name'       => 'Shah',
            'email'           => 'priya@example.com',
            'phone'           => '07700 900123',
            'is_member'       => '1',
            'girl_tickets'    => '2',
            'boy_tickets'     => '1',
            'eligible'        => '1',
            'privacy_consent' => '1',
            'attendees'       => [
                'girl' => [
                    1 => $this->attendee('Priya', 'Shah', 'PRIYA@example.com'),
                    2 => $this->attendee('Mira', 'Patel', 'mira@example.com'),
                    // Beyond the tickets chosen: ignored.
                    3 => ['first_name' => 'Ghost', 'last_name' => '', 'email' => 'nope', 'phone' => ''],
                ],
                'boy'  => [
                    1 => $this->attendee('Raj', 'Kotecha', 'raj@example.com'),
                ],
            ],
        ], $overrides);
    }

    /** A booking straight into the table. */
    private function booking(array $overrides = [], array $attendees = [['girl', 'asha@example.com']]): int
    {
        $model = new ConnexionsBookingModel();
        $girls = count(array_filter($attendees, static fn(array $a): bool => $a[0] === 'girl'));
        $boys  = count($attendees) - $girls;

        $id = $model->insert(array_merge([
            'booking_ref'           => $model->generateRef('CNX26'),
            'first_name'            => 'Asha',
            'last_name'             => 'Chotai',
            'email'                 => 'asha@example.com',
            'phone'                 => '07700900123',
            'eligibility_confirmed' => 1,
            'privacy_consent'       => 1,
            'girl_tickets'          => $girls,
            'boy_tickets'           => $boys,
            'ticket_price'          => 20,
            'amount'                => 20 * count($attendees),
            'pay_token'             => $model->generatePayToken(),
            'hold_until'            => date('Y-m-d H:i:s', time() + 3600),
            'status'                => 'pending',
        ], $overrides), true);

        $pos = ['girl' => 0, 'boy' => 0];
        foreach ($attendees as [$gender, $email]) {
            (new ConnexionsAttendeeModel())->insert([
                'booking_id' => $id, 'gender' => $gender, 'position' => ++$pos[$gender],
                'first_name' => 'A' . $pos[$gender], 'last_name' => 'Person', 'email' => $email, 'phone' => '07700900000',
            ]);
        }

        return $id;
    }

    private function paidBooking(array $attendees = [['girl', 'asha@example.com']]): int
    {
        return $this->booking(['status' => 'paid', 'payment_received' => 1, 'payment_received_at' => date('Y-m-d H:i:s'),
            'payment_marked_by' => 'Stripe', 'hold_until' => null], $attendees);
    }

    private function find(int $id): array
    {
        return (new ConnexionsBookingModel())->find($id);
    }

    private function queued(string $type): int
    {
        return $this->db->table('email_queue')->where('type', $type)->countAllResults();
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

    /* =====================================================================
       Booking
    ===================================================================== */

    public function testBookingSavesEveryAttendeeAndGoesStraightToStripe(): void
    {
        $this->post('connexions', $this->form())->assertRedirectTo('https://checkout.stripe.test/cs_test_1');

        $booking = (new ConnexionsBookingModel())->first();
        $this->assertSame('pending', $booking['status']);
        $this->assertSame('2', (string) $booking['girl_tickets']);
        $this->assertSame('1', (string) $booking['boy_tickets']);
        $this->assertSame(60.0, (float) $booking['amount']);
        $this->assertSame('1', (string) $booking['is_member']);
        $this->assertSame('cs_test_1', $booking['stripe_checkout_session_id']);
        $this->assertGreaterThan(date('Y-m-d H:i:s'), $booking['hold_until']);

        $attendees = (new ConnexionsAttendeeModel())->forBooking((int) $booking['id']);
        $this->assertSame(['Priya', 'Mira', 'Raj'], array_column($attendees, 'first_name'));
        $this->assertSame(['girl', 'girl', 'boy'], array_column($attendees, 'gender'));

        $params = $this->stripe->created[0];
        $this->assertSame('price_test_connexions', $params['line_items'][0]['price']);
        $this->assertSame(3, $params['line_items'][0]['quantity']);
        $this->assertSame('connexions', $params['metadata']['purpose']);
        $this->assertSame((string) $booking['id'], $params['metadata']['booking_id']);
        $this->assertSame('priya@example.com', $params['customer_email']);

        // Nobody is emailed until it is paid.
        $this->assertSame(0, $this->db->table('email_queue')->countAllResults());
    }

    public function testEveryTicketNeedsFullAttendeeDetails(): void
    {
        $form = $this->form();
        $form['attendees']['girl'][2]['email'] = 'not-an-email';
        unset($form['attendees']['boy'][1]['phone']);

        $this->post('connexions', $form)->assertRedirect();

        $errors = session('errors');
        $this->assertSame('Girl 2: please enter a valid email address.', $errors['attendees.girl.2.email']);
        $this->assertSame('Boy 1: please enter their mobile number.', $errors['attendees.boy.1.phone']);
        $this->assertArrayNotHasKey('attendees.girl.3.last_name', $errors);
        $this->assertSame(0, (new ConnexionsBookingModel())->countAllResults());
    }

    public function testAtLeastOneTicketAndTheEligibilityTickAreRequired(): void
    {
        $this->post('connexions', $this->form(['girl_tickets' => '0', 'boy_tickets' => '0', 'eligible' => '']));

        $errors = session('errors');
        $this->assertArrayHasKey('tickets', $errors);
        $this->assertStringContainsString('Gujarati singles aged 25–40', $errors['eligible']);
    }

    public function testNoMoreThanFivePerGender(): void
    {
        $this->post('connexions', $this->form(['girl_tickets' => '6']));

        $this->assertSame('Please choose up to 5 girl tickets.', session('errors')['girl_tickets']);
    }

    public function testGirlsAndBoysHaveSeparateCapacities(): void
    {
        $this->setConfig('Connexions', ['capacity' => ['girl' => 3, 'boy' => 50]]);
        $this->paidBooking([['girl', 'a@example.com'], ['girl', 'b@example.com']]);
        // An abandoned checkout holds nothing.
        $this->booking(['hold_until' => date('Y-m-d H:i:s', time() - 60)], [['girl', 'c@example.com'], ['girl', 'd@example.com']]);

        // Two girls wanted, one place left.
        $this->post('connexions', $this->form());
        $this->assertStringContainsString('only 1 place left for girls', session('errors')['tickets']);

        // One girl and a boy is fine.
        $form = $this->form(['girl_tickets' => '1']);
        $this->post('connexions', $form)->assertRedirectTo('https://checkout.stripe.test/cs_test_1');

        // Girls are now sold out (that booking is holding the last place), boys are not.
        $page = $this->get('connexions');
        $page->assertSee('Girl tickets are sold out.');
        $page->assertSee('Boy tickets');
    }

    public function testTheLaterOfTwoRacingBookingsLosesTheLastPlace(): void
    {
        $this->setConfig('Connexions', ['capacity' => ['girl' => 2, 'boy' => 50]]);
        $first  = $this->booking([], [['girl', 'a@example.com'], ['girl', 'b@example.com']]);
        $second = $this->booking([], [['girl', 'c@example.com']]);

        $service = new ConnexionsBookingService();
        $this->assertFalse($service->overbooked($this->find($first)));
        $this->assertTrue($service->overbooked($this->find($second)));
    }

    public function testBookingClosesAfterTheClosingDateAndWithoutAStripePrice(): void
    {
        $this->setConfig('Stripe', ['connexionsPriceId' => '']);
        $this->get('connexions')->assertSee('Booking opens soon');
        $this->post('connexions', $this->form())->assertRedirectTo(site_url('connexions'));

        $this->setConfig('Stripe', ['connexionsPriceId' => 'price_test_connexions']);
        $this->setConfig('Connexions', ['closingDate' => '2000-01-01']);
        $this->get('connexions')->assertSee('Online booking has closed');
        $this->assertSame(0, (new ConnexionsBookingModel())->countAllResults());
    }

    public function testThankYouPageNeedsTheBookingSession(): void
    {
        $id = $this->booking();

        $this->get('connexions/thank-you')->assertRedirectTo(site_url('connexions'));

        $result = $this->withSession(['connexions_booking_id' => $id])->get('connexions/thank-you');
        $result->assertOK();
        $result->assertSee('Your payment has not been completed');
        $result->assertSee('connexions/pay/' . $this->find($id)['pay_token']);
    }

    /* =====================================================================
       Payment and emails
    ===================================================================== */

    public function testPayingEmailsTheBookerEveryAttendeeAndTheOrganisersOnce(): void
    {
        $this->post('connexions', $this->form());
        $booking = (new ConnexionsBookingModel())->first();
        $session = $this->stripe->complete('cs_test_1', 6000);

        $this->get('connexions/paid?session_id=cs_test_1')->assertRedirectTo(site_url('connexions/thank-you'));

        $row = $this->find((int) $booking['id']);
        $this->assertSame('paid', $row['status']);
        $this->assertSame('Stripe', $row['payment_marked_by']);
        $this->assertSame('pi_test_1', $row['stripe_payment_intent_id']);

        // Booker (who is also Girl 1, so gets no second email), Girl 2 and Boy 1.
        $this->assertSame(1, $this->queued('connexions_booking_confirmed'));
        $this->assertSame(2, $this->queued('connexions_attendee_ticket'));
        $this->assertSame(1, $this->queued('connexions_booking_notification'));

        $to = array_column($this->db->table('email_queue')->where('type', 'connexions_attendee_ticket')->get()->getResultArray(), 'to_email');
        sort($to);
        $this->assertSame(['mira@example.com', 'raj@example.com'], $to);

        $confirmed = $this->db->table('email_queue')->where('type', 'connexions_booking_confirmed')->get()->getRowArray()['body_html'];
        foreach (['Thank you for booking', 'no refunds', 'bring this confirmation email', 'Mira', 'Kotecha', $booking['booking_ref']] as $text) {
            $this->assertStringContainsString($text, $confirmed);
        }

        $ticket = $this->db->table('email_queue')->where('to_email', 'mira@example.com')->get()->getRowArray()['body_html'];
        $this->assertStringContainsString('Priya Shah', $ticket);
        $this->assertStringNotContainsString('Kotecha', $ticket);

        // The webhook arriving after the return changes nothing.
        $this->webhook('checkout.session.completed', $session)->assertStatus(200);
        $this->assertSame(1, $this->queued('connexions_booking_confirmed'));
        $this->assertSame(2, $this->queued('connexions_attendee_ticket'));

        $this->withSession(['connexions_booking_id' => $booking['id']])->get('connexions/thank-you')
            ->assertSee('Please bring your confirmation email with you on the day');
    }

    public function testWebhookAloneConfirmsAPayment(): void
    {
        $this->post('connexions', $this->form());
        $id = (int) (new ConnexionsBookingModel())->first()['id'];

        $this->webhook('checkout.session.completed', $this->stripe->complete('cs_test_1', 6000), 'evt_paid')->assertStatus(200);

        $this->assertSame('paid', $this->find($id)['status']);
        $this->assertSame('ok', $this->db->table('stripe_webhook_events')->get()->getRowArray()['process_status']);
        $this->assertSame(1, $this->queued('connexions_booking_confirmed'));
    }

    public function testAWrongAmountIsFlaggedForAdminsNotConfirmed(): void
    {
        $this->post('connexions', $this->form());
        $id = (int) (new ConnexionsBookingModel())->first()['id'];

        $this->webhook('checkout.session.completed', $this->stripe->complete('cs_test_1', 2000));

        $row = $this->find($id);
        $this->assertSame('pending', $row['status']);
        $this->assertStringContainsString('does not match', $row['admin_notes']);
        $this->assertSame(0, $this->queued('connexions_booking_confirmed'));
    }

    public function testExpiredCheckoutEmailsAPayLinkOnce(): void
    {
        $this->post('connexions', $this->form());
        $booking = (new ConnexionsBookingModel())->first();
        $session = $this->stripe->expire('cs_test_1');

        $this->webhook('checkout.session.expired', $session)->assertStatus(200);
        $this->webhook('checkout.session.expired', $session)->assertStatus(200);

        $this->assertSame(1, $this->queued('connexions_payment_incomplete'));
        $body = $this->db->table('email_queue')->where('type', 'connexions_payment_incomplete')->get()->getRowArray()['body_html'];
        $this->assertStringContainsString('connexions/pay/' . $booking['pay_token'], $body);
    }

    public function testPayLinkReopensCheckoutWhilePlacesLastAndRefusesOnceTheyHaveGone(): void
    {
        $this->setConfig('Connexions', ['capacity' => ['girl' => 2, 'boy' => 50]]);
        $id    = $this->booking([], [['girl', 'a@example.com'], ['girl', 'b@example.com']]);
        $token = $this->find($id)['pay_token'];

        // Hold still running: Checkout opens.
        $this->get('connexions/pay/' . $token)->assertRedirectTo('https://checkout.stripe.test/cs_test_1');

        // The Checkout expires, the hold runs out and someone else books the girls' places.
        $this->stripe->expire('cs_test_1');
        (new ConnexionsBookingModel())->update($id, ['hold_until' => date('Y-m-d H:i:s', time() - 60)]);
        $this->paidBooking([['girl', 'c@example.com']]);

        $this->get('connexions/pay/' . $token)->assertRedirectTo(site_url('connexions/thank-you'));
        $this->assertStringContainsString('only 1 place left for girls', session('error'));
        $this->assertCount(1, $this->stripe->created);
    }

    public function testPayLinkForAPaidOrUnknownBookingDoesNotCharge(): void
    {
        $paid = $this->paidBooking();

        $this->get('connexions/pay/' . $this->find($paid)['pay_token'])->assertRedirectTo(site_url('connexions/thank-you'));
        $this->get('connexions/pay/' . str_repeat('ab', 24))->assertRedirectTo(site_url('connexions'));
        $this->assertCount(0, $this->stripe->created);
    }

    public function testIfStripeIsDownTheBookingIsKeptAndThePayerTold(): void
    {
        $this->stripe->failCreates = true;

        $this->post('connexions', $this->form())->assertRedirectTo(site_url('connexions/thank-you'));

        $this->assertSame(1, (new ConnexionsBookingModel())->countAllResults());
        $this->assertStringContainsString('You have not been charged', session('error'));
    }

    public function testChopdaCheckoutsStillGoToChopda(): void
    {
        // A Checkout with another purpose is not ours, so nothing happens here.
        $id = $this->booking();
        $this->webhook('checkout.session.completed', [
            'id' => 'cs_other', 'object' => 'checkout.session', 'payment_status' => 'paid', 'amount_total' => 2000,
            'currency' => 'gbp', 'metadata' => ['purpose' => 'chopda_pujan', 'registration_id' => '999999'],
        ]);

        $this->assertSame('pending', $this->find($id)['status']);
    }

    /* =====================================================================
       Admin
    ===================================================================== */

    public function testAdminPagesAreForAdminAndEventsRoles(): void
    {
        $this->get('admin/connexions')->assertRedirect();

        $this->withSession(['isAdminLoggedIn' => true, 'admin_role' => 'MEMBERSHIP'])
            ->get('admin/connexions')
            ->assertRedirectTo('/admin/system/dashboard');

        $result = $this->withSession(self::ADMIN)->get('admin/connexions');
        $result->assertOK();
        $result->assertSee('Connexions Bookings');
    }

    public function testRepeatAttendeeEmailsAreFlaggedButAllowed(): void
    {
        $this->paidBooking([['girl', 'same@example.com']]);
        $this->paidBooking([['girl', 'Same@Example.com'], ['boy', 'other@example.com']]);
        // An abandoned booking doesn't count as a repeat.
        $this->booking(['hold_until' => date('Y-m-d H:i:s', time() - 60)], [['boy', 'other@example.com']]);

        $result = $this->withSession(self::ADMIN)->get('admin/connexions');
        $result->assertOK();
        $this->assertSame(2, substr_count($result->getBody(), 'This email is also on another booking'));
    }

    public function testDoorListShowsOnlyPaidAttendees(): void
    {
        $this->paidBooking([['girl', 'paid@example.com']]);
        $this->booking([], [['boy', 'unpaid@example.com']]);

        $result = $this->withSession(self::ADMIN)->get('admin/connexions/attendees');
        $result->assertOK();
        $result->assertSee('1 attendees');
        $result->assertSee('(1 girls, 0 boys)');
    }

    public function testAdminCanCorrectDetailsAndResendTheConfirmations(): void
    {
        $id       = $this->paidBooking([['girl', 'asha@example.com'], ['boy', 'typo@exmaple.com']]);
        $attendee = (new ConnexionsAttendeeModel())->forBooking($id)[1];

        $this->withSession(self::ADMIN)->post("admin/connexions/update/{$id}", [
            'first_name' => 'Asha', 'last_name' => 'Chotai', 'email' => 'asha@example.com', 'phone' => '07700900123',
            'admin_notes' => 'Fixed Boy 1 email',
            'attendees'  => [
                (new ConnexionsAttendeeModel())->forBooking($id)[0]['id'] => ['first_name' => 'A1', 'last_name' => 'Person', 'email' => 'asha@example.com', 'phone' => '07700900000'],
                $attendee['id'] => ['first_name' => 'Dev', 'last_name' => 'Lakhani', 'email' => 'dev@example.com', 'phone' => '07700900001'],
            ],
        ])->assertRedirectTo(site_url('admin/connexions'));

        $this->assertSame('dev@example.com', (new ConnexionsAttendeeModel())->find($attendee['id'])['email']);

        $this->withSession(self::ADMIN)->post("admin/connexions/resend/{$id}")->assertRedirect();
        $this->assertSame(1, $this->queued('connexions_booking_confirmed'));
        $this->assertSame(1, $this->db->table('email_queue')->where('to_email', 'dev@example.com')->countAllResults());
    }

    public function testAdminMarkPaidSendsTheConfirmationsOnlyOnce(): void
    {
        $id = $this->booking();

        $this->withSession(self::ADMIN)->post("admin/connexions/payment/{$id}")->assertRedirect();
        $this->withSession(self::ADMIN)->post("admin/connexions/payment/{$id}")->assertRedirect();

        $this->assertSame('Test Admin', $this->find($id)['payment_marked_by']);
        $this->assertSame(1, $this->queued('connexions_booking_confirmed'));
    }

    public function testCancellingFreesThePlaces(): void
    {
        $this->setConfig('Connexions', ['capacity' => ['girl' => 1, 'boy' => 50]]);
        $id = $this->paidBooking([['girl', 'a@example.com']]);
        $this->assertSame(0, (new ConnexionsBookingService())->placesLeft()['girl']);

        $this->withSession(self::ADMIN)->post("admin/connexions/cancel/{$id}")->assertRedirect();
        $this->assertSame(1, (new ConnexionsBookingService())->placesLeft()['girl']);

        $this->withSession(self::ADMIN)->post("admin/connexions/reinstate/{$id}")->assertRedirect();
        $this->assertSame('paid', $this->find($id)['status']);
    }

    public function testHomeBannerShowsUntilTheEventDay(): void
    {
        $banner = view('connexions/_home_banner');
        $this->assertStringContainsString('Singles Mix &amp; Mingle', $banner);
        $this->assertStringContainsString('Book your place', $banner);

        $this->setConfig('Connexions', ['eventDate' => '2000-01-01', 'closingDate' => '2000-01-01']);
        $this->assertStringNotContainsString('connexions-feature', view('connexions/_home_banner'));
    }
}
