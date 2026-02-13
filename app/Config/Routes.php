<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(false);

/* =========================================================
   PUBLIC ROUTES
========================================================= */

$routes->post('stripe/webhook', 'StripeWebhookController::handle');

$routes->get('/', 'Home::index');
$routes->get('aboutus', 'Home::aboutUs');
$routes->get('sample', 'Home::sample');
$routes->get('privacy', 'Home::privacy');
$routes->get('gallery', 'Home::gallery');
$routes->get('contact', 'Home::contact');
$routes->get('membership', 'Home::membership');
$routes->get('faq', 'Home::faq');
$routes->get('bereavement', 'Home::bereavement');
$routes->get('tabletennis', 'Home::tabletennis');
$routes->post('contact/send', 'ContactController::send');

$routes->get('sitemap.xml', 'Sitemap::index');

/* =========================================================
   PUBLIC EVENTS
========================================================= */

$routes->get('events', 'Events::index');
$routes->get('events/(:num)', 'Events::eventDetail/$1');

/* =========================================================
   EVENT REGISTRATION
========================================================= */

$routes->get('events/register', 'EventRegistrationController::register');
$routes->get('events/register/(:segment)', 'EventRegistrationController::register/$1');
$routes->post('events/register/submit', 'EventRegistrationController::submit');
$routes->get('events/thanks', 'EventRegistrationController::thankyou');

/* =========================================================
   AUTH
========================================================= */

$routes->get('auth/login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

/* =========================================================
   MEMBER AREA (PUBLIC)
========================================================= */

$routes->group('membership', ['namespace' => 'App\Controllers'], function ($routes) {

    $routes->get('register', 'MembershipController::register');
    $routes->post('register', 'MembershipController::create');
    $routes->get('verify/(:segment)', 'MembershipController::verify/$1');
    $routes->get('success', 'MembershipController::success');
    $routes->get('resend-verification', 'MembershipController::resendVerification');
    $routes->get('activated', 'MembershipController::activated');

});

/* =========================================================
   MEMBER ACCOUNT AREA
========================================================= */

$routes->group('account', [
    'namespace' => 'App\Controllers\Account',
    'filter' => 'authMember'
], function ($routes) {

    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('profile', 'ProfileController::edit');
    $routes->post('profile', 'ProfileController::update');

    $routes->get('family', 'FamilyController::index');
    $routes->post('family/create', 'FamilyController::create');
    $routes->post('family/update/(:num)', 'FamilyController::update/$1');
    $routes->post('family/delete/(:num)', 'FamilyController::delete/$1');

    // Stripe Upgrade
    $routes->post('membership/upgrade/checkout', 'MembershipUpgradeController::checkout');
    $routes->get('membership/upgrade/success', 'MembershipUpgradeController::success');
    $routes->get('membership/upgrade/cancel', 'MembershipUpgradeController::cancel');

});

/* =========================================================
   ADMIN AREA
========================================================= */

$routes->group('admin', [
    'namespace' => 'App\Controllers\Admin',
    'filter' => 'authAdmin'
], function ($routes) {

    /* --------------------
       SYSTEM
    -------------------- */
    $routes->group('system', function ($routes) {
        $routes->get('dashboard', 'Admin::dashboard');
        $routes->get('cron-logs', 'CronController::index');
        $routes->get('cron-logs/(:num)', 'CronController::show/$1');
    });

    /* --------------------
       CONTENT (Events, FAQs, Committee)
    -------------------- */
    $routes->group('content', ['filter' => 'authAdmin:ADMIN,EVENTS,WEBSITE'], function ($routes) {

        /* ---- Events ---- */
        $routes->group('events', function ($routes) {
            $routes->get('', 'Events::index');
            $routes->get('create', 'Events::create');
            $routes->post('store', 'Events::store');
            $routes->get('edit/(:num)', 'Events::edit/$1');
            $routes->post('update/(:num)', 'Events::update/$1');
            $routes->get('delete/(:num)', 'Events::delete/$1');
            $routes->get('clone/(:num)', 'Events::clone/$1');

            $routes->get('event-registrations', 'EventRegistrationController::index');
            $routes->post('event-registrations/summary', 'EventRegistrationController::summary');
        });

        /* ---- FAQs ---- */
        $routes->group('faqs', function ($routes) {
            $routes->get('', 'FaqAdmin::index');
            $routes->get('create', 'FaqAdmin::create');
            $routes->post('store', 'FaqAdmin::store');
            $routes->get('edit/(:num)', 'FaqAdmin::edit/$1');
            $routes->post('update/(:num)', 'FaqAdmin::update/$1');
            $routes->get('delete/(:num)', 'FaqAdmin::delete/$1');
            $routes->post('reorder', 'FaqAdmin::reorder');
        });

        /* ---- Committee ---- */
        $routes->group('committee', function ($routes) {
            $routes->get('', 'CommitteeController::index');
            $routes->get('create', 'CommitteeController::create');
            $routes->post('store', 'CommitteeController::store');
            $routes->get('edit/(:num)', 'CommitteeController::edit/$1');
            $routes->post('update/(:num)', 'CommitteeController::update/$1');
            $routes->get('delete/(:num)', 'CommitteeController::delete/$1');
            $routes->get('clone/(:num)', 'CommitteeController::clone/$1');
        });

    });

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
