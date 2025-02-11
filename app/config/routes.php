<?php

use App\core\Router;
use App\controllers\front\AuthController;

$router = new Router();
$router->addRoute('GET', '/', [App\controllers\front\HomeController::class, 'index']);
$router->addRoute('GET', '/auth', [AuthController::class, 'index']);
$router->addRoute('POST', '/auth/register', [AuthController::class, 'register']);
$router->addRoute('POST', '/auth/login', [App\controllers\front\AuthController::class, 'login']);
$router->addRoute('GET', '/auth/login/google', [App\controllers\front\AuthController::class, 'loginWithGougle']);