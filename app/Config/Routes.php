<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Recommended hardening
$routes->setAutoRoute(false);

/* -----------------------
   Public pages
------------------------*/
$routes->get('/', 'Home::index');
$routes->get('/events', 'Home::events');
$routes->get('/events/(:num)', 'Home::eventDetail/$1');

$routes->get('/gallery', 'Home::gallery');
$routes->get('/contact', 'Home::contact');
$routes->get('/membership', 'Home::membership'); // landing page
$routes->get('/mahila', 'Home::mahila');
$routes->get('/faq', 'Home::faq');
$routes->get('/bereavement', 'Home::bereavement');
// Test routes
$routes->get('/dbcheck', 'Test::dbcheck');
$routes->get('/pwhash', 'Test::pwhash');
$routes->get('/email', 'Test::email');

$routes->get('/aboutus', 'Home::aboutUs');
$routes->get('/sample', 'Home::sample');
$routes->get('/privacy', 'Home::privacy');
$routes->post('contact/send', 'ContactController::send');


$routes->get('/committee', 'Home::committee');
$routes->get('/yls', 'Home::yls');
$routes->get('/youth', 'Home::youth');

/* -----------------------
   FAQs (public)
------------------------*/
$routes->get('faqs', 'Home::faq');                           // grouped view
$routes->get('faqs/all', 'FaqController::all');              // all FAQs
$routes->get('faqs/(:segment)', 'FaqController::group/$1');  // FAQs by group
$routes->get('bereavement', 'FaqController::bereavement');   // single definitive route

/* -----------------------
   Auth
------------------------*/
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

/* -----------------------
   Forgot / Reset Password
------------------------*/
// MEMBER auth (public endpoints)
$routes->get('member/login', 'MemberAuth::login', ['as' => 'member.login']);
$routes->post('member/login', 'MemberAuth::attempt');
$routes->get('member/logout', 'MemberAuth::logout');

$routes->get('member/forgot', 'Account\PasswordController::forgot', ['as' => 'member.forgot']);
$routes->post('member/forgot', 'Account\PasswordController::sendReset');
$routes->get('member/reset/(:segment)', 'Account\PasswordController::reset/$1', ['as' => 'member.reset']);
$routes->post('member/reset', 'Account\PasswordController::doReset');


/* ---------------------------------------------------------
   PUBLIC: Membership (keep OUTSIDE admin group)
----------------------------------------------------------*/
$routes->group('membership', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->get('register', 'MembershipController::register', ['as' => 'membership.register']);
    $routes->post('register', 'MembershipController::create',   ['as' => 'membership.create']);
    $routes->get('verify/(:segment)', 'MembershipController::verify/$1', ['as' => 'membership.verify']);
    $routes->get('success', 'MembershipController::success', ['as' => 'membership.success']);
    $routes->get('resend-verification', 'MembershipController::resendVerification', ['as' => 'membership.resend']);

});



// Account shortcut → dashboard
$routes->get('account', 'Account\DashboardController::index', ['as'=>'account.root']);

// ACCOUNT: Member dashboard & profile (requires MEMBER login)
$routes->group('account', [
    'namespace' => 'App\Controllers\Account',
    'filter'    => 'authMember'
], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'account.dashboard']);
    // Simple profile edit (mobile, postcode, consent)
    $routes->get('profile',  'ProfileController::edit',   ['as' => 'account.profile.edit']);
    $routes->post('profile', 'ProfileController::update', ['as' => 'account.profile.update']);
});



// ACCOUNT area uses member guard (NOT admin guard)
    $routes->group('account/household', [
    'namespace' => 'App\Controllers\Account',
    'filter'    => 'authMember'
], static function ($routes) {
    $routes->get('/',              'HouseholdController::index',        ['as' => 'account.household']);
    $routes->post('create',        'HouseholdController::create',       ['as' => 'account.household.create']);
    $routes->post('add-dependent', 'HouseholdController::addDependent', ['as' => 'account.household.addDependent']);
    $routes->post('link',          'HouseholdController::linkExisting', ['as' => 'account.household.linkExisting']);
});

