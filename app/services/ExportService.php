<?php

namespace App\services;

use App\repository\UserRepository;
use App\repository\EvenmentRepository;
use TCPDF;

class ExportService
{
    private UserRepository $userRepository;
    private EvenmentRepository $evenmentRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->evenmentRepository = new EvenmentRepository();
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
}
