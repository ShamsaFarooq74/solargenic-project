<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Plant;
use App\Http\Models\PlantDetail;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\Inverter;
use App\Http\Models\PlantUser;
use App\Http\Models\ProcessedPlantDetail;
use App\Http\Models\Notification;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\GenerationLog;
use App\Http\Models\MicrotechEnergyGenerationLog;
use App\Http\Models\MicrotechPowerGenerationLog;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\PlantType;
use App\Http\Models\PlantSite;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\SystemType;
use App\Http\Models\Setting;
use App\Http\Models\Weather;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\AccumulativeProcessedDetail;
use App\Http\Models\TotalProcessedPlantDetail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\NotificationController;
use Carbon\Carbon;
use App\Http\Controllers\LEDController;
use App\Http\Controllers\Admin\CommunicationController;
use App\Http\Controllers\Admin\PlantsController;

class FaultAndAlarmController extends Controller
{
    public function alarmData() {

        $micro_itr = 0;
        date_default_timezone_set('Asia/Karachi');
        $time_difference_saltec = Date('Y-m-d H:i:s');
        $time_difference_micro = Date('Y-m-d H:i:s');
        $generation_log_created_time = Date('Y-m-d H:i:s');
        $current_time = Date('Ymd').'T'.Date('His');
        // $plant_id_value = Session::get('plant_idd');
        $plant_id_value = NULL;
        $token = '';
        $data = [
            'userName' => 'viper.bel',
            'password' => 'vdotb021',
            'lifeMinutes' => '240',
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://67.23.248.117:8089/api/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                'X-API-Version' => '1.0',
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "CURL Authentication Error 1 #:" . $err;
        }
        $res = json_decode($response);
        if($res){
            $token = $res->data;
        }
        // echo '<pre>';print_r($token);exit;

        if(isset($token) && $token){

            $plants = Plant::where('id', 48)->get();

            if($plants){
                foreach ($plants as $key => $plant) {

                    $plant_sites = PlantSite::where('plant_id', $plant->id)->get();

                    foreach($plant_sites as $key1 => $site) {

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://67.23.248.117:8089/api/site/live/".$site->site_id,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_HTTPHEADER => array(
                                // Set Here Your Requesred Headers
                                'Content-Type: application/json',
                                'X-API-Version: 1.0',
                                'Authorization: Bearer ' . $token,
                            ),
                        ));
                        $response1 = curl_exec($curl);
                        $err = curl_error($curl);
                        curl_close($curl);

                        if ($err) {
                            echo "cURL Error 2 #:" . $err;
                        }
                        $plant_inverter_data = json_decode($response1);

                        if($plant_inverter_data && isset($plant_inverter_data->data)) {
                            $plant_inverter_final_data = $plant_inverter_data->data;
                        }

                        if(isset($plant_inverter_final_data) && $plant_inverter_final_data) {

                            $timestm = $plant_inverter_final_data->lastUpdated;
                            $time_difference_saltec = $timestm;

                            date_default_timezone_set("Asia/Karachi");

                            if(isset($plant_inverter_final_data->numberOfInverters) && $plant_inverter_final_data->numberOfInverters != null && (int)$plant_inverter_final_data->numberOfInverters != 0) {

                                for($i = 1; $i <= (int)$plant_inverter_final_data->numberOfInverters; $i++) {

                                    $inverters_data = (array)$plant_inverter_final_data;
                                    $user_ids = PlantUser::where('plant_id',$plant->id)->get();

                                    if(isset($inverters_data['dv_Inv'.$i.'OutputType']) && $inverters_data['dv_Inv'.$i.'OutputType'] != null) {

                                        if($inverters_data['dv_Inv'.$i.'OutputType'] > 2){

                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'OutputType'])->where('api_param','dv_InvOutputType')->first();
                                            if($fault_data) {

                                                $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'dv_Inv'.$i.'OutputType',$user_ids);
                                            }
                                        }else{
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'OutputType'])->where('api_param','dv_InvOutputType')->first();
                                            if($fault_data) {

                                                $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                            }
                                        }
                                    }

