<?php

namespace App\controllers\front;

use App\core\Controller;
use App\services\EventService;
use App\repository\EvenmentRepository;
use App\repository\CategoryRepository;
use App\services\ReservationService;

class EventController extends Controller
{
  private EventService $eventService;
  private EvenmentRepository $evenmentRepository;
  private CategoryRepository $categoryRepo;
  private ReservationService $ReservationService;

  public function __construct()
  {
    parent::__construct();
    $this->eventService = new EventService();
    $this->evenmentRepository = new EvenmentRepository();
    $this->categoryRepo = new CategoryRepository();
    $this->ReservationService = new ReservationService();
  }

  public function index()
  {
    $categories = [];
    // Check if categories are set and convert to array
    if (isset($_GET['categories'])) {
      // If it's a single value, wrap it in an array
      $categories = is_array($_GET['categories']) ? $_GET['categories'] : [$_GET['categories']];

      // Convert to integers and remove any invalid values
      $categories = array_map('intval', $categories);
      $categories = array_filter($categories);
    }
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 2; // Events per page


    $events = $this->evenmentRepository->getPaginatedEvents($page, $limit, $categories);
    $totalEvents = $this->evenmentRepository->totalActiveEvents();
    $totalPages = ceil($totalEvents / $limit);
    $categories = $this->categoryRepo->getAll();
    $data = [
      'events' => $events,
      'totalEvents' => $totalEvents,
      'currentPage' => $page,
      'totalPages' => $totalPages,
      'categories' => $categories,
    ];


    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
      // Return JSON for AJAX requests
      header('Content-Type: application/json');
      echo json_encode($data);
      exit;
    }

      $standard_tickets_available = $availible->standard_tickets_available > 0 ? $availible->standard_tickets_available: 0 ;
      $gratuit_tickets_available = $availible->gratuit_tickets_available  > 0 ? $availible->gratuit_tickets_available: 0;
      $vip_tickets_available = $availible->vip_tickets_available  > 0 ? $availible->vip_tickets_available: 0;
     
     echo $this->render('front/event/event-detail.html.twig',['event' => $data, 'statistics'=> $statis, 'tags'=> $tags, 'standard_tickets_available'=> $standard_tickets_available, 'vip_tickets_available'=> $vip_tickets_available, 'gratuit_tickets_available'=> $gratuit_tickets_available]);

  }


  public function eventDetails($id)
  {
    $data = $this->eventService->getEventById($id[0]);
    $statis = $this->eventService->getCapacities($id[0]);
    $tags = $this->eventService->getTags($id[0]);
    $availible = $this->ReservationService->getAvailable($id[0]);

    echo $this->render('front/event/event-detail.html.twig', ['event' => $data, 'statistics' => $statis, 'tags' => $tags, 'available' => $availible]);
  }

  public function search()
  {
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 2;

    $this->logger->info('Searching events with keyword: ' . $keyword);

    $categories = $this->categoryRepo->getAll();

    $events = $this->eventService->searchEvents($keyword, $page, $perPage);
    $totalEvents = $this->eventService->getTotalSearchResults($keyword);
    $totalPages = ceil($totalEvents / $perPage);

    $messages = $this->session->get('messages') ?? [];

    return $this->render('front/event/event-list.html.twig', [
      'events' => $events,
      'keyword' => $keyword,
      'messages' => $messages,
      'currentPage' => $page,
      'totalPages' => $totalPages,
      'totalEvents' => $totalEvents,
      'categories' => $categories,
    ]);
  }
}
