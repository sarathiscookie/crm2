<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customervehicle extends Model
{
    protected $table = 'customer_vehicles';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['customer_id', 'vehicle_id'];

}
