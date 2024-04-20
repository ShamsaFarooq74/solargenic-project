<?php

namespace App\Http\Controllers\Admin;

use DB;
use Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Plant;
use App\Http\Models\Company;
use App\Http\Models\User;
use App\Http\Models\UserDevice;
use App\Http\Models\PlantUser;
use App\Http\Models\Notification;
use App\Http\Models\Setting;
use App\Http\Models\NotificationEmail;
use App\Http\Models\NotificationSMS;
use Illuminate\Support\Facades\Validator;

class CommunicationController extends Controller

{
    public function index() {

        if(Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            return redirect()->back()->with('error', 'You have no rights of that section!');
        }
        else if(Auth::user()->roles == 3 || Auth::user()->roles == 4) {

            $company = Company::where('id', Auth::user()->company_id)->get();
            $plants = Plant::where('company_id', Auth::user()->company_id)->get(['id', 'plant_name', 'company_id']);
        }
        else if(Auth::user()->roles == 1 || Auth::user()->roles == 2) {

            $company = Company::get();
            $companyArray = Company::pluck('id')->toArray();
            $plants = Plant::get(['id', 'plant_name', 'company_id']);
        }

        return view('admin.communication', ['company' => $company, 'plants' => $plants, 'companyArray' => $companyArray]);
    }

    public function storeEmail(Request $request) {

        $compaign_entry_no = NotificationEmail::max('campaign_entry');
        $compaign_entry_no = $compaign_entry_no + 1;

        $plant_arr = $request->plant_name;
        $company_arr = $request->company;

        if(!empty($company_arr)) {

            if(!empty($plant_arr)) {

                $plant_user = PlantUser::whereIn('plant_id', (array)$plant_arr)->pluck('user_id')->toArray();

            }

            else {

                $plantArr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
                $plant_user = PlantUser::whereIn('plant_id', $plantArr)->pluck('user_id')->toArray();
            }

            $user_emails = User::whereIn('id', $plant_user)->get(['id','email']);

            if($user_emails) {

                foreach ($user_emails as $key => $usr_email) {

                    $noti_email = new NotificationEmail();
                    $noti_email->user_id = $usr_email->id;
                    $noti_email->to_email = $usr_email->email;
                    $noti_email->email_subject = $request->email_subject;
                    $noti_email->email_body = $request->email_body;

                    if($request->email_option_schedule == 'email_schedule') {

                        $noti_email->schedule_date = date('Y-m-d H:i:s', (strtotime(($request->schedule_date).' '.$request->schedule_time) - 18000));
                    }
                    else {

                        $noti_email->schedule_date = date('Y-m-d H:i:s');
                    }

                    $noti_email->email_sent_status = 'N';
                    $noti_email->campaign_entry = $compaign_entry_no;

                    $noti_email->save();
                }
            }
        }

        if($request->additional_email != '') {

            foreach(explode(',', $request->additional_email) as $key => $email) {

                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {

                    $noti_email = new NotificationEmail();
                    $noti_email->to_email = $email;
                    $noti_email->email_subject = $request->email_subject;
                    $noti_email->email_body = $request->email_body;

                    if($request->email_option_schedule == 'email_schedule') {

                        $noti_email->schedule_date = $request->schedule_date.' '.$request->schedule_time.':00';
                    }
                    else {

                        $noti_email->schedule_date = date('Y-m-d H:i:s');
                    }

                    $noti_email->email_sent_status = 'N';
                    $noti_email->campaign_entry = $compaign_entry_no;

                    $noti_email->save();
                }
            }
        }

        $this->send_comm_email();

        return redirect()->back()->with('success', 'Email sent successfully!');
    }

