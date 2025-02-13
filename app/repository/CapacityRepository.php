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

    public function getEventStatistics($evenment_id)
    {
        $query = "SELECT 
                    c.total_tickets,
                    (COALESCE(c.vip_tickets_sold, 0) + COALESCE(c.standard_tickets_sold, 0) + COALESCE(c.gratuit_tickets_sold, 0)) AS tickets_sold,
                    (c.total_tickets - (COALESCE(c.vip_tickets_sold, 0) + COALESCE(c.standard_tickets_sold, 0) + COALESCE(c.gratuit_tickets_sold, 0))) AS tickets_available,
                    c.vip_price,
                    c.standard_price,
                    c.gratuit_tickets_number,
                    c.vip_tickets_number - COALESCE(c.vip_tickets_sold, 0) AS vip_available,
                    c.standard_tickets_number - COALESCE(c.standard_tickets_sold, 0) AS standard_available,
                    c.gratuit_tickets_number - COALESCE(c.gratuit_tickets_sold, 0) AS gratuit_available,
                    (COALESCE(c.vip_tickets_sold, 0) + COALESCE(c.standard_tickets_sold, 0) + COALESCE(c.gratuit_tickets_sold, 0)) AS tickets_sold,
                    (c.total_tickets - (COALESCE(c.vip_tickets_sold, 0) + COALESCE(c.standard_tickets_sold, 0) + COALESCE(c.gratuit_tickets_sold, 0))) AS available_tickets
                  FROM capacity c
                  WHERE c.evenment_id = :evenment_id";
    
        $stmt = $this->DB->query($query, ["evenment_id" => $evenment_id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
