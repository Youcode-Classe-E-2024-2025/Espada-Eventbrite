<?php
namespace app\controllers\front;
use App\core\Controller;
class HomeController extends Controller{
    public function index(){
        echo $this->render("/front/home.html.twig");
    }
}
?>