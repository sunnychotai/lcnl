<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(false);

/* =========================================================
   PUBLIC ROUTES
========================================================= */

// Stripe
$routes->post('stripe/webhook', 'StripeWebhookController::handle');

/* =========================================================
   ADMIN AUTH
========================================================= */

$routes->get('auth/login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

// Core pages
$routes->get('/', 'Home::index');
$routes->get('aboutus', 'Home::aboutUs');
$routes->get('sample', 'Home::sample');
$routes->get('privacy', 'Home::privacy');
$routes->get('gallery', 'Home::gallery');
$routes->get('contact', 'Home::contact');
$routes->post('contact/send', 'ContactController::send');
$routes->get('membership', 'Home::membership');
// FAQs
$routes->get('faqs', 'FaqController::index');
$routes->get('faqs/group/(:segment)', 'FaqController::group/$1');
$routes->get('faqs/all', 'FaqController::all');
$routes->get('faqs/bereavement', 'FaqController::bereavement');
$routes->get('bereavement', 'Home::bereavement');
$routes->get('tabletennis', 'Home::tabletennis');
$routes->get('dlc-hire', 'Home::dlcHire');

$routes->get('committee', 'Committee::committee');
$routes->get('yls', 'Committee::yls');
$routes->get('youth', 'Committee::youth');
$routes->get('mahila', 'Committee::mahila');
$routes->get('lcf', 'Committee::lcf');

$routes->get('sitemap.xml', 'Sitemap::index');

/* =========================================================
   GOLF TOURNAMENT 2026 (PUBLIC)
========================================================= */

$routes->get('golf', 'GolfController::info');
$routes->get('golf/register', 'GolfController::register');
$routes->post('golf/register', 'GolfController::submit');
$routes->get('golf/confirmation/(:segment)', 'GolfController::confirmation/$1');

/* =========================================================
   EVENTS (PUBLIC)
========================================================= */

$routes->get('events', 'Events::index');
$routes->get('events/(:num)', 'Events::eventDetail/$1');

// Registration
$routes->get('events/register', 'EventRegistrationController::register');
$routes->get('events/register/(:segment)', 'EventRegistrationController::register/$1');
$routes->post('events/register/submit', 'EventRegistrationController::submit');
$routes->get('events/thanks', 'EventRegistrationController::thankyou');

/* =========================================================
   MEMBERSHIP AUTH & REGISTRATION
========================================================= */

// Login / Logout

$routes->get('membership/logout', 'MemberAuth::logout');

// Register
$routes->get('membership/register', 'MembershipController::register');
$routes->post('membership/register', 'MembershipController::create');

// Email verification
$routes->get('membership/verify/(:segment)', 'MembershipController::verify/$1');
$routes->get('membership/success', 'MembershipController::success');
$routes->get('membership/resend-verification', 'MembershipController::resendVerification');
$routes->get('membership/activated', 'MembershipController::activated');

$routes->get('membership/login', 'MemberAuth::login', ['as' => 'member.login']);
$routes->post('membership/login', 'MemberAuth::attempt', ['as' => 'member.login.post']);

$routes->get('membership/forgot', 'Account\PasswordController::forgot', ['as' => 'member.forgot']);
$routes->post('membership/forgot', 'Account\PasswordController::sendReset', ['as' => 'member.forgot.post']);

$routes->get('membership/reset/(:segment)', 'Account\PasswordController::reset/$1', ['as' => 'member.reset']);
$routes->post('membership/reset', 'Account\PasswordController::doReset', ['as' => 'member.reset.post']);




/* =========================================================
   MEMBER ACCOUNT AREA (AUTH REQUIRED)
========================================================= */

$routes->group('account', [
    'namespace' => 'App\Controllers\Account',
    'filter' => 'authMember'
], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index', [
        'as' => 'account.dashboard'
    ]);

    // Profile
    $routes->get('profile', 'ProfileController::edit', [
        'as' => 'account.profile.edit'
    ]);
    $routes->post('profile', 'ProfileController::update', [
        'as' => 'account.profile.update'
    ]);

    // Family
    $routes->get('family', 'FamilyController::index', [
        'as' => 'account.family'
    ]);
    $routes->post('family/create', 'FamilyController::create', [
        'as' => 'account.family.create'
    ]);
    $routes->post('family/update/(:num)', 'FamilyController::update/$1', [
        'as' => 'account.family.update'
    ]);
    $routes->post('family/delete/(:num)', 'FamilyController::delete/$1', [
        'as' => 'account.family.delete'
    ]);

    // Membership Upgrade (Stripe)
    $routes->post('membership/upgrade/checkout', 'MembershipUpgradeController::checkout', [
        'as' => 'account.membership.upgrade.checkout'
    ]);
    $routes->get('membership/upgrade/success', 'MembershipUpgradeController::success', [
        'as' => 'account.membership.upgrade.success'
    ]);
    $routes->get('membership/upgrade/cancel', 'MembershipUpgradeController::cancel', [
        'as' => 'account.membership.upgrade.cancel'
    ]);
});


