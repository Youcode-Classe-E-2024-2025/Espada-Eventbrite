<?php

namespace App\controllers\front;

use App\core\Controller;
use App\services\EventService;
use App\repository\EventRepository;
use App\models\Event;

class EventController extends Controller
{
    private EventService $eventService;

    public function __construct()
    {
        parent::__construct();
        $this->eventService = new EventService();
    }

    public function index()
    {
        $events = $this->eventService->getEvents();
        var_dump($events);
        die();
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
