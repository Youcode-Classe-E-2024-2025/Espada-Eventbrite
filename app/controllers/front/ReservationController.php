<?php

namespace App\controllers\front;

use App\services\ReservationService;
use App\services\EventService;
use App\core\Controller;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Dotenv\Dotenv;

class ReservationController extends Controller {
    private $ReservationService;
    private $eventService;

    public function __construct() {
        parent::__construct();
        $this->ReservationService = new ReservationService();
        $this->eventService = new EventService();
    }

    public function index($id) {
        $data = $this->eventService->getEventById($id[0]);
        $statis = $this->eventService->getCapacities($id[0]);

        echo $this->view->render("front/event/booking.html.twig", ['data' => $statis, 'event' => $data]);
    }

    public function getBooking() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sanitize and validate input data
            $userId = $this->session->get('user')->id; 
            $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
            $vipQuantity = filter_input(INPUT_POST, 'vipQuantity', FILTER_VALIDATE_INT);
            $standardQuantity = filter_input(INPUT_POST, 'standardQuantity', FILTER_VALIDATE_INT);
            $freeQuantity = filter_input(INPUT_POST, 'freeQuantity', FILTER_VALIDATE_INT);
            $totalPrice = filter_input(INPUT_POST, 'totalPrice', FILTER_VALIDATE_FLOAT);

            // Ensure that required fields are valid
            if (!$event_id || $totalPrice <= 0 || $vipQuantity === null || $standardQuantity === null || $freeQuantity === null) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input']);
                exit;
            }

            $date = date('d-m-y');
            $booking_date = (new \DateTime($date))->format('Y-m-d H:i:s');
            
            $type = 'VIP'; // You can adjust this logic based on the actual type selected by the user

            // Load environment variables for Stripe key
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
            $dotenv->safeLoad();

            $stripeKey = 'sk_test_51Qsl4eC6cEMis3nd6veqKeIyZpo2ap2AeE8An8uSWjW9nWGmMTdNt8dIPpxjtA4ZVre7Z7hYUlucjO52zcYIwpPB00fnOgtUFe'; // Fetch Stripe key from .env

            if (!$stripeKey) {
                http_response_code(500);
                echo json_encode(['error' => 'Stripe API key is missing']);
                exit;
            }

            Stripe::setApiKey($stripeKey);

            try {
                // Create a PaymentIntent with the calculated total price (in cents)
                $paymentIntent = PaymentIntent::create([
                    'amount' => intval($totalPrice * 100), // Convert to cents
                    'currency' => 'usd', // Adjust as needed for your use case
                    'payment_method_types' => ['card'],
                ]);

                // Return the client secret to the frontend
                echo json_encode([
                    'clientSecret' => $paymentIntent->client_secret,
                    'message' => 'Booking successful!',
                ]);

                // Insert booking details into the database
                $this->ReservationService->insertBooking($userId, $event_id, $type, $totalPrice, $booking_date);
                $this->ReservationService->updateSold($event_id, $vipQuantity, $standardQuantity, $freeQuantity);
            } catch (\Exception $e) {
                // Handle any errors with Stripe payment
                http_response_code(500);
                echo json_encode(['error' => 'Payment error: ' . $e->getMessage()]);
            }
        }
    }
}