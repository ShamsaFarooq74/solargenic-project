<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationManagement extends Model
{
    protected $table = "notification_management";
    protected $fillable = [
        'notify_by','sms','mail','mobile_app_title','mobile_app_description','send_sms','send_email','send_app_noti'];
    public $timestamps = false;


}
