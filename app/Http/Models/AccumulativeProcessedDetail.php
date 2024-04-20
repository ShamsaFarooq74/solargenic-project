<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class AccumulativeProcessedDetail extends Model
{
    protected $table = "accumulative_processed_detail";

    protected $fillable = [
        'id','total_current_power','total_generation','total_reduction','created_at','updated_at'
    ];
}
