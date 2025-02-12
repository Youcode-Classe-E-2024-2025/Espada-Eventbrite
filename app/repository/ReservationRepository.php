<?php

namespace App\repository;

use App\core\Database;
use PDO;
use App\models\User;

class ReservationRepository
{
    private Database $DB;

    public function __construct()
    {
        $this->DB = new Database();
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
