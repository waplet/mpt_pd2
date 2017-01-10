<?php

namespace BigF\Models;

use Illuminate\Database\Capsule\Manager;

class Spele
{
    public $table = 'spele';

    public function __construct()
    {
        return Manager::table($this->table);
    }

    public static function prepare()
    {
        return new static;
    }
}