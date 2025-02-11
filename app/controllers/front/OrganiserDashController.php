<?php


namespace App\controllers\front ; 

use App\core\Controller;
use App\core\View;

class OrganiserDashController extends Controller{



    public function index(): void
    {
        
        echo $this->view->render('front/orgniser/dashboard.twig',[]);
    }







}


