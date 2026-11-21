<?php

namespace Tests\Feature;

use App\Libraries\GroupEmail\PlaceholderRenderer;
use App\Services\GroupEmailService;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * @internal
 */
final class GroupEmailTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // The app's older migrations use MySQL-only SQL, so build just the tables
    // these tests touch on the in-memory SQLite test database.
    protected $migrate = false;
    protected $refresh = false;

    private const ADMIN  = ['isAdminLoggedIn' => true, 'admin_role' => 'ADMIN', 'admin_name' => 'Test Admin'];
    private const EVENTS = ['isAdminLoggedIn' => true, 'admin_role' => 'EVENTS', 'admin_name' => 'Events Admin'];

    private int $eventId;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF is covered sitewide; these tests are about the controllers.
        $filters = config('Filters');
        unset($filters->globals['before']['csrf']);
        Factories::injectMock('config', 'Filters', $filters);

        $forge = \Config\Database::forge($this->DBGroup);
        foreach (['events', 'event_registrations', 'email_queue', 'group_emails'] as $table) {
            $forge->dropTable($table, true);
        }

        $text = ['type' => 'TEXT', 'null' => true];

        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'title', 'slug', 'description', 'event_date', 'location', 'time_from', 'time_to', 'max_headcount',
            'created_at', 'updated_at',
        ], $text));
        $forge->addKey('id', true);
        $forge->createTable('events');

        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'event_id', 'first_name', 'last_name', 'email', 'phone', 'num_participants', 'num_guests', 'notes',
            'status', 'member_id', 'is_lcnl_member', 'agreed_terms', 'ip_address', 'created_at', 'updated_at',
        ], $text));
        $forge->addKey('id', true);
        $forge->createTable('event_registrations');

        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'to_email', 'to_name', 'subject', 'type', 'related_id', 'body_html', 'body_text', 'headers_json',
            'priority', 'status', 'attempts', 'last_error', 'scheduled_at', 'sent_at', 'created_at', 'updated_at',
        ], $text));
        $forge->addKey('id', true);
        $forge->createTable('email_queue');

        // The real migration: creates group_emails, adds email_queue.group_email_id.
        require_once APPPATH . 'Database/Migrations/2026-11-21-120000_CreateGroupEmails.php';
        (new \App\Database\Migrations\CreateGroupEmails($forge))->up();

        $this->db->table('events')->insert([
            'title'      => 'Diwali Dinner',
            'slug'       => 'diwali-dinner',
            'event_date' => '2099-11-14',
            'location'   => 'Dhamecha Lohana Centre',
            'time_from'  => '19:00:00',
            'time_to'    => '23:00:00',
        ]);
        $this->eventId = (int) $this->db->insertID();
    }

    protected function tearDown(): void
    {
        Factories::reset('config');
        Services::reset();

        parent::tearDown();
    }

    private function registration(array $overrides = []): int
    {
        $this->db->table('event_registrations')->insert(array_merge([
            'event_id'         => $this->eventId,
            'first_name'       => 'Asha',
            'last_name'        => 'Chotai',
            'email'            => 'asha@example.com',
            'num_participants' => 1,
            'num_guests'       => 0,
            'status'           => 'submitted',
            'created_at'       => '2099-10-01 10:00:00',
        ], $overrides));

        return (int) $this->db->insertID();
    }

    private function form(array $overrides = []): array
    {
        return array_merge([
            'draft_id'    => '',
            'subject'     => 'Your {event_title} details',
            'body_html'   => '<p>Dear {name}, we have you down for a party of {{party}}.</p>',
            'wrap_layout' => '1',
            'filter'      => ['statuses' => ['', 'submitted', 'confirmed']],
        ], $overrides);
    }

    private function url(string $suffix = ''): string
    {
        return 'admin/group-emails/new/event/' . $this->eventId . $suffix;
    }

    private function queueRows(string $type = GroupEmailService::TYPE): array
    {
        return $this->db->table('email_queue')->where('type', $type)->orderBy('to_email')->get()->getResultArray();
    }

    /* ---------------------------------------------------------- */

    public function testPlaceholdersAcceptBothBraceStylesAndLeaveUnknownOnesAlone(): void
    {
        $out = PlaceholderRenderer::render('{name} / {{ name }} / {nope} / a{color:red}', ['name' => 'Ravi & <Co>'], true);

        $this->assertSame('Ravi &amp; &lt;Co&gt; / Ravi &amp; &lt;Co&gt; / {nope} / a{color:red}', $out);
        $this->assertSame(['{nope}', '{{nope}}'], PlaceholderRenderer::unknown('{name} {nope} {{nope}}', ['name']));
    }

    public function testOnlyAdminsCanOpenIt(): void
    {
        $this->withSession(self::EVENTS)->get($this->url())->assertRedirectTo(site_url('admin/system/dashboard'));
        $this->withSession(self::EVENTS)->post($this->url('/queue'), $this->form())->assertRedirect();
        $this->assertSame([], $this->queueRows());

        $page = $this->withSession(self::ADMIN)->get($this->url());
        $page->assertOK();
        $page->assertSee('Diwali Dinner');
    }

    public function testEventPageShowsTheButtonToAdminsOnly(): void
    {
        $page = 'admin/content/events/event-registrations/' . $this->eventId;

        $this->withSession(self::ADMIN)->get($page)->assertSee('Email registrants');
        $this->withSession(self::EVENTS)->get($page)->assertDontSee('Email registrants');
    }

    public function testPreviewUsesTheLatestRegistrationPerAddressAndLeavesOutCancelled(): void
    {
        $this->registration(['num_guests' => 1, 'created_at' => '2099-10-01 10:00:00']);
        $this->registration(['email' => ' ASHA@example.com ', 'num_guests' => 3, 'created_at' => '2099-10-05 10:00:00']);
        $this->registration(['first_name' => '', 'last_name' => 'Patel', 'email' => 'patel@example.com', 'status' => 'confirmed']);
        $this->registration(['first_name' => 'Cancelled', 'email' => 'gone@example.com', 'status' => 'cancelled']);
        $this->registration(['first_name' => 'Typo', 'email' => 'not-an-email']);

        $data = json_decode($this->withSession(self::ADMIN)->post($this->url('/preview'), $this->form())->getJSON(), true);

        // Oldest (latest-per-address) registration first: Patel 1 Oct, Asha's rebooking 5 Oct.
        $this->assertSame(['patel@example.com', 'asha@example.com'], array_column($data['recipients'], 'email'));
        $this->assertSame('4', $data['recipients'][1]['party']); // latest booking: 1 + 3 guests
        $this->assertCount(2, $data['skipped']);                 // the older Asha booking and the bad address
        $this->assertSame('Your Diwali Dinner details', $data['subject']);
        $this->assertStringContainsString('Dear Patel, we have you down for a party of 1.', $data['html']);
        $this->assertStringContainsString('Lohana Community North London', $data['html']); // wrapped
    }

    public function testNameFallsBackToTheFullNameAndUnknownPlaceholdersAreFlagged(): void
    {
        $this->registration(['first_name' => '', 'last_name' => 'Patel']);

        $data = json_decode($this->withSession(self::ADMIN)->post($this->url('/preview'), $this->form([
            'body_html'   => '<p>Hi {name}, {surname}</p>',
            'wrap_layout' => '0',
        ]))->getJSON(), true);

        $this->assertStringContainsString('Hi Patel, {surname}', $data['html']);
        $this->assertStringNotContainsString('Lohana Community North London', $data['html']);
        $this->assertStringContainsString('{surname}', implode(' ', $data['warnings']));
    }

    public function testTestSendQueuesOneMarkedCopyAndSavesADraft(): void
    {
        $this->registration();
        $this->registration(['first_name' => 'Ravi', 'email' => 'ravi@example.com']);

        $data = json_decode($this->withSession(self::ADMIN)->post($this->url('/test'), $this->form([
            'test_email' => 'tester@example.com',
        ]))->getJSON(), true);

        $this->assertTrue($data['ok']);
        $tests = $this->queueRows(GroupEmailService::TYPE_TEST);
        $this->assertCount(1, $tests);
        $this->assertSame('tester@example.com', $tests[0]['to_email']);
        $this->assertSame('[TEST] Your Diwali Dinner details', $tests[0]['subject']);
        $this->assertSame('1', (string) $tests[0]['priority']);
        $this->assertSame(['Reply-To' => 'info@lcnl.org'], json_decode($tests[0]['headers_json'], true));
        $this->assertSame([], $this->queueRows()); // nobody else yet

        $draft = $this->db->table('group_emails')->get()->getRowArray();
        $this->assertSame((int) $draft['id'], $data['draft_id']);
        $this->assertSame('draft', $draft['status']);
        $this->assertSame('1', (string) $draft['test_count']);
    }

    public function testQueueingSendsOnePersonalisedCopyEachAndOnlyOnce(): void
    {
        $this->registration(['num_guests' => 2]);
        $this->registration(['first_name' => 'Ravi', 'email' => 'ravi@example.com']);

        $result = $this->withSession(self::ADMIN)->post($this->url('/queue'), $this->form());
        $id     = (int) $this->db->table('group_emails')->get()->getRowArray()['id'];
        $result->assertRedirectTo(site_url('admin/group-emails/' . $id));

        $rows = $this->queueRows();
        $this->assertCount(2, $rows);
        $this->assertSame(['asha@example.com', 'ravi@example.com'], array_column($rows, 'to_email'));
        $this->assertStringContainsString('Dear Asha, we have you down for a party of 3.', $rows[0]['body_html']);
        $this->assertStringContainsString('Dear Ravi, we have you down for a party of 1.', $rows[1]['body_html']);
        $this->assertStringContainsString('Dear Ravi', $rows[1]['body_text']);
        $this->assertSame('7', (string) $rows[0]['priority']);
        $this->assertNull($rows[0]['related_id']); // emails:send reads related_id as a member id
        $this->assertSame($id, (int) $rows[0]['group_email_id']);

        $groupEmail = $this->db->table('group_emails')->get()->getRowArray();
        $this->assertSame('queued', $groupEmail['status']);
        $this->assertSame('2', (string) $groupEmail['recipient_count']);

        // Resubmitting the same form (back button, double click) adds nothing.
        $this->withSession(self::ADMIN)->post($this->url('/queue'), $this->form(['draft_id' => (string) $id]))->assertRedirect();
        $this->assertCount(2, $this->queueRows());
    }

    public function testNothingIsQueuedWithoutRecipientsOrContent(): void
    {
        $this->registration(['status' => 'cancelled']);

        $this->withSession(self::ADMIN)->post($this->url('/queue'), $this->form())->assertRedirect();
        $this->withSession(self::ADMIN)->post($this->url('/queue'), $this->form(['subject' => '  ']))->assertRedirect();

        $this->assertSame([], $this->queueRows());
        $this->assertSame(0, $this->db->table('group_emails')->where('status', 'queued')->countAllResults());
    }

    public function testCancelTakesOutOnlyUnsentCopies(): void
    {
        $this->registration();
        $this->registration(['email' => 'ravi@example.com']);
        $this->registration(['email' => 'mina@example.com']);

        $this->withSession(self::ADMIN)->post($this->url('/queue'), $this->form());
        $id = (int) $this->db->table('group_emails')->get()->getRowArray()['id'];
        $this->db->table('email_queue')->where('to_email', 'asha@example.com')->update(['status' => 'sent']);

        $this->withSession(self::ADMIN)->post('admin/group-emails/' . $id . '/cancel')
            ->assertRedirectTo(site_url('admin/group-emails/' . $id));

        $this->assertSame(['asha@example.com'], array_column($this->queueRows(), 'to_email'));
        $groupEmail = $this->db->table('group_emails')->get()->getRowArray();
        $this->assertSame('cancelled', $groupEmail['status']);
        $this->assertSame('2', (string) $groupEmail['cancelled_count']);

        $this->withSession(self::ADMIN)->get('admin/group-emails/' . $id)->assertSee('asha@example.com');
        $this->withSession(self::ADMIN)->get('admin/group-emails')->assertSee('Your Diwali Dinner details');
    }
}
