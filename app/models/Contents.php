<?php
namespace App\Models;

use Phalcon\Mvc\MongoCollection;

class Contents extends MongoCollection
{
    // public $name;
    // public $type;
    // public $attr;

    
    public function getSource()
    {
        return 'contents';
    }
}