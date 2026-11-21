<?php

namespace Tests\Feature;

use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;

/**
 * Who can reach which part of the admin (Config\AdminAccess), and the
 * login and Users-screen rules that keep ADMIN to the people it's given to.
 *
 * @internal
 */
final class AdminAccessTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = false;
    protected $refresh = false;

    private const DASHBOARD = 'admin/system/dashboard';

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF is covered sitewide; these tests are about access.
        $filters = config('Filters');
        unset($filters->globals['before']['csrf']);
        Factories::injectMock('config', 'Filters', $filters);

        helper('role');

        $forge = \Config\Database::forge($this->DBGroup);
        $forge->dropTable('lcnl_users', true);
        $forge->addField([
            'id'         => ['type' => 'INTEGER', 'auto_increment' => true],
            'name'       => ['type' => 'TEXT'],
            'email'      => ['type' => 'TEXT'],
            'role'       => ['type' => 'TEXT'],
            'password'   => ['type' => 'TEXT'],
            'created_at' => ['type' => 'TEXT', 'null' => true],
            'updated_at' => ['type' => 'TEXT', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('lcnl_users');
    }

    protected function tearDown(): void
    {
        Factories::reset('config');
        Services::reset();

        parent::tearDown();
    }

    private function as(string $role, int $id = 99): array
    {
        return ['isAdminLoggedIn' => true, 'admin_role' => $role, 'admin_name' => 'Test', 'admin_id' => $id];
    }

    private function user(string $email, string $role, string $password = 'secret123'): int
    {
        $this->db->table('lcnl_users')->insert([
            'name'     => ucfirst(strtok($email, '@')),
            'email'    => $email,
            'role'     => $role,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return (int) $this->db->insertID();
    }

    /* ---------------------------------------------------------- */

    public function testAdminCanUseEverythingAndUnknownFeaturesAreAdminOnly(): void
    {
        session()->set($this->as('ADMIN'));
        $this->assertTrue(canAccess('users'));
        $this->assertTrue(canAccess('no_such_feature'));

        session()->set($this->as('WEBADMIN'));
        $this->assertFalse(canAccess('no_such_feature'));
        $this->assertTrue(canAccess('gallery'));
        $this->assertTrue(canAccess('users', 'gallery')); // any one will do

        session()->remove('isAdminLoggedIn');
        $this->assertFalse(canAccess('dashboard'));
    }

    public function testWebAdminIsKeptOutOfUsersEmailsGroupEmailsAndCron(): void
    {
        foreach ([
            'admin/system/users',
            'admin/system/users/edit/1',
            'admin/system/emails',
            'admin/system/cron-logs',
            'admin/group-emails',
            'admin/group-emails/new/event/1',
        ] as $path) {
            $this->withSession($this->as('WEBADMIN'))->get($path)->assertRedirectTo(site_url(self::DASHBOARD));
        }

        $this->withSession($this->as('WEBADMIN'))->post('admin/system/users/delete/1')
            ->assertRedirectTo(site_url(self::DASHBOARD));
    }

    public function testWebAdminGetsTheRest(): void
    {
        $page = $this->withSession($this->as('WEBADMIN'))->get('admin/content/events/event-registrations');
        $page->assertOK();

        // Menu: their features are there, the ADMIN-only ones aren't.
        $page->assertSee(base_url('admin/gallery'));
        $page->assertSee(base_url('admin/membership'));
        $page->assertSee(base_url('admin/surveys/ticketing'));
        $page->assertDontSee(base_url('admin/system/users'));
        $page->assertDontSee(base_url('admin/system/emails'));
        $page->assertDontSee(base_url('admin/group-emails'));
        $page->assertDontSee(base_url('admin/system/cron-logs'));
    }

    public function testOtherRolesCanNoLongerReachTheUsersScreen(): void
    {
        foreach (['EVENTS', 'WEBSITE', 'MEMBERSHIP', 'FINANCE'] as $role) {
            $this->withSession($this->as($role))->get('admin/system/users')->assertRedirectTo(site_url(self::DASHBOARD));
            $this->withSession($this->as($role))->get('admin/system/emails')->assertRedirectTo(site_url(self::DASHBOARD));
        }

        $this->withSession([])->get('admin/system/users')->assertRedirectTo(site_url('auth/login'));
    }

    public function testAdminSeesTheAdminOnlyMenu(): void
    {
        $this->user('sunny@example.com', 'ADMIN');

        $page = $this->withSession($this->as('ADMIN', 1))->get('admin/system/users');
        $page->assertOK();
        $page->assertSee(base_url('admin/group-emails'));
        $page->assertSee(base_url('admin/system/cron-logs'));
    }

    public function testTheOldMasterLoginNoLongerWorks(): void
    {
        $result = $this->post('auth/attemptLogin', ['email' => 'sa@sunny.com', 'password' => 'yfbnmasc']);

        $result->assertSessionMissing('isAdminLoggedIn');
    }

    public function testTheOldMemberMasterLoginNoLongerWorks(): void
    {
        $forge = \Config\Database::forge($this->DBGroup);
        $forge->dropTable('members', true);
        $forge->addField([
            'id'            => ['type' => 'INTEGER', 'auto_increment' => true],
            'email'         => ['type' => 'TEXT'],
            'password_hash' => ['type' => 'TEXT', 'null' => true],
            'status'        => ['type' => 'TEXT', 'null' => true],
            'deleted_at'    => ['type' => 'TEXT', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('members');

        $result = $this->post('membership/login', ['email' => 'sam@sunny.com', 'password' => 'yfbnmasc']);

        $result->assertSessionMissing('isMemberLoggedIn');
        $result->assertSessionHas('error', 'Invalid email or password.');
    }

    public function testAWebAdminCanSignInAndUnknownRolesCannot(): void
    {
        $this->user('web@example.com', 'webadmin');
        $this->user('odd@example.com', 'SUPERHERO');

        $ok = $this->post('auth/attemptLogin', ['email' => 'web@example.com', 'password' => 'secret123']);
        $ok->assertRedirectTo(site_url(self::DASHBOARD));
        $ok->assertSessionHas('admin_role', 'WEBADMIN');

        session()->destroy();

        $odd = $this->post('auth/attemptLogin', ['email' => 'odd@example.com', 'password' => 'secret123']);
        $odd->assertSessionMissing('isAdminLoggedIn');
    }

    public function testAdminsCantChangeTheirOwnRoleOrDeleteThemselves(): void
    {
        $me    = $this->user('sunny@example.com', 'ADMIN');
        $other = $this->user('helper@example.com', 'ADMIN');

        $this->withSession($this->as('ADMIN', $me))->post('admin/system/users/update/' . $me, [
            'name' => 'Sunny', 'email' => 'sunny@example.com', 'role' => 'WEBSITE', 'password' => '',
        ])->assertRedirectTo(site_url('admin/system/users'));
        $this->assertSame('ADMIN', $this->db->table('lcnl_users')->where('id', $me)->get()->getRow()->role);

        $this->withSession($this->as('ADMIN', $me))->post('admin/system/users/delete/' . $me);
        $this->assertSame(1, $this->db->table('lcnl_users')->where('id', $me)->countAllResults());

        // Other people's roles can change: here, another ADMIN moves to WEBADMIN.
        $this->withSession($this->as('ADMIN', $me))->post('admin/system/users/update/' . $other, [
            'name' => 'Helper', 'email' => 'helper@example.com', 'role' => 'WEBADMIN', 'password' => '',
        ])->assertRedirectTo(site_url('admin/system/users'));
        $this->assertSame('WEBADMIN', $this->db->table('lcnl_users')->where('id', $other)->get()->getRow()->role);
    }
}
