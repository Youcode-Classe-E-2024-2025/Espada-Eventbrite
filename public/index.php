<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../app/config/routes.php';
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath = $_SERVER['REQUEST_URI'];

try {

    echo $router->dispatch($requestMethod, $requestPath);
} catch (Exception $e) {
    echo 'Page not found';
}
