<?php

use App\core\Router;

$router = new Router();
$router->addRoute('GET', '/', [App\controllers\front\HomeController::class, 'index']);


$router->addRoute('GET', '/auth', [App\controllers\front\AuthController::class, 'index']);
$router->addRoute('GET', '/dashboard', [App\controllers\Back\DashboardController::class, 'index']);
$router->addRoute('GET', '/admin/users', [App\controllers\Back\DashboardController::class, 'showUsers']);
$router->addRoute('GET', '/admin/events', [App\controllers\Back\DashboardController::class, 'showEvents']);
$router->addRoute('GET', '/admin/comments', [App\controllers\Back\DashboardController::class, 'showComments']);
$router->addRoute('POST', '/auth/register', [App\controllers\front\AuthController::class, 'register']);


$router->addRoute('POST', '/auth/login', [App\controllers\front\AuthController::class, 'login']);


$router->addRoute('GET', '/auth/logout', [App\controllers\front\AuthController::class, 'logout']);

$router->addRoute('GET', '/reservation', [App\controllers\front\ReservationController::class, 'getBooking']);

$router->addRoute('GET', '/Organiser/dash', [App\controllers\front\OrganiserDashController::class, 'index']);
$router->addRoute('GET', '/Organiser/test', [App\controllers\front\OrganiserDashController::class, 'serviceTest']);


$router->addRoute('GET', '/events/search', [App\controllers\front\EventController::class, 'search']);


$router->addRoute('GET', '/admin/users', [App\controllers\back\AdminUserController::class, 'index']);
$router->addRoute('GET', '/admin/users/search', [App\controllers\back\AdminUserController::class, 'search']);
$router->addRoute('GET', '/admin/users/filter', [App\controllers\back\AdminUserController::class, 'filter']);
$router->addRoute('POST', '/admin/users/status', [App\controllers\back\AdminUserController::class, 'updateStatus']);
$router->addRoute('POST', '/admin/users/banUser', [App\controllers\back\AdminUserController::class, 'updateStatus']);


// $router->addRoute('GET', '/auth/login/google', [App\controllers\front\AuthController::class, 'loginWithGoogle']);
// $router->addRoute('GET', '/auth/google/login', [App\controllers\front\AuthController::class, 'loginWithGoogle']);
// $router->addRoute('GET', '/auth/google/callback', [App\controllers\front\AuthController::class, 'handleGoogleCallback']);
