<?php

namespace App\services;

use App\repository\CapacityRepository;
use App\repository\EvenmentTagRepository;
use App\repository\EventRepository;
use App\repository\ReservationRepository; // Ensure this is included
use App\models\Event;
use App\core\Database;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use TCPDF;


class ReservationService
{
    private CapacityRepository $capacityRepo;
    private EvenmentTagRepository $evenmentTagRepo;
    private EventRepository $eventRepository;
    private ReservationRepository $reservationRepository; // Change this line
    private Event $event;

    public function __construct()
    {
        $this->capacityRepo = new CapacityRepository();
        $this->evenmentTagRepo = new EvenmentTagRepository();
        $this->event = new Event();
        $this->reservationRepository = new ReservationRepository(); // Change this line
        $this->eventRepository = new EventRepository(new Database(), $this->event);
    }

    public function insertBooking($userId, $eventId, $type,  $totalPrice, $booking_date)
    {
        $ticketId = uniqid('TICKET-');

        $qrData = [
            'ticket_id' => $ticketId,
            'event_id' => $eventId,
            'user_id' => $userId,
            'type' => $type
        ];

        $qrPath = $this->generateQRCode($qrData);

        $this->reservationRepository->createBooking($userId, $eventId, $type, $totalPrice, $booking_date, $qrPath);
    }

    public function updateSold($event_id, $new_vip_tickets, $new_standard_tickets, $new_gratuit_tickets)
    {
        $this->capacityRepo->updateSold($event_id, $new_vip_tickets, $new_standard_tickets, $new_gratuit_tickets);
    }

    public function getAvailable($event_id)
    {
        $this->capacityRepo->getAvailable($event_id);
    }

    private function generateQRCode($data)
    {
        $qrCode = new QrCode(json_encode($data));
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $path = 'public/assets/qrcodes/' . $data['ticket_id'] . '.png';
        $result->saveToFile($path);

        return $path;
    }

    public function getUserTickets($userId)
    {
        $query = "SELECT b.*, e.title as event_title, e.date as event_date 
              FROM booking b
              JOIN evenments e ON b.evenment_id = e.id
              WHERE b.user_id = :user_id
              ORDER BY b.booking_date DESC";

        return $this->reservationRepository->getUserTickets($userId);
    }

    public function generateTicketPDF($ticketData)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator('Eventbrite');
        $pdf->SetAuthor('Eventbrite');
        $pdf->SetTitle('Event Ticket');

        $pdf->AddPage();

        // Add ticket content
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Event: ' . $ticketData->event_title, 0, 1);
        $pdf->Cell(0, 10, 'Date: ' . $ticketData->event_date, 0, 1);
        $pdf->Cell(0, 10, 'Ticket Type: ' . $ticketData->type, 0, 1);
        $pdf->Cell(0, 10, 'Price: $' . $ticketData->price, 0, 1);

        // Add QR Code
        $pdf->Image($ticketData->qr_code_path, 15, 100, 50, 50, 'PNG');

        return $pdf->Output('ticket.pdf', 'S');
    }

    public function getTicketById($ticketId)
    {
        return $this->reservationRepository->getBookingById($ticketId);
    }
}
