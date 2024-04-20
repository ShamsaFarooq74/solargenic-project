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

class HuaweiRealTimeController extends Controller
{
    public function index($globalGenerationLogMaxID = null, $globalProcessedLogMaxID = null, $globalInverterDetailMaxID = null) {

        //try {

            date_default_timezone_set('Asia/Karachi');
            $currentTime = date('Y-m-d H:i:s');
            $generationLogMaxCronJobID = $globalGenerationLogMaxID + 1;
            $processedMaxCronJobID = $globalProcessedLogMaxID + 1;
            $inverterMaxCronJobID = $globalInverterDetailMaxID + 1;
            $inverterEnergyLogMaxCronJobID = InverterEnergyLog::max('cron_job_id') + 1;
            $envReductionValue = Setting::where('perimeter', 'env_reduction')->exists() ? Setting::where('perimeter', 'env_reduction')->first()->value : 0;
            $irradianceValue = Setting::where('perimeter', 'irradiance')->exists() ? Setting::where('perimeter', 'irradiance')->first()->value : 0;
            $huaweiAPIBaseURL = Setting::where('perimeter', 'huawei_api_base_url')->exists() ? Setting::where('perimeter', 'huawei_api_base_url')->first()->value : '';
            $huaweiAPIUserName = Setting::where('perimeter', 'huawei_api_user_name')->exists() ? Setting::where('perimeter', 'huawei_api_user_name')->first()->value : '';
            $huaweiAPISystemCode = Setting::where('perimeter', 'huawei_api_system_code')->exists() ? Setting::where('perimeter', 'huawei_api_system_code')->first()->value : '';
            
            $settingCookie = Setting::where('perimeter', 'huawei_api_cookie')->exists() ? Setting::where('perimeter', 'huawei_api_cookie')->first()->value : 'eybj';
            $settingSessionID = Setting::where('perimeter', 'huawei_session_id')->exists() ? Setting::where('perimeter', 'huawei_session_id')->first()->value : 'eybj';

            $tokenSessionData = [$settingCookie, $settingSessionID];

            //ALL PLANTS DATA
            // $allPlantsData = Plant::where('meter_type', 'Huawei')->get();
            // $allPlantsData = Plant::where('meter_type', 'Huawei')->get();
            $allPlantsData = Plant::where('id', 66)->where('meter_type', 'Huawei')->get();
            // $allPlantsData = Plant::where('id', 66)->where('meter_type', 'Huawei')->get();

            if($allPlantsData) {

                foreach($allPlantsData as $key => $plant) {

                    $plantID = $plant->id;
                    $plantTotalBuyEnergy = 0;
                    $plantTotalSellEnergy = 0;
                    $plantGridInverterArray = array();

                    //ALL SITES OF UPPER SELECTED PLANT
                    $plantSiteList = PlantSite::where('plant_id', $plantID)->get();

                    if($plantSiteList) {

                        $plantSiteListString = '';
                        $plantSiteListArray = array();
                        $inverterStateArray = array();
                        $countSiteList = 0;

                        foreach($plantSiteList as $key1 => $site) {

                            $siteID = $site->site_id;
                            $inverterDetailTime = date('Y-m-d H:i:s');
                            $inverterSerialNoString = "";
                            $plantSiteListArray[] = $siteID;
                            $siteInverterArray = array();
                            $siteGridMeterArray = array();
                            $siteSmartInverterArray = array();
                            $siteGridInverterArray = array();
                            $siteEMIInverterArray = array();
                            $countSiteList++;
                            $siteSmartInverterLogStartTime = array();
                            $siteEMIInverterLogStartTime = array();
                            $siteGridInverterLogStartTime = array();
                            $siteAllInverterLogStartTime = array();

                            if($countSiteList == count($plantSiteList)) {

                                $plantSiteListString .= $siteID;
                            }
                            else {

                                $plantSiteListString .= $siteID.',';
                            }

                            //SITE STATUS DATA
							$plantSiteStatusData = [
								"stationCodes" => $siteID,
							];

							$plantSiteStatusCurl = curl_init();

							curl_setopt_array($plantSiteStatusCurl, array(

								CURLOPT_URL => $huaweiAPIBaseURL.'/getStationRealKpi',
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => '',
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_SSL_VERIFYHOST => false,
								CURLOPT_SSL_VERIFYPEER => false,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => 'POST',
								CURLOPT_POSTFIELDS => json_encode($plantSiteStatusData),
								CURLOPT_HTTPHEADER => array(
									'XSRF-TOKEN: '.$tokenSessionData[0],
									'Accept: application/json',
									'Content-Type: application/json',
									'Cookie: JSESSIONID='.$tokenSessionData[1].'; XSRF-TOKEN='.$tokenSessionData[0].'; web-auth=true'
								),
							));

							$plantSiteStatusResponse = curl_exec($plantSiteStatusCurl);

							curl_close($plantSiteStatusCurl);

							$plantSiteStatusResponseData = json_decode($plantSiteStatusResponse);

							if($plantSiteStatusResponseData) {
                                // if($plantSiteStatusResponseData && isset($plantSiteStatusResponseData->data)) {

                                $plantSiteStatusFinalData = $plantSiteStatusResponseData;

                                if($plantSiteStatusFinalData) {

                                    if(isset($plantSiteStatusFinalData->failCode) && isset($plantSiteStatusFinalData->message) && ($plantSiteStatusFinalData->failCode == 305 || $plantSiteStatusFinalData->failCode == 306)) {
                                        
                                        $a = $this->getTokenAndSessionID($huaweiAPIBaseURL, $huaweiAPIUserName, $huaweiAPISystemCode, 'CRON_JOB');
                                 
                                        return $this->index($globalGenerationLogMaxID = null, $globalProcessedLogMaxID = null, $globalInverterDetailMaxID = null);
                                    }

                                    else {

                                        foreach((array)$plantSiteStatusFinalData as $key8 => $finalData2) {

                                            $siteStatusString = '';
        
                                            if(isset($finalData2->dataItemMap)) {
    
                                                $finalMapData = $finalData2->dataItemMap;
            
                                                if($finalMapData->real_health_state == "1") {
            
                                                    $siteStatusString = 'N';
                                                }
                                                else if($finalMapData->real_health_state == "2") {
            
                                                    $siteStatusString = 'F';
                                                }
                                                else if($finalMapData->real_health_state == "3") {
            
                                                    $siteStatusString = 'Y';
                                                }
            
                                                //SITE STATUS UPDATE DATA
                                                $siteStatusUpdateResponse = PlantSite::where(['plant_id' => $plantID, 'site_id' => $finalData2->stationCode])->update(['online_status' => $siteStatusString]);
                                            }
                                        }
                                        print_r('site status');
                                        print_r(date("Y-m-d H:i:s"));
                                        print_r("\n");
                                    }
                                }
							}

                            //PLANT ALARM DATA
                            /*$plantSiteAlarmData = [
                                "stationCodes" => $plantSiteListString,
                                "beginTime" => strtotime(date('Y-m-d H:i:s.u', strtotime("-25 days"))).'000',
                                "endTime" => strtotime(date('Y-m-d H:i:s.u')).'000',
                                "language" => "en_UK",
                                "devTypes"=> 1
                            ];

                            $plantSiteAlarmCurl = curl_init();

                            curl_setopt_array($plantSiteAlarmCurl, array(

                                CURLOPT_URL => $huaweiAPIBaseURL.'/getAlarmList',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_SSL_VERIFYHOST => false,
                                CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => json_encode($plantSiteAlarmData),
                                CURLOPT_HTTPHEADER => array(
                                    'XSRF-TOKEN: '.$tokenSessionData[0],
                                    'Accept: application/json',
                                    'Content-Type: application/json',
                                    'Cookie: JSESSIONID='.$tokenSessionData[1].'; XSRF-TOKEN=eyJhbGciOiJIUzI1NiJ9.eyJyYW5kb21LZXkiOiI3NDNmNDI3ZC1iYWQyLTRhZGMtYWMzZi01N2I5MDg2ZGI5NDEifQ.wXYnbs2FOh_RX_CKlqkjtj6cRDeFc3n4JxXhVLWOFm4; web-auth=true'
                                ),
                            ));

                            $plantSiteAlarmResponse = curl_exec($plantSiteAlarmCurl);

                            curl_close($plantSiteAlarmCurl);

                            $plantSiteAlarmResponseData = json_decode($plantSiteAlarmResponse);

                            if($plantSiteAlarmResponseData && isset($plantSiteAlarmResponseData->data)) {

                                $plantSiteAlarmFinalData = $plantSiteAlarmResponseData->data;

                                foreach((array)$plantSiteAlarmFinalData as $key5 => $finalData1) {

                                    $alarmLevelString = '';
                                    $alarmStatusString = '';

                                    if($finalData1->lev == 1) {

                                        $alarmLevelString = 'Critical';
                                    }
                                    else if($finalData1->lev == 2) {

                                        $alarmLevelString = 'Major';
                                    }
                                    else if($finalData1->lev == 3) {

                                        $alarmLevelString = 'Minor';
                                    }
                                    else if($finalData1->lev == 4) {

                                        $alarmLevelString = 'Warning';
                                    }

                                    if($finalData1->status == 1 || $finalData1->status == 2 || $finalData1->status == 3 || $finalData1->status == 4) {

                                        $alarmStatusString = 'Y';
                                    }
                                    else {

                                        $alarmStatusString = 'N';
                                    }

                                    //ALARM AND FAULT DATA
                                    $alarmData = FaultAndAlarm::updateOrCreate(
                                        ['plant_meter_type' => 'Huawei', 'alarm_code' => $finalData1->alarmId, 'severity' => $alarmLevelString],
                                        ['description' => $finalData1->alarmName, 'correction_action' => $finalData1->repairSuggestion, 'type' => 'Alarm', 'category' => 'Hardware', 'sub_category' => 'Inverter', 'alarm_source' => 'Inveter #', 'proactive_complain' => 'No']
                                    );

                                    $faultAndAlarmID = FaultAndAlarm::where(['plant_meter_type' => 'Huawei', 'alarm_code' => $finalData1->alarmId, 'severity' => $alarmLevelString])->exists() ? FaultAndAlarm::where(['plant_meter_type' => 'Huawei', 'alarm_code' => $finalData1->alarmId, 'severity' => $alarmLevelString])->first()->id : 184;
                                    $DvInverterCode = InverterSerialNo::where(['inverter_name' => $finalData1->devName])->first()->dv_inverter;

                                    $faultAndAlarmLogData = FaultAlarmLog::where('fault_and_alarm_id', $faultAndAlarmID)->where('plant_id', $plantID)->where('siteId', $finalData1->stationCode)->where('dv_inverter', $DvInverterCode)->where('status', 'Y')->latest()->first();

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
                                        $faultAlarmLogObject->siteId = $finalData1->stationCode;
                                        $faultAlarmLogObject->dv_inverter = $DvInverterCode;
                                        $faultAlarmLogObject->status = $alarmStatusString;
                                        $faultAlarmLogObject->created_at = date('Y-m-d H:i:s', substr($finalData1->raiseTime, 0, 10));
                                        if($alarmStatusString == 'N') {

                                            $faultAlarmLogObject->updated_at = date('Y-m-d H:i:s', substr($finalData1->recoverDate, 0, 10));
                                        }
                                        if($alarmStatusString == 'Y') {

                                            $faultAlarmLogObject->updated_at = NULL;
                                        }

                                        $faultAlarmLogObject->save();

                                        if(PlantUser::where('plant_id', $plantID)->exists()) {

                                            $plantUsers = PlantUser::where('plant_id', $plantID)->get();

                                            foreach($plantUsers as $key6 => $user) {

                                                $alarmNotification['plant_id'] = $plantID;
                                                $alarmNotification['user_id'] = $user->user_id;
                                                $alarmNotification['entry_date'] = $currentTime;
                                                $alarmNotification['schedule_date'] = $currentTime;
                                                $alarmNotification['notification_type'] = $faultAlarmLogObject->status;
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
                            }*/

							//DEVICE LIST OF UPPER SELECTED SITE ID
                            $siteDevListData = [
                                "stationCodes" => $siteID,
                            ];

                            $siteDevListCurl = curl_init();

                            curl_setopt_array($siteDevListCurl, array(

                                CURLOPT_URL => $huaweiAPIBaseURL.'/getDevList',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_SSL_VERIFYHOST => false,
                                CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => json_encode($siteDevListData),
                                CURLOPT_HTTPHEADER => array(
                                    'XSRF-TOKEN: '.$tokenSessionData[0],
                                    'Accept: application/json',
                                    'Content-Type: application/json',
                                    'Cookie: JSESSIONID='.$tokenSessionData[1].'; XSRF-TOKEN=eyJhbGciOiJIUzI1NiJ9.eyJyYW5kb21LZXkiOiI3NDNmNDI3ZC1iYWQyLTRhZGMtYWMzZi01N2I5MDg2ZGI5NDEifQ.wXYnbs2FOh_RX_CKlqkjtj6cRDeFc3n4JxXhVLWOFm4; web-auth=true'
                                ),
                            ));

                            $siteDevListResponse = curl_exec($siteDevListCurl);

                            curl_close($siteDevListCurl);

                            $siteDevListResponseData = json_decode($siteDevListResponse);

                            if($siteDevListResponseData && isset($siteDevListResponseData->data)) {

                                $siteDevListFinalData = $siteDevListResponseData->data;
                            }

                            if(isset($siteDevListFinalData) && $siteDevListFinalData) {

                                $countDevList = 0;

                                foreach((array)$siteDevListFinalData as $key2 => $dev) {

                                    //SITE INVERTER DETAIL
                                    $invSerial = SiteInverterDetail::updateOrCreate(
                                        ['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $dev->id, 'dv_inverter_type' => $dev->devTypeId],
                                        ['dv_inverter_serial_no' => $dev->esnCode, 'dv_inverter_name' => $dev->devName, 'longitude' => $dev->longitude, 'latitude' => $dev->latitude]
                                    );

                                    $countDevList++;

                                    if(isset($dev) && isset($dev->devTypeId) && $dev->devTypeId == 17) {

                                        $siteGridMeterArray[] = $dev->id;
                                        $plantGridInverterArray[] = $dev->id;
                                        $siteGridInverterArray[] = $dev->id;
                                    }

                                    if(isset($dev->id) && $dev->id != null && $dev->devTypeId == 1) {

                                        //INVERTER SERIAL NO
                                        $invSerial = InverterSerialNo::updateOrCreate(
                                            ['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $dev->id, 'inverter_type_id' => $dev->devTypeId],
                                            ['dv_inverter_serial_no' => $dev->esnCode, 'inverter_name' => $dev->devName]
                                        );

                                        $siteSmartInverterArray[] = $dev->id;

                                        if($countDevList == count($siteDevListFinalData)) {

                                            $inverterSerialNoString .= $dev->id;
                                        }
                                        else {

                                            $inverterSerialNoString .= $dev->id.",";
                                        }
                                    }

                                    if(isset($dev) && isset($dev->devTypeId) && $dev->devTypeId == 10) {

                                        $siteEMIInverterArray[] = $dev->id;
                                    }

                                    $siteInverterArray[] = $dev->id;
                                }

                                print_r('site device list');
                                print_r(date("Y-m-d H:i:s"));
                                print_r("\n");

                                //SMART INVERTER LOG
                                $dailyGenerationData = 0;

                                $siteSmartInverterData = [
                                    "devIds" => $inverterSerialNoString,
                                    "devTypeId" => 1,
                                ];

                                $siteSmartInverterCurl = curl_init();

                                curl_setopt_array($siteSmartInverterCurl, array(

                                    CURLOPT_URL => $huaweiAPIBaseURL.'/getDevRealKpi',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_SSL_VERIFYHOST => false,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($siteSmartInverterData),
                                    CURLOPT_HTTPHEADER => array(
                                        'XSRF-TOKEN: '.$tokenSessionData[0],
                                        'Accept: application/json',
                                        'Content-Type: application/json',
                                        'Cookie: JSESSIONID='.$tokenSessionData[1].'; XSRF-TOKEN=eyJhbGciOiJIUzI1NiJ9.eyJyYW5kb21LZXkiOiI3NDNmNDI3ZC1iYWQyLTRhZGMtYWMzZi01N2I5MDg2ZGI5NDEifQ.wXYnbs2FOh_RX_CKlqkjtj6cRDeFc3n4JxXhVLWOFm4; web-auth=true'
                                    ),
                                ));

                                $siteSmartInverterResponse = curl_exec($siteSmartInverterCurl);

                                curl_close($siteSmartInverterCurl);

                                $siteSmartInverterResponseData = json_decode($siteSmartInverterResponse);

                                if($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->data)) {

                                    $siteSmartInverterFinalData = $siteSmartInverterResponseData->data;

                                    if($siteSmartInverterFinalData) {

                                        foreach((array)$siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {

                                            if(isset($smartInverterFinalData->dataItemMap)) {

                                                $data = $smartInverterFinalData->dataItemMap;

                                                $inverterStateArray[] = $data->inverter_state;

                                                $inverterStateString = '';
                                                $inverterStateObject = InverterStateDescription::where('plant_meter_type', 'Huawei')->where('decimal_code', $data->inverter_state)->first();

                                                if($inverterStateObject) {

                                                    $inverterStateString = $inverterStateObject->code_description;
                                                }
                                                else {

                                                    $invStateDescriptionArray['plant_meter_type'] = 'Huawei';
                                                    $invStateDescriptionArray['status_code'] = base_convert($data->inverter_state, 10, 16);
                                                    $invStateDescriptionArray['decimal_code'] = $data->inverter_state;
                                                    $invStateDescriptionArray['code_description'] = '-----';

                                                    $invStateDescriptionResponse = InverterStateDescription::create($invStateDescriptionArray);

                                                    $inverterStateString = $invStateDescriptionResponse->code_description;
                                                }

                                                $inverterDetailLog = array();

                                                $inverterDetailLog['plant_id'] = $plantID;
                                                $inverterDetailLog['siteId'] = $siteID;
                                                $inverterDetailLog['dv_inverter'] = $smartInverterFinalData->devId;
                                                $inverterDetailLog['inverterPower'] = isset($data->active_power) ? $data->active_power : 0;
                                                $dailyGenerationData = $data->day_cap != null ? $data->day_cap : $dailyGenerationData;
                                                $inverterDetailLog['daily_generation'] = isset($data->day_cap) ? $data->day_cap : 0;
                                                $inverterDetailLog['inverterEfficieny'] = isset($data->efficiency) ? $data->efficiency : 0;
                                                $inverterDetailLog['inverterTemperature'] = isset($data->temperature) ? $data->temperature : 0;
                                                $inverterDetailLog['inverterState'] = $inverterStateString;
                                                $inverterDetailLog['inverterStateCode'] = isset($data->inverter_state) ? $data->inverter_state : 0;
                                                $inverterDetailLog['mpptPower'] = isset($data->mppt_power) ? $data->mppt_power : 0;
                                                $inverterDetailLog['frequency'] = isset($data->elec_freq) ? $data->elec_freq : 0;

                                                $inverterStartTime = date("Y-m-d H:i:s", $data->open_time);

                                                $inverterDetailLog['start_time'] = $inverterStartTime;
                                                $inverterDetailLog['phase_voltage_r'] = isset($data->a_u) ? $data->a_u : 0;
                                                $inverterDetailLog['phase_voltage_s'] = isset($data->b_u) ? $data->b_u : 0;
                                                $inverterDetailLog['phase_voltage_t'] = isset($data->c_u) ? $data->c_u : 0;
                                                $inverterDetailLog['phase_current_r'] = isset($data->a_i) ? $data->a_i : 0;
                                                $inverterDetailLog['phase_current_s'] = isset($data->b_i) ? $data->b_i : 0;
                                                $inverterDetailLog['phase_current_t'] = isset($data->c_i) ? $data->c_i : 0;
                                                $inverterDetailLog['collect_time'] = date('Y-m-d H:i:s', substr($siteSmartInverterResponseData->params->currentTime, 0, -3));
                                                $inverterDetailLog['inverter_cron_job_id'] = $inverterMaxCronJobID;

                                                $inverterDetailResponse = InverterDetail::create($inverterDetailLog);
                                    
                                                //INVERTER MPPT DATA
                                                $dataInArray = (array)$data;

                                                for($mi = 1; $mi <= 24; $mi++) {

                                                    $inverterMPPTLog = array();

                                                    $inverterMPPTLog['plant_id'] = $plantID;
                                                    $inverterMPPTLog['site_id'] = $siteID;
                                                    $inverterMPPTLog['dv_inverter'] = $smartInverterFinalData->devId;
                                                    $inverterMPPTLog['mppt_number'] = $mi;
                                                    $inverterMPPTLog['mppt_voltage'] = isset($dataInArray['pv'.$mi.'_u']) ? $dataInArray['pv'.$mi.'_u'] : 0;
                                                    $inverterMPPTLog['mppt_current'] = isset($dataInArray['pv'.$mi.'_i']) ? $dataInArray['pv'.$mi.'_i'] : 0;
                                                    $inverterMPPTLog['mppt_power'] = isset($dataInArray['pv'.$mi.'_u']) && isset($dataInArray['pv'.$mi.'_i']) ? ((double)$dataInArray['pv'.$mi.'_u'] * (double)$dataInArray['pv'.$mi.'_i']) : 0;
                                                    $inverterMPPTLog['collect_time'] = date('Y-m-d H:i:s', substr($siteSmartInverterResponseData->params->currentTime, 0, -3));

                                                    $inverterMPPTResponse = InverterMPPTDetail::create($inverterMPPTLog);
                                                }
                                            }
                                        }
                                        print_r('smart inverter details and mppt');
                                        print_r(date("Y-m-d H:i:s"));
                                        print_r("\n");
                                    }
                                }

                                //DAILY INVERTER DATA
                                /*$dailyInvData = array();

                                $dailyGenerationData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->exists() ? InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;

                                $dailyInvData['plant_id'] = $plantID;
                                $dailyInvData['siteId'] = $siteID;
                                $dailyInvData['dv_inverter'] = $smartInverter;
                                $dailyInvData['updated_at'] = $currentTime;
                                $dailyInvData['daily_generation'] = $dailyGenerationData;

                                //$dailyGenerationData = InverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->exists() ? InverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;

                                $DailyInvDataExist = DailyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('created_at', $lastRecordDate)->first();

                                if($DailyInvDataExist){

                                    $dailyInvDataResponse =  $DailyInvDataExist->fill($dailyInvData)->save();
                                }
                                else {

                                    $dailyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($lastRecordDate));
                                    $dailyInvDataResponse = DailyInverterDetail::create($dailyInvData);
                                }
                                    

                                $logYear = date('Y', strtotime($lastRecordDate));
                                $logMonth = date('m', strtotime($lastRecordDate));

                                //MONTHLY INVERTER DATA
                                $monthlyInvData = array();

                                $monthlyInvData['plant_id'] = $plantID;
                                $monthlyInvData['siteId'] = $siteID;
                                $monthlyInvData['dv_inverter'] = $smartInverter;
                                $monthlyInvData['updated_at'] = $currentTime;

                                $dailyInverterData = DailyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('daily_generation');

                                $monthlyInvData['monthly_generation'] = isset($dailyInverterData) ? $dailyInverterData : 0;

                                $monthlyInvDataExist = MonthlyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->first();

                                if($monthlyInvDataExist) {

                                    $monthlyInvDataResponse =  $monthlyInvDataExist->fill((array)$monthlyInvData)->save();
                                }
                                else {

                                    $monthlyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($lastRecordDate));
                                    $monthlyInvDataResponse = MonthlyInverterDetail::create((array)$monthlyInvData);
                                }

                                //YEARLY INVERTER DATA
                                $yearlyInvData = array();

                                $yearlyInvData['plant_id'] = $plantID;
                                $yearlyInvData['siteId'] = $siteID;
                                $yearlyInvData['dv_inverter'] = $smartInverter;
                                $yearlyInvData['updated_at'] = $currentTime;

                                $monthlyInverterData = MonthlyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->sum('monthly_generation');

                                $yearlyInvData['yearly_generation'] = isset($monthlyInverterData) ? $monthlyInverterData : 0;

                                $yearlyInvDataExist = YearlyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->first();

                                if($yearlyInvDataExist) {

                                    $yearlyInvDataResponse =  $yearlyInvDataExist->fill((array)$yearlyInvData)->save();
                                }
                                else {

                                    $yearlyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($lastRecordDate));
                                    $yearlyInvDataResponse = YearlyInverterDetail::create((array)$yearlyInvData);
                                }

                                print_r('all smart inverter daily and monthly');
                                print_r(date("Y-m-d H:i:s"));
                                print_r("\n");*/

                                //EMI INVERTER LOG
                                /*if($plant->plant_has_emi == 'Y') {

                                    foreach($siteEMIInverterArray as $emiKey => $emiInverter) {

                                        $lastRecordTimeStamp = InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $emiInverter])->exists() ? InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $emiInverter])->orderBy('collect_time', 'DESC')->first()->collect_time : null;

                                        if(isset($lastRecordTimeStamp) && $lastRecordTimeStamp) {

                                            if(strtotime(date('Y-m-d', strtotime($lastRecordTimeStamp))) == strtotime(date('Y-m-d'))) {

                                                $lastRecordDate = date('Y-m-d', strtotime($lastRecordTimeStamp));
                                            }
                                            else {

                                                $lastRecordDate = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordTimeStamp)));
                                            }
                                        }
                                        else {

                                            $lastRecordDate = $plant->data_collect_date;
                                        }

                                        $siteEMIInverterLogStartTime[] = strtotime($lastRecordDate);
                                        $siteAllInverterLogStartTime[] = strtotime($lastRecordDate);

                                        while(strtotime($lastRecordDate) != strtotime(date('Y-m-d', strtotime("+1 days")))) {

                                            $collectTime = strtotime(date('Y-m-d H:i:s.u', strtotime('+5 hours', strtotime($lastRecordDate)))).'000';

                                            $siteEMIInverterData = [
                                                "devIds" => $emiInverter,
                                                "devTypeId" => 10,
                                                "collectTime" => $collectTime
                                            ];

                                            $siteEMIInverterCurl = curl_init();

                                            curl_setopt_array($siteEMIInverterCurl, array(

                                                CURLOPT_URL => $huaweiAPIBaseURL.'/getDevFiveMinutes',
                                                CURLOPT_RETURNTRANSFER => true,
                                                CURLOPT_ENCODING => '',
                                                CURLOPT_MAXREDIRS => 10,
                                                CURLOPT_TIMEOUT => 0,
                                                CURLOPT_FOLLOWLOCATION => true,
                                                CURLOPT_SSL_VERIFYHOST => false,
                                                CURLOPT_SSL_VERIFYPEER => false,
                                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                CURLOPT_CUSTOMREQUEST => 'POST',
                                                CURLOPT_POSTFIELDS => json_encode($siteEMIInverterData),
                                                CURLOPT_HTTPHEADER => array(
                                                    'XSRF-TOKEN: '.$tokenSessionData[0],
                                                    'Accept: application/json',
                                                    'Content-Type: application/json',
                                                    'Cookie: JSESSIONID='.$tokenSessionData[1].'; XSRF-TOKEN=eyJhbGciOiJIUzI1NiJ9.eyJyYW5kb21LZXkiOiI3NDNmNDI3ZC1iYWQyLTRhZGMtYWMzZi01N2I5MDg2ZGI5NDEifQ.wXYnbs2FOh_RX_CKlqkjtj6cRDeFc3n4JxXhVLWOFm4; web-auth=true'
                                                ),
                                            ));

                                            $siteEMIInverterResponse = curl_exec($siteEMIInverterCurl);

                                            curl_close($siteEMIInverterCurl);

                                            $siteEMIInverterResponseData = json_decode($siteEMIInverterResponse);

                                            if($siteEMIInverterResponseData && isset($siteEMIInverterResponseData->data)) {

                                                $siteEMIInverterFinalData = $siteEMIInverterResponseData->data;

                                                if($siteEMIInverterFinalData) {

                                                    foreach((array)$siteEMIInverterFinalData as $emiKeyData => $emiInverterFinalData) {

                                                        if(isset($emiInverterFinalData->dataItemMap)) {

                                                            $data = $emiInverterFinalData->dataItemMap;

                                                            if($lastRecordDate == date('Y-m-d')) {
                                                        
                                                                $todayLastTime = InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $emiInverter])->whereDate('collect_time', $lastRecordDate)->exists() ? InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $emiInverter])->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:00:00');
                                                            
                                                                if($emiInverterFinalData->collectTime > strtotime($todayLastTime)) {
                                                                
                                                                    $inverterEMIDetail['plant_id'] = $plantID;
                                                                    $inverterEMIDetail['site_id'] = $siteID;
                                                                    $inverterEMIDetail['dv_inverter'] = $emiInverterFinalData->devId;
                                                                    $inverterEMIDetail['temperature'] = isset($data->temperature) ? $data->temperature : 0;
                                                                    $inverterEMIDetail['pv_temperature'] = isset($data->pv_temperature) ? $data->pv_temperature : 0;
                                                                    $inverterEMIDetail['wind_speed'] = isset($data->wind_speed) ? $data->wind_speed : 0;
                                                                    $inverterEMIDetail['wind_direction'] = isset($data->wind_direction) ? $data->wind_direction : 0;
                                                                    $inverterEMIDetail['radiant_total'] = isset($data->radiant_total) ? $data->radiant_total : 0;
                                                                    $inverterEMIDetail['radiant_line'] = isset($data->radiant_line) ? $data->radiant_line : 0;
                                                                    $inverterEMIDetail['horiz_radiant_line'] = isset($data->horiz_radiant_line) ? $data->horiz_radiant_line : 0;
                                                                    $inverterEMIDetail['horiz_radiant_total'] = isset($data->horiz_radiant_total) ? $data->horiz_radiant_total : 0;
                                                                    $inverterEMIDetail['collect_time'] = date('Y-m-d H:i:s', substr($emiInverterFinalData->collectTime, 0, -3));
                                                                    $inverterEMIDetail['created_at'] = $currentTime;
                                                                    $inverterEMIDetail['updated_at'] = $currentTime;

                                                                    $inverterEMIDetailResponse = InverterEMIDetail::create($inverterEMIDetail);
                                                                }
                                                            }
                                                            else {

                                                                $inverterEMIDetail['plant_id'] = $plantID;
                                                                $inverterEMIDetail['site_id'] = $siteID;
                                                                $inverterEMIDetail['dv_inverter'] = $emiInverterFinalData->devId;
                                                                $inverterEMIDetail['temperature'] = isset($data->temperature) ? $data->temperature : 0;
                                                                $inverterEMIDetail['pv_temperature'] = isset($data->pv_temperature) ? $data->pv_temperature : 0;
                                                                $inverterEMIDetail['wind_speed'] = isset($data->wind_speed) ? $data->wind_speed : 0;
                                                                $inverterEMIDetail['wind_direction'] = isset($data->wind_direction) ? $data->wind_direction : 0;
                                                                $inverterEMIDetail['radiant_total'] = isset($data->radiant_total) ? $data->radiant_total : 0;
                                                                $inverterEMIDetail['radiant_line'] = isset($data->radiant_line) ? $data->radiant_line : 0;
                                                                $inverterEMIDetail['horiz_radiant_line'] = isset($data->horiz_radiant_line) ? $data->horiz_radiant_line : 0;
                                                                $inverterEMIDetail['horiz_radiant_total'] = isset($data->horiz_radiant_total) ? $data->horiz_radiant_total : 0;
                                                                $inverterEMIDetail['collect_time'] = date('Y-m-d H:i:s', substr($emiInverterFinalData->collectTime, 0, -3));
                                                                $inverterEMIDetail['created_at'] = $currentTime;
                                                                $inverterEMIDetail['updated_at'] = $currentTime;

                                                                $inverterEMIDetailResponse = InverterEMIDetail::create($inverterEMIDetail);
                                                            }
                                                        }
                                                    }
                                                    print_r('emi inverter log');
                                                    print_r(date("Y-m-d H:i:s"));
                                                    print_r("\n");
                                                }
                                            }

                                            // $lastRecordDate = date('Y-m-d', strtotime("+1 day",strtotime($lastRecordDate)));
                                            break;
                                        }
                                    }
                                }

                                print_r('all emi inverter daily and monthly');
                                                    print_r(date("Y-m-d H:i:s"));
                                                    print_r("\n");

                                //GRID METER LOG
                                foreach($siteGridInverterArray as $gridKey => $gridMeter) {

                                    $lastRecordTimeStamp = InverterEnergyLog::where('dv_inverter', $gridMeter)->exists() ? InverterEnergyLog::where('dv_inverter', $gridMeter)->orderBy('collect_time', 'DESC')->first()->collect_time : null;
                                    //$lastRecordDate = date('Y-m-d', strtotime($lastRecordTimeStamp));
                                    
                                    if(isset($lastRecordTimeStamp) && $lastRecordTimeStamp) {

                                        if(strtotime(date('Y-m-d', strtotime($lastRecordTimeStamp))) == strtotime(date('Y-m-d'))) {

                                            $lastRecordDate = date('Y-m-d', strtotime($lastRecordTimeStamp));
                                        }
                                        else {

                                            $lastRecordDate = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordTimeStamp)));
                                        }
                                    }
                                    else {

                                        $lastRecordDate = $plant->data_collect_date;
                                    }

                                    $siteGridInverterLogStartTime[] = strtotime($lastRecordDate);
                                    $siteAllInverterLogStartTime[] = strtotime($lastRecordDate);

                                    while(strtotime($lastRecordDate) != strtotime(date('Y-m-d', strtotime("+1 days")))) {

                                        $collectTime = strtotime(date('Y-m-d H:i:s.u', strtotime($lastRecordDate))).'000';

                                        //INVERTER ENERGY LOG
                                        $siteInverterEnergyLogData = [
                                            "devIds" => $gridMeter,
                                            "devTypeId" => 17,
                                            "collectTime" => $collectTime
                                        ];

                                        $siteInverterEnergyLogCurl = curl_init();

                                        curl_setopt_array($siteInverterEnergyLogCurl, array(

                                            CURLOPT_URL => $huaweiAPIBaseURL.'/getDevFiveMinutes',
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => '',
                                            CURLOPT_MAXREDIRS => 10,
                                            CURLOPT_TIMEOUT => 0,
                                            CURLOPT_FOLLOWLOCATION => true,
                                            CURLOPT_SSL_VERIFYHOST => false,
                                            CURLOPT_SSL_VERIFYPEER => false,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => 'POST',
                                            CURLOPT_POSTFIELDS => json_encode($siteInverterEnergyLogData),
                                            CURLOPT_HTTPHEADER => array(
                                                'XSRF-TOKEN: '.$tokenSessionData[0],
                                                'Accept: application/json',
                                                'Content-Type: application/json',
                                                'Cookie: JSESSIONID='.$tokenSessionData[1].'; XSRF-TOKEN=eyJhbGciOiJIUzI1NiJ9.eyJyYW5kb21LZXkiOiI3NDNmNDI3ZC1iYWQyLTRhZGMtYWMzZi01N2I5MDg2ZGI5NDEifQ.wXYnbs2FOh_RX_CKlqkjtj6cRDeFc3n4JxXhVLWOFm4; web-auth=true'
                                            ),
                                        ));

                                        $siteInverterEnergyLogResponse = curl_exec($siteInverterEnergyLogCurl);

                                        curl_close($siteInverterEnergyLogCurl);

                                        $siteInverterEnergyLogResponseData = json_decode($siteInverterEnergyLogResponse);

                                        if($siteInverterEnergyLogResponseData && isset($siteInverterEnergyLogResponseData->data)) {

                                            $siteInverterEnergyLogFinalData = $siteInverterEnergyLogResponseData->data;

                                            foreach((array)$siteInverterEnergyLogFinalData as $key6 => $finalData3) {

                                                if(isset($finalData3->dataItemMap)) {

                                                    $data = $finalData3->dataItemMap;

                                                    if($lastRecordDate == date('Y-m-d')) {
                                                        
                                                        $todayLastTime = InverterEnergyLog::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $gridMeter])->whereDate('collect_time', $lastRecordDate)->exists() ? InverterEnergyLog::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $gridMeter])->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:00:00');
                                                    
                                                        if($finalData3->collectTime > strtotime($todayLastTime)) {
                                                        
                                                            $inverterEnergyLog['plant_id'] = $plantID;
                                                            $inverterEnergyLog['site_id'] = $siteID;
                                                            $inverterEnergyLog['dv_inverter'] = $finalData3->devId;
                                                            $inverterEnergyLog['grid_power'] = $data->active_power;
                                                            $inverterEnergyLog['import_energy'] = $data->reverse_active_cap;
                                                            $inverterEnergyLog['export_energy'] = $data->active_cap;
                                                            $inverterEnergyLog['cron_job_id'] = $inverterEnergyLogMaxCronJobID;
                                                            $inverterEnergyLog['collect_time'] = date('Y-m-d H:i:s', substr($finalData3->collectTime, 0, -3));
                                                            $inverterEnergyLog['created_at'] = $currentTime;
                                                            $inverterEnergyLog['updated_at'] = $currentTime;

                                                            $inverterEnergyLogResponse = InverterEnergyLog::create($inverterEnergyLog);
                                                        }
                                                    }
                                                    else {

                                                        $inverterEnergyLog['plant_id'] = $plantID;
                                                        $inverterEnergyLog['site_id'] = $siteID;
                                                        $inverterEnergyLog['dv_inverter'] = $finalData3->devId;
                                                        $inverterEnergyLog['grid_power'] = $data->active_power;
                                                        $inverterEnergyLog['import_energy'] = $data->reverse_active_cap;
                                                        $inverterEnergyLog['export_energy'] = $data->active_cap;
                                                        $inverterEnergyLog['cron_job_id'] = $inverterEnergyLogMaxCronJobID;
                                                        $inverterEnergyLog['collect_time'] = date('Y-m-d H:i:s', substr($finalData3->collectTime, 0, -3));
                                                        $inverterEnergyLog['created_at'] = $currentTime;
                                                        $inverterEnergyLog['updated_at'] = $currentTime;

                                                        $inverterEnergyLogResponse = InverterEnergyLog::create($inverterEnergyLog);
                                                    }
                                                }
                                            }
                                            print_r('grid inverter log');
                                                    print_r(date("Y-m-d H:i:s"));
                                                    print_r("\n");
                                        }

                                        // $lastRecordDate = date('Y-m-d', strtotime("+1 day",strtotime($lastRecordDate)));
                                        break;
                                    }
                                }
                                print_r('all grid inverter log');
                                                    print_r(date("Y-m-d H:i:s"));
                                                    print_r("\n");*/
                            }

                            //SMART INVERTER GENERATION LOG DATA
                            /*if(!(empty($siteSmartInverterLogStartTime))) {

                                $minTimeSmartInverter = date('Y-m-d', min($siteSmartInverterLogStartTime));
                                // $minTimeSmartInverter = date('Y-m-3');

                                $smartInverterStartTimeData = InverterDetail::select(DB::raw('SUM(inverterPower) as current_generation, SUM(daily_generation) as totalEnergy'), 'collect_time')->where(['plant_id' => $plantID, 'siteId' => $siteID])->whereBetween('collect_time', [date($minTimeSmartInverter.' 00:00:00'),date($minTimeSmartInverter.' 23:59:59')])->groupBy('collect_time')->get();

                                foreach($smartInverterStartTimeData as $generationLogKey => $generationLogData) {

                                    if(GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $generationLogData->collect_time)->exists()){

                                        $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $generationLogData->collect_time)->first();

                                        $generationData->current_generation = $generationLogData->current_generation;
                                        $generationData->totalEnergy = $generationLogData->totalEnergy;
                                        $generationData->save();
                                    }
                                    else {

                                        $generationLog['plant_id'] = $plantID;
                                        $generationLog['siteId'] = $siteID;
                                        $generationLog['current_generation'] = $generationLogData->current_generation;
                                        $generationLog['comm_failed'] = 0;
                                        $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
                                        $generationLog['current_consumption'] = 0;
                                        $generationLog['current_grid'] = 0;
                                        $generationLog['current_irradiance'] = 0;
                                        $generationLog['totalEnergy'] = $generationLogData->totalEnergy;
                                        $generationLog['collect_time'] = $generationLogData->collect_time;
                                        $generationLog['created_at'] = $currentTime;
                                        $generationLog['updated_at'] = $currentTime;

                                        $generationLogResponse = GenerationLog::create($generationLog);
                                    }
                                }
                            }

                            print_r('smart inverter generation log');
                                                    print_r(date("Y-m-d H:i:s"));
                                                    print_r("\n");

                            //EMI INVERTER GENERATION LOG DATA
                            if($plant->plant_has_emi == 'Y') {

                                if(!(empty($siteEMIInverterLogStartTime))) {

                                    $minTimeEMIInverter = date('Y-m-d', min($siteEMIInverterLogStartTime));
                                    // $minTimeEMIInverter = date('Y-m-d');

                                    // $emiInverterStartTimeData = InverterEMIDetail::select(DB::raw('SUM(radiant_line) as radiant_line'), 'collect_time')->where(['plant_id' => $plantID, 'site_id' => $siteID])->whereBetween('collect_time', [date($minTimeEMIInverter.' 00:00:00'),date('Y-m-d 23:59:59')])->groupBy('collect_time')->get();
                                    $emiInverterStartTimeData = InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID])->whereBetween('collect_time', [date($minTimeEMIInverter.' 00:00:00'),date($minTimeEMIInverter.' 23:59:59')])->groupBy('collect_time')->get();
					
                                    foreach($emiInverterStartTimeData as $emiLogKey => $emiLogData) {

                                        if(GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $emiLogData->collect_time)->exists()){

                                            $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $emiLogData->collect_time)->first();

                                            $generationData->current_irradiance = $emiLogData->radiant_line;
                                            $generationData->save();
                                        }
                                        else {

                                            $generationLog['plant_id'] = $plantID;
                                            $generationLog['siteId'] = $siteID;
                                            $generationLog['current_generation'] = 0;
                                            $generationLog['comm_failed'] = 0;
                                            $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
                                            $generationLog['current_consumption'] = 0;
                                            $generationLog['current_grid'] = 0;
                                            $generationLog['current_irradiance'] = $emiLogData->radiant_line;
                                            $generationLog['totalEnergy'] = 0;
                                            $generationLog['collect_time'] = $emiLogData->collect_time;
                                            $generationLog['created_at'] = $currentTime;
                                            $generationLog['updated_at'] = $currentTime;

                                            $generationLogResponse = GenerationLog::create($generationLog);
                                        }
                                    }
                                }
                            }

                            print_r('smart inverter emi log');
                                                    print_r(date("Y-m-d H:i:s"));
                                                    print_r("\n");

                            //Grid INVERTER GENERATION LOG DATA
                            if(!(empty($siteGridInverterLogStartTime))) {

                                $minTimeGridInverter = date('Y-m-d', min($siteGridInverterLogStartTime));
                                // $minTimeGridInverter = date('Y-m-d');

                                $gridInverterStartTimeData = InverterEnergyLog::select(DB::raw('SUM(grid_power) as grid_power'), 'collect_time')->where(['plant_id' => $plantID, 'site_id' => $siteID])->whereBetween('collect_time', [date($minTimeGridInverter.' 00:00:00'),date($minTimeGridInverter.' 23:59:59')])->groupBy('collect_time')->get();
                                
                                foreach($gridInverterStartTimeData as $gridLogKey => $gridLogData) {

                                    if(GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $gridLogData->collect_time)->exists()){

                                        $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $gridLogData->collect_time)->first();

                                        $generationData->current_consumption = ($generationData->current_generation + (($gridLogData->grid_power / 1000) * (-1))) > 0 ? ($generationData->current_generation + (($gridLogData->grid_power / 1000) * (-1))) : 0;
                                        $generationData->current_grid = (($gridLogData->grid_power / 1000) * (-1));
                                        $generationData->save();
                                    }
                                    else {

                                        $generationLog['plant_id'] = $plantID;
                                        $generationLog['siteId'] = $siteID;
                                        $generationLog['current_generation'] = 0;
                                        $generationLog['comm_failed'] = 0;
                                        $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
                                        $generationLog['current_consumption'] = (0 + ((($gridLogData->grid_power / 1000) * (-1)))) > 0 ? (0 + ((($gridLogData->grid_power / 1000) * (-1)))) : 0;
                                        $generationLog['current_grid'] = (($gridLogData->grid_power / 1000) * (-1));
                                        $generationLog['current_irradiance'] = 0;
                                        $generationLog['totalEnergy'] = 0;
                                        $generationLog['collect_time'] = $gridLogData->collect_time;
                                        $generationLog['created_at'] = $currentTime;
                                        $generationLog['updated_at'] = $currentTime;

                                        $generationLogResponse = GenerationLog::create($generationLog);
                                    }
                                }
                            }

                            print_r('smart inverter grid log');
                                                    print_r(date("Y-m-d H:i:s"));
                                                    print_r("\n");*/
							
                        }
                    }

                    /*//PLANT PROCESSED CURRENT DATA
                    $currentGeneration = 0;
                    $currentConsumption = 0;
                    $currentGrid = 0;
                    $currentIrradiance = 0;
                    $totalEnergy = 0;
                    $processedCurrentData['comm_failed'] = 0;

                    if(!(empty($siteAllInverterLogStartTime))) {

                        $minTimeInverter = date('Y-m-d', min($siteAllInverterLogStartTime));
                        $maxTimeInverter = date('Y-m-d', max($siteAllInverterLogStartTime));

                        $generationLogInverterStartTimeData = GenerationLog::select(DB::raw('SUM(current_generation) as current_generation, SUM(current_consumption) as current_consumption, SUM(current_grid) as current_grid, SUM(current_irradiance) as current_irradiance, SUM(totalEnergy) as totalEnergy'), 'collect_time')->where(['plant_id' => $plantID])->whereBetween('collect_time', [date($minTimeInverter.' 00:00:00'),date('Y-m-d 23:59:59')])->groupBy('collect_time')->get();

                        foreach($generationLogInverterStartTimeData as $key45 => $processedData) {

                            if(ProcessedCurrentVariable::where(['plant_id' => $plantID])->where('collect_time', $processedData->collect_time)->exists()) {

                                $processedCurrentData['plant_id'] = $plantID;
                                $processedCurrentData['current_generation'] = $processedData->current_generation;
                                $processedCurrentData['current_consumption'] = $processedData->current_consumption;
                                $processedCurrentData['current_grid'] = abs($processedData->current_grid);
                                $processedCurrentData['grid_type'] = $processedData->current_grid >= 0 ? '+ve' : '-ve';
                                $processedCurrentData['current_irradiance'] = $processedData->current_irradiance;
                                $processedCurrentData['totalEnergy'] = $processedData->totalEnergy ? $processedData->totalEnergy : 0;
                                $processedCurrentData['current_saving'] = $processedData->current_generation * (double)$plant->benchmark_price;
                                $processedCurrentData['processed_cron_job_id'] = $processedMaxCronJobID;
                                $processedCurrentData['collect_time'] = $processedData->collect_time;
                                $processedCurrentData['created_at'] = $currentTime;
                                $processedCurrentData['updated_at'] = $currentTime;

                                $processedCurrentDataExist = ProcessedCurrentVariable::where(['plant_id' => $plantID])->where('collect_time', $processedData->collect_time)->first();

                                $processedCurrentDataResponse = $processedCurrentDataExist->fill($processedCurrentData)->save();
                            }

                            else {

                                $processedCurrentData['plant_id'] = $plantID;
                                $processedCurrentData['current_generation'] = $processedData->current_generation;
                                $processedCurrentData['current_consumption'] = $processedData->current_consumption;
                                $processedCurrentData['current_grid'] = abs($processedData->current_grid);
                                $processedCurrentData['grid_type'] = $processedData->current_grid >= 0 ? '+ve' : '-ve';
                                $processedCurrentData['current_irradiance'] = $processedData->current_irradiance;
                                $processedCurrentData['totalEnergy'] = $processedData->totalEnergy ? $processedData->totalEnergy : 0;
                                $processedCurrentData['current_saving'] = $processedData->current_generation * (double)$plant->benchmark_price;
                                $processedCurrentData['processed_cron_job_id'] = $processedMaxCronJobID;
                                $processedCurrentData['collect_time'] = $processedData->collect_time;
                                $processedCurrentData['created_at'] = $currentTime;
                                $processedCurrentData['updated_at'] = $currentTime;

                                $processedCurrentDataResponse = ProcessedCurrentVariable::create($processedCurrentData);
                            }
                        }

                        while(strtotime($minTimeInverter) != strtotime(date('Y-m-d', strtotime("+1 day", strtotime($maxTimeInverter))))) {

                            $plantDataDateToday = $minTimeInverter;
                            $plantDataDateYesterday = date('Y-m-d', strtotime('-1 day', strtotime($minTimeInverter)));

                            $plantDailyTotalGeneration = 0;
                            $plantDailyTotalBuyEnergy = 0;
                            $plantDailyTotalIrradiance = 0;
                            $plantDailyTotalSellEnergy = 0;
    
                            $plantInverterListData = SiteInverterDetail::where('plant_id', $plantID)->get();

                            foreach($plantInverterListData as $invListData) {

                                if($invListData->dv_inverter_type == 1) {

                                    $plantDailyTotalGeneration += DailyInverterDetail::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first()->daily_generation : 0;
                                }
                                else if($invListData->dv_inverter_type == 10) {

                                    $plantDailyTotalIrradiance += InverterEMIDetail::where(['plant_id' => $plantID, 'dv_inverter' => $invListData->dv_inverter])->whereDate('collect_time', $plantDataDateToday)->exists() ? (double)(InverterEMIDetail::where(['plant_id' => $plantID, 'dv_inverter' => $invListData->dv_inverter])->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->first()->radiant_total) * (double)($irradianceValue) : 0;
                                }
                                else if($invListData->dv_inverter_type == 17) {

                                    $inverterEnergyTodayImportData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->exists() ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->whereNotNull('import_energy')->first()->import_energy : 0;
                                    $inverterEnergyYesterdayImportData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->exists() ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->whereNotNull('import_energy')->first()->import_energy : 0;
                            
                                    $inverterEnergyTodayExportData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->exists() ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->whereNotNull('export_energy')->first()->export_energy : 0;
                                    $inverterEnergyYesterdayExportData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->exists() ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->whereNotNull('export_energy')->first()->export_energy : 0;
                    
                                    $plantDailyTotalBuyEnergy += (double)$inverterEnergyTodayImportData - (double)$inverterEnergyYesterdayImportData;
                                    $plantDailyTotalSellEnergy += (double)$inverterEnergyTodayExportData - (double)$inverterEnergyYesterdayExportData;
                                }
                            }
                            
                            //PLANT DAILY DATA
                            $dailyProcessed['plant_id'] = $plantID;
                            $dailyProcessed['dailyGeneration'] = $plantDailyTotalGeneration;
                            $dailyProcessed['dailyGridPower'] = $plantDailyTotalBuyEnergy > $plantDailyTotalSellEnergy ? $plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy : $plantDailyTotalSellEnergy - $plantDailyTotalBuyEnergy;
                            $dailyProcessed['dailyBoughtEnergy'] = $plantDailyTotalBuyEnergy;
                            $dailyProcessed['dailySellEnergy'] = $plantDailyTotalSellEnergy;
                            $dailyProcessed['dailyMaxSolarPower'] = 0;
                            $dailyProcessed['dailyConsumption'] = $plantDailyTotalGeneration + ($plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy);
                            $dailyProcessed['dailySaving'] = (double)$plantDailyTotalGeneration * (double)$plant->benchmark_price;
                            $dailyProcessed['dailyIrradiance'] = 0;
                            $dailyProcessed['updated_at'] = $currentTime;
    
                            $dailyProcessedPlantDetailExist = DailyProcessedPlantDetail::where('plant_id',$plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first();
                            
                            if($dailyProcessedPlantDetailExist){
    
                                $dailyProcessedPlantDetailInsertionResponce =  $dailyProcessedPlantDetailExist->fill($dailyProcessed)->save();
                            }
                            else {
    
                                $dailyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($plantDataDateToday));
                                $dailyProcessedPlantDetailInsertionResponce = DailyProcessedPlantDetail::create($dailyProcessed);
                            }

                            //PLANT DAILY EMI DATA
                            $dailyEMIProcessed['plant_id'] = $plantID;
                            $dailyEMIProcessed['daily_irradiance'] = $plantDailyTotalIrradiance;
                            $dailyEMIProcessed['updated_at'] = $currentTime;

                            $dailyProcessedPlantEMIDetailExist = DailyProcessedPlantEMIDetail::where('plant_id',$plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first();
                      
                            if($dailyProcessedPlantEMIDetailExist) {

                                $dailyProcessedPlantEMIDetailInsertionResponce =  $dailyProcessedPlantEMIDetailExist->fill($dailyEMIProcessed)->save();
                            }
                            else {

                                $dailyEMIProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($plantDataDateToday));
                                $dailyProcessedPlantEMIDetailInsertionResponce = DailyProcessedPlantEMIDetail::create($dailyEMIProcessed);
                            }
                            
                            $minTimeInverter = date('Y-m-d', strtotime("+1 day", strtotime($minTimeInverter)));
                        }
    
                        $logYear = date('Y', strtotime($minTimeInverter));
                        $logMonth = date('m', strtotime($minTimeInverter));
    
                        //PLANT MONTHLY DATA
                        $plantDailyGenerationDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyGeneration');
                        $plantDailyConsumptionDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyConsumption');
                        $plantDailyGridDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyGridPower');
                        $plantDailyBoughtDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyBoughtEnergy');
                        $plantDailySellDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailySellEnergy');
                        $plantDailySavingDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailySaving');
                        $plantDailyIrradianceDataSum = DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('daily_irradiance');
    
                        $monthlyProcessed['plant_id'] = $plantID;
                        $monthlyProcessed['monthlyGeneration'] = $plantDailyGenerationDataSum;
                        $monthlyProcessed['monthlyConsumption'] = $plantDailyConsumptionDataSum;
                        $monthlyProcessed['monthlyGridPower'] = $plantDailyGridDataSum;
                        $monthlyProcessed['monthlyBoughtEnergy'] = $plantDailyBoughtDataSum;
                        $monthlyProcessed['monthlySellEnergy'] = $plantDailySellDataSum;
                        $monthlyProcessed['monthlySaving'] = $plantDailySavingDataSum;
                        $monthlyProcessed['updated_at'] = $currentTime;
    
                        $monthlyProcessedPlantDetailExist = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->orderBy('created_at', 'DESC')->first();
    
                        if ($monthlyProcessedPlantDetailExist) {
    
                            $monthlyProcessedPlantDetailResponse = $monthlyProcessedPlantDetailExist->fill($monthlyProcessed)->save();
                        } 
                        else {
    
                            $monthlyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
                            $monthlyProcessedPlantDetailResponse = MonthlyProcessedPlantDetail::create($monthlyProcessed);
                        }

                        //PLANT MONTHLY EMI DATA
                        $monthlyEMIProcessed['plant_id'] = $plantID;
                        $monthlyEMIProcessed['monthly_irradiance'] = $plantDailyIrradianceDataSum;
                        $monthlyEMIProcessed['updated_at'] = $currentTime;

                        $monthlyProcessedPlantEMIDetailExist = MonthlyProcessedPlantEMIDetail::where('plant_id',$plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->orderBy('created_at', 'DESC')->first();

                        if($monthlyProcessedPlantEMIDetailExist) {

                            $monthlyProcessedPlantEMIDetailResponse =  $monthlyProcessedPlantEMIDetailExist->fill($monthlyEMIProcessed)->save();
                        }
                        else {

                            $monthlyEMIProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
                            $monthlyProcessedPlantEMIDetailResponse = MonthlyProcessedPlantEMIDetail::create($monthlyEMIProcessed);
                        }
    
                        //PLANT YEARLY DATA
                        $plantmonthlyGenerationDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlyGeneration');
                        $plantmonthlyConsumptionDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlyConsumption');
                        $plantmonthlyGridDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlyGridPower');
                        $plantmonthlyBoughtDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlyBoughtEnergy');
                        $plantmonthlySellDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlySellEnergy');
                        $plantmonthlySavingDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlySaving');
    
                        $yearlyProcessed['plant_id'] = $plantID;
                        $yearlyProcessed['yearlyGeneration'] = $plantmonthlyGenerationDataSum;
                        $yearlyProcessed['yearlyConsumption'] = $plantmonthlyConsumptionDataSum;
                        $yearlyProcessed['yearlyGridPower'] = $plantmonthlyGridDataSum;
                        $yearlyProcessed['yearlyBoughtEnergy'] = $plantmonthlyBoughtDataSum;
                        $yearlyProcessed['yearlySellEnergy'] = $plantmonthlySellDataSum;
                        $yearlyProcessed['yearlySaving'] = $plantmonthlySavingDataSum;
                        $yearlyProcessed['updated_at'] = $currentTime;
    
                        $yearlyProcessedPlantDetailExist = YearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->orderBy('created_at', 'DESC')->first();
    
                        if ($yearlyProcessedPlantDetailExist) {
    
                            $yearlyProcessedPlantDetailResponse = $yearlyProcessedPlantDetailExist->fill($yearlyProcessed)->save();
                        } 
                        else {
    
                            $yearlyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
                            $yearlyProcessedPlantDetailResponse = YearlyProcessedPlantDetail::create($yearlyProcessed);
                        }

                        //PLANT YEARLY EMI DATA
                        $plantMonthlyIrradianceDataSum = MonthlyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthly_irradiance');

                        $yearlyEMIProcessed['plant_id'] = $plantID;
                        $yearlyEMIProcessed['yearly_irradiance'] = $plantMonthlyIrradianceDataSum;
                        $yearlyEMIProcessed['updated_at'] = $currentTime;

                        $yearlyProcessedPlantEMIDetailExist = YearlyProcessedPlantEMIDetail::where('plant_id',$plantID)->whereYear('created_at', $logYear)->orderBy('created_at', 'DESC')->first();

                        if($yearlyProcessedPlantEMIDetailExist){

                            $yearlyProcessedPlantEMIDetailResponse =  $yearlyProcessedPlantEMIDetailExist->fill($yearlyEMIProcessed)->save();
                        }
                        else {

                            $yearlyEMIProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
                            $yearlyProcessedPlantEMIDetailResponse = YearlyProcessedPlantEMIDetail::create($yearlyEMIProcessed);
                        }
                    }

                    print_r('processed current data log');
                    print_r(date("Y-m-d H:i:s"));
                    print_r("\n");

                    //PLANT Total DATA
                    $plantyearlyCurrentPowerDataSum = ProcessedCurrentVariable::where('plant_id', $plantID)->exists() ? ProcessedCurrentVariable::where('plant_id', $plantID)->orderBy('collect_time', 'DESC')->first()->current_generation : 0;
                    $plantyearlyGenerationDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyGeneration');
                    $plantyearlyConsumptionDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyConsumption');
                    $plantyearlyGridDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyGridPower');
                    $plantyearlyBoughtDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyBoughtEnergy');
                    $plantyearlySellDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlySellEnergy');
                    $plantyearlySavingDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlySaving');
                    $plantyearlyIrradianceDataSum = YearlyProcessedPlantEMIDetail::where('plant_id', $plantID)->sum('yearly_irradiance');

                    $totalProcessed['plant_id'] = $plantID;
                    $totalProcessed['plant_total_current_power'] = $plantyearlyCurrentPowerDataSum;
                    $totalProcessed['plant_total_generation'] = $plantyearlyGenerationDataSum;
                    $totalProcessed['plant_total_consumption'] = $plantyearlyConsumptionDataSum;
                    $totalProcessed['plant_total_grid'] = $plantyearlyGridDataSum;
                    $totalProcessed['plant_total_buy_energy'] = $plantyearlyBoughtDataSum;
                    $totalProcessed['plant_total_sell_energy'] = $plantyearlySellDataSum;
                    $totalProcessed['plant_total_saving'] = $plantyearlySavingDataSum;
                    $totalProcessed['plant_total_reduction'] = $plantyearlyGenerationDataSum * $envReductionValue;
                    $totalProcessed['plant_total_irradiance'] = $plantyearlyIrradianceDataSum;
                    $totalProcessed['updated_at'] = $currentTime;

                    $totalProcessedPlantDetailExist = TotalProcessedPlantDetail::where('plant_id', $plantID)->first();

                    if ($totalProcessedPlantDetailExist) {

                        $totalProcessedPlantDetailResponse = $totalProcessedPlantDetailExist->fill($totalProcessed)->save();
                    } 
                    else {

                        $totalProcessed['created_at'] = $currentTime;
                        $totalProcessedPlantDetailResponse = TotalProcessedPlantDetail::create($totalProcessed);
                    }*/
                }
            }

            //PLANT STATUS UPDATE FUNCTION
            $this->plantStatusUpdate();
            print_r('plant status');
            print_r(date("Y-m-d H:i:s"));
            print_r("\n");

        /*}

        catch (Exception $e) {

            return $e->getMessage();
        }*/

    }

    public function getTokenAndSessionID($huaweiAPIBaseURL, $huaweiAPIUserName, $huaweiAPISystemCode, $requestFrom) {

        $loginData = [
            "userName" => $huaweiAPIUserName,
            "systemCode" => $huaweiAPISystemCode,
        ];

        $url = $huaweiAPIBaseURL.'/login';

        $loginCurl = curl_init($url);

        $loginDataEncoded = json_encode($loginData);

        $fp = fopen(__DIR__ . "/cookieFileError.txt", 'w');
        $fp1 = fopen(__DIR__ . "/cookieFileData.txt", 'w');

        chmod(__DIR__ . "/cookieFileError.txt", 0777);
        chmod(__DIR__ . "/cookieFileData.txt", 0777);

        curl_setopt($loginCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($loginCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($loginCurl, CURLOPT_COOKIEFILE, __DIR__ . "/cookieFileData.txt");
        curl_setopt($loginCurl, CURLOPT_COOKIESESSION, 1);
        curl_setopt($loginCurl, CURLOPT_POST, 1);
        curl_setopt($loginCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($loginCurl, CURLINFO_HEADER_OUT, true);
        curl_setopt($loginCurl, CURLOPT_POSTFIELDS, $loginDataEncoded);
        curl_setopt($loginCurl, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
        curl_setopt($loginCurl, CURLOPT_DNS_CACHE_TIMEOUT, 2 );
        curl_setopt($loginCurl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($loginCurl, CURLOPT_VERBOSE, true);
        curl_setopt($loginCurl, CURLOPT_STDERR, $fp);
        curl_setopt($loginCurl, CURLOPT_COOKIEJAR, __DIR__ . "/cookieFileData.txt");

        $data=curl_exec($loginCurl);
        curl_close($loginCurl);

        $lines = file(__DIR__ . "/cookieFileData.txt");

        $tokenLine = '';
        $jSessionLine = '';
        $csrfToken = '';
        $jSessionID = '';

        foreach ($lines as $line_num => $line) {

            if($line_num == 4) {
                $jSessionLine = $line;
            }
            if($line_num == 4) {
                $tokenLine = $line;
            }
        }

        $TokenArr = preg_split("@[\s+　]@u", $tokenLine);
        $sessionIDArr = preg_split("@[\s+　]@u", $jSessionLine);

        if(!empty($TokenArr) && isset($TokenArr[6])) {

            $csrfToken = $TokenArr[6];
        }

        if(!empty($sessionIDArr) && isset($sessionIDArr[6])) {

            $jSessionID = $sessionIDArr[6];
        }

        if($requestFrom == "BUILD_PLANT") {

            return [json_decode($data), $csrfToken, $jSessionID];
        }

        $settingResponse = Setting::where('perimeter', 'huawei_api_cookie')->exists() ? Setting::where('perimeter', 'huawei_api_cookie')->update(['value' => $csrfToken]) : 'eybj';
        $settingResponse = Setting::where('perimeter', 'huawei_session_id')->exists() ? Setting::where('perimeter', 'huawei_session_id')->update(['value' => $jSessionID]) : 'eybj';
    }

    public function getPlantList($huaweiAPIBaseURL, $tokenSessionData) {

        $plantSiteCurl = curl_init();

        curl_setopt_array($plantSiteCurl, array(

            CURLOPT_URL => $huaweiAPIBaseURL.'/getStationList',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'XSRF-TOKEN: '.$tokenSessionData[0],
                'Accept: application/json',
                'Content-Type: application/json',
                'Cookie: JSESSIONID='.$tokenSessionData[1].'; XSRF-TOKEN='.$tokenSessionData[0].'; web-auth=true'
            ),
        ));

        $plantSiteStatusResponse = curl_exec($plantSiteCurl);

        curl_close($plantSiteCurl);

        $plantSiteStatusResponseData = json_decode($plantSiteStatusResponse);

        return $plantSiteStatusResponseData;
    }

    public function getSiteDeviceList($huaweiAPIBaseURL, $tokenSessionData, $siteID) {

        $siteDevListData = [
            "stationCodes" => $siteID,
        ];

        $plantDeviceCurl = curl_init();

        curl_setopt_array($plantDeviceCurl, array(

            CURLOPT_URL => $huaweiAPIBaseURL.'/getDevList',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($siteDevListData),
            CURLOPT_HTTPHEADER => array(
                'XSRF-TOKEN: '.$tokenSessionData[0],
                'Accept: application/json',
                'Content-Type: application/json',
                'Cookie: JSESSIONID='.$tokenSessionData[1].'; XSRF-TOKEN='.$tokenSessionData[0].'; web-auth=true'
            ),
        ));

        $plantSiteStatusResponse = curl_exec($plantDeviceCurl);

        curl_close($plantDeviceCurl);

        $plantSiteStatusResponseData = json_decode($plantSiteStatusResponse);

        return $plantSiteStatusResponseData;
    }

    private function plantStatusUpdate() {

        $plants = DB::table('plants')
                    ->join('plant_sites', 'plants.id', 'plant_sites.plant_id')
                    ->select('plants.*', 'plant_sites.site_id')
                    ->get();

        foreach ($plants as $key => $plant) {

            $updateStatus = array();
            
            $plantStatus = PlantSite::where('plant_id', $plant->id)->get('online_status');

            if($plantStatus->contains('online_status', 'F')) {

                $updateStatus['is_online'] = 'P_Y';
                $updateStatus['faultLevel'] = 1;
            }
            else {

                $updateStatus['faultLevel'] = 0;
            }

            if($plantStatus->contains('online_status', 'P_Y')) {

                $updateStatus['is_online'] = 'P_Y';
            }

            if($plantStatus->contains('online_status', 'Y') && $plantStatus->contains('online_status', 'N')) {

                $updateStatus['is_online'] = 'P_Y';
            }

            else if($plantStatus->contains('online_status', 'Y')) {

                $updateStatus['is_online'] = 'Y';
            }

            else {

                $updateStatus['is_online'] = 'N';
            }

            $plantAlertStatus = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
                                                ->select('fault_alarm_log.*')
                                                ->where('fault_alarm_log.plant_id', $plant->id)
                                                ->where('fault_alarm_log.status', 'Y')
                                                ->where('fault_and_alarms.type', 'Alarm')
                                                ->count();

            if((int)$plantAlertStatus > 0) {

                $updateStatus['alarmLevel'] = 1;
            }

            else {

                $updateStatus['alarmLevel'] = 0;
            }

            $plantRes = Plant::where('id', $plant->id)->update($updateStatus);
        }
    }
}
