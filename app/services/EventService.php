<?php
namespace App\services;

use App\repository\EvenmentRepository;
use App\repository\CapacityRepository;
use App\repository\EvenmentTagRepository;




class EventService {

    private EvenmentRepository $evenmentRepo;
    private CapacityRepository $capacityRepo;
    private EvenmentTagRepository $evenmentTagRepo;

    public function __construct() {
        $this->evenmentRepo = new EvenmentRepository();
        $this->capacityRepo = new CapacityRepository();
        $this->evenmentTagRepo = new EvenmentTagRepository();
    }

    public function createEvent(array $evenmentData, array $capacityData, array $tagIds): bool {
        try {
            // Step 1: Create the event and get its ID
            $res=$this->evenmentRepo->create($evenmentData);
            if (!$res) {
                return false; // Event creation failed
            }
            
            // Get the event ID (assuming it's returned by the `create` method)
            // $eventId = $this->evenmentRepo->getLastInsertedId();

            // Step 2: Insert the capacity data for the created event
            $capacityData['evenment_id'] = $res;
            if (!$this->capacityRepo->create($capacityData)) {
                return false; // Capacity creation failed
            }
            // var_dump($res);
            if (!$this->evenmentTagRepo->massInsert($tagIds, $res)) {
                return false; 
            }

            return true; // Everything succeeded
        } catch (\Exception $e) {
            // Log the error if necessary
            error_log($e->getMessage());
            return false;
        }
    }
}
