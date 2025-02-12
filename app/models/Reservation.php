<?php
namespace App\models;

use app\core\Database;
use PDO;

class Reservation {
    private $db;
    public $id;
    public $user_id;
    public $evenment_id;
    public $type;
    public $price;
    public $booking_date;
    public $status;

    public function __construct(Database $database, $user_id = null, $evenment_id = null, $type = null, $price = null, $booking_date = null, $status = 'confirmed') {
        $this->db = $database->getConnection();
        $this->user_id = $user_id;
        $this->evenment_id = $evenment_id;
        $this->type = $type;
        $this->price = $price;
        $this->booking_date = $booking_date ?? date('Y-m-d H:i:s');
        $this->status = $status;
    }

    public function __get($name) {
        return isset($this->$name) ? $this->$name : null;
    }
    
    public function __set($name, $value) {
        $this->$name = $value;
    }

    // Create a new booking
    public function createBooking() {
        $query = "INSERT INTO bookings (user_id, event_id, type, price, booking_date, status) VALUES (:user_id, :event_id, :type, :price, :booking_date, :status)";
        $stmt = $this->db->prepare($query);
        $params = [
            ':user_id' => $this->user_id,
            ':event_id' => $this->evenment_id,
            ':type' => $this->type,
            ':price' => $this->price,
            ':booking_date' => $this->booking_date,
            ':status' => $this->status
        ];
        return $stmt->execute($params);
    }

    // Read a booking by ID
    public function getBookingById($booking_id) {
        $query = "SELECT * FROM bookings WHERE id = :booking_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':booking_id' => $booking_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Read all bookings for a user
    public function getUserBookings($user_id) {
        $query = "SELECT * FROM bookings WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update a booking
    public function updateBooking($booking_id) {
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
    public function deleteBooking($booking_id) {
        $query = "DELETE FROM bookings WHERE id = :booking_id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':booking_id' => $booking_id]);
    }
}