<?php

namespace App\services;

use App\core\Database;
use PDO;

class NotifData {
    private Database $DB;

    public function __construct() {
        $this->DB = new Database();
    }


    public function getEventBookingsUsers(int $id): array {
        $query = "SELECT 
    e.id AS event_id, 
    e.title AS event_name, 
    COALESCE(array_agg(b.user_id) FILTER (WHERE b.user_id IS NOT NULL), '{}'::integer[]) AS user_ids, 
    COALESCE(array_agg(u.username) FILTER (WHERE u.username IS NOT NULL), '{}'::text[]) AS usernames
FROM evenments e
LEFT JOIN booking b ON e.id = b.evenment_id
LEFT JOIN users u ON b.user_id = u.id
WHERE e.id = :id
GROUP BY e.id, e.title
";

        $stmt = $this->DB->getConnection()->prepare($query);
        $stmt->execute([':id' => $id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}



?>