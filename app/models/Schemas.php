<?php
namespace App\Models;

use Phalcon\Mvc\MongoCollection;

class Schemas extends MongoCollection
{
    public $name;

    
    public function getSource()
    {
        return 'schemas';
    }
}