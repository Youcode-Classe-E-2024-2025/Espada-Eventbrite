<?php

use App\core\Router;

$router = new Router();
$router->addRoute('GET', '/', [HomeController::class, 'index']);