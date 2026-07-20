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
 


    // CRUD Tranches Frais
    $routes->get('tranches', 'Admin\TrancheFraisController::index');
    $routes->get('tranches/nouveau', 'Admin\TrancheFraisController::nouveau');
    $routes->post('tranches/creer', 'Admin\TrancheFraisController::creer');
    $routes->get('tranches/modifier/(:num)', 'Admin\TrancheFraisController::modifier/$1');
    $routes->post('tranches/mettreAJour/(:num)', 'Admin\TrancheFraisController::mettreAJour/$1');
    $routes->get('tranches/supprimer/(:num)', 'Admin\TrancheFraisController::supprimer/$1');

    // CRUD Type Opération
    $routes->get('types-operation', 'Admin\TypeOperationController::index');
    $routes->get('types-operation/nouveau', 'Admin\TypeOperationController::nouveau');
    $routes->post('types-operation/creer', 'Admin\TypeOperationController::creer');
    $routes->get('types-operation/modifier/(:num)', 'Admin\TypeOperationController::modifier/$1');
    $routes->post('types-operation/mettreAJour/(:num)', 'Admin\TypeOperationController::mettreAJour/$1');
    $routes->get('types-operation/supprimer/(:num)', 'Admin\TypeOperationController::supprimer/$1');

    // CRUD Préfixe Opérateur
    $routes->get('prefixes', 'Admin\PrefixeController::index');
    $routes->get('prefixes/nouveau', 'Admin\PrefixeController::nouveau');
    $routes->post('prefixes/creer', 'Admin\PrefixeController::creer');
    $routes->get('prefixes/modifier/(:num)', 'Admin\PrefixeController::modifier/$1');
    $routes->post('prefixes/mettreAJour/(:num)', 'Admin\PrefixeController::mettreAJour/$1');
    $routes->get('prefixes/supprimer/(:num)', 'Admin\PrefixeController::supprimer/$1');
});
