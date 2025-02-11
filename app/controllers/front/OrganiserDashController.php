<?php


namespace App\controllers ; 

use App\core\Controller;
use App\core\View;

class OrganiserDashController extends Controller{



    public function index(): void
    {
        $view = new View();
        echo $view->render('front/orgniser/dashboard.twig',[]);
    }







}


