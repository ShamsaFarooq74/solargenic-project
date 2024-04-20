<?php

namespace App\Http\Controllers\HardwareAPIData;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Models\SiteInverterDetail;
use Cassandra\Date;
use Illuminate\Http\Request;
use App\Http\Models\Plant;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterMPPTDetail;
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
use App\Http\Models\PlantSite;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\SystemType;
use App\Http\Models\Setting;
use App\Http\Models\Weather;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\AccumulativeProcessedDetail;
use App\Http\Models\TotalProcessedPlantDetail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\NotificationController;
use Carbon\Carbon;
use App\Http\Controllers\LEDController;
use App\Http\Controllers\Admin\CommunicationController;

class SunGrowMPPTController extends Controller
{

    public function sunGrow($globalGenerationLogMaxID = null, $globalProcessedLogMaxID = null, $globalInverterDetailMaxID = null)
    {
        // try {
            date_default_timezone_set('Asia/Karachi');
            $currentTime = date('Y-m-d H:i:s');
            $appKey = Setting::where('perimeter', 'sun_grow_api_app_key')->exists() ? Setting::where('perimeter', 'sun_grow_api_app_key')->first()->value : '3yhg';
            $userAccount = Setting::where('perimeter', 'sun_grow_api_user_account')->exists() ? Setting::where('perimeter', 'sun_grow_api_user_account')->first()->value : '3yhg';
            $userPassword = Setting::where('perimeter', 'sun_grow_api_user_password')->exists() ? Setting::where('perimeter', 'sun_grow_api_user_password')->first()->value : '3yhg';
            $generationLogMaxCronJobID = $globalGenerationLogMaxID;
            $processedMaxCronJobID = $globalProcessedLogMaxID;
            $inverterMaxCronJobID = $globalInverterDetailMaxID + 1;
            $curl = curl_init();

            $userCredentials = [

                "appkey" => $appKey,
                "user_account" => $userAccount,
                "user_password" => $userPassword,
                "login_type" => "1"
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/v1/userService/login',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($userCredentials),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'sys_code: 901',
                    'lang: _en_US',
					'x-access-key: x0qapvhjzzhvy9byj0j38b3en9nacwk9'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $curl = curl_init();
            $responseType = json_decode($response, true);
            $userId = $responseType['result_data']['user_id'];
            $token = $responseType['result_data']['token'];
            $allPlantsData = Plant::where('meter_type', 'SunGrow')->get();
            if($allPlantsData)
            {
            for ($i = 0; $i < count($allPlantsData); $i++) {
                $siteData = $plantSiteList = PlantSite::where('plant_id', $allPlantsData[$i]['id'])->get();
                $siteIdData = 0;
                if ($siteData) {
                    $paginate = 1;
                    for ($k = 0; $k < count($siteData); $k++) {

                        $siteSmartInverterLogStartTime = array();
                        $siteAllInverterLogStartTime = array();

                        $psKeysList = SiteInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('site_id', $siteData[$k]['site_id'])->where('dv_inverter_type', 1)->exists() ? SiteInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('site_id', $siteData[$k]['site_id'])->where('dv_inverter_type', 1)->pluck('dv_inverter')->toArray() : [];

                        //INVERTER LOG
                        foreach($psKeysList as $smartKey => $smartInverter) {

                            $lastRecordTimeStamp = InverterMPPTDetail::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id'], 'dv_inverter' => $smartInverter])->exists() ? InverterMPPTDetail::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id'], 'dv_inverter' => $smartInverter])->orderBy('collect_time', 'DESC')->first()->collect_time : null;

                            if(isset($lastRecordTimeStamp) && $lastRecordTimeStamp) {

                                if(strtotime(date('Y-m-d', strtotime($lastRecordTimeStamp))) == strtotime(date('Y-m-d'))) {

                                    $lastRecordDate = date('Y-m-d', strtotime($lastRecordTimeStamp));
                                }
                                else {

                                    $lastRecordDate = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordTimeStamp)));
                                }
                            }
                            else {

                                $lastRecordDate = $allPlantsData[$i]['data_collect_date'];
                            }

                            $siteSmartInverterLogStartTime[] = strtotime($lastRecordDate);
                            $siteAllInverterLogStartTime[] = strtotime($lastRecordDate);

                            while(strtotime($lastRecordDate) != strtotime(date('Y-m-d', strtotime("+1 days")))) {

                                $collectTime = date('Ymd', strtotime($lastRecordDate));
                                $dailyGenerationData = 0;

                                $siteSmartInverterData = [
                                    
                                    "appkey" => $appKey,
                                    "token" => $token,
                                    "start_time_stamp" => $collectTime."000000",
                                    "end_time_stamp" => $collectTime."235959",
                                    "minute_interval" => "5",
                                    "points" => "p1,p2,p4,p5,p6,p7,p8,p9,p10,p14,p24,p27,p29,p45,p46,p47,p48,p49,p50,p51,p52,p53,p54,p55,p56,p57,p58",
                                    "ps_key" => $smartInverter
                                ];

                                $siteSmartInverterCurl = curl_init();

                                curl_setopt_array($siteSmartInverterCurl, array(

                                    CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/v1/commonService/queryDevicePointMinuteDataList',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($siteSmartInverterData),
                                    CURLOPT_HTTPHEADER => array(
                                        'Content-Type: application/json',
                                        'sys_code: 901',
                                        'lang: _en_US'
                                    ),
                                ));

                                $siteSmartInverterResponse = curl_exec($siteSmartInverterCurl);

                                curl_close($siteSmartInverterCurl);

                                $siteSmartInverterResponseData = json_decode($siteSmartInverterResponse);

                                if($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->result_data)) {

                                    $siteSmartInverterFinalData = $siteSmartInverterResponseData->result_data;

                                    if($siteSmartInverterFinalData) {

                                        foreach($siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {

                                            //INVERTER MPPT DATA
                                            $dataInArray = (array)$smartInverterFinalData;

                                            if($lastRecordDate == date('Y-m-d')) {

                                                $todayLastTime = InverterMPPTDetail::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id'], 'dv_inverter' => $smartInverter])->whereDate('collect_time', $lastRecordDate)->exists() ? InverterMPPTDetail::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id'], 'dv_inverter' => $smartInverter])->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:00:00');

                                                if(strtotime($smartInverterFinalData->time_stamp) > strtotime($todayLastTime)) {

                                                    for($mi = 1; $mi <= 10; $mi++) {

                                                        $inverterMPPTLog = array();
                                                        $mpptVoltageValue = 0;
                                                        $mpptCurrentValue = 0;
    
                                                        if($mi == 1) {
    
                                                            $mpptVoltageValue = isset($smartInverterFinalData->p5) ? $smartInverterFinalData->p5 : 0;
                                                            $mpptCurrentValue = isset($smartInverterFinalData->p6) ? $smartInverterFinalData->p6 : 0;
                                                        }
                                                        else if($mi == 2) {
    
                                                            $mpptVoltageValue = isset($smartInverterFinalData->p7) ? $smartInverterFinalData->p7 : 0;
                                                            $mpptCurrentValue = isset($smartInverterFinalData->p8) ? $smartInverterFinalData->p8 : 0;
                                                        }
                                                        else if($mi == 3) {
    
                                                            $mpptVoltageValue = isset($smartInverterFinalData->p9) ? $smartInverterFinalData->p9 : 0;
                                                            $mpptCurrentValue = isset($smartInverterFinalData->p10) ? $smartInverterFinalData->p10 : 0;
                                                        }
                                                        else if($mi == 4) {
    
                                                            $mpptVoltageValue = isset($smartInverterFinalData->p45) ? $smartInverterFinalData->p45 : 0;
                                                            $mpptCurrentValue = isset($smartInverterFinalData->p46) ? $smartInverterFinalData->p46 : 0;
                                                        }
                                                        else if($mi == 5) {
    
                                                            $mpptVoltageValue = isset($smartInverterFinalData->p47) ? $smartInverterFinalData->p47 : 0;
                                                            $mpptCurrentValue = isset($smartInverterFinalData->p48) ? $smartInverterFinalData->p48 : 0;
                                                        }
                                                        else if($mi == 6) {
    
                                                            $mpptVoltageValue = isset($smartInverterFinalData->p49) ? $smartInverterFinalData->p49 : 0;
                                                            $mpptCurrentValue = isset($smartInverterFinalData->p50) ? $smartInverterFinalData->p50 : 0;
                                                        }
                                                        else if($mi == 7) {
    
                                                            $mpptVoltageValue = isset($smartInverterFinalData->p51) ? $smartInverterFinalData->p51 : 0;
                                                            $mpptCurrentValue = isset($smartInverterFinalData->p52) ? $smartInverterFinalData->p52 : 0;
                                                        }
                                                        else if($mi == 8) {
    
                                                            $mpptVoltageValue = isset($smartInverterFinalData->p53) ? $smartInverterFinalData->p53 : 0;
                                                            $mpptCurrentValue = isset($smartInverterFinalData->p54) ? $smartInverterFinalData->p54 : 0;
                                                        }
                                                        else if($mi == 9) {
    
                                                            $mpptVoltageValue = isset($smartInverterFinalData->p55) ? $smartInverterFinalData->p55 : 0;
                                                            $mpptCurrentValue = isset($smartInverterFinalData->p56) ? $smartInverterFinalData->p56 : 0;
                                                        }
                                                        else if($mi == 10) {
    
                                                            $mpptVoltageValue = isset($smartInverterFinalData->p57) ? $smartInverterFinalData->p57 : 0;
                                                            $mpptCurrentValue = isset($smartInverterFinalData->p58) ? $smartInverterFinalData->p58 : 0;
                                                        }
    
                                                        $inverterMPPTLog['plant_id'] = $allPlantsData[$i]['id'];
                                                        $inverterMPPTLog['site_id'] = $siteData[$k]['site_id'];
                                                        $inverterMPPTLog['dv_inverter'] = $smartInverter;
                                                        $inverterMPPTLog['mppt_number'] = $mi;
                                                        $inverterMPPTLog['mppt_voltage'] = $mpptVoltageValue;
                                                        $inverterMPPTLog['mppt_current'] = $mpptCurrentValue;
                                                        $inverterMPPTLog['mppt_power'] = (((double)$mpptVoltageValue * (double)$mpptCurrentValue) / 1000);
                                                        $inverterMPPTLog['collect_time'] = date('Y-m-d H:i:s', strtotime($smartInverterFinalData->time_stamp));
    
                                                        $inverterMPPTResponse = InverterMPPTDetail::create($inverterMPPTLog);
                                                    }
                                                }
                                            }
                                            else {

                                                for($mi = 1; $mi <= 10; $mi++) {

                                                    $inverterMPPTLog = array();
                                                    $mpptVoltageValue = 0;
                                                    $mpptCurrentValue = 0;

                                                    if($mi == 1) {

                                                        $mpptVoltageValue = isset($smartInverterFinalData->p5) ? $smartInverterFinalData->p5 : 0;
                                                        $mpptCurrentValue = isset($smartInverterFinalData->p6) ? $smartInverterFinalData->p6 : 0;
                                                    }
                                                    else if($mi == 2) {

                                                        $mpptVoltageValue = isset($smartInverterFinalData->p7) ? $smartInverterFinalData->p7 : 0;
                                                        $mpptCurrentValue = isset($smartInverterFinalData->p8) ? $smartInverterFinalData->p8 : 0;
                                                    }
                                                    else if($mi == 3) {

                                                        $mpptVoltageValue = isset($smartInverterFinalData->p9) ? $smartInverterFinalData->p9 : 0;
                                                        $mpptCurrentValue = isset($smartInverterFinalData->p10) ? $smartInverterFinalData->p10 : 0;
                                                    }
                                                    else if($mi == 4) {

                                                        $mpptVoltageValue = isset($smartInverterFinalData->p45) ? $smartInverterFinalData->p45 : 0;
                                                        $mpptCurrentValue = isset($smartInverterFinalData->p46) ? $smartInverterFinalData->p46 : 0;
                                                    }
                                                    else if($mi == 5) {

                                                        $mpptVoltageValue = isset($smartInverterFinalData->p47) ? $smartInverterFinalData->p47 : 0;
                                                        $mpptCurrentValue = isset($smartInverterFinalData->p48) ? $smartInverterFinalData->p48 : 0;
                                                    }
                                                    else if($mi == 6) {

                                                        $mpptVoltageValue = isset($smartInverterFinalData->p49) ? $smartInverterFinalData->p49 : 0;
                                                        $mpptCurrentValue = isset($smartInverterFinalData->p50) ? $smartInverterFinalData->p50 : 0;
                                                    }
                                                    else if($mi == 7) {

                                                        $mpptVoltageValue = isset($smartInverterFinalData->p51) ? $smartInverterFinalData->p51 : 0;
                                                        $mpptCurrentValue = isset($smartInverterFinalData->p52) ? $smartInverterFinalData->p52 : 0;
                                                    }
                                                    else if($mi == 8) {

                                                        $mpptVoltageValue = isset($smartInverterFinalData->p53) ? $smartInverterFinalData->p53 : 0;
                                                        $mpptCurrentValue = isset($smartInverterFinalData->p54) ? $smartInverterFinalData->p54 : 0;
                                                    }
                                                    else if($mi == 9) {

                                                        $mpptVoltageValue = isset($smartInverterFinalData->p55) ? $smartInverterFinalData->p55 : 0;
                                                        $mpptCurrentValue = isset($smartInverterFinalData->p56) ? $smartInverterFinalData->p56 : 0;
                                                    }
                                                    else if($mi == 10) {

                                                        $mpptVoltageValue = isset($smartInverterFinalData->p57) ? $smartInverterFinalData->p57 : 0;
                                                        $mpptCurrentValue = isset($smartInverterFinalData->p58) ? $smartInverterFinalData->p58 : 0;
                                                    }

                                                    $inverterMPPTLog['plant_id'] = $allPlantsData[$i]['id'];
                                                    $inverterMPPTLog['site_id'] = $siteData[$k]['site_id'];
                                                    $inverterMPPTLog['dv_inverter'] = $smartInverter;
                                                    $inverterMPPTLog['mppt_number'] = $mi;
                                                    $inverterMPPTLog['mppt_voltage'] = $mpptVoltageValue;
                                                    $inverterMPPTLog['mppt_current'] = $mpptCurrentValue;
                                                    $inverterMPPTLog['mppt_power'] = (((double)$mpptVoltageValue * (double)$mpptCurrentValue) / 1000);
                                                    $inverterMPPTLog['collect_time'] = date('Y-m-d H:i:s', strtotime($smartInverterFinalData->time_stamp));

                                                    $inverterMPPTResponse = InverterMPPTDetail::create($inverterMPPTLog);
                                                }
                                            }
                                        }
                                    }
                                }    

                                break;
                            }
                        }
                    }
                }
            }
        }

        // } catch (Exception $e) {

        //     return $e->getMessage();
        // }
    }
}
