<?php
namespace App\Models;

use Phalcon\Mvc\MongoCollection;

class Types extends MongoCollection
{
    public $name;
    
    public function getSource()
    {
        return 'types';
    }
}