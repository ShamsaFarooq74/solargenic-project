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
use Carbon\Carbon;
use App\Http\Controllers\LEDController;
use App\Http\Controllers\Admin\CommunicationController;
use App\Http\Controllers\Admin\PlantsController;

class SolisCopiedController extends Controller
{
//    public $arrayDifference = [];
    public function solis($globalGenerationLogMaxID = null, $globalProcessedLogMaxID = null, $globalInverterDetailMaxID = null)
    {

        date_default_timezone_set('Asia/Karachi');
        $currentTime = date('Y-m-d H:i:s');
        print_r('Crone Job Start Time');
        print_r(date("Y-m-d H:i:s"));
        print_r("\n");
        $solisAppData = Setting::where('perimeter', 'solis_api_app_id')->first();
//        InverterMPPTDetail::whereDate('created_at',date('Y-m-d'))->delete();
//        return;
        if (!empty($solisAppData)) {
            $appID = $solisAppData->value;
        } else {
            $appID = '3yhg';
        }
        $solisAppKey = Setting::where('perimeter', 'solis_api_app_key')->first();
        if (!empty($solisAppKey)) {
            $appKey = $solisAppKey->value;
        } else {
            $appKey = '3yhg';
        }
        $solisUserAccount = Setting::where('perimeter', 'solis_api_user_account')->first();
        if (!empty($solisUserAccount)) {
            $userAccount = $solisUserAccount->value;
        } else {
            $userAccount = '3yhg';
        }
        $solisPassword = Setting::where('perimeter', 'solis_api_user_password')->first();
        $userPassword = !empty($solisPassword) ? $solisPassword->value : '3yhg';
        $solisOrgId = Setting::where('perimeter', 'solis_api_orgID')->first();
        $OrgId = !empty($solisOrgId) ? $solisOrgId->value : '3yhg';
        $solisApiBaseUrl = Setting::where('perimeter', 'solis_api_base_url')->first();
        $solisAPIBaseURL = !empty($solisApiBaseUrl) ? $solisApiBaseUrl->value : '';
        $solisReductionValue = Setting::where('perimeter', 'env_reduction')->first();
        $envReductionValue = !empty($solisReductionValue) ? $solisReductionValue->value : 0;
        $solisIrredianceValue = Setting::where('perimeter', 'irradiance')->first();
        $irradianceValue = !empty($solisIrredianceValue) ? $solisIrredianceValue->value : 0;
        $generationLogMaxCronJobID = $globalGenerationLogMaxID + 1;
        $processedMaxCronJobID = $globalProcessedLogMaxID + 1;
        $inverterMaxCronJobID = $globalInverterDetailMaxID + 1;

        $token = $this->getToken($solisAPIBaseURL, $appID, $appKey, $userAccount, $userPassword, $OrgId);

        // $allPlantsData = Plant::where('id', 102)->where('meter_type','Solis')->get();
        // $allPlantsData = Plant::where('meter_type', 'Solis')->get();
       $allPlantsData = Plant::where('meter_type', 'Solis')->where('id', 108)->get();
        $dataArrayValues = array();

        if ($allPlantsData) {

            foreach ($allPlantsData as $key => $plant) {

                $siteAllInverterLogStartTime = array();
                $plantID = $plant->id;
//                return $plantID;

                $plantSites = PlantSite::where('plant_id', $plantID)->get();

                if ($plantSites) {
//                    $arrayDifference = array();

                    foreach ($plantSites as $site) {

                        $siteSmartInverterArray = array();
                        $siteSmartInverterLogStartTime = array();

                        $siteID = $site->site_id;
                        $alertController = new SolisAlertsController();
//                        return [$token,$plantID,$siteID];
                       $alertData = $alertController->AlarmAndFault($token,$plantID,$siteID);
                       return $alertData;

                        $siteInverterList = [

                            "stationId" => $siteID,
                        ];

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $solisAPIBaseURL . '/station/v1.0/device',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($siteInverterList),
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Bearer ' . $token,
                                'Content-Type: application/json'
                            ),
                        ));

                        $response = curl_exec($curl);
                        curl_close($curl);

                        $plantDeviceList = json_decode($response);
//                        print_r (json_encode($plantDeviceList));

                        if (isset($plantDeviceList) && isset($plantDeviceList->deviceListItems)) {
                            $plantDeviceStatusList = array();
                            $updateSiteStatusArray = array();

                            $plantDeviceListFinalData = $plantDeviceList->deviceListItems;


                            foreach ($plantDeviceListFinalData as $key2 => $dev) {

                                //SITE INVERTER DETAIL
                                $invSerial = SiteInverterDetail::updateOrCreate(
                                    ['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $dev->deviceSn, 'dv_inverter_type' => $dev->deviceType],
                                    ['dv_inverter_serial_no' => $dev->deviceSn, 'dv_inverter_name' => $dev->deviceSn]
                                );

                                if (isset($dev->deviceType) && isset($dev->deviceSn) && strtolower($dev->deviceType) == 'inverter') {

                                    //INVERTER SERIAL NO
                                    $invSerial = InverterSerialNo::updateOrCreate(
                                        ['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $dev->deviceSn, 'inverter_type_id' => $dev->deviceType],
                                        ['dv_inverter_serial_no' => $dev->deviceSn, 'inverter_name' => $dev->deviceSn]
                                    );

                                    $siteSmartInverterArray[] = $dev->deviceSn;
                                    $plantDeviceStatusList[] = $dev->connectStatus;
                                }
                            }
//                            return json_encode(count($siteSmartInverterArray));
                            if (in_array(2, $plantDeviceStatusList)) {

                                $updateSiteStatusArray['online_status'] = 'Y';
                            }

                            if (in_array(1, $plantDeviceStatusList)) {

                                $updateSiteStatusArray['online_status'] = 'N';
                            }

                            if (in_array(2, $plantDeviceStatusList) && in_array(1, $plantDeviceStatusList)) {

                                $updateSiteStatusArray['online_status'] = 'P_Y';
                            }

                            if (in_array(3, $plantDeviceStatusList)) {

                                $updateSiteStatusArray['online_status'] = 'A';
                            }

                            //SITE STATUS UPDATE DATA
                            $siteStatusUpdateResponse = PlantSite::where(['plant_id' => $plantID, 'site_id' => $siteID])->update(['online_status' => $updateSiteStatusArray]);
                        }

                        //INVERTER LOG
                        foreach ($siteSmartInverterArray as $smartKey => $smartInverter) {

                            $lastRecordTimeStamp = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->exists() ? InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->orderBy('collect_time', 'DESC')->first()->collect_time : null;
//                            return $lastRecordTimeStamp;

                            if (isset($lastRecordTimeStamp) && $lastRecordTimeStamp) {

                                if (strtotime(date('Y-m-d', strtotime($lastRecordTimeStamp))) == strtotime(date('Y-m-d'))) {

                                    $lastRecordDate = date('Y-m-d', strtotime($lastRecordTimeStamp));
                                } else {

                                    $lastRecordDate = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordTimeStamp)));
                                }
                            } else {

                                $lastRecordDate = $plant->data_collect_date;
                            }


                            $siteSmartInverterLogStartTime[] = strtotime($lastRecordDate);
                            $siteAllInverterLogStartTime[] = strtotime($lastRecordDate);
//                            return $lastRecordDate;
//                            return [json_encode(strtotime($lastRecordDate)),json_encode(strtotime(date('Y-m-d', strtotime("+1 days"))))];

                            while (strtotime($lastRecordDate) != strtotime(date('Y-m-d', strtotime("+1 days")))) {

                                $collectTime = date('Y-m-d', strtotime($lastRecordDate));
                                $dailyGenerationData = 0;
//                                return $lastRecordDate;

                                $siteSmartInverterData = [

                                    "deviceSn" => $smartInverter,
                                    "endTime" => $lastRecordDate,
                                    "startTime" => $lastRecordDate,
                                    "timeType" => 1
                                ];

                                $siteSmartInverterCurl = curl_init();

                                curl_setopt_array($siteSmartInverterCurl, array(

                                    CURLOPT_URL => $solisAPIBaseURL . '/device/v1.0/historical',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => json_encode($siteSmartInverterData),
                                    CURLOPT_HTTPHEADER => array(
                                        'Authorization: Bearer ' . $token,
                                        'Content-Type: application/json'
                                    ),
                                ));

                                $siteSmartInverterResponse = curl_exec($siteSmartInverterCurl);

                                curl_close($siteSmartInverterCurl);

                                $siteSmartInverterResponseData = json_decode($siteSmartInverterResponse);


                                if ($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->paramDataList)) {

                                    $siteSmartInverterFinalData = $siteSmartInverterResponseData->paramDataList;

                                    if ($siteSmartInverterFinalData) {
                                        $dataArrayDetails = [];

                                        foreach ($siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {
                                            $responseData = isset($smartInverterFinalData->dataList) ? $smartInverterFinalData->dataList : [];
//                                            return $responseData;
//                                            return count($responseData);

                                            if ($responseData) {

//                                                if ($lastRecordDate == date('Y-m-d')) {
////                                                    print_r('today');
//
//                                                    $todayLastTime = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->whereDate('collect_time', $lastRecordDate)->exists() ? InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:00:00');
////                                                    return [strtotime($smartInverterFinalData->collectTime) > strtotime($todayLastTime)];
////                                                    return [date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)) > $todayLastTime,date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)) , $todayLastTime];
//                                                    if (date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)) > $todayLastTime) {
//                                                        print_r('today collect time base');
////                                                        array_push($dataArrayValues,['collectTime' => strtotime($smartInverterFinalData->collectTime),'lastTimeConversion' => strtotime($todayLastTime),'lastTime' => $todayLastTime,'calculatedTime' => date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))]);
////                                                        array_push($dataArrayValues,['collectTime' => date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))]);
//                                                        $inverterDetailLog = array();
//
//                                                        $inverterDetailLog['plant_id'] = $plantID;
//                                                        $inverterDetailLog['siteId'] = $siteID;
//                                                        $inverterDetailLog['dv_inverter'] = $smartInverter;
//                                                        $keys = array_keys(array_column($responseData, 'key'), 'APo_t1');
//                                                        if ($keys) {
//                                                            $inverterDetailLog['inverterPower'] = ($responseData[$keys[0]]->value / 1000);
//                                                        }
//                                                        $dailyGen = array_keys(array_column($responseData, 'key'), 'Etdy_ge1');
//                                                        if ($dailyGen) {
//                                                            $inverterDetailLog['daily_generation'] = $responseData[$dailyGen[0]]->value;
//                                                        }
//                                                        $mpptPow = array_keys(array_column($responseData, 'key'), 'DPi_t1');
//                                                        if ($mpptPow) {
//                                                            $inverterDetailLog['mpptPower'] = ($responseData[$mpptPow[0]]->value / 1000);
//                                                        }
//                                                        $freq = array_keys(array_column($responseData, 'key'), 'PG_F_METER1');
//                                                        if ($freq) {
//                                                            $inverterDetailLog['frequency'] = ($responseData[$freq[0]]->value / 1000);
//                                                        }
//                                                        $invertTemp = array_keys(array_column($responseData, 'key'), 'T_in1');
//                                                        if ($invertTemp) {
//                                                            $inverterDetailLog['inverterTemperature'] = $responseData[$invertTemp[0]]->value;
//                                                        }
//                                                        $phaseVolt = array_keys(array_column($responseData, 'key'), 'AV1');
//                                                        if ($phaseVolt) {
//                                                            $inverterDetailLog['phase_voltage_r'] = $responseData[$phaseVolt[0]]->value;
//                                                        }
//                                                        $phaseVoltS = array_keys(array_column($responseData, 'key'), 'AV2');
//                                                        if ($phaseVoltS) {
//                                                            $inverterDetailLog['phase_voltage_s'] = $responseData[$phaseVoltS[0]]->value;
//                                                        }
//                                                        $phaseVoltT = array_keys(array_column($responseData, 'key'), 'AV3');
//                                                        if ($phaseVoltT) {
//                                                            $inverterDetailLog['phase_voltage_t'] = $responseData[$phaseVoltT[0]]->value;
//                                                        }
//                                                        $phaseCurrR = array_keys(array_column($responseData, 'key'), 'AC1');
//                                                        if ($phaseCurrR) {
//                                                            $inverterDetailLog['phase_current_r'] = $responseData[$phaseCurrR[0]]->value;
//                                                        }
//                                                        $phaseCurrS = array_keys(array_column($responseData, 'key'), 'AC2');
//                                                        if ($phaseCurrS) {
//                                                            $inverterDetailLog['phase_current_s'] = $responseData[$phaseCurrS[0]]->value;
//                                                        }
//                                                        $phaseCurrT = array_keys(array_column($responseData, 'key'), 'AC3');
//                                                        if ($phaseCurrT) {
//                                                            $inverterDetailLog['phase_current_t'] = $responseData[$phaseCurrT[0]]->value;
//                                                        }
////                                                        return date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
//
//                                                        if (!InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->where('collect_time', date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)))->exists()) {
//                                                            $inverterDetailLog['collect_time'] = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
//
//                                                            $inverterDetailResponse = InverterDetail::create($inverterDetailLog);
//
//                                                        }
//                                                        for ($mi = 1; $mi <= 4; $mi++) {
//
//                                                            if (!InverterMPPTDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter, 'mppt_number' => $mi])->where('collect_time', date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)))->exists()) {
//
//                                                                $inverterMPPTLog = array();
//
//                                                                foreach ($responseData as $rData) {
//
//                                                                    if ($rData->key == 'DV' . $mi) {
//
//                                                                        $inverterMPPTLog['mppt_voltage'] = $rData->value != null ? $rData->value : 0;
//                                                                    }
//
//                                                                    if ($rData->key == 'DC' . $mi) {
//
//                                                                        $inverterMPPTLog['mppt_current'] = $rData->value != null ? $rData->value : 0;
//                                                                    }
//
//                                                                    if ($rData->key == 'DP' . $mi) {
//
//                                                                        $inverterMPPTLog['mppt_power'] = $rData->value != null ? ($rData->value / 1000) : 0;
//                                                                    }
//                                                                }
//
//                                                                $inverterMPPTLog['collect_time'] = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
//                                                                $inverterMPPTLog['plant_id'] = $plantID;
//                                                                $inverterMPPTLog['site_id'] = $siteID;
//                                                                $inverterMPPTLog['dv_inverter'] = $smartInverter;
//                                                                $inverterMPPTLog['mppt_number'] = $mi;
//
//                                                                $inverterMPPTResponse = InverterMPPTDetail::create($inverterMPPTLog);
//                                                            }
//                                                        }
//                                                    }
//                                                } else {
//                                                    print_r('not today');
//                                                $todayLastTime = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->whereDate('collect_time', $lastRecordDate)->exists() ? InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:00:00');
                                                array_push( $dataArrayDetails,date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)));
                                                $invertDetailExist = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter, 'collect_time' => date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))])->orderBy('collect_time', 'desc')->first();
                                                if (!$invertDetailExist) {
                                                    $inverterDetailLog = array();

                                                    $inverterDetailLog['plant_id'] = $plantID;
                                                    $inverterDetailLog['siteId'] = $siteID;
                                                    $inverterDetailLog['dv_inverter'] = $smartInverter;
                                                    $keys = array_keys(array_column($responseData, 'key'), 'APo_t1');
                                                    if ($keys) {
                                                        $inverterDetailLog['inverterPower'] = ($responseData[$keys[0]]->value / 1000);
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['inverterPower'] = 0;
                                                    }
                                                    $dailyGen = array_keys(array_column($responseData, 'key'), 'Etdy_ge1');
                                                    if ($dailyGen) {
                                                        $inverterDetailLog['daily_generation'] = $responseData[$dailyGen[0]]->value;
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['daily_generation'] = 0;
                                                    }
                                                    $mpptPow = array_keys(array_column($responseData, 'key'), 'DPi_t1');
                                                    if ($mpptPow) {
                                                        $inverterDetailLog['mpptPower'] = ($responseData[$mpptPow[0]]->value / 1000);
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['mpptPower'] = 0;
                                                    }
                                                    $freq = array_keys(array_column($responseData, 'key'), 'PG_F_METER1');
                                                    if ($freq) {
                                                        $inverterDetailLog['frequency'] = ($responseData[$freq[0]]->value / 1000);
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['frequency'] = 0;
                                                    }
                                                    $invertTemp = array_keys(array_column($responseData, 'key'), 'T_in1');
                                                    if ($invertTemp) {
                                                        $inverterDetailLog['inverterTemperature'] = $responseData[$invertTemp[0]]->value;
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['inverterTemperature'] = 0;
                                                    }
                                                    $phaseVolt = array_keys(array_column($responseData, 'key'), 'AV1');
                                                    if ($phaseVolt) {
                                                        $inverterDetailLog['phase_voltage_r'] = $responseData[$phaseVolt[0]]->value;
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['phase_voltage_r'] = 0;
                                                    }
                                                    $phaseVoltS = array_keys(array_column($responseData, 'key'), 'AV2');
                                                    if ($phaseVoltS) {
                                                        $inverterDetailLog['phase_voltage_s'] = $responseData[$phaseVoltS[0]]->value;
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['phase_voltage_s'] = 0;
                                                    }
                                                    $phaseVoltT = array_keys(array_column($responseData, 'key'), 'AV3');
                                                    if ($phaseVoltT) {
                                                        $inverterDetailLog['phase_voltage_t'] = $responseData[$phaseVoltT[0]]->value;
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['phase_voltage_t'] = 0;
                                                    }
                                                    $phaseCurrR = array_keys(array_column($responseData, 'key'), 'AC1');
                                                    if ($phaseCurrR) {
                                                        $inverterDetailLog['phase_current_r'] = $responseData[$phaseCurrR[0]]->value;
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['phase_current_r'] = 0;
                                                    }
                                                    $phaseCurrS = array_keys(array_column($responseData, 'key'), 'AC2');
                                                    if ($phaseCurrS) {
                                                        $inverterDetailLog['phase_current_s'] = $responseData[$phaseCurrS[0]]->value;
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['phase_current_s'] = 0;
                                                    }
                                                    $phaseCurrT = array_keys(array_column($responseData, 'key'), 'AC3');
                                                    if ($phaseCurrT) {
                                                        $inverterDetailLog['phase_current_t'] = $responseData[$phaseCurrT[0]]->value;
                                                    }
                                                    else
                                                    {
                                                        $inverterDetailLog['phase_current_t'] = 0;
                                                    }

                                                    $inverterDetailLog['collect_time'] = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
                                                    $invertDetails = new InverterDetail();
                                                    $invertDetails->plant_id = $plantID;
                                                    $invertDetails->siteId = $siteID;
                                                    $invertDetails->dv_inverter = $smartInverter;
                                                    $invertDetails->inverterPower = $inverterDetailLog['inverterPower'];
                                                    $invertDetails->daily_generation = $inverterDetailLog['daily_generation'];
                                                    $invertDetails->mpptPower = $inverterDetailLog['mpptPower'];
                                                    $invertDetails->frequency = $inverterDetailLog['frequency'];
                                                    $invertDetails->inverterTemperature = $inverterDetailLog['inverterTemperature'];
                                                    $invertDetails->phase_voltage_r = $inverterDetailLog['phase_voltage_r'];
                                                    $invertDetails->phase_voltage_s = $inverterDetailLog['phase_voltage_s'];
                                                    $invertDetails->phase_voltage_t = $inverterDetailLog['phase_voltage_t'];
                                                    $invertDetails->phase_current_r = $inverterDetailLog['phase_current_r'];
                                                    $invertDetails->phase_current_s = $inverterDetailLog['phase_current_s'];
                                                    $invertDetails->phase_current_t = $inverterDetailLog['phase_current_t'];
                                                    $invertDetails->collect_time = $inverterDetailLog['collect_time'];
                                                    $invertDetails->save();
//                                                $collectTimeDate = date('Y-m-d', ($smartInverterFinalData->collectTime));

//                                                    $inverterDetailResponse = InverterDetail::create($inverterDetailLog);
                                                    for ($mi = 1; $mi <= 4; $mi++) {
                                                        $mpptData = InverterMPPTDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter, 'mppt_number' => $mi])->where('collect_time', date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)))->orderBy('collect_time', 'desc')->first();
                                                        if (!$mpptData) {
                                                            $inverterMPPTLog = array();

                                                            foreach ($responseData as $rData) {

                                                                if ($rData->key == 'DV' . $mi) {

                                                                    $inverterMPPTLog['mppt_voltage'] = $rData->value != null ? $rData->value : 0;
                                                                }

                                                                if ($rData->key == 'DC' . $mi) {

                                                                    $inverterMPPTLog['mppt_current'] = $rData->value != null ? $rData->value : 0;
                                                                }

                                                                if ($rData->key == 'DP' . $mi) {

                                                                    $inverterMPPTLog['mppt_power'] = $rData->value != null ? ($rData->value / 1000) : 0;
                                                                }
                                                            }
                                                            $inverterMPPTLog['collect_time'] = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
                                                            $inverterMPPTLog['plant_id'] = $plantID;
                                                            $inverterMPPTLog['site_id'] = $siteID;
                                                            $inverterMPPTLog['dv_inverter'] = $smartInverter;
                                                            $inverterMPPTLog['mppt_number'] = $mi;
                                                            $inverterMPPTResponse = InverterMPPTDetail::create($inverterMPPTLog);
//                                                        $mpptData->update($inverterMPPTLog);

                                                        }
