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
        $events = $this->eventService->getEvents();

        return $this->render('back/events.html.twig', ['events' => $events]);
    }

    public function search()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        // $owner = isset($_GET['owner_id']) ? (int)$_GET['owner_id'] : null;
        // $status = isset($_GET['status']) ? $_GET['status'] : null;

        $events = $this->eventService->searchEvents($keyword);

        return $this->render('back/events.html.twig', [
            'events' => $events,
            'keyword' => $keyword,
            // 'owner' => $owner,
            // 'status' => $status
        ]);
    }

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
