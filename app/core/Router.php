<?php

namespace App\core;

use App\controllers\ErrorController;

class Router
{
    private array $routes = [];

    /**
     * Adds a new route to the routing table
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path URL path pattern
     * @param array $handler Controller and method to handle the route
     * @return void
     */
    public function addRoute(string $method, string $path, array $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * Extracts parameters from URL based on route pattern
     * 
     * @param string $routePath Original route pattern
     * @param string $requestPath Actual request URL
     * @return array|null Matched parameters or null if no match
     */
    // private function extractParams(string $routePath, string $requestPath)
    // {
    //     $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath);
    //     $pattern = "#^{$pattern}$#";

    //     if (preg_match($pattern, $requestPath, $matches)) {
    //         return array_filter($matches, fn($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY);
    //     }

    //     return null;
    // }

    /**
     * Dispatches request to appropriate controller
     * 
     * @param string $method HTTP request method
     * @param string $uri Request URI
     * @return mixed Controller response
     * @throws \Exception When route not found
     */
    public function dispatch($method, $uri)
    {
        $uri = explode('?', $uri)[0];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $pattern = $this->convertPattern($route['path']);
                if (preg_match($pattern, $uri, $params)) {
                    $handler = $route['handler'];
                    $controller = new $handler[0]();
                    // echo $controller;
                    $action = $handler[1];
                    array_shift($params);
                    return $controller->$action(array_values($params));
                }
            }
        }
        $error = new ErrorController();
        $error->notfound();
    }


    /**
     * Converts route pattern to regex pattern
     * 
     * @param string $path Route path pattern
     * @return string Regex pattern for matching
     */
    private function convertPattern($path)
    {
        $pattern = str_replace('/', '\/', $path);
        return "#^" . preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern) . "$#";
    }
}