//                                                    else {
//                                                        $inverterMPPTResponse = InverterMPPTDetail::create($inverterMPPTLog);
//                                                    }
                                                    }
//                                                    $result = $invertDetailExist->update($inverterDetailLog);

                                                }
//                                                else {
//                                                    $inverterDetailResponse = InverterDetail::create($inverterDetailLog);
//                                                    for ($mi = 1; $mi <= 4; $mi++) {
//
//                                                        $inverterMPPTLog = array();
//
//                                                        foreach ($responseData as $rData) {
//
//                                                            if ($rData->key == 'DV' . $mi) {
//
//                                                                $inverterMPPTLog['mppt_voltage'] = $rData->value != null ? $rData->value : 0;
//                                                            }
//
//                                                            if ($rData->key == 'DC' . $mi) {
//
//                                                                $inverterMPPTLog['mppt_current'] = $rData->value != null ? $rData->value : 0;
//                                                            }
//
//                                                            if ($rData->key == 'DP' . $mi) {
//
//                                                                $inverterMPPTLog['mppt_power'] = $rData->value != null ? ($rData->value / 1000) : 0;
//                                                            }
//                                                        }
//
//                                                        $inverterMPPTLog['collect_time'] = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
//                                                        $inverterMPPTLog['site_id'] = $siteID;
//                                                        $inverterMPPTLog['plant_id'] = $plantID;
//                                                        $inverterMPPTLog['dv_inverter'] = $smartInverter;
//                                                        $inverterMPPTLog['mppt_number'] = $mi;
//
//                                                        $inverterMPPTResponse = InverterMPPTDetail::create($inverterMPPTLog);
//                                                    }
//                                               }




