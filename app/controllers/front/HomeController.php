<?php
namespace app\controllers\front;
use App\core\View;
class HomeController extends View{
    public function index(){
        echo $this->render("/front/auth.twig");
    }
}
?>