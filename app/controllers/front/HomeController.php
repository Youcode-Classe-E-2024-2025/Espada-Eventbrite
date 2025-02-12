<?php
namespace app\controllers\front;
use App\core\Controller;

class HomeController extends controller{

        public function index(){
          $res=$this->session->get('user');
    
          if($res->role_id== '1'){ //user
            $this->redirect('/auth');
          }else if($res->role_id == '2'){ //particepant
            $this->redirect('/auth');
          }else{ // admin
            $this->redirect('/auth');
          }

        }
    }


