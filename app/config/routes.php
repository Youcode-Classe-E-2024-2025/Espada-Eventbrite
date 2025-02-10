<?php

use App\core\Router;
use App\controllers\front\HomeController;

$router = new Router();
$router->addRoute('GET', '/', [HomeController::class, 'index']);