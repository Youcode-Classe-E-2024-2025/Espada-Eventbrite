<?php

namespace App\repository;

use App\core\Database;
use PDO;

class CapacityRepository {
    private Database $DB;

    public function __construct() {
        $this->DB = new Database();
    }

    public function create(array $capacityData): bool {
        $query = "INSERT INTO capacity (evenment_id, total_tickets, vip_tickets_number, vip_price, standard_tickets_number, standard_price, gratuit_tickets_number, early_bird_discount)
                  VALUES (:evenment_id, :total_tickets, :vip_tickets_number, :vip_price, :standard_tickets_number, :standard_price, :gratuit_tickets_number, :early_bird_discount)";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute($capacityData);
    }

    public function getOneByEventId(int $evenmentId): ?object {
        $query = "SELECT * FROM capacity WHERE evenment_id = :evenment_id";
        $stmt = $this->DB->getConnection()->prepare($query);
        $stmt->execute([':evenment_id' => $evenmentId]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }
}
