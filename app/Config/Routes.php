<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('home', 'AdminController::home');
    $routes->post('insert', 'AdminController::insert');
    $routes->post('edit/(:any)', 'AdminController::edit/$1');
    $routes->get('delete/(:any)', 'AdminController::delete/$1');
    $routes->get('scan', 'AdminController::scan');
    $routes->get('findMahasiswa/(:any)', 'AdminController::findMahasiswa/$1');

    $routes->get('export', 'AdminController::export');
    $routes->post('import', 'AdminController::import');
});