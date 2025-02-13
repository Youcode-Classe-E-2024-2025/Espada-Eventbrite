<?php

namespace App\repository;

use App\core\Database;
use PDO;

class TagRepo {
    private Database $DB;

    public function __construct() {
        $this->DB = new Database();
    }

    public function massInsert(array $tags): bool {
        $values = implode(',', array_map(fn($tag) => "('$tag')", $tags));
        $query = "INSERT INTO tags (title) VALUES $values";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute();
    }

    public function getAll(): array {
        $query = "SELECT * FROM tags";
        $stmt = $this->DB->getConnection()->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


}
