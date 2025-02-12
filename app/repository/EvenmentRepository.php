<?php

namespace App\repository;

use App\core\Database;
use PDO;

class EvenmentRepository {
    private Database $DB;

    public function __construct() {
        $this->DB = new Database();
    }

    public function create(array $evenmentData) {
        $query = "INSERT INTO evenments (title, description, visual_content, lieu, validation, archived, owner_id, category_id, date, type)
                  VALUES (:title, :description, :visual_content, :lieu, 1, 0, :owner_id, :category_id, :date, :type) RETURNING id";
        $stmt = $this->DB->getConnection()->prepare($query);
        $res = $stmt->execute(

            [
                ":title"=>$evenmentData['title'], 
                ":description"=>$evenmentData['description'], 
                ":visual_content"=>$evenmentData['visual_content'], 
                ":lieu"=>$evenmentData['lieu'], 
                ":owner_id"=>$evenmentData['owner_id'], 
                ":category_id"=>$evenmentData['category_id'],
                ":date"=>$evenmentData['date'], 
                ":type"=>$evenmentData['type']

            ]

        );
        if ($res) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC); 
            var_dump($result);
            // Fetch the ID
            return  $result['id'] ;  // Ensure it returns an integer
        }
    
        return false;
    }
    

    public function validate(int $evenmentId): bool {
        $query = "UPDATE evenments SET validation = 1 WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute([':id' => $evenmentId]);
    }

    public function unValidate(int $evenmentId): bool {
        $query = "UPDATE evenments SET validation = 0 WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute([':id' => $evenmentId]);
    }



    public function archive(int $evenmentId): bool {
        $query = "UPDATE evenments SET archived = 1 WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute([':id' => $evenmentId]);
    }

    public function unArchive(int $evenmentId): bool {
        $query = "UPDATE evenments SET archived = 0 WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute([':id' => $evenmentId]);
    }

    public function changeType(int $evenmentId, string $type): bool {
        $query = "UPDATE evenments SET type = :type WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute([':type' => $type, ':id' => $evenmentId]);
    }

    public function getAll(): array {
        $query = "SELECT * FROM evenments";
        $stmt = $this->DB->getConnection()->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    public function getOrganiserEvent($id): array {
        $query = 

       " SELECT                      
    e.id AS event_id,
    e.title,
    e.description,
    e.visual_content,
    e.lieu,
    e.validation,
    e.archived,
    e.owner_id,
    e.category_id,
    c.name AS category_name,
    e.date,
    e.type,
    cap.total_tickets,
    cap.vip_tickets_number,
    cap.vip_price,
    cap.standard_tickets_number,
    cap.standard_price,
    cap.gratuit_tickets_number,
    cap.early_bird_discount,
    Array_agg(DISTINCT t.title) AS tags
FROM evenments e
LEFT JOIN categories c ON e.category_id = c.id
LEFT JOIN capacity cap ON e.id = cap.evenment_id
LEFT JOIN envenment_tag et ON e.id = et.envenment_id
LEFT JOIN tags t ON et.tag_id = t.id
WHERE e.owner_id = 1
GROUP BY 
    e.id, c.name, 
    cap.total_tickets, cap.vip_tickets_number, cap.vip_price,
    cap.standard_tickets_number, cap.standard_price, cap.gratuit_tickets_number, cap.early_bird_discount;


        ";
        $stmt = $this->DB->getConnection()->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

   
}



// SELECT 
//     b.*, 
//     u.username 
// FROM booking b
// INNER JOIN users u ON b.user_id = u.id
// WHERE b.envenment_id = $id;
