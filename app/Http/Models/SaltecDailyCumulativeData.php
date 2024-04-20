<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SaltecDailyCumulativeData extends Model
{
    protected $table = "saltec_daily_cumulative_plant_data";

    protected $fillable = ['plant_id','site_id','total_generation','total_consumption','total_grid','total_bought','total_sell','updated_at','created_at'];
}
