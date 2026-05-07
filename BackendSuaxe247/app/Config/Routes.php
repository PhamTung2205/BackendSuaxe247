<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->group('api', ['namespace' => 'App\Controllers'], function($routes) {
    // $routes->get('users', 'CUser::index'); // API lấy danh sách user
    // bạn có thể thêm $routes->get('spare-part', 'CSparePart::index') ở đây nếu chưa có
});
