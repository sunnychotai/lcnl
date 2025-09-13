<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Optional but recommended hardening:
$routes->setAutoRoute(false);

$routes->get('/', 'Home::index');
$routes->get('/events', 'Home::events');
$routes->get('/gallery', 'Home::gallery');
$routes->get('/contact', 'Home::contact');
$routes->get('/bereavement', 'Home::bereavement');
$routes->get('/membership', 'Home::membership');
$routes->get('/mahila', 'Home::mahila');
$routes->get('/faq', 'Home::faq');
$routes->get('/dbcheck', 'Test::dbcheck');
$routes->get('/aboutus', 'Home::aboutUs');
$routes->get('/sample', 'Home::sample');

$routes->get('/events/(:num)', 'Home::eventDetail/$1');

// Committee Routes (public)
$routes->get('/committee', 'Home::committee');
// $routes->get('/mahila', 'Home::mahila'); // duplicate of above, keep one
$routes->get('/yls', 'Home::yls');
$routes->get('/youth', 'Home::youth');

// FAQ Routes (public)
$routes->get('faqs', 'Home::faq');                           // grouped view
$routes->get('faqs/all', 'FaqController::all');              // all FAQs
$routes->get('faqs/(:segment)', 'FaqController::group/$1');  // FAQs by group
$routes->get('bereavement', 'FaqController::bereavement');

$routes->get('/pwhash', 'Test::pwhash');

// Auth
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');


// ---------------------------------------------------------
// PUBLIC: Membership (⚠️ keep OUTSIDE the admin group)
// ---------------------------------------------------------
$routes->group('membership', ['namespace' => 'App\Controllers'], static function ($routes) {
    // Registration form + submit
    $routes->get('register', 'MembershipController::register', ['as' => 'membership.register']);
    $routes->post('register', 'MembershipController::create',   ['as' => 'membership.create']);

    // (Future) email verification
    $routes->get('verify/(:segment)', 'MembershipController::verify/$1', ['as' => 'membership.verify']);

    // Simple success/pending page after registration
    $routes->get('success', 'MembershipController::success', ['as' => 'membership.success']);
});


// ---------------------------------------------------------
// 🔒 Admin Area (single group; avoid nesting another /admin)
// ---------------------------------------------------------
$routes->group('admin', ['filter' => 'auth'], function($routes) {

    // Dashboard (all logged-in roles)
    $routes->get('dashboard', 'Admin::dashboard');

    // Committee CRUD (any logged-in user)
    $routes->group('committee', function($routes) {
        $routes->get('', 'Admin\CommitteeController::index');   // /admin/committee
        $routes->get('create', 'Admin\CommitteeController::create');
        $routes->post('store', 'Admin\CommitteeController::store');
        $routes->get('edit/(:num)', 'Admin\CommitteeController::edit/$1');
        $routes->post('update/(:num)', 'Admin\CommitteeController::update/$1');
        $routes->get('delete/(:num)', 'Admin\CommitteeController::delete/$1');
        $routes->get('clone/(:num)', 'Admin\CommitteeController::clone/$1');
    });

    // Events CRUD (any logged-in user)
    $routes->group('events', function($routes) {
        $routes->get('', 'Admin\Events::index');   // /admin/events
        $routes->get('create', 'Admin\Events::create');
        $routes->post('store', 'Admin\Events::store');
        $routes->get('edit/(:num)', 'Admin\Events::edit/$1');
        $routes->post('update/(:num)', 'Admin\Events::update/$1');
        $routes->get('delete/(:num)', 'Admin\Events::delete/$1');
        $routes->get('clone/(:num)', 'Admin\Events::clone/$1');
    });

    // FAQs CRUD (ADMIN + WEBSITE only if you adjust filter)
    $routes->group('faqs', function($routes) {
        $routes->get('', 'Admin\FaqAdmin::index');   // /admin/faqs
        $routes->get('create', 'Admin\FaqAdmin::create');
        $routes->post('store', 'Admin\FaqAdmin::store');
        $routes->get('edit/(:num)', 'Admin\FaqAdmin::edit/$1');
        $routes->post('update/(:num)', 'Admin\FaqAdmin::update/$1');
        $routes->get('delete/(:num)', 'Admin\FaqAdmin::delete/$1');
        $routes->post('reorder', 'Admin\FaqAdmin::reorder');
    });

    // Users CRUD (🔒 ADMIN only)
    $routes->group('users', ['filter' => 'auth:ADMIN'], function($routes) {
        $routes->get('', 'Admin\Users::index');   // /admin/users
        $routes->get('create', 'Admin\Users::create');
        $routes->post('store', 'Admin\Users::store');
        $routes->get('edit/(:num)', 'Admin\Users::edit/$1');
        $routes->post('update/(:num)', 'Admin\Users::update/$1');
        $routes->get('delete/(:num)', 'Admin\Users::delete/$1');
    });

    // Members Admin (put here so it’s /admin/members)
    // If you want only Admins, add ['filter' => 'auth:ADMIN'] like Users group above.
    $routes->group('members', function($routes) {
        $routes->get('', 'Admin\MembersController::index', ['as' => 'admin.members.index']);
        $routes->get('(:num)', 'Admin\MembersController::show/$1', ['as' => 'admin.members.show']); // optional
        $routes->post('(:num)/activate', 'Admin\MembersController::activate/$1', ['as' => 'admin.members.activate']);
        $routes->post('(:num)/disable',  'Admin\MembersController::disable/$1',  ['as' => 'admin.members.disable']);
        $routes->post('(:num)/resend',   'Admin\MembersController::resend/$1',   ['as' => 'admin.members.resend']);
    });
});

// ENVIRONMENT ROUTES (keep LAST)
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
