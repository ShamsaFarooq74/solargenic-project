<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FaultAndAlarm extends Model
{
    protected $table = "fault_and_alarms";

    protected $fillable = ['plant_meter_type', 'api_param', 'alarm_code', 'description', 'type', 'severity', 'category', 'sub_category', 'correction_action', 'alarm_source', 'proactive_complain'];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function fault_alarm_log()
    {
        return $this->hasOne(FaultAlarmLog::class,'fault_and_alarm_id');
    }

}
