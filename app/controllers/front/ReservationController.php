<?php


namespace App\controllers\front ; 

use App\core\Controller;
use App\models\Reservation;
 class ReservationController extends Controller{
    public function index(){
        echo $this->view->render("front/event/booking.html.twig");
    }
    public function getBooking(){}
}