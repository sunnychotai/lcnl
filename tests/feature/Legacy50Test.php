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
            'last_name'             => 'Chotai',
            'email'                 => 'asha@example.com',
            'phone'                 => '07700900123',
            'plaque_name'           => 'The Chotai Family',
            'plaque_name_submitted' => 'The Chotai Family',
            'gift_aid'              => 1,
            'house_name_number'     => '12',
            'town'                  => 'Harrow',
            'postcode'              => 'HA2 6NG',
            'payment_reference'     => 'LCNL50 CHOTAI',
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

    /** The edit form as an admin would post it unchanged, with overrides. */
    private function editForm(int $id, array $overrides = []): array
    {
        $row  = (new LegacyPledgeModel())->find($id);
        $form = array_intersect_key($row, array_flip([
            'title', 'first_name', 'last_name', 'email', 'phone', 'plaque_name', 'payment_reference',
            'payment_timing', 'comments', 'admin_notes', 'house_name_number', 'street', 'town', 'postcode',
        ]));
        $form = array_map(static fn($v) => (string) $v, $form);
        if ($row['gift_aid']) {
            $form['gift_aid'] = '1';
        }

        return array_merge($form, $overrides);
    }

    public function testPlaqueEditKeepsTheSubmittedName(): void
    {
        $id = $this->pledge();

        $this->withSession(self::ADMIN)->post("admin/legacy50/update/{$id}", $this->editForm($id, [
            'plaque_name' => 'Asha  &  Raj Chotai',
            'admin_notes' => 'Confirmed by phone',
        ]))->assertRedirectTo('/admin/legacy50');

        $row = (new LegacyPledgeModel())->find($id);
        $this->assertSame('Asha & Raj Chotai', $row['plaque_name']);
        $this->assertSame('The Chotai Family', $row['plaque_name_submitted']);
        $this->assertStringStartsWith("Confirmed by phone\n[", $row['admin_notes']);
        $this->assertStringEndsWith('Test Admin: changed plaque name', $row['admin_notes']);
    }

    public function testAdminCanCorrectDonorDetails(): void
    {
        $id = $this->pledge();

        $this->withSession(self::ADMIN)->post("admin/legacy50/update/{$id}", $this->editForm($id, [
            'title'             => 'Mrs',
            'email'             => 'asha.chotai@example.com',
            'payment_reference' => 'lcnl50 asha',
            'payment_timing'    => 'paid',
            'postcode'          => 'ha37ab',
        ]))->assertRedirectTo('/admin/legacy50');

        $row = (new LegacyPledgeModel())->find($id);
        $this->assertSame('Mrs', $row['title']);
        $this->assertSame('asha.chotai@example.com', $row['email']);
        $this->assertSame('LCNL50 ASHA', $row['payment_reference']);
        $this->assertSame('paid', $row['payment_timing']);
        $this->assertSame('HA3 7AB', $row['postcode']);
        $this->assertSame('pledged', $row['status']);
        $this->assertStringContainsString('changed title, email, payment reference, payment timing, postcode', $row['admin_notes']);
    }

    public function testSavingUnchangedAddsNoNote(): void
    {
        $id = $this->pledge();

        $this->withSession(self::ADMIN)->post("admin/legacy50/update/{$id}", $this->editForm($id));

        $this->assertNull((new LegacyPledgeModel())->find($id)['admin_notes']);
    }

    public function testTurningGiftAidOnNeedsTheDonorConfirmationAndAnAddress(): void
    {
        $id   = $this->pledge(['gift_aid' => 0, 'house_name_number' => null, 'town' => null, 'postcode' => null]);
        $form = $this->editForm($id, ['gift_aid' => '1', 'house_name_number' => '4', 'town' => 'Harrow', 'postcode' => 'HA2 6NG']);

        $this->withSession(self::ADMIN)->post("admin/legacy50/update/{$id}", $form)->assertRedirect();
        $this->assertSame('0', (string) (new LegacyPledgeModel())->find($id)['gift_aid']);

        $this->withSession(self::ADMIN)->post("admin/legacy50/update/{$id}", ['gift_aid_confirmed' => '1', 'town' => ''] + $form);
        $this->assertSame('0', (string) (new LegacyPledgeModel())->find($id)['gift_aid']);

        $this->withSession(self::ADMIN)->post("admin/legacy50/update/{$id}", ['gift_aid_confirmed' => '1'] + $form)
            ->assertRedirectTo('/admin/legacy50');

        $row = (new LegacyPledgeModel())->find($id);
        $this->assertSame('1', (string) $row['gift_aid']);
        $this->assertSame('Harrow', $row['town']);
        $this->assertNotNull($row['gift_aid_declared_at']);
        $this->assertSame(config('Legacy50')->giftAidVersion, $row['gift_aid_version']);
        $this->assertSame(config('Legacy50')->giftAidWording(), $row['gift_aid_wording']);
        $this->assertStringEndsWith('Test Admin: Gift Aid turned on (declaration confirmed by donor)', $row['admin_notes']);
    }

    public function testTurningGiftAidOffClearsTheDeclarationAndAddress(): void
    {
        $id = $this->pledge(['gift_aid_declared_at' => '2026-10-11 09:00:00', 'gift_aid_version' => 'LCF-GA-2026-10']);

        $form = $this->editForm($id);
        unset($form['gift_aid']);
        $this->withSession(self::ADMIN)->post("admin/legacy50/update/{$id}", $form)->assertRedirectTo('/admin/legacy50');

        $row = (new LegacyPledgeModel())->find($id);
        $this->assertSame('0', (string) $row['gift_aid']);
        $this->assertNull($row['gift_aid_declared_at']);
        $this->assertNull($row['house_name_number']);
        $this->assertNull($row['postcode']);
        $this->assertNull($row['town']);
        $this->assertStringEndsWith('Test Admin: Gift Aid turned off', $row['admin_notes']);
    }

    public function testEditPageShowsTheWholeForm(): void
    {
        $id = $this->pledge(['comments' => 'Happy to help']);

        $result = $this->withSession(self::ADMIN)->get("admin/legacy50/edit/{$id}");
        $result->assertOK();
        $result->assertSee('asha@example.com');
        $result->assertSee('Happy to help');
        $result->assertSee('name="gift_aid"');
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

    public function testPledgesStopAtTheCapAndCancelledOnesFreeAPlace(): void
    {
        $config           = config('Legacy50');
        $config->capacity = 2;
        \CodeIgniter\Config\Factories::injectMock('config', 'Legacy50', $config);

        $this->pledge();
        $this->pledge(['status' => 'cancelled']);
        $this->assertFalse((new LegacyPledgeModel())->isFull(2));

        $this->pledge();
        $this->assertTrue((new LegacyPledgeModel())->isFull(2));

        $page = $this->get('legacy50');
        $page->assertSee('Fully pledged');
        $page->assertSee('All pledges for this campaign have been made.');
        $page->assertDontSee('id="pledgeForm"');

        $this->post('legacy50', [])->assertRedirectTo(site_url('legacy50'));
        $this->assertSame(3, (new LegacyPledgeModel())->countAllResults());
    }

    public function testThankYouPageNeedsThePledgingSession(): void
    {
        $id = $this->pledge();

        $this->get('legacy50/thank-you')->assertRedirectTo(site_url('legacy50'));

        $result = $this->withSession(['legacy50_pledge_id' => $id])->get('legacy50/thank-you');
        $result->assertOK();
        $result->assertSee('LCNL50 CHOTAI');
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
