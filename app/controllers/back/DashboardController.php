<?php

namespace App\controllers\back;

use App\core\Controller;
use App\services\EventService;

class DashboardController extends Controller
{
    private  EventService $eventService;
    public function __construct()
    {
        $this->eventService = new EventService();
        parent::__construct();
    }

    public function index()
    {
        if ($_SESSION['user']->role_id == 1) {
            echo $this->render("/front/organiser/dashboard.twig");
        } else if ($_SESSION['user']->role_id == 2) {
            $id = $this->session->get('user')->id;
            $data = $this->eventService->getMyEvents($id);
            echo $this->render("/front/profile.html.twig", ["event1"=> $data[0],"event2"=> $data[1]]);
        } else if ($_SESSION['user']->role_id == 3) {
            echo $this->render("/back/index.html.twig");
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
}
