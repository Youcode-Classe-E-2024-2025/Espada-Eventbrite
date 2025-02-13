<?php

namespace App\controllers\back;

use App\core\Controller;
use App\services\EventService;
use App\services\UserService;

class AdminEventController extends Controller
{
    private EventService $eventService;
    private UserService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->eventService = new EventService();
        $this->userService = new UserService();
    }

    public function index()
    {
        // $this->getStats();
        $events = $this->eventService->getEvents();

        return $this->render('back/events.html.twig', ['events' => $events]);
    }

    public function search()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

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
        // var_dump($eventId);
        // var_dump($status);
        // exit;
        if ($eventId && isset($status)) {
            $this->eventService->updateEventStatus($eventId, $status);
            // $this->redirect('/admin/events');
        }

        $this->redirect('/admin/events');
        exit;
    }

    public function delete()
    {
        $eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;
        if ($eventId) {
            $this->eventService->deleteEvent($eventId);
        }
        $this->redirect('/admin/events');
    }

    // private function getStats()
    // {
    //     $totalUsers = $this->userService->getTotalUsers();
    //     $activeEvents = $this->eventService->getTotalActiveEvents();
    //     $ticketsSold = $this->eventService->getTotalTicketsSold();
    //     $revenue = $this->eventService->getTotalRevenue();

    //     $this->render('back/index.html.twig', [
    //         'totalUsers' => $totalUsers,
    //         'activeEvents' => $activeEvents,
    //         'ticketsSold' => $ticketsSold,
    //         'revenue' => $revenue
    //     ]);
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
