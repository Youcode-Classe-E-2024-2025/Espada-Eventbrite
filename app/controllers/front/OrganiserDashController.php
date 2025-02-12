<?php


namespace App\controllers\front ; 

use App\core\Controller;
use App\core\View;
use App\services\EventService;

class OrganiserDashController extends Controller{

    protected EventService $evsdn;

    public function __construct(){
        $this->evsdn=new EventService();

    }


    public function index(): void
    {
        $view = new View();
        echo $view->render('front/organiser/dashboard.twig',[]);
    }

    public function serviceTest(){
        $evenmentData = [
            'title' => 'Test',
            'description' => 'A conference about the latest trends in technology.',
            'visual_content' => 'tech_conference_image.jpg',
            'lieu' => 'New York City, USA',
            'owner_id' => 2,  
            'category_id' => 2,  
            'date' => '2025-05-20',
            'type' => 'public'
        ];
        
        $capacityData = [
            'total_tickets' => 1000,
            'vip_tickets_number' => 100,
            'vip_price' => 200.00,
            'standard_tickets_number' => 800,
            'standard_price' => 50.00,
            'gratuit_tickets_number' => 100,
            'early_bird_discount' => 10  
        ];
        $tagIds = [1, 2, 3]; 

        $this->evsdn->createEvent($evenmentData ,$capacityData , $tagIds);
        
    } 







}


