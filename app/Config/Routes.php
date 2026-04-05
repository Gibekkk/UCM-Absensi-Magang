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
        $routes->get('viewToday', 'AttendanceController::viewTodayAttendances');
        $routes->get('dateRange/(:num)/(:num)/(:num)/(:num)/(:num)/(:num)/(:alpha)', 'AttendanceController::getAttendancesByDateRange/$1/$2/$3/$4/$5/$6/$7');
        $routes->get('nim/(:any)/(:alpha)', 'AttendanceController::getAttendancesByNIM/$1/$2');
        $routes->get('department/(:any)/(:alpha)', 'AttendanceController::getAttendancesByDepartment/$1/$2');
        $routes->get('internship/(:any)/(:alpha)', 'AttendanceController::getAttendancesByInternship/$1/$2');
        // $routes->get('/', 'AttendanceController::getAttendances');
        // $routes->get('(:any)', 'AttendanceController::getAttendances/$1');
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
            $routes->patch('setIsActive/(:any)/(:any)', 'StudentController::setIsActive/$1/$2');
            // $routes->delete('(:any)', 'StudentController::deleteStudent/$1');
            $routes->post('import', 'StudentController::importStudents');
        });
        $routes->group('internships', function ($routes) {
            $routes->get('department', 'InternshipController::getDepartments');
            $routes->get('department/(:any)', 'InternshipController::findInternshipByDepartment/$1');
            $routes->get('(:any)', 'InternshipController::getInternships/$1');
            $routes->get('/', 'InternshipController::getInternships');
            $routes->post('/', 'InternshipController::addInternship');
            $routes->patch('setIsActive/(:any)/(:any)', 'InternshipController::setIsActive/$1/$2');
            $routes->put('(:any)', 'InternshipController::editInternship/$1');
            // // $routes->delete('(:any)', 'InternshipController::deleteInternship/$1');
        });
        $routes->group('users', ['filter' => 'superAdmin'], function ($routes) {
            $routes->get('(:any)', 'UserController::getUsers/$1');
            $routes->get('/', 'UserController::getUsers');
            $routes->post('/', 'UserController::addUser');
            $routes->patch('setIsActive/(:any)/(:any)', 'UserController::setIsActive/$1/$2');
            $routes->put('(:any)', 'UserController::editUser/$1');
            // // $routes->delete('(:any)', 'UserController::deleteUser/$1');
        });
    });

    $routes->get('students', 'AdminController::index');
    $routes->get('attendance', 'AdminController::attendance');
    $routes->get('internships', 'AdminController::internship');
    $routes->get('users', 'AdminController::users', ['filter' => 'superAdmin']);
});
