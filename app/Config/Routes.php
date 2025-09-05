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
$routes->get('/faq', 'Home::faq');
