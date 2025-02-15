<?php

namespace App\repository;

use App\core\Database;
use PDO;

class BookingRepository {
    private Database $DB;

    public function __construct() {
        $this->DB = new Database();
    }

    // Create a new booking
    public function create(array $bookingData): bool {
        $query = "INSERT INTO booking (user_id, evenment_id, type, price, booking_date) 
                  VALUES (:user_id, :evenment_id, :type, :price, :booking_date)";
        $stmt = $this->DB->getConnection()->prepare($query);

        // Execute the query with provided data
        return $stmt->execute([
            ':user_id' => $bookingData['user_id'],
            ':evenment_id' => $bookingData['evenment_id'],
            ':type' => $bookingData['type'],
            ':price' => $bookingData['price'],
            ':booking_date' => $bookingData['booking_date']
        ]);
    }

    // Get bookings by evenment_id with user username
    public function getBookingsByEventId(int $id): array {
        $query = "SELECT 
                    b.*, 
                    u.username 
                  FROM booking b
                  INNER JOIN users u ON b.user_id = u.id
                  WHERE b.evenment_id = :id";
        
        $stmt = $this->DB->getConnection()->prepare($query);
        $stmt->execute([':id' => $id]);

        // Fetch the result as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    

    
}
