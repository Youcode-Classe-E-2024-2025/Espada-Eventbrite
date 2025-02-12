<?php

namespace App\services;

use App\repository\EvenmentRepository;
use App\repository\CapacityRepository;
use App\repository\EvenmentTagRepository;
use App\repository\EventRepository;
use App\models\Event;
use App\core\Database;



class EventService {

    private EvenmentRepository $evenmentRepo;
    private CapacityRepository $capacityRepo;
    private EvenmentTagRepository $evenmentTagRepo;
    private EventRepository $eventRepository;
    private Event $event;

    public function __construct() {
        $this->evenmentRepo = new EvenmentRepository();
        $this->capacityRepo = new CapacityRepository();
        $this->evenmentTagRepo = new EvenmentTagRepository();
        $this->event = new Event();
        $this->eventRepository = new EventRepository(new Database(), $this->event);
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

    public function searchEvents(string $keyword, int $page, int $limit): array
    {
        if (!$this->event->validateSearchKeyword($keyword)) {
            return [
                'events' => [],
                'total' => 0,
                'pages' => 0
            ];
        }

        $events = $this->eventRepository->searchEvents($keyword, $page, $limit);
        $totalCount = $this->eventRepository->searchCount($keyword);

        return [
            'events' => $events,
            'total' => $totalCount,
            'pages' => ceil($totalCount / $limit)
        ];
    }
}
