<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table ='vehicles';
    protected $guarded = ['id'];
    public $timestamps = false;
}
