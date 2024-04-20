<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    public $table = "weathers";
    protected $fillable = [
        'city','condition','temperature','temperature_min','temperature_max','sunrise','sunset','icon','created_at','updated_at'
    ];

}
