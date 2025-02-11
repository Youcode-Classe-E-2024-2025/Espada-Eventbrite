<?php

namespace app\controllers\front;

use app\core\Controller;
use app\repository\EventRepository;

class EventController extends Controller
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        parent::__construct();
        $this->eventRepository = $eventRepository;
    }

    public function index()
    {
        $this->render('front/event-list.html.twig');
    }

    public function search()
    {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;

        $results = $this->eventRepository->searchEvents($keyword, $page, $limit);
        $totalCount = $this->eventRepository->searchCount($keyword);
        $totalPages = ceil($totalCount / $limit);

        $this->render('front/event-list.html.twig', [
            'events' => $results,
            'keyword' => $keyword,
            'current_page' => $page,
            'total_pages' => $totalPages,
        ]);
    }
}
