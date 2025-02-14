<?php

namespace App\repository;

use App\core\Database;
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
    public function getAll(): array
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


//     public function getOrganiserEvent($id): array {
//         $query = 
//        " SELECT                      
//     e.id AS event_id,
//     e.title,
//     e.description,
//     e.visual_content,
//     e.lieu,
//     e.validation,
//     e.archived,
//     e.owner_id,
//     e.category_id,
//     c.name AS category_name,
//     e.date,
//     e.type,
//     cap.total_tickets,
//     cap.vip_tickets_number,
//     cap.vip_price,
//     cap.standard_tickets_number,
//     cap.standard_price,
//     cap.gratuit_tickets_number,
//     cap.early_bird_discount,
//     Array_agg(DISTINCT t.title) AS tags
// FROM evenments e
// LEFT JOIN categories c ON e.category_id = c.id
// LEFT JOIN capacity cap ON e.id = cap.evenment_id
// LEFT JOIN envenment_tag et ON e.id = et.envenment_id
// LEFT JOIN tags t ON et.tag_id = t.id
// WHERE e.owner_id = 1
// GROUP BY 
//     e.id, c.name, 
//     cap.total_tickets, cap.vip_tickets_number, cap.vip_price,
//     cap.standard_tickets_number, cap.standard_price, cap.gratuit_tickets_number, cap.early_bird_discount;
//         ";
//         $stmt = $this->DB->getConnection()->query($query);
//         return $stmt->fetchAll(PDO::FETCH_OBJ);
//     }


    public function getEventsForOwner(int $owner_id): array {
        $sql = "
            SELECT 
                e.id,
                e.title AS event_name,
                e.date AS event_date,
                e.lieu as lieu ,
                e.validation AS status,
                (SELECT SUM(b.price) 
                 FROM booking b 
                 WHERE b.evenment_id = e.id) AS sales
            FROM evenments e
            WHERE e.owner_id = :owner_id AND e.validation = 1 AND e.archived = 0;
        ";

        $stmt = $this->DB->getConnection()->prepare($sql);
        $stmt->bindParam(':owner_id', $owner_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // public function ticketsStaT


    public function ticketsStaT(int $owner_id): array {
        $sql = "
            SELECT                  
    -- Total Tickets: Sum of total tickets for all events owned by the user
    COALESCE(SUM(c.total_tickets), 0) AS Total_Tickets, 

    -- Available Tickets: Total tickets minus all sold tickets
    COALESCE(SUM(c.total_tickets - (c.vip_tickets_sold + c.standard_tickets_sold + c.gratuit_tickets_sold)), 0) AS Available_Tickets,

    -- Sold Tickets: Count of sold tickets (can still rely on 'capacity' if needed)
    COALESCE(SUM(c.vip_tickets_sold + c.standard_tickets_sold + c.gratuit_tickets_sold), 0) AS Sold_Tickets,

    -- Refunds: Count of refunded (canceled) tickets from the 'booking' table
    COALESCE(COUNT(b.id) FILTER (WHERE b.canceled = 1), 0) AS Refund_Tickets

FROM evenments e
LEFT JOIN capacity c ON e.id = c.evenment_id
LEFT JOIN booking b ON e.id = b.evenment_id

WHERE e.owner_id = :owner_id;

        ";

        $stmt = $this->DB->getConnection()->prepare($sql);
        $stmt->bindParam(':owner_id', $owner_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    



    public function getOwnerStatistics(int $owner_id): array {
        $sql = "
            SELECT 
                (SELECT COUNT(*) FROM evenments WHERE owner_id = :owner_id) AS total_events,
                (SELECT COUNT(*) FROM evenments WHERE owner_id = :owner_id AND validation = 1 AND archived = 0) AS active_events,
                (SELECT SUM(b.price) 
                 FROM booking b 
                 INNER JOIN evenments e ON b.evenment_id = e.id 
                 WHERE e.owner_id = :owner_id AND e.validation = 1 AND e.archived = 0) AS total_sales,
                (SELECT COUNT(*) 
                 FROM booking b 
                 INNER JOIN evenments e ON b.evenment_id = e.id 
                 WHERE e.owner_id = :owner_id AND e.validation = 1 AND e.archived = 0) AS total_attendees;
        ";

        $stmt = $this->DB->getConnection()->prepare($sql);
        $stmt->bindParam(':owner_id', $owner_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }





    // public function getClient(int $owner_id ,int $even_id): array {
    //     $sql = "SELECT 
    //                 u.username AS user_name,
    //                 u.email AS user_email,
    //                 u.avatar AS user_avatar,
    //                 e.id AS event_id,
    //                 e.title AS event_title,
    //                 b.type AS booking_type,
    //                 b.price AS booking_price
    //             FROM booking b
    //             JOIN evenments e ON b.evenment_id = e.id
    //             JOIN users u ON b.user_id = u.id
    //             WHERE e.owner_id = :owner_id";
    
    //     $stmt = $this->DB->getConnection()->prepare($sql);
    //     $stmt->bindParam(':owner_id', $owner_id, PDO::PARAM_INT);
    //     $stmt->execute();
    
    //     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    //     return ['p' => $result]; // Return an array with 'p' as the key (or change to a different name)
    // }

    public function getClient(int $owner_id, int $even_id = 0): array {
        // Start with the base SQL query
        $sql = "SELECT 
                    u.username AS user_name,
                    u.email AS user_email,
                    u.avatar AS user_avatar,
                    e.id AS event_id,
                    e.title AS event_title,
                    b.type AS booking_type,
                    b.price AS booking_price
                FROM booking b
                JOIN evenments e ON b.evenment_id = e.id
                JOIN users u ON b.user_id = u.id
                WHERE e.owner_id = :owner_id";
        
        // Modify query if even_id > 0
        if ($even_id > 0) {
            $sql .= " AND e.id = :even_id";
        }
    
        // Prepare the statement
        $stmt = $this->DB->getConnection()->prepare($sql);
        $stmt->bindParam(':owner_id', $owner_id, PDO::PARAM_INT);
        
        if ($even_id > 0) {
            $stmt->bindParam(':even_id', $even_id, PDO::PARAM_INT);
        }
    
        // Execute the query
        $stmt->execute();
    
        // Fetch all results
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $result;  // Return the fetched results
    }
    
    
    
    
    // Function to get all events for a user
    function getUserEvents($user_id) {
        // SQL query to fetch events for the user
        $sql = "SELECT 
                    e.id AS event_id,
                    e.title AS event_title
                    
                FROM 
                    evenments e
                
                WHERE 
                    e.owner_id = :user_id"; // Bind the user ID dynamically
    
        try {
            // Prepare and execute the query
            $stmt = $this->DB->getConnection()->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
    
            // Fetch all events
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Return the events
            return $events;
        } catch (PDOException $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
    
    
    
    
    

    public function getEventsales($owner , $even){

        $sql = "
                SELECT 
                    u.username AS user_name,
                    u.email AS user_email,
                    u.avatar AS user_avatar,
                    e.id AS event_id,
                    e.title AS event_title,
                    b.type AS booking_type,
                    b.price AS booking_price
                FROM booking b
                JOIN evenments e ON b.evenment_id = e.id
                JOIN users u ON b.user_id = u.id
                WHERE e.owner_id = :user_id AND e.id = :eve_id;

            ";
    
            $stmt = $this->DB->getConnection()->prepare($sql);
            $stmt->bindParam(':user_id', $owner, PDO::PARAM_INT);
            $stmt->bindParam(':eve_id', $even, PDO::PARAM_INT);
            $stmt->execute();
    
            $event = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $event ;

    }

   





    public function delete(int $eventId)
    {
        
            $conn = $this->DB->getConnection();
    
            // Delete from envenment_tag
            $sql1 = "DELETE FROM envenment_tag WHERE envenment_id = :eventId;";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bindParam(':eventId', $eventId, PDO::PARAM_INT);
            $stmt1->execute();
    
            // Delete from capacity
            $sql2 = "DELETE FROM capacity WHERE evenment_id = :eventId;";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bindParam(':eventId', $eventId, PDO::PARAM_INT);
            $stmt2->execute();
    
            // Delete from evenments
            $sql3 = "DELETE FROM evenments WHERE id = :eventId;";
            $stmt3 = $conn->prepare($sql3);
            $stmt3->bindParam(':eventId', $eventId, PDO::PARAM_INT);
            $stmt3->execute();
    
            return $stmt3->rowCount() > 0;
    
        
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
}