                                    if(isset($inverters_data['dv_Inv'.$i.'FaultCode']) ) {

                                        if($inverters_data['dv_Inv'.$i.'FaultCode'] > 0){

                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'FaultCode'])->where('api_param','dv_InvFaultCode')->first();
                                            if($fault_data) {

                                                $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'dv_Inv'.$i.'FaultCode',$user_ids);
                                            }
                                        }else{
                                            $fault_data = FaultAndAlarm::where('alarm_code',(int)$inverters_data['dv_Inv'.$i.'FaultCode'])->where('api_param','dv_InvFaultCode')->first();

                                            if($fault_data) {

                                                $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                            }
                                        }
                                    }

                                    if(isset($inverters_data['dv_Inv'.$i.'PIDAlarmCode']) && $inverters_data['dv_Inv'.$i.'PIDAlarmCode'] != null) {

                                        if($inverters_data['dv_Inv'.$i.'PIDAlarmCode'] > 0){
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'PIDAlarmCode'])->where('api_param','dv_InvPIDAlarmCode')->first();
                                            if($fault_data) {

                                                $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'dv_Inv'.$i.'PIDAlarmCode',$user_ids);
                                            }
                                        }else{
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'PIDAlarmCode'])->where('api_param','dv_InvPIDAlarmCode')->first();
                                            if($fault_data) {

                                                $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                            }
                                        }
                                    }

                                    if(isset($inverters_data['dv_Inv'.$i.'PIDWorkState']) && $inverters_data['dv_Inv'.$i.'PIDWorkState'] != null) {

                                        if($inverters_data['dv_Inv'.$i.'PIDWorkState'] > 0){
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'PIDWorkState'])->where('api_param','dv_InvPIDWorkState')->first();
                                            if($fault_data) {

                                                $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'dv_Inv'.$i.'PIDWorkState',$user_ids);
                                            }
                                        }else{
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'PIDWorkState'])->where('api_param','dv_InvPIDWorkState')->first();
                                            if($fault_data) {

                                                $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                            }
                                        }
                                    }

                                    if(isset($inverters_data['dv_Inv'.$i.'WorkState1']) && $inverters_data['dv_Inv'.$i.'WorkState1'] != null) {

                                        if($inverters_data['dv_Inv'.$i.'WorkState1'] > 0){
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'WorkState1'])->where('api_param','dv_InvWorkState1')->first();
                                            if($fault_data) {

                                                $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'dv_Inv'.$i.'WorkState1',$user_ids);
                                            }
                                        }else{
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'WorkState1'])->where('api_param','dv_InvWorkState1')->first();
                                            if($fault_data) {

                                                $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                            }
                                        }
                                    }

                                    if(isset($inverters_data['inverter'.$i.'CommFail']) && $inverters_data['inverter'.$i.'CommFail'] != null) {

                                        if($inverters_data['inverter'.$i.'CommFail'] > 0){
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['inverter'.$i.'CommFail'])->where('api_param','inverterCommFail')->first();
                                            if($fault_data) {

                                                $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'inverter'.$i.'CommFail',$user_ids);
                                            }
                                        }else{
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['inverter'.$i.'CommFail'])->where('api_param','inverterCommFail')->first();
                                            if($fault_data) {

                                                $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                            }
                                        }
                                    }

                                    /*if(isset($inverters_data['inverterConfigFail']) && $inverters_data['inverterConfigFail'] != null) {

                                        if($inverters_data['inverterConfigFail'] > 0){
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['inverterConfigFail'])->where('api_param','inverterConfigFail')->first();
                                            if($fault_data) {

                                                $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'inverterConfigFail',$user_ids);
                                            }
                                        }else{
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['inverterConfigFail'])->where('api_param','inverterConfigFail')->first();
                                            if($fault_data) {

                                                $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                            }
                                        }
                                    }*/
                                }

                                if($plant->system_type != 1){

                                    if(isset($inverters_data['exportLimitEnabled']) && $inverters_data['exportLimitEnabled'] != null) {

                                        if($inverters_data['exportLimitEnabled'] > 0){
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['exportLimitEnabled'])->where('api_param','exportLimitEnabled')->first();
                                            if($fault_data) {

                                                $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'exportLimitEnabled',$user_ids);
                                            }
                                        }else{
                                            $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['exportLimitEnabled'])->where('api_param','exportLimitEnabled')->first();
                                            if($fault_data) {

                                                $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                            }
                                        }
                                    }

                                    /*if($plant->meter_type == 'Saltec'){

                                        if(isset($inverters_data['meterCommFail']) && $inverters_data['meterCommFail'] != null) {

                                            if($inverters_data['meterCommFail'] > 0){
                                                $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['meterCommFail'])->where('api_param','meterCommFail')->first();
                                                if($fault_data) {

                                                    $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'meterCommFail',$user_ids);
                                                }
                                            }else{
                                                $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['meterCommFail'])->where('api_param','meterCommFail')->first();
                                                if($fault_data) {

                                                    $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                                }
                                            }
                                        }
                                    }*/
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    public function faults_data_insertion($curr_Date,$dv_inv, $plant_id, $inverters_data, $fault_data,$string,$users){
        $curr_Date = date('Y-m-d H:i:s');
        return 'HELL';
        $existing_data = DB::table('fault_and_alarms')
                            ->join('fault_alarm_log', 'fault_and_alarms.id','fault_alarm_log.fault_and_alarm_id')
                            ->select('fault_alarm_log.plant_id', 'fault_alarm_log.fault_and_alarm_id',
                                    'fault_alarm_log.siteId', 'fault_alarm_log.status', 'fault_alarm_log.created_at',
                                    'fault_and_alarms.id', 'fault_and_alarms.alarm_code')
                            ->where(['fault_alarm_log.plant_id'=>$plant_id,'fault_alarm_log.fault_and_alarm_id' => $fault_data['id'],'fault_and_alarms.alarm_code' => $fault_data['alarm_code'], 'fault_alarm_log.status' => 'Y'])
                            ->orderBy('fault_alarm_log.created_at', 'DESC')
                            ->first();

        return $existing_data;

        if($existing_data == null){
            $fault_log['plant_id'] = $plant_id;
            $fault_log['siteId'] = $inverters_data['siteId'];
            $fault_log['dv_inverter'] = $dv_inv;
            $fault_log['fault_and_alarm_id'] = $fault_data['id'];
            $fault_log['status'] = $inverters_data[$string] > 0 ? 'Y' : 'N';
            $fault_log['lastUpdated'] = $inverters_data['lastUpdated'];
            $fault_log['created_at'] = $curr_Date;
            $fault_log['updated_at'] = NUll;
            // dd($fault_log);
            $fault_log_responce = FaultAlarmLog::create($fault_log);
            //dd($fault_log_responce);

            if($users && $fault_data['type'] != 'Status'){
                foreach ($users as $key => $user) {
                    $notification['plant_id'] = $plant_id;
                    $notification['user_id'] = $user['user_id'];
                    $notification['fault_and_alarm_id'] = $fault_data['id'];
                    $notification['title'] = $fault_data['type'];
                    $notification['description'] = $fault_data['description'];
                    $notification['entry_date'] = $curr_Date;
                    $notification['schedule_date'] = $curr_Date;
                    $notification['notification_type'] = $fault_data['severity'];
                    $notification['alarm_log_id'] = $fault_log_responce->id;
                    $notification['is_msg_app'] = 'Y';
                    $notification['is_msg_sms'] = 'N';
                    $notification['is_msg_email'] = 'N';
                    $notification['is_notification_required'] = 'N';
                    //dd($notification);
                    $notification_responce = Notification::create($notification);
                }
            }
        }
    }

    public function faults_data_updation($curr_Date,$plant_id, $fault_data){
        $curr_Date = date('Y-m-d H:i:s');

        $fault_obj = DB::table('fault_alarm_log')
                            ->join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
                            ->select('fault_alarm_log.id as log_id','fault_alarm_log.plant_id', 'fault_alarm_log.fault_and_alarm_id',
                                    'fault_alarm_log.siteId', 'fault_alarm_log.dv_inverter', 'fault_alarm_log.status', 'fault_alarm_log.created_at',
                                    'fault_and_alarms.id', 'fault_and_alarms.alarm_code', 'fault_and_alarms.api_param')
                            ->where(['fault_alarm_log.plant_id'=>$plant_id,'fault_and_alarms.api_param' => $fault_data['api_param'], 'fault_alarm_log.status' => 'Y'])
                            ->orderBy('fault_alarm_log.created_at', 'ASC')
                            ->first();
                            return $fault_obj;

        if($existing_data != null)
        {
            $fault_obj = FaultAlarmLog::findOrFail($existing_data->log_id);

            $fault_obj->plant_id = $existing_data->plant_id;
            $fault_obj->siteId = $existing_data->siteId;
            $fault_obj->dv_inverter = $existing_data->dv_inverter;
            $fault_obj->fault_and_alarm_id = $existing_data->fault_and_alarm_id;
            $fault_obj->status = 'N';
            $fault_obj->created_at = $existing_data->created_at;
            $fault_obj->updated_at = $curr_Date;

            $fault_obj->save();

            dd($fault_obj);
        }
        return true;
    }
}
