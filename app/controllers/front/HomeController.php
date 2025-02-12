<?php

namespace app\controllers\front;

use App\core\Controller;

class HomeController extends controller
{
    public function __construct(){
        parent::__construct();
    }
   public function index()
  {
    echo $this->render('front/home.html.twig');
  }
}
