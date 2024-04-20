<?php

namespace App\Http\Controllers\HardwareAPIData;

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
use App\Http\Models\DailyProcessedPlantEMIDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\MonthlyProcessedPlantEMIDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\YearlyProcessedPlantEMIDetail;
use App\Http\Models\TotalProcessedPlantEMIDetail;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\InverterEnergyLog;
use App\Http\Models\InverterEMIDetail;
use App\Http\Models\InverterGridMeterDetail;
use App\Http\Models\SiteInverterDetail;
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
use App\Http\Models\InverterStateDescription;
use App\Http\Models\InverterMPPTDetail;
use App\Http\Models\TotalProcessedPlantDetail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\HardwareAPIData\TestingController;
use Carbon\Carbon;
use App\Http\Controllers\LEDController;
use App\Http\Controllers\Admin\CommunicationController;
use App\Http\Controllers\Admin\PlantsController;


class SungrowFaultAndAlarmController extends Controller
{
    public function AlarmAndFault($appKey,$token,$ps_id,$accessKey,$Plant_ID)
    {
        date_default_timezone_set('Asia/Karachi');
        $siteDetails = [
            "appkey" => $appKey,
            "token" => $token,
            "ps_id" => $ps_id,
            "curPage" => "1",
            "size" => "5000"
        ];
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/getFaultAlarmInfo',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($siteDetails),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'sys_code: 901',
            'lang: _en_US',
            'x-access-key: ' . $accessKey,
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        return $response;
        $plantSiteAlarmResponseData = json_decode($response);
        // return $plantSiteAlarmResponseData;
    //    return 'okkkkkkkkkkk' + json_encode($plantSiteAlarmResponseData);
    if($plantSiteAlarmResponseData && isset($plantSiteAlarmResponseData->result_data)) {

        $plantSiteAlarmFinalData = $plantSiteAlarmResponseData->result_data;
            // $plantSiteAlarmFinalData = isset($plantSiteAlarmResponseData->result_data) ? $plantSiteAlarmResponseData->result_data : ["abcd"];
           if($plantSiteAlarmFinalData && isset($plantSiteAlarmFinalData->pageList)){

            $plantSiteAlarmData=$plantSiteAlarmFinalData->pageList;

                foreach($plantSiteAlarmData as $key5 => $finalData1) {

                    $alarmLevelString = '';
                    $alarmStatusString = '';

                    if($finalData1->fault_type == 1) {

                        $alarmLevelString = 'Critical';

                    }
                    else if($finalData1->fault_type == 2) {

                        $alarmLevelString = 'Major';
                    }
                    else if($finalData1->fault_type == 3) {

                        $alarmLevelString = 'Minor';
                    }
                    else if($finalData1->fault_type == 4) {

                        $alarmLevelString = 'Warning';
                    }

                    if($finalData1->fault_type == 1 || $finalData1->fault_type == 2 || $finalData1->fault_type == 3 || $finalData1->fault_type == 4) {

                        $alarmStatusString = 'Y';
                    }
                    else {

                        $alarmStatusString = 'N';
                    }

                    //ALARM AND FAULT DATA
                    if($alarmLevelString == 'Critical'){
                        $alarmData = FaultAndAlarm::updateOrCreate(
                            [ 'plant_meter_type' => 'SunGrow', 'alarm_code' => $finalData1->fault_code, 'severity' => $alarmLevelString],
                            ['description' => $finalData1->fault_name, 'correction_action' => 'N/A', 'type' => 'Fault', 'category' => 'Hardware', 'sub_category' => $finalData1->type_name, 'alarm_source' => 'Inveter #', 'proactive_complain' => 'No']
                        );
                    }
                    else{

                        $alarmData = FaultAndAlarm::updateOrCreate(
                            [ 'plant_meter_type' => 'SunGrow', 'alarm_code' => $finalData1->fault_code, 'severity' => $alarmLevelString],
                            ['description' => $finalData1->fault_name, 'correction_action' => 'N/A', 'type' => 'Alarm', 'category' => 'Hardware', 'sub_category' => $finalData1->type_name, 'alarm_source' => 'Inveter #', 'proactive_complain' => 'No']
                        );
                    }
                    $faultAndAlarmID = FaultAndAlarm::where(['plant_meter_type' => 'SunGrow', 'alarm_code' => $finalData1->fault_code, 'severity' => $alarmLevelString])->exists() ? FaultAndAlarm::where(['plant_meter_type' => 'SunGrow', 'alarm_code' => $finalData1->fault_code, 'severity' => $alarmLevelString])->first()->id : 000;
                    $DvInverterCode = InverterSerialNo::where(['dv_inverter_serial_no' => $finalData1->device_sn])->first();
                    $dvInverters = $finalData1->device_sn;

                    $faultAndAlarmLogData = FaultAlarmLog::where('fault_and_alarm_id', $faultAndAlarmID)->where('plant_id', $Plant_ID)->where('siteId', $ps_id)->where('dv_inverter', $dvInverters)->where('status', 'Y')->latest()->first();

                    if($faultAndAlarmLogData) {

                        if($alarmStatusString == 'N') {

                            return "return IF ALARMLOGDATA EXIST";

                            $faultAndAlarmLogData->status = $alarmStatusString;
                            $faultAndAlarmLogData->updated_at = date('Y-m-d H:i:s', substr($finalData1->over_time, 0, 10));

                            $faultAndAlarmLogData->save();
                        }
                    }
                    else {
                        // return gettype($faultAndAlarmID);

                        $faultAlarmLogObject = new FaultAlarmLog();

                        $faultAlarmLogObject->fault_and_alarm_id = $faultAndAlarmID;
                        $faultAlarmLogObject->plant_id = $Plant_ID;
                        $faultAlarmLogObject->siteId = $ps_id;
                        $faultAlarmLogObject->dv_inverter = $dvInverters;
                        $faultAlarmLogObject->status = $alarmStatusString;
                        $faultAlarmLogObject->created_at = $finalData1->create_time;
                        if($alarmStatusString == 'N') {
                            return "return in newfaultAlarmlog";
                            $faultAlarmLogObject->updated_at = $finalData1->over_time;
                        }
                       if($alarmStatusString == 'Y') {

                           $faultAlarmLogObject->updated_at = NULL;
                       }
                        $faultAlarmLogObject->save();


                        if(PlantUser::where('plant_id', $Plant_ID)->exists()) {

                            $plantUsers = PlantUser::where('plant_id', $Plant_ID)->get();

                            foreach($plantUsers as $key6 => $user) {

                                $alarmNotification['plant_id'] = $Plant_ID;
                                $alarmNotification['user_id'] = $user->user_id;
                                $alarmNotification['entry_date'] = date('Y-m-d');
                                $alarmNotification['schedule_date'] = date('Y-m-d');
                                $alarmNotification['notification_type'] = 'mobile';
                                $alarmNotification['alarm_log_id'] = $faultAlarmLogObject->id;
                                $alarmNotification['is_msg_app'] = 'Y';
                                $alarmNotification['is_msg_sms'] = 'N';
                                $alarmNotification['is_msg_email'] = 'N';
                                $alarmNotification['is_notification_required'] = 'N';

                                $alarmNotificationResponce = Notification::create($alarmNotification);
                            }
                        }
                    }


                }
            }
        }

              $Details = [
                        "appkey" => $appKey,
                        "token" => $token,
                        "ps_id" => $ps_id,
                        "process_status" => "9",
                        "curPage" => "1",
                        "size" => "5000",
                    ];
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/getFaultAlarmInfo',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>json_encode($Details),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'sys_code: 901',
                        'lang: _en_US',
                        'x-access-key: ' . $accessKey,
                    ),
                    ));

                    $Resolvedresponse = curl_exec($curl);
                    // return $Resolvedresponse;
                    curl_close($curl);
                    $plantSiteResolvedAlarmResponseData = json_decode($Resolvedresponse);
                    // return json_encode($plantSiteResolvedAlarmResponseData);

                    if($plantSiteResolvedAlarmResponseData && isset($plantSiteResolvedAlarmResponseData->result_data)) {

                        $plantSiteResolvedAlarmFinalData = $plantSiteResolvedAlarmResponseData->result_data;
                        // return "fdhg" + $plantSiteResolvedAlarmFinalData;
                        if($plantSiteResolvedAlarmFinalData && isset($plantSiteResolvedAlarmFinalData->pageList)){

                            $plantSiteResolvedAlarmData=$plantSiteResolvedAlarmFinalData->pageList;
                            foreach($plantSiteResolvedAlarmData as $key3 => $FinalData) {

                                $alarmLevelStringResolved = '';
                                $alarmStatusStringResolved = '';

                                if($FinalData->fault_type == 1) {

                                    $alarmLevelStringResolved = 'Critical';

                                }
                                else if($FinalData->fault_type == 2) {

                                    $alarmLevelStringResolved = 'Major';
                                }
                                else if($FinalData->fault_type == 3) {

                                    $alarmLevelStringResolved = 'Minor';
                                }
                                else if($FinalData->fault_type == 4) {

                                    $alarmLevelStringResolved = 'Warning';
                                }
                                if($alarmLevelStringResolved == 'Critical'){
                                    $alarmDataPrevious = FaultAndAlarm::updateOrCreate(
                                        [ 'plant_meter_type' => 'SunGrow', 'alarm_code' => $FinalData->fault_code, 'severity' => $alarmLevelStringResolved],
                                        ['description' => $FinalData->fault_name, 'correction_action' => 'N/A', 'type' => 'Fault', 'category' => 'Hardware', 'sub_category' => $FinalData->type_name, 'alarm_source' => 'Inveter #', 'proactive_complain' => 'No']
                                    );
                                }
                                else{

                                    $alarmDataPrevious = FaultAndAlarm::updateOrCreate(
                                        [ 'plant_meter_type' => 'SunGrow', 'alarm_code' => $FinalData->fault_code, 'severity' => $alarmLevelStringResolved],
                                        ['description' => $FinalData->fault_name, 'correction_action' => 'N/A', 'type' => 'Alarm', 'category' => 'Hardware', 'sub_category' => $FinalData->type_name, 'alarm_source' => 'Inveter #', 'proactive_complain' => 'No']
                                    );
                                }

                                $faultAndAlarmID1 = FaultAndAlarm::where(['plant_meter_type' => 'SunGrow', 'alarm_code' => $FinalData->fault_code, 'severity' => $alarmLevelStringResolved])->exists() ? FaultAndAlarm::where(['plant_meter_type' => 'SunGrow', 'alarm_code' => $FinalData->fault_code,'severity' => $alarmLevelStringResolved])->first()->id : 12500;
                                // return $faultAndAlarmID1;
                                $DvInverterCode = InverterSerialNo::where(['dv_inverter_serial_no' => $FinalData->ps_key])->first();
                                $DVInverters = $FinalData->ps_key;
                                $faultAndAlarmLogData1 = FaultAlarmLog::where('fault_and_alarm_id', $faultAndAlarmID1)->where('plant_id', $Plant_ID)->where('siteId', $ps_id)->where('dv_inverter', $DVInverters)->where('created_at',$FinalData->create_time)->latest()->first();

                                if($faultAndAlarmLogData1) {

                                    $faultAndAlarmLogData1->status = "N";
                                    $faultAndAlarmLogData1->updated_at = $FinalData->over_time;
                                    $faultAndAlarmLogData1->save();
                                }
                                else {
                                    // return gettype($faultAndAlarmID);
return "ok";
                                    $faultAlarmLogPreviousObject = new FaultAlarmLog();

                                    $faultAlarmLogPreviousObject->fault_and_alarm_id = $faultAndAlarmID1;
                                    $faultAlarmLogPreviousObject->plant_id = $Plant_ID;
                                    $faultAlarmLogPreviousObject->siteId = $ps_id;
                                    $faultAlarmLogPreviousObject->dv_inverter = $DVInverters;
                                    $faultAlarmLogPreviousObject->status = "N";
                                    $faultAlarmLogPreviousObject->created_at = $FinalData->create_time;
                                    $faultAlarmLogPreviousObject->updated_at = $FinalData->over_time;

                                    $faultAlarmLogPreviousObject->save();


                                    if(PlantUser::where('plant_id', $Plant_ID)->exists()) {

                                        $plantUsers = PlantUser::where('plant_id', $Plant_ID)->get();

                                        foreach($plantUsers as $key6 => $user) {

                                            $alarmNotification['plant_id'] = $Plant_ID;
                                            $alarmNotification['user_id'] = $user->user_id;
                                            $alarmNotification['entry_date'] = date('Y-m-d');
                                            $alarmNotification['schedule_date'] = date('Y-m-d');
                                            $alarmNotification['notification_type'] = 'mobile';
                                            $alarmNotification['alarm_log_id'] = $faultAlarmLogPreviousObject->id;
                                            $alarmNotification['is_msg_app'] = 'Y';
                                            $alarmNotification['is_msg_sms'] = 'N';
                                            $alarmNotification['is_msg_email'] = 'N';
                                            $alarmNotification['is_notification_required'] = 'N';

                                            $alarmNotificationResponce = Notification::create($alarmNotification);
                                        }
                                    }
                                }

                            }
                        }
                    }
//        return 'okkkkkkkk';
    }
}
