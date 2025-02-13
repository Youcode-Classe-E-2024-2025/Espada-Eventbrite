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

            $p = $this->evenmentRepo->getClient(1);
            $data['p']= $p;


            return $data ;
            
       
    }

   
}
