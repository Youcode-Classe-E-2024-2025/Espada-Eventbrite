<?php

use App\Core\Router;

$router = new Router();
$router->addRoute('GET', '/', [HomeController::class, 'index']);
