<?php

namespace Tests\Feature;

use App\Models\ChopdaRegistrationModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

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

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF is covered sitewide; these tests are about the controllers.
        $filters = config('Filters');
        unset($filters->globals['before']['csrf']);
        Factories::injectMock('config', 'Filters', $filters);

        $forge = \Config\Database::forge($this->DBGroup);

        foreach (['chopda_registrations', 'email_queue'] as $table) {
            $forge->dropTable($table, true);
        }

        require_once APPPATH . 'Database/Migrations/2026-10-23-120000_CreateChopdaRegistrations.php';
        (new \App\Database\Migrations\CreateChopdaRegistrations($forge))->up();

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
            'payment_reference' => 'cp26 achotai',
            'payment_timing'    => 'will_pay',
            'privacy_consent'   => '1',
        ], $overrides);
    }

    private function queued(string $type): int
    {
        return $this->db->table('email_queue')->where('type', $type)->countAllResults();
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
}
