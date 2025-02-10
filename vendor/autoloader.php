<?php

class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            // Convert namespace separators to directory separators
            $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

            error_log("Autoloader: Trying to load {$class} from {$file}");

            // Check if the file exists
            if (file_exists($file)) {
                error_log("Autoloader: File found at {$file}");
                require_once $file;
                return true;
            }

            // Log an error if the class file is not found
            error_log("Autoloader: Class file not found for {$class} at {$file}");
            return false;
        });
    }
}

// Register the autoloader
Autoloader::register();

?>