<?php

namespace App\Http\Controllers\HardwareAPIData;

use App\Http\Controllers\HardwareAPIData\SungrowFaultAndAlarmController;
use App\Http\Models\CronJobTime;
use App\Http\Models\InverterStatusCode;
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
use App\Http\Controllers\Admin\PlantsController;
use App\Http\Models\InverterEnergyLog;

class SunGrowController extends Controller
{

    public function sunGrow($globalGenerationLogMaxID = 0, $globalProcessedLogMaxID = 0, $globalInverterDetailMaxID = 0)
    {

        try {
            date_default_timezone_set('Asia/Karachi');
            $currentTime = date('Y-m-d H:i:s');
            $cronJobStartTime = date('Y-m-d H:i:s');
//            $appKey = Setting::where('perimeter', 'sun_grow_api_app_key')->exists() ? Setting::where('perimeter', 'sun_grow_api_app_key')->first()->value : '3yhg';
//            $userAccount = Setting::where('perimeter', 'sun_grow_api_user_account')->exists() ? Setting::where('perimeter', 'sun_grow_api_user_account')->first()->value : '3yhg';
//            $userPassword = Setting::where('perimeter', 'sun_grow_api_user_password')->exists() ? Setting::where('perimeter', 'sun_grow_api_user_password')->first()->value : '3yhg';
            print_r('Crone Job Start Time');
            print_r(date("Y-m-d H:i:s"));
            print_r("\n");
            $appKeyData = Setting::where('perimeter', 'sun_grow_api_app_key')->first();
            $appKey = '3yhg';
            $userAccount = '3yhg';
            $userPassword = '3yhg';
            if($appKeyData)
            {
                $appKey = $appKeyData->value;
            }
            $userAccountData = Setting::where('perimeter', 'sun_grow_api_user_account')->first();
            if($userAccountData)
            {
                $userAccount = $userAccountData->value;
            }
            $userPasswordData = Setting::where('perimeter', 'sun_grow_api_user_password')->first();
            if($userPasswordData)
            {
                $userPassword = $userPasswordData->value;
            }
            $generationLogMaxCronJobID = $globalGenerationLogMaxID;
            $processedMaxCronJobID = $globalProcessedLogMaxID;
            $inverterMaxCronJobID = $globalInverterDetailMaxID + 1;
//            $accessKey = 'x0qapvhjzzhvy9byj0j38b3en9nacwk9';
            $accessKey = 'rxyrr4gt34kqx4ggdrdg2vs82k234zny';
//            $appKey = '3A7715CEE8399D0FA61B23248997C093';
            $curl = curl_init();

            $userCredentials = [

                "appkey" => $appKey,
                "user_account" => $userAccount,
                "user_password" => $userPassword,
                "login_type" => "1"
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/login',
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
                    'x-access-key: ' . $accessKey,
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $curl = curl_init();
            $responseType = json_decode($response, true);

            $userId = $responseType['result_data']['user_id'];
            $token = $responseType['result_data']['token'];

            $allPlantsData = Plant::where('meter_type', 'SunGrow')->get();

            if ($allPlantsData) {

                for ($i = 0; $i < count($allPlantsData); $i++) {
                    $siteData = $plantSiteList = PlantSite::where('plant_id', $allPlantsData[$i]['id'])->get();
//                     return count($siteData);
                    $siteIdData = 0;
                    if ($siteData) {
                        $paginate = 1;
                        for ($k = 0; $k < count($siteData); $k++) {

                            //SITE STATUS DATA
                            $plantSiteStatusData = [
                                "appkey" => $appKey,
                                "token" => $token,
                                "user_id" => $userId,
                                "size" => "1000",
                                "curPage" => "1"
                            ];

                            $plantSiteStatusCurl = curl_init();

                            curl_setopt_array($plantSiteStatusCurl, array(

                                CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/getPowerStationList',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => json_encode($plantSiteStatusData),
                                CURLOPT_HTTPHEADER => array(
                                    'Content-Type: application/json',
                                    'sys_code: 901',
                                    'lang: _en_US',
                                    'x-access-key: ' . $accessKey,
                                ),
                            ));

                            $plantSiteStatusResponse = curl_exec($plantSiteStatusCurl);

                            curl_close($plantSiteStatusCurl);

                            $plantSiteStatusResponseData = json_decode($plantSiteStatusResponse);
//                            return json_encode($plantSiteStatusResponseData);

                            if ($plantSiteStatusResponseData && isset($plantSiteStatusResponseData->result_data)) {

                                $plantSiteStatusFinalData = $plantSiteStatusResponseData->result_data;

                                if ($plantSiteStatusFinalData && isset($plantSiteStatusFinalData->pageList)) {

                                    $plantSiteStatusFinalDataPage = $plantSiteStatusFinalData->pageList;

                                    if ($plantSiteStatusFinalDataPage) {

                                        foreach ((array)$plantSiteStatusFinalDataPage as $key8 => $finalData2) {
                                            //  return $key8;

                                            $siteStatusString = '';
                                            // return json_encode($finalData2);
                                            // if($key8 == 3){
                                            if (isset($finalData2->ps_status)) {

                                                if($finalData2->ps_id == $siteData[$k]['site_id'])
                                                {
                                                    // return $finalData2->ps_status;
                                                    if ($finalData2->ps_status == 1) {

                                                        //    return "in if";

                                                        if ($finalData2->ps_fault_status == 1) {

                                                            $siteStatusString = 'F';
                                                        } else if ($finalData2->ps_fault_status == 2) {

                                                            $siteStatusString = 'A';
                                                        } else {

                                                            $siteStatusString = 'Y';
                                                        }
                                                    } else if ($finalData2->ps_status == 0) {

                                                        $siteStatusString = 'N';
                                                        // return "in exact condition";
                                                    }
                                                    // return $siteStatusString;
                                                    $siteStatusUpdateResponse = PlantSite::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id']])->update(['online_status' => $siteStatusString]);

                                                }
                                                // return $siteStatusString;
                                                //SITE STATUS UPDATE DATA
                                            }
                                            // }
                                        }
                                    }
                                }
                            }
                            // return "done";
                            $siteSmartInverterLogStartTime = array();
                            $siteAllInverterLogStartTime = array();
                            $ps_id = $siteData[$k]['site_id'];
                            $Plant_ID = $allPlantsData[$i]['id'];
//                            $SungrowAlertController = new SungrowFaultAndAlarmController();
//                            $alertData = $SungrowAlertController->AlarmAndFault($appKey, $token, $ps_id, $accessKey, $Plant_ID);
                            // return    $alertData;
                            $siteDetails = [
                                "appkey" => $appKey,
                                "token" => $token,
                                "ps_id" => $siteData[$k]['site_id'],
                                "curPage" => 1,
                                "size" => 1000,
                            ];
                            $curl = curl_init();

                            curl_setopt_array($curl, array(

                                CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/getDeviceList',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => json_encode($siteDetails),
                                CURLOPT_HTTPHEADER => array(
                                    'Content-Type: application/json',
                                    'sys_code: 901',
                                    'lang: _en_US',
                                    'x-access-key: ' . $accessKey,
                                ),
                            ));
                            $plantList = curl_exec($curl);
                            $plantDeviceList = json_decode($plantList, true);
                            $psKeysList = [];
                            $pskeyData = '';
//return $plantDeviceList;
                            if ($plantDeviceList['result_data']) {
                                for ($j = 0; $j < count($plantDeviceList['result_data']['pageList']); $j++) {
                                    if ($plantDeviceList['result_data']['pageList'][$j]['device_type'] == 1) {
//                                        return $plantDeviceList['result_data']['pageList'];
                                        if (InverterSerialNo::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id'], 'dv_inverter' => $plantDeviceList['result_data']['pageList'][$j]['ps_key']])->exists()) {
                                            $invertSerialNo = InverterSerialNo::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id'], 'dv_inverter' => $plantDeviceList['result_data']['pageList'][$j]['ps_key']])->first();
                                            $invertSerialNo->plant_id = $allPlantsData[$i]['id'];
                                            $invertSerialNo->site_id = $siteData[$k]['site_id'];
                                            $invertSerialNo->dv_inverter = $plantDeviceList['result_data']['pageList'][$j]['ps_key'];
                                            $invertSerialNo->dv_inverter_serial_no = $plantDeviceList['result_data']['pageList'][$j]['device_sn'];
                                            $invertSerialNo->dv_inverter_model = $plantDeviceList['result_data']['pageList'][$j]['device_model_code'];
                                            $invertSerialNo->update();
                                        } else {
                                            $invertSerialNo = new InverterSerialNo();
                                            $invertSerialNo->plant_id = $allPlantsData[$i]['id'];
                                            $invertSerialNo->site_id = $siteData[$k]['site_id'];
                                            $invertSerialNo->dv_inverter = $plantDeviceList['result_data']['pageList'][$j]['ps_key'];
                                            $invertSerialNo->dv_inverter_serial_no = $plantDeviceList['result_data']['pageList'][$j]['device_sn'];
                                            $invertSerialNo->dv_inverter_model = $plantDeviceList['result_data']['pageList'][$j]['device_model_code'];
                                            $invertSerialNo->save();
                                        }

                                        array_push($psKeysList, $plantDeviceList['result_data']['pageList'][$j]['ps_key']);
                                    }
                                    elseif ($plantDeviceList['result_data']['pageList'][$j]['device_type'] == 7)
                                    {
                                        $pskeyData = $plantDeviceList['result_data']['pageList'][$j]['ps_key'];
                                    }

                                    if (SiteInverterDetail::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id'], 'dv_inverter' => $plantDeviceList['result_data']['pageList'][$j]['ps_key']])->exists()) {
                                        $invertSerialNo = SiteInverterDetail::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id'], 'dv_inverter' => $plantDeviceList['result_data']['pageList'][$j]['ps_key']])->first();
                                        $invertSerialNo->plant_id = $allPlantsData[$i]['id'];
                                        $invertSerialNo->site_id = $siteData[$k]['site_id'];
                                        $invertSerialNo->dv_inverter = $plantDeviceList['result_data']['pageList'][$j]['ps_key'];
                                        $invertSerialNo->dv_inverter_serial_no = $plantDeviceList['result_data']['pageList'][$j]['device_sn'];
                                        $invertSerialNo->dv_inverter_type = $plantDeviceList['result_data']['pageList'][$j]['device_type'];
                                        // $invertSerialNo->dv_installed_dc_power = 1;
                                        $invertSerialNo->update();
                                    } else {
                                        $invertSerialNo = new SiteInverterDetail();
                                        $invertSerialNo->plant_id = $allPlantsData[$i]['id'];
                                        $invertSerialNo->site_id = $siteData[$k]['site_id'];
                                        $invertSerialNo->dv_inverter = $plantDeviceList['result_data']['pageList'][$j]['ps_key'];
                                        $invertSerialNo->dv_inverter_serial_no = $plantDeviceList['result_data']['pageList'][$j]['device_sn'];
                                        $invertSerialNo->dv_inverter_type = $plantDeviceList['result_data']['pageList'][$j]['device_type'];
                                        // $invertSerialNo->dv_installed_dc_power = 1;
                                        $invertSerialNo->save();
                                    }

                                }
                            }
//                            return [$psKeysList,$pskeyData];

                            $invData =
                                [
                                    "appkey" => $appKey,
                                    "token" => $token,
                                    "ps_key_list" => $psKeysList
                                ];
                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/getPVInverterRealTimeData',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => json_encode($invData),
                                CURLOPT_HTTPHEADER => array(
                                    'Content-Type: application/json',
                                    'sys_code: 901',
                                    'lang: _en_US',
                                    'x-access-key: ' . $accessKey,
                                ),
                            ));

                            $deviceRealTimeData = curl_exec($curl);
                            curl_close($curl);
                            $inverterDetail = json_decode($deviceRealTimeData, true);
                            $devSungrowStatus = 'Default';
                            $devStatusArray = [];
                            $devfaultStatus = [];
//                            return $inverterDetail;

                            if ($deviceRealTimeData) {
                                $inverterDetail = json_decode($deviceRealTimeData, true);
                                $deviceInverterPointList = $inverterDetail['result_data']['device_point_list'];
                                if($inverterDetail['result_data']['device_point_list']){
                                    for ($d = 0; $d < count($deviceInverterPointList); $d++) {
//                                        return $deviceInverterPointList[$d]['device_point'];
                                        if ($deviceInverterPointList[$d]['device_point']) {
                                            array_push($devStatusArray, $deviceInverterPointList[$d]['device_point']['dev_status']);
                                            array_push($devfaultStatus, $deviceInverterPointList[$d]['device_point']['dev_fault_status']);
                                        }
                                    }
                                }

                            }
                            if ($devStatusArray) {
                                $devStatusArray = array_unique($devStatusArray);
//                                return $devStatusArray[0];
                                if ($devStatusArray[0] == 1) {
                                    $devSungrowStatus = '1';
                                } elseif ($devStatusArray[0] == 2) {
                                    $devSungrowStatus = '2';
                                }

                            }
                            if ($devfaultStatus) {
                                $devfaultStatus = array_unique($devfaultStatus);
                                if (($devfaultStatus[0] == 1 && $devStatusArray[0] == 1) || ($devfaultStatus[0] == 2 && $devStatusArray[0] == 1)) {
                                    $devSungrowStatus = '3';
                                }
                            }
//                            return $devSungrowStatus;
                            $inverterStatusCode = InverterStatusCode::where('plant_name','sun-grow')->where('code',$devSungrowStatus)->first();
//                            return [$devSungrowStatus,$inverterStatusCode];
                            if($inverterStatusCode)
                            {
                                $devSungrowStatus = $inverterStatusCode->description;
                            }
//                            return $plantDeviceList['result_data'];
                            if ($plantDeviceList['result_data']) {
                                for ($j = 0; $j < count($plantDeviceList['result_data']['pageList']); $j++) {
                                    if ($plantDeviceList['result_data']['pageList'][$j]) {
                                        $dataArray = InverterSerialNo::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id'], 'dv_inverter' => $plantDeviceList['result_data']['pageList'][$j]['ps_key']])->first();
                                        if ($dataArray) {
                                            $dataArray->status = $devSungrowStatus;
                                            $dataArray->save();
                                        }
                                    }
                                }
                            }
                            /*$currGeneration = 0;
                            $totalEnergy = 0;
                            $invLastUpdated = '';
                            if ($deviceInverterPointList) {
                                for ($d = 0; $d < count($deviceInverterPointList); $d++) {
                                    if ($deviceInverterPointList[$d]['device_point']['device_type'] == 1) {
                                        $inverterDetails = new InverterDetail();
                                        $currentDeviceTimes = $deviceInverterPointList[count($deviceInverterPointList) - 1]['device_point']['device_time'];
                                        $dateYear = substr($currentDeviceTimes, 0, 4);
                                        $dateMonth = substr($currentDeviceTimes, 4, 2);
                                        $dateDay = substr($currentDeviceTimes, 6, 2);
                                        $dateHour = substr($currentDeviceTimes, 8, 2);
                                        $dateMinute = substr($currentDeviceTimes, 10, 2);
                                        $dateSecond = substr($currentDeviceTimes, 12, 2);
                                        $collectDeviceDates = $dateYear.'-'.$dateMonth.'-'.$dateDay.' '.$dateHour.':'.$dateMinute.':'.$dateSecond;
                                        $inverterDetails->plant_id = $allPlantsData[$i]['id'];
                                        $inverterDetails->siteId = $siteData[$k]['site_id'];
                                        $inverterDetails->dv_inverter = $deviceInverterPointList[$d]['device_point']['device_sn'];
                                        $inverterDetails->inverterPower = (double)$deviceInverterPointList[$d]['device_point']['p24'] / 1000;
                                        $inverterDetails->totalInverterPower = (double)$deviceInverterPointList[$d]['device_point']['p2'] / 1000;
                                        $inverterDetails->lastUpdated = $deviceInverterPointList[$d]['device_point']['device_time'];
                                        $inverterDetails->inverterState = $deviceInverterPointList[$d]['device_point']['dev_status'];
                                        $inverterDetails->inverter_cron_job_id = $inverterMaxCronJobID;
                                        $inverterDetails->mpptPower = isset($deviceInverterPointList[$d]['device_point']['p14']) ? (double)$deviceInverterPointList[$d]['device_point']['p14'] / 1000 : 0;
                                        $inverterDetails->collect_time = $collectDeviceDates;
                                        $inverterDetails->save();
                                        $currGeneration = $currGeneration + (double)$deviceInverterPointList[$d]['device_point']['p24'] / 1000;
                                        $totalEnergy = $totalEnergy + (double)$deviceInverterPointList[$d]['device_point']['p1'] / 1000;
                                        $invLastUpdated = $deviceInverterPointList[$d]['device_point']['device_time'];
                                        if ($deviceInverterPointList[$d]['device_point']['p1']) {
                                            if (DailyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $deviceInverterPointList[$d]['device_point']['device_sn'])->whereDate('created_at', '=', date('Y-m-d'))->exists()) {
                                                $dailyInverterDetail = DailyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $deviceInverterPointList[$d]['device_point']['device_sn'])->first();
                                                $dailyInverterDetail->plant_id = $allPlantsData[$i]['id'];
                                                $dailyInverterDetail->siteId = $siteData[$k]['site_id'];
                                                $dailyInverterDetail->dv_inverter = $deviceInverterPointList[$d]['device_point']['device_sn'];
                                                $dailyInverterDetail->serial_no = $deviceInverterPointList[$d]['device_point']['device_sn'];
                                                $dailyInverterDetail->daily_generation = (double)$deviceInverterPointList[$d]['device_point']['p1'] / 1000;
                                                $dailyInverterDetail->update();

                                            } else {
                                                $dailyInverterDetail = new DailyInverterDetail();
                                                $dailyInverterDetail->plant_id = $allPlantsData[$i]['id'];
                                                $dailyInverterDetail->siteId = $siteData[$k]['site_id'];
                                                $dailyInverterDetail->dv_inverter = $deviceInverterPointList[$d]['device_point']['device_sn'];
                                                $dailyInverterDetail->serial_no = $deviceInverterPointList[$d]['device_point']['device_sn'];
                                                $dailyInverterDetail->daily_generation = (double)$deviceInverterPointList[$d]['device_point']['p1'] / 1000;
                                                $dailyInverterDetail->save();
                                            }

                                        }
                                        $monthlyInvData = array();

                                        $monthlyInvData['plant_id'] = $allPlantsData[$i]['id'];
                                        $monthlyInvData['siteId'] = $siteData[$k]['site_id'];
                                        $monthlyInvData['dv_inverter'] = $deviceInverterPointList[$d]['device_point']['device_sn'];
                                        $monthlyInvData['created_at'] = $currentTime;
                                        $monthlyInvData['updated_at'] = $currentTime;
                                        $monthlyGeneration = DailyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $deviceInverterPointList[$d]['device_point']['device_sn'])->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('daily_generation');
                                        $monthlyInvData['monthly_generation'] = isset($monthlyGeneration) ? $monthlyGeneration : 0;
                                        $monthlyInvDataExist = MonthlyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $deviceInverterPointList[$d]['device_point']['device_sn'])->whereYear('created_at', '=', date('Y'))->whereMonth('created_at', '=', date('m'))->first();

                                        if ($monthlyInvDataExist) {

                                            $monthlyInvDataResponse = $monthlyInvDataExist->fill((array)$monthlyInvData)->save();
                                        } else {

                                            $monthlyInvDataResponse = MonthlyInverterDetail::create((array)$monthlyInvData);
                                        }
                                        $yearlyInvData = array();
                                        $siteIdData = $siteData[$k]['site_id'];
                                        $yearlyInvData['plant_id'] = $allPlantsData[$i]['id'];
                                        $yearlyInvData['siteId'] = $siteData[$k]['site_id'];
                                        $yearlyInvData['dv_inverter'] = $deviceInverterPointList[$d]['device_point']['device_sn'];
                                        $yearlyInvData['created_at'] = $currentTime;
                                        $yearlyInvData['updated_at'] = $currentTime;

                                        $monthlyInverterData = MonthlyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $deviceInverterPointList[$d]['device_point']['device_sn'])->whereYear('created_at', date('Y'))->sum('monthly_generation');

                                        $yearlyInvData['yearly_generation'] = isset($monthlyInverterData) ? $monthlyInverterData : 0;

                                        $yearlyInvDataExist = YearlyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $deviceInverterPointList[$d]['device_point']['device_sn'])->whereYear('created_at', '=', date('Y'))->first();

                                        if ($yearlyInvDataExist) {

                                            $yearlyInvDataResponse = $yearlyInvDataExist->fill((array)$yearlyInvData)->save();
                                        } else {

                                            $yearlyInvDataResponse = YearlyInverterDetail::create((array)$yearlyInvData);
                                        }

                                    }
                                }
                            }
                        }*/

                            $lastRecordTimeStamp = $allPlantsData[$i]['last_cron_job_date'];

                            if (isset($lastRecordTimeStamp) && $lastRecordTimeStamp) {

                                if (strtotime(date('Y-m-d', strtotime($lastRecordTimeStamp))) == strtotime(date('Y-m-d'))) {

                                    $lastRecordDate = date('Y-m-d', strtotime($lastRecordTimeStamp));

                                } else {

                                    $lastRecordDate = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordTimeStamp)));

                                }

                            } else {

                                $lastRecordDate = $allPlantsData[$i]['data_collect_date'];
                            }

//return "oiookkk".$lastRecordDate;
//return $psKeysList;
                            //INVERTER LOG

                            foreach ($psKeysList as $smartKey => $smartInverter) {

                                $siteSmartInverterLogStartTime[] = strtotime($lastRecordDate);
                                $siteAllInverterLogStartTime[] = strtotime($lastRecordDate);

                                while (strtotime($lastRecordDate) != strtotime(date('Y-m-d', strtotime("+1 days")))) {
                                    $collectTime = date('Ymd', strtotime($lastRecordDate));

                                    $NewCollectTime = $collectTime;
                                    $startLoopTime = 0;
                                    $endLoopTime = 0;
                                    $dump = [];
                                    for($x = 0 ; $x <= 7; $x++ ){

                                        if($startLoopTime < 10){
                                            $startLoopTime = '0'.$startLoopTime;
                                        }
                                        $endLoopTime = $startLoopTime+2;
                                        if($endLoopTime < 10){
                                            $endLoopTime = '0'.$endLoopTime;
                                        }
//                                        array_push($dump,$NewCollectTime .$endLoopTime."0000");
//                                        return [$startLoopTime,$endLoopTime,Date("Y-m-d H:i:s",strtotime('+2 hours'))];
                                        if (Date("Y-m-d H:i:s", strtotime($NewCollectTime . $endLoopTime . "5959")) < Date("Y-m-d H:i:s",strtotime('+2 hours'))) {
                                            if ($allPlantsData[$i]->plant_has_grid_meter == 'Y') {

                                                $dailyGenerationData = 0;
                                                $siteSmartMeterData = [
                                                    "appkey" => $appKey,
                                                    "token" => $token,
                                                    "start_time_stamp" => $NewCollectTime . $startLoopTime . "0000",
                                                    "end_time_stamp" => $NewCollectTime . $endLoopTime . "5959",
                                                    "minute_interval" => "5",
                                                    "ps_key_list" => array($pskeyData),
                                                    "points" => "p8018,p8062,p8063",

                                                ];

//                                         print_r($NewCollectTime . $startLoopTime . "0000");
//                                         print_r("<br>");
                                                $siteSmartMeterCurl = curl_init();

                                                curl_setopt_array($siteSmartMeterCurl, array(

                                                    CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/getDevicePointMinuteDataList',
                                                    CURLOPT_RETURNTRANSFER => true,
                                                    CURLOPT_ENCODING => '',
                                                    CURLOPT_MAXREDIRS => 10,
                                                    CURLOPT_TIMEOUT => 0,
                                                    CURLOPT_FOLLOWLOCATION => true,
                                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                                    CURLOPT_POSTFIELDS => json_encode($siteSmartMeterData),
                                                    CURLOPT_HTTPHEADER => array(
                                                        'Content-Type: application/json',
                                                        'sys_code: 901',
                                                        'lang: _en_US',
                                                        'x-access-key: ' . $accessKey,
                                                    ),
                                                ));

                                                $siteSmartMeterResponse = curl_exec($siteSmartMeterCurl);

                                                curl_close($siteSmartMeterCurl);

                                                $siteSmartMeterResponseData = json_decode($siteSmartMeterResponse);

                                                if ($siteSmartMeterResponseData && isset($siteSmartMeterResponseData->result_data)) {

                                                    $siteSmartMeterFinalData = $siteSmartMeterResponseData->result_data;

                                                    if($siteSmartMeterFinalData && isset($siteSmartMeterFinalData->$pskeyData)) {

                                                        $siteSmartMeterFinalData = $siteSmartMeterFinalData->$pskeyData;

                                                        foreach ($siteSmartMeterFinalData as $key => $meterData) {

                                                            $todayLastTime = InverterEnergyLog::where(['plant_id' => $allPlantsData[$i]['id'], 'site_id' => $siteData[$k]['site_id'], 'dv_inverter' => $smartInverter])->whereDate('collect_time', $collectTime)->orderBy('collect_time', 'desc')->first();
                                                            if (empty($todayLastTime) || date('Y-m-d H:i:s', strtotime($meterData->time_stamp)) > ($todayLastTime['collect_time'])) {

                                                                if (isset($meterData->p8018)) {
                                                                    $gridMeterPower = $meterData->p8018 / 1000;
                                                                } else {
                                                                    $gridMeterPower = 0;
                                                                }
                                                                if (isset($meterData->p8063)) {
                                                                    $exportPower = $meterData->p8063 / 1000;
                                                                } else {
                                                                    $exportPower = 0;
                                                                }
                                                                if (isset($meterData->p8062)) {
                                                                    $importPower = $meterData->p8062 / 1000;
                                                                } else {
                                                                    $importPower = 0;
                                                                }
                                                                $inverterEnergyLog['plant_id'] = $allPlantsData[$i]['id'];
                                                                $inverterEnergyLog['site_id'] = $siteData[$k]['site_id'];
                                                                $inverterEnergyLog['dv_inverter'] = $smartInverter;
                                                                $inverterEnergyLog['grid_power'] = $gridMeterPower;
                                                                $inverterEnergyLog['import_energy'] = $importPower;
                                                                $inverterEnergyLog['export_energy'] = $exportPower;
                                                                $inverterEnergyLog['cron_job_id'] = $inverterMaxCronJobID;
                                                                $collectTime = date('Y-m-d H:i:s', strtotime($meterData->time_stamp));
                                                                $inverterEnergyLog['collect_time'] = $collectTime;

                                                                $inverterEnergyLogResponse = InverterEnergyLog::create($inverterEnergyLog);
                                                            }
                                                        }
                                                    }
                                                }

                                                //                                        $todayLastTime = InverterEnergyLog::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])->whereDate('collect_time', $collectTime)->orderBy('collect_time', 'desc')->first();
                                                //                                        if (!empty($todayLastTime) && date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)) > ($todayLastTime['collect_time'])) {
                                                //
                                                //                                            $totalGridPowerData = array_keys(array_column($responseData, 'key'), 'PG_Pt1');
                                                //                                            if ($totalGridPowerData) {
                                                //                                                $gridPower = $responseData[$totalGridPowerData[0]]->value;
                                                //                                            } else {
                                                //                                                $gridPower = 0;
                                                //                                            }
                                                //                                            $totalGridDailyEnergyData = array_keys(array_column($responseData, 'key'), 'Etdy_pu1');
                                                //                                            if ($totalGridDailyEnergyData) {
                                                //                                                $gridImportEnergy = $responseData[$totalGridDailyEnergyData[0]]->value;
                                                //                                            } else {
                                                //                                                $gridImportEnergy = 0;
                                                //                                            }
                                                //                                            $totalGridDailyFeedData = array_keys(array_column($responseData, 'key'), 't_gc_tdy1');
                                                //                                            if ($totalGridDailyFeedData) {
                                                //                                                $gridExportEnergy = $responseData[$totalGridDailyFeedData[0]]->value;
                                                //                                            } else {
                                                //                                                $gridExportEnergy = 0;
                                                //                                            }
                                                //                                            if ($gridPower && $gridPower < 0) {
                                                //                                                $gridPower = $gridPower * (-1);
                                                //                                                $gridPower = $gridPower / 1000;
                                                //                                            } else {
                                                ////                                                            $gridPower = $gridPower * (-1);
                                                //                                                $gridPower = $gridPower / 1000;
                                                //                                            }

                                                //                                                        $gridTotalPowerData = $this->unitConversion((double)$gridPower, 'W');
                                                //                                                            return $gridPower;
                                                //                    if ($todayLastTime->collect_time > date('Y-m-d H:i:s')) {

                                                //                                            $inverterEnergyLog['plant_id'] = $plantID;
                                                //                                            $inverterEnergyLog['site_id'] = $siteID;
                                                //                                            $inverterEnergyLog['dv_inverter'] = $smartInverter;
                                                //                                            $inverterEnergyLog['grid_power'] = $gridPower;
                                                //                                            $inverterEnergyLog['import_energy'] = $gridImportEnergy;
                                                //                                            $inverterEnergyLog['export_energy'] = $gridExportEnergy;
                                                //                                            $inverterEnergyLog['cron_job_id'] = $processedMaxCronJobID;
                                                //                                            $collectTime = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
                                                //                                            $inverterEnergyLog['collect_time'] = $collectTime;
                                                //
                                                //                                            $inverterEnergyLogResponse = InverterEnergyLog::create($inverterEnergyLog);
                                                //                                        }
                                                //                                        return json_encode($siteSmartMeterResponseData);
                                            }

                                            //                                    return $NewCollectTime;
                                            $inverterPowerArray = array();
                                            $siteSmartInverterData = [

                                                "appkey" => $appKey,
                                                "token" => $token,
                                                "start_time_stamp" => $NewCollectTime . $startLoopTime . "0000",
                                                "end_time_stamp" => $NewCollectTime . $endLoopTime . "5959",
                                                "minute_interval" => "5",
                                                "points" => "p1,p2,p4,p5,p6,p7,p8,p9,p10,p14,p18,p19,p20,p21,p22,p23,p24,p27,p29,p45,p46,p47,p48,p49,p50,p51,p52,p53,p54,p55,p56,p57,p58",
                                                "ps_key_list" => array($smartInverter)
                                            ];

                                            $siteSmartInverterCurl = curl_init();

                                            curl_setopt_array($siteSmartInverterCurl, array(

                                                CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/getDevicePointMinuteDataList',
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
                                                    'lang: _en_US',
                                                    'x-access-key: ' . $accessKey,
                                                ),
                                            ));

                                            $siteSmartInverterResponse = curl_exec($siteSmartInverterCurl);

                                            curl_close($siteSmartInverterCurl);

                                            $siteSmartInverterResponseData = json_decode($siteSmartInverterResponse);

                                            if ($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->result_data)) {

                                                $siteSmartInverterFinalData = $siteSmartInverterResponseData->result_data;

                                                if ($siteSmartInverterFinalData && isset($siteSmartInverterFinalData->$smartInverter)) {

                                                    $siteSmartInverterFinalData = $siteSmartInverterFinalData->$smartInverter;

                                                    foreach ($siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {

                                                        if ($lastRecordDate == date('Y-m-d')) {

                                                            $todayLastTime = InverterDetail::where(['plant_id' => $allPlantsData[$i]['id'], 'siteId' => $siteData[$k]['site_id'], 'dv_inverter' => $smartInverter])->whereDate('collect_time', $lastRecordDate)->exists() ? InverterDetail::where(['plant_id' => $allPlantsData[$i]['id'], 'siteId' => $siteData[$k]['site_id'], 'dv_inverter' => $smartInverter])->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:00:00');
                                                            $inverterFinalData = date('Y-m-d H:i:s', strtotime($smartInverterFinalData->time_stamp));
                                                            //                                                    return [$inverterFinalData , $todayLastTime];
                                                            if ($inverterFinalData > $todayLastTime) {

                                                                $inverterDetailLog['plant_id'] = $allPlantsData[$i]['id'];
                                                                $inverterDetailLog['siteId'] = $siteData[$k]['site_id'];
                                                                $inverterDetailLog['dv_inverter'] = $smartInverter;
                                                                $inverterDetailLog['inverterPower'] = isset($smartInverterFinalData->p24) && $smartInverterFinalData->p24 != 0 ? ($smartInverterFinalData->p24 / 1000) : 0;
                                                                $inverterDetailLog['daily_generation'] = isset($smartInverterFinalData->p1) && $smartInverterFinalData->p1 != 0 ? ($smartInverterFinalData->p1 / 1000) : 0;
                                                                $inverterDetailLog['inverterTemperature'] = isset($smartInverterFinalData->p4) ? $smartInverterFinalData->p4 : 0;
                                                                $inverterDetailLog['inverterState'] = isset($smartInverterFinalData->p29) ? $smartInverterFinalData->p29 : '-----';
                                                                $inverterDetailLog['mpptPower'] = isset($smartInverterFinalData->p14) && $smartInverterFinalData->p14 != 0 ? ($smartInverterFinalData->p14 / 1000) : 0;
                                                                $inverterDetailLog['frequency'] = isset($smartInverterFinalData->p27) ? $smartInverterFinalData->p27 : 0;

                                                                $inverterDetailLog['phase_voltage_r'] = isset($smartInverterFinalData->p18) ? $smartInverterFinalData->p18 : 0;
                                                                $inverterDetailLog['phase_voltage_s'] = isset($smartInverterFinalData->p19) ? $smartInverterFinalData->p19 : 0;
                                                                $inverterDetailLog['phase_voltage_t'] = isset($smartInverterFinalData->p20) ? $smartInverterFinalData->p20 : 0;
                                                                $inverterDetailLog['phase_current_r'] = isset($smartInverterFinalData->p21) ? $smartInverterFinalData->p21 : 0;
                                                                $inverterDetailLog['phase_current_s'] = isset($smartInverterFinalData->p22) ? $smartInverterFinalData->p22 : 0;
                                                                $inverterDetailLog['phase_current_t'] = isset($smartInverterFinalData->p23) ? $smartInverterFinalData->p23 : 0;
                                                                $inverterDetailLog['collect_time'] = date('Y-m-d H:i:s', strtotime($smartInverterFinalData->time_stamp));
                                                                $inverterDetailLog['inverter_cron_job_id'] = $inverterMaxCronJobID;

                                                                $inverterDetailResponse = InverterDetail::create($inverterDetailLog);

                                                            }
                                                        } else {

                                                            $inverterDetailLog = array();

                                                            $inverterDetailLog['plant_id'] = $allPlantsData[$i]['id'];
                                                            $inverterDetailLog['siteId'] = $siteData[$k]['site_id'];
                                                            $inverterDetailLog['dv_inverter'] = $smartInverter;
                                                            $inverterDetailLog['inverterPower'] = isset($smartInverterFinalData->p24) && $smartInverterFinalData->p24 != 0 ? ($smartInverterFinalData->p24 / 1000) : 0;
                                                            $inverterDetailLog['daily_generation'] = isset($smartInverterFinalData->p1) && $smartInverterFinalData->p1 != 0 ? ($smartInverterFinalData->p1 / 1000) : 0;
                                                            $inverterDetailLog['inverterTemperature'] = isset($smartInverterFinalData->p4) ? $smartInverterFinalData->p4 : 0;
                                                            $inverterDetailLog['inverterState'] = isset($smartInverterFinalData->p29) ? $smartInverterFinalData->p29 : '-----';
                                                            $inverterDetailLog['mpptPower'] = isset($smartInverterFinalData->p14) && $smartInverterFinalData->p14 != 0 ? ($smartInverterFinalData->p14 / 1000) : 0;
                                                            $inverterDetailLog['frequency'] = isset($smartInverterFinalData->p27) ? $smartInverterFinalData->p27 : 0;

                                                            $inverterDetailLog['phase_voltage_r'] = isset($smartInverterFinalData->p18) ? $smartInverterFinalData->p18 : 0;
                                                            $inverterDetailLog['phase_voltage_s'] = isset($smartInverterFinalData->p19) ? $smartInverterFinalData->p19 : 0;
                                                            $inverterDetailLog['phase_voltage_t'] = isset($smartInverterFinalData->p20) ? $smartInverterFinalData->p20 : 0;
                                                            $inverterDetailLog['phase_current_r'] = isset($smartInverterFinalData->p21) ? $smartInverterFinalData->p21 : 0;
                                                            $inverterDetailLog['phase_current_s'] = isset($smartInverterFinalData->p22) ? $smartInverterFinalData->p22 : 0;
                                                            $inverterDetailLog['phase_current_t'] = isset($smartInverterFinalData->p23) ? $smartInverterFinalData->p23 : 0;
                                                            $inverterDetailLog['collect_time'] = date('Y-m-d H:i:s', strtotime($smartInverterFinalData->time_stamp));
                                                            $inverterDetailLog['inverter_cron_job_id'] = $inverterMaxCronJobID;

                                                            $inverterDetailResponse = InverterDetail::create($inverterDetailLog);

                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $startLoopTime = $startLoopTime + 3;
                                    }

                                    /*print_r($siteSmartInverterResponseData);
                                    exit();*/
                                    //DAILY INVERTER DATA
                                    $dailyInvData = array();

                                    $dailyGenerationDetail = InverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'DESC')->first();
                                    $dailyGenerationData = 0;
                                    if($dailyGenerationDetail)
                                    {
                                        $dailyGenerationData = $dailyGenerationDetail->daily_generation;
                                    }
//                                    ? InverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;

                                    $dailyInvData['plant_id'] = $allPlantsData[$i]['id'];
                                    $dailyInvData['siteId'] = $siteData[$k]['site_id'];
                                    $dailyInvData['dv_inverter'] = $smartInverter;
                                    $dailyInvData['updated_at'] = $currentTime;
                                    $dailyInvData['daily_generation'] = $dailyGenerationData;

                                    //$dailyGenerationData = InverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->exists() ? InverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;

                                    $DailyInvDataExist = DailyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereDate('created_at', $lastRecordDate)->first();

                                    if ($DailyInvDataExist) {

                                        $dailyInvDataResponse = $DailyInvDataExist->fill($dailyInvData)->save();
                                    } else {

                                        $dailyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($lastRecordDate));
                                        $dailyInvDataResponse = DailyInverterDetail::create($dailyInvData);
                                    }


//                                    $collectTime = date('Ymd', strtotime($lastRecordDate));
                                    $dailyGenerationData = 0;


                                    break;
                                }

                                $logYear = date('Y', strtotime($lastRecordDate));
                                $logMonth = date('m', strtotime($lastRecordDate));

                                //MONTHLY INVERTER DATA
                                $monthlyInvData = array();

                                $monthlyInvData['plant_id'] = $allPlantsData[$i]['id'];
                                $monthlyInvData['siteId'] = $siteData[$k]['site_id'];
                                $monthlyInvData['dv_inverter'] = $smartInverter;
                                $monthlyInvData['updated_at'] = $currentTime;

                                $dailyInverterData = DailyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('daily_generation');

                                $monthlyInvData['monthly_generation'] = isset($dailyInverterData) ? $dailyInverterData : 0;

                                $monthlyInvDataExist = MonthlyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->first();

                                if ($monthlyInvDataExist) {

                                    $monthlyInvDataResponse = $monthlyInvDataExist->fill((array)$monthlyInvData)->save();
                                } else {

                                    $monthlyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($lastRecordDate));
                                    $monthlyInvDataResponse = MonthlyInverterDetail::create((array)$monthlyInvData);
                                }

                                //YEARLY INVERTER DATA
                                $yearlyInvData = array();

                                $yearlyInvData['plant_id'] = $allPlantsData[$i]['id'];
                                $yearlyInvData['siteId'] = $siteData[$k]['site_id'];
                                $yearlyInvData['dv_inverter'] = $smartInverter;
                                $yearlyInvData['updated_at'] = $currentTime;

                                $monthlyInverterData = MonthlyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->sum('monthly_generation');

                                $yearlyInvData['yearly_generation'] = isset($monthlyInverterData) ? $monthlyInverterData : 0;

                                $yearlyInvDataExist = YearlyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->where('siteId', $siteData[$k]['site_id'])->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->first();

                                if ($yearlyInvDataExist) {

                                    $yearlyInvDataResponse = $yearlyInvDataExist->fill((array)$yearlyInvData)->save();
                                } else {

                                    $yearlyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($lastRecordDate));
                                    $yearlyInvDataResponse = YearlyInverterDetail::create((array)$yearlyInvData);
                                }
                            }

                            /*$currentDeviceTime = $deviceInverterPointList[count($deviceInverterPointList) - 1]['device_point']['device_time'];
                            $dateYear = substr($currentDeviceTime, 0, 4);
                            $dateMonth = substr($currentDeviceTime, 4, 2);
                            $dateDay = substr($currentDeviceTime, 6, 2);
                            $dateHour = substr($currentDeviceTime, 8, 2);
                            $dateMinute = substr($currentDeviceTime, 10, 2);
                            $dateSecond = substr($currentDeviceTime, 12, 2);
                            $collectDeviceDate = $dateYear.'-'.$dateMonth.'-'.$dateDay.' '.$dateHour.':'.$dateMinute.':'.$dateSecond;
                            $dateYear = substr($currentDeviceTime, 0, 4);
                            $dateYear = substr($currentDeviceTime, 0, 4);
                            $cronJobId = $generationLogMaxCronJobID ? $generationLogMaxCronJobID + 1 : 1;
                            $generationLog = new GenerationLog();
                            $generationLog->siteId = $siteData[$k]['site_id'];
                            $generationLog->plant_id = $allPlantsData[$i]['id'];
                            $generationLog->current_generation = $currGeneration;
                            $generationLog->current_consumption = $currGeneration;
                            $generationLog->totalEnergy = $totalEnergy;
                            $generationLog->cron_job_id = $cronJobId;
                            $generationLog->current_grid = 0;
                            $generationLog->lastUpdated = $invLastUpdated;
                            $generationLog->collect_time = $collectDeviceDate;
                            $generationLog->save();*/

                            //SMART INVERTER GENERATION LOG DATA
                            if (!(empty($siteSmartInverterLogStartTime))) {

                                $minTimeSmartInverter = date('Y-m-d', max($siteSmartInverterLogStartTime));
//                                return $minTimeSmartInverter;
                                // $minTimeSmartInverter = date('Y-m-3');

                                $smartInverterStartTimeData = InverterDetail::select(DB::raw('SUM(inverterPower) as current_generation, SUM(daily_generation) as totalEnergy'), 'collect_time')->where(['plant_id' => $Plant_ID, 'siteId' => $ps_id])->whereBetween('collect_time', [date($minTimeSmartInverter . ' 00:00:00'), date($minTimeSmartInverter . ' 23:59:59')])->groupBy('collect_time')->get();

                                foreach ($smartInverterStartTimeData as $generationLogKey => $generationLogData) {
                                    if (GenerationLog::where(['plant_id' => $Plant_ID, 'siteId' => $ps_id])->where('collect_time', $generationLogData->collect_time)->exists()) {

                                        $generationData = GenerationLog::where(['plant_id' => $Plant_ID, 'siteId' => $ps_id])->where('collect_time', $generationLogData->collect_time)->first();

                                        $generationData->current_generation = $generationLogData->current_generation;
                                        $generationData->totalEnergy = $generationLogData->totalEnergy;
                                        $generationData->save();
                                    } else {

                                        $generationLog['plant_id'] = $Plant_ID;
                                        $generationLog['siteId'] = $ps_id;
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
                            if($allPlantsData[$i]->plant_has_grid_meter == 'Y'){

                                if (!(empty($siteAllInverterLogStartTime))) {

                                    $minTimeGridInverter = date('Y-m-d', min($siteAllInverterLogStartTime));
//                                return $minTimeGridInverter;
                                    // $minTimeGridInverter = date('Y-m-d');

                                    $gridInverterStartTimeData = InverterEnergyLog::select(DB::raw('SUM(grid_power) as grid_power'), 'collect_time')->where(['plant_id' => $Plant_ID, 'site_id' => $ps_id])->whereBetween('collect_time', [date($minTimeGridInverter . ' 00:00:00'), date($minTimeGridInverter . ' 23:59:59')])->groupBy('collect_time')->get();
//                                return $gridInverterStartTimeData;
                                    foreach ($gridInverterStartTimeData as $gridLogKey => $gridLogData) {

//                                    return $gridLogData->collect_time;
//                                    return ['plant_id' => $Plant_ID, 'siteId' => $ps_id,'collect_time' => $gridLogData->collect_time] ;
                                        if (GenerationLog::where(['plant_id' => $Plant_ID, 'siteId' => $ps_id])->where('collect_time', $gridLogData->collect_time)->exists()) {

                                            $generationData = GenerationLog::where(['plant_id' => $Plant_ID, 'siteId' => $ps_id])->where('collect_time', $gridLogData->collect_time)->first();

                                            $generationData->current_consumption = ($generationData->current_generation + $gridLogData->grid_power) > 0 ? ($generationData->current_generation + $gridLogData->grid_power) : 0;
                                            $generationData->current_grid = ($gridLogData->grid_power);
                                            $generationData->save();
                                        } else {
//return 'helll';
                                            $generationLog['plant_id'] = $Plant_ID;
                                            $generationLog['siteId'] = $ps_id;
                                            $generationLog['current_generation'] = 0;
                                            $generationLog['comm_failed'] = 0;
                                            $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
                                            $generationLog['current_consumption'] = (0 + ($gridLogData->grid_power)) > 0 ? (0 + $gridLogData->grid_power) : 0;
                                            $generationLog['current_grid'] = $gridLogData->grid_power;
                                            $generationLog['current_irradiance'] = 0;
                                            $generationLog['totalEnergy'] = 0;
                                            $generationLog['collect_time'] = $gridLogData->collect_time;
                                            $generationLog['created_at'] = $currentTime;
                                            $generationLog['updated_at'] = $currentTime;

                                            $generationLogResponse = GenerationLog::create($generationLog);
                                        }
                                    }
                                }
                            }


                            //Site Fault & Alarm Data
                            /*$siteSmartInverterData = [

                                "appkey" => $appKey,
                                "token" => $token,
                                "user_id" => $userId,
                                "process_status" => "1,2,3,4,8,9,10",
                                "curPage" => "1",
                                "size" => "1000"
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

                            $siteSmartInverterResponseData = json_decode($siteSmartInverterResponse);*/
                        }

                    }

                    if (!(empty($siteAllInverterLogStartTime))) {

                        $minTimeInverter = date('Y-m-d', min($siteAllInverterLogStartTime));
                        $maxTimeInverter = date('Y-m-d', max($siteAllInverterLogStartTime));

                        $generationLogInverterStartTimeData = GenerationLog::select(DB::raw('SUM(current_generation) as current_generation, SUM(current_consumption) as current_consumption, SUM(current_grid) as current_grid, SUM(current_irradiance) as current_irradiance, SUM(totalEnergy) as totalEnergy'), 'collect_time')->where(['plant_id' => $allPlantsData[$i]['id']])->whereBetween('collect_time', [date($minTimeInverter . ' 00:00:00'), date($maxTimeInverter . ' 23:59:59')])->groupBy('collect_time')->get();

                        foreach ($generationLogInverterStartTimeData as $key45 => $processedData) {

                            if (ProcessedCurrentVariable::where(['plant_id' => $Plant_ID])->where('collect_time', $processedData->collect_time)->exists()) {

                                $processedCurrentData['plant_id'] = $allPlantsData[$i]['id'];
                                $processedCurrentData['current_generation'] = $processedData->current_generation;
                                $processedCurrentData['current_consumption'] = $processedData->current_consumption;
                                $processedCurrentData['current_grid'] = abs($processedData->current_grid);
                                $processedCurrentData['grid_type'] = $processedData->current_grid >= 0 ? '+ve' : '-ve';
                                $processedCurrentData['current_irradiance'] = $processedData->current_irradiance;
                                $processedCurrentData['totalEnergy'] = $processedData->totalEnergy ? $processedData->totalEnergy : 0;
                                $processedCurrentData['current_saving'] = $processedData->current_generation * (double)$allPlantsData[$i]['benchmark_price'];
                                $processedCurrentData['processed_cron_job_id'] = $processedMaxCronJobID;
                                $processedCurrentData['collect_time'] = $processedData->collect_time;
                                $processedCurrentData['created_at'] = $currentTime;
                                $processedCurrentData['updated_at'] = $currentTime;

                                $processedCurrentDataExist = ProcessedCurrentVariable::where(['plant_id' => $Plant_ID])->where('collect_time', $processedData->collect_time)->first();

                                $processedCurrentDataResponse = $processedCurrentDataExist->fill($processedCurrentData)->save();
                            } else {

                                $processedCurrentData['plant_id'] = $Plant_ID;
                                $processedCurrentData['current_generation'] = $processedData->current_generation;
                                $processedCurrentData['current_consumption'] = $processedData->current_consumption;
                                $processedCurrentData['current_grid'] = abs($processedData->current_grid);
                                $processedCurrentData['grid_type'] = $processedData->current_grid >= 0 ? '+ve' : '-ve';
                                $processedCurrentData['current_irradiance'] = $processedData->current_irradiance;
                                $processedCurrentData['totalEnergy'] = $processedData->totalEnergy ? $processedData->totalEnergy : 0;
                                $processedCurrentData['current_saving'] = $processedData->current_generation * (double)$allPlantsData[$i]['benchmark_price'];
                                $processedCurrentData['processed_cron_job_id'] = $processedMaxCronJobID;
                                $processedCurrentData['collect_time'] = $processedData->collect_time;
                                $processedCurrentData['created_at'] = $currentTime;
                                $processedCurrentData['updated_at'] = $currentTime;

                                $processedCurrentDataResponse = ProcessedCurrentVariable::create($processedCurrentData);
                            }
                        }

//return $minTimeInverter;
                        while (strtotime($minTimeInverter) != strtotime(date('Y-m-d', strtotime("+1 day", strtotime($maxTimeInverter))))) {

                            $plantDataDateToday = $minTimeInverter;
                            $plantDailyTotalBuyEnergy = 0;
//                        $plantDailyTotalIrradiance = 0;
                            $plantDailyTotalSellEnergy = 0;
                            $plantInverterListData = SiteInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->get();
//                        return $plantInverterListData;

                            foreach ($plantInverterListData as $invListData) {

//                            if($invListData->dv_inverter_type == 1) {
//
//                                $plantDailyTotalGeneration += DailyInverterDetail::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first()->daily_generation : 0;
//                            }
//                            else if($invListData->dv_inverter_type == 10) {
//
//                                $plantDailyTotalIrradiance += InverterEMIDetail::where(['plant_id' => $plantID, 'dv_inverter' => $invListData->dv_inverter])->whereDate('collect_time', $plantDataDateToday)->exists() ? (double)(InverterEMIDetail::where(['plant_id' => $plantID, 'dv_inverter' => $invListData->dv_inverter])->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->first()->radiant_total) * (double)($irradianceValue) : 0;
//                            }
//                            else if($invListData->dv_inverter_type == 17) {

                                $inverterEnergyData = InverterEnergyLog::where('plant_id', $allPlantsData[$i]['id'])->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->whereNotNull('import_energy')->first();
                                if ($inverterEnergyData) {
                                    $inverterEnergyTodayImportData = $inverterEnergyData->import_energy;
                                } else {
                                    $inverterEnergyTodayImportData = 0;
                                }
//                                    ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->whereNotNull('import_energy')->first()->import_energy : 0;
//                            $inverterEnergyYesterdayData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->whereNotNull('import_energy')->first();
////                            return [$plantID,$invListData->dv_inverter];
//                            if ($inverterEnergyYesterdayData) {
//                                $inverterEnergyYesterdayImportData = $inverterEnergyYesterdayData->import_energy;
//                            } else {
//                                $inverterEnergyYesterdayImportData = 0;
//                            }
                                //                                ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->whereNotNull('import_energy')->first()->import_energy : 0;

                                $inverterEnergyExportData = InverterEnergyLog::where('plant_id', $allPlantsData[$i]['id'])->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->whereNotNull('export_energy')->first();
                                if ($inverterEnergyExportData) {
                                    $inverterEnergyTodayExportData = $inverterEnergyExportData->export_energy;
                                } else {
                                    $inverterEnergyTodayExportData = 0;
                                }
//                            ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->whereNotNull('export_energy')->first()->export_energy : 0;
//                            $inverterEnergyYesterdayData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->whereNotNull('export_energy')->first();
//                            if ($inverterEnergyYesterdayData) {
//                                $inverterEnergyYesterdayExportData = $inverterEnergyYesterdayData->export_energy;
//                            } else {
//                                $inverterEnergyYesterdayExportData = 0;
//                            }
//                            ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->whereNotNull('export_energy')->first()->export_energy : 0;

                                $plantDailyTotalBuyEnergy += (double)$inverterEnergyTodayImportData;
                                $plantDailyTotalSellEnergy += (double)$inverterEnergyTodayExportData;
//                            return [$plantDailyTotalBuyEnergy,$plantDailyTotalSellEnergy];
                            }
//                            return [$plantDailyTotalBuyEnergy,$plantDailyTotalSellEnergy];
                            $plantDataDateYesterday = date('Y-m-d', strtotime('-1 day', strtotime($minTimeInverter)));

                            $plantDailyTotalGeneration = DailyInverterDetail::where('plant_id', $allPlantsData[$i]['id'])->whereDate('created_at', $plantDataDateToday)->sum('daily_generation');

                            //PLANT DAILY DATA
                            $dailyProcessed['plant_id'] = $allPlantsData[$i]['id'];
                            $dailyProcessed['dailyGeneration'] = $plantDailyTotalGeneration;
                            $dailyProcessed['dailyGridPower'] = $plantDailyTotalBuyEnergy > $plantDailyTotalSellEnergy ? $plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy : $plantDailyTotalSellEnergy - $plantDailyTotalBuyEnergy;
                            $dailyProcessed['dailyBoughtEnergy'] = $plantDailyTotalBuyEnergy;
                            $dailyProcessed['dailySellEnergy'] = $plantDailyTotalSellEnergy;
                            $dailyProcessed['dailyMaxSolarPower'] = 0;
                            $dailyProcessed['dailyConsumption'] =  ($plantDailyTotalGeneration + ($plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy)) > 0 ? ($plantDailyTotalGeneration + ($plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy)) : 0;
//                            return [$plantDailyTotalBuyEnergy,$plantDailyTotalSellEnergy,$plantDailyTotalGeneration, $dailyProcessed['dailyConsumption']];

                            $dailyProcessed['dailySaving'] = (double)$plantDailyTotalGeneration * (double)$allPlantsData[$i]['benchmark_price'];
                            $dailyProcessed['dailyIrradiance'] = 0;
                            $dailyProcessed['updated_at'] = $currentTime;

                            $dailyProcessedPlantDetailExist = DailyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first();

                            if ($dailyProcessedPlantDetailExist) {

                                $dailyProcessedPlantDetailExist['plant_id'] = $allPlantsData[$i]['id'];
                                $dailyProcessedPlantDetailExist['dailyGeneration'] = $plantDailyTotalGeneration;
                                $dailyProcessedPlantDetailExist['dailyGridPower'] = $plantDailyTotalBuyEnergy > $plantDailyTotalSellEnergy ? $plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy : $plantDailyTotalSellEnergy - $plantDailyTotalBuyEnergy;
                                $dailyProcessedPlantDetailExist['dailyBoughtEnergy'] = $plantDailyTotalBuyEnergy;
                                $dailyProcessedPlantDetailExist['dailySellEnergy'] = $plantDailyTotalSellEnergy;
                                $dailyProcessedPlantDetailExist['dailyMaxSolarPower'] = 0;
                                $dailyProcessedPlantDetailExist['dailyConsumption'] =  ($plantDailyTotalGeneration + ($plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy)) > 0 ? ($plantDailyTotalGeneration + ($plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy)) : 0;
                                //                            return [$plantDailyTotalBuyEnergy,$plantDailyTotalSellEnergy,$plantDailyTotalGeneration, $dailyProcessed['dailyConsumption']];

                                $dailyProcessedPlantDetailExist['dailySaving'] = (double)$plantDailyTotalGeneration * (double)$allPlantsData[$i]['benchmark_price'];
                                $dailyProcessedPlantDetailExist['dailyIrradiance'] = 0;
                                $dailyProcessedPlantDetailExist['updated_at'] = $currentTime;
                                $dailyProcessedPlantDetailExist->save();
                                // $dailyProcessedPlantDetailInsertionResponce = $dailyProcessedPlantDetailExist->fill($dailyProcessed)->save();
                            } else {

                                $dailyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($plantDataDateToday));
                                $dailyProcessedPlantDetailInsertionResponce = DailyProcessedPlantDetail::create($dailyProcessed);
                            }

                            $minTimeInverter = date('Y-m-d', strtotime("+1 day", strtotime($minTimeInverter)));
                        }

                        $logYear = date('Y', strtotime($minTimeInverter));
                        $logMonth = date('m', strtotime($minTimeInverter));

//                        $plantDailyGenerationDataSum = DailyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyGeneration');
//                        $plantDailyConsumptionDataSum = DailyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyConsumption');
//                        $plantDailyGridDataSum = DailyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyGridPower');
//                        $plantDailyBoughtDataSum = DailyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyBoughtEnergy');
//                        $plantDailySellDataSum = DailyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailySellEnergy');
//                        $plantDailySavingDataSum = DailyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailySaving');
                        $plantGenerationTableData = DailyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->get();
                        $dataArray = json_decode(json_encode($plantGenerationTableData), true);
                        $plantDailyGenerationDataSum = array_sum(array_column($dataArray, 'dailyGeneration'));
                        $plantDailyConsumptionDataSum = array_sum(array_column($dataArray, 'dailyConsumption'));
                        $plantDailyGridDataSum = array_sum(array_column($dataArray, 'dailyGridPower'));
                        $plantDailyBoughtDataSum = array_sum(array_column($dataArray, 'dailyBoughtEnergy'));
                        $plantDailySellDataSum = array_sum(array_column($dataArray, 'dailySellEnergy'));
                        $plantDailySavingDataSum = array_sum(array_column($dataArray, 'dailySaving'));

                        $monthlyProcessed['plant_id'] = $allPlantsData[$i]['id'];
                        $monthlyProcessed['monthlyGeneration'] = $plantDailyGenerationDataSum;
                        $monthlyProcessed['monthlyConsumption'] = $plantDailyConsumptionDataSum;
                        $monthlyProcessed['monthlyGridPower'] = $plantDailyGridDataSum;
                        $monthlyProcessed['monthlyBoughtEnergy'] = $plantDailyBoughtDataSum;
                        $monthlyProcessed['monthlySellEnergy'] = $plantDailySellDataSum;
                        $monthlyProcessed['monthlySaving'] = $plantDailySavingDataSum;
                        $monthlyProcessed['updated_at'] = $currentTime;

                        $monthlyProcessedPlantDetailExist = MonthlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->orderBy('created_at', 'DESC')->first();

                        if ($monthlyProcessedPlantDetailExist) {

                            $monthlyProcessedPlantDetailResponse = $monthlyProcessedPlantDetailExist->fill($monthlyProcessed)->save();
                        } else {

                            $monthlyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
                            $monthlyProcessedPlantDetailResponse = MonthlyProcessedPlantDetail::create($monthlyProcessed);
                        }
                        $plantMonthlyData = MonthlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->get();
                        $monthlyDataArray = json_decode(json_encode($plantMonthlyData), true);

//                        $plantmonthlyGenerationDataSum = MonthlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->sum('monthlyGeneration');
//                        $plantmonthlyConsumptionDataSum = MonthlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->sum('monthlyConsumption');
//                        $plantmonthlyGridDataSum = MonthlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->sum('monthlyGridPower');
//                        $plantmonthlyBoughtDataSum = MonthlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->sum('monthlyBoughtEnergy');
//                        $plantmonthlySellDataSum = MonthlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->sum('monthlySellEnergy');
//                        $plantmonthlySavingDataSum = MonthlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->sum('monthlySaving');
                        $plantmonthlyGenerationDataSum = array_sum(array_column($monthlyDataArray, 'monthlyGeneration'));
                        $plantmonthlyConsumptionDataSum = array_sum(array_column($monthlyDataArray, 'monthlyConsumption'));
                        $plantmonthlyGridDataSum = array_sum(array_column($monthlyDataArray, 'monthlyGridPower'));
                        $plantmonthlyBoughtDataSum = array_sum(array_column($monthlyDataArray, 'monthlyBoughtEnergy'));
                        $plantmonthlySellDataSum = array_sum(array_column($monthlyDataArray, 'monthlySellEnergy'));
                        $plantmonthlySavingDataSum = array_sum(array_column($monthlyDataArray, 'monthlySaving'));

                        $yearlyProcessed['plant_id'] = $allPlantsData[$i]['id'];
                        $yearlyProcessed['yearlyGeneration'] = $plantmonthlyGenerationDataSum;
                        $yearlyProcessed['yearlyConsumption'] = $plantmonthlyConsumptionDataSum;
                        $yearlyProcessed['yearlyGridPower'] = $plantmonthlyGridDataSum;
                        $yearlyProcessed['yearlyBoughtEnergy'] = $plantmonthlyBoughtDataSum;
                        $yearlyProcessed['yearlySellEnergy'] = $plantmonthlySellDataSum;
                        $yearlyProcessed['yearlySaving'] = $plantmonthlySavingDataSum;
                        $yearlyProcessed['updated_at'] = $currentTime;

                        $yearlyProcessedPlantDetailExist = yearlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', $logYear)->orderBy('created_at', 'DESC')->first();

                        if ($yearlyProcessedPlantDetailExist) {

                            $yearlyProcessedPlantDetailResponse = $yearlyProcessedPlantDetailExist->fill($yearlyProcessed)->save();
                        } else {

                            $yearlyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
                            $yearlyProcessedPlantDetailResponse = yearlyProcessedPlantDetail::create($yearlyProcessed);
                        }
                    }

                    $plantReduction = Setting::where('perimeter', 'env_reduction')->first()['value'];
                    $totalPlantReduction = $plantReduction * yearlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', date('Y'))->sum('yearlyGeneration');
                    $totalProcessedData = TotalProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->first();
                    $yearlyDataArray = yearlyProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->whereYear('created_at', date('Y'))->get();
                    $yearlyData = json_decode(json_encode($yearlyDataArray), true);
                    $plantTotalCurrentPower = 0;
                    $totalPowerData = ProcessedCurrentVariable::where('plant_id', $allPlantsData[$i]['id'])->latest()->first();
                    if($totalPowerData)
                    {
                        $plantTotalCurrentPower = $totalPowerData->current_generation;
                    }
                    $plantYearlyGenerationDataSum = array_sum(array_column($yearlyData, 'yearlyGeneration'));
                    $plantYearlyConsumptionDataSum = array_sum(array_column($yearlyData, 'yearlyConsumption'));
                    $plantYearlyGridDataSum = array_sum(array_column($yearlyData, 'yearlyGridPower'));
                    $plantYearlyBoughtDataSum = array_sum(array_column($yearlyData, 'yearlyBoughtEnergy'));
                    $plantYearlySellDataSum = array_sum(array_column($yearlyData, 'yearlySellEnergy'));
                    $plantYearlySavingDataSum = array_sum(array_column($yearlyData, 'yearlySaving'));
                    if ($totalProcessedData) {
                        $totalProcessedPlantDetail = TotalProcessedPlantDetail::where('plant_id', $allPlantsData[$i]['id'])->first();
                        $totalProcessedPlantDetail->plant_total_current_power = $plantTotalCurrentPower;
                        $totalProcessedPlantDetail->plant_total_generation = $plantYearlyGenerationDataSum;
                        $totalProcessedPlantDetail->plant_total_consumption = $plantYearlyConsumptionDataSum;
                        $totalProcessedPlantDetail->plant_total_grid = $plantYearlyGridDataSum;
                        $totalProcessedPlantDetail->plant_total_buy_energy = $plantYearlyBoughtDataSum;
                        $totalProcessedPlantDetail->plant_total_sell_energy = $plantYearlySellDataSum;
                        $totalProcessedPlantDetail->plant_total_saving = $plantYearlySavingDataSum;
                        $totalProcessedPlantDetail->plant_total_reduction = $totalPlantReduction;
                        $totalProcessedPlantDetail->update();

                    } else {
                        $totalProcessedPlantDetail = new TotalProcessedPlantDetail();
                        $totalProcessedPlantDetail->plant_id = $allPlantsData[$i]['id'];
                        $totalProcessedPlantDetail->plant_total_current_power = $plantTotalCurrentPower;
                        $totalProcessedPlantDetail->plant_total_generation = $plantYearlyGenerationDataSum;
                        $totalProcessedPlantDetail->plant_total_consumption = $plantYearlyConsumptionDataSum;
                        $totalProcessedPlantDetail->plant_total_grid = $plantYearlyGridDataSum;
                        $totalProcessedPlantDetail->plant_total_buy_energy = $plantYearlyBoughtDataSum;
                        $totalProcessedPlantDetail->plant_total_sell_energy = $plantYearlySellDataSum;
                        $totalProcessedPlantDetail->plant_total_saving = $plantYearlySavingDataSum;
                        $totalProcessedPlantDetail->plant_total_reduction = $totalPlantReduction;
                        $totalProcessedPlantDetail->save();

                    }
                    $accumulativeCurrentDataSum = TotalProcessedPlantDetail::sum('plant_total_current_power');
                    $accumulativeGenerationDataSum = TotalProcessedPlantDetail::sum('plant_total_generation');
                    $accumulativeReductionDataSum = TotalProcessedPlantDetail::sum('plant_total_reduction');

                    $accumulativeProcessed['total_current_power'] = $accumulativeCurrentDataSum;
                    $accumulativeProcessed['total_generation'] = $accumulativeGenerationDataSum;
                    $accumulativeProcessed['total_reduction'] = $accumulativeReductionDataSum;
                    $accumulativeProcessed['updated_at'] = $currentTime;

                    $accumulativeProcessedPlantDetailExist = AccumulativeProcessedDetail::first();

                    if ($accumulativeProcessedPlantDetailExist) {

                        $accumulativeProcessedPlantDetailResponse = $accumulativeProcessedPlantDetailExist->fill($accumulativeProcessed)->save();
                    } else {

                        $accumulativeProcessedPlantDetailResponse = AccumulativeProcessedDetail::create($accumulativeProcessed);
                    }
//                    if (strtotime(date('Y-m-d', strtotime($lastRecordDate))) == strtotime(date('Y-m-d'))) {
//
//                        $NextRecordDate = date('Y-m-d', strtotime($lastRecordDate));
//                    } else {
//
//                        $NextRecordDate = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordDate)));
//                    }

                    $allPlantsData[$i]->last_cron_job_date = $lastRecordDate;
                    $allPlantsData[$i]->save();

                }




            }

