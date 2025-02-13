<?php

namespace app\controllers\front;

use App\core\Controller;
use App\services\EventService;
use App\repository\EvenmentRepository;
class HomeController extends controller
{
  private EventService $eventService;
  private EvenmentRepository $evenmentRepository; 
    public function __construct(){
        parent::__construct();
        $this->eventService = new EventService();
        $this->evenmentRepository = new EvenmentRepository();
    }
   public function index()
  {
    $events = $this->evenmentRepository->getAll();
    $data = [
      'events' => $events
    ];
    echo $this->render('front/home.html.twig',$data);
  }
}
