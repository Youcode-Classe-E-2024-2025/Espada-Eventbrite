<?php


namespace App\controllers\front ; 

use App\core\Controller;
use App\models\Reservation;
 class ReservationController extends Controller{
        public function index(){
            echo $this->view->render("front/event/booking.html.twig");
        }
        public function getBooking(){
            $db = $this->db;
            $reservation = new Reservation($db);
            $reservation->user_id = 2;
            $reservation->evenment_id = 1;
            $reservation->type = 'standard';
            $reservation->price = 50.00;
            $reservation->createBooking();
        }
    }