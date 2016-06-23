<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehiclehardware extends Model
{
    protected $table   ='vehicle_hardwares';
    protected $guarded = ['id'];
    public $timestamps = false;
}
