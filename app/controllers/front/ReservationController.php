<?php

namespace App\controllers\front;

use App\services\ReservationService;
use App\services\EventService;
use App\core\Controller;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Dotenv\Dotenv;

class ReservationController extends Controller
{
    private $reservationService;
    private $eventService;

    public function __construct()
    {
        parent::__construct();
        $this->reservationService = new ReservationService();
        $this->eventService = new EventService();
    }

    public function index($id)
    {
        $data = $this->eventService->getEventById($id[0]);
        $statis = $this->eventService->getCapacities($id[0]);

        echo $this->view->render("front/event/booking.html.twig", [
            'data' => $statis,
            'event' => $data
        ]);
    }

    public function getBooking()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $user = $this->session->get('user');
        if (!$user) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $userId = $user->id;

        $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        $vipQuantity = filter_input(INPUT_POST, 'vipQuantity', FILTER_VALIDATE_INT);
        $standardQuantity = filter_input(INPUT_POST, 'standardQuantity', FILTER_VALIDATE_INT);
        $freeQuantity = filter_input(INPUT_POST, 'freeQuantity', FILTER_VALIDATE_INT);
        $totalPrice = filter_input(INPUT_POST, 'totalPrice', FILTER_VALIDATE_FLOAT);

        if (!$event_id || $totalPrice < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            exit;
        }

        $booking_date = (new \DateTime())->format('Y-m-d H:i:s');

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
        $dotenv->safeLoad();

        $stripeKey = 'sk_test_51Qsl4eC6cEMis3nd6veqKeIyZpo2ap2AeE8An8uSWjW9nWGmMTdNt8dIPpxjtA4ZVre7Z7hYUlucjO52zcYIwpPB00fnOgtUFe'; // Your Stripe API key

        if (!$stripeKey) {
            http_response_code(500);
            echo json_encode(['error' => 'Stripe API key is missing']);
            exit;
        }

        Stripe::setApiKey($stripeKey);

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($totalPrice * 100), // Convert to cents
                'currency' => 'usd',
                'payment_method_types' => ['card'],
            ]);

            echo json_encode([
                'clientSecret' => $paymentIntent->client_secret,
                'message' => 'Booking successful!',
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Payment error: ' . $e->getMessage()]);
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

    public function downloadTicket($ticketId)
    {
        $ticket = $this->reservationService->getTicketById($ticketId);
        $pdf = $this->reservationService->generateTicketPDF($ticket);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="ticket.pdf"');
        echo $pdf;
        exit;
    }
}