$routes->group('admin/membership', [
    'filter' => 'authAdmin:ADMIN,MEMBERSHIP'
], function ($routes) {

    // MEMBERS LIST UI
    $routes->get('/', 'Admin\MembersController::index');
    $routes->get('', 'Admin\MembersController::index'); // safety (some servers)

    // DATATABLES DATA ENDPOINT (THIS FIXES tn/7 ajax error + 404s)
    $routes->post('data', 'Admin\MemberDataController::list');

    // MEMBER CRUD PAGES (if you use them)
    $routes->get('create', 'Admin\MembersController::create');
    $routes->post('store', 'Admin\MembersController::store');
    $routes->get('(:num)', 'Admin\MembersController::show/$1');
    $routes->get('(:num)/edit', 'Admin\MembersController::edit/$1');
    $routes->post('(:num)/update', 'Admin\MembersController::update/$1');

    // EMAIL VALIDITY TOGGLE (your JS hits this)
    $routes->post('(:num)/email-validity', 'Admin\MembersController::toggleEmailValidity/$1');

    // EXPORT (if you use it)
    $routes->get('export', 'Admin\MembersController::export');

    // REPORTS
    $routes->get('reports', 'Admin\MembershipReportsController::index');
    $routes->get('reports/export', 'Admin\MembershipReportsController::exportAll');
    $routes->get('reports/life/export', 'Admin\MembershipReportsController::exportLife');
    $routes->get('reports/non-life/export', 'Admin\MembershipReportsController::exportNonLife');
    $routes->get('reports/stripe/export', 'Admin\MembershipReportsController::exportStripe');

    $routes->post('(:num)/update-type', 'Admin\MembersController::updateType/$1');
    $routes->post('(:num)/activate', 'Admin\MembersController::activate/$1');
    $routes->post('(:num)/disable-with-reason', 'Admin\MembersController::disableWithReason/$1');
    $routes->post('(:num)/queue-activation', 'Admin\MembersController::queueActivationEmail/$1'); // <-- ADD THIS
});



/* =========================================================
   ADMIN SYSTEM (DASHBOARD + EMAILS + CRON)
============================================================ */

$routes->group('admin/system', [
    'filter' => 'authAdmin'
], function ($routes) {

    // IMPORTANT: This controller lives in App\Controllers\Admin.php
    $routes->get('dashboard', '\App\Controllers\Admin::dashboard');

    $routes->get('cron-logs', 'Admin\CronController::index');
    $routes->get('cron-logs/(:num)', 'Admin\CronController::show/$1');

    // Email Queue
    $routes->get('emails', 'Admin\Emails::index');
    $routes->get('emails/view/(:num)', 'Admin\Emails::view/$1');
    $routes->get('emails/retry/(:num)', 'Admin\Emails::retry/$1');
    $routes->get('emails/delete/(:num)', 'Admin\Emails::delete/$1');
    $routes->post('emails/data', 'Admin\EmailDataController::list');
    $routes->get('emails/stats', 'Admin\EmailDataController::stats');
});


/* =========================================================
   ADMIN CONTENT (EVENTS, FAQ, COMMITTEE)
========================================================= */

$routes->group('admin/content', [
    'filter' => 'authAdmin:ADMIN,EVENTS,WEBSITE'
], function ($routes) {

    // Events
    $routes->get('events', 'Admin\Events::index');
    $routes->get('events/create', 'Admin\Events::create');
    $routes->post('events/store', 'Admin\Events::store');
    $routes->get('events/edit/(:num)', 'Admin\Events::edit/$1');
    $routes->post('events/update/(:num)', 'Admin\Events::update/$1');
    $routes->get('events/delete/(:num)', 'Admin\Events::delete/$1');
    $routes->get('events/clone/(:num)', 'Admin\Events::clone/$1');

    $routes->get('events/event-registrations', 'Admin\EventRegistrationController::index');
    $routes->post('events/event-registrations/summary', 'Admin\EventRegistrationController::summary');

    // Golf Tournament 2026
    $routes->get('golf', 'Admin\GolfRegistrationController::index');
    $routes->get('golf/export', 'Admin\GolfRegistrationController::export');

    // FAQs
    $routes->get('faqs', 'Admin\FaqAdmin::index');
    $routes->get('faqs/create', 'Admin\FaqAdmin::create');
    $routes->post('faqs/store', 'Admin\FaqAdmin::store');
    $routes->get('faqs/edit/(:num)', 'Admin\FaqAdmin::edit/$1');
    $routes->post('faqs/update/(:num)', 'Admin\FaqAdmin::update/$1');
    $routes->get('faqs/delete/(:num)', 'Admin\FaqAdmin::delete/$1');
    $routes->post('faqs/reorder', 'Admin\FaqAdmin::reorder');

    // Committee
    $routes->get('committee', 'Admin\CommitteeController::index');
    $routes->get('committee/create', 'Admin\CommitteeController::create');
    $routes->post('committee/store', 'Admin\CommitteeController::store');
    $routes->get('committee/edit/(:num)', 'Admin\CommitteeController::edit/$1');
    $routes->post('committee/update/(:num)', 'Admin\CommitteeController::update/$1');
    $routes->get('committee/delete/(:num)', 'Admin\CommitteeController::delete/$1');
    $routes->get('committee/clone/(:num)', 'Admin\CommitteeController::clone/$1');
});


/* =========================================================
   ACCESS DENIED
========================================================= */

$routes->get('access-denied', 'Home::accessDenied');


/* =========================================================
   ENVIRONMENT ROUTES
========================================================= */

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
