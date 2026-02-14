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

// Core pages
$routes->get('/', 'Home::index');
$routes->get('aboutus', 'Home::aboutUs');
$routes->get('sample', 'Home::sample');
$routes->get('privacy', 'Home::privacy');
$routes->get('gallery', 'Home::gallery');
$routes->get('contact', 'Home::contact');
$routes->post('contact/send', 'ContactController::send');
$routes->get('membership', 'Home::membership');
$routes->get('faq', 'Home::faq');
$routes->get('bereavement', 'Home::bereavement');
$routes->get('tabletennis', 'Home::tabletennis');

$routes->get('committee', 'Committee::committee');
$routes->get('yls', 'Committee::yls');
$routes->get('youth', 'Committee::youth');
$routes->get('mahila', 'Committee::mahila');

$routes->get('sitemap.xml', 'Sitemap::index');


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
$routes->get('membership/login', 'MemberAuth::login', ['as' => 'membership.login']);
$routes->post('membership/login', 'MemberAuth::attempt');
$routes->get('membership/logout', 'MemberAuth::logout');

// Register
$routes->get('membership/register', 'MembershipController::register');
$routes->post('membership/register', 'MembershipController::create');

// Email verification
$routes->get('membership/verify/(:segment)', 'MembershipController::verify/$1');
$routes->get('membership/success', 'MembershipController::success');
$routes->get('membership/resend-verification', 'MembershipController::resendVerification');
$routes->get('membership/activated', 'MembershipController::activated');

// Forgot / Reset
$routes->get('membership/forgot', 'Account\PasswordController::forgot');
$routes->post('membership/forgot', 'Account\PasswordController::sendReset');
$routes->get('membership/reset/(:segment)', 'Account\PasswordController::reset/$1');
$routes->post('membership/reset', 'Account\PasswordController::doReset');


/* =========================================================
   MEMBER ACCOUNT AREA (AUTH REQUIRED)
========================================================= */

$routes->group('account', [
    'namespace' => 'App\Controllers\Account',
    'filter'    => 'authMember'
], function ($routes) {

    $routes->get('dashboard', 'DashboardController::index', ['as' => 'account.dashboard']);
    $routes->get('profile', 'ProfileController::edit');
    $routes->post('profile', 'ProfileController::update');

    $routes->get('family', 'FamilyController::index');
    $routes->post('family/create', 'FamilyController::create');
    $routes->post('family/update/(:num)', 'FamilyController::update/$1');
    $routes->post('family/delete/(:num)', 'FamilyController::delete/$1');

    // Stripe upgrade
    $routes->post('membership/upgrade/checkout', 'MembershipUpgradeController::checkout');
    $routes->get('membership/upgrade/success', 'MembershipUpgradeController::success');
    $routes->get('membership/upgrade/cancel', 'MembershipUpgradeController::cancel');
});


/* =========================================================
   ADMIN SYSTEM (DASHBOARD + EMAILS + CRON)
========================================================= */

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
