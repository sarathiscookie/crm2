<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Formfield extends Model
{
    protected $table ='form_fields';
    protected $guarded = ['id'];
}
