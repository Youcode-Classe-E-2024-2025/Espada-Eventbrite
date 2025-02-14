<?php

namespace App\repository;

use App\core\Database;
use PDO;

class CategoryRepository
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

    public function create($title, $icon)
    {
        $query = "INSERT INTO categories (name, icon) VALUES (:title, :icon)";
        $stmt = $this->DB->query($query, [':title' => $title, ':icon' => $icon]);
        return $stmt->rowCount() > 0;
    }

    public function delete($id)
    {
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->DB->query($query, [':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
