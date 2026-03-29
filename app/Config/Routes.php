<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AttendanceController::scanner');
$routes->get('camera', 'AttendanceController::camera');

$routes->group('api', ['filter' => 'api'], function ($routes) {
    $routes->post('attend', 'AttendanceController::createAttendance');
    $routes->group('attendance', function ($routes) {
        $routes->get('today', 'AttendanceController::getTodayAttendances');
        $routes->get('(:num)/(:num)/(:num)', 'AttendanceController::getDateAttendances/$1/$2/$3');
        $routes->get('/', 'AttendanceController::getAttendances');
        $routes->get('(:any)', 'AttendanceController::getAttendances/$1');
    });
});

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
    $routes->get('attendance', 'AdminController::attendance');
    $routes->get('internships', 'AdminController::internship');
});
