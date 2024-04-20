<?php


namespace App\Http\Controllers\Api;


use App\Http\Models\Notification;
use App\Http\Models\User;
use App\Http\Models\Message;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\Plant;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\UserDevice;
use App\Http\Models\Setting;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class NotificationController extends ResponseController
{
    public function __construct()
    {
        date_default_timezone_set("Asia/Karachi");

    }

    public function updateReadStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',

        ]);
        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $ID = $request->get('id');
        $company = Notification::find($ID);
        if($company) {
            $data = $company->update(["read_status" => "Y", "read_date" => date('Y-m-d')]);
            if ($data) {
                return $this->sendResponse(1, 'Read Status updated successfully', null);
            } else {
                return $this->sendError(0, "Something went wrong!", $data);
            }
        }
        else{
            return $this->sendError(0, "Invalid Notification ID", null);
        }
    }

    public function allNotifications(Request $request){

        $notifications = DB::table('notification')
                            ->leftjoin('fault_alarm_log', 'notification.alarm_log_id', 'fault_alarm_log.id')
                            ->leftjoin('fault_and_alarms', 'notification.fault_and_alarm_id', 'fault_and_alarms.id')
                            ->where('fault_alarm_log.fault_and_alarm_id','!=',173)
                            ->join('plants', 'plants.id', 'notification.plant_id')
                            ->select('notification.id', 'plants.plant_name', 'notification.user_id', 'notification.plant_id',
                                    'notification.fault_and_alarm_id', 'notification.ticket_id', 'notification.title', 'notification.description', 'notification.alarm_log_id',
                                    'notification.schedule_date', 'notification.notification_type', 'notification.read_status', 'fault_alarm_log.created_at as from', 'fault_alarm_log.updated_at as to',
                                    'fault_and_alarms.correction_action', 'fault_and_alarms.severity');

        if ($request->get('type') && $request->get('type') != 'all' && $request->get('type') != ''){
            $notifications->where('notification.title',$request->get('type') );
        }
        if ($request->get('plant_id') && $request->get('plant_id') != 'all' && $request->get('plant_id') != ''){
            $notifications->where('notification.plant_id', $request->get('plant_id') );
        }
        if ($request->get('time') && $request->get('time') != 'all' && $request->get('time') != ''){
            if($request->get('time') == 'Daily') {
                $notifications->where('notification.schedule_date', '=', date('Y-m-d H:i:s'));
            }
            if($request->get('time') == 'Monthly') {
                $notifications->whereMonth('notification.schedule_date', '=', date('m'));
            }
            if($request->get('time') == 'Yearly') {
                $notifications->whereYear('notification.schedule_date', '=', date('Y'));
            }
        }

        $notifications->where('notification.user_id', $request->user()->id)
                        ->where('notification.notification_type', '!=', 'Custom')
                        ->where(function($q) use($request) {
                            $q->Where('notification.is_notification_required', 'N')
                            ->orWhere(function($q1) {
                                $q1->where('notification.is_notification_required', "Y")
                                   ->where('notification.sent_status', "Y");
                            });
                        })
                        ->orderBy('notification.entry_date', 'DESC');

        $result = $notifications->paginate(50)->appends(request()->except('page'));

        foreach($result as $key => $noti) {

            $noti->ticket_id = $noti->ticket_id == null ? 0 : $noti->ticket_id;
            $noti->fault_and_alarm_id = $noti->fault_and_alarm_id == null ? 0 : $noti->fault_and_alarm_id;
        }

        if(count($result->getCollection()) > 0){

            foreach($result->getCollection() as $key => $value) {

                $value->id = (int)$value->id;
                $value->from = date('h:i A, d/m', strtotime($value->from));

                if($value->to != null && $value->to != '') {
                    $value->to = date('h:i A, d/m', strtotime($value->to));
                }
                else {
                    $value->to = 'Continue';
                }

            }
        }

        if(count($result) > 0){
            return $this->sendResponse(1, 'Showing all notifications',$result);
        }

        return $this->sendResponse(1, 'No notifications found',$result);
    }


    public function getNotificationDetail(Request $request){

        $notification = Notification::where('id', $request->get('notification_id'))->get();
        $notification[0]->alert_detail  = (FaultAndAlarm::where('id',$notification[0]->fault_and_alarm_id)->get())[0]->description;
        $notification[0]->suggestion  = (FaultAndAlarm::where('id',$notification[0]->fault_and_alarm_id)->get())[0]->correction_action;
        $notification[0]->plant_name = (Plant::where('id',$notification[0]->plant_id)->get())[0]->plant_name;
        if($notification) {
            $ID = $request->get('notification_id');
            $company = Notification::find($ID);
            if ($company) {
                $data = $company->update(["read_status" => "Y", "read_date" => date('Y-m-d')]);
            }
        }
        if(count($notification) > 0){
            return $this->sendResponse(1, 'Notification detail',$notification);
        }

        return $this->sendResponse(1, 'No notifications found',$notification);
    }


    public function push_notification()
    {
        return "ok";
        /*api_key available in:
        Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/
        $server_key = 'AAAAuiFsHdw:APA91bEogsxFmmnTOZy20mLMJ3YpFhn62KcGqSSM1KdTd_TzH5gAbcbksDPwELOMn0yfdZkVW12h27JKHzyge0AfQUYpHyITOra68sAUv-_Cgdtq6BKHUvic7toRkFXGkRmRst9UiF9W';
        //API URL of FCM
        $url = 'https://fcm.googleapis.com/fcm/send';
        $currentDate = date('Y-m-d H:i:s');

        $noti =  DB::table('notification')
            ->where(('sent_status') ,'=', "N")
            ->where("schedule_date","<=",$currentDate)
            ->get();

        if(count($noti) > 0)
        {
            foreach ($noti as $element)
            {
                #send App notification
                if (($element->is_msg_app) == 'Y') {
                    $title = $element->title;
                    $description =  $element->description;
                    $user_id = $element->user_id;
                    $q = UserDevice::where('user_id', $user_id)->where('status','=','A')->get();
                    if (count($q) > 0)
                    {
                        foreach ($q as $row)
                        {
                            // dd($row);
                            $key = $row->token;
                            $headers = array(
                                'Authorization:key=' . $server_key,
                                'Content-Type:application/json'
                            );
                            $fields = array(
                                'to' => $key,
                                'notification' => array('title' => $title, 'body' => $description, 'sound' => 1, 'vibrate' => 1),
                                'data' => array('type' => $element->notification_type, 'title' => $title, 'body' => $description)
                            );

                            $payload = json_encode($fields);
                            $curl_session = curl_init();
                            curl_setopt($curl_session, CURLOPT_URL, $url);
                            curl_setopt($curl_session, CURLOPT_POST, true);
                            curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                            curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
                            $curlResult = curl_exec($curl_session);
                            $res = json_decode($curlResult, true);
                            // dd($res);
                            if ($res['failure'])
                            {
                                $array = $res['results'];
                                $error = $array[0]['error'];
                                DB::table('notification')
                                ->where('id', '=', $element->id)
                                ->update(array('message_error' =>  $error));
                            } else {
                                DB::table('notification')
                                ->where('id', '=', $element->id)
                                ->update(array('message_error' =>  '','sent_status'=>'Y','app_sent_date'=>$currentDate));
                            }
                        }
                    }
                }
            }
            return response(['success' => 1, 'message' => 'Sending all notifications', 'result' =>true], 200);
            // return true;
        }
        else
        {
            return response(['success' => 0, 'message' => 'Notifications not send', 'result' => false], 200);
            // return false;
        }
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|',
            'description' => 'string|'
        ]);

        if($validator->fails()){
            return $this->sendError(0,"Something went wrong! Please try again", $validator->errors()->all());
        }

        $userID = $request->user()->id;
        $user = User::find($userID);
        if(!($request->has('platform'))) {
            $request->request->add(['platform' => 'Mobile']);
        }
        $request->request->add(['user_id' => $userID]);
        $user_msg = Message::create($request->all());

        if($user_msg){

            return $this->sendMessageEmail($user_msg,$user, $request->subject, $request->description);
        }
        else{
            $error = "Something went wrong! Please try again";
            return $this->sendError(0,$error,null ,401);
        }
    }

    public function sendMessageEmail($user_msg, $user, $title, $description) {

        if($user){

            $user_plant = DB::table('plant_user')
                            ->join('plants', 'plant_user.plant_id', 'plants.id')
                            ->select('plants.id', 'plants.plant_name', 'plant_user.user_id', 'plant_user.plant_id')
                            ->where('plant_user.user_id', $user->id)
                            ->get();

            $plant_string = '';

            foreach($user_plant as $key => $plant) {

                $plant_string .= $plant->plant_name.',';
            }

            $userName = $user->name;
            $plantName = $plant_string;
            $platform = $user_msg->platform;
            $title = $title;
            $description = $description;

            $toEmail = Setting::where('perimeter', 'contact_noc_email')->first();

            \Mail::to($toEmail->value)
                    ->bcc('shehzad@viiontech.com')
                    ->send(new \App\Mail\ContactNOC($userName, $plantName, $platform, $title, $description));
//            \Mail::to($user->email)
//                    ->send(new \App\Mail\ContactThanks());

            //\Mail::to('zaman.javed@viiontech.com')->send(new \App\Mail\ContactNOC($userName, $plantName, $platform, $title, $description));

            $message="Message sent successfully";
            return $this->sendResponse(1,$message,null);
       }
    }

    public function sendNotificationIOS(Request $request)
    {
        $data = [
            "registration_ids" => [$request->device_token],
            'notification' => [
                "title" => "A new trailer has arrived for you",
                "body" => "Fast and Furious F9 Official Trailer",
                "sound"=> "bingbong.aiff",
            ],
            'priority'=>'high'
        ];

        $headers = [
            'Authorization: key=' . $request->server_key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return json_encode($data) . curl_exec($ch);
    }

}