            $this->plantStatusUpdate();
            $cronJobTime = new CronJobTime();
            $cronJobTime->start_time = $cronJobStartTime;
            $cronJobTime->end_time = date('Y-m-d H:i:s');
            $cronJobTime->type = 'Sungrow';
            $cronJobTime->status = 'completed';
            $cronJobTime->save();
            print_r('Crone Job End Time');
            print_r(date("Y-m-d H:i:s"));
            print_r("\n");
        } catch (Exception $e) {

            return $e->getMessage();
        }
    }

    public function getTokenAndUserID($appKey, $userAccount, $userPassword)
    {

        $userId = '';
        $token = '';


        $userCredentials = [

            "appkey" => $appKey,
            "user_account" => $userAccount,
            "user_password" => $userPassword,
            "login_type" => "1"
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/login',
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
                'x-access-key: rxyrr4gt34kqx4ggdrdg2vs82k234zny'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $curl = curl_init();
        $responseType = json_decode($response, true);

        $userId = $responseType['result_data']['user_id'];
        $token = $responseType['result_data']['token'];

        return [$token, $userId];
    }

    public function getPlantList($appKey, $token, $userId)
    {

        $plantSiteData = [
            "appkey" => $appKey,
            "token" => $token,
            "user_id" => $userId,
            "size" => "1000",
            "curPage" => "1"
        ];

        $plantSiteCurl = curl_init();

        curl_setopt_array($plantSiteCurl, array(

            CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/openapi/getPowerStationList',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($plantSiteData),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'sys_code: 901',
                'lang: _en_US',
                'x-access-key: rxyrr4gt34kqx4ggdrdg2vs82k234zny'
            ),
        ));

        $plantSiteStatusResponse = curl_exec($plantSiteCurl);

        curl_close($plantSiteCurl);

        $plantSiteStatusResponseData = json_decode($plantSiteStatusResponse);

        $plantListFinalResponse = isset($plantSiteStatusResponseData) && isset($plantSiteStatusResponseData->result_data) && isset($plantSiteStatusResponseData->result_data->pageList) ? $plantSiteStatusResponseData->result_data->pageList : [];

        return $plantListFinalResponse;
    }

    public function getSiteDeviceList($appKey, $token, $siteID)
    {

        $siteDeviceDetails = [
            "appkey" => $appKey,
            "token" => $token,
            "ps_id" => $siteID,
            "size" => "1000",
            "curPage" => "1"
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/v1/devService/getDeviceList',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($siteDeviceDetails),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'sys_code: 901',
                'lang: _en_US',
                'x-access-key: x0qapvhjzzhvy9byj0j38b3en9nacwk9'
            ),
        ));

        $deviceList = curl_exec($curl);
        $plantDeviceList = json_decode($deviceList);

        $plantDeviceListFinalResponse = isset($plantDeviceList) && isset($plantDeviceList->result_data) && isset($plantDeviceList->result_data->pageList) ? $plantDeviceList->result_data->pageList : [];

        return $plantDeviceListFinalResponse;
    }

    private function plantStatusUpdate()
    {

        $plants = DB::table('plants')
            ->join('plant_sites', 'plants.id', 'plant_sites.plant_id')
            ->select('plants.*', 'plant_sites.site_id')
            ->where('plants.meter_type', 'SunGrow')
            ->get();

        foreach ($plants as $key => $plant) {

            $updateStatus = array();

            $plantStatus = PlantSite::where('plant_id', $plant->id)->get('online_status');

            if ($plantStatus->contains('online_status', 'F')) {

                $updateStatus['is_online'] = 'P_Y';
                $updateStatus['faultLevel'] = 1;
            } else {

                $updateStatus['faultLevel'] = 0;
            }

            if ($plantStatus->contains('online_status', 'A')) {

                $updateStatus['is_online'] = 'P_Y';
                $updateStatus['alarmLevel'] = 1;
            } else {

                $updateStatus['alarmLevel'] = 0;
            }

            if ($plantStatus->contains('online_status', 'P_Y')) {

                $updateStatus['is_online'] = 'P_Y';
            }

            if ($plantStatus->contains('online_status', 'Y') && $plantStatus->contains('online_status', 'N')) {

                $updateStatus['is_online'] = 'P_Y';
            } else if ($plantStatus->contains('online_status', 'Y')) {

                $updateStatus['is_online'] = 'Y';
            } else if ($plantStatus->contains('online_status', 'N')) {

                $updateStatus['is_online'] = 'N';
            }

            /*$plantAlertStatus = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
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
            }*/

            $plantRes = Plant::where('id', $plant->id)->update($updateStatus);
        }
    }

    public function unitConversion($num, $unit)
    {

        $num = (double)$num;

        if ($num < 0) {

            $num = $num * (-1);
        }

        if ($num < pow(10, 3)) {
            if ($unit == 'PKR') {
                $unit = ' PKR';
            } else if ($unit == 'W') {
                $unit = 'W';
            }
        } else if ($num >= pow(10, 3) && $num < pow(10, 6)) {
            $num = $num / pow(10, 3);

            if ($unit == 'kWh') {
                $unit = 'MWh';
            } else if ($unit == 'kW') {
                $unit = 'MW';
            } else if ($unit == 'kWp') {
                $unit = 'MWp';
            } else if ($unit == 'PKR') {
                $unit = 'K PKR';
            } else if ($unit == 'W') {
                $unit = 'kW';
            }

        } else if ($num >= pow(10, 6) && $num < pow(10, 9)) {
            $num = $num / pow(10, 6);

            if ($unit == 'kWh') {
                $unit = 'GWh';
            } else if ($unit == 'kW') {
                $unit = 'GW';
            } else if ($unit == 'kWp') {
                $unit = 'GWp';
            } else if ($unit == 'PKR') {
                $unit = 'M PKR';
            } else if ($unit == 'W') {
                $unit = 'MW';
            }

        } else if ($num >= pow(10, 9) && $num < pow(10, 12)) {
            $num = $num / pow(10, 9);

            if ($unit == 'kWh') {
                $unit = 'TWh';
            } else if ($unit == 'kW') {
                $unit = 'TW';
            } else if ($unit == 'kWp') {
                $unit = 'TWp';
            } else if ($unit == 'PKR') {
                $unit = 'B PKR';
            } else if ($unit == 'W') {
                $unit = 'GW';
            }

        } else if ($num >= pow(10, 12) && $num < pow(10, 15)) {
            $num = $num / pow(10, 12);

            if ($unit == 'kWh') {
                $unit = 'PWh';
            } else if ($unit == 'kW') {
                $unit = 'PW';
            } else if ($unit == 'kWp') {
                $unit = 'PWp';
            } else if ($unit == 'PKR') {
                $unit = 'T PKR';
            } else if ($unit == 'W') {
                $unit = 'TW';
            }

        } else if ($num >= pow(10, 15) && $num < pow(10, 18)) {
            $num = $num / pow(10, 15);

            if ($unit == 'kWh') {
                $unit = 'EWh';
            } else if ($unit == 'kW') {
                $unit = 'EW';
            } else if ($unit == 'kWp') {
                $unit = 'EWp';
            } else if ($unit == 'PKR') {
                $unit = 'Q PKR';
            } else if ($unit == 'W') {
                $unit = 'PW';
            }

        }

        return [$num, $unit];
    }
}
