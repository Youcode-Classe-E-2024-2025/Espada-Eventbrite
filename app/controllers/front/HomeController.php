<?php

namespace App\controllers\front;

use App\core\Controller;

class HomeController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        return $this->render('back/index.html.twig');
    }
}
