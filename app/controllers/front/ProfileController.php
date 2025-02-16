<?php

namespace App\controllers\front;

use App\core\Controller;
use App\core\View;
use App\services\UserService;

class ProfileController extends Controller
{

    protected UserService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }
    public function updateUser(){
        $id = $this->session->get('user')->id;
        $name = $_POST['fullname'];  
        $this->session->get('user')->username = $name;

        $this->userService->updateUser($id,$name);
        $this->redirect('/dashboard');
    }
}