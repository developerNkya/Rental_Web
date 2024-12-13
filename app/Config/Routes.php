<?php

use App\Controllers\OwnerController;
use CodeIgniter\Router\RouteCollection;
use App\Controllers\Login;
use App\Controllers\AdminSeederController;




/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('/login', [Login::class, 'login']);

$routes->post('/add-owner', [OwnerController::class, 'addOwner']);
$routes->get('/fetch-owners', [OwnerController::class, 'fetchOwners']);


$routes->get('/initialize-admin', [AdminSeederController::class,'initializeAdmin']);

