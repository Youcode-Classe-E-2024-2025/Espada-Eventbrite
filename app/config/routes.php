<?php

use App\core\Router;

$router = new Router();
$router->addRoute('GET', '/', [App\controllers\front\HomeController::class, 'index']);
