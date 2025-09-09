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
$routes->get('/committee', 'Home::committee');
$routes->get('/events/(:num)', 'Home::eventDetail/$1');


// FAQ routes
$routes->get('faqs', 'Home::faq');                    // grouped view
$routes->get('faqs/all', 'FaqController::all');       // all FAQs
$routes->get('faqs/(:segment)', 'FaqController::group/$1'); // FAQs by group
$routes->get('bereavement', 'FaqController::bereavement');

$routes->get('/pwhash', 'Test::pwhash');

// Auth
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

// Admin
$routes->get('admin/dashboard', 'Admin::dashboard');

// 🔒 Admin group (locked with auth filter)
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    
    // Dashboard
    $routes->get('dashboard', 'Admin::dashboard');

    // Committee CRUD
    $routes->get('committee', 'Admin\CommitteeController::index');
    $routes->get('committee/create', 'Admin\CommitteeController::create');
    $routes->post('committee/store', 'Admin\CommitteeController::store');
    $routes->get('committee/edit/(:num)', 'Admin\CommitteeController::edit/$1');
    $routes->post('committee/update/(:num)', 'Admin\CommitteeController::update/$1');
    $routes->get('committee/delete/(:num)', 'Admin\CommitteeController::delete/$1');
    $routes->get('committee/clone/(:num)', 'Admin\CommitteeController::clone/$1');

    // Events CRUD
    $routes->get('events', 'Admin\Events::index');
    $routes->get('events/create', 'Admin\Events::create');
    $routes->post('events/store', 'Admin\Events::store');
    $routes->get('events/edit/(:num)', 'Admin\Events::edit/$1');
    $routes->post('events/update/(:num)', 'Admin\Events::update/$1');
    $routes->get('events/delete/(:num)', 'Admin\Events::delete/$1');
    $routes->get('events/clone/(:num)', 'Admin\Events::clone/$1');
});
