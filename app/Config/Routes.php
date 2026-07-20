<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Front\AuthClientController::login');

$routes->get('client/login', 'Front\AuthClientController::login');
$routes->post('client/login', 'Front\AuthClientController::login');
$routes->post('client/logout', 'Front\AuthClientController::logout');

$routes->get('unauthorized', static fn() => view('errors/unauthorized'));

$routes->group('admin', function ($routes) {

    // CRUD Opérateurs
    $routes->get('operateurs', 'Admin\OperateurController::index');
    $routes->get('operateurs/nouveau', 'Admin\OperateurController::nouveau');
    $routes->post('operateurs/creer', 'Admin\OperateurController::creer');
    $routes->get('operateurs/modifier/(:num)', 'Admin\OperateurController::modifier/$1');
    $routes->post('operateurs/mettreAJour/(:num)', 'Admin\OperateurController::mettreAJour/$1');
    $routes->get('operateurs/supprimer/(:num)', 'Admin\OperateurController::supprimer/$1');

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
    $routes->get('types-operation/tranches-json/(:num)', 'Admin\TypeOperationController::tranchesJson/$1');

    // Rapport gains JSON
    $routes->get('rapport/gains-par-mois', 'Admin\RapportController::gainsParMois');

    // CRUD Préfixe Opérateur
    $routes->get('prefixes', 'Admin\PrefixeController::index');
    $routes->get('prefixes/nouveau', 'Admin\PrefixeController::nouveau');
    $routes->post('prefixes/creer', 'Admin\PrefixeController::creer');
    $routes->get('prefixes/modifier/(:num)', 'Admin\PrefixeController::modifier/$1');
    $routes->post('prefixes/mettreAJour/(:num)', 'Admin\PrefixeController::mettreAJour/$1');
    $routes->get('prefixes/supprimer/(:num)', 'Admin\PrefixeController::supprimer/$1');

    // Comptes clients
    $routes->get('comptes', 'Admin\CompteController::index');
    $routes->get('comptes/(:num)', 'Admin\CompteController::afficher/$1');

    // Rapport gains
    $routes->get('rapport/gains', 'Admin\RapportController::gains');
    $routes->get('rapport/montants-a-envoyer', 'Admin\RapportController::montantsAEnvoyer');
});

$routes->group('client', ['filter' => 'client'], function($routes) {
    $routes->get('solde', 'Front\CompteClientController::solde');
    $routes->get('operation', 'Front\OperationController::operation');
    $routes->post('operation', 'Front\OperationController::enregistrer');
    $routes->get('historique', 'Front\OperationController::historique');
    $routes->get('tranches-json', 'Front\OperationController::tranchesJson');
    $routes->get('numero-existe-json', 'Front\OperationController::numeroExisteJson');
    $routes->get('operateur-du-numero-json', 'Front\OperationController::operateurDuNumeroJson');
});
