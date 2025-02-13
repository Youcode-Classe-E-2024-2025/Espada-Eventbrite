<?php
namespace App\controllers\front ; 
use App\core\Controller;
use App\core\View;
// use App\services\EventService;
use App\services\StatService;

class OrganiserDashController extends Controller
{

    protected EventService $evsdn;
    protected StatService $statServ;

    public function __construct(){
        parent::__construct();
        // $this->evsdn=new EventService();
        $this->statServ=new StatService();
        

        

    }


    public function index(): void
    {

       $data = $this->statServ->getOwnerStatistic(1)  ;

       


        echo $this->render('front/organiser/dashboard.twig',[ 'data' =>$data]);

        var_dump($data);
    }







    public function events(): void
    {
        echo $this->render('front/organiser/events.html.twig',[]);
    }


    public function tickets(): void
    {

        $data = $this->statServ->ticketsStaTs(1)  ;

        var_dump($data);
 
        echo $this->render('front/organiser/tickets.html.twig',[[ 'data' =>$data]]);
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

        // $this->evsdn->createEvent($evenmentData ,$capacityData , $tagIds);
        
    } 





}
