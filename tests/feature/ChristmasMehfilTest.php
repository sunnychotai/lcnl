<?php

namespace Tests\Feature;

use App\Models\MehfilBookingModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\ChristmasMehfil;
use Config\Services;

/**
 * @internal
 */
final class ChristmasMehfilTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // The app's older migrations use MySQL-only SQL, so build just the tables
    // these tests touch on the in-memory SQLite test database.
    protected $migrate = false;
    protected $refresh = false;

    private const ADMIN = ['isAdminLoggedIn' => true, 'admin_role' => 'EVENTS', 'admin_name' => 'Test Admin'];

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

        // Fixed dates, whatever today is: booking open, early bird running.
        $this->setConfig([
            'eventDate'     => '2099-12-13',
            'closingDate'   => '2099-12-10',
            'earlyBirdEnds' => '2099-12-04',
            'capacity'      => 100,
        ]);

        $forge = \Config\Database::forge($this->DBGroup);

        foreach (['mehfil_bookings', 'email_queue'] as $table) {
            $forge->dropTable($table, true);
        }

        require_once APPPATH . 'Database/Migrations/2026-11-16-120000_CreateMehfilBookings.php';
        (new \App\Database\Migrations\CreateMehfilBookings($forge))->up();

        $text = ['type' => 'TEXT', 'null' => true];

        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'to_email', 'to_name', 'subject', 'type', 'related_id', 'body_html', 'body_text', 'priority',
            'status', 'created_at', 'updated_at',
        ], $text));
        $forge->addKey('id', true);
        $forge->createTable('email_queue');
    }

    protected function tearDown(): void
    {
        Factories::reset('config');
        Services::reset();

        parent::tearDown();
    }

    private function setConfig(array $values): void
    {
        $config = config('ChristmasMehfil');
        foreach ($values as $key => $value) {
            $config->{$key} = $value;
        }
        Factories::injectMock('config', 'ChristmasMehfil', $config);
    }

    private function booking(array $overrides = []): int
    {
        return (new MehfilBookingModel())->insert(array_merge([
            'booking_ref'        => 'XMAS-ASHCHO',
            'first_name'         => 'Asha',
            'last_name'          => 'Chotai',
            'email'              => 'asha@example.com',
            'phone'              => '07700900123',
            'postcode'           => 'HA2 8AX',
            'is_member'          => 0,
            'individual_tickets' => 2,
            'tables'             => 0,
            'price_band'         => 'early',
            'individual_price'   => 30,
            'table_price'        => 270,
            'amount'             => 60,
            'privacy_consent'    => 1,
            'status'             => 'booked',
        ], $overrides), true);
    }

    private function form(array $overrides = []): array
    {
        return array_merge([
            'form_time'          => time() - 30,
            'website'            => '',
            'first_name'         => 'Ronak',
            'last_name'          => 'Pawar',
            'email'              => 'ronak@example.com',
            'phone'              => '07700 900123',
            'postcode'           => 'ha28ax',
            'is_member'          => '1',
            'individual_tickets' => '2',
            'tables'             => '1',
            'privacy_consent'    => '1',
        ], $overrides);
    }

    private function queued(string $type): int
    {
        return $this->db->table('email_queue')->where('type', $type)->countAllResults();
    }

    private function find(int $id): array
    {
        return (new MehfilBookingModel())->find($id);
    }

    /* =====================================================================
       Config: prices, dates and references
    ===================================================================== */

    public function testEarlyBirdPricesRunToTheEndOfTheFourthThenSwapOver(): void
    {
        $config                = new ChristmasMehfil();
        $config->earlyBirdEnds = '2026-12-04';
        $london                = new \DateTimeZone('Europe/London');

        $last  = $config->quote(2, 1, new \DateTimeImmutable('2026-12-04 23:59:00', $london));
        $first = $config->quote(2, 1, new \DateTimeImmutable('2026-12-05 00:00:01', $london));

        $this->assertSame(['band' => 'early', 'individual_price' => 30, 'table_price' => 270, 'amount' => 330, 'people' => 12], $last);
        $this->assertSame(['band' => 'standard', 'individual_price' => 35, 'table_price' => 300, 'amount' => 370, 'people' => 12], $first);
        $this->assertSame('5 December', $config->standardFromLabel());
    }

    public function testReferenceIsXmasPlusThreeLettersOfEachName(): void
    {
        $config = new ChristmasMehfil();

        $this->assertSame('XMAS-RONPAW', $config->referenceFor('Ronak', 'Pawar'));
        $this->assertSame('XMAS-SEAOBR', $config->referenceFor('Seán', "O'Brien"));
        $this->assertSame('XMAS-ALLI', $config->referenceFor('Al', 'Li'));
        $this->assertSame('XMAS-GUEST', $config->referenceFor('—', '…'));
    }

    public function testPayByIsFortyEightHoursAfterBookingInLondonTime(): void
    {
        $config = new ChristmasMehfil();

        // Stored in UTC; 1 Dec is GMT so the clock reads the same.
        $this->assertSame('9:45am on Thursday 3 December', $config->payByLabel('2026-12-01 09:45:00'));
        $this->assertTrue($config->isOverdue(
            ['status' => 'booked', 'payment_received' => 0, 'created_at' => '2026-12-01 09:45:00'],
            new \DateTimeImmutable('2026-12-03 09:46:00', new \DateTimeZone('Europe/London'))
        ));
        $this->assertFalse($config->isOverdue(
            ['status' => 'booked', 'payment_received' => 0, 'created_at' => '2026-12-01 09:45:00'],
            new \DateTimeImmutable('2026-12-03 09:44:00', new \DateTimeZone('Europe/London'))
        ));
    }

    /* =====================================================================
       Public booking
    ===================================================================== */

    public function testBookingSavesTicketsAtTheEarlyBirdPriceAndEmailsBankDetails(): void
    {
        $this->post('christmas-mehfil', $this->form())->assertRedirectTo(site_url('christmas-mehfil/thank-you'));

        $row = (new MehfilBookingModel())->first();
        $this->assertSame('XMAS-RONPAW', $row['booking_ref']);
        $this->assertSame('HA2 8AX', $row['postcode']);
        $this->assertSame('1', (string) $row['is_member']);
        $this->assertSame('early', $row['price_band']);
        $this->assertSame('330.00', number_format((float) $row['amount'], 2, '.', ''));
        $this->assertSame('booked', $row['status']);

        $this->assertSame(1, $this->queued('mehfil_booking_confirmation'));
        $this->assertSame(1, $this->queued('mehfil_booking_notification'));

        $body = $this->db->table('email_queue')->where('type', 'mehfil_booking_confirmation')->get()->getRowArray()['body_html'];
        $this->assertStringContainsString('21497995', $body);
        $this->assertStringContainsString('40-23-13', $body);
        $this->assertStringContainsString('XMAS-RONPAW', $body);
        $this->assertStringContainsString('within 48 hours to confirm your booking', $body);
    }

    public function testBookingAfterTheEarlyBirdUsesStandardPrices(): void
    {
        $this->setConfig(['earlyBirdEnds' => '2020-01-01']);

        $this->post('christmas-mehfil', $this->form())->assertRedirect();

        $row = (new MehfilBookingModel())->first();
        $this->assertSame('standard', $row['price_band']);
        $this->assertSame('370.00', number_format((float) $row['amount'], 2, '.', ''));
    }

    public function testASecondRonakPawarGetsANumberedReference(): void
    {
        $this->booking(['booking_ref' => 'XMAS-RONPAW', 'status' => 'cancelled']);

        $this->post('christmas-mehfil', $this->form())->assertRedirect();

        $this->assertSame(['XMAS-RONPAW', 'XMAS-RONPAW2'],
            array_column((new MehfilBookingModel())->orderBy('id')->findAll(), 'booking_ref'));
    }

    public function testABookingNeedsAtLeastOneTicketAndAValidPostcode(): void
    {
        $this->post('christmas-mehfil', $this->form(['individual_tickets' => '0', 'tables' => '0', 'postcode' => 'nowhere']))
            ->assertRedirect();

        $this->assertSame(0, (new MehfilBookingModel())->countAllResults());
        $this->assertArrayHasKey('tickets', session('errors'));
        $this->assertArrayHasKey('postcode', session('errors'));
    }

    public function testBookingStopsAtCapacityCountingTablesAsTenAndCancelledOnesFree(): void
    {
        $this->setConfig(['capacity' => 20]);
        $this->booking(['booking_ref' => 'XMAS-ONE', 'individual_tickets' => 0, 'tables' => 1]);               // 10 seats
        $this->booking(['booking_ref' => 'XMAS-TWO', 'individual_tickets' => 9, 'status' => 'cancelled']);   // frees its seats

        // 2 + 10 = 12 > 10 left.
        $this->post('christmas-mehfil', $this->form())->assertRedirect();
        $this->assertStringContainsString('only 10 places left', session('errors')['tickets']);

        $this->post('christmas-mehfil', $this->form(['individual_tickets' => '0', 'tables' => '1']))
            ->assertRedirectTo(site_url('christmas-mehfil/thank-you'));

        $this->get('christmas-mehfil')->assertSee('Sold out');
    }

    public function testBookingClosesAfterTheClosingDate(): void
    {
        $this->setConfig(['closingDate' => '2020-01-01']);

        $this->get('christmas-mehfil')->assertSee('Online booking has closed');
        $this->post('christmas-mehfil', $this->form())->assertRedirectTo(site_url('christmas-mehfil'));
        $this->assertSame(0, (new MehfilBookingModel())->countAllResults());
    }

    public function testThankYouPageNeedsTheBookingSession(): void
    {
        $id = $this->booking();

        $this->get('christmas-mehfil/thank-you')->assertRedirectTo(site_url('christmas-mehfil'));

        $result = $this->withSession(['mehfil_booking_id' => $id])->get('christmas-mehfil/thank-you');
        $result->assertOK();
        $result->assertSee('XMAS-ASHCHO');
        $result->assertSee('21497995');
        $result->assertSee('your order will be cancelled');
    }

    /* =====================================================================
       Admin
    ===================================================================== */

    public function testAdminPagesAreForAdminAndEventsRoles(): void
    {
        $this->get('admin/christmas-mehfil')->assertRedirect();

        $this->withSession(['isAdminLoggedIn' => true, 'admin_role' => 'MEMBERSHIP'])
            ->get('admin/christmas-mehfil')
            ->assertRedirectTo('/admin/system/dashboard');

        $this->booking();
        $result = $this->withSession(self::ADMIN)->get('admin/christmas-mehfil');
        $result->assertOK();
        $result->assertSee('XMAS-ASHCHO');
    }

    public function testMarkingPaidConfirmsTheBookingOnlyOnce(): void
    {
        $id = $this->booking();

        $this->withSession(self::ADMIN)->post("admin/christmas-mehfil/payment/{$id}")->assertRedirect();
        $this->assertSame('paid', $this->find($id)['status']);
        $this->assertSame('Test Admin', $this->find($id)['payment_marked_by']);

        // Unmark and mark again: still only one confirmation email.
        $this->withSession(self::ADMIN)->post("admin/christmas-mehfil/payment/{$id}")->assertRedirect();
        $this->withSession(self::ADMIN)->post("admin/christmas-mehfil/payment/{$id}")->assertRedirect();

        $this->assertSame(1, $this->queued('mehfil_payment_received'));
    }

    public function testCancellingEmailsTheBookerOnlyWhenAsked(): void
    {
        $quiet = $this->booking(['booking_ref' => 'XMAS-ONE']);
        $told  = $this->booking(['booking_ref' => 'XMAS-TWO']);

        $this->withSession(self::ADMIN)->post("admin/christmas-mehfil/cancel/{$quiet}")->assertRedirect();
        $this->withSession(self::ADMIN)->post("admin/christmas-mehfil/cancel/{$told}", ['notify' => '1'])->assertRedirect();

        $this->assertSame('cancelled', $this->find($quiet)['status']);
        $this->assertSame('cancelled', $this->find($told)['status']);
        $this->assertSame(1, $this->queued('mehfil_booking_cancelled'));
    }

    public function testEditingTicketsRepricesAtTheBookingsOwnPrices(): void
    {
        $id = $this->booking(); // early bird, 2 × £30

        $this->setConfig(['earlyBirdEnds' => '2020-01-01']); // now standard prices

        $this->withSession(self::ADMIN)->post("admin/christmas-mehfil/update/{$id}", [
            'individual_tickets' => '1', 'tables' => '1', 'admin_notes' => '',
        ])->assertRedirect();

        $this->assertSame('300.00', number_format((float) $this->find($id)['amount'], 2, '.', ''));
    }
}
