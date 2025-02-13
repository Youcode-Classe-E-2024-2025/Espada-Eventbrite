<?php

namespace App\controllers\front;

use App\core\Controller;
use App\services\EventService;
use App\repository\EvenmentRepository;
use App\models\Event;

class EventController extends Controller
{
    private EventService $eventService;
    private EvenmentRepository $evenmentRepository; 

    public function __construct()
    {
        parent::__construct();
        $this->eventService = new EventService();
        $this->evenmentRepository = new EvenmentRepository();
    }

    public function index()
    {
      $events =  $this->evenmentRepository->getAll();
      $data = [
        'events' => $events
      ];
      echo $this->render('front/event/event-list.html.twig',$data);
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
