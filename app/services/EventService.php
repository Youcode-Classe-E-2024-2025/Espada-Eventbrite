<?php

namespace App\services;

use App\repository\EvenmentRepository;
use App\repository\CapacityRepository;
use App\repository\EvenmentTagRepository;
use App\repository\EventRepository;
use App\repository\ReservationRepository;
use App\models\Event;
use App\core\Database;



class EventService
{

    private EvenmentRepository $evenmentRepo;
    private CapacityRepository $capacityRepo;
    private EvenmentTagRepository $evenmentTagRepo;
    private EventRepository $eventRepository;
    private ReservationRepository $ReservationRepository;

    private Event $event;

    public function __construct()
    {
        $this->evenmentRepo = new EvenmentRepository();
        $this->capacityRepo = new CapacityRepository();
        $this->evenmentTagRepo = new EvenmentTagRepository();
        $this->event = new Event();
        $this->ReservationRepository = new ReservationRepository();
        $this->eventRepository = new EventRepository(new Database(), $this->event);
    }

    public function createEvent(array $evenmentData, array $capacityData, array $tagIds): bool
    {
        try {
            // Step 1: Create the event and get its ID
            $res = $this->evenmentRepo->create($evenmentData);
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

    public function searchEvents(string $keyword, int $page = 1, int $limit = 5)
    {

        return $this->evenmentRepo->searchEvents($keyword, $page, $limit);
    }

    public function getTotalSearchResults(string $keyword)
    {

        return $this->evenmentRepo->getTotalSearchResults($keyword);
    }

    public function getEvents()
    {
        return $this->evenmentRepo->getAll();
    }

    public function updateEventStatus($eventId, $status)
    {
        switch ($status) {
            case Event::VALIDATED:
                return $this->evenmentRepo->validate($eventId);
            case Event::UNVALIDATED:
                return $this->evenmentRepo->unValidate($eventId);
            case Event::ARCHIVED:
                return $this->evenmentRepo->archive($eventId);
            case Event::UNARCHIVED:
                return $this->evenmentRepo->unArchive($eventId);
            default:
                throw new \Exception("Invalid status");
        }
    }

    public function deleteEvent($eventId)
    {
        return $this->evenmentRepo->delete($eventId);
    }

    public function getTotalActiveEvents()
    {
        return $this->evenmentRepo->totalActiveEvents();
    }

    public function getTotalTicketsSold()
    {
        return $this->evenmentRepo->totalTicketsSold();
    }

    public function getTotalRevenue()
    {
        return $this->evenmentRepo->totalRevenue();
    }

    public function getPendingEvents()
    {
        return $this->evenmentRepo->getPendingEvents();
    }

    public function getEventById($id)
    {
        return $this->evenmentRepo->getById($id);
    }

    public function getCapacities($id)
    {
        return $this->capacityRepo->getEventStatistics($id);
    }

    public function getTags($id)
    {
        return $this->evenmentTagRepo->getTagById($id);
    }

    public function getMyEvent($id)
    {
        return $this->evenmentRepo->getMyEvents($id);
    }
    public function getRecentEvents($limit = 2)
    {
        return $this->evenmentRepo->getRecentEvents($limit);
    }

    public function sortEvents($sort)
    {
        return $this->evenmentRepo->sortEvents($sort);
    }

    public function getPaginatedEvents($page = 1, $perPage = 5)
    {
        return $this->evenmentRepo->getAdminPaginatedEvents($page, $perPage);
    }
    public function getPaginatedEventsHome($page = 1,$limit = 2,$categories = [])
    {
        return $this->evenmentRepo->getPaginatedEvents($page,$limit,$categories);
    }

    public function getTotalEvents()
    {
        return $this->evenmentRepo->getTotalEvents();
    }

    public function getEventTicketsAndCapacity($eventId)
    {
        return $this->evenmentRepo->getEventTicketsAndCapacity($eventId);
    }
}
