<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AllPlantsCumulativeDataHistory extends Model
{
    protected $table = "dashboard_graph_data_history";
    protected $fillable = [
        'id','total_energy','total_saving','collect_time' ];

}
