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
$routes->get('faqs', 'Home::faq');       // grouped view
$routes->get('faqs/all', 'FaqController::all');     // all FAQs
$routes->get('faqs/(:segment)', 'FaqController::group/$1'); // FAQs by group
$routes->get('bereavement', 'FaqController::bereavement');