<?php

namespace App\services;

use App\repository\UserRepository;
use App\repository\EvenmentRepository;
use TCPDF;

class ExportService
{
    private UserRepository $userRepository;
    private EvenmentRepository $evenmentRepository;
    private EventService $eventService;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->evenmentRepository = new EvenmentRepository();
        $this->eventService = new EventService();
    }

    public function getCsvData()
    {
        $users = $this->userRepository->getAll();
        $events = $this->evenmentRepository->getAll();

        if (empty($users) && empty($events)) {
            throw new \RuntimeException('Aucune donnée à exporter.');
        }

        ob_start();
        $output = fopen('php://output', 'w');

        try {
            fputcsv($output, ['ID', 'Username', 'Email', 'Created At']);

            foreach ($users as $user) {
                fputcsv($output, [$user->id, $user->username, $user->email, $user->created_at]);
            }

            fputcsv($output, []);
            fputcsv($output, []);

            fputcsv($output, ['ID', 'Title', 'Description', 'Date']);

            foreach ($events as $event) {
                fputcsv($output, [$event->id, $event->title, $event->description, $event->date]);
            }
            $csvData = ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            throw new \RuntimeException('Erreur lors de la génération du CSV : ' . $e->getMessage());
        }

        return $csvData;
    }

    public function getPdfData()
    {
        $users = $this->userRepository->getAll();
        $events = $this->evenmentRepository->getAll();

        if (empty($users) && empty($events)) {
            throw new \RuntimeException('Aucune donnée à exporter.');
        }

        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        $style = '
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #2c3e50;
                        color: white;
                        font-weight: bold;
                    }
                    tr:nth-child(even) {
                        background-color: #f9f9f9;
                    }
                    tr:hover {
                        background-color: #f1f1f1;
                    }
                </style>
            ';

        $html = "
                <h1>Users</h1>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>";

        foreach ($users as $user) {
            $html .= "<tr>
                            <td>{$user->id}</td>
                            <td>{$user->username}</td>
                            <td>{$user->email}</td>
                            <td>{$user->created_at}</td>
                        </tr>";
        }
        $html .= "</tbody>
                </table>";

        $html .= "<h1>Events</h1>
                <table border='1'>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                    </tr>";

        foreach ($events as $event) {
            $html .= "<tr>
                        <td>{$event->id}</td>
                        <td>{$event->title}</td>
                        <td>{$event->date}</td>
                    </tr>";
        }
        $html .= "</table>";

        $pdf->writeHTML($style . $html);
        return $pdf->Output('', 'S');
    }

    public function getOrganizerCsvData($organizerId)
    {

        $events = $this->eventService->getMyEvent($organizerId);

        ob_start();
        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'Event ID',
            'Title',
            'Description',
            'Visual Content',
            'Location',
            'Date',
            'Type',
            'Category',
            'Total Capacity',
            'VIP Capacity',
            'VIP Price',
            'VIP Sold',
            'Standard Capacity',
            'Standard Price',
            'Standard Sold',
            'Free Capacity',
            'Free Sold',
            'Organizer ID',
            'Organizer Name',
            'Organizer Email'
        ]);

        foreach ($events as $event) {
            $organizer = $this->userRepository->getUserById($event->owner_id);
            $capacity = $this->eventService->getEventTicketsAndCapacity($event->id);

            fputcsv($output, [
                $event->id,
                $event->title,
                $event->description,
                $event->visual_content,
                $event->lieu,
                $event->date,
                $event->type,
                $event->category ?? 'N/A',
                $capacity->total_capacity ?? 0,
                $capacity->vip_capacity ?? 0,
                $capacity->vip_price ?? 0,
                $capacity->vip_sold ?? 0,
                $capacity->standard_capacity ?? 0,
                $capacity->standard_price ?? 0,
                $capacity->standard_sold ?? 0,
                $capacity->free_capacity ?? 0,
                $capacity->free_sold ?? 0,
                $organizer->id,
                $organizer->username,
                $organizer->email
            ]);
        }

        return ob_get_clean();
    }


    public function getOrganizerPdfData($organizerId)
    {
        $events = $this->eventService->getMyEvent($organizerId);

        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Eventbrite');
        $pdf->SetAuthor('Eventbrite System');
        $pdf->SetTitle('Events Report');

        // Disable header and footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 10, 'Events Report', 0, 1, 'C');
        $pdf->Ln(10);

        // Table headers
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(60, 7, 'Event Name', 1);
        $pdf->Cell(40, 7, 'Date', 1);
        $pdf->Cell(30, 7, 'Type', 1);
        $pdf->Cell(30, 7, 'Location', 1);
        $pdf->Ln();

        // Table content
        $pdf->SetFont('helvetica', '', 12);
        foreach ($events as $event) {
            $pdf->Cell(60, 6, $event->title, 1);
            $pdf->Cell(40, 6, $event->date, 1);
            $pdf->Cell(30, 6, $event->type, 1);
            $pdf->Cell(30, 6, $event->lieu, 1);
            $pdf->Ln();
        }

        // Output the PDF as a string
        return $pdf->Output('events_report.pdf', 'S');
    }
}
