<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
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

$routes->get('/events/(:num)', 'Home::eventDetail/$1');

// Committee Routes (public)
$routes->get('/committee', 'Home::committee');
$routes->get('/mahila', 'Home::mahila');
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

// 🔒 Admin Area
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

});
