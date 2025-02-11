<?php

use App\core\Router;

$router = new Router();
$router->addRoute('GET', '/', [App\controllers\front\HomeController::class, 'index']);
$router->addRoute('GET', '/auth', [App\controllers\front\AuthController::class, 'index']);
$router->addRoute('POST', '/auth/register', [App\controllers\front\AuthController::class, 'register']);
$router->addRoute('POST', '/auth/login', [App\controllers\front\AuthController::class, 'login']);