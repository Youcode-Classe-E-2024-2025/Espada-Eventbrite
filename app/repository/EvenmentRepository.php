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
        $query = "INSERT INTO evenments (title, description, visual_content, lieu, validation, archived, owner_id, category_id, date, type ,video_path)
                  VALUES (:title, :description, :visual_content, :lieu, 1, 0, :owner_id, :category_id, :date, :type , :video_path) RETURNING id";
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
                ":type" => $evenmentData['type'],
                ":video_path" => $evenmentData['video_path']

            ]

        );
        if ($res) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            // Fetch the ID
            return  $result['id'];  // Ensure it returns an integer
        }

        return false;
    }

    public function update(int $eventId, array $evenmentData)
    {
        $query = "UPDATE evenments 
              SET title = :title, 
                  description = :description, 
                  visual_content = :visual_content, 
                   video_path = :video_path
                 
              WHERE id = :eventId";

        $stmt = $this->DB->getConnection()->prepare($query);

        $res = $stmt->execute([
            ":title" => $evenmentData['title'],
            ":description" => $evenmentData['description'],
            ":visual_content" => $evenmentData['visual_content'],
            "::video_path" => $evenmentData[':video_path'],

            ":eventId" => $eventId
        ]);

        return $res && $stmt->rowCount() > 0;
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

    public function getMyEvents($id): array
    {
        $query = "SELECT *
            FROM evenments e
            JOIN booking b ON e.id = b.evenment_id
            WHERE b.user_id = :id
            ORDER BY b.booking_date DESC
            LIMIT 2;
        ";
        $stmt = $this->DB->query($query, [":id" => $id]);
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


    public function getEventsForOwner(int $owner_id): array
    {
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
    public function ticketsStaT(int $owner_id): array
    {
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

    public function getOwnerStatistics(int $owner_id): array
    {
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


    public function getClient(int $owner_id, int $even_id = 0): array
    {
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
    function getUserEvents($user_id)
    {
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
        } catch (\PDOException $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    public function getEventsales($owner, $even)
    {

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
        return $event;
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

    public function searchEvents($keyword, $page = 1, $perPage = 5)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT e.*, 
            c.total_tickets,
            c.vip_tickets_sold,
            c.standard_tickets_sold,
            c.gratuit_tickets_sold,
            cat.name as category_name,
            cat.icon as icon
            FROM evenments e
            LEFT JOIN capacity c ON e.id = c.evenment_id
            LEFT JOIN categories cat ON e.category_id = cat.id
            WHERE e.title LIKE :keyword 
            OR e.description LIKE :keyword 
            OR e.lieu LIKE :keyword
            AND e.validation = 0
            LIMIT :limit OFFSET :offset";

        $params = ['keyword' => "%$keyword%", 'limit' => $perPage, 'offset' => $offset];
        return $this->DB->query($sql, $params)->fetchAll(PDO::FETCH_OBJ);
    }

    public function getTotalSearchResults($keyword)
    {
        $sql = "SELECT COUNT(*) as total FROM evenments 
            WHERE title LIKE :keyword 
            OR description LIKE :keyword 
            OR lieu LIKE :keyword
            AND validation = 1";

        $result = $this->DB->query($sql, ['keyword' => "%$keyword%"])->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    public function totalActiveEvents()
    {
        $query = "SELECT COUNT(*) as total FROM evenments WHERE validation = 0 AND archived = 0";
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

    // public function getPaginatedEvents(int $page = 1, int $limit = 2): array
    // {
    //     $offset = ($page - 1) * $limit;
    //     $query = "SELECT e.id as event_id, e.*, u.username as owner, c.*, cat.name as category, cat.icon as icon
    //             FROM evenments e 
    //             LEFT JOIN capacity c ON e.id = c.evenment_id
    //             LEFT JOIN users u ON e.owner_id = u.id
    //             LEFT JOIN categories cat ON e.category_id = cat.id
    //             WHERE e.validation = 1 AND e.archived = 0
    //             ORDER BY e.date DESC
    //             LIMIT :limit OFFSET :offset";

    //     $stmt = $this->DB->getConnection()->prepare($query);
    //     $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    //     $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    //     $stmt->execute();

    //     return $stmt->fetchAll(PDO::FETCH_OBJ);
    // }

    public function getPaginatedEvents(int $page = 1, int $limit = 2, array $categories = []): array
    {
        $offset = ($page - 1) * $limit;

        $query = "SELECT e.id as event_id, e.*, u.username as owner, c.*, cat.name as category_name, cat.icon as icon
                FROM evenments e 
                LEFT JOIN capacity c ON e.id = c.evenment_id
                LEFT JOIN users u ON e.owner_id = u.id
                LEFT JOIN categories cat ON e.category_id = cat.id
                WHERE e.validation = 1 AND e.archived = 0";

        // Prepare parameters array
        $params = [];

        // Add category filter if categories are provided
        if (!empty($categories)) {
            // Create placeholders for categories
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $query .= " AND e.category_id IN ($placeholders)";

            // Add category values to params
            $params = array_merge($params, $categories);
        }

        $query .= " ORDER BY e.date DESC LIMIT ? OFFSET ?";

        // Add limit and offset to params
        $params[] = $limit;
        $params[] = $offset;

        // Prepare and execute the statement properly
        $stmt = $this->DB->query($query, $params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getPendingEvents()
    {
        $sql = "SELECT * FROM evenments WHERE validation = :status";
        $stmt = $this->DB->query($sql, ['status' => Event::VALIDATED]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getById($id)
    {
        $query = "SELECT e.id as event_id, e.*, u.username as owner, c.*, cat.name as category, cat.icon as icon
        FROM evenments e 
        LEFT JOIN capacity c ON e.id = c.evenment_id
        LEFT JOIN users u ON e.owner_id = u.id
        LEFT JOIN categories cat ON e.category_id = cat.id
        where e.id= :id
        ORDER BY e.date DESC
        LIMIT 4";

        $stmt = $this->DB->query($query, ["id" => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getRecentEvents($limit)
    {
        $sql = "SELECT e.id as event_id, e.*, u.username as owner, c.*, cat.name as category, cat.icon as icon
        FROM evenments e 
        LEFT JOIN capacity c ON e.id = c.evenment_id
        LEFT JOIN users u ON e.owner_id = u.id
        LEFT JOIN categories cat ON e.category_id = cat.id
        ORDER BY e.date DESC LIMIT :limit";
        $param = [
            'limit' => $limit
        ];
        $stmt = $this->DB->query($sql, $param);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function sortEvents($sortBy)
    {
        $query = "SELECT e.*, 
            c.total_tickets, c.vip_tickets_sold, c.standard_tickets_sold, 
            c.gratuit_tickets_sold, c.vip_tickets_number, c.standard_tickets_number,
            c.gratuit_tickets_number, c.vip_price, c.standard_price,
            cat.name as category, cat.icon as icon
            FROM evenments e 
            LEFT JOIN capacity c ON e.id = c.evenment_id
            LEFT JOIN categories cat ON e.category_id = cat.id
            WHERE 1=1";

        if (!empty($sortBy)) {
            switch ($sortBy) {
                case 'date_asc':
                    $query .= " ORDER BY date ASC";
                    break;
                case 'date_desc':
                    $query .= " ORDER BY date DESC";
                    break;
                case 'name_asc':
                    $query .= " ORDER BY title ASC";
                    break;
                case 'name_desc':
                    $query .= " ORDER BY title DESC";
                    break;
            }
        }

        return $this->DB->query($query)->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAdminPaginatedEvents($page = 1, $perPage = 5)
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT e.id as event_id, e.*, u.username as owner, c.*, cat.name as category, cat.icon as icon
        FROM evenments e 
        LEFT JOIN capacity c ON e.id = c.evenment_id
        LEFT JOIN users u ON e.owner_id = u.id
        LEFT JOIN categories cat ON e.category_id = cat.id
        ORDER BY e.date
        LIMIT :limit OFFSET :offset";

        $stmt = $this->DB->query($query, [
            'limit' => $perPage,
            'offset' => $offset
        ]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getTotalEvents()
    {
        $query = "SELECT COUNT(*) as total FROM evenments";
        $result = $this->DB->query($query)->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    public function getEventTicketsAndCapacity($eventId)
    {
        $sql = "SELECT 
        e.*,
        c.name as category,
        cap.vip_tickets_number,
        cap.vip_price,
        cap.vip_tickets_sold,
        cap.standard_tickets_number,
        cap.standard_price, 
        cap.standard_tickets_sold,
        cap.gratuit_tickets_number,
        cap.gratuit_tickets_sold,
        (cap.vip_tickets_number + cap.standard_tickets_number + cap.gratuit_tickets_number) as total_capacity
    FROM evenments e
    LEFT JOIN categories c ON e.category_id = c.id
    LEFT JOIN capacity cap ON e.id = cap.evenment_id
    LEFT JOIN users u ON e.owner_id = u.id
    WHERE e.id = :event_id";

        $stmt = $this->DB->query($sql, [':event_id' => $eventId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
