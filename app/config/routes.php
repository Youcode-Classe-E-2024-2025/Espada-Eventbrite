<?php

use App\core\Router;

$router = new Router();
$router->addRoute('GET', '/', [App\controllers\front\HomeController::class, 'index']);


$router->addRoute('GET', '/auth', [App\controllers\front\AuthController::class, 'index']);
$router->addRoute('GET', '/dashboard', [App\controllers\back\DashboardController::class, 'index']);
// $router->addRoute('GET', '/admin/users', [App\controllers\back\DashboardController::class, 'showUsers']);
$router->addRoute('GET', '/admin/events', [App\controllers\back\AdminEventController::class, 'index']);
$router->addRoute('GET', '/admin/events/search', [App\controllers\back\AdminEventController::class, 'search']);
$router->addRoute('POST', '/admin/events/status', [App\controllers\back\AdminEventController::class, 'updateStatus']);
$router->addRoute('POST', '/admin/events/delete', [App\controllers\back\AdminEventController::class, 'delete']);
$router->addRoute('GET', '/admin/comments', [App\controllers\back\DashboardController::class, 'showComments']);
$router->addRoute('POST', '/auth/register', [App\controllers\front\AuthController::class, 'register']);


$router->addRoute('POST', '/auth/login', [App\controllers\front\AuthController::class, 'login']);


$router->addRoute('GET', '/auth/logout', [App\controllers\front\AuthController::class, 'logout']);



// $router->addRoute('GET', '/Organiser/dash', [App\controllers\front\OrganiserDashController::class, 'index']);
$router->addRoute('GET', '/Organiser/tickets', [App\controllers\front\OrganiserDashController::class, 'tickets']);
$router->addRoute('GET', '/Organiser/events', [App\controllers\front\OrganiserDashController::class, 'events']);
$router->addRoute('GET', '/Organiser/test', [App\controllers\front\OrganiserDashController::class, 'serviceTest']);


// $router->addRoute('GET', '/events/search', [App\controllers\front\EventController::class, 'search']);

$router->addRoute('GET', '/events/list', [App\controllers\front\EventController::class, 'index']);
$router->addRoute('GET', '/event/details/{id}', [App\controllers\front\EventController::class, 'eventDetails']);
$router->addRoute('GET', '/events/booking', [App\controllers\front\ReservationController::class, 'index']);
// $router->addRoute('GET', '/events/list/searchByCaty', [App\controllers\front\EventController::class, 'serchByCategory']);

$router->addRoute('POST', '/organiser/dash', [App\controllers\front\OrganiserDashController::class, 'index']);
$router->addRoute('GET', '/reservation', [App\controllers\front\ReservationController::class, 'index']);


$router->addRoute('GET', '/admin/users', [App\controllers\back\AdminUserController::class, 'index']);
$router->addRoute('GET', '/admin/users/search', [App\controllers\back\AdminUserController::class, 'search']);
$router->addRoute('GET', '/admin/users/filter', [App\controllers\back\AdminUserController::class, 'filter']);
$router->addRoute('POST', '/admin/users/status', [App\controllers\back\AdminUserController::class, 'updateStatus']);
// $router->addRoute('POST', '/admin/users/banUser', [App\controllers\back\AdminUserController::class, 'updateStatus']);




// $router->addRoute('GET', '/auth/login/google', [App\controllers\front\AuthController::class, 'loginWithGoogle']);
// $router->addRoute('GET', '/auth/google/login', [App\controllers\front\AuthController::class, 'loginWithGoogle']);
// $router->addRoute('GET', '/auth/google/callback', [App\controllers\front\AuthController::class, 'handleGoogleCallback']);
