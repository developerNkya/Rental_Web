<?php

use App\Controllers\ElectricityController;
use App\Controllers\LanguageController;
use App\Controllers\NotificationController;
use App\Controllers\OwnerController;
use App\Controllers\TenantController;
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

$routes->get('/fetch-tenant-details', [TenantController::class, 'fetchTenant']);
$routes->get('/fetch-tenant-contract', [TenantController::class, 'fetchTenantContract']);

$routes->post('/add-collection', [OwnerController::class, 'addCollection']);
$routes->get('/fetch-collection', [OwnerController::class, 'fetchCollection']);

$routes->post('/add-tenant', [OwnerController::class, 'addTenant']);
$routes->get('/fetch-tenants', [OwnerController::class, 'fetchTenants']);
$routes->get('/owner-summary', [OwnerController::class, 'OwnerSummary']);

$routes->post('/update-electricity', [ElectricityController::class, 'updateElectricity']);
$routes->get('/initialize-admin', [AdminSeederController::class,'initializeAdmin']);


$routes->post('/update-user-language', [LanguageController::class, 'updateUserLanguage']);


$routes->get('sendRentNotifications', [NotificationController::class, 'sendRentNotifications']);


$routes->get('/owner-profile', [OwnerController::class, 'ownerProfile']);

$routes->get('/delete_item/(:num)/type/(:segment)', [OwnerController::class, 'deleteItem']);

