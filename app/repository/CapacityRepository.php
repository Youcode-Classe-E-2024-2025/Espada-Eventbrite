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

    //update the sold whene the user get teckit
    public function updateSold($event_id,$new_vip_tickets,$new_standard_tickets,$new_gratuit_tickets){
        $query = "UPDATE capacity
        SET 
            vip_tickets_sold = vip_tickets_sold + :new_vip_tickets,
            standard_tickets_sold = standard_tickets_sold + :new_standard_tickets,
            gratuit_tickets_sold = gratuit_tickets_sold + :new_gratuit_tickets
        WHERE evenment_id = :event_id;";

    return $stmt = $this->DB->query($query, [":new_vip_tickets" => $new_vip_tickets,
                                            ":new_standard_tickets" => $new_standard_tickets,
                                            ":new_gratuit_tickets" => $new_gratuit_tickets,
                                            ":event_id" => $event_id
                                            ]);
    }


    public function getAvailable($event_id){
        $query = "SELECT
    (vip_tickets_number - vip_tickets_sold) AS vip_tickets_available,
    (standard_tickets_number - standard_tickets_sold) AS standard_tickets_available,
    (gratuit_tickets_number - gratuit_tickets_sold) AS gratuit_tickets_available
FROM
    capacity
WHERE
    evenment_id = :event_id;";

     $stmt = $this->DB->query($query, [
                                            ":event_id" => $event_id
     ]);
     return $stmt->fetch(PDO::FETCH_OBJ);
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
