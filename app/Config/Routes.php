<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Login;
use App\Controllers\AdminSeederController;




/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('/login', [Login::class, 'login']);
$routes->get('/initialize-admin', [AdminSeederController::class,'initializeAdmin']);
