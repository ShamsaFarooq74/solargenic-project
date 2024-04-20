<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FaultAlarmLog extends Model
{
    protected $table = 'fault_alarm_log';
    protected $fillable = [
        'plant_id','fault_and_alarm_id','siteId','dv_inverter','status','lastUpdated','created_at','updated_at'
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function fault_and_alarm()
    {
        return $this->belongsTo(FaultAndAlarm::class);
    }

}
