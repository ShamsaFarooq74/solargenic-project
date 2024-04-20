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

class SolisAlertsController extends Controller
{
    public function AlarmAndFault($token,$plantID,$stationId)
    {
        $curl = curl_init();
        $siteData = [

            "stationId" => $stationId,
            "startTime" => date(date('Y-m-d', strtotime("-25 days"))),
            "endTime" => date('Y-m-d')
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.solarmanpv.com/station/v1.0/alert',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($siteData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $plantSiteAlarmResponseData = json_decode($response);
        if($plantSiteAlarmResponseData) {

            $plantSiteAlarmFinalData = isset($plantSiteAlarmResponseData->stationAlertItems) ? $plantSiteAlarmResponseData->stationAlertItems : [];

            foreach((array)$plantSiteAlarmFinalData as $key5 => $finalData1) {

                $alarmLevelString = '';
                $alarmStatusString = '';

                if($finalData1->level == 0) {

                    $alarmLevelString = 'Minor';

                }
                else if($finalData1->level == 1) {

                    $alarmLevelString = 'Major';
                }
                else if($finalData1->level == 2) {

                    $alarmLevelString = 'Critical';
                }

                if($finalData1->level == 0 || $finalData1->level == 1 || $finalData1->level == 2) {

                    $alarmStatusString = 'Y';
                }
                else {

                    $alarmStatusString = 'N';
                }
                if($alarmLevelString == 'Critical'){
                    $alarmData = FaultAndAlarm::updateOrCreate(
                        [ 'plant_meter_type' => 'Solis', 'alarm_code' => $finalData1->ruleId, 'severity' => $alarmLevelString, ],
                        ['description' => $finalData1->showName, 'correction_action' => 'N/A', 'type' => 'Fault', 'category' => 'Hardware', 'sub_category' => 'Inverter', 'alarm_source' => 'Inveter #', 'proactive_complain' => 'No']
                    );
                }
                else{

                    $alarmData = FaultAndAlarm::updateOrCreate(
                        [ 'plant_meter_type' => 'Solis', 'alarm_code' => $finalData1->ruleId, 'severity' => $alarmLevelString],
                        ['description' => $finalData1->showName, 'correction_action' => 'N/A', 'type' => 'Alarm', 'category' => 'Hardware', 'sub_category' => 'Inverter', 'alarm_source' => 'Inveter #', 'proactive_complain' => 'No']
                    );
                }
                $faultAndAlarmID = FaultAndAlarm::where(['plant_meter_type' => 'Solis', 'alarm_code' => $finalData1->ruleId, 'severity' => $alarmLevelString])->exists() ? FaultAndAlarm::where(['plant_meter_type' => 'Solis', 'alarm_code' => $finalData1->ruleId, 'severity' => $alarmLevelString])->first()->id : 100001;
                $DvInverterCode = InverterSerialNo::where(['inverter_name' => $finalData1->deviceSn])->first();
                $dvInverters = $finalData1->deviceSn;
                $faultAndAlarmLogData = FaultAlarmLog::where('fault_and_alarm_id', $faultAndAlarmID)->where('plant_id', $plantID)->where('siteId', $stationId)->where('dv_inverter', $dvInverters)->where('created_at',date('Y-m-d H:i:s', substr($finalData1->alertTime, 0, 10)))->latest()->first();
                $GetAlarmDatafromDB = FaultAlarmLog::where('plant_id', $plantID)->where('status', 'Y')->get();

                if($GetAlarmDatafromDB){

                    foreach($GetAlarmDatafromDB as $key3 => $DBData){

                        $currDate=strtotime(date('Y-m-d H:i:s'));
                        $lastTrigger = $DBData->created_at;
                        $minutes = abs(strtotime(date('Y-m-d H:i:s')) - strtotime($lastTrigger)) / 60;

                        if( $minutes > 10)
                        {
                            $DBData->status = "N";
                            $DBData->updated_at = date('Y-m-d H:i:s');
                            $DBData->update();
                        }

                    }
                }

                if($faultAndAlarmLogData) {

                    if($alarmStatusString == 'N') {

                        $faultAndAlarmLogData->status = $alarmStatusString;
                        $faultAndAlarmLogData->updated_at = date('Y-m-d H:i:s', substr($finalData1->recoverDate, 0, 10));
                        $faultAndAlarmLogData->save();
                    }
                }
                else {

                    $faultAlarmLogObject = new FaultAlarmLog();

                    $faultAlarmLogObject->fault_and_alarm_id = $faultAndAlarmID;
                    $faultAlarmLogObject->plant_id = $plantID;
                    $faultAlarmLogObject->siteId = $stationId;
                    $faultAlarmLogObject->dv_inverter = $dvInverters;
                    $faultAlarmLogObject->status = $alarmStatusString;
                    //    return $alarmStatusString;
                    $faultAlarmLogObject->created_at = date('Y-m-d H:i:s', substr($finalData1->alertTime, 0, 10));
                    if($alarmStatusString == 'N') {

                        $faultAlarmLogObject->updated_at = date('Y-m-d H:i:s');
                    }
                    if($alarmStatusString == 'Y') {

                        $faultAlarmLogObject->updated_at = NULL;
                    }
                    // return $faultAlarmLogObject;
                    $faultAlarmLogObject->save();

                    if(PlantUser::where('plant_id', $plantID)->exists()) {

                        $plantUsers = PlantUser::where('plant_id', $plantID)->get();

                        foreach($plantUsers as $key6 => $user) {

                            $alarmNotification['plant_id'] = $plantID;
                            $alarmNotification['user_id'] = $user->user_id;
                            $alarmNotification['fault_and_alarm_id'] = $alarmData->id;
                            if($alarmLevelString == 'Critical') {
                                $alarmNotification['title'] = 'Fault';
                            }else{
                                $alarmNotification['title'] = 'Alarm';
                            }
                            $alarmNotification['description'] = $finalData1->showName;
                            $alarmNotification['entry_date'] = date('Y-m-d');
                            $alarmNotification['schedule_date'] = date('Y-m-d');
                            $alarmNotification['notification_type'] = 'Alert';
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
}
