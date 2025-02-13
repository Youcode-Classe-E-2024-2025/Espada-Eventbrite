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
        parent::__construct();
        $this->eventService = new EventService();
        $this->userService = new UserService();
    }

    public function index()
    {
        if ($_SESSION['user']->role_id == 1) {
            echo $this->render("/front/organiser/dashboard.twig");
        } else if ($_SESSION['user']->role_id == 2) {
            echo $this->render("/front/profile.html.twig");
        } else if ($_SESSION['user']->role_id == 3) {
            $stats = $this->getStats();
            $pendingActions = $this->getPendingActions();
            // var_dump($pendingActions);
            echo $this->render("/back/index.html.twig", ['stats' => $stats, 'pending_actions' => $pendingActions]);
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
}
