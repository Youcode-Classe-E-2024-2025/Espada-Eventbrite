<?php

namespace App\repository;

use App\core\Database;
use App\models\Event;
use PDO;

class EvenmentRepository
{
    private Database $DB;

    public function __construct()
    {
        $this->DB = new Database();
    }

    public function create(array $evenmentData)
    {
        $query = "INSERT INTO evenments (title, description, visual_content, lieu, validation, archived, owner_id, category_id, date, type)
                  VALUES (:title, :description, :visual_content, :lieu, 1, 0, :owner_id, :category_id, :date, :type) RETURNING id";
        $stmt = $this->DB->getConnection()->prepare($query);
        $res = $stmt->execute(

            [
                ":title" => $evenmentData['title'],
                ":description" => $evenmentData['description'],
                ":visual_content" => $evenmentData['visual_content'],
                ":lieu" => $evenmentData['lieu'],
                ":owner_id" => $evenmentData['owner_id'],
                ":category_id" => $evenmentData['category_id'],
                ":date" => $evenmentData['date'],
                ":type" => $evenmentData['type']

            ]

        );
        if ($res) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            var_dump($result);
            // Fetch the ID
            return  $result['id'];  // Ensure it returns an integer
        }

        return false;
    }


    public function validate(int $evenmentId)
    {
        $query = "UPDATE evenments SET validation = 1 WHERE id = :id";
        return $this->DB->query($query, [':id' => $evenmentId]);
    }

    public function unValidate(int $evenmentId)
    {
        $query = "UPDATE evenments SET validation = 0 WHERE id = :id";
        return $this->DB->query($query, [':id' => $evenmentId]);
    }

    public function archive(int $evenmentId)
    {
        $query = "UPDATE evenments SET archived = 1 WHERE id = :id";
        return $this->DB->query($query, [':id' => $evenmentId]);
    }

    public function unArchive(int $evenmentId)
    {
        $query = "UPDATE evenments SET archived = 0 WHERE id = :id";
        return $this->DB->query($query, [':id' => $evenmentId]);
    }

    public function changeType(int $evenmentId, string $type): bool
    {
        $query = "UPDATE evenments SET type = :type WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute([':type' => $type, ':id' => $evenmentId]);
    }
    public function getAll()
    {
        $query = "SELECT e.id as event_id, e.*, u.username as owner, c.*, cat.name as category, cat.icon as icon
        FROM evenments e 
        LEFT JOIN capacity c ON e.id = c.evenment_id
        LEFT JOIN users u ON e.owner_id = u.id
        LEFT JOIN categories cat ON e.category_id = cat.id
        ORDER BY e.date DESC
        ";
        $stmt = $this->DB->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function delete(int $eventId)
    {
        $query = "DELETE FROM evenments WHERE id = :id";
        return $this->DB->query($query, [':id' => $eventId]);
    }

    public function searchEvents($keyword)
    {
        $sql = "SELECT e.*, 
            c.total_tickets,
            c.vip_tickets_sold,
            c.standard_tickets_sold,
            c.gratuit_tickets_sold,
            cat.name as category_name
            FROM evenments e
            LEFT JOIN capacity c ON e.id = c.evenment_id
            LEFT JOIN categories cat ON e.category_id = cat.id
            WHERE e.title LIKE :keyword 
            OR e.description LIKE :keyword 
            OR e.lieu LIKE :keyword";

        $params = ['keyword' => "%$keyword%"];
        return $this->DB->query($sql, $params)->fetchAll(PDO::FETCH_OBJ);
    }

    public function totalActiveEvents()
    {
        $query = "SELECT COUNT(*) as total FROM evenments WHERE validation = 1 AND archived = 0";
        $stmt = $this->DB->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function totalTicketsSold()
    {
        $query = "SELECT SUM(vip_tickets_sold + standard_tickets_sold + gratuit_tickets_sold) as total FROM capacity ";
        $stmt = $this->DB->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function totalRevenue()
    {
        $query = "SELECT SUM(vip_tickets_sold * vip_price) + SUM(standard_tickets_sold * standard_price) as totalRevenue FROM capacity ";
        $stmt = $this->DB->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['totalRevenue'] ?? 0.0;
    }

    public function getPendingEvents()
    {
        $sql = "SELECT * FROM evenments WHERE validation = :status";
        $stmt = $this->DB->query($sql, ['status' => Event::VALIDATED]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
