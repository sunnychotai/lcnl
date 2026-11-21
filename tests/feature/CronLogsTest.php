<?php

namespace Tests\Feature;

use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * The admin cron log pages, for every shape of summary emails:send writes.
 *
 * @internal
 */
final class CronLogsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = false;
    protected $refresh = false;

    private const ADMIN = ['isAdminLoggedIn' => true, 'admin_role' => 'ADMIN', 'admin_name' => 'Test', 'admin_id' => 1];

    protected function setUp(): void
    {
        parent::setUp();

        $forge = \Config\Database::forge($this->DBGroup);
        $forge->dropTable('cron_logs', true);
        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'job_name', 'status', 'summary', 'started_at', 'finished_at', 'created_at', 'updated_at',
        ], ['type' => 'TEXT', 'null' => true]));
        $forge->addKey('id', true);
        $forge->createTable('cron_logs');
    }

    protected function tearDown(): void
    {
        Factories::reset('config');
        Services::reset();

        parent::tearDown();
    }

    private function log(string $status, ?array $summary): int
    {
        $this->db->table('cron_logs')->insert([
            'job_name'    => 'emails:send',
            'status'      => $status,
            'summary'     => $summary === null ? null : json_encode($summary),
            'started_at'  => '2026-11-21 10:00:00',
            'finished_at' => '2026-11-21 10:00:07',
        ]);

        return (int) $this->db->insertID();
    }

    /** A partial run as emails:send records it: one sent, one failed. */
    private function partialSummary(): array
    {
        return [
            'processed'  => 2,
            'sent'       => 1,
            'failed'     => 1,
            'sent_to'    => ['ok@example.com'],
            'errors'     => [],
            'limit_info' => [
                'sent_last_10_min' => 3, 'remaining_10_min' => 47, 'sent_last_24h' => 20, 'remaining_24h' => 930,
                'hard_per_run_cap' => 5, 'batch_requested' => 100, 'take_effective' => 5,
            ],
            'backoff'    => [['row_id' => 42, 'email' => 'bounce@example.com', 'error' => 'SMTP send failed']],
            'paused'     => false,
            'test_mode'  => false,
            'test_email' => null,
        ];
    }

    public function testAPartialRunShowsItsFailures(): void
    {
        $id = $this->log('partial', $this->partialSummary());

        $page = $this->withSession(self::ADMIN)->get('admin/system/cron-logs/' . $id);

        $page->assertOK();
        $page->assertSee('Partial');
        $page->assertSee('bounce@example.com');
        $page->assertSee('SMTP send failed');
    }

    public function testOddOrEmptySummariesStillRender(): void
    {
        $ids = [
            $this->log('error', ['errors' => ['Database went away']]),
            $this->log('success', null),
            $this->log('success', ['processed' => 0, 'test_mode' => true]),          // older runs: no 'paused'
            $this->log('partial', ['backoff' => [['row_id' => 7]], 'limit_info' => ['take_effective' => 5]]),
        ];

        foreach ($ids as $id) {
            $this->withSession(self::ADMIN)->get('admin/system/cron-logs/' . $id)->assertOK();
        }

        $this->withSession(self::ADMIN)->get('admin/system/cron-logs')->assertOK();
    }
}
