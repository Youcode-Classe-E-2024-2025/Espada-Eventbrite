<?php

namespace app\core;

use PDO;
use PDOException;

class Database {
    private PDO $connection;

    public function __construct() {
        $config = require __DIR__ . '/../config/config.php';
        if (empty($config['db'])) {
            throw new \Exception('Database configuration is missing');
        }

        $dsn = "pgsql:host={$config['db']['host']};dbname={$config['db']['name']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $config['db']['user'], $config['db']['password'], $options);
            
            // Set the search path to public schema
            $this->connection->exec('SET search_path TO public');
            
            // Verify connection
            $this->connection->query('SELECT 1');
        } catch (PDOException $e) {
            throw new \Exception("Database connection error: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}
