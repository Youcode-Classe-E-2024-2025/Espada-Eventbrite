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

        $csvData = "ID, Username, Email, Created At\n";
        foreach ($users as $user) {
            $csvData .= "{$user->id}, {$user->username}, {$user->email}, {$user->created_at}\n";
        }

        $csvData .= "\nID, Title, Date\n";
        foreach ($events as $event) {
            $csvData .= "{$event->id}, {$event->title}, {$event->date}\n";
        }

        return $csvData;
    }

    public function getPdfData()
    {
        $users = $this->userRepository->getAll();
        $events = $this->evenmentRepository->getAll();

        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        $html = '<h1>Users</h1>
                <table border="1">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Created At</th>
                    </tr>';

        foreach ($users as $user) {
            $html .= '<tr>
                        <td>{$user->id}</td>
                        <td>{ $user->username }</td>
                        <td>{ $user->email }</td>
                        <td>{ $user->created_at }</td>
                    </tr>';
        }
        $html .= '</table>';

        $html .= '<h1>Events</h1>
                <table border="1">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                    </tr>';

        foreach ($events as $event) {
            $html .= "<tr>
                        <td>{$event->id}</td>
                        <td>{$event->title}</td>
                        <td>{$event->date}</td>
                    </tr>";
        }
        $html .= '</table>';

        $pdf->writeHTML($html);
        return $pdf->Output('', 'S');
    }
}
