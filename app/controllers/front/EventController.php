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
      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $limit = 2; // Events per page
      
      $events = $this->evenmentRepository->getPaginatedEvents($page, $limit);
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
    public function serchByCategory()
    {
      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $limit = 2; // Events per page
      
      $events = $this->evenmentRepository->getPaginatedEvents($page, $limit);
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