    public function storeSMS(Request $request) {

        $compaign_entry_no = NotificationSMS::max('campaign_entry');
        $compaign_entry_no = $compaign_entry_no + 1;

        $plant_arr = $request->plant_name;
        $company_arr = $request->company;

        if(!empty($company_arr)) {

            if(!empty($plant_arr)) {

                $plant_user = Plant::whereIn('id', (array)$plant_arr)->pluck('phone')->toArray();
            }

            else {

                $plantArr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
                $plant_user = Plant::whereIn('id', $plantArr)->pluck('phone')->toArray();
            }


            if($plant_user) {

                foreach ($plant_user as $key => $usr_phone) {

                    if($usr_phone != null) {

                        $noti_sms = new NotificationSMS();
//                        $noti_sms->user_id = $usr_phone->id;
                        $noti_sms->phone_number = $usr_phone;
                        $noti_sms->sms_body = $request->sms_body;

                        if($request->sms_option_schedule == 'sms_schedule') {

                            $noti_sms->sms_schedule_date = $request->schedule_date.' '.$request->schedule_time.':00';
                        }
                        else {

                            $noti_sms->sms_schedule_date = date('Y-m-d H:i:s');
                        }

                        $noti_sms->sms_sent_status = 'N';
                        $noti_sms->campaign_entry = $compaign_entry_no;

                        $noti_sms->save();
                    }
                }
            }
        }

        if($request->additional_phone != '') {

            foreach(explode(',', $request->additional_phone) as $key => $phone) {

                if(preg_match("/^[0-9]{11}$/", $phone)) {

                    $noti_sms = new NotificationSMS();
                    $noti_sms->phone_number = $phone;
                    $noti_sms->sms_body = $request->sms_body;

                    if($request->sms_option_schedule == 'sms_schedule') {

                        $noti_sms->sms_schedule_date = $request->schedule_date.' '.$request->schedule_time.':00';
                    }
                    else {

                        $noti_sms->sms_schedule_date = date('Y-m-d H:i:s');
                    }

                    $noti_sms->sms_sent_status = 'N';
                    $noti_sms->campaign_entry = $compaign_entry_no;

                    $noti_sms->save();
                }
            }
        }

        $this->send_comm_sms();

        return redirect()->back()->with('success', 'SMS sent successfully!');
    }

    public function storeAppNotification(Request $request) {

        $compaign_entry_no = Notification::max('campaign_entry');
        $compaign_entry_no = $compaign_entry_no + 1;

        $plant_arr = $request->plant_name;
        $company_arr = $request->company;
        $devices_arr = $request->devices;
        $device = '';

        $target = array('iOS', 'android');

        if(count(array_intersect($devices_arr, $target)) == count($target)){

            $device = 'all';
        }

        else {

            $device = $devices_arr[0];
        }

        if(!empty($company_arr)) {

            if(!empty($plant_arr)) {

                $plant_user = PlantUser::whereIn('plant_id', (array)$plant_arr)->get();
            }

            else {

                $plantArr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
                $plant_user = PlantUser::whereIn('plant_id', $plantArr)->get();
            }

            if($plant_user) {

                foreach ($plant_user as $key => $usr) {

                    $noti_app = new Notification();
                    $noti_app->user_id = $usr->user_id;
                    $noti_app->plant_id = $usr->plant_id;
                    $noti_app->description = $request->notification_body;
                    $noti_app->title = $request->notification_title;
                    $noti_app->notification_type = 'Custom';
                    $noti_app->device_type = $device;

                    if($request->noti_option_schedule == 'noti_schedule') {

                        $noti_app->schedule_date = $request->schedule_date.' '.$request->schedule_time.':00';
                    }
                    else {

                        $noti_app->schedule_date = date('Y-m-d H:i:s');
                    }

                    $noti_app->sent_status = 'N';
                    $noti_app->is_msg_app = 'Y';
                    $noti_app->is_notification_required = 'Y';
                    $noti_app->campaign_entry = $compaign_entry_no;

                    $noti_app->save();
                }
            }


        }

        $this->send_comm_app_notification();

        return redirect()->back()->with('success', 'Notification sent successfully!');
    }

    public function send_comm_email()
    {
        $number_email = 100;

        $currentDate = date('Y-m-d H:i:s');

        $setting_comm_email = Setting::where('perimeter', 'communication_email')->first();
        $setting_comm_email_name = Setting::where('perimeter', 'communication_email_name')->first();

        $noti = NotificationEmail::where('email_sent_status', 'N')->where('schedule_date','<=', $currentDate)->orderBy('schedule_date', 'DESC')->limit($number_email)->get();

        if(count($noti) > 0)
        {
            foreach ($noti as $key => $element)
            {
                $to_email = $element->to_email;
                $email_subject = $element->email_subject;
                $email_body =  $element->email_body;
                $cc_email =  $element->cc_email;
                $bcc_email =  $element->bcc_email;

                Mail::send([], [], function ($message) use ($setting_comm_email, $setting_comm_email_name,
                                                 $to_email, $email_subject, $email_body, $cc_email, $bcc_email) {
                    $message->from($setting_comm_email->value, $setting_comm_email_name->value)
                    ->to($to_email)
                    ->subject($email_subject ? $email_subject : '')
                    ->setBody($email_body, 'text/html');
                });

                if(count(Mail::failures()) > 0) {

                    $mail_err = '';

                    foreach(Mail::failures as $email_address) {
                        $mail_err .= $email_address.'=>';
                    }

                    NotificationEmail::where('id', $element->id)->update(array('response' =>  $mail_err));
                }
                else {

                    NotificationEmail::where('id', $element->id)->update(array('from_email' => $setting_comm_email->value, 'from_name' => $setting_comm_email_name->value,'response' =>  'sent successfully!', 'email_sent_status' => 'Y', 'sent_date'=>$currentDate));
                }
            }

            //return redirect()->back()->with('success', 'Emails send successfully!');
        }
        /*else
        {
            return redirect()->back()->with('error', 'No emails to send!');
        }*/

    }

