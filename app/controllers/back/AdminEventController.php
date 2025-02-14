<?php

namespace App\controllers\back;

use App\core\Controller;
use App\services\EventService;

class AdminEventController extends Controller
{
    private EventService $eventService;

    public function __construct()
    {
        parent::__construct();
        $this->eventService = new EventService();
    }

    public function index()
    {
        $this->logger->info('Fetching all events');
        $events = $this->eventService->getEvents();

        return $this->render('back/events.html.twig', ['events' => $events]);
    }

    public function search()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $this->logger->info('Searching events with keyword: ' . $keyword);

        $events = $this->eventService->searchEvents($keyword);

        return $this->render('back/events.html.twig', [
            'events' => $events,
            'keyword' => $keyword,
        ]);
    }

    public function updateStatus()
    {
        $eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;
        $status = isset($_POST['status']) ? (int)$_POST['status'] : null;
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!$this->security->validateCsrfToken($csrfToken)) {
            $this->logger->error('Invalid CSRF token.');
            $this->redirect('/admin/events');
            exit;
        }

        if ($eventId && isset($status)) {
            $this->logger->info('Updating event status: ' . $eventId . ' to ' . $status);
            $this->eventService->updateEventStatus($eventId, $status);
            // $this->redirect('/admin/events');
        } else {
            $this->logger->error('Failed to update event status.');
        }

        $this->redirect('/admin/events');
        exit;
    }

    public function delete()
    {
        $eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!$this->security->validateCsrfToken($csrfToken)) {
            $this->logger->error('Invalid CSRF token.');
            $this->redirect('/admin/events');
            exit;
        }

        if ($eventId) {
            $this->logger->info('Deleting event: ' . $eventId);
            $this->eventService->deleteEvent($eventId);
        } else {
            $this->logger->error('Failed to delete event.');
        }

        $this->redirect('/admin/events');
    }

    // private function getStats()
    // {
    //     $totalUsers = $this->userService->getTotalUsers();
    //     $activeEvents = $this->eventService->getTotalActiveEvents();
    //     $ticketsSold = $this->eventService->getTotalTicketsSold();
    //     $revenue = $this->eventService->getTotalRevenue();

    //     $stats = [
    //         'totalUsers' => $totalUsers,
    //         'activeEvents' => $activeEvents,
    //         'ticketsSold' => $ticketsSold,
    //         'revenue' => $revenue
    //     ];

    //     // var_dump($totalUsers, $activeEvents, $ticketsSold, $revenue);
    //     // die();

    //     return $stats;
    // }


    // public function filter()
    // {
    //     $roleId = isset($_GET['role_id']) ? (int)$_GET['role_id'] : null;
    //     $status = isset($_GET['status']) ? (int)$_GET['status'] : null;

    //     $results = $this->userService->filterUsers($roleId, $status);

    //     return $this->render('back/users.html.twig', [
    //         'users' => $results,
    //         'role_id' => $roleId,
    //         'status' => $status
    //     ]);
    // }

    // public function updateStatus()
    // {
    //     $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    //     $status = isset($_POST['status']) ? (int)$_POST['status'] : null;

    //     if ($userId && isset($status)) {
    //         $this->userService->updateUserStatus($userId, $status);
    //         $this->redirect('/admin/users');
    //     }

    //     // $this->redirect('/back/users');
    //     exit;
    // }
}
