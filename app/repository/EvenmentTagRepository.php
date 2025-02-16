<?php

namespace App\repository;

use App\core\Database;
use PDO;

class EvenmentTagRepository
{
    private Database $DB;

    public function __construct()
    {
        $this->DB = new Database();
    }

    // Create a single entry in the evenment_tag table
    public function create(int $tagId, int $evenmentId): bool
    {
        $query = "INSERT INTO evenment_tag (tag_id, evenment_id) VALUES (:tag_id, :evenment_id)";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute([
            ':tag_id' => $tagId,
            ':evenment_id' => $evenmentId,
        ]);
    }

    // Get all tags associated with a specific evenment
    public function getByEvenmentId(int $evenmentId): array
    {
        $query = "SELECT t.* 
                  FROM tags t
                  INNER JOIN evenment_tag et ON t.id = et.tag_id
                  WHERE et.evenment_id = :evenment_id";
        $stmt = $this->DB->getConnection()->prepare($query);
        $stmt->execute([':evenment_id' => $evenmentId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Mass insert tags for a specific evenment
    public function massInsert(array $tags, int $evenmentId): bool
    {
        $values = implode(',', array_map(fn($tagId) => "($tagId, $evenmentId)", $tags));
        $query = "INSERT INTO envenment_tag (tag_id, envenment_id) VALUES $values;";
        echo $query;
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute();
    }
    public function getTagById($id): array
    {
        $query = "SELECT t.id, t.title
                  FROM tags t
                  INNER JOIN envenment_tag et ON t.id = et.tag_id
                  WHERE et.envenment_id = :id";

        $stmt = $this->DB->getConnection()->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
