<?php

use App\core\Router;

$router = new Router();
$router->addRoute('GET', '/', [App\controllers\front\HomeController::class, 'index']);


$router->addRoute('GET', '/auth', [App\controllers\front\AuthController::class, 'index']);


$router->addRoute('POST', '/auth/register', [App\controllers\front\AuthController::class, 'register']);


$router->addRoute('POST', '/auth/login', [App\controllers\front\AuthController::class, 'login']);


$router->addRoute('GET', '/auth/logout', [App\controllers\front\AuthController::class, 'logout']);


$router->addRoute('GET', '/Organiser/dash', [App\controllers\front\OrganiserDashController::class, 'index']);
$router->addRoute('GET', '/Organiser/test', [App\controllers\front\OrganiserDashController::class, 'serviceTest']);


// $router->addRoute('GET', '/auth/login/google', [App\controllers\front\AuthController::class, 'loginWithGoogle']);
// $router->addRoute('GET', '/auth/google/login', [App\controllers\front\AuthController::class, 'loginWithGoogle']);
// $router->addRoute('GET', '/auth/google/callback', [App\controllers\front\AuthController::class, 'handleGoogleCallback']);