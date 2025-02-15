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
        $this->logger->info('Dashboard index accessed');

        $messages = $this->session->get('messages') ?? [];
        $csrfToken = $this->security->generateCsrfToken();

        // Safely get role_id from session user
        $userRole = is_object($_SESSION['user']) 
            ? $_SESSION['user']->role_id 
            : $_SESSION['user']['role_id'] ?? null;

        if ($userRole == 1) {
            $this->logger->info('Organiser dashboard accessed');
            echo $this->redirect('/Organiser/dash');
        } else if ($userRole == 2) {
            // Safely get user id from session
            $id = is_object($_SESSION['user']) 
                ? $_SESSION['user']->id 
                : $_SESSION['user']['id'] ?? null;

            $data = $this->eventService->getMyEvent($id);

            // Ensure $data has at least two elements
            if (count($data) >= 2) {
                $this->logger->info('User dashboard accessed with events.');
                echo $this->render("/front/profile.html.twig", [
                    "event1" => $data[0],
                    "event2" => $data[1],
                    "messages" => $messages,
                    "csrf_token" => $csrfToken
                ]);
            } else {
                // Handle the case where $data doesn't have enough elements
                $this->logger->debug('Not enough events found for user dashboard.');
                echo $this->render("/front/profile.html.twig", [
                    "event1" => null,
                    "event2" => null,
                    "messages" => $messages,
                    "csrf_token" => $csrfToken
                ]);
            }
        } else if ($userRole == 3) {
            $this->logger->info('Admin dashboard accessed');
            $stats = $this->getStats();
            $pendingActions = $this->getPendingActions();
            $recentActivities = $this->getRecentActivities();
            
            echo $this->render('/back/index.html.twig', [
                'stats' => $stats,
                'pendingActions' => $pendingActions,
                'recentActivities' => $recentActivities,
                "messages" => $messages,
                "csrf_token" => $csrfToken
            ]);
        } else {
            $this->logger->error('Invalid role for dashboard access');
            echo $this->render("/back/404.html.twig", [
                "messages" => $messages,
                "csrf_token" => $csrfToken
            ]);
        }
    }
    public function showEvents()
    {
        $this->logger->info('Events page accessed');
        $messages = $this->session->get('messages') ?? [];
        $csrfToken = $this->security->generateCsrfToken();
        echo $this->render("/back/events.html.twig", [
            "messages" => $messages,
            "csrf_token" => $csrfToken
        ]);
    }
    public function showUsers()
    {
        $this->logger->info('Users page accessed');
        $messages = $this->session->get('messages') ?? [];
        $csrfToken = $this->security->generateCsrfToken();
        echo $this->render("/back/users.html.twig", [
            "messages" => $messages,
            "csrf_token" => $csrfToken
        ]);
    }
    // public function showComments()
    // {
    //     $this->logger->info('Comments page accessed');
    //     echo $this->render("/back/comments.html.twig");
    // }

    private function getStats()
    {
        $this->logger->info('Fetching stats for dashboard');
        $totalUsers = $this->userService->getTotalUsers();
        $activeEvents = $this->eventService->getTotalActiveEvents();
        $ticketsSold = $this->eventService->getTotalTicketsSold();
        $revenue = $this->eventService->getTotalRevenue();

        // var_dump( $totalUsers, $activeEvents, $ticketsSold, $revenue );
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
        $this->logger->info('Fetching pending actions for dashboard');
        $pendingEvents = $this->eventService->getPendingEvents();
        // $pendingUsers = $this->userService->getPendingUsers();

        return [
            'pendingEvents' => $pendingEvents,
            // 'pendingUsers' => $pendingUsers
        ];
    }

    private function getRecentActivities()
    {
        $this->logger->info('Fetching recent activities for dashboard');
        $recentUsers = $this->userService->getRecentUsers();
        $recentEvents = $this->eventService->getRecentEvents($limit = 2);
        // $recentComments = $this->eventService->getRecentComments();

        return [
            'recentUsers' => $recentUsers,
            'recentEvents' => $recentEvents
            // 'reportedComments' => $recentComments
        ];
    }
}
