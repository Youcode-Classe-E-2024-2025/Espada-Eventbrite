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
    $response = file_get_contents('https://restcountries.com/v3.1/all');
    if($response === FALSE){
      die('error to fetch data');
    }
    $locations = json_decode($response,true);
    $events = $this->evenmentRepository->getAll();
    $data = [
      'events' => $events,
      'locations' => $locations
    ];
    echo $this->render('front/home.html.twig',$data);
  }
}
