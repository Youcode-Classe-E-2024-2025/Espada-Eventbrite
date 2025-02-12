<?php

namespace App\models;

use App\core\Model;
use App\core\Validator;

class User extends Model
{
    protected $table = 'users';
    public $id;
    public $role_id;
    public $username;
    public $email;
    public $password;
    public $avater;
    public $banned;
    public $archived;
    public const ADMIN = 1;
    public const ORGANIZER = 2;
    public const USER = 3;

    public const ACTIVE = 0;
    public const BANNED = 1;
    public const ARCHIVED = 2;

    public function __construct()
    {
        parent::__construct();
    }

    public function isAdmin()
    {
        return $this->role_id === self::ADMIN;
    }

    public function isOrganizer()
    {
        return $this->role_id === self::ORGANIZER;
    }

    public function isBanned()
    {
        return $this->banned === 1;
    }

    public function isArchived()
    {
        return $this->archived === 1;
    }

    public function validationRules()
    {
        return [
            'email' => ['required', 'email'],
            'username' => ['required', 'min:3'],
            'password' => ['required', 'min:6'],
        ];
    }

    public function validateUserData($data)
    {
        $validator = new Validator();
        return $validator->validate($data, $this->validationRules());
    }
}
