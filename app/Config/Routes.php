<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/csv-import', 'CsvController::index');
$routes->post('/csv-import/upload', 'CsvController::upload');

