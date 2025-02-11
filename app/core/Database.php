<?php

namespace App\core;

use PDO;
use PDOException;

class Database
{
    private PDO $connection;

    public function __construct()
    {
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

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Executes a prepared SQL query
     * 
     * @param string $sql The SQL query to execute
     * @param array $params Parameters to bind to the query
     * @return \PDOStatement The statement after execution
     * @throws \Exception If query execution fails
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Fetches a single record from the database
     * 
     * @param string $sql The SQL query to execute
     * @param array $params Parameters to bind to the query
     * @return array|false Single record as associative array or false if not found
     */
    public function find($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Fetches all matching records from the database
     * 
     * @param string $sql The SQL query to execute
     * @param array $params Parameters to bind to the query
     * @return array Array of records as associative arrays
     */
    public function findAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }
}
