<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once "../vendor/autoload.php";

use app\core\Router;
use app\models\Article;

$controllerRouter = new Router();
$routes = require_once "../app/config/routes.php";

foreach ($routes as $path => $controller) {
    $controllerRouter->add('GET', $path, $controller);
    $controllerRouter->add('POST', $path, $controller);
}





$controllerRouter->dispatch(strtok($_SERVER['REQUEST_URI'], "?"));
?>