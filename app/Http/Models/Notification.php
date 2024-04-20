<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = "notification";
    protected $fillable = [
        'user_id','plant_id','fault_and_alarm_id','title','description','entry_date','schedule_date','read_status','read_date','app_sent_date','for_admin','notification_type','alarm_log_id','sent_status','is_msg_app','is_msg_sms','is_msg_email','user_notification','is_notification_required','message_error',
    ];
    public $timestamps = false;

    protected $casts = [
        'user_id'=>'string',
        'plant_id'=>'string',
        'fault_and_alarm_id'=>'string',
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
