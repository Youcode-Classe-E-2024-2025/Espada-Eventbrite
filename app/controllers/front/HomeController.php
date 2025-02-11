<?php
namespace app\controllers\front;
use App\core\View;

use App\core\Session;
use App\core\Database;

class HomeController extends View{

    private $db;
    public function index(){
          $s =new Session();
          $res=$s->get('user');
          echo $res->email;
        
    }
}
?>