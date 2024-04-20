<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PlantSite extends Model
{
    protected $table = 'plant_sites';

    protected $fillable = [

        'plant_id','site_id','online_status','created_by','updated_by','updated_by_at','created_at','updated_at'

    ];
}
