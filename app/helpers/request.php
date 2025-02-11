<?php
/**
 * Request Helper Functions
 * Provides utility methods for handling HTTP requests
 */

if (!function_exists('get_request_method')) {
    /**
     * Get the current HTTP request method
     * 
     * @return string
     */
    function get_request_method() {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}

if (!function_exists('get_request_param')) {
    /**
     * Safely retrieve a request parameter
     * 
     * @param string $key Parameter name
     * @param mixed $default Default value if parameter not found
     * @param string $method Request method (GET, POST, etc.)
     * @return mixed
     */
    function get_request_param($key, $default = null, $method = null) {
        $method = strtoupper($method ?? get_request_method());
        
        switch ($method) {
            case 'GET':
                return $_GET[$key] ?? $default;
            case 'POST':
                return $_POST[$key] ?? $default;
            default:
                $input = file_get_contents('php://input');
                $params = json_decode($input, true) ?? [];
                return $params[$key] ?? $default;
        }
    }
}

if (!function_exists('is_ajax_request')) {
    /**
     * Check if the current request is an AJAX request
     * 
     * @return bool
     */
    function is_ajax_request() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
