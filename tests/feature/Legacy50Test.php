<?php

namespace Tests\Feature;

use App\Models\LegacyPledgeModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class Legacy50Test extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // The app's older migrations use MySQL-only SQL, so build just the tables
    // these tests touch on the in-memory SQLite test database.
    protected $migrate = false;
    protected $refresh = false;

    private const ADMIN = ['isAdminLoggedIn' => true, 'admin_role' => 'ADMIN', 'admin_name' => 'Test Admin'];

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF is covered sitewide; these tests are about the controllers.
        $filters = config('Filters');
        unset($filters->globals['before']['csrf']);
        \CodeIgniter\Config\Factories::injectMock('config', 'Filters', $filters);

        $forge = \Config\Database::forge($this->DBGroup);

        foreach (['legacy_pledges', 'email_queue', 'golf_registrations'] as $table) {
            $forge->dropTable($table, true);
        }

        require_once APPPATH . 'Database/Migrations/2026-10-10-120000_CreateLegacyPledges.php';
        (new \App\Database\Migrations\CreateLegacyPledges($forge))->up();

        $text = ['type' => 'TEXT', 'null' => true];

        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'to_email', 'to_name', 'subject', 'type', 'related_id', 'body_html', 'body_text', 'priority',
            'status', 'created_at', 'updated_at',
        ], $text));
        $forge->addKey('id', true);
        $forge->createTable('email_queue');

        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'registration_ref', 'team_name', 'status', 'created_at', 'updated_at',
            'p1_first_name', 'p1_last_name', 'p1_email', 'p1_phone', 'p1_handicap', 'p1_meal', 'p1_tshirt',
            'p2_first_name', 'p3_first_name', 'p4_first_name',
        ], $text));
        $forge->addKey('id', true);
        $forge->createTable('golf_registrations');
    }

    private function pledge(array $overrides = []): int
    {
        return (new LegacyPledgeModel())->insert(array_merge([
            'pledge_ref'            => 'L50-' . strtoupper(bin2hex(random_bytes(3))),
            'first_name'            => 'Asha',
            'last_name'             => 'Patel',
            'email'                 => 'asha@example.com',
            'phone'                 => '07700900123',
            'plaque_name'           => 'The Patel Family',
            'plaque_name_submitted' => 'The Patel Family',
            'gift_aid'              => 1,
            'house_name_number'     => '12',
            'postcode'              => 'HA2 6NG',
            'payment_reference'     => 'LCNL50 PATEL',
            'payment_timing'        => 'will_pay',
            'amount'                => 500,
            'confirmed_donation'    => 1,
            'privacy_consent'       => 1,
            'status'                => 'pledged',
        ], $overrides), true);
    }

    private function queued(string $type): int
    {
        return $this->db->table('email_queue')->where('type', $type)->countAllResults();
    }

    public function testAdminPagesAreAdminOnly(): void
    {
        $this->get('admin/legacy50')->assertRedirect();

        $this->withSession(['isAdminLoggedIn' => true, 'admin_role' => 'EVENTS'])
            ->get('admin/legacy50')
            ->assertRedirectTo('/admin/system/dashboard');

        $result = $this->withSession(self::ADMIN)->get('admin/legacy50');
        $result->assertOK();
        $result->assertSee('Legacy 50 Pledges');
    }

    public function testMarkingPaidThanksTheDonorOnlyOnce(): void
    {
        $id = $this->pledge();

        $this->withSession(self::ADMIN)->post("admin/legacy50/payment/{$id}")->assertRedirect();
        $row = (new LegacyPledgeModel())->find($id);
        $this->assertSame('paid', $row['status']);
        $this->assertSame('Test Admin', $row['payment_marked_by']);
        $this->assertSame(1, $this->queued('legacy50_payment_received'));

        // Unpaid, then paid again: no second thank-you.
        $this->withSession(self::ADMIN)->post("admin/legacy50/payment/{$id}");
        $this->withSession(self::ADMIN)->post("admin/legacy50/payment/{$id}");
        $this->assertSame(1, $this->queued('legacy50_payment_received'));
    }

    public function testReminderOnlyForUnpaidPledges(): void
    {
        $unpaid = $this->pledge();
        $paid   = $this->pledge(['status' => 'paid', 'payment_received' => 1, 'payment_received_at' => date('Y-m-d H:i:s')]);

        $this->withSession(self::ADMIN)->post("admin/legacy50/remind/{$unpaid}");
        $this->withSession(self::ADMIN)->post("admin/legacy50/remind/{$paid}");

        $this->assertSame(1, $this->queued('legacy50_payment_reminder'));
        $this->assertSame('1', (string) (new LegacyPledgeModel())->find($unpaid)['reminder_count']);
    }

    public function testPlaqueEditKeepsTheSubmittedName(): void
    {
        $id = $this->pledge();

        $this->withSession(self::ADMIN)->post("admin/legacy50/update/{$id}", [
            'plaque_name' => 'Asha  &  Raj Patel',
            'admin_notes' => 'Confirmed by phone',
        ])->assertRedirect();

        $row = (new LegacyPledgeModel())->find($id);
        $this->assertSame('Asha & Raj Patel', $row['plaque_name']);
        $this->assertSame('The Patel Family', $row['plaque_name_submitted']);
    }

    public function testGiftAidExportListsOnlyPaidGiftAidDonors(): void
    {
        $this->pledge(['last_name' => 'Unpaid']);
        $this->pledge(['last_name' => 'NoGiftAid', 'gift_aid' => 0, 'status' => 'paid', 'payment_received' => 1, 'payment_received_at' => '2026-10-05 10:00:00']);
        $this->pledge(['last_name' => 'Claimable', 'title' => 'Mrs', 'status' => 'paid', 'payment_received' => 1, 'payment_received_at' => '2026-10-05 10:00:00']);

        $csv = $this->withSession(self::ADMIN)->get('admin/legacy50/export/gift-aid')->getBody();

        $this->assertStringContainsString('Mrs,Asha,Claimable,12,"HA2 6NG",,,05/10/26,500.00', $csv);
        $this->assertStringNotContainsString('Unpaid', $csv);
        $this->assertStringNotContainsString('NoGiftAid', $csv);
    }

    public function testThankYouPageNeedsThePledgingSession(): void
    {
        $id = $this->pledge();

        $this->get('legacy50/thank-you')->assertRedirectTo(site_url('legacy50'));

        $result = $this->withSession(['legacy50_pledge_id' => $id])->get('legacy50/thank-you');
        $result->assertOK();
        $result->assertSee('LCNL50 PATEL');
        $result->assertSee('GB61MYMB23058018161893');
    }

    public function testGolfConfirmationIsNotPublicByReference(): void
    {
        $this->db->table('golf_registrations')->insert([
            'registration_ref' => 'GOLF26-SMITH',
            'team_name'        => 'Team',
            'p1_first_name'    => 'Jo',
            'p1_last_name'     => 'Smith',
            'p1_email'         => 'jo@example.com',
            'p1_phone'         => '07700900000',
            'p1_handicap'      => 10,
            'p1_meal'          => 'vegetarian',
            'created_at'       => '2026-07-01 10:00:00',
        ]);

        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('golf/confirmation/GOLF26-SMITH');
    }

    public function testGolfConfirmationShownToTheRegisteringSession(): void
    {
        $this->db->table('golf_registrations')->insert([
            'registration_ref' => 'GOLF26-SMITH',
            'team_name'        => 'Team',
            'p1_first_name'    => 'Jo',
            'p1_last_name'     => 'Smith',
            'p1_email'         => 'jo@example.com',
            'p1_phone'         => '07700900000',
            'p1_handicap'      => 10,
            'p1_meal'          => 'vegetarian',
            'created_at'       => '2026-07-01 10:00:00',
        ]);

        $result = $this->withSession(['golf_confirmed_refs' => ['GOLF26-SMITH']])->get('golf/confirmation/GOLF26-SMITH');
        $result->assertOK();
        $result->assertSee('jo@example.com');
    }
}
