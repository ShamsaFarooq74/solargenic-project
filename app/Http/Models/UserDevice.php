<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $table = "user_devices";
    protected $fillable = [
        'user_id','manufacturer','model','battery','last_active_time','app_version','platform',
        'serial','created_at','updated_at','uuid','version','token','status','created_at','updated_at'
    ];
}
