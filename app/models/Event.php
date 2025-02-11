<?php

namespace app\models;

use app\core\Model;

class Event extends Model
{
    protected $table = 'events';

    public $id;
    public $title;
    public $description;
    public $date;
    public $location;
    public $created_at;

    public function __construct()
    {
        parent::__construct();
    }
}
