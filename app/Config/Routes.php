<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Front\Front::index');
$routes->get('contact', 'Front\Front::contact');
$routes->post('contact', 'Front\Front::contactSend');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->post('logout', 'Auth::logout', ['filter' => 'auth']);
$routes->get('unauthorized', static fn() => view('errors/unauthorized'));

$routes->group('admin', function ($routes) {
 


    // CRUD Préfixe Opérateur
    $routes->get('prefixes', 'Admin\PrefixeController::index');
    $routes->get('prefixes/nouveau', 'Admin\PrefixeController::nouveau');
    $routes->post('prefixes/creer', 'Admin\PrefixeController::creer');
    $routes->get('prefixes/modifier/(:num)', 'Admin\PrefixeController::modifier/$1');
    $routes->post('prefixes/mettreAJour/(:num)', 'Admin\PrefixeController::mettreAJour/$1');
    $routes->get('prefixes/supprimer/(:num)', 'Admin\PrefixeController::supprimer/$1');
});
