<?php

namespace App\controllers\back;

use App\core\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($_SESSION['user']->role_id == 1) {
            echo $this->render("/front/organiser/dashboard.html.twig");
        } else if ($_SESSION['user']->role_id == 2) {
            echo $this->render("/front/profile.html.twig");
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
