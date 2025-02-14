<?php

namespace App\controllers\front;

use App\core\Controller;
use App\services\EventService;
use App\repository\EvenmentRepository;
use App\repository\CategoryRepo;

class EventController extends Controller
{
    private EventService $eventService;
    private EvenmentRepository $evenmentRepository; 
    private CategoryRepo $categoryRepo; 

    public function __construct()
    {
        parent::__construct();
        $this->eventService = new EventService();
        $this->evenmentRepository = new EvenmentRepository();
        $this->categoryRepo = new CategoryRepo();
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

      $events = $this->evenmentRepository->getPaginatedEvents($page, $limit,$categories);
      $totalEvents = $this->evenmentRepository->totalActiveEvents();
      $totalPages = ceil($totalEvents / $limit);
      $categories = $this->categoryRepo->getAll();
      $data = [
        'events' => $events,
        'totalEvents' => $totalEvents,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'categories' => $categories
      ];

      if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        // Return JSON for AJAX requests
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
      }

      echo $this->render('front/event/event-list.html.twig', $data);
    }


    public function eventDetails($id){
      $data = $this->eventService->getEventById($id[0]);
      $statis = $this->eventService->getCapacities($id[0]);
      $tags = $this->eventService->getTags($id[0]);
      
     echo $this->render('front/event/event-detail.html.twig',['event' => $data, 'statistics'=> $statis, 'tags'=> $tags]);
  }

    public function search()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;

        $results = $this->eventService->searchEvents($keyword, $page, $limit);

        $this->render('front/event-list.html.twig', [
            'events' => $results['events'],
            'keyword' => $keyword,
            'current_page' => $page,
            'total_pages' => $results['pages'],
            'total' => $results['total']
        ]);
    }
}
