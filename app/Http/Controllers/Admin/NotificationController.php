<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\User;
use App\Http\Models\Company;
use App\Http\Models\Plant;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\Notification;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;


class NotificationController extends Controller
{
    public function allNotifications(Request $request){

        $where_array = array();
        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $user_id = Auth::user()->id;
            $where_array['user_id'] =  $user_id;
        }

        $notifications = Notification::where($where_array)->where('sent_status', "Y")->get();
        // Session::put(['notifications'=> $notifications]);
    }



}
