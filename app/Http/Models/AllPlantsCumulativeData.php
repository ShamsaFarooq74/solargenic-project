<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AllPlantsCumulativeData extends Model
{
    protected $table = "dashboard_graph_data";
    protected $fillable = [
        'id','total_energy','total_saving','collect_time' ];

}
