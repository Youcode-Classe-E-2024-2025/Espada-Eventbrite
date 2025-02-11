<?php

use App\core\Router;

$router = new Router();
$router->addRoute('GET', '/', [App\controllers\front\HomeController::class, 'index']);
$router->addRoute('GET', '/auth', [App\controllers\front\AuthController::class, 'index']);
$router->addRoute('GET', '/admin/dashboard', [App\controllers\Back\DashboardController::class, 'index']);
$router->addRoute('GET', '/admin/users', [App\controllers\Back\DashboardController::class, 'showUsers']);
$router->addRoute('GET', '/admin/events', [App\controllers\Back\DashboardController::class, 'showEvents']);
$router->addRoute('GET', '/admin/comments', [App\controllers\Back\DashboardController::class, 'showComments']);
$router->addRoute('POST', '/auth/register', [App\controllers\front\AuthController::class, 'register']);
$router->addRoute('POST', '/auth/login', [App\controllers\front\AuthController::class, 'login']);
$router->addRoute('GET', '/auth/login/google', [App\controllers\front\AuthController::class, 'loginWithGoogle']);
$router->addRoute('GET', '/auth/google/login', [App\controllers\front\AuthController::class, 'loginWithGoogle']);
$router->addRoute('GET', '/auth/google/callback', [App\controllers\front\AuthController::class, 'handleGoogleCallback']);