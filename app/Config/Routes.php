<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AttendanceController::index');
$routes->get('scanner', 'AttendanceController::scanner');

$routes->group('auth', function ($routes) {
    $routes->get('login', 'AuthController::index');
    $routes->post('login', 'AuthController::processLogin');
    $routes->post('logout', 'AuthController::processLogout');
    $routes->get('me', 'AuthController::getUserByToken');
});

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->group('api', ['filter' => 'api'], function ($routes) {
        $routes->group('students', function ($routes) {
            $routes->get('(:any)', 'StudentController::getStudents/$1');
            $routes->get('/', 'StudentController::getStudents');
            $routes->post('/', 'StudentController::addStudent');
            $routes->put('(:any)', 'StudentController::editStudent/$1');
            $routes->delete('(:any)', 'StudentController::deleteStudent/$1');
            $routes->post('import', 'StudentController::importStudents');
        });
        $routes->group('internships', function ($routes) {
            $routes->get('(:any)', 'InternshipController::getInternships/$1');
            $routes->get('/', 'InternshipController::getInternships');
            $routes->post('/', 'InternshipController::addInternship');
            $routes->put('(:any)', 'InternshipController::editInternship/$1');
            $routes->delete('(:any)', 'InternshipController::deleteInternship/$1');
        });
    });

    $routes->get('students', 'AdminController::index');
    $routes->get('internships', 'AdminController::internship');
});
