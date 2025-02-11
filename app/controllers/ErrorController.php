<?php
namespace App\controllers;
use App\core\Controller;
class ErrorController extends Controller{
    public function __construct(){
        parent::__construct();
    }
    function notfound(): void{
        echo $this->render('/back/404.html.twig');
    }
}