    public function send_comm_sms()
    {
        $number_sms = 10;

        $currentDate = date('Y-m-d H:i:s');

        $setting_comm_sms_username = Setting::where('perimeter', 'communication_sms_username')->first();
        $setting_comm_sms_password = Setting::where('perimeter', 'communication_sms_password')->first();
        $setting_comm_sms_sender_id = Setting::where('perimeter', 'communication_sms_sender_id')->first();

        $noti = NotificationSMS::where('sms_sent_status', 'N')->where('sms_schedule_date','<=', $currentDate)->orderBy('sms_schedule_date', 'DESC')->limit($number_sms)->get();

        if(count($noti) > 0)
        {
            foreach ($noti as $key => $element)
            {
                $username = $setting_comm_sms_username->value;
                $password = $setting_comm_sms_password->value;
                $mobile = $element->phone_number;
                $sender = $setting_comm_sms_sender_id->value;
                $message = $element->sms_body;

                ////sending sms

                $post = "user=".$username."&pwd=".$password."&sender=".urlencode($sender)."&reciever=".urlencode($mobile)."&msg-data=".urlencode($message)."&response=string";
                $url = "https://pk.eocean.us/API/RequestAPI?".$post;
                $ch = curl_init();
                $timeout = 10; // set to zero for no timeout
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $result = curl_exec($ch);

                if (strpos($result, 'Message Id') === false) {

                    NotificationSMS::where('id', $element->id)->update(array('response' =>  $result));
                }
                else {

                    NotificationSMS::where('id', $element->id)->update(array('from_phone_number' => $setting_comm_sms_sender_id->value, 'response' =>  $result, 'sms_sent_status' => 'Y', 'sent_date'=>$currentDate));
                }
            }

            //return redirect()->back()->with('success', 'SMS send successfully!');
        }
        /*else
        {
            return redirect()->back()->with('error', 'No sms to send!');
        }*/

    }

    public function send_comm_app_notification()
    {
        $number_noti = 1000;
        /*api_key available in:
        Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/
        $server_key = 'AAAAuiFsHdw:APA91bEogsxFmmnTOZy20mLMJ3YpFhn62KcGqSSM1KdTd_TzH5gAbcbksDPwELOMn0yfdZkVW12h27JKHzyge0AfQUYpHyITOra68sAUv-_Cgdtq6BKHUvic7toRkFXGkRmRst9UiF9W';
        //API URL of FCM
        $url = 'https://fcm.googleapis.com/fcm/send';
        $currentDate = date('Y-m-d H:i:s');

        $noti =  Notification::where(('sent_status') ,'=', "N")->where("schedule_date","<=",$currentDate)->where("is_notification_required", "Y")->orderBy('schedule_date', 'DESC')->limit($number_noti)->get();

        if(count($noti) > 0)
        {
            foreach ($noti as $element)
            {
                #send App notification
                if (($element->is_msg_app) == 'Y') {
                    $title = $element->title;
                    $description =  $element->description;
                    $user_id = $element->user_id;
                    if($element->device_type == 'all'){

                        $q = UserDevice::where('user_id', $user_id)->where('status','=','A')->get();
                    }
                    else {

                        $q = UserDevice::where('user_id', $user_id)->where('status','=','A')->where('platform', $element->device_type)->get();
                    }
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
            //return response(['success' => 1, 'message' => 'Sending all notifications', 'result' =>true], 200);
            // return true;
        }
    }
}

