<?php

namespace App\controllers\front;

use App\services\ReservationService;
use App\services\EventService;
use App\core\Controller;
// use App\services\NotificationService;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Dotenv\Dotenv;

class ReservationController extends Controller
{
    private $reservationService;
    private $eventService;
    // private NotificationService $notif ;
    public function __construct()
    {
        parent::__construct();
        $this->reservationService = new ReservationService();
        $this->eventService = new EventService();
// use App\services\NotificationService;
        // $this->notif = new NotificationService();

    }

    public function index($id)
    {
        $data = $this->eventService->getEventById($id[0]);
        $statis = $this->eventService->getCapacities($id[0]);
        $availible = $this->reservationService->getAvailable($id[0]);

        $standard_tickets_available = $availible->standard_tickets_available > 0 ? $availible->standard_tickets_available : 0;
        $gratuit_tickets_available = $availible->gratuit_tickets_available  > 0 ? $availible->gratuit_tickets_available : 0;
        $vip_tickets_available = $availible->vip_tickets_available  > 0 ? $availible->vip_tickets_available : 0;

        echo $this->view->render("front/event/booking.html.twig", ['data' => $statis, 'event' => $data, 'standard_tickets_available' => $standard_tickets_available, 'vip_tickets_available' => $vip_tickets_available, 'gratuit_tickets_available' => $gratuit_tickets_available]);
    }

    public function getBooking()
    {
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
                $this->reservationService->insertBooking($userId, $event_id, $type, $totalPrice, $booking_date);

               
                
                $this->reservationService->updateSold($event_id, $vipQuantity, $standardQuantity, $freeQuantity);
            } catch (\Exception $e) {
                // Handle any errors with Stripe payment
                http_response_code(500);
                echo json_encode(['error' => 'Payment error: ' . $e->getMessage()]);
            }
        }
    }


    public function getMyTickets()
    {
        $userId = $this->session->get('user')->id;
        $tickets = $this->reservationService->getUserTickets($userId);

        return $this->render('front/tickets.html.twig', [
            'tickets' => $tickets
        ]);
    }

    public function downloadTicket($params)
    {
        $ticketId = (int)$params[0];
        $ticket = $this->reservationService->getTicketById($ticketId);
        $pdf = $this->reservationService->generateTicketPDF($ticket);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="ticket.pdf"');
        echo $pdf;
        exit;
    }


    public function handlePayment()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $userId = $this->session->get('user')->id;
            $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
            $vipQuantity = filter_input(INPUT_POST, 'vipQuantity', FILTER_VALIDATE_INT);
            $standardQuantity = filter_input(INPUT_POST, 'standardQuantity', FILTER_VALIDATE_INT);
            $freeQuantity = filter_input(INPUT_POST, 'freeQuantity', FILTER_VALIDATE_INT);
            $vipPrice = $_POST['vipPrice'];
            $standardPrice = $_POST['standardPrice'];
            $totalPrice = 0;

            // Array to hold ticket details
            $tickets = [];

            // Process VIP Tickets
            if ($vipQuantity > 0) {
                for ($i = 0; $i < $vipQuantity; $i++) {
                    $tickets[] = [
                        'type' => 'VIP',
                        'price' => $vipPrice
                    ];
                }
                $totalPrice += $vipPrice * $vipQuantity;
            }

            // Process Standard Tickets
            if ($standardQuantity > 0) {
                for ($i = 0; $i < $standardQuantity; $i++) {
                    $tickets[] = [
                        'type' => 'Standard',
                        'price' => $standardPrice
                    ];
                }
                $totalPrice += $standardPrice * $standardQuantity;
            }

            // Process Free Tickets
            if ($freeQuantity > 0) {
                for ($i = 0; $i < $freeQuantity; $i++) {
                    $tickets[] = [
                        'type' => 'Free',
                        'price' => 0
                    ];
                }
                // Free tickets do not add to totalPrice
            }

            // error_log("VIP Price: " . $vipPrice);
            // error_log("Standard Price: " . $standardPrice);
            // error_log("Tickets: " . json_encode($tickets));

            // Load environment variables for Stripe key
            // $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
            // $dotenv->safeLoad();
            // $stripeKey = getenv('STRIPE_API_KEY'); // Fetch Stripe key from .env

            // if (!$stripeKey) {
            //     http_response_code(500);
            //     echo json_encode(['error' => 'Stripe API key is missing']);
            //     exit;
            // }

            // \Stripe\Stripe::setApiKey($stripeKey);

            try {
                $date = date('d-m-y');
                $booking_date = (new \DateTime($date))->format('Y-m-d H:i:s');

                // Insert each ticket into the database
                foreach ($tickets as $ticket) {
                    $this->reservationService->insertBooking($userId, $event_id, $ticket['type'], $ticket['price'], $booking_date);
                }
                $this->reservationService->updateSold($event_id, $vipQuantity, $standardQuantity, $freeQuantity);
                // Create a PaymentIntent with the calculated total price (in cents)
                \Stripe\Stripe::setApiKey('sk_test_51Qsl4eC6cEMis3nd6veqKeIyZpo2ap2AeE8An8uSWjW9nWGmMTdNt8dIPpxjtA4ZVre7Z7hYUlucjO52zcYIwpPB00fnOgtUFe');

                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Event Ticket',
                            ],
                            'unit_amount' => $totalPrice * 100, // Amount in cents
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => 'http://localhost:8240/events/list', // Redirect URL after successful payment
                    'cancel_url' => 'http://localhost:8240/events/list', // Redirect URL if payment is canceled
                ]);

                header("Location: " . $session->url);
                exit();
            } catch (\Exception $e) {
                // Handle any errors with Stripe payment
                http_response_code(500);
                echo json_encode(['error' => 'Payment error: ' . $e->getMessage()]);
            }
        }
    }
}