//                                                    $inverterFinalData = InverterDetail::where('collect_time',$inverterDetailLog['collect_time'])->orderBy('collect_time')->first();
//                                                    if($inverterFinalData)
//                                                    {
//                                                        $inverterFinalData
//                                                    }
//                                                    else {

//                                                    }

//                                                }
                                            }
                                        }
//                                        return $dataArrayDetails;
                                        print_r('Invert Detail Loop Time');
                                        print_r(date("Y-m-d H:i:s"));
                                        print_r("\n");
                                    }
                                }

                                //DAILY INVERTER DATA
                                $dailyInvData = array();

                                $dailyGenerationData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->exists() ? InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;

                                $dailyInvData['plant_id'] = $plantID;
                                $dailyInvData['siteId'] = $siteID;
                                $dailyInvData['dv_inverter'] = $smartInverter;
                                $dailyInvData['updated_at'] = $currentTime;
                                $dailyInvData['daily_generation'] = $dailyGenerationData;

                                //$dailyGenerationData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->exists() ? InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;

                                $DailyInvDataExist = DailyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('created_at', $lastRecordDate)->first();

                                if ($DailyInvDataExist) {

                                    $dailyInvDataResponse = $DailyInvDataExist->fill($dailyInvData)->save();
                                } else {

                                    $dailyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($lastRecordDate));
                                    $dailyInvDataResponse = DailyInverterDetail::create($dailyInvData);
                                }

                                break;
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

                            if ($monthlyInvDataExist) {

                                $monthlyInvDataResponse = $monthlyInvDataExist->fill((array)$monthlyInvData)->save();
                            } else {

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

                            if ($yearlyInvDataExist) {

                                $yearlyInvDataResponse = $yearlyInvDataExist->fill((array)$yearlyInvData)->save();
                            } else {

                                $yearlyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($lastRecordDate));
                                $yearlyInvDataResponse = YearlyInverterDetail::create((array)$yearlyInvData);
                            }
                        }

                        //SMART INVERTER GENERATION LOG DATA
                        if (!(empty($siteSmartInverterLogStartTime))) {

                            $minTimeSmartInverter = date('Y-m-d', min($siteSmartInverterLogStartTime));
//                            return [date($minTimeSmartInverter . ' 00:00:00'),date($minTimeSmartInverter . ' 23:59:59')];
//                            return

                            $smartInverterStartTimeData = InverterDetail::select(DB::raw('SUM(inverterPower) as current_generation, SUM(daily_generation) as totalEnergy'), 'collect_time')->where(['plant_id' => $plantID, 'siteId' => $siteID])->whereBetween('collect_time', [date($minTimeSmartInverter . ' 00:00:00'), date($minTimeSmartInverter . ' 23:59:59')])->groupBy('collect_time')->get();
//                            return $smartInverterStartTimeData;

                            foreach ($smartInverterStartTimeData as $generationLogKey => $generationLogData) {
                                $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $generationLogData->collect_time)->first();
//                                 print_r('\n');
//                                 print_r('generationData');
//                                 print_r($generationData);
                                if (!empty($generationData)) {
//                                  print_r('update generation data');
                                    if ($generationData->current_generation != $generationLogData->current_generation || $generationData->totalEnergy != $generationLogData->totalEnergy) {
                                        $generationData->current_generation = $generationLogData->current_generation;
                                        $generationData->totalEnergy = $generationLogData->totalEnergy;
                                        $generationData->save();
                                    }
                                } else {
                                    $generationLogResponseData = new GenerationLog();
                                    $generationLogResponseData->plant_id = $plantID;
                                    $generationLogResponseData->siteId = $siteID;
                                    $generationLogResponseData->current_generation = $generationLogData->current_generation;
                                    $generationLogResponseData->comm_failed = 0;
                                    $generationLogResponseData->cron_job_id = $generationLogMaxCronJobID;
                                    $generationLogResponseData->current_consumption = 0;
                                    $generationLogResponseData->current_grid = 0;
                                    $generationLogResponseData->current_irradiance = 0;
                                    $generationLogResponseData->totalEnergy = $generationLogData->totalEnergy;
                                    $generationLogResponseData->collect_time = $generationLogData->collect_time;
                                    $generationLogResponseData->save();

//                                    $generationLog = array();
////                                    print_r('save generation data');
//
//                                    $generationLog['plant_id'] = $plantID;
//                                    $generationLog['siteId'] = $siteID;
//                                    $generationLog['current_generation'] = $generationLogData->current_generation;
//                                    $generationLog['comm_failed'] = 0;
//                                    $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
//                                    $generationLog['current_consumption'] = 0;
//                                    $generationLog['current_grid'] = 0;
//                                    $generationLog['current_irradiance'] = 0;
//                                    $generationLog['totalEnergy'] = $generationLogData->totalEnergy;
//                                    $generationLog['collect_time'] = $generationLogData->collect_time;
////                                    $generationLog['created_at'] = $currentTime;
////                                    $generationLog['updated_at'] = $currentTime;
//
//                                    $generationLogResponse = GenerationLog::create($generationLog);

                                }
                            }
                        }
