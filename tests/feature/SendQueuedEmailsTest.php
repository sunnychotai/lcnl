<?php

namespace Tests\Feature;

use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Mock\MockCache;
use Config\Services;

/**
 * emails:send against a fake SMTP server (tests/_support/FakeSmtpServer.php),
 * using CodeIgniter's real SMTP code.
 *
 * @internal
 */
final class SendQueuedEmailsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $refresh = false;

    /** @var resource|null */
    private $server;

    private int $port;

    protected function setUp(): void
    {
        parent::setUp();

        $this->port   = random_int(20000, 40000);
        $this->server = proc_open(
            [PHP_BINARY, SUPPORTPATH . 'FakeSmtpServer.php', (string) $this->port],
            [1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']],
            $pipes
        );
        $this->waitForServer();

        $email = config('Email');
        $email->protocol    = 'smtp';
        $email->SMTPHost    = '127.0.0.1';
        $email->SMTPPort    = $this->port;
        $email->SMTPUser    = 'info@example.com';
        $email->SMTPPass    = 'secret';
        $email->SMTPCrypto  = '';
        $email->SMTPTimeout = 5;
        $email->fromEmail   = 'info@example.com';
        $email->fromName    = 'LCNL';
        Factories::injectMock('config', 'Email', $email);

        $queue = config('EmailQueue');
        $queue->sendDelayMs = 0;
        Factories::injectMock('config', 'EmailQueue', $queue);

        Services::injectMock('cache', new MockCache());

        $forge = \Config\Database::forge($this->DBGroup);
        foreach (['email_queue', 'cron_logs'] as $table) {
            $forge->dropTable($table, true);
        }
        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'to_email', 'to_name', 'subject', 'type', 'related_id', 'group_email_id', 'body_html', 'body_text',
            'headers_json', 'priority', 'status', 'attempts', 'last_error', 'scheduled_at', 'sent_at',
            'created_at', 'updated_at', 'deleted_at',
        ], ['type' => 'TEXT', 'null' => true]));
        $forge->addKey('id', true);
        $forge->createTable('email_queue');

        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'job_name', 'status', 'summary', 'started_at', 'finished_at', 'created_at', 'updated_at',
        ], ['type' => 'TEXT', 'null' => true]));
        $forge->addKey('id', true);
        $forge->createTable('cron_logs');
    }

    protected function tearDown(): void
    {
        if (is_resource($this->server)) {
            proc_terminate($this->server);
            proc_close($this->server);
        }

        Factories::reset('config');
        Services::reset();

        parent::tearDown();
    }

    private function waitForServer(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $probe = @fsockopen('127.0.0.1', $this->port, $errno, $errstr, 0.1);
            if ($probe) {
                fclose($probe);   // the server moves on to the next connection

                return;
            }
            usleep(50_000);
        }
        $this->markTestSkipped('Fake SMTP server did not start.');
    }

    private function queue(string $to, string $body): void
    {
        $this->db->table('email_queue')->insert([
            'to_email' => $to,
            'subject'  => 'Hello',
            'body_html' => '<p>' . $body . '</p>',
            'priority' => 1,
            'status'   => 'pending',
            'attempts' => 0,
        ]);
    }

    private function row(string $to): array
    {
        return $this->db->table('email_queue')->where('to_email', $to)->get()->getRowArray();
    }

    public function testARejectedEmailKeepsTheServersReasonAndTheRestStillGo(): void
    {
        $this->queue('first@example.com', 'Hello there');
        $this->queue('spam@example.com', 'SPAMMY content');
        $this->queue('after@example.com', 'Sent after the rejection');

        command('emails:send');

        $this->assertSame('sent', $this->row('first@example.com')['status']);

        $spam = $this->row('spam@example.com');
        $this->assertSame('failed', $spam['status']);
        $this->assertSame('554 5.7.1 Spam message rejected', $spam['last_error']);

        // The email after a failure still goes: every email gets a fresh connection.
        $this->assertSame('sent', $this->row('after@example.com')['status']);

        $log = $this->db->table('cron_logs')->get()->getRowArray();
        $this->assertSame('partial', $log['status']);
        $summary = json_decode($log['summary'], true);
        $this->assertSame('554 5.7.1 Spam message rejected', $summary['backoff'][0]['error']);

        // And "Retry all failed" leaves it alone: it would only be refused again.
        $this->assertSame(0, (new \App\Models\EmailQueueModel())->retryableFailed()->countAllResults());
    }
}
