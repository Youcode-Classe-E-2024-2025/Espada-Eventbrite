<?php

namespace App\core;

abstract class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Retrieves all records from the model's table
     * 
     * @return array Array of all records
     */
    public function findAll()
    {
        return $this->db->findAll("SELECT * FROM {$this->table}");
    }


    /**
     * Retrieves a single record by its ID
     * 
     * @param int $id Record identifier
     * @return array|false Record data or false if not found
     */
    public function findById($id)
    {
        return $this->db->find("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    /**
     * Creates a new record in the database
     * 
     * @param array $data Associative array of column names and values
     * @return \PDOStatement Result of the insert operation
     */
    public function create(array $data)
    {
        $columns = implode(',', array_keys($data));
        $values = implode(',', array_fill(0, count($data), '?'));

        return $this->db->query(
            "INSERT INTO {$this->table} ($columns) VALUES ($values)",
            array_values($data)
        );
    }
}
