<?php



namespace App\repository;

use App\core\Database;
use App\models\Event;
use PDO;

class ReservationRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Create a new booking
    public function createBooking($userId, $eventId, $type, $totalPrice, $booking_date, $qrPath)
    {
        $query = "INSERT INTO booking (user_id, evenment_id, type, price, booking_date, qr_code_path) VALUES (:user_id, :event_id, :type, :price, :booking_date, :qr_code_path)";

        $params = [
            ':user_id' => $userId,
            ':event_id' => $eventId,
            ':type' => $type,
            ':price' => $totalPrice,
            ':booking_date' => $booking_date,
            ':qr_code_path' => $qrPath
        ];

        $stmt = $this->db->query($query, $params);
        return $stmt->rowCount() > 0;
    }

    // Read a booking by ID
    public function getBookingById($booking_id)
    {
        $query = "SELECT * FROM booking WHERE id = :booking_id";
        $stmt = $this->db->query($query, [':booking_id' => $booking_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Read all bookings for a user
    public function getUserBookings($user_id)
    {
        $query = "SELECT * FROM bookings WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update a booking
    public function updateBooking($booking_id)
    {
        $query = "UPDATE bookings SET user_id = :user_id, event_id = :event_id, type = :type, price = :price, booking_date = :booking_date, status = :status WHERE id = :booking_id";
        $stmt = $this->db->prepare($query);
        $params = [
            ':user_id' => $this->user_id,
            ':event_id' => $this->evenment_id,
            ':type' => $this->type,
            ':price' => $this->price,
            ':booking_date' => $this->booking_date,
            ':status' => $this->status,
            ':booking_id' => $booking_id
        ];
        return $stmt->execute($params);
    }

    // Delete a booking
    public function deleteBooking($booking_id)
    {
        $query = "DELETE FROM bookings WHERE id = :booking_id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':booking_id' => $booking_id]);
    }

    public function insertBooking($userId, $eventId, $type, $totalPrice)
    {
        $query = "INSERT INTO booking ( user_id, evenment_id, type, price, booking_date)
        VALUES (:user_id, :evenment_id, :type, :price, :booking_date);";

        $stmt = $this->db->prepare($query);
        $params = [
            ':user_id' => $this->user_id,
            ':evenment_id' => $this->evenment_id,
            ':type' => $this->type,
            ':price' => $this->price,
            ':booking_date' => $this->booking_date
        ];
        return $stmt->execute($params);
    }

    public function getUserTickets($userId, $limit = null)
    {
        $query = "SELECT b.*, e.title as event_title, e.date as event_date, e.lieu as event_location,
                e.type as ticket_type, b.price as ticket_price, b.booking_date, b.qr_code_path
                FROM booking b
                JOIN evenments e ON b.evenment_id = e.id
                WHERE b.user_id = :user_id
                ORDER BY b.booking_date DESC";

        $params = [':user_id' => $userId];

        if ($limit !== null) {
            $query .= " LIMIT :limit";
            $params[':limit'] = $limit;
        }


        $stmt = $this->db->query($query, $params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
