<?php

namespace App\repository;

use App\core\Database;
use PDO;

class TagRepository
{
    private Database $DB;

    public function __construct()
    {
        $this->DB = new Database();
    }

    public function massInsert(array $tags)
    {
        $values = implode(',', array_map(fn($tag) => "('$tag')", $tags));
        $query = "INSERT INTO tags (title) VALUES $values";
        return $this->DB->query($query);
    }

    public function getAll()
    {
        $query = "SELECT * FROM tags";
        $stmt = $this->DB->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function create($title)
    {
        try {
            $sql = "INSERT INTO tags (title) VALUES (:title)";

            var_dump('Executing query: ' . $sql);
            var_dump('With title: ' . $title);

            $stmt = $this->DB->query($sql, ['title' => $title]);
            var_dump('Query executed');

            return $stmt->rowCount() >  0;
        } catch (\PDOException $e) {
            var_dump('Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
