<?php
namespace app\core;

class Router {
    private array $routes = [];
    private array $middleware = [];

    // '' => 
    // array (size=2)
    //   0 => string 'app\controllers\HomeController' (length=30)
    //   1 => string 'index' (length=5)
    public function add(string $method, string $path, $controller) {
        $path = $this->normalizePath($path);
        $path = preg_replace('/{([^}]+)}/', '(?<\\1>[^/]+)', $path);
        $this->routes[] = [
            'path' => $path,
            'method' => strtoupper($method),
            'controller' => $controller
        ];
    }


    public function setMiddleware($middleware) {
        if (is_callable($middleware)) {
            $this->middleware[] = $middleware;
        } elseif (is_array($middleware)) {
            foreach ($middleware as $m) {
                if (is_callable($m)) {
                    $this->middleware[] = $m;
                }
            }
        }
    }
    
    public function group(array $options, callable $callback) {
        $this->setMiddleware($options['middleware']);
        call_user_func($callback, $this);
    }

    private function runMiddleware() {
        foreach ($this->middleware as $middleware) {
            
            $result = $middleware();
            if ($result === false) {
                return false;
            }
        }
        return true;
    }

    private function normalizePath(string $path): string {
        $path = trim($path, '/');
        $path = "/{$path}/";
        $path = preg_replace('#[/]{2,}#', '/', $path);
        return $path;
    
    }

    public function dispatch(string $path) {
        // Run middleware stack
        if (!$this->runMiddleware()) {
            return;
        }


        $path = $this->normalizePath($path);
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        
        foreach ($this->routes as $route) {
            $pattern = "#^{$route['path']}$#";
            
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                if (is_array($route['controller'])) {
                    [$class, $function] = $route['controller'];
                    $controllerInstance = new $class();
                    
                    if (!empty($params)) {
                        call_user_func_array([$controllerInstance, $function], array_values($params));
                    } else {
                        $controllerInstance->{$function}();
                    
                    }
                    return;
                }
            }
        }
        
        // No route found
        http_response_code(404);
        echo "404 - Page not found";
    }
}
