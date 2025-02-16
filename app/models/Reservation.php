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
}