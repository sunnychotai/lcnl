<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class MembershipReportsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // The app's older migrations use MySQL-only SQL, so build just the tables
    // these tests touch on the in-memory SQLite test database.
    protected $migrate = false;
    protected $refresh = false;

    private const ADMIN = ['isAdminLoggedIn' => true, 'admin_role' => 'MEMBERSHIP', 'admin_name' => 'Test Admin'];

    protected function setUp(): void
    {
        parent::setUp();

        $forge = \Config\Database::forge($this->DBGroup);
        $text  = ['type' => 'TEXT', 'null' => true];

        $tables = [
            'members' => [
                'first_name', 'last_name', 'email', 'mobile', 'address1', 'address2', 'city', 'postcode',
                'gender', 'date_of_birth', 'status', 'verified_at', 'created_at', 'updated_at', 'deleted_at',
            ],
            'memberships' => [
                'member_id', 'membership_type', 'membership_number', 'start_date', 'status', 'created_at', 'updated_at',
            ],
            'membership_payments' => [
                'member_id', 'provider', 'status', 'amount_minor', 'currency', 'paid_at',
                'stripe_payment_intent_id', 'stripe_checkout_session_id', 'stripe_customer_id',
            ],
        ];

        foreach ($tables as $table => $fields) {
            $forge->dropTable($table, true);
            $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys($fields, $text));
            $forge->addKey('id', true);
            $forge->createTable($table);
        }

        $this->seedMembers();
    }

    private function member(string $status, array $memberships, ?string $deletedAt = null): int
    {
        $this->db->table('members')->insert([
            'first_name' => 'Test',
            'last_name'  => 'Member',
            'status'     => $status,
            'deleted_at' => $deletedAt,
        ]);
        $id = (int) $this->db->insertID();

        foreach ($memberships as [$type, $msStatus]) {
            $this->db->table('memberships')->insert(['member_id' => $id, 'membership_type' => $type, 'status' => $msStatus]);
        }

        return $id;
    }

    private function seedMembers(): void
    {
        $this->member('active', [['life', 'active']]);          // lowercase from admin create
        $this->member('pending', [['Life', 'active']]);          // imported, never activated
        $this->member('disabled', [['life', 'active']]);         // disabled: not counted as Life
        $this->member('active', [['Life', 'pending']]);          // was missing from both CSVs
        $this->member('pending', [['Standard', 'pending']]);     // was missing from Non-Life CSV
        $this->member('active', [['Standard', 'active']]);
        $this->member('active', [['Life', 'active'], ['Standard', 'active']]); // latest active row wins
        $this->member('active', [['Standard', 'active'], ['Life', 'pending']]); // active row beats newer pending
        $this->member('active', [['Life', 'active']], '2026-01-01 00:00:00'); // soft-deleted

        $payer = $this->member('active', [['Life', 'active']]);
        foreach ([1, 2] as $n) {
            $this->db->table('membership_payments')->insert([
                'member_id' => $payer, 'provider' => 'stripe', 'status' => 'paid', 'amount_minor' => 75,
            ]);
        }
        $this->db->table('membership_payments')->insert([
            'member_id' => $payer, 'provider' => 'stripe', 'status' => 'failed', 'amount_minor' => 75,
        ]);
    }

    private function csvRows(string $path): array
    {
        $body = $this->withSession(self::ADMIN)->get($path)->response()->getBody();
        $lines = array_map('str_getcsv', array_filter(explode("\n", trim(substr($body, 3)))));

        return array_slice($lines, 1);
    }

    public function testCardsMatchCsvRowCounts(): void
    {
        $result = $this->withSession(self::ADMIN)->get('admin/membership/reports');
        $result->assertOK();
        preg_match_all('/stat-value">([\d,]+)</', $result->getBody(), $m);
        [$total, $life, $nonLife, $stripe] = array_map(static fn ($v) => (int) str_replace(',', '', $v), $m[1]);

        $this->assertSame([9, 4, 4, 1], [$total, $life, $nonLife, $stripe]);
        // The disabled Life member is in neither Life nor Non-Life.
        $this->assertSame($total - 1, $life + $nonLife);

        $this->assertCount($total, $this->csvRows('admin/membership/reports/export'));
        $this->assertCount($life, $this->csvRows('admin/membership/reports/life/export'));
        $this->assertCount($nonLife, $this->csvRows('admin/membership/reports/non-life/export'));
        $this->assertCount($stripe, $this->csvRows('admin/membership/reports/stripe/export'));
    }

    public function testStripeExportShowsRepeatPayments(): void
    {
        $rows = $this->csvRows('admin/membership/reports/stripe/export');

        $this->assertSame('2', end($rows[0]));
    }
}
