<?php
namespace App\Core;

class Router {
    protected $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Hapus query string dari path (contoh: /users?id=1 jadi /users)
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }

        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            http_response_code(404);
            echo "404 - Not Found";
            return;
        }

        // Jika callback adalah Array (Controller, Method)
        if (is_array($callback)) {
            $controller = new $callback[0]();
            $method = $callback[1];
            return call_user_func([$controller, $method]);
        }

        // Jika callback adalah Function biasa
        echo call_user_func($callback);
    }
}
