<?php
namespace App\services;

use App\repository\CapacityRepository;
use App\repository\EvenmentTagRepository;
use App\repository\EventRepository;
use App\repository\ReservationRepository; // Ensure this is included
use App\models\Event;
use App\core\Database;

class ReservationService {
    private CapacityRepository $capacityRepo;
    private EvenmentTagRepository $evenmentTagRepo;
    private EventRepository $eventRepository;
    private ReservationRepository $reservationRepository; // Change this line
    private Event $event;

    public function __construct() {
        $this->capacityRepo = new CapacityRepository();
        $this->evenmentTagRepo = new EvenmentTagRepository();
        $this->event = new Event();
        $this->reservationRepository = new ReservationRepository(); // Change this line
        $this->eventRepository = new EventRepository(new Database(), $this->event);
    }

    public function insertBooking($userId, $eventId, $type,  $totalPrice, $booking_date){
        $this->reservationRepository->createBooking($userId, $eventId, $type, $totalPrice , $booking_date);
    }

    public function updateSold($event_id, $new_vip_tickets,$new_standard_tickets,$new_gratuit_tickets){
        $this->capacityRepo->updateSold($event_id, $new_vip_tickets,$new_standard_tickets,$new_gratuit_tickets);
    }

    public function getAvailable($event_id){
        return $this->capacityRepo->getAvailable($event_id);
    }
}