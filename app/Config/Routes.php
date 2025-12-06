<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(false);

/* -----------------------
   Public pages
------------------------*/
$routes->get('/', 'Home::index');
$routes->get('/events', 'Events::index');
$routes->get('/events/(:num)', 'Events::eventDetail/$1');
$routes->get('sitemap.xml', 'Sitemap::index');
$routes->get('/aboutus', 'Home::aboutUs');
$routes->get('/sample', 'Home::sample');
$routes->get('/privacy', 'Home::privacy');
$routes->post('contact/send', 'ContactController::send');
$routes->get('/gallery', 'Home::gallery');
$routes->get('/contact', 'Home::contact');
$routes->get('/membership', 'Home::membership');
$routes->get('/faq', 'Home::faq');
$routes->get('/lcf', 'Committee::lcf');
$routes->get('/bereavement', 'Home::bereavement');
$routes->get('/tabletennis', 'Home::tabletennis');

/* Committee Routes */
$routes->get('/committee', 'Committee::committee');
$routes->get('/yls', 'Committee::yls');
$routes->get('/youth', 'Committee::youth');
$routes->get('/mahila', 'Committee::mahila');

/* Event registrations */
$routes->get('events/register/chopda-pujan', 'EventRegistrationController::register');
$routes->post('events/register/submit', 'EventRegistrationController::submit');
$routes->get('events/register/chopda-pujan/thankyou', 'EventRegistrationController::thankyou');

/* Dev / test utilities */
$routes->get('/dbcheck', 'Test::dbcheck');
$routes->get('/pwhash', 'Test::pwhash');
$routes->get('/email', 'Test::email');

/* -----------------------
   FAQs (public)
------------------------*/
$routes->get('faqs', 'Home::faq');
$routes->get('faqs/all', 'FaqController::all');
$routes->get('faqs/(:segment)', 'FaqController::group/$1');
$routes->get('bereavement', 'FaqController::bereavement');

/* -----------------------
   Auth
------------------------*/
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

/* -----------------------
   Forgot / Reset Password
------------------------*/
$routes->get('membership/login', 'MemberAuth::login', ['as' => 'membership.login']);
$routes->post('membership/login', 'MemberAuth::attempt');
$routes->get('membership/logout', 'MemberAuth::logout');

$routes->get('membership/forgot', 'Account\PasswordController::forgot', ['as' => 'member.forgot']);
$routes->post('membership/forgot', 'Account\PasswordController::sendReset');
$routes->get('membership/reset/(:segment)', 'Account\PasswordController::reset/$1', ['as' => 'member.reset']);
$routes->post('membership/reset', 'Account\PasswordController::doReset');

/* ---------------------------------------------------------
   PUBLIC: Membership (keep OUTSIDE admin group)
----------------------------------------------------------*/
$routes->group('membership', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->get('register', 'MembershipController::register', ['as' => 'membership.register']);
    $routes->post('register', 'MembershipController::create', ['as' => 'membership.create']);
    $routes->get('verify/(:segment)', 'MembershipController::verify/$1', ['as' => 'membership.verify']);
    $routes->get('success', 'MembershipController::success', ['as' => 'membership.success']);
    $routes->get('resend-verification', 'MembershipController::resendVerification', ['as' => 'membership.resend']);
});

/* -----------------------
   Member account area
------------------------*/
$routes->get('account', 'Account\DashboardController::index', ['as' => 'account.root']);
$routes->group('account', [
    'namespace' => 'App\Controllers\Account',
    'filter'    => 'authMember'
], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'account.dashboard']);
    $routes->get('profile', 'ProfileController::edit', ['as' => 'account.profile.edit']);
    $routes->post('profile', 'ProfileController::update', ['as' => 'account.profile.update']);
    // Family Management (member-side)
    $routes->get('family', 'FamilyController::index', ['as' => 'account.family']);
    $routes->post('family/create', 'FamilyController::create', ['as' => 'account.family.create']);
    $routes->post('family/update/(:num)', 'FamilyController::update/$1', ['as' => 'account.family.update']);
    $routes->post('family/delete/(:num)', 'FamilyController::delete/$1', ['as' => 'account.family.delete']);
});

/* ---------------------------------------------------------
   🔒 ADMIN AREA — ROLE-BASED GROUPS
----------------------------------------------------------*/
$routes->group('admin/system', ['filter' => 'authAdmin'], function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
});

/* --- System / Core admin (ADMIN only) --- */
$routes->group('admin/system', ['filter' => 'authAdmin:ADMIN'], function ($routes) {

    // Emails Admin
    $routes->get('emails', 'Admin\Emails::index');
    $routes->get('emails/view/(:num)', 'Admin\Emails::view/$1');
    $routes->get('emails/retry/(:num)', 'Admin\Emails::retry/$1');
    $routes->get('emails/delete/(:num)', 'Admin\Emails::delete/$1');

    // Users Admin
    $routes->group('users', function ($routes) {
        $routes->get('', 'Admin\Users::index');
        $routes->get('create', 'Admin\Users::create');
        $routes->post('store', 'Admin\Users::store');
        $routes->get('edit/(:num)', 'Admin\Users::edit/$1');
        $routes->post('update/(:num)', 'Admin\Users::update/$1');
        $routes->get('delete/(:num)', 'Admin\Users::delete/$1');
    });
});

