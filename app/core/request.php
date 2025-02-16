<?php
namespace App\core;
class request {
    public function get(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    public function post(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }
}