<?php

namespace App\repository;

use App\core\Database;
use PDO;

class CategoryRepository {
    private Database $DB;

    public function __construct() {
        $this->DB = new Database();
    }

    public function massInsert(array $categories): bool {
        $values = implode(',', array_map(fn($category) => "('$category')", $categories));
        $query = "INSERT INTO categories (name) VALUES $values";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute();
    }

    public function getAll(): array {
        $query = "SELECT * FROM categories";
        $stmt = $this->DB->getConnection()->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
