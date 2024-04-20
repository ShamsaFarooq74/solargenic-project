<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Loggers extends Model
{
    protected $fillable = [
        'plant_id','communication_method','max_devices','logger_ops_mode','heartbeat_freq','uploading_freq'
    ];
}
