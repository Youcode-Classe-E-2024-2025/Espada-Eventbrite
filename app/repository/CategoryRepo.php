<?php

namespace App\repository;

use App\core\Database;
use PDO;

class CategoryRepo
{
    private Database $DB;

    public function __construct()
    {
        $this->DB = new Database();
    }

    public function massInsert(array $categories)
    {
        $values = implode(',', array_map(fn($category) => "('$category')", $categories));
        $query = "INSERT INTO categories (name) VALUES $values";
        return $this->DB->query($query);
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM categories";
        $stmt = $this->DB->getConnection()->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
