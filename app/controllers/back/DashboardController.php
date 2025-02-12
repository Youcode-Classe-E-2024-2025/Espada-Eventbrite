<?php
namespace App\controllers\back;
use App\core\Controller;
class DashboardController extends Controller{
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        echo $this->render("/back/index.html.twig");
    }
    public function showEvents(){
        echo $this->render("/back/events.html.twig");
    }
    public function showUsers(){
        echo $this->render("/back/users.html.twig");
    }
    public function showComments(){
        echo $this->render("/back/comments.html.twig");
    }
}