<?php


namespace App\controllers\front ; 
use App\services\ReservationService;
use App\services\EventService;

use App\core\Controller;
class ReservationController extends Controller{
    private $ReservationService;
    private $eventService;
    public function __construct(){
        parent::__construct();
        $this->ReservationService = new ReservationService();
        $this->eventService = new EventService();
    }
    public function index($id){
        $data = $this->eventService->getEventById($id[0]);
        $statis = $this->eventService->getCapacities($id[0]);
        echo $this->view->render("front/event/booking.html.twig", ['data' => $statis,'event'=>$data]);
    }
    public function getBooking($id) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $event_id = $id[0];
            $vipQuantity = $_POST['vipQuantity'];
            $standardQuantity = $_POST['standardQuantity'];
            $totalPrice = $_POST['totalPrice'];
    
            $userId = $this->session->get('user')->id; 
            $date = date('d-m-y');

            $booking_date = (new \DateTime($date))->format('Y-m-d H:i:s');

            $type = 'VIP';

            $this->ReservationService->insertBooking($userId, $event_id, $type , $totalPrice, $booking_date);
    
            echo "Booking successful! VIP Quantity: $vipQuantity, Standard Quantity: $standardQuantity, Total Price: $$totalPrice";
        }
    }
}