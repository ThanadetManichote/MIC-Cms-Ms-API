<?php
namespace App\Models;

use Phalcon\Mvc\MongoCollection;

class Users extends MongoCollection
{
    public $username;
    public $password;
    public $name;
    public $last_name;
    public $email;
    public $role_id;
    public $status;
    public $created_at;
    public $updated_at;
    
    public function getSource()
    {
        return 'users';
    }
}