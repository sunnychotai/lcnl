<?php

namespace Tests\Feature;

use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * The admin email queue's "Retry all failed" button.
 *
 * @internal
 */
final class EmailQueueAdminTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = false;
    protected $refresh = false;

    private const ADMIN = ['isAdminLoggedIn' => true, 'admin_role' => 'ADMIN', 'admin_name' => 'Test', 'admin_id' => 1];

    protected function setUp(): void
    {
        parent::setUp();

        $filters = config('Filters');
        unset($filters->globals['before']['csrf']);
        Factories::injectMock('config', 'Filters', $filters);

        $forge = \Config\Database::forge($this->DBGroup);
        $forge->dropTable('email_queue', true);
        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'to_email', 'to_name', 'subject', 'type', 'related_id', 'group_email_id', 'body_html', 'body_text',
            'headers_json', 'priority', 'status', 'attempts', 'last_error', 'scheduled_at', 'sent_at',
            'created_at', 'updated_at', 'deleted_at',
        ], ['type' => 'TEXT', 'null' => true]));
        $forge->addKey('id', true);
        $forge->createTable('email_queue');
    }

    protected function tearDown(): void
    {
        Factories::reset('config');
        Services::reset();

        parent::tearDown();
    }

    private function email(string $to, string $status, ?string $error = null): void
    {
        $this->db->table('email_queue')->insert([
            'to_email'     => $to,
            'subject'      => 'Hello',
            'status'       => $status,
            'attempts'     => 1,
            'last_error'   => $error,
            'scheduled_at' => $status === 'failed' ? '2099-01-01 00:00:00' : null,
        ]);
    }

    private function statusOf(string $to): array
    {
        return $this->db->table('email_queue')->where('to_email', $to)->get()->getRowArray();
    }

    public function testRetryAllQueuesEveryFailedEmailExceptInvalidAddresses(): void
    {
        $this->email('smtp@example.com', 'failed', 'SMTP send failed');
        $this->email('noerror@example.com', 'failed');
        $this->email('bad-address', 'failed', 'INVALID_EMAIL');
        $this->email('sent@example.com', 'sent');
        $this->email('waiting@example.com', 'pending');

        $page = $this->withSession(self::ADMIN)->get('admin/system/emails');
        $page->assertSee('Retry all failed (2)');

        $result = $this->withSession(self::ADMIN)->post('admin/system/emails/retry-failed');
        $result->assertRedirectTo(site_url('admin/system/emails'));
        $result->assertSessionHas('message', '2 failed emails queued again.');

        foreach (['smtp@example.com', 'noerror@example.com'] as $to) {
            $row = $this->statusOf($to);
            $this->assertSame('pending', $row['status']);
            $this->assertNull($row['last_error']);
            $this->assertNull($row['scheduled_at']); // goes on the next run, not at the old retry time
        }
        $this->assertSame('failed', $this->statusOf('bad-address')['status']);
        $this->assertSame('sent', $this->statusOf('sent@example.com')['status']);
    }

    public function testNothingToRetrySaysSo(): void
    {
        $this->email('bad-address', 'failed', 'INVALID_EMAIL');

        $this->withSession(self::ADMIN)->post('admin/system/emails/retry-failed')
            ->assertSessionHas('error', 'There are no failed emails to retry.');
        $this->assertSame('failed', $this->statusOf('bad-address')['status']);
    }

    public function testOnlyAdminsCanRetryAll(): void
    {
        $this->email('smtp@example.com', 'failed', 'SMTP send failed');

        $this->withSession(['isAdminLoggedIn' => true, 'admin_role' => 'WEBADMIN', 'admin_id' => 2])
            ->post('admin/system/emails/retry-failed')
            ->assertRedirectTo(site_url('admin/system/dashboard'));

        $this->assertSame('failed', $this->statusOf('smtp@example.com')['status']);
    }
}
