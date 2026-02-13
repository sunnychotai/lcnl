<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(false);

/* -----------------------
   Public pages
------------------------*/

$routes->post('stripe/webhook', 'StripeWebhookController::handle');

$routes->get('/', 'Home::index');
$routes->get('events', 'Events::index');
$routes->get('events/(:num)', 'Events::eventDetail/$1');

$routes->get('sitemap.xml', 'Sitemap::index');
$routes->get('aboutus', 'Home::aboutUs');
$routes->get('sample', 'Home::sample');
$routes->get('privacy', 'Home::privacy');
$routes->post('contact/send', 'ContactController::send');
$routes->get('gallery', 'Home::gallery');
$routes->get('contact', 'Home::contact');
$routes->get('membership', 'Home::membership');
$routes->get('faq', 'Home::faq');
$routes->get('lcf', 'Committee::lcf');
$routes->get('bereavement', 'Home::bereavement');
$routes->get('tabletennis', 'Home::tabletennis');

/* Committee Routes */
$routes->get('committee', 'Committee::committee');
$routes->get('yls', 'Committee::yls');
$routes->get('youth', 'Committee::youth');
$routes->get('mahila', 'Committee::mahila');

/* Event registrations */
$routes->get('events/register', 'EventRegistrationController::register');
$routes->get('events/register/(:segment)', 'EventRegistrationController::register/$1');
$routes->post('events/register/submit', 'EventRegistrationController::submit');
$routes->get('events/thanks', 'EventRegistrationController::thankyou');

/* Dev utilities */
$routes->get('dbcheck', 'Test::dbcheck');
$routes->get('pwhash', 'Test::pwhash');
$routes->get('email', 'Test::email');

/* -----------------------
   FAQs (public)
------------------------*/
$routes->get('faqs', 'Home::faq');
$routes->get('faqs/all', 'FaqController::all');
$routes->get('faqs/(:segment)', 'FaqController::group/$1');

/* -----------------------
   Auth
------------------------*/
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

/* -----------------------
   Member Account
------------------------*/
$routes->get('account', 'Account\DashboardController::index');

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

});

/* =========================================================
   🔒 ADMIN AREA — EXACT STRUCTURE RESTORED
========================================================= */

/* --- ADMIN DASHBOARD --- */
$routes->group('admin/system', ['filter' => 'authAdmin'], function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('cron-logs', 'Admin\CronController::index');
    $routes->get('cron-logs/(:num)', 'Admin\CronController::show/$1');
});

/* --- ADMIN ONLY (ADMIN role) --- */
$routes->group('admin/system', ['filter' => 'authAdmin:ADMIN'], function ($routes) {

    $routes->get('emails', 'Admin\Emails::index');
    $routes->get('emails/view/(:num)', 'Admin\Emails::view/$1');
    $routes->get('emails/retry/(:num)', 'Admin\Emails::retry/$1');
    $routes->get('emails/delete/(:num)', 'Admin\Emails::delete/$1');

    $routes->post('emails/data', 'Admin\EmailDataController::list');
    $routes->get('emails/stats', 'Admin\EmailDataController::stats');


});

/* --- CONTENT ADMIN --- */
$routes->group('admin/content', ['filter' => 'authAdmin:ADMIN,EVENTS,WEBSITE'], function ($routes) {

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

/* -----------------------
   Access Denied
------------------------*/
$routes->get('access-denied', 'Home::accessDenied');

/* -----------------------
   Environment routes
------------------------*/
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