//                        $alertController = new SolisAlertsController();
//                        $alertData = $alertController->AlarmAndFault($token, $plantID, $siteID);
//                        return $alertData;
                    }
                }

                if (!(empty($siteAllInverterLogStartTime))) {

                    $minTimeInverter = date('Y-m-d', min($siteAllInverterLogStartTime));
                    $maxTimeInverter = date('Y-m-d', max($siteAllInverterLogStartTime));

                    $generationLogInverterStartTimeData = GenerationLog::select(DB::raw('SUM(current_generation) as current_generation, SUM(current_consumption) as current_consumption, SUM(current_grid) as current_grid, SUM(current_irradiance) as current_irradiance, SUM(totalEnergy) as totalEnergy'), 'collect_time')->where(['plant_id' => $plantID])->whereBetween('collect_time', [date($minTimeInverter . ' 00:00:00'), date($maxTimeInverter . ' 23:59:59')])->groupBy('collect_time')->get();
//                    return $generationLogInverterStartTimeData;

                    foreach ($generationLogInverterStartTimeData as $key45 => $processedData) {

                        $processedCurrentDataExist = ProcessedCurrentVariable::where(['plant_id' => $plantID])->where('collect_time', $processedData->collect_time)->first();
                        if ($processedCurrentDataExist) {

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

                            $processedCurrentDataResponse = $processedCurrentDataExist->fill($processedCurrentData)->save();
                        } else {

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
//                    return [$minTimeInverter,date('Y-m-d', strtotime("+1 day", strtotime($maxTimeInverter)))];

                    while ($minTimeInverter != date('Y-m-d', strtotime("+1 day", strtotime($maxTimeInverter)))) {

                        $plantDataDateToday = $minTimeInverter;
                        $plantDataDateYesterday = date('Y-m-d', strtotime('-1 day', strtotime($minTimeInverter)));

                        $plantDailyTotalGeneration = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->sum('daily_generation');

                        //PLANT DAILY DATA
                        $dailyProcessed['plant_id'] = $plantID;
                        $dailyProcessed['dailyGeneration'] = $plantDailyTotalGeneration;
                        $dailyProcessed['dailyGridPower'] = 0;
                        $dailyProcessed['dailyBoughtEnergy'] = 0;
                        $dailyProcessed['dailySellEnergy'] = 0;
                        $dailyProcessed['dailyMaxSolarPower'] = 0;
                        $dailyProcessed['dailyConsumption'] = $plantDailyTotalGeneration;
                        $dailyProcessed['dailySaving'] = (double)$plantDailyTotalGeneration * (double)$plant->benchmark_price;
                        $dailyProcessed['dailyIrradiance'] = 0;
                        $dailyProcessed['updated_at'] = $currentTime;

                        $dailyProcessedPlantDetailExist = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first();

                        if ($dailyProcessedPlantDetailExist) {

                            $dailyProcessedPlantDetailInsertionResponce = $dailyProcessedPlantDetailExist->fill($dailyProcessed)->save();
                        } else {

                            $dailyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($plantDataDateToday));
                            $dailyProcessedPlantDetailInsertionResponce = DailyProcessedPlantDetail::create($dailyProcessed);
                        }

                        $minTimeInverter = date('Y-m-d', strtotime("+1 day", strtotime($minTimeInverter)));
                    }

                    $logYear = date('Y', strtotime($minTimeInverter));
                    $logMonth = date('m', strtotime($minTimeInverter));

//                    $plantDailyGenerationDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyGeneration');
//                    $plantDailyConsumptionDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyConsumption');
//                    $plantDailyGridDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyGridPower');
//                    $plantDailyBoughtDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyBoughtEnergy');
//                    $plantDailySellDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailySellEnergy');
//                    $plantDailySavingDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailySaving');
                    $plantGenerationTableData = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->get();
                    $dataArray = json_decode(json_encode($plantGenerationTableData), true);
                    $plantDailyGenerationDataSum = array_sum(array_column($dataArray, 'dailyGeneration'));
                    $plantDailyConsumptionDataSum = array_sum(array_column($dataArray, 'dailyConsumption'));
                    $plantDailyGridDataSum = array_sum(array_column($dataArray, 'dailyGridPower'));
                    $plantDailyBoughtDataSum = array_sum(array_column($dataArray, 'dailyBoughtEnergy'));
                    $plantDailySellDataSum = array_sum(array_column($dataArray, 'dailySellEnergy'));
                    $plantDailySavingDataSum = array_sum(array_column($dataArray, 'dailySaving'));
//                    return [$plantDailyGenerationDataSum,$plantDailyConsumptionDataSum,$plantDailyGridDataSum,$plantDailyBoughtDataSum,$plantDailySellDataSum,$plantDailySavingDataSum];

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
                    } else {

                        $monthlyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
                        $monthlyProcessedPlantDetailResponse = MonthlyProcessedPlantDetail::create($monthlyProcessed);
                    }

//                    $plantmonthlyGenerationDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlyGeneration');
//                    $plantmonthlyConsumptionDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlyConsumption');
//                    $plantmonthlyGridDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlyGridPower');
//                    $plantmonthlyBoughtDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlyBoughtEnergy');
//                    $plantmonthlySellDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlySellEnergy');
//                    $plantmonthlySavingDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlySaving');
                    $plantMonthlyGenerationTableData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->get();
                    $monthlyDataArray = json_decode(json_encode($plantMonthlyGenerationTableData), true);
                    $plantmonthlyGenerationDataSum = array_sum(array_column($monthlyDataArray, 'monthlyGeneration'));
                    $plantmonthlyConsumptionDataSum = array_sum(array_column($monthlyDataArray, 'monthlyConsumption'));
                    $plantmonthlyGridDataSum = array_sum(array_column($monthlyDataArray, 'monthlyGridPower'));
                    $plantmonthlyBoughtDataSum = array_sum(array_column($monthlyDataArray, 'monthlyBoughtEnergy'));
                    $plantmonthlySellDataSum = array_sum(array_column($monthlyDataArray, 'monthlySellEnergy'));
                    $plantmonthlySavingDataSum = array_sum(array_column($monthlyDataArray, 'monthlySaving'));

                    $yearlyProcessed['plant_id'] = $plantID;
                    $yearlyProcessed['yearlyGeneration'] = $plantmonthlyGenerationDataSum;
                    $yearlyProcessed['yearlyConsumption'] = $plantmonthlyConsumptionDataSum;
                    $yearlyProcessed['yearlyGridPower'] = $plantmonthlyGridDataSum;
                    $yearlyProcessed['yearlyBoughtEnergy'] = $plantmonthlyBoughtDataSum;
                    $yearlyProcessed['yearlySellEnergy'] = $plantmonthlySellDataSum;
                    $yearlyProcessed['yearlySaving'] = $plantmonthlySavingDataSum;
                    $yearlyProcessed['updated_at'] = $currentTime;

                    $yearlyProcessedPlantDetailExist = yearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->orderBy('created_at', 'DESC')->first();

                    if ($yearlyProcessedPlantDetailExist) {

                        $yearlyProcessedPlantDetailResponse = $yearlyProcessedPlantDetailExist->fill($yearlyProcessed)->save();
                    } else {

                        $yearlyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
                        $yearlyProcessedPlantDetailResponse = yearlyProcessedPlantDetail::create($yearlyProcessed);
                    }
                }

                //PLANT Total DATA
                $plantyearlyCurrentPowerDataSum = ProcessedCurrentVariable::where('plant_id', $plantID)->exists() ? ProcessedCurrentVariable::where('plant_id', $plantID)->orderBy('collect_time', 'DESC')->first()->current_generation : 0;
