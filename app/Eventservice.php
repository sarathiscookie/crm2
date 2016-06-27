<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Eventservice extends Model
{
    protected $guarded   = ['id'];
    protected $table     = 'event_services';
    public $timestamps   = false;
}
