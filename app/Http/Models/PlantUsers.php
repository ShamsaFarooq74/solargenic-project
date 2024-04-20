<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PlantUsers extends Model
{
    protected $fillable = ['user_id','plant_id','is_active'];
}
