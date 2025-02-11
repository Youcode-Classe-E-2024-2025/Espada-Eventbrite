<?php

namespace app\repository;

use app\core\Database;

abstract class BaseRepository
{
    protected Database $db;
    protected string $table;

    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * Find all records in the table
     * 
     * @return array
     */
    public function findAll()
    {
        return $this->db->findAll("SELECT * FROM {$this->table}");
    }

    /**
     * Find one record by ID
     * 
     * @param int $id
     * @return array|false
     */
    public function findOne(int $id)
    {
        return $this->db->find("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

    /**
     * Find records by specific criteria
     * 
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria)
    {
        $whereClauses = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            $whereClauses[] = "$key = :$key";
            $params[$key] = $value;
        }

        $whereSQL = implode(' AND ', $whereClauses);
        return $this->db->findAll("SELECT * FROM {$this->table} WHERE $whereSQL", $params);
    }

    /**
     * Insert a new record
     * 
     * @param array $data
     * @return bool
     */
    public function insert(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        return $this->db->query($sql, $data)->rowCount() > 0;
    }

    /**
     * Update a record
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data)
    {
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = :$key";
        }

        $setSQL = implode(', ', $setClauses);
        $data['id'] = $id;

        $sql = "UPDATE {$this->table} SET $setSQL WHERE id = :id";
        return $this->db->query($sql, $data)->rowCount() > 0;
    }

    /**
     * Delete a record
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        return $this->db->query(
            "DELETE FROM {$this->table} WHERE id = :id",
            ['id' => $id]
        )->rowCount() > 0;
    }
}
