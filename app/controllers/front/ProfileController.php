<?php

namespace App\controllers\front;

use App\core\Controller;
use App\core\View;
use App\services\UserService;

class ProfileController extends Controller
{

    protected StatService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }
    public function updateUser(){

        
    }
}