//                $plantyearlyGenerationDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyGeneration');
//                $plantyearlyConsumptionDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyConsumption');
//                $plantyearlyGridDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyGridPower');
//                $plantyearlyBoughtDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyBoughtEnergy');
//                $plantyearlySellDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlySellEnergy');
//                $plantyearlySavingDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlySaving');
//                $plantyearlyIrradianceDataSum = YearlyProcessedPlantEMIDetail::where('plant_id', $plantID)->sum('yearly_irradiance');
                $plantYearlyGenerationTableData = YearlyProcessedPlantDetail::where('plant_id', $plantID)->get();
                $yearlyDataArray = json_decode(json_encode($plantYearlyGenerationTableData), true);
                $plantyearlyGenerationDataSum = array_sum(array_column($yearlyDataArray, 'yearlyGeneration'));
                $plantyearlyConsumptionDataSum = array_sum(array_column($yearlyDataArray, 'yearlyConsumption'));
                $plantyearlyGridDataSum = array_sum(array_column($yearlyDataArray, 'yearlyGridPower'));
                $plantyearlyBoughtDataSum = array_sum(array_column($yearlyDataArray, 'yearlyBoughtEnergy'));
                $plantyearlySellDataSum = array_sum(array_column($yearlyDataArray, 'yearlySellEnergy'));
                $plantyearlySavingDataSum = array_sum(array_column($yearlyDataArray, 'yearlySaving'));
                $plantyearlyIrradianceDataSum = array_sum(array_column($yearlyDataArray, 'yearly_irradiance'));

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
                } else {

                    $totalProcessed['created_at'] = $currentTime;
                    $totalProcessedPlantDetailResponse = TotalProcessedPlantDetail::create($totalProcessed);
                }
            }
        }

        $this->plantStatusUpdate();
        print_r('Crone Job End Time');
        print_r(date("Y-m-d H:i:s"));
        print_r("\n");
    }

    public function getOrgToken($solisAPIBaseURL, $appID, $appKey, $userAccount, $userPassword)
    {

        $curl = curl_init();

        $userCredentials = [

            "appSecret" => $appKey,
            "username" => $userAccount,
            "password" => $userPassword,
        ];

        curl_setopt_array($curl, array(

            CURLOPT_URL => $solisAPIBaseURL . '/account/v1.0/token?appId=' . $appID,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($userCredentials),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $responseType = json_decode($response);

        $token = isset($responseType->access_token) ? $responseType->access_token : '';

        return $token;
    }

    public function getOrgID($solisAPIBaseURL, $token)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => $solisAPIBaseURL . '/account/v1.0/info',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $responseType = json_decode($response);

        $token = isset($responseType->orgInfoList) ? $responseType->orgInfoList : [];
        $companyID = '';

        if (!empty($token)) {

            $companyID = $token[0]->companyId;
        }

        return $companyID;
    }

    public function getToken($solisAPIBaseURL, $appID, $appKey, $userAccount, $userPassword, $OrgId)
    {

        $curl = curl_init();

        $userCredentials = [

            "appSecret" => $appKey,
            "username" => $userAccount,
            "password" => $userPassword,
            "orgId" => $OrgId,
        ];

        curl_setopt_array($curl, array(

            CURLOPT_URL => $solisAPIBaseURL . '/account/v1.0/token?appId=' . $appID,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($userCredentials),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $responseType = json_decode($response);

        $token = isset($responseType->access_token) ? $responseType->access_token : '';

        return $token;
    }

    public function arrayDifferenceData($newArrayData)
    {
        $array = $newArrayData;
        return $array;
    }


    public function getPlantList($solisAPIBaseURL, $token)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => $solisAPIBaseURL . '/station/v1.0/list',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $responseType = json_decode($response);

        return $responseType;
    }

    public function getSiteDeviceList($solisAPIBaseURL, $token, $siteID)
    {

        $siteInverterList = [

            "stationId" => $siteID,
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $solisAPIBaseURL . '/station/v1.0/device',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($siteInverterList),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $plantDeviceList = json_decode($response);

        $plantDeviceListFinalResponse = isset($plantDeviceList) && isset($plantDeviceList->deviceListItems) ? $plantDeviceList->deviceListItems : [];

        return $plantDeviceListFinalResponse;
    }

    private function plantStatusUpdate()
    {

        $plants = DB::table('plants')
            ->join('plant_sites', 'plants.id', 'plant_sites.plant_id')
            ->select('plants.*', 'plant_sites.site_id')
            ->where('plants.meter_type', 'Solis')
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
}

