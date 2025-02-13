<?php

namespace App\controllers\back;

use App\core\Controller;
use App\services\EventService;
use App\services\UserService;

class DashboardController extends Controller
{
    private EventService $eventService;
    private UserService $userService;

    public function __construct()
    {
        $this->eventService = new EventService();
        parent::__construct();
        $this->eventService = new EventService();
        $this->userService = new UserService();
    }

    public function index()
    {
        if ($_SESSION['user']->role_id == 1) {
            echo $this->render("/front/organiser/dashboard.twig");
        } else if ($_SESSION['user']->role_id == 2) {
            $id = $this->session->get('user')->id;
            $data = $this->eventService->getMyEvents($id);
            echo $this->render("/front/profile.html.twig", ["event1" => $data[0], "event2" => $data[1]]);
        } else if ($_SESSION['user']->role_id == 3) {
            $stats = $this->getStats();
            $pendingActions = $this->getPendingActions();
            $recentActivities = $this->getRecentActivities();
            // var_dump($pendingActions);
            echo $this->render("/back/index.html.twig", [
                'stats' => $stats,
                'pendingActions' => $pendingActions,
                'recentActivities' => $recentActivities
            ]);
        } else {
            echo $this->render("/back/404.html.twig");
        }
    }
    public function showEvents()
    {
        echo $this->render("/back/events.html.twig");
    }
    public function showUsers()
    {
        echo $this->render("/back/users.html.twig");
    }
    public function showComments()
    {
        echo $this->render("/back/comments.html.twig");
    }

    private function getStats()
    {
        $totalUsers = $this->userService->getTotalUsers();
        $activeEvents = $this->eventService->getTotalActiveEvents();
        $ticketsSold = $this->eventService->getTotalTicketsSold();
        $revenue = $this->eventService->getTotalRevenue();

        // var_dump($totalUsers, $activeEvents, $ticketsSold, $revenue);
        // die();

        return  [
            'totalUsers' => $totalUsers,
            'activeEvents' => $activeEvents,
            'ticketsSold' => $ticketsSold,
            'revenue' => $revenue
        ];
    }

    private function getPendingActions()
    {
        $pendingEvents = $this->eventService->getPendingEvents();
        // $pendingUsers = $this->userService->getPendingUsers();

        return [
            'pendingEvents' => $pendingEvents,
            // 'pendingUsers' => $pendingUsers
        ];
    }

    private function getRecentActivities()
    {
        $recentUsers = $this->userService->getRecentUsers();
        $recentEvents = $this->eventService->getRecentEvents();
        // $recentComments = $this->eventService->getRecentComments();

        return [
            'recentUsers' => $recentUsers,
            'recentEvents' => $recentEvents,
            // 'reportedComments' => $recentComments
        ];
    }
}