/* --- Content (ADMIN + WEBSITE) --- */
$routes->group('admin/content', ['filter' => 'authAdmin:ADMIN,WEBSITE'], function ($routes) {

    // Committee Admin
    $routes->group('committee', function ($routes) {
        $routes->get('', 'Admin\CommitteeController::index');
        $routes->get('create', 'Admin\CommitteeController::create');
        $routes->post('store', 'Admin\CommitteeController::store');
        $routes->get('edit/(:num)', 'Admin\CommitteeController::edit/$1');
        $routes->post('update/(:num)', 'Admin\CommitteeController::update/$1');
        $routes->get('delete/(:num)', 'Admin\CommitteeController::delete/$1');
        $routes->get('clone/(:num)', 'Admin\CommitteeController::clone/$1');
    });

    // FAQs
    $routes->group('faqs', function ($routes) {
        $routes->get('', 'Admin\FaqAdmin::index');
        $routes->get('create', 'Admin\FaqAdmin::create');
        $routes->post('store', 'Admin\FaqAdmin::store');
        $routes->get('edit/(:num)', 'Admin\FaqAdmin::edit/$1');
        $routes->post('update/(:num)', 'Admin\FaqAdmin::update/$1');
        $routes->get('delete/(:num)', 'Admin\FaqAdmin::delete/$1');
        $routes->post('reorder', 'Admin\FaqAdmin::reorder');
    });
});

$routes->group('admin/content', ['filter' => 'authAdmin:ADMIN,EVENTS,WEBSITE'], function ($routes) {

    // Events
    $routes->group('events', function ($routes) {
        $routes->get('', 'Admin\Events::index');
        $routes->get('create', 'Admin\Events::create');
        $routes->post('store', 'Admin\Events::store');
        $routes->get('edit/(:num)', 'Admin\Events::edit/$1');
        $routes->post('update/(:num)', 'Admin\Events::update/$1');
        $routes->get('delete/(:num)', 'Admin\Events::delete/$1');
        $routes->get('clone/(:num)', 'Admin\Events::clone/$1');
    });
});

/* --- Membership area (ADMIN + MEMBERSHIP) --- */
$routes->group('admin/membership', ['filter' => 'authAdmin:ADMIN,MEMBERSHIP'], function ($routes) {
    $routes->get('', 'Admin\MembersController::index', ['as' => 'admin.membership.index']);
    $routes->get('export', 'Admin\MembersController::export', ['as' => 'admin.membership.export']);
    $routes->get('(:num)', 'Admin\MembersController::show/$1', ['as' => 'admin.membership.show']);
    $routes->post('(:num)/activate', 'Admin\MembersController::activate/$1', ['as' => 'admin.membership.activate']);
    $routes->post('(:num)/disable', 'Admin\MembersController::disable/$1', ['as' => 'admin.membership.disable']);
    $routes->post('(:num)/resend', 'Admin\MembersController::resend/$1', ['as' => 'admin.membership.resend']);
    // In Config/Routes.php inside the admin/membership group
    $routes->get('(:num)/edit', 'Admin\MembersController::edit/$1', ['as' => 'admin.membership.edit']);
    $routes->post('(:num)/update', 'Admin\MembersController::update/$1', ['as' => 'admin.membership.update']);
    // Family Management (admin-side)
    $routes->post('(:num)/family/create', 'Admin\FamilyController::create/$1', ['as' => 'admin.family.create']);
    $routes->post('(:num)/family/update/(:num)', 'Admin\FamilyController::update/$1/$2', ['as' => 'admin.family.update']);
    $routes->post('(:num)/family/delete/(:num)', 'Admin\FamilyController::delete/$1/$2', ['as' => 'admin.family.delete']);
    $routes->post('data', 'Admin\MemberDataController::list');
    $routes->post('family/add',    'Admin\MemberFamilyController::add');
    $routes->post('family/update', 'Admin\MemberFamilyController::update');
    $routes->post('family/delete', 'Admin\MemberFamilyController::delete');
});





/* --- Finance area (ADMIN + FINANCE) --- */
$routes->group('admin/finance', ['filter' => 'authAdmin:ADMIN,FINANCE'], function ($routes) {
    $routes->get('', 'Admin\FinanceController::index');
    $routes->get('reports', 'Admin\FinanceController::reports');
});

/* -----------------------
   Access Denied (safe public route)
------------------------*/
$routes->get('access-denied', 'Home::accessDenied');

/* -----------------------
   Environment routes (LAST)
------------------------*/
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