/* ---------------------------------------------------------
   🔒 Admin Area (single group)
----------------------------------------------------------*/
$routes->group('admin', ['filter' => 'authAdmin'], function($routes) {

    // Dashboard
        $routes->get('dashboard', 'Admin::dashboard');

    // Email module admin panel
        $routes->get('emails', 'Admin\Emails::index');
        $routes->get('emails/view/(:num)', 'Admin\Emails::view/$1');
        $routes->get('emails/retry/(:num)', 'Admin\Emails::retry/$1');
        $routes->get('emails/delete/(:num)', 'Admin\Emails::delete/$1');
    // Committee CRUD
        $routes->group('committee', function($routes) {
        $routes->get('', 'Admin\CommitteeController::index');
        $routes->get('create', 'Admin\CommitteeController::create');
        $routes->post('store', 'Admin\CommitteeController::store');
        $routes->get('edit/(:num)', 'Admin\CommitteeController::edit/$1');
        $routes->post('update/(:num)', 'Admin\CommitteeController::update/$1');
        $routes->get('delete/(:num)', 'Admin\CommitteeController::delete/$1');
        $routes->get('clone/(:num)', 'Admin\CommitteeController::clone/$1');
    });

    // Events CRUD
        $routes->group('events', function($routes) {
        $routes->get('', 'Admin\Events::index');
        $routes->get('create', 'Admin\Events::create');
        $routes->post('store', 'Admin\Events::store');
        $routes->get('edit/(:num)', 'Admin\Events::edit/$1');
        $routes->post('update/(:num)', 'Admin\Events::update/$1');
        $routes->get('delete/(:num)', 'Admin\Events::delete/$1');
        $routes->get('clone/(:num)', 'Admin\Events::clone/$1');
    });

    // FAQs CRUD (ADMIN + WEBSITE only if you adjust filter)
        $routes->group('faqs', function($routes) {
        $routes->get('', 'Admin\FaqAdmin::index');
        $routes->get('create', 'Admin\FaqAdmin::create');
        $routes->post('store', 'Admin\FaqAdmin::store');
        $routes->get('edit/(:num)', 'Admin\FaqAdmin::edit/$1');
        $routes->post('update/(:num)', 'Admin\FaqAdmin::update/$1');
        $routes->get('delete/(:num)', 'Admin\FaqAdmin::delete/$1');
        $routes->post('reorder', 'Admin\FaqAdmin::reorder');
    });

    // Users CRUD (ADMIN only)
        $routes->group('users', ['filter' => 'authAdmin'], function($routes) {
        $routes->get('', 'Admin\Users::index');
        $routes->get('create', 'Admin\Users::create');
        $routes->post('store', 'Admin\Users::store');
        $routes->get('edit/(:num)', 'Admin\Users::edit/$1');
        $routes->post('update/(:num)', 'Admin\Users::update/$1');
        $routes->get('delete/(:num)', 'Admin\Users::delete/$1');
    });

    // Members Admin
        $routes->group('members', function($routes) {
        $routes->get('', 'Admin\MembersController::index', ['as' => 'admin.members.index']);
        $routes->get('(:num)', 'Admin\MembersController::show/$1', ['as' => 'admin.members.show']); // optional
        $routes->post('(:num)/activate', 'Admin\MembersController::activate/$1', ['as' => 'admin.members.activate']);
        $routes->post('(:num)/disable',  'Admin\MembersController::disable/$1',  ['as' => 'admin.members.disable']);
        $routes->post('(:num)/resend',   'Admin\MembersController::resend/$1',   ['as' => 'admin.members.resend']);
    });

    // Families admin (merge stub)
        $routes->group('families', function($routes) {
        $routes->post('merge', 'Admin\FamiliesController::merge', ['as' => 'admin.families.merge']);
    });
});

/* -----------------------
   Environment routes (LAST)
------------------------*/
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
