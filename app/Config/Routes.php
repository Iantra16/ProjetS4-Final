<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Front\Front::index');

$routes->get('client/login', 'Front\AuthClientController::login');
$routes->post('client/login', 'Front\AuthClientController::login');
$routes->post('client/logout', 'Front\AuthClientController::logout');

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

$routes->group('client', ['filter' => 'client'], function($routes) {
    $routes->get('solde', 'Front\CompteClientController::solde');
    $routes->get('historique', 'Front\OperationController::historique');
    $routes->get('depot', 'Front\OperationController::depot');
    $routes->post('depot', 'Front\OperationController::depot');
    $routes->get('retrait', 'Front\OperationController::retrait');
    $routes->post('retrait', 'Front\OperationController::retrait');
    $routes->get('transfert', 'Front\OperationController::transfert');
    $routes->post('transfert', 'Front\OperationController::transfert');
});
