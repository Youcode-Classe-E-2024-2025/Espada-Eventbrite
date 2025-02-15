<?php

namespace App\services;

use App\repository\EvenmentRepository;


class StatService {
    private EvenmentRepository $evenmentRepo;

    public function __construct() {
        $this->evenmentRepo = new EvenmentRepository();
    }
    public function getOwnerStatistic($id) {


        $data = [];
            
            $res=$this->evenmentRepo->getOwnerStatistics($id);
            $data['stat']= $res;
            $events = $this->evenmentRepo->getEventsForOwner($id);
            $data['events']= $events;


            return $data ;
            
       
    }


    // ticketsStaT

    public function ticketsStaTs($id) {


        $data = [];
            
          
            $events = $this->evenmentRepo->ticketsStaT($id);
            $data['tickts']= $events[0];

            $p = $this->evenmentRepo->getClient($id);
            $data['p']= $p;

            $res = $this->evenmentRepo->getUserEvents($id);
            $data['eve']= $res;
            


            return $data ;
            
       
    }
    public function getET($id , $even){


       

        $res = $this->evenmentRepo->getEventsales($id , $even);

         return $res ;

    }

    

    public function deleteE($eventId){
        
        $res = $this->evenmentRepo->delete($eventId);

         return $res ;
       
    }

   
}
