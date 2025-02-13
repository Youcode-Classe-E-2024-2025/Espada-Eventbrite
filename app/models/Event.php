<?php

namespace App\models;

use App\core\Model;

class Event extends Model
{
    protected $table = 'events';

    public $id;
    public $title;
    public $description;
    public $date;
    public $location;
    public $created_at;
    public const VALIDATED = 0;
    public const UNVALIDATED = 1;
    public const ARCHIVED = 2;
    public const UNARCHIVED = 3;

    public function __construct()
    {
        parent::__construct();
    }

    public function validateSearchKeyword($keyword)
    {
        $validator = new \App\core\Validator();
        return $validator->validate(['keyword' => $keyword], []);
    }
}
