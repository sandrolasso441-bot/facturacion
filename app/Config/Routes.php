<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// 1. Rutas Públicas (Login)
$routes->get('login', 'AuthController::index');
$routes->post('login/authenticate', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

// 2. Rutas Protegidas que devuelven Vistas HTML (Solo requieren Login)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Home::index');
    $routes->get('facturacion', 'Home::index');
    
    // Vista principal de categorías (HTML)
    $routes->get('categorias', 'CategoriasController::index');
    $routes->get('marcas', 'MarcasController::index'); // Agregado para marcas
    $routes->get('clientes', 'ClientesController::index');
});

// 3. Rutas de la API / Endpoints (Requieren Login Y Petición AJAX)
$routes->group('categorias', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('getCategorias', 'CategoriasController::getCategorias');
    $routes->post('guardar', 'CategoriasController::guardar');
    $routes->get('obtener/(:num)', 'CategoriasController::obtener/$1');
    $routes->delete('eliminar/(:num)', 'CategoriasController::eliminar/$1');
});

$routes->group('marcas', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('getMarcas', 'MarcasController::getMarcas');
    $routes->post('guardar', 'MarcasController::guardar');
    $routes->get('obtener/(:num)', 'MarcasController::obtener/$1');
    $routes->delete('eliminar/(:num)', 'MarcasController::eliminar/$1');
});

// 3. Rutas de la API / Endpoints (Requieren Login Y Petición AJAX)
$routes->group('clientes', ['filter' => ['auth', 'ajax']], function($routes) {
    $routes->get('getClientes', 'ClientesController::getClientes');
    $routes->post('guardar', 'ClientesController::guardar');
    $routes->get('obtener/(:num)', 'ClientesController::obtener/$1');
    $routes->delete('eliminar/(:num)', 'ClientesController::eliminar/$1');
});
