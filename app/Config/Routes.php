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

$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    $routes->get('dashboard', static fn() => 'Dashboard admin (à faire plus tard)');

    $routes->get('entites', 'Admin\Entites::index');
    $routes->get('entites/new', 'Admin\Entites::new');
    $routes->post('entites/create', 'Admin\Entites::create');
    $routes->get('entites/edit/(:num)', 'Admin\Entites::edit/$1');
    $routes->post('entites/update/(:num)', 'Admin\Entites::update/$1');
    $routes->get('entites/delete/(:num)', 'Admin\Entites::delete/$1');
    $routes->get('entites/export/pdf', 'Admin\Entites::exportPdf');
    $routes->get('entites/export/excel', 'Admin\Entites::exportExcel');
    $routes->get('entites/import', 'Admin\Entites::importForm');
    $routes->post('entites/import', 'Admin\Entites::import');
    $routes->get('stats', 'Admin\Stats::index');
});
