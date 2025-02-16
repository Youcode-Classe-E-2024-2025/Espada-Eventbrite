<?php

namespace App\repository;

use App\core\Database;
use PDO;

class NotificationRepository
{
    private Database $DB;

    public function __construct()
    {
        $this->DB = new Database();
    }

    /**
     * Create a new notification
     */
    public function create(array $notificationData)
    {
        // var_dump($notificationData);
        
        $query = "INSERT INTO notification (from_id, to_id, action, message , checked) 
                  VALUES (:from_id, :to_id, :action, :message , 0) RETURNING id";
                  
        $stmt = $this->DB->getConnection()->prepare($query);
        $res = $stmt->execute([
            ":from_id" => $notificationData['from_id'],
            ":to_id" => $notificationData['to_id'],
            ":action" => $notificationData['action'],
            ":message" => $notificationData['message']
        ]);


        // if ($res) {
        //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //     return $result['id'];  // Return notification IDdie();
        // }

        // return false;
    }

    /**
     * Get notifications for a specific user
     */
    public function getNotificationsForUser(int $userId)
    {
        $query = "SELECT n.*, u.username AS from_username
                  FROM notification n
                  JOIN users u ON n.from_id = u.id
                  WHERE n.to_id = :userId
                  ORDER BY n.id DESC";

        $stmt = $this->DB->getConnection()->prepare($query);
        $stmt->execute([":userId" => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark notification as read (optional)
     */
    public function markAsRead(int $notificationId)
    {
        $query = "UPDATE notification SET status = 'read' WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        $stmt->execute([":id" => $notificationId]);

        return $stmt->rowCount() > 0;
    }
}
