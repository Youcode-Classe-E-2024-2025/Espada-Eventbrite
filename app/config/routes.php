<?php

use App\core\Router;

$router = new Router();
$router->addRoute('GET', '/', [App\controllers\front\HomeController::class, 'index']);


$router->addRoute('GET', '/auth', [App\controllers\front\AuthController::class, 'index']);
$router->addRoute('GET', '/admin/dashboard', [App\controllers\back\DashboardController::class, 'index']);
$router->addRoute('GET', '/admin/users', [App\controllers\back\DashboardController::class, 'showUsers']);
$router->addRoute('GET', '/admin/events', [App\controllers\back\DashboardController::class, 'showEvents']);
$router->addRoute('GET', '/admin/comments', [App\controllers\back\DashboardController::class, 'showComments']);
$router->addRoute('POST', '/auth/register', [App\controllers\front\AuthController::class, 'register']);


$router->addRoute('POST', '/auth/login', [App\controllers\front\AuthController::class, 'login']);


$router->addRoute('POST', '/auth/logout', [App\controllers\front\AuthController::class, 'logout']);

$router->addRoute('POST', '/organiser/dash', [App\controllers\front\OrganiserDashController::class, 'index']);


// $router->addRoute('GET', '/auth/login/google', [App\controllers\front\AuthController::class, 'loginWithGoogle']);
// $router->addRoute('GET', '/auth/google/login', [App\controllers\front\AuthController::class, 'loginWithGoogle']);
// $router->addRoute('GET', '/auth/google/callback', [App\controllers\front\AuthController::class, 'handleGoogleCallback']);