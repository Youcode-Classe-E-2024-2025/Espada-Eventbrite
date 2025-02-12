<?php

namespace app\controllers\front;

use App\core\Controller;

class HomeController extends controller
{

  public function index()
  {
    $res = $this->session->get('user');

    if ($res->role_id == '1') { //organiser
      $this->redirect('/dashboard');
    } else if ($res->role_id == '2') { //user
      $this->redirect('/dashboard');
    } else { // admin
      $this->redirect('/dashboard');
    }
  }
}
