<?php

require_once __DIR__ . '/../vendor/autoload.php';

use RidwanHidayat\BackendTest\App\Router;
use RidwanHidayat\BackendTest\Config\Database;
use RidwanHidayat\BackendTest\Middleware\AuthMiddleware;
use RidwanHidayat\BackendTest\Controller\TaskController;

Database::getConnection('prod');

Router::add('POST', '/token', TaskController::class, 'token', []);
Router::add('GET', '/tasks', TaskController::class, 'findAll', [AuthMiddleware::class]);
Router::add('GET', '/tasks/([0-9]*)', TaskController::class, 'findById', [AuthMiddleware::class]);
Router::add('POST', '/tasks', TaskController::class, 'save', [AuthMiddleware::class]);
Router::add('PUT', '/tasks/([0-9]*)', TaskController::class, 'update', [AuthMiddleware::class]);
Router::add('DELETE', '/tasks', TaskController::class, 'delete', [AuthMiddleware::class]);

Router::run();