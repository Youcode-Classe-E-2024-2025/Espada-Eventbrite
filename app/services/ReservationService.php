<?php

namespace App\services;

use App\repository\CapacityRepository;
use App\repository\EvenmentTagRepository;
use App\repository\EvenmentRepository;
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
    private EvenmentRepository $eventRepository;
    private ReservationRepository $reservationRepository; // Change this line
    private Event $event;

    public function __construct()
    {
        $this->capacityRepo = new CapacityRepository();
        $this->evenmentTagRepo = new EvenmentTagRepository();
        $this->event = new Event();
        $this->reservationRepository = new ReservationRepository(); // Change this line
        $this->eventRepository = new EvenmentRepository();
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
        ob_clean();

        $event = $this->eventRepository->getById($ticketData['evenment_id']);
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator('Eventbrite');
        $pdf->SetAuthor('Eventbrite');
        $pdf->SetTitle('Event Ticket');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();
        $pdf->SetFillColor(240, 248, 255);
        $pdf->Rect(0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'F');

        $pdf->Image('public/assets/images/logo.png', 15, 10, 30);

        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->SetTextColor(51, 51, 51);
        $pdf->Cell(0, 30, 'EVENT TICKET', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(0, 102, 204);
        $pdf->Cell(0, 10, $event->title, 0, 1, 'C');

        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetLineWidth(0.1);
        $pdf->Rect(15, 70, 180, 100, 'D');

        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(51, 51, 51);
        $pdf->SetXY(20, 80);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'Date:', 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, date('F d, Y', strtotime($event->date)), 0, 1);

        $pdf->SetX(20);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'Ticket Type:', 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $ticketData['type'], 0, 1);

        $pdf->SetX(20);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(40, 10, 'Price:', 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, '$' . $ticketData['price'], 0, 1);

        if (!empty($ticketData['qr_code_path'])) {
            $qrPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/qrcodes/' . $ticketData['qr_code_path'];
            if (file_exists($qrPath)) {
                $pdf->Image($qrPath, 140, 80, 50, 50, 'PNG');
            }
        }

        $pdf->SetY(-40);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(128, 128, 128);
        $pdf->Cell(0, 10, 'This ticket is valid for one-time entry only. Please present this ticket at the event.', 0, 1, 'C');

        return $pdf->Output('ticket.pdf', 'D');
    }


    public function getTicketById($ticketId)
    {
        return $this->reservationRepository->getBookingById($ticketId);
    }
}
