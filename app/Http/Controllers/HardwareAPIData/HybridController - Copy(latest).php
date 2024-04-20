<?php

namespace App\Http\Controllers\HardwareAPIData;

use App\Http\Controllers\Controller;
use App\Http\Models\InverterStatusCode;
use App\Http\Models\InverterVersionInformation;
use App\Http\Models\SolarEnergyUtilization;
use App\Http\Models\StationBattery;
use DateInterval;
use DateTime;
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


class HybridController extends Controller
{
    public $cronJobCollectTime;

    public function hybrid($plant, $plantId, $token, $solisAPIBaseURL, $processedMaxCronJobID, $dataCollectDate, $plantHasGridMeter, $currentTime, $envReductionValue, $benchMarkPrice, $generationLogMaxCronJobID)
    {

//        return [$plantId, $token, $solisAPIBaseURL, $processedMaxCronJobID, $dataCollectDate, $plantHasGridMeter, $currentTime, $envReductionValue, $benchMarkPrice, $generationLogMaxCronJobID];
        date_default_timezone_set('Asia/Karachi');
        if ($plantId) {

//            foreach ($allPlantsData as $key => $plant) {

            $siteAllInverterLogStartTime = array();
            $plantID = $plantId;
            $plantData = Plant::findOrFail($plantID);
//                return $plantID;

            $plantSites = PlantSite::where('plant_id', $plantID)->get();
//            return $plantSites;
            if ($plantSites) {
//                    $arrayDifference = array();

                foreach ($plantSites as $site) {

                    $siteSmartInverterArray = array();
                    $siteSmartInverterLogStartTime = array();

                    $siteID = $site->site_id;

//                    $alertController = new SolisAlertsController();
//                        return [$token,$plantID,$siteID];
//                    $alertData = $alertController->AlarmAndFault($token, $plantID, $siteID);
//                       return $alertData;

//                    return $plantStationData;
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
                    $inverterStatusArray = [];

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
//                                return
                            $inverterData =
                                [
                                    "deviceSn" => $dev->deviceSn,
                                    "deviceId" => $dev->deviceId
                                ];
//                            return json_encode($inverterData);

                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => $solisAPIBaseURL . '/device/v1.0/currentData',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'POST',
                                CURLOPT_POSTFIELDS => json_encode($inverterData),
                                CURLOPT_HTTPHEADER => array(
                                    'Authorization: Bearer ' . $token,
                                    'Content-Type: application/json'
                                ),
                            ));

                            $response = curl_exec($curl);
                            curl_close($curl);
                            $invertDataList = json_decode($response);
                            array_push($inverterStatusArray, $invertDataList->deviceState);
//                                return json_encode($invertDataList->deviceState);
                        }
//                            return $invertDataList;
//                            return $plantDeviceStatusList;
//                            return json_encode(count($siteSmartInverterArray));

                        if (in_array(1, $inverterStatusArray)) {
//                                $status = InverterStatusCode::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])
                            $updateSiteStatusArray['online_status'] = 'Y';
                        }

                        if (in_array(2, $inverterStatusArray)) {

                            $updateSiteStatusArray['online_status'] = 'A';
                        }

                        if (in_array(2, $inverterStatusArray) && in_array(1, $inverterStatusArray)) {

                            $updateSiteStatusArray['online_status'] = 'P_Y';
                        }

                        if (in_array(3, $inverterStatusArray)) {

                            $updateSiteStatusArray['online_status'] = 'N';
                        }
                        $status = 'default';
                        if ($inverterStatusArray) {
                            $statusArray = array_unique($inverterStatusArray);
                            if ($statusArray) {
                                $status = $statusArray[0];
                            }

                        }
                        $inverterStatusCode = InverterStatusCode::where('plant_name', 'solis')->where('code', $status)->first();
//                            return $inverterStatusCode;
                        if ($inverterStatusCode) {
                            $status = $inverterStatusCode->description;
                        }

//return $status;
                        //SITE STATUS UPDATE DATA
//                            $siteStatusUpdateResponse = PlantSite::where(['plant_id' => $plantID, 'site_id' => $siteID])->update(['online_status' => $updateSiteStatusArray]);
                        $siteStatusUpdateResponse = PlantSite::where(['plant_id' => $plantID, 'site_id' => $siteID])->first();
                        if ($siteStatusUpdateResponse) {
                            $siteStatusUpdateResponse->online_status = $updateSiteStatusArray['online_status'];
                            $siteStatusUpdateResponse->save();
                        }
                    }

                    //INVERTER LOG
                    foreach ($siteSmartInverterArray as $smartKey => $smartInverter) {
                        $inverterSerialNo = InverterSerialNo::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])->first();
                        if ($inverterSerialNo) {
                            $inverterSerialNo->status = $status;
                            $inverterSerialNo->save();
                        }
                        InverterEnergyLog::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])->whereDate('collect_time', date('Y-m-d'))->delete();
//                            if ($plant->plant_has_grid_meter == 'Y') {
//                                $solisMeterController = new SolisMeterController();
//                                $result = $solisMeterController->meterData($plantID, $siteID, $smartInverter, $processedMaxCronJobID);
////                                return $result;
//                            }
                        $lastRecordTimeStamp = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->exists() ? InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->orderBy('collect_time', 'DESC')->first()->collect_time : null;
//                            return $lastRecordTimeStamp;

                        if (isset($lastRecordTimeStamp) && $lastRecordTimeStamp) {

                            if (strtotime(date('Y-m-d', strtotime($lastRecordTimeStamp))) == strtotime(date('Y-m-d'))) {

                                $lastRecordDate = date('Y-m-d', strtotime($lastRecordTimeStamp));
                                $this->cronJobCollectTime = $lastRecordDate;
                            } else {

                                $lastRecordDate = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordTimeStamp)));
                                $this->cronJobCollectTime = $lastRecordDate;
                            }
                        } else {

                            $lastRecordDate = $dataCollectDate;
                            $this->cronJobCollectTime = $lastRecordDate;
                        }

//return $lastRecordDate;
                        $siteSmartInverterLogStartTime[] = strtotime($lastRecordDate);
                        $siteAllInverterLogStartTime[] = strtotime($lastRecordDate);
//                            return $lastRecordDate;
//                            return [json_encode(strtotime($lastRecordDate)),json_encode(strtotime(date('Y-m-d', strtotime("+1 days"))))];

                        while (strtotime($lastRecordDate) != strtotime(date('Y-m-d', strtotime("+1 days")))) {

                            $collectTime = date('Y-m-d', strtotime($lastRecordDate));
                            $dailyGenerationData = 0;
//                                return $lastRecordDate;

//                            $siteSmartInverterData = [
//
//                                "deviceSn" => $smartInverter,
//                                "endTime" => $lastRecordDate,
//                                "startTime" => $lastRecordDate,
//                                "timeType" => 1
//                            ];
//
//
//                            $siteSmartInverterCurl = curl_init();
//
//                            curl_setopt_array($siteSmartInverterCurl, array(
//
//                                CURLOPT_URL => $solisAPIBaseURL . '/device/v1.0/historical',
//                                CURLOPT_RETURNTRANSFER => true,
//                                CURLOPT_ENCODING => '',
//                                CURLOPT_MAXREDIRS => 10,
//                                CURLOPT_TIMEOUT => 0,
//                                CURLOPT_FOLLOWLOCATION => true,
//                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                                CURLOPT_CUSTOMREQUEST => 'POST',
//                                CURLOPT_POSTFIELDS => json_encode($siteSmartInverterData),
//                                CURLOPT_HTTPHEADER => array(
//                                    'Authorization: Bearer ' . $token,
//                                    'Content-Type: application/json'
//                                ),
//                            ));
//
//                            $siteSmartInverterResponse = curl_exec($siteSmartInverterCurl);
//
//                            curl_close($siteSmartInverterCurl);
//                            return $siteSmartInverterResponse;
//                            return $smartInverter;
                            $solisHistoricalData = $this->getHistoricalData($solisAPIBaseURL, $smartInverter, $lastRecordDate, $token);
//                            return $solisHistoricalData;
                            $siteSmartInverterLogStartTime[] = strtotime($this->cronJobCollectTime);
                            $siteAllInverterLogStartTime[] = strtotime($this->cronJobCollectTime);
//                             return $solisHistoricalData;
                            $siteSmartInverterResponseData = json_decode($solisHistoricalData);


                            if ($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->paramDataList)) {

                                $siteSmartInverterFinalData = $siteSmartInverterResponseData->paramDataList;

                                if ($siteSmartInverterFinalData) {
                                    $dataArrayDetails = [];

                                    foreach ($siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {
                                        $responseData = isset($smartInverterFinalData->dataList) ? $smartInverterFinalData->dataList : [];
                                        $invertDetailExist = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter, 'collect_time' => date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))])->orderBy('collect_time', 'desc')->first();
                                        $batteryStationData = StationBattery::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter, 'collect_time' => date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))])->orderBy('collect_time', 'desc')->first();
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
//                                                array_push( $dataArrayDetails,date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)));

                                            if (empty($invertDetailExist) || (date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))) > ($invertDetailExist['collect_time'])) {

                                                $inverterDetailLog = array();

                                                $inverterDetailLog['plant_id'] = $plantID;
                                                $inverterDetailLog['siteId'] = $siteID;
                                                $inverterDetailLog['dv_inverter'] = $smartInverter;

                                                if ($plant->grid_type == 'Three-phase') {
                                                    $keys = array_keys(array_column($responseData, 'key'), 'S_P_T');
                                                    if ($keys) {
                                                        $inverterDetailLog['inverterPower'] = ($responseData[$keys[0]]->value / 1000);
                                                    } else {
                                                        $inverterDetailLog['inverterPower'] = 0;
                                                    }
//                                                    return $inverterDetailLog['inverterPower'];
                                                } else {
                                                    $keys = array_keys(array_column($responseData, 'key'), 'DPi_t1');
                                                    if ($keys) {
                                                        $inverterDetailLog['inverterPower'] = ($responseData[$keys[0]]->value / 1000);
                                                    } else {
                                                        $inverterDetailLog['inverterPower'] = 0;
                                                    }
                                                }
                                                $dailyGen = array_keys(array_column($responseData, 'key'), 'Etdy_ge1');
                                                if ($dailyGen) {
                                                    $inverterDetailLog['daily_generation'] = $responseData[$dailyGen[0]]->value;
                                                } else {
                                                    $inverterDetailLog['daily_generation'] = 0;
                                                }
                                                $dailyCons = array_keys(array_column($responseData, 'key'), 'Etdy_use1');
                                                if ($dailyCons) {
                                                    $inverterDetailLog['daily_consumption'] = $responseData[$dailyCons[0]]->value;
                                                } else {
                                                    $inverterDetailLog['daily_consumption'] = 0;
                                                }
                                                $dailyCurrentCons = array_keys(array_column($responseData, 'key'), 'E_Puse_t1');
                                                if ($dailyCurrentCons) {
                                                    $inverterDetailLog['current_consumption'] = $responseData[$dailyCurrentCons[0]]->value;
                                                } else {
                                                    $inverterDetailLog['current_consumption'] = 0;
                                                }
//                                                return $inverterDetailLog['daily_consumption'];
                                                $mpptPow = array_keys(array_column($responseData, 'key'), 'DPi_t1');
                                                if ($mpptPow) {
                                                    $inverterDetailLog['mpptPower'] = ($responseData[$mpptPow[0]]->value / 1000);
                                                } else {
                                                    $inverterDetailLog['mpptPower'] = 0;
                                                }
                                                $freq = array_keys(array_column($responseData, 'key'), 'PG_F_METER1');
                                                if ($freq) {
                                                    $inverterDetailLog['frequency'] = ($responseData[$freq[0]]->value / 1000);
                                                } else {
                                                    $inverterDetailLog['frequency'] = 0;
                                                }
                                                $invertTemp = array_keys(array_column($responseData, 'key'), 'AC_T');
                                                if ($invertTemp) {
                                                    $inverterDetailLog['inverterTemperature'] = $responseData[$invertTemp[0]]->value;
                                                } else {
                                                    $inverterDetailLog['inverterTemperature'] = 0;
                                                }
                                                $DcinvertTemp = array_keys(array_column($responseData, 'key'), 'T_DC');
                                                if ($DcinvertTemp) {
                                                    $inverterDetailLog['DCinverterTemperature'] = $responseData[$DcinvertTemp[0]]->value;
                                                } else {
                                                    $inverterDetailLog['DCinverterTemperature'] = 0;
                                                }
                                                $BatteryinvertTemp = array_keys(array_column($responseData, 'key'), 'B_T1');
                                                if ($BatteryinvertTemp) {
                                                    $inverterDetailLog['BatteryTemperature'] = $responseData[$BatteryinvertTemp[0]]->value;
                                                } else {
                                                    $inverterDetailLog['BatteryTemperature'] = 0;
                                                }
                                                $phaseVolt = array_keys(array_column($responseData, 'key'), 'AV1');
                                                if ($phaseVolt) {
                                                    $inverterDetailLog['phase_voltage_r'] = $responseData[$phaseVolt[0]]->value;
                                                } else {
                                                    $inverterDetailLog['phase_voltage_r'] = 0;
                                                }
                                                $phaseVoltS = array_keys(array_column($responseData, 'key'), 'AV2');
                                                if ($phaseVoltS) {
                                                    $inverterDetailLog['phase_voltage_s'] = $responseData[$phaseVoltS[0]]->value;
                                                } else {
                                                    $inverterDetailLog['phase_voltage_s'] = 0;
                                                }
                                                $phaseVoltT = array_keys(array_column($responseData, 'key'), 'AV3');
                                                if ($phaseVoltT) {
                                                    $inverterDetailLog['phase_voltage_t'] = $responseData[$phaseVoltT[0]]->value;
                                                } else {
                                                    $inverterDetailLog['phase_voltage_t'] = 0;
                                                }
                                                $phaseCurrR = array_keys(array_column($responseData, 'key'), 'AC1');
                                                if ($phaseCurrR) {
                                                    $inverterDetailLog['phase_current_r'] = $responseData[$phaseCurrR[0]]->value;
                                                } else {
                                                    $inverterDetailLog['phase_current_r'] = 0;
                                                }
                                                $phaseCurrS = array_keys(array_column($responseData, 'key'), 'AC2');
                                                if ($phaseCurrS) {
                                                    $inverterDetailLog['phase_current_s'] = $responseData[$phaseCurrS[0]]->value;
                                                } else {
                                                    $inverterDetailLog['phase_current_s'] = 0;
                                                }
                                                $phaseCurrT = array_keys(array_column($responseData, 'key'), 'AC3');
                                                if ($phaseCurrT) {
                                                    $inverterDetailLog['phase_current_t'] = $responseData[$phaseCurrT[0]]->value;
                                                } else {
                                                    $inverterDetailLog['phase_current_t'] = 0;
                                                }
                                                if ($plant->grid_type == 'Three-phase') {
                                                    $totalGridVoltage = array_keys(array_column($responseData, 'key'), 'PG_Pt1');
                                                    if ($totalGridVoltage) {
                                                        $inverterDetailLog['total_grid_voltage'] = $responseData[$totalGridVoltage[0]]->value;
                                                    } else {
                                                        $inverterDetailLog['total_grid_voltage'] = 0;
                                                    }
                                                } else {
                                                    $totalGridVoltage = array_keys(array_column($responseData, 'key'), 'PG_V4');
                                                    if ($totalGridVoltage) {
                                                        $inverterDetailLog['total_grid_voltage'] = $responseData[$totalGridVoltage[0]]->value;
                                                    } else {
                                                        $inverterDetailLog['total_grid_voltage'] = 0;
                                                    }
                                                }
                                                $generalSettings = array_keys(array_column($responseData, 'key'), 'GESET');
                                                if ($generalSettings) {
                                                    $inverterDetailLog['general_settings'] = $responseData[$generalSettings[0]]->value;
                                                } else {
                                                    $inverterDetailLog['general_settings'] = 0;
                                                }
                                                $productionCompliance = array_keys(array_column($responseData, 'key'), 'SS_CY1');
                                                if ($productionCompliance) {
                                                    $inverterDetailLog['production_compliance'] = $responseData[$productionCompliance[0]]->value;
                                                } else {
                                                    $inverterDetailLog['production_compliance'] = 0;
                                                }
                                                $ratedPower = array_keys(array_column($responseData, 'key'), 'Pr1');
                                                if ($ratedPower) {
                                                    $inverterDetailLog['rated_power'] = $responseData[$ratedPower[0]]->value;
                                                } else {
                                                    $inverterDetailLog['rated_power'] = 0;
                                                }
                                                $protocolVersion = array_keys(array_column($responseData, 'key'), 'PTCv1');
                                                if ($protocolVersion) {
                                                    $inverterDetailLog['protocol_version'] = $responseData[$protocolVersion[0]]->value;
                                                } else {
                                                    $inverterDetailLog['protocol_version'] = 0;
                                                }
                                                if ($plant->grid_type == 'Three-phase') {
                                                    $control_software_version = array_keys(array_column($responseData, 'key'), 'MAIN');
                                                    if ($control_software_version) {
                                                        $inverterDetailLog['control_software_version'] = $responseData[$control_software_version[0]]->value;
                                                    } else {
                                                        $inverterDetailLog['control_software_version'] = 0;
                                                    }
                                                } else {
                                                    $control_software_version = array_keys(array_column($responseData, 'key'), 'SWctrl_v1');
                                                    if ($control_software_version) {
                                                        $inverterDetailLog['control_software_version'] = $responseData[$control_software_version[0]]->value;
                                                    } else {
                                                        $inverterDetailLog['control_software_version'] = 0;
                                                    }
                                                }
                                                $communication_cpu_software = array_keys(array_column($responseData, 'key'), 'COMM_CPU_SWv1');
                                                if ($communication_cpu_software) {
                                                    $inverterDetailLog['communication_cpu_software'] = $responseData[$communication_cpu_software[0]]->value;
                                                } else {
                                                    $inverterDetailLog['communication_cpu_software'] = 0;
                                                }
                                                $HMI = array_keys(array_column($responseData, 'key'), 'HMI');
                                                if ($HMI) {
                                                    $inverterDetailLog['HMI'] = $responseData[$HMI[0]]->value;
                                                } else {
                                                    $inverterDetailLog['HMI'] = 0;
                                                }
                                                $LithiumBatteryVersion = array_keys(array_column($responseData, 'key'), 'LBVN');
                                                if ($LithiumBatteryVersion) {
                                                    $inverterDetailLog['lithium_battery_version'] = $responseData[$LithiumBatteryVersion[0]]->value;
                                                } else {
                                                    $inverterDetailLog['lithium_battery_version'] = 0;
                                                }
                                                $main1 = array_keys(array_column($responseData, 'key'), 'MAIN_1');
                                                if ($main1) {
                                                    $inverterDetailLog['main_1'] = $responseData[$main1[0]]->value;
                                                } else {
                                                    $inverterDetailLog['main_1'] = 0;
                                                }
                                                $main2 = array_keys(array_column($responseData, 'key'), 'MAIN_2');
                                                if ($main2) {
                                                    $inverterDetailLog['main_2'] = $responseData[$main2[0]]->value;
                                                } else {
                                                    $inverterDetailLog['main_2'] = 0;
                                                }
                                                $consumptionVoltage = array_keys(array_column($responseData, 'key'), 'E_Vuse1');
                                                if ($consumptionVoltage) {
                                                    $inverterDetailLog['consumption_voltage'] = $responseData[$consumptionVoltage[0]]->value;
                                                } else {
                                                    $inverterDetailLog['consumption_voltage'] = 0;
                                                }
                                                $consumptionFrequency = array_keys(array_column($responseData, 'key'), 'E_Fuse1');
                                                if ($consumptionFrequency) {
                                                    $inverterDetailLog['consumption_frequency'] = $responseData[$consumptionFrequency[0]]->value;
                                                } else {
                                                    $inverterDetailLog['consumption_frequency'] = 0;
                                                }
                                                $consumptionActivePowerR = array_keys(array_column($responseData, 'key'), 'E_Puse1');
                                                if ($consumptionActivePowerR) {
                                                    $inverterDetailLog['consumption_active_power_r'] = $responseData[$consumptionActivePowerR[0]]->value;
                                                } else {
                                                    $inverterDetailLog['consumption_active_power_r'] = 0;
                                                }
                                                $totalConsumptionEnergy = array_keys(array_column($responseData, 'key'), 'Et_use1');
                                                if ($totalConsumptionEnergy) {
                                                    $inverterDetailLog['total_consumption_energy'] = $responseData[$totalConsumptionEnergy[0]]->value;
                                                } else {
                                                    $inverterDetailLog['total_consumption_energy'] = 0;
                                                }
                                                $totalOutputVoltage = array_keys(array_column($responseData, 'key'), 'AV_LINE1');
                                                if ($totalOutputVoltage) {
                                                    $inverterDetailLog['inverter_output_voltage'] = $responseData[$totalOutputVoltage[0]]->value;
                                                } else {
                                                    $inverterDetailLog['inverter_output_voltage'] = 0;
                                                }
                                                $acPowerRUA = array_keys(array_column($responseData, 'key'), 'AP1');
                                                if ($acPowerRUA) {
                                                    $inverterDetailLog['ac_power_r_u_a'] = $responseData[$acPowerRUA[0]]->value;
                                                } else {
                                                    $inverterDetailLog['ac_power_r_u_a'] = 0;
                                                }
                                                $totalProduction = array_keys(array_column($responseData, 'key'), 'Et_ge0');
                                                if ($totalProduction) {
                                                    $inverterDetailLog['total_production'] = $responseData[$totalProduction[0]]->value;
                                                } else {
                                                    $inverterDetailLog['total_production'] = 0;
                                                }
                                                $totalConsumption = array_keys(array_column($responseData, 'key'), 'E_C_T');
                                                if ($totalConsumption) {
                                                    $inverterDetailLog['total_consumption'] = $responseData[$totalConsumption[0]]->value;
                                                } else {
                                                    $inverterDetailLog['total_consumption'] = 0;
                                                }
                                                $inverterOutputPowerL1 = array_keys(array_column($responseData, 'key'), 'INV_O_P_L1');
                                                if ($inverterOutputPowerL1) {
                                                    $inverterOutputPowerL1Data['output_power_l1'] = $responseData[$inverterOutputPowerL1[0]]->value;
                                                } else {
                                                    $inverterOutputPowerL1Data['output_power_l1'] = 0;
                                                }
                                                $inverterOutputPowerL2 = array_keys(array_column($responseData, 'key'), 'INV_O_P_L2');
                                                if ($inverterOutputPowerL2) {
                                                    $inverterOutputPowerL2Data['output_power_l2'] = $responseData[$inverterOutputPowerL2[0]]->value;
                                                } else {
                                                    $inverterOutputPowerL2Data['output_power_l2'] = 0;
                                                }
                                                $inverterOutputPowerL3 = array_keys(array_column($responseData, 'key'), 'INV_O_P_L3');
                                                if ($inverterOutputPowerL3) {
                                                    $inverterOutputPowerL3Data['output_power_l3'] = $responseData[$inverterOutputPowerL3[0]]->value;
                                                } else {
                                                    $inverterOutputPowerL3Data['output_power_l3'] = 0;
                                                }
                                                $totalinverterOutputPower = array_keys(array_column($responseData, 'key'), 'INV_O_P_T');
                                                if ($totalinverterOutputPower) {
                                                    $totalinverterOutputPowerData['total_output_power'] = $responseData[$totalinverterOutputPower[0]]->value;
                                                } else {
                                                    $totalinverterOutputPowerData['total_output_power'] = 0;
                                                }
                                                $loadVoltagel1 = array_keys(array_column($responseData, 'key'), 'C_V_L1');
                                                if ($loadVoltagel1) {
                                                    $loadVoltagel1Data['load_voltage_l1'] = $responseData[$loadVoltagel1[0]]->value;
                                                } else {
                                                    $loadVoltagel1Data['load_voltage_l1'] = 0;
                                                }
                                                $loadVoltagel2 = array_keys(array_column($responseData, 'key'), 'C_V_L2');
                                                if ($loadVoltagel2) {
                                                    $loadVoltagel2Data['load_voltage_l2'] = $responseData[$loadVoltagel2[0]]->value;
                                                } else {
                                                    $loadVoltagel2Data['load_voltage_l2'] = 0;
                                                }
                                                $loadVoltagel3 = array_keys(array_column($responseData, 'key'), 'C_V_L3');
                                                if ($loadVoltagel3) {
                                                    $loadVoltagel3Data['load_voltage_l3'] = $responseData[$loadVoltagel3[0]]->value;
                                                } else {
                                                    $loadVoltagel3Data['load_voltage_l3'] = 0;
                                                }
                                                $loadVoltagelN = array_keys(array_column($responseData, 'key'), 'L_V_LN');
                                                if ($loadVoltagelN) {
                                                    $loadVoltagelNData['load_voltage_ln'] = $responseData[$loadVoltagelN[0]]->value;
                                                } else {
                                                    $loadVoltagelNData['load_voltage_ln'] = 0;
                                                }
                                                $InverterOutputPowerlN = array_keys(array_column($responseData, 'key'), 'I_O_P_LN');
                                                if ($InverterOutputPowerlN) {
                                                    $InverterOutputPowerlNData['inverter_output_power_ln'] = $responseData[$InverterOutputPowerlN[0]]->value;
                                                } else {
                                                    $InverterOutputPowerlNData['inverter_output_power_ln'] = 0;
                                                }
                                                $GeneInputLoadEnable = array_keys(array_column($responseData, 'key'), 'ENABLE_EGiAS_LOo');
                                                if ($GeneInputLoadEnable) {
                                                    $GeneInputLoadEnableData['Gene_Input_Load_Enable'] = $responseData[$GeneInputLoadEnable[0]]->value;
                                                } else {
                                                    $GeneInputLoadEnableData['Gene_Input_Load_Enable'] = 0;
                                                }
                                                $ConsumpApparentPower = array_keys(array_column($responseData, 'key'), 'E_Suse_t1');
                                                if ($ConsumpApparentPower) {
                                                    $ConsumpApparentPowerData['Consump_Apparent_Power'] = $responseData[$ConsumpApparentPower[0]]->value;
                                                } else {
                                                    $ConsumpApparentPowerData['Consump_Apparent_Power'] = 0;
                                                }
                                                $LoadFrequency = array_keys(array_column($responseData, 'key'), 'L_F');
                                                if ($LoadFrequency) {
                                                    $LoadFrequencyData['load_frequency'] = $responseData[$LoadFrequency[0]]->value;
                                                } else {
                                                    $LoadFrequencyData['load_frequency'] = 0;
                                                }
//                                                $keys = array_keys(array_column($responseData, 'key'), 'B_left_cap1');
//                                                if ($keys) {
//                                                    $inverterDetailLog['battery_capacity'] = $responseData[$keys[0]]->value . $responseData[$keys[0]]->unit;
//                                                } else {
//                                                    $inverterDetailLog['battery_capacity'] = 0;
//                                                }
//                                                $batteryPower = array_keys(array_column($responseData, 'key'), 'B_P1');
//                                                if ($batteryPower) {
//                                                    $inverterDetailLog['battery_power'] = $responseData[$batteryPower[0]]->value;
//                                                } else {
//                                                    $inverterDetailLog['battery_power'] = 0;
//                                                }

                                                $inverterDetailLog['collect_time'] = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
                                                $invertDetails = new InverterDetail();
                                                $invertDetails->plant_id = $plantID;
                                                $invertDetails->siteId = $siteID;
                                                $invertDetails->dv_inverter = $smartInverter;
                                                $invertDetails->inverterPower = $inverterDetailLog['inverterPower'];
                                                $invertDetails->daily_generation = $inverterDetailLog['daily_generation'];
                                                $invertDetails->daily_consumption = $inverterDetailLog['daily_consumption'];
                                                $invertDetails->current_consumption = $inverterDetailLog['current_consumption'];
                                                $invertDetails->mpptPower = $inverterDetailLog['mpptPower'];
                                                $invertDetails->frequency = $inverterDetailLog['frequency'];
                                                $invertDetails->inverterTemperature = $inverterDetailLog['inverterTemperature'];
                                                $invertDetails->phase_voltage_r = $inverterDetailLog['phase_voltage_r'];
                                                $invertDetails->phase_voltage_s = $inverterDetailLog['phase_voltage_s'];
                                                $invertDetails->phase_voltage_t = $inverterDetailLog['phase_voltage_t'];
                                                $invertDetails->phase_current_r = $inverterDetailLog['phase_current_r'];
                                                $invertDetails->phase_current_s = $inverterDetailLog['phase_current_s'];
                                                $invertDetails->phase_current_t = $inverterDetailLog['phase_current_t'];
                                                $invertDetails->total_grid_voltage = $inverterDetailLog['total_grid_voltage'];
                                                $invertDetails->consumption_voltage = $inverterDetailLog['consumption_voltage'];
                                                $invertDetails->consumption_frequency = $inverterDetailLog['consumption_frequency'];
                                                $invertDetails->consumption_active_power_r = $inverterDetailLog['consumption_active_power_r'];
                                                $invertDetails->total_consumption_energy = $inverterDetailLog['total_consumption_energy'];
                                                $invertDetails->inverter_output_voltage = $inverterDetailLog['inverter_output_voltage'];
                                                $invertDetails->ac_power_r_u_a = $inverterDetailLog['ac_power_r_u_a'];
                                                $invertDetails->total_production = $inverterDetailLog['total_production'];
                                                $invertDetails->total_consumption = $inverterDetailLog['total_consumption'];
                                                $invertDetails->battery_temperature = $inverterDetailLog['total_consumption'];
                                                $invertDetails->dc_temperature = $inverterDetailLog['DCinverterTemperature'];
                                                $invertDetails->battery_temperature = $inverterDetailLog['BatteryTemperature'];
                                                $invertDetails->output_power_l1 = $inverterOutputPowerL1Data['output_power_l1'];
                                                $invertDetails->output_power_l2 = $inverterOutputPowerL2Data['output_power_l2'];
                                                $invertDetails->output_power_l3 = $inverterOutputPowerL3Data['output_power_l3'];
                                                $invertDetails->load_voltage_l1 = $loadVoltagel1Data['load_voltage_l1'];
                                                $invertDetails->load_voltage_l2 = $loadVoltagel2Data['load_voltage_l2'];
                                                $invertDetails->load_voltage_l3 = $loadVoltagel3Data['load_voltage_l3'];
                                                $invertDetails->load_voltage_ln = $loadVoltagelNData['load_voltage_ln'];
                                                $invertDetails->total_output_power = $totalinverterOutputPowerData['total_output_power'];
                                                $invertDetails->inverter_output_power_ln = $InverterOutputPowerlNData['inverter_output_power_ln'];
                                                $invertDetails->Gene_Input_Load_Enable = $GeneInputLoadEnableData['Gene_Input_Load_Enable'];
                                                $invertDetails->consump_apparent_power = $ConsumpApparentPowerData['Consump_Apparent_Power'];
                                                $invertDetails->load_frequency = $LoadFrequencyData['load_frequency'];

//                                                $invertDetails->battery_power = $inverterDetailLog['battery_power'];
//                                                $invertDetails->battery_capacity = $inverterDetailLog['battery_capacity'];
//                                                $invertDetails->battery_type = $inverterDetailLog['battery_power'] >= 0 ? '+ve' : '-ve';
                                                $invertDetails->collect_time = $inverterDetailLog['collect_time'];
                                                $invertDetails->save();
                                                if (!InverterVersionInformation::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])->exists()) {
                                                    $inverterVersionInformation = new InverterVersionInformation();
                                                    $inverterVersionInformation->plant_id = $plantID;
                                                    $inverterVersionInformation->site_id = $siteID;
                                                    $inverterVersionInformation->dv_inverter = $smartInverter;
                                                    $inverterVersionInformation->general_settings = $inverterDetailLog['general_settings'];
                                                    $inverterVersionInformation->production_compliance = $inverterDetailLog['production_compliance'];
                                                    $inverterVersionInformation->rated_power = $inverterDetailLog['rated_power'];
                                                    $inverterVersionInformation->protocol_version = $inverterDetailLog['protocol_version'];
                                                    $inverterVersionInformation->control_software_version = $inverterDetailLog['control_software_version'];
                                                    $inverterVersionInformation->HMI = $inverterDetailLog['HMI'];
                                                    $inverterVersionInformation->communication_cpu_software = $inverterDetailLog['communication_cpu_software'];
                                                    $inverterVersionInformation->lithium_battery_version = $inverterDetailLog['lithium_battery_version'];
                                                    $inverterVersionInformation->main_1 = $inverterDetailLog['main_1'];
                                                    $inverterVersionInformation->main_2 = $inverterDetailLog['main_2'];
                                                    $inverterVersionInformation->save();
                                                } else {
                                                    $InverterVersionInformationUpdate = InverterVersionInformation::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])->first();
//                                                    $inverterVersionInformation = new InverterVersionInformation();
                                                    $InverterVersionInformationUpdate->plant_id = $plantID;
                                                    $InverterVersionInformationUpdate->site_id = $siteID;
                                                    $InverterVersionInformationUpdate->dv_inverter = $smartInverter;
                                                    $InverterVersionInformationUpdate->general_settings = $inverterDetailLog['general_settings'];
                                                    $InverterVersionInformationUpdate->production_compliance = $inverterDetailLog['production_compliance'];
                                                    $InverterVersionInformationUpdate->rated_power = $inverterDetailLog['rated_power'];
                                                    $InverterVersionInformationUpdate->protocol_version = $inverterDetailLog['protocol_version'];
                                                    $InverterVersionInformationUpdate->control_software_version = $inverterDetailLog['control_software_version'];
                                                    $InverterVersionInformationUpdate->communication_cpu_software = $inverterDetailLog['communication_cpu_software'];
                                                    $InverterVersionInformationUpdate->HMI = $inverterDetailLog['HMI'];
                                                    $InverterVersionInformationUpdate->lithium_battery_version = $inverterDetailLog['lithium_battery_version'];
                                                    $InverterVersionInformationUpdate->main_1 = $inverterDetailLog['main_1'];
                                                    $InverterVersionInformationUpdate->main_2 = $inverterDetailLog['main_2'];
                                                    $InverterVersionInformationUpdate->Update();
                                                }
//                                                $collectTimeDate = date('Y-m-d', ($smartInverterFinalData->collectTime));

//                                                    $inverterDetailResponse = InverterDetail::create($inverterDetailLog);
                                                for ($mi = 1; $mi <= 4; $mi++) {
                                                    $mpptData = InverterMPPTDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter, 'mppt_number' => $mi])->where('collect_time', date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)))->exists();
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
                                            if (empty($batteryStationData) || (date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))) > ($batteryStationData['collect_time'])) {
                                                $inverterBatteryDetail = array();

                                                $inverterBatteryDetail['plant_id'] = $plantID;
                                                $inverterBatteryDetail['site_id'] = $siteID;
                                                $inverterBatteryDetail['dv_inverter'] = $smartInverter;
                                                $keys = array_keys(array_column($responseData, 'key'), 'B_left_cap1');
                                                if ($keys) {
                                                    $inverterBatteryDetail['battery_capacity'] = $responseData[$keys[0]]->value . $responseData[$keys[0]]->unit;
                                                } else {
                                                    $inverterBatteryDetail['battery_capacity'] = 0;
                                                }
                                                $batteryPower = array_keys(array_column($responseData, 'key'), 'B_P1');
                                                if ($batteryPower) {
                                                    $inverterBatteryDetail['battery_power'] = $responseData[$batteryPower[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['battery_power'] = 0;
                                                }
                                                $totalChargeEnergy = array_keys(array_column($responseData, 'key'), 't_cg_n1');
                                                if ($totalChargeEnergy) {
                                                    $inverterBatteryDetail['total_charge_energy'] = $responseData[$totalChargeEnergy[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['total_charge_energy'] = 0;
                                                }
                                                $totalDischargeEnergy = array_keys(array_column($responseData, 'key'), 't_dcg_n1');
                                                if ($totalDischargeEnergy) {
                                                    $inverterBatteryDetail['total_discharge_energy'] = $responseData[$totalDischargeEnergy[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['total_discharge_energy'] = 0;
                                                }
                                                $dailyChargeEnergy = array_keys(array_column($responseData, 'key'), 'Etdy_cg1');
                                                if ($dailyChargeEnergy) {
                                                    $inverterBatteryDetail['daily_charge_energy'] = $responseData[$dailyChargeEnergy[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['daily_charge_energy'] = 0;
                                                }
                                                $dailyDischargeEnergy = array_keys(array_column($responseData, 'key'), 'Etdy_dcg1');
                                                if ($dailyDischargeEnergy) {
                                                    $inverterBatteryDetail['daily_discharge_energy'] = $responseData[$dailyDischargeEnergy[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['daily_discharge_energy'] = 0;
                                                }
                                                $batteryVoltage = array_keys(array_column($responseData, 'key'), 'B_V1');
                                                if ($batteryVoltage) {
                                                    $inverterBatteryDetail['battery_voltage'] = $responseData[$batteryVoltage[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['battery_voltage'] = 0;
                                                }
                                                $inverterRealTimeConsumption = array_keys(array_column($responseData, 'key'), 'E_Puse_t1');
                                                if ($inverterRealTimeConsumption) {
                                                    $inverterBatteryDetail['inverter_consumption'] = $responseData[$inverterRealTimeConsumption[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['inverter_consumption'] = 0;
                                                }
                                                $inverterRatedPower = array_keys(array_column($responseData, 'key'), 'Pr1');
                                                if ($inverterRatedPower) {
                                                    $inverterBatteryDetail['rated_power'] = $responseData[$inverterRatedPower[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['rated_power'] = 0;
                                                }
                                                $batteryTemperature = array_keys(array_column($responseData, 'key'), 'B_T1');
                                                if ($batteryTemperature) {
                                                    $inverterBatteryDetail['battery_temperature'] = $responseData[$batteryTemperature[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['battery_temperature'] = 0;
                                                }
                                                $batteryStatus = array_keys(array_column($responseData, 'key'), 'B_ST1');
                                                if ($batteryStatus) {
                                                    $inverterBatteryDetail['battery_status'] = $responseData[$batteryStatus[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['battery_status'] = 0;
                                                }
                                                $batteryCurrentData = array_keys(array_column($responseData, 'key'), 'B_C1');
                                                if ($batteryCurrentData) {
                                                    $inverterBatteryDetail['battery_current'] = $responseData[$batteryCurrentData[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['battery_current'] = 0;
                                                }
//                                                $batteryPowerData = array_keys(array_column($responseData, 'key'), 'B_P1');
//                                                if ($batteryPowerData) {
//                                                    $inverterBatteryDetail['battery_power'] = $responseData[$batteryPowerData[0]]->value;
//                                                } else {
//                                                    $inverterBatteryDetail['battery_power'] = 0;
//                                                }
                                                if ($plant->grid_type == 'Three-phase') {
                                                    $batteryTypeData = array_keys(array_column($responseData, 'key'), 'BCT');
                                                    if ($batteryTypeData) {
                                                        $inverterBatteryDetail['battery_type_data'] = $responseData[$batteryTypeData[0]]->value;
                                                    } else {
                                                        $inverterBatteryDetail['battery_type_data'] = 0;
                                                    }
                                                } else {
                                                    $batteryTypeData = array_keys(array_column($responseData, 'key'), 'B_TYP1');
                                                    if ($batteryTypeData) {
                                                        $inverterBatteryDetail['battery_type_data'] = $responseData[$batteryTypeData[0]]->value;
                                                    } else {
                                                        $inverterBatteryDetail['battery_type_data'] = 0;
                                                    }
                                                }

                                                $batteryChargingVoltage = array_keys(array_column($responseData, 'key'), 'BCV');
                                                if ($batteryChargingVoltage) {
                                                    $inverterBatteryDetail['battery_charging_voltage'] = $responseData[$batteryChargingVoltage[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['battery_charging_voltage'] = 0;
                                                }
                                                $batteryBMSCurrent = array_keys(array_column($responseData, 'key'), 'BMS_B_C1');
                                                if ($batteryBMSCurrent) {
                                                    $inverterBatteryDetail['battery_bms_current'] = $responseData[$batteryBMSCurrent[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['battery_bms_current'] = 0;
                                                }
                                                if($plant->grid_type == 'Three-phase'){
                                                    $batteryBMSCurrent = array_keys(array_column($responseData, 'key'), 'BMS_C_V');
                                                    if ($batteryBMSCurrent) {
                                                        $inverterBatteryDetail['battery_bms_voltage'] = $responseData[$batteryBMSCurrent[0]]->value;
                                                    } else {
                                                        $inverterBatteryDetail['battery_bms_voltage'] = 0;
                                                    }
                                                }else{
                                                    $batteryBMSCurrent = array_keys(array_column($responseData, 'key'), 'BMS_B_V1');
                                                    if ($batteryBMSCurrent) {
                                                        $inverterBatteryDetail['battery_bms_voltage'] = $responseData[$batteryBMSCurrent[0]]->value;
                                                    } else {
                                                        $inverterBatteryDetail['battery_bms_voltage'] = 0;
                                                    }
                                                }
                                                $batteryBMSDischargeVoltage = array_keys(array_column($responseData, 'key'), 'BMS_D_V');
                                                if ($batteryBMSDischargeVoltage) {
                                                    $inverterBatteryDetail['battery_bms_charge_voltage'] = $responseData[$batteryBMSDischargeVoltage[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['battery_bms_charge_voltage'] = 0;
                                                }
                                                if($plant->grid_type == 'Three-phase'){
                                                    $batteryBMSCurrentLimitingCharging = array_keys(array_column($responseData, 'key'), 'BMS_C_C_L');
                                                    if ($batteryBMSCurrentLimitingCharging) {
                                                        $inverterBatteryDetail['battery_bms_current_limiting_charging'] = $responseData[$batteryBMSCurrentLimitingCharging[0]]->value;
                                                    } else {
                                                        $inverterBatteryDetail['battery_bms_current_limiting_charging'] = 0;
                                                    }
                                                }else{
                                                    $batteryBMSCurrentLimitingCharging = array_keys(array_column($responseData, 'key'), 'BMS_B_Ccg_thd1');
                                                    if ($batteryBMSCurrentLimitingCharging) {
                                                        $inverterBatteryDetail['battery_bms_current_limiting_charging'] = $responseData[$batteryBMSCurrentLimitingCharging[0]]->value;
                                                    } else {
                                                        $inverterBatteryDetail['battery_bms_current_limiting_charging'] = 0;
                                                    }
                                                }

                                                $batteryBMSTemperature = array_keys(array_column($responseData, 'key'), 'BMST');
                                                if ($batteryBMSTemperature) {
                                                    $inverterBatteryDetail['battery_bms_temperature'] = $responseData[$batteryBMSTemperature[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['battery_bms_temperature'] = 0;
                                                }
                                                $batteryBMSSoc = array_keys(array_column($responseData, 'key'), 'BMS_SOC');
                                                if ($batteryBMSSoc) {
                                                    $inverterBatteryDetail['battery_bms_soc'] = $responseData[$batteryBMSSoc[0]]->value;
                                                } else {
                                                    $inverterBatteryDetail['battery_bms_soc'] = 0;
                                                }
                                                if($plant->grid_type == 'Three-phase'){
                                                    $batteryBMSCurrentLimitingDischarging = array_keys(array_column($responseData, 'key'), 'BMS_D_C_L');
                                                    if ($batteryBMSCurrentLimitingDischarging) {
                                                        $inverterBatteryDetail['battery_bms_current_limiting_discharging'] = $responseData[$batteryBMSCurrentLimitingDischarging[0]]->value;
                                                    } else {
                                                        $inverterBatteryDetail['battery_bms_current_limiting_discharging'] = 0;
                                                    }
                                                }else{
                                                    $batteryBMSCurrentLimitingDischarging = array_keys(array_column($responseData, 'key'), 'BMS_B_Cdcg_thd1');
                                                    if ($batteryBMSCurrentLimitingDischarging) {
                                                        $inverterBatteryDetail['battery_bms_current_limiting_discharging'] = $responseData[$batteryBMSCurrentLimitingDischarging[0]]->value;
                                                    } else {
                                                        $inverterBatteryDetail['battery_bms_current_limiting_discharging'] = 0;
                                                    }
                                                }

//                                                return [$inverterBatteryDetail['battery_temperature'],$inverterBatteryDetail['battery_status']];

                                                $inverterBatteryDetail['collect_time'] = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
                                                $stationBattery = new StationBattery();
                                                $stationBattery->plant_id = $plantID;
                                                $stationBattery->site_id = $siteID;
                                                $stationBattery->dv_inverter = $smartInverter;
                                                $stationBattery->battery_capacity = $inverterBatteryDetail['battery_capacity'];
                                                $stationBattery->battery_power = $inverterBatteryDetail['battery_power'];
                                                $stationBattery->battery_type = $inverterBatteryDetail['battery_power'] >= 0 ? '+ve' : '-ve';
                                                $stationBattery->total_charge_energy = $inverterBatteryDetail['total_charge_energy'];
                                                $stationBattery->total_discharge_energy = $inverterBatteryDetail['total_discharge_energy'];
                                                $stationBattery->daily_charge_energy = $inverterBatteryDetail['daily_charge_energy'];
                                                $stationBattery->daily_discharge_energy = $inverterBatteryDetail['daily_discharge_energy'];
                                                $stationBattery->inverter_real_time_consumption = $inverterBatteryDetail['inverter_consumption'];
                                                $stationBattery->rated_power = $inverterBatteryDetail['rated_power'];
                                                $stationBattery->collect_time = $inverterBatteryDetail['collect_time'];
                                                $stationBattery->battery_temperature = $inverterBatteryDetail['battery_temperature'];
                                                $stationBattery->battery_status = $inverterBatteryDetail['battery_status'];
                                                $stationBattery->battery_current = $inverterBatteryDetail['battery_current'];
                                                $stationBattery->battery_type_data = $inverterBatteryDetail['battery_type_data'];
                                                $stationBattery->battery_charging_voltage = $inverterBatteryDetail['battery_charging_voltage'];
                                                $stationBattery->battery_bms_current = $inverterBatteryDetail['battery_bms_current'];
                                                $stationBattery->battery_bms_current_limiting_charging = $inverterBatteryDetail['battery_bms_current_limiting_charging'];
                                                $stationBattery->battery_bms_temperature = $inverterBatteryDetail['battery_bms_temperature'];
                                                $stationBattery->battery_bms_current_limiting_discharging = $inverterBatteryDetail['battery_bms_current_limiting_discharging'];
                                                $stationBattery->battery_voltage = $inverterBatteryDetail['battery_voltage'];
                                                $stationBattery->battery_bms_voltage = $inverterBatteryDetail['battery_bms_voltage'];
                                                $stationBattery->bms_discharge_voltage = $inverterBatteryDetail['battery_bms_charge_voltage'];
                                                $stationBattery->battery_bms_soc = $inverterBatteryDetail['battery_bms_soc'];
//                                                return $stationBattery;
                                                $result = $stationBattery->save();
//                                                return $result;

                                            }

                                            if ($plantHasGridMeter == 'Y') {

                                                $todayLastTime = InverterEnergyLog::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])->whereDate('collect_time', $collectTime)->orderBy('collect_time', 'desc')->first();
                                                if (empty($todayLastTime) || date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)) > ($todayLastTime['collect_time'])) {

                                                    $totalGridPowerData = array_keys(array_column($responseData, 'key'), 'PG_Pt1');
                                                    if ($totalGridPowerData) {
                                                        $gridPower = $responseData[$totalGridPowerData[0]]->value;
                                                    } else {
                                                        $gridPower = 0;
                                                    }
//                                                    return $gridPower;
//                                                    return $plant->meter_type;
                                                    if ($plant->grid_type == "Three-phase") {
                                                        $totalGridDailyEnergyData = array_keys(array_column($responseData, 'key'), 'E_B_D');
                                                        if ($totalGridDailyEnergyData) {
                                                            $gridImportEnergy = $responseData[$totalGridDailyEnergyData[0]]->value;
                                                        } else {
                                                            $gridImportEnergy = 0;
                                                        }
                                                    } else {
                                                        $totalGridDailyEnergyData = array_keys(array_column($responseData, 'key'), 'Etdy_pu1');
                                                        if ($totalGridDailyEnergyData) {
                                                            $gridImportEnergy = $responseData[$totalGridDailyEnergyData[0]]->value;
                                                        } else {
                                                            $gridImportEnergy = 0;
                                                        }
                                                    }
//                                                    $totalGridDailyEnergyData = array_keys(array_column($responseData, 'key'), 'Etdy_pu1');
//                                                    if ($totalGridDailyEnergyData) {
//                                                        $gridImportEnergy = $responseData[$totalGridDailyEnergyData[0]]->value;
//                                                    } else {
//                                                        $gridImportEnergy = 0;
//                                                    }
                                                    if ($plant->grid_type == "Three-phase") {
                                                        $totalGridDailyFeedData = array_keys(array_column($responseData, 'key'), 'E_S_D');
                                                        if ($totalGridDailyFeedData) {
                                                            $gridExportEnergy = $responseData[$totalGridDailyFeedData[0]]->value;
                                                        } else {
                                                            $gridExportEnergy = 0;
                                                        }
                                                    } else {
                                                        $totalGridDailyFeedData = array_keys(array_column($responseData, 'key'), 't_gc_tdy1');
                                                        if ($totalGridDailyFeedData) {
                                                            $gridExportEnergy = $responseData[$totalGridDailyFeedData[0]]->value;
                                                        } else {
                                                            $gridExportEnergy = 0;
                                                        }
                                                    }
//                                                    $totalGridDailyFeedData = array_keys(array_column($responseData, 'key'), 't_gc_tdy1');
//                                                    if ($totalGridDailyFeedData) {
//                                                        $gridExportEnergy = $responseData[$totalGridDailyFeedData[0]]->value;
//                                                    } else {
//                                                        $gridExportEnergy = 0;
//                                                    }
//                                                    if ($plant->grid_type == "Three-phase") {
//                                                        $gridType = array_keys(array_column($responseData, 'key'), 'INV_MOD1');
//                                                        if ($gridType) {
//                                                            $gridTypeData = $responseData[$gridType[0]]->value;
//                                                        } else {
//                                                            $gridTypeData = 0;
//                                                        }
//                                                    } else {
//                                                        $gridType = array_keys(array_column($responseData, 'key'), 'GT');
//                                                        if ($gridType) {
//                                                            $gridTypeData = $responseData[$gridType[0]]->value;
//                                                        } else {
//                                                            $gridTypeData = 0;
//                                                        }
//                                                    }
                                                    $gridType = array_keys(array_column($responseData, 'key'), 'INV_MOD1');
                                                    if ($gridType) {
                                                        $gridTypeData = $responseData[$gridType[0]]->value;
                                                    } else {
                                                        $gridTypeData = 0;
                                                    }
                                                    $safety = array_keys(array_column($responseData, 'key'), 'SAFETY');
                                                    if ($safety) {
                                                        $safetyData = $responseData[$safety[0]]->value;
                                                    } else {
                                                        $safetyData = 0;
                                                    }

                                                    $gridVoltage = array_keys(array_column($responseData, 'key'), 'PG_V1');
                                                    if ($gridVoltage) {
                                                        $gridVoltageData = $responseData[$gridVoltage[0]]->value;
                                                    } else {
                                                        $gridVoltageData = 0;
                                                    }
                                                    $gridStatus = array_keys(array_column($responseData, 'key'), 'ST_PG1');
                                                    if ($gridStatus) {
                                                        $gridStatusData = $responseData[$gridStatus[0]]->value;
                                                    } else {
                                                        $gridStatusData = 0;
                                                    }
                                                    $totalCurrent = array_keys(array_column($responseData, 'key'), 'PG_C1');
                                                    if ($totalCurrent) {
                                                        $gridCurrentData = $responseData[$totalCurrent[0]]->value;
                                                    } else {
                                                        $gridCurrentData = 0;
                                                    }
                                                    $totalGridVoltage = array_keys(array_column($responseData, 'key'), 'PG_V4');
                                                    if ($totalGridVoltage) {
                                                        $totalGridVoltageData = $responseData[$totalGridVoltage[0]]->value;
                                                    } else {
                                                        $totalGridVoltageData = 0;
                                                    }
                                                    $gridFrequency = array_keys(array_column($responseData, 'key'), 'PG_F1');
                                                    if ($gridFrequency) {
                                                        $gridFrequencyData = $responseData[$gridFrequency[0]]->value;
                                                    } else {
                                                        $gridFrequencyData = 0;
                                                    }
                                                    $gridVoltageL1 = array_keys(array_column($responseData, 'key'), 'G_V_L1');
                                                    if ($gridVoltageL1) {
                                                        $gridVoltageL1Data = $responseData[$gridVoltageL1[0]]->value;
                                                    } else {
                                                        $gridVoltageL1Data = 0;
                                                    }
                                                    $gridVoltageL2 = array_keys(array_column($responseData, 'key'), 'G_V_L2');
                                                    if ($gridVoltageL2) {
                                                        $gridVoltageL2Data = $responseData[$gridVoltageL2[0]]->value;
                                                    } else {
                                                        $gridVoltageL2Data = 0;
                                                    }
                                                    $gridVoltageL3 = array_keys(array_column($responseData, 'key'), 'G_V_L3');
                                                    if ($gridVoltageL3) {
                                                        $gridVoltageL3Data = $responseData[$gridVoltageL3[0]]->value;
                                                    } else {
                                                        $gridVoltageL3Data = 0;
                                                    }
                                                    $gridCurrentL1 = array_keys(array_column($responseData, 'key'), 'G_C_L1');
                                                    if ($gridCurrentL1) {
                                                        $gridCurrentL1Data = $responseData[$gridCurrentL1[0]]->value;
                                                    } else {
                                                        $gridCurrentL1Data = 0;
                                                    }
                                                    $gridCurrentL2 = array_keys(array_column($responseData, 'key'), 'G_C_L2');
                                                    if ($gridCurrentL2) {
                                                        $gridCurrentL2Data = $responseData[$gridCurrentL2[0]]->value;
                                                    } else {
                                                        $gridCurrentL2Data = 0;
                                                    }
                                                    $gridCurrentL3 = array_keys(array_column($responseData, 'key'), 'G_C_L3');
                                                    if ($gridCurrentL3) {
                                                        $gridCurrentL3Data = $responseData[$gridCurrentL3[0]]->value;
                                                    } else {
                                                        $gridCurrentL3Data = 0;
                                                    }
                                                    $gridPowerLD1 = array_keys(array_column($responseData, 'key'), 'G_P_L1');
                                                    if ($gridPowerLD1) {
                                                        $gridPowerLD1Data = $responseData[$gridPowerLD1[0]]->value;
                                                    }else{
                                                        $gridPowerLD1Data = 0;
                                                    }
                                                    $gridPowerLD2 = array_keys(array_column($responseData, 'key'), 'G_P_L2');
                                                    if ($gridPowerLD2) {
                                                        $gridPowerLD2Data = $responseData[$gridPowerLD2[0]]->value;
                                                    } else {
                                                        $gridPowerLD2Data = 0;
                                                    }
                                                    $gridPowerLD3 = array_keys(array_column($responseData, 'key'), 'G_P_L3');
                                                    if ($gridPowerLD3) {
                                                        $gridPowerLD3Data = $responseData[$gridPowerLD3[0]]->value;
                                                    } else {
                                                        $gridPowerLD3Data = 0;
                                                    }
                                                    $gridExternalct1 = array_keys(array_column($responseData, 'key'), 'CT1_P_E');
                                                    if ($gridExternalct1) {
                                                        $gridExternalct1Data = $responseData[$gridExternalct1[0]]->value;
                                                    }else{
                                                        $gridExternalct1Data = 0;
                                                    }
                                                    $gridExternalct2 = array_keys(array_column($responseData, 'key'), 'CT2_P_E');
                                                    if ($gridExternalct2) {
                                                        $gridExternalct2Data = $responseData[$gridExternalct2[0]]->value;
                                                    } else {
                                                        $gridExternalct2Data = 0;
                                                    }
                                                    $gridExternalct3 = array_keys(array_column($responseData, 'key'), 'CT3_P_E');
                                                    if ($gridExternalct3) {
                                                        $gridExternalct3Data = $responseData[$gridExternalct3[0]]->value;
                                                    } else {
                                                        $gridExternalct3Data = 0;
                                                    }
                                                    $totalCtPower = array_keys(array_column($responseData, 'key'), 'CT_T_E');
                                                    if ($totalCtPower) {
                                                        $totalCtPowerData = $responseData[$totalCtPower[0]]->value;
                                                    } else {
                                                        $totalCtPowerData = 0;
                                                    }
                                                    $totalGridPower1 = array_keys(array_column($responseData, 'key'), 'PG_Pt1');
                                                    if ($totalGridPower1) {
                                                        $totalGridPowerDetail = $responseData[$totalGridPower1[0]]->value;
                                                    } else {
                                                        $totalGridPowerDetail = 0;
                                                    }
                                                    $phaseGridPowerUL1 = array_keys(array_column($responseData, 'key'), 'G_P_U');

                                                    if ($phaseGridPowerUL1) {
                                                        $phaseGridPowerUL1Data = $responseData[$phaseGridPowerUL1[0]]->value;
                                                    } else {
                                                        $phaseGridPowerUL1Data = 0;
                                                    }
                                                    if ($plant->grid_type == 'Three-phase') {
                                                        $totalGridFeedIn = array_keys(array_column($responseData, 'key'), 'E_S_TO');
                                                        if ($totalGridFeedIn) {
                                                            $totalGridFeedInData = $responseData[$totalGridFeedIn[0]]->value;
                                                        } else {
                                                            $totalGridFeedInData = 0;
                                                        }
                                                    } else {
                                                        $totalGridFeedIn = array_keys(array_column($responseData, 'key'), 't_gc1');
                                                        if ($totalGridFeedIn) {
                                                            $totalGridFeedInData = $responseData[$totalGridFeedIn[0]]->value;
                                                        } else {
                                                            $totalGridFeedInData = 0;
                                                        }
                                                    }

                                                    $meterTotalActivePower = array_keys(array_column($responseData, 'key'), 'METER_Pt1');
                                                    if ($meterTotalActivePower) {
                                                        $meterTotalActivePowerData = $responseData[$meterTotalActivePower[0]]->value;
                                                    } else {
                                                        $meterTotalActivePowerData = 0;
                                                    }
                                                    if ($plant->grid_type == 'Three-phase') {
                                                        $totalEnergyPurchased = array_keys(array_column($responseData, 'key'), 'E_B_TO');
                                                        if ($totalEnergyPurchased) {
                                                            $totalEnergyPurchasedData = $responseData[$totalEnergyPurchased[0]]->value;
                                                        } else {
                                                            $totalEnergyPurchasedData = 0;
                                                        }
                                                    } else {
                                                        $totalEnergyPurchased = array_keys(array_column($responseData, 'key'), 'Et_pu1');
                                                        if ($totalEnergyPurchased) {
                                                            $totalEnergyPurchasedData = $responseData[$totalEnergyPurchased[0]]->value;
                                                        } else {
                                                            $totalEnergyPurchasedData = 0;
                                                        }
                                                    }

                                                    $meterActivePower = array_keys(array_column($responseData, 'key'), 'P_METER2');
                                                    if ($meterActivePower) {
                                                        $meterActivePowerData = $responseData[$meterActivePower[0]]->value;
                                                    } else {
                                                        $meterActivePowerData = 0;
                                                    }
                                                    $meterAcCurrent = array_keys(array_column($responseData, 'key'), 'AC_METER1');
                                                    if ($meterAcCurrent) {
                                                        $meterAcCurrentData = $responseData[$meterAcCurrent[0]]->value;
                                                    } else {
                                                        $meterAcCurrentData = 0;
                                                    }
                                                    if ($gridPower) {
                                                        $gridPower = (($gridPower / 1000));
                                                    } else {
                                                        $gridPower = 0;
                                                    }
                                                    $GridVoltageLN = array_keys(array_column($responseData, 'key'), 'G_V_LN');
                                                    if ($GridVoltageLN) {
                                                        $GridVoltageLNData = $responseData[$GridVoltageLN[0]]->value;
                                                    } else {
                                                        $GridVoltageLNData = 0;
                                                    }
                                                    $GridCurrentLN = array_keys(array_column($responseData, 'key'), 'G_C_LN');
                                                    if ($GridCurrentLN) {
                                                        $GridCurrentLNData = $responseData[$GridCurrentLN[0]]->value;
                                                    } else {
                                                        $GridCurrentLNData = 0;
                                                    }
                                                    $ExternalCTCurrentLN = array_keys(array_column($responseData, 'key'), 'E_CT_C');
                                                    if ($ExternalCTCurrentLN) {
                                                        $ExternalCTCurrentLNData = $responseData[$ExternalCTCurrentLN[0]]->value;
                                                    } else {
                                                        $ExternalCTCurrentLNData = 0;
                                                    }
                                                    $ExternalCTPowerLN = array_keys(array_column($responseData, 'key'), 'E_CT_P');
                                                    if ($ExternalCTPowerLN) {
                                                        $ExternalCTPowerLNData = $responseData[$ExternalCTPowerLN[0]]->value;
                                                    } else {
                                                        $ExternalCTPowerLNData = 0;
                                                    }

                                                    $inverterEnergyLog = new InverterEnergyLog();
                                                    $inverterEnergyLog['plant_id'] = $plantID;
                                                    $inverterEnergyLog['site_id'] = $siteID;
                                                    $inverterEnergyLog['dv_inverter'] = $smartInverter;
                                                    $inverterEnergyLog['grid_power'] = $gridPower;
                                                    $inverterEnergyLog['import_energy'] = $gridImportEnergy;
                                                    $inverterEnergyLog['export_energy'] = $gridExportEnergy;
                                                    $inverterEnergyLog['cron_job_id'] = $processedMaxCronJobID;
                                                    $inverterEnergyLog['grid_type'] = $gridTypeData;
                                                    $inverterEnergyLog['total_grid_feed_in'] = $totalGridFeedInData;
                                                    $inverterEnergyLog['grid_voltage_r_u_a'] = $gridVoltageData;
                                                    $inverterEnergyLog['grid_current_r_u_a'] = $gridCurrentData;
                                                    $inverterEnergyLog['phase_grid_power'] = $phaseGridPowerUL1Data;
                                                    $inverterEnergyLog['total_grid_voltage'] = $totalGridVoltageData;
                                                    $inverterEnergyLog['grid_frequency'] = $gridFrequencyData;
                                                    $inverterEnergyLog['total_grid_power'] = $totalGridPowerDetail;
                                                    $inverterEnergyLog['meter_total_active_power'] = $meterTotalActivePowerData;
                                                    $inverterEnergyLog['total_energy_purchased'] = $totalEnergyPurchasedData;
                                                    $inverterEnergyLog['meter_active_power'] = $meterActivePowerData;
                                                    $inverterEnergyLog['meter_ac_current'] = $meterAcCurrentData;
                                                    $inverterEnergyLog['grid_status'] = $gridStatusData;
                                                    $inverterEnergyLog['grid_voltage_l1'] = $gridVoltageL1Data;
                                                    $inverterEnergyLog['grid_voltage_l2'] = $gridVoltageL2Data;
                                                    $inverterEnergyLog['grid_voltage_l3'] = $gridVoltageL3Data;
                                                    $inverterEnergyLog['grid_current_l1'] = $gridCurrentL1Data;
                                                    $inverterEnergyLog['grid_current_l2'] = $gridCurrentL2Data;
                                                    $inverterEnergyLog['grid_current_l3'] = $gridCurrentL3Data;
                                                    $inverterEnergyLog['grid_power_ld1'] = $gridPowerLD1Data;
                                                    $inverterEnergyLog['grid_power_ld2'] = $gridPowerLD2Data;
                                                    $inverterEnergyLog['grid_power_ld3'] = $gridPowerLD3Data;
                                                    $inverterEnergyLog['grid_external_ct1'] = $gridExternalct1Data;
                                                    $inverterEnergyLog['grid_external_ct2'] = $gridExternalct2Data;
                                                    $inverterEnergyLog['grid_external_ct3'] = $gridExternalct3Data;
                                                    $inverterEnergyLog['total_Ct_power'] = $totalCtPowerData;
                                                    $inverterEnergyLog['safety_type'] = $safetyData;
                                                    $inverterEnergyLog['grid_voltage_ln'] = $GridVoltageLNData;
                                                    $inverterEnergyLog['grid_current_ln'] = $GridCurrentLNData;
                                                    $inverterEnergyLog['external_ct_current_ln'] = $ExternalCTCurrentLNData;
                                                    $inverterEnergyLog['external_ct_power_ln'] = $ExternalCTPowerLNData;
//                                                    $inverterEnergyLog['safety_type'] = $safetyData;
//                                                    return $inverterEnergyLog;
                                                    $collectTime = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
                                                    $inverterEnergyLog['collect_time'] = $collectTime;

                                                    $inverterEnergyLog->save();
//                                                    $inverterEnergyLogResponse = InverterEnergyLog::create($inverterEnergyLog);
//                                                    return $inverterEnergyLog;
                                                }
                                            }
//                                                else {
//                                                    $inverterEnergyLog['plant_id'] = $plantID;
//                                                    $inverterEnergyLog['site_id'] = $siteID;
//                                                    $inverterEnergyLog['dv_inverter'] = $smartInverter;
//                                                    $inverterEnergyLog['grid_power'] = $solisGridDetails->generationPower;
//                                                    $inverterEnergyLog['import_energy'] = $usePower[0];
//                                                    $inverterEnergyLog['export_energy'] = 0;
//                                                    $inverterEnergyLog['cron_job_id'] = $processedMaxCronJobID;
//                                                    $inverterEnergyLog['collect_time'] = $collectTime;
//
//                                                    $inverterEnergyLogResponse = InverterEnergyLog::create($inverterEnergyLog);
//                                                }
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


                            $dailyGenerationData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->exists() ? InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;
                            $dailyConsumptionData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->exists() ? InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->first()->daily_consumption : 0;
//                            return [$dailyGenerationData, $dailyConsumptionData];
                            //                            $batteryInverterData = StationBattery::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->first();
//                            $inverterEnergyLogData = InverterEnergyLog::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->first();

//                            $dailyInverterData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->get();
//                            $dailyBatteryInverterData = StationBattery::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->get();
//                            $inverterEnergyLogData = InverterEnergyLog::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->get();
//                            $dailyInverterData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->latest()->first();
                            $dailyBatteryInverterData = StationBattery::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->first();
                            $inverterEnergyLogData = InverterEnergyLog::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->first();

//                            $dataArray = json_decode(json_encode($dailyInverterData), true);
//                            $dailyGenerationData = array_sum(array_column($dataArray, 'daily_generation'));
//                            $dailyConsumptionData = array_sum(array_column($dataArray, 'daily_consumption'));
//                            $batteryDataArray = json_decode(json_encode($dailyBatteryInverterData), true);
//                            $dailyDischargeEnergy = array_sum(array_column($batteryDataArray, 'daily_discharge_energy'));
//                            $dailyChargeEnergy = array_sum(array_column($batteryDataArray, 'daily_charge_energy'));
                            $dailyDischargeEnergy = 0;
                            $dailyChargeEnergy = 0;
                            if ($dailyBatteryInverterData) {
                                $dailyDischargeEnergy = $dailyBatteryInverterData->daily_discharge_energy;
                                $dailyChargeEnergy = $dailyBatteryInverterData->daily_charge_energy;
                            }

//                            $energyDataArray = json_decode(json_encode($inverterEnergyLogData), true);
//                            $dailyEnergyPurchased = array_sum(array_column($energyDataArray, 'import_energy'));
//                            $dailyGridFeedIn = array_sum(array_column($energyDataArray, 'export_energy'));
//                            if ($batteryInverterData) {
//                                $dailyDischargeEnergy = $batteryInverterData->daily_discharge_energy;
//                                $dailyChargeEnergy = $batteryInverterData->daily_charge_energy;
//                            } else {
//                                $dailyDischargeEnergy = 0;
//                                $dailyChargeEnergy = 0;
//                            }
//                            $dailyConsumptionData = 0;
//                            $dailyGenerationData = 0;
//                            if($dailyInverterData)
//                            {
//                                $dailyConsumptionData = $dailyInverterData->daily_generation;
//                                $dailyGenerationData = $dailyInverterData->daily_consumption;
//                            }
                            if ($inverterEnergyLogData) {
                                $dailyEnergyPurchased = $inverterEnergyLogData->import_energy;
                                $dailyGridFeedIn = $inverterEnergyLogData->export_energy;
                            } else {
                                $dailyEnergyPurchased = 0;
                                $dailyGridFeedIn = 0;
                            }
//                            return [$dailyChargeEnergy,$dailyDischargeEnergy];
                            $dailyInvData['plant_id'] = $plantID;
                            $dailyInvData['siteId'] = $siteID;
                            $dailyInvData['dv_inverter'] = $smartInverter;
                            $dailyInvData['updated_at'] = $currentTime;
                            $dailyInvData['daily_generation'] = $dailyGenerationData;
                            $dailyInvData['daily_consumption'] = $dailyConsumptionData;
                            $dailyInvData['daily_charge_energy'] = $dailyChargeEnergy;
                            $dailyInvData['daily_energy_purchased'] = $dailyEnergyPurchased;
                            $dailyInvData['daily_grid_feed_in'] = $dailyGridFeedIn;
                            $dailyInvData['daily_discharge_energy'] = $dailyDischargeEnergy;
//                            return $dailyInvData;

                            //$dailyGenerationData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->exists() ? InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;

                            $DailyInvDataExist = DailyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('created_at', $lastRecordDate)->first();

                            if ($DailyInvDataExist) {

                                $dailyInvDataResponse = $DailyInvDataExist->fill($dailyInvData)->save();
                            } else {

                                $dailyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($this->cronJobCollectTime));
                                $dailyInvDataResponse = DailyInverterDetail::create($dailyInvData);
                            }

                            break;
                        }

                        $logYear = date('Y', strtotime($lastRecordDate));
                        $logMonth = date('m', strtotime($lastRecordDate));

                        //MONTHLY INVERTER DATA
                        $solisMonthlyYearlyController = new SolisMonthlyYearlyController();
                        $solisMonthlyDataResult = $solisMonthlyYearlyController->SolisInverterMonthlyData($solisAPIBaseURL, $token, $smartInverter, $lastRecordDate);
                        $solisMonthlyResponseData = json_decode($solisMonthlyDataResult);
                        $monthlyGeneration = 0;
                        $gridFeedIn = 0;
                        $monthlyEnergyPurchased = 0;
                        $monthlyConsumption = 0;
                        $monthlyCharge = 0;
                        $monthlyDischarge = 0;
                        if ($solisMonthlyResponseData && isset($solisMonthlyResponseData->paramDataList)) {
//                            return ($solisMonthlyResponseData->paramDataList[0]->dataList[0]);
                            $dataArray = json_decode(json_encode($solisMonthlyResponseData->paramDataList[0]->dataList), true);
                            for ($k = 0; $k < count($dataArray); $k++) {
                                if ($dataArray[$k]['key'] == 'generation') {
                                    $monthlyGeneration = $dataArray[$k]['value'];
                                } elseif ($dataArray[$k]['key'] == 'grid') {
                                    $gridFeedIn = $dataArray[$k]['value'];
                                } elseif ($dataArray[$k]['key'] == 'consumption') {
                                    $monthlyConsumption = $dataArray[$k]['value'];
                                } elseif ($dataArray[$k]['key'] == 'purchase') {
                                    $monthlyEnergyPurchased = $dataArray[$k]['value'];
                                } elseif ($dataArray[$k]['key'] == 'charge') {
                                    $monthlyCharge = $dataArray[$k]['value'];
                                } elseif ($dataArray[$k]['key'] == 'discharge') {
                                    $monthlyDischarge = $dataArray[$k]['value'];
                                }
                            }
                        }
//                        return [$monthlyGeneration, $gridFeedIn, $monthlyConsumption, $monthlyEnergyPurchased];
                        $monthlyInvData = array();

                        $monthlyInvData['plant_id'] = $plantID;
                        $monthlyInvData['siteId'] = $siteID;
                        $monthlyInvData['dv_inverter'] = $smartInverter;
                        $monthlyInvData['updated_at'] = $currentTime;

//                        $dailyInverterData = DailyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('daily_generation');
//                        $monthlyChargeEnergy = StationBattery::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('daily_charge_energy');
//                        $monthlyDischargeEnergy = StationBattery::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('daily_discharge_energy');

                        $monthlyInvData['monthly_generation'] = $monthlyGeneration;
                        $monthlyInvData['monthly_energy_purchased'] = $monthlyEnergyPurchased;
                        $monthlyInvData['monthly_grid_feed_in'] = $gridFeedIn;
                        $monthlyInvData['monthly_consumption_energy'] = $monthlyConsumption;
                        $monthlyInvData['monthly_charge_energy'] = $monthlyCharge;
                        $monthlyInvData['monthly_discharge_energy'] = $monthlyDischarge;

                        $monthlyInvDataExist = MonthlyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->first();

                        if ($monthlyInvDataExist) {

                            $monthlyInvDataResponse = $monthlyInvDataExist->fill((array)$monthlyInvData)->save();
                        } else {

                            $monthlyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($this->cronJobCollectTime));
                            $monthlyInvDataResponse = MonthlyInverterDetail::create((array)$monthlyInvData);
                        }

                        //YEARLY INVERTER DATA
                        $yearlyInvData = array();

                        $yearlyInvData['plant_id'] = $plantID;
                        $yearlyInvData['siteId'] = $siteID;
                        $yearlyInvData['dv_inverter'] = $smartInverter;
                        $yearlyInvData['updated_at'] = $currentTime;

                        $monthlyInverterData = MonthlyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->sum('monthly_generation');
                        $yearlyChargeEnergy = MonthlyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->sum('monthly_charge_energy');
                        $yearlyDischargeEnergy = MonthlyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->sum('monthly_discharge_energy');

                        $yearlyInvData['yearly_generation'] = isset($monthlyInverterData) ? $monthlyInverterData : 0;
                        $yearlyInvData['yearly_charge_energy'] = isset($yearlyChargeEnergy) ? $yearlyChargeEnergy : 0;
                        $yearlyInvData['yearly_discharge_energy'] = isset($yearlyDischargeEnergy) ? $yearlyDischargeEnergy : 0;

                        $yearlyInvDataExist = YearlyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->first();

                        if ($yearlyInvDataExist) {

                            $yearlyInvDataResponse = $yearlyInvDataExist->fill((array)$yearlyInvData)->save();
                        } else {

                            $yearlyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($this->cronJobCollectTime));
                            $yearlyInvDataResponse = YearlyInverterDetail::create((array)$yearlyInvData);
                        }
                    }

                    //SMART INVERTER GENERATION LOG DATA


//                    return $siteSmartInverterLogStartTime;
                    if (!(empty($siteSmartInverterLogStartTime))) {

                        $minTimeSmartInverter = date('Y-m-d', min($siteSmartInverterLogStartTime));
//                        return $minTimeSmartInverter;
                        // $minTimeSmartInverter = date('Y-m-3');

                        $smartInverterStartTimeData = InverterDetail::select(DB::raw('SUM(inverterPower) as current_generation,SUM(current_consumption) as current_consumption, daily_generation as totalEnergy'), 'collect_time')->where(['plant_id' => $plantID, 'siteId' => $siteID])->whereBetween('collect_time', [date($minTimeSmartInverter . ' 00:00:00'), date($minTimeSmartInverter . ' 23:59:59')])->groupBy('collect_time')->get();

                        foreach ($smartInverterStartTimeData as $generationLogKey => $generationLogData) {
//                            return [$generationLogData->battery_power,$generationLogData->battery_capacity,$generationLogData->battery_type];

                            if (GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $generationLogData->collect_time)->exists()) {

                                $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $generationLogData->collect_time)->first();

                                $generationData->current_generation = $generationLogData->current_generation;
                                $generationData->totalEnergy = $generationLogData->totalEnergy;
                                $generationData->current_consumption = $generationLogData->current_consumption;
//                                $generationData->battery_power = $generationLogData->battery_power;
//                                $generationData->battery_capacity = $generationLogData->battery_capacity;
//                                $generationData->battery_type = $generationLogData->battery_type;
                                $generationData->save();
                            } else {

                                $generationLog['plant_id'] = $plantID;
                                $generationLog['siteId'] = $siteID;
                                $generationLog['current_generation'] = $generationLogData->current_generation;
                                $generationLog['comm_failed'] = 0;
                                $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
//                                $generationData['totalConsumption'] = $generationLogData->totalConsumption;
                                $generationLog['current_consumption'] = $generationLogData->current_consumption;
                                $generationLog['current_grid'] = 0;
                                $generationLog['current_irradiance'] = 0;
                                $generationLog['totalEnergy'] = $generationLogData->totalEnergy;
                                $generationLog['collect_time'] = $generationLogData->collect_time;
//                                $generationLog['battery_power'] = $generationLogData->battery_power;
//                                $generationLog['battery_capacity'] = $generationLogData->battery_capacity;
//                                $generationLog['battery_type'] = $generationLogData->battery_type;
                                $generationLog['created_at'] = $currentTime;
                                $generationLog['updated_at'] = $currentTime;


                                $generationLogResponse = GenerationLog::create($generationLog);
                            }
                        }
                    }
//                    if (!(empty($siteSmartInverterLogStartTime))) {
//
//                        $minTimeSmartInverter = date('Y-m-d', min($siteSmartInverterLogStartTime));
//                        // $minTimeSmartInverter = date('Y-m-3');
//
//                        $smartInverterStartTimeData = StationBattery::where(['plant_id' => $plantID, 'site_id' => $siteID])->whereBetween('collect_time', [date($minTimeSmartInverter . ' 00:00:00'), date($minTimeSmartInverter . ' 23:59:59')])->groupBy('collect_time')->get();
//
//                        foreach ($smartInverterStartTimeData as $generationLogKey => $generationLogData) {
//
//                            if (GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $generationLogData->collect_time)->exists()) {
//
//                                $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $generationLogData->collect_time)->first();
//
//                                $generationData->battery_type = $generationLogData->battery_type;
//                                $generationData->battery_capacity = $generationLogData->battery_capacity;
//                                $generationData->battery_power = $generationLogData->battery_power;
//                                $generationData->save();
//                            } else {
//
//                                $generationLog['plant_id'] = $plantID;
//                                $generationLog['siteId'] = $siteID;
//                                $generationLog['current_generation'] = 0;
//                                $generationLog['comm_failed'] = 0;
//                                $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
//                                $generationLog['current_consumption'] = 0;
//                                $generationLog['current_grid'] = 0;
//                                $generationLog['current_irradiance'] = 0;
//                                $generationLog['totalEnergy'] = 0;
//                                $generationLog['collect_time'] = $generationLogData->collect_time;
//                                $generationLog['battery_power'] = $generationData->battery_power;
//                                $generationLog['battery_capacity'] = $generationData->battery_capacity;
//                                $generationLog['battery_type'] = $generationData->battery_type;
//                                $generationLog['created_at'] = $currentTime;
//                                $generationLog['updated_at'] = $currentTime;
//
//                                $generationLogResponse = GenerationLog::create($generationLog);
//                            }
//                        }
//                    }
                    if (!(empty($siteAllInverterLogStartTime))) {

                        $minTimeGridInverter = date('Y-m-d', min($siteAllInverterLogStartTime));
                        // $minTimeGridInverter = date('Y-m-d');

                        $gridInverterStartTimeData = InverterEnergyLog::select(DB::raw('SUM(grid_power) as grid_power'), 'collect_time')->where(['plant_id' => $plantID, 'site_id' => $siteID])->whereBetween('collect_time', [date($minTimeGridInverter . ' 00:00:00'), date($minTimeGridInverter . ' 23:59:59')])->groupBy('collect_time')->get();

                        foreach ($gridInverterStartTimeData as $gridLogKey => $gridLogData) {

                            if (GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $gridLogData->collect_time)->exists()) {

                                $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $gridLogData->collect_time)->first();

//                                $generationData->current_consumption = ($generationData->current_generation + $gridLogData->grid_power) > 0 ? ($generationData->current_generation + $gridLogData->grid_power) : 0;
                                $generationData->current_grid = ($gridLogData->grid_power);
                                $generationData->save();
                            } else {

                                $generationLog['plant_id'] = $plantID;
                                $generationLog['siteId'] = $siteID;
                                $generationLog['current_generation'] = 0;
                                $generationLog['current_consumption'] = 0;
                                $generationLog['comm_failed'] = 0;
                                $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
//                                $generationLog['current_consumption'] = $gridLogData->grid_power > 0 ? $gridLogData->grid_power : 0;
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


//                        if (!(empty($siteSmartInverterLogStartTime))) {
//
//                            $minTimeSmartInverter = date('Y-m-d', min($siteSmartInverterLogStartTime));
////                            return [date($minTimeSmartInverter . ' 00:00:00'),date($minTimeSmartInverter . ' 23:59:59')];
////                            return
//
//                            $smartInverterStartTimeData = InverterDetail::select(DB::raw('SUM(inverterPower) as current_generation, SUM(daily_generation) as totalEnergy'), 'collect_time')->where(['plant_id' => $plantID, 'siteId' => $siteID])->whereBetween('collect_time', [date($minTimeSmartInverter . ' 00:00:00'), date($minTimeSmartInverter . ' 23:59:59')])->groupBy('collect_time')->get();
////                            return $smartInverterStartTimeData;
//
//                            foreach ($smartInverterStartTimeData as $generationLogKey => $generationLogData) {
//                                $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $generationLogData->collect_time)->first();
////                                 print_r('\n');
////                                 print_r('generationData');
////                                 print_r($generationData);
//                                if (!empty($generationData)) {
////                                  print_r('update generation data');
//                                    if ($generationData->current_generation != $generationLogData->current_generation || $generationData->totalEnergy != $generationLogData->totalEnergy) {
//                                        $generationData->current_generation = $generationLogData->current_generation;
//                                        $generationData->totalEnergy = $generationLogData->totalEnergy;
//                                        $generationData->save();
//                                    }
//                                } else {
//                                    $generationLogResponseData = new GenerationLog();
//                                    $generationLogResponseData->plant_id = $plantID;
//                                    $generationLogResponseData->siteId = $siteID;
//                                    $generationLogResponseData->current_generation = $generationLogData->current_generation;
//                                    $generationLogResponseData->comm_failed = 0;
//                                    $generationLogResponseData->cron_job_id = $generationLogMaxCronJobID;
//                                    $generationLogResponseData->current_consumption = 0;
//                                    $generationLogResponseData->current_grid = 0;
//                                    $generationLogResponseData->current_irradiance = 0;
//                                    $generationLogResponseData->totalEnergy = $generationLogData->totalEnergy;
//                                    $generationLogResponseData->collect_time = $generationLogData->collect_time;
//                                    $generationLogResponseData->save();
//
////                                    $generationLog = array();
//////                                    print_r('save generation data');
////
////                                    $generationLog['plant_id'] = $plantID;
////                                    $generationLog['siteId'] = $siteID;
////                                    $generationLog['current_generation'] = $generationLogData->current_generation;
////                                    $generationLog['comm_failed'] = 0;
////                                    $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
////                                    $generationLog['current_consumption'] = 0;
////                                    $generationLog['current_grid'] = 0;
////                                    $generationLog['current_irradiance'] = 0;
////                                    $generationLog['totalEnergy'] = $generationLogData->totalEnergy;
////                                    $generationLog['collect_time'] = $generationLogData->collect_time;
//////                                    $generationLog['created_at'] = $currentTime;
//////                                    $generationLog['updated_at'] = $currentTime;
////
////                                    $generationLogResponse = GenerationLog::create($generationLog);
//
//                                }
//                            }
//                        }
//                        $alertController = new SolisAlertsController();
//                        $alertData = $alertController->AlarmAndFault($token, $plantID, $siteID);
//                        return $alertData;
                }
            }
//return [$this->cronJobCollectTime,"oooooookkkkkkkkkk"];
            if (!(empty($siteAllInverterLogStartTime))) {

                $minTimeInverter = date('Y-m-d', min($siteAllInverterLogStartTime));
                $maxTimeInverter = date('Y-m-d', max($siteAllInverterLogStartTime));
                $stationHistoryforProcessedParam =
                    [
                        "stationId" => $siteID,
                        "timeType" => 1,
                        "startTime" => $this->cronJobCollectTime,
                        "endTime" => $this->cronJobCollectTime,
                    ];
//                return $stationHistoryforProcessedParam;
                $stationHistoryprocessedcurl = curl_init();

                curl_setopt_array($stationHistoryprocessedcurl, array(
                    CURLOPT_URL => $solisAPIBaseURL . '/station/v1.0/history',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($stationHistoryforProcessedParam),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ' . $token,
                        'Content-Type: application/json'
                    ),
                ));

                $StationHistoryprocessedresponse = curl_exec($stationHistoryprocessedcurl);

                curl_close($stationHistoryprocessedcurl);

                $stationHistoryProcessedData = json_decode($StationHistoryprocessedresponse);
//                return json_encode($stationHistoryProcessedData);

                if (isset($stationHistoryProcessedData) && isset($stationHistoryProcessedData->stationDataItems)) {
                  $StatIonProcessedFinalDAta =  $stationHistoryProcessedData->stationDataItems;
                    foreach ($StatIonProcessedFinalDAta as $key69 => $stationProcessedhistoryData){
//                        return [$stationProcessedhistoryData,date('Y-m-d H:i:s',$stationProcessedhistoryData->dateTime)];
                        $TotalEnergy = DailyInverterDetail::where('siteId',$siteID)->where('created_at',$this->cronJobCollectTime)->sum('daily_generation');

                        $processedCurrentDataExist = ProcessedCurrentVariable::where(['plant_id' => $plantID])->where('collect_time', $stationProcessedhistoryData->dateTime)->first();
                        if ($processedCurrentDataExist) {

                            $processedCurrentData['plant_id'] = $plantID;
                            $processedCurrentData['current_generation'] = $stationProcessedhistoryData->generationPower;
                            $processedCurrentData['current_consumption'] = $stationProcessedhistoryData->usePower;
                            $processedCurrentData['current_grid'] = abs($stationProcessedhistoryData->purchasePower);
                            $processedCurrentData['grid_type'] = $stationProcessedhistoryData->purchasePower >= 0 ? '+ve' : '-ve';
                            $processedCurrentData['current_irradiance'] = isset($stationProcessedhistoryData->current_irradiance) ? $stationProcessedhistoryData->current_irradiance : 0;
                            $processedCurrentData['totalEnergy'] =$TotalEnergy ? $TotalEnergy : 0;
                            $processedCurrentData['current_saving'] = $stationProcessedhistoryData->generationPower * (double)$benchMarkPrice;
                            $processedCurrentData['processed_cron_job_id'] = $processedMaxCronJobID;
//                        $processedCurrentData['battery_power'] = $processedData->battery_power;
//                        $processedCurrentData['battery_capacity'] = $processedData->battery_capacity;
//                        $processedCurrentData['battery_type'] = $processedData->battery_type;
                            $processedCurrentData['collect_time'] = date('Y-m-d H:i:s',$stationProcessedhistoryData->dateTime);
                            $processedCurrentData['created_at'] = $currentTime;
                            $processedCurrentData['updated_at'] = $currentTime;

                            $processedCurrentDataResponse = $processedCurrentDataExist->fill($processedCurrentData)->save();
                        } else {

                            $processedCurrentData['plant_id'] = $plantID;
                            $processedCurrentData['current_generation'] = $stationProcessedhistoryData->generationPower;
                            $processedCurrentData['current_consumption'] = $stationProcessedhistoryData->usePower;
                            $processedCurrentData['current_grid'] = abs($stationProcessedhistoryData->purchasePower);
                            $processedCurrentData['grid_type'] = $stationProcessedhistoryData->purchasePower >= 0 ? '+ve' : '-ve';
                            $processedCurrentData['current_irradiance'] = isset($stationProcessedhistoryData->current_irradiance) ? $stationProcessedhistoryData->current_irradiance : 0;
                            $processedCurrentData['totalEnergy'] =$TotalEnergy ? $TotalEnergy : 0;
                            $processedCurrentData['current_saving'] = $stationProcessedhistoryData->generationPower * (double)$benchMarkPrice;
                            $processedCurrentData['processed_cron_job_id'] = $processedMaxCronJobID;
//                        $processedCurrentData['battery_power'] = $processedData->battery_power;
//                        $processedCurrentData['battery_capacity'] = $processedData->battery_capacity;
//                        $processedCurrentData['battery_type'] = $processedData->battery_type;
                            $processedCurrentData['collect_time'] = date('Y-m-d H:i:s',$stationProcessedhistoryData->dateTime);
                            $processedCurrentData['created_at'] = $currentTime;
                            $processedCurrentData['updated_at'] = $currentTime;

                            $processedCurrentDataResponse = ProcessedCurrentVariable::create($processedCurrentData);
                        }
                    }
                }

                $batteryData = StationBattery::where(['plant_id' => $plantID])->whereBetween('collect_time', [date($minTimeInverter . ' 00:00:00'), date($maxTimeInverter . ' 23:59:59')])->groupBy('collect_time')->get();

//                    return $generationLogInverterStartTimeData;

                foreach ($batteryData as $key45 => $battery) {

                    $processedCurrentBatteryData = ProcessedCurrentVariable::where(['plant_id' => $plantID])->where('collect_time', $battery->collect_time)->first();
                    if ($processedCurrentBatteryData) {

                        $processedCurrentBatteryData->battery_power = $battery->battery_power;
                        $processedCurrentBatteryData->battery_capacity = $battery->battery_capacity;
                        $processedCurrentBatteryData->battery_type = $battery->battery_type;
                        $processedCurrentBatteryData->total_discharge_energy = $battery->total_discharge_energy;
                        $processedCurrentBatteryData->total_charge_energy = $battery->total_charge_energy;
                        $processedCurrentBatteryData->battery_charge = $battery->daily_charge_energy;
                        $processedCurrentBatteryData->battery_discharge = $battery->daily_discharge_energy;
                        $processedCurrentBatteryData->created_at = $currentTime;
                        $processedCurrentBatteryData->updated_at = $currentTime;

                        $processedCurrentBatteryData->update();
                    }
                }
//                    return [$minTimeInverter,date('Y-m-d', strtotime("+1 day", strtotime($maxTimeInverter)))];
//                    return [$minTimeInverter,date('Y-m-d', strtotime("+1 day", strtotime($maxTimeInverter)))];

                while ($minTimeInverter != date('Y-m-d', strtotime("+1 day", strtotime($maxTimeInverter)))) {
//                        return 'okkk';

                    $plantDataDateToday = $minTimeInverter;

                    $plantDailyTotalBuyEnergy = 0;
                    $plantDailyTotalSellEnergy = 0;

                    $plantInverterListData = SiteInverterDetail::where('plant_id', $plantID)->where('dv_inverter_type', "INVERTER")->get();


                    foreach ($plantInverterListData as $invListData) {


                        $inverterEnergyData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->whereNotNull('import_energy')->first();
                        if ($inverterEnergyData) {
                            $inverterEnergyTodayImportData = $inverterEnergyData->import_energy;
                        } else {

                            $inverterEnergyTodayImportData = 0;
                        }

                        $inverterEnergyExportData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateToday)->orderBy('collect_time', 'DESC')->whereNotNull('export_energy')->first();
                        if ($inverterEnergyExportData) {
                            $inverterEnergyTodayExportData = $inverterEnergyExportData->export_energy;
                        } else {
                            $inverterEnergyTodayExportData = 0;
                        }

                        $plantDailyTotalBuyEnergy = $inverterEnergyTodayImportData;
                        $plantDailyTotalSellEnergy = $inverterEnergyTodayExportData;
                    }
//                            return ["ok", $plantDailyTotalBuyEnergy,$plantDailyTotalSellEnergy];

                    $plantDailyTotalGeneration = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first()->daily_generation : 0;
                    $plantDailyTotalConsumption = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first()->daily_consumption : 0;
                    $plantDailyChargeEnergy = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first()->daily_charge_energy : 0;
                    $plantDailyDischargeEnergy = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first()->daily_discharge_energy : 0;
                    $peakTimeStart = $plantData->peak_time_start;
                    $dailyPeakConsumptionValue = 0;
                    $peakTimeEnd = $plantData->peak_time_end;
                    $peakStartTimeDetail = $peakTimeStart . ':00:00';
                    $peakEndTimeDetail = $peakTimeEnd . ':00:00';
                    $peakStartTimeConsumptionValue = 0;
                    $peakEndTimeConsumptionValue = 0;
//                    $inverterDetailData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->pluck('collect_time');
//                    return $inverterDetailData;
                    $peakStartTimeConsumption = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '<=', $peakEndTimeDetail)->whereTime('collect_time', '>=', $peakStartTimeDetail)->orderBy('collect_time')->first();
                    $peakEndTimeConsumption = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '>=', $peakStartTimeDetail)->whereTime('collect_time', '<=', $peakEndTimeDetail)->orderBy('collect_time','DESC')->latest()->first();
//return [$peakStartTimeDetail, $peakEndTimeDetail, $peakEndTimeConsumption];
                    if ($peakStartTimeConsumption) {
                        $peakStartTimeConsumptionValue = $peakStartTimeConsumption->daily_consumption;
                    }
                    if ($peakEndTimeConsumption) {
                        $peakEndTimeConsumptionValue = $peakEndTimeConsumption->daily_consumption;
                    }
//                    return [$peakStartTimeConsumption,$peakEndTimeConsumption,$peakEndTimeConsumptionValue,$peakStartTimeConsumptionValue];
                    if (($peakStartTimeConsumptionValue != $peakEndTimeConsumptionValue) && ($peakEndTimeConsumptionValue != 0)) {
                        if ($peakEndTimeConsumptionValue > $peakStartTimeConsumptionValue) {
                            $dailyPeakConsumptionValue = $peakEndTimeConsumptionValue - $peakStartTimeConsumptionValue;
                            $dailyPeakConsumptionValue = round($dailyPeakConsumptionValue, 2);
                        } else {
                            $dailyPeakConsumptionValue = 0;
                        }
                    }
                    $peakStartTimeGridBuyValue = 0;
                    $peakEndTimeGridBuyValue = 0;
                    $dailyPeakGridImportEnergy = 0;
                    $peakStartTimeGridBuy = InverterEnergyLog::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '<=', $peakEndTimeDetail)->whereTime('collect_time', '>=', $peakStartTimeDetail)->orderBy('collect_time')->first();
                    $peakEndTimeGridBuy = InverterEnergyLog::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '>=', $peakStartTimeDetail)->whereTime('collect_time', '<=', $peakEndTimeDetail)->orderBy('collect_time','DESC')->latest()->first();

                    if ($peakStartTimeGridBuy) {
                        $peakStartTimeGridBuyValue = $peakStartTimeGridBuy->import_energy;
                    }
                    if ($peakEndTimeGridBuy) {
                        $peakEndTimeGridBuyValue = $peakEndTimeGridBuy->import_energy;
                    }
//                    return [$peakStartTimeGridBuy,$peakEndTimeGridBuy,$peakStartTimeDetail,$peakEndTimeDetail,$dailyPeakGridImportEnergy,$peakEndTimeGridBuyValue];
                    if (($peakEndTimeGridBuyValue != $peakStartTimeGridBuyValue) && ($peakEndTimeGridBuyValue != 0)) {
                        if ($peakEndTimeGridBuyValue > $peakStartTimeGridBuyValue) {
                            $dailyPeakGridImportEnergy = $peakEndTimeGridBuyValue - $peakStartTimeGridBuyValue;
                            $dailyPeakGridImportEnergy = round($dailyPeakGridImportEnergy, 2);
                        } else {
                            $dailyPeakGridImportEnergy = 0;
                        }
                    }
//                    return [$dailyPeakGridImportEnergy,$peakStartTimeGridBuy,$peakEndTimeGridBuy,$peakStartTimeDetail,$peakEndTimeDetail,$peakStartTimeGridBuyValue,$peakEndTimeGridBuyValue];

                    $peakStartTimeBatteryDischargeValue = 0;
                    $peakEndTimeBatteryDischargeValue = 0;
                    $dailyPeakBatteryDischargeEnergy = 0;
                    $dailyOutagesHoursData = 0;
                    $peakStartTimeBatteryDischarge = StationBattery::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '<=', $peakEndTimeDetail)->whereTime('collect_time', '>=', $peakStartTimeDetail)->first();
                    $peakEndTimeBatteryDischarge = StationBattery::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '>=', $peakStartTimeDetail)->whereTime('collect_time', '<=', $peakEndTimeDetail)->orderBy('collect_time', 'DESC')->latest()->first();
                    if ($peakStartTimeBatteryDischarge) {
                        $peakStartTimeBatteryDischargeValue = $peakStartTimeBatteryDischarge->daily_discharge_energy;
                    }
                    if ($peakEndTimeBatteryDischarge) {
                        $peakEndTimeBatteryDischargeValue = $peakEndTimeBatteryDischarge->daily_discharge_energy;
                    }
                    if (($peakEndTimeBatteryDischargeValue != $peakStartTimeBatteryDischargeValue) && ($peakEndTimeBatteryDischargeValue != 0)) {
                        if ($peakEndTimeBatteryDischargeValue > $peakStartTimeBatteryDischargeValue) {
                            $dailyPeakBatteryDischargeEnergy = $peakEndTimeBatteryDischargeValue - $peakStartTimeBatteryDischargeValue;
                            $dailyPeakBatteryDischargeEnergy = round($dailyPeakBatteryDischargeEnergy, 2);
                        } else {
                            $dailyPeakBatteryDischargeEnergy = 0;
                        }
                    }
//                    return [$peakStartTimeBatteryDischarge,$peakEndTimeBatteryDischarge, $peakStartTimeBatteryDischargeValue,$peakEndTimeBatteryDischargeValue];
                    $dailyOutagesHours = InverterDetail::Select('collect_time', 'total_grid_voltage')->where('plant_id', $plantID)->where('total_grid_voltage', '!=', Null)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->get();
//                    return $dailyOutagesHours;
                    $maxValue = 0;
                    $minValue = 0;
                    $totalValue = [];
                    $totalValuesData = 0;
//                    for ($k = 0; $k < count($dailyOutagesHours); $k++) {
//
//                        if ($dailyOutagesHours[$k]['total_grid_voltage']) {
//                            if ($dailyOutagesHours[$k]['total_grid_voltage'] <= 160 && $minValue == 0) {
//                                $minValue = $dailyOutagesHours[$k]['collect_time'];
//                            } elseif ($dailyOutagesHours[$k]['total_grid_voltage'] > 160 && $maxValue == 0 && $minValue != 0) {
//                                $maxValue = $dailyOutagesHours[$k]['collect_time'];
//                            }
//                            if ($minValue != 0 && $maxValue != 0) {
////                                return [$minValue,$maxValue];
//                                $startTime = date('H:i:s', strtotime($minValue));
//                                $endTime = date('H:i:s', strtotime($maxValue));
//                                $start_t = new DateTime($startTime);
//                                $current_t = new DateTime($endTime);
//                                $difference = $start_t->diff($current_t);
//                                $return_time = $difference->format('%H:%I');
////                                return $return_time;
//
////                                $totalMinutes = strtotime($endTime) - strtotime($startTime);
////                                return [$totalMinutes];
////                                return $totalMinutes;
////                                $hours = date('H:i:s',$totalMinutes);
////                                return [$totalMinutes,$hours];
////                                $hours = floor($totalMinutes / 60) . ':' . ($totalMinutes - floor($totalMinutes / 60) * 60);
////                                $start_date = new DateTime($minValue);
////                                $end_date = new DateTime($maxValue);
////                                $interval = $start_date->diff($end_date);
////                                $hours = $interval->format('%h');
////                                $minutes = $interval->format('%i');
////                                $totalValuesData = $hours . ':' . $minutes;
////                                return $hours;
//                                array_push($totalValue, $return_time);
//                                $maxValue = 0;
//                                $minValue = 0;
//                            }
//                        }
////                            $maxValue = 0;
////                            $minValue = 0;
//                    }
////                    return $totalValue;
//                    $dailyDataSum = $this->AddOutagesTime($totalValue);
////                    return
////                    return $dailyOutageDetailData;
////                    return date('H:i',$totalTimeSum);
//                    if ($dailyDataSum) {
//                        $dailyOutagesHoursData = $dailyDataSum;
//                    } else {
//                        $dailyOutagesHoursData = '00:00';
//                    }
//                    return $dailyOutagesHoursData;
//                    return $dailyOutageData;


                    //Daily Outage Served
                    if ($plant->grid_type == 'Three-phase') {
//                        return [$siteID,$smartInverter, $this->cronJobCollectTime,$plant->grid_type];
                        $dailyOutages = InverterEnergyLog::Select('collect_time', 'grid_voltage_l1','grid_voltage_l2','grid_voltage_l3')->where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'desc')->get();
//return $dailyOutages;
                        for ($k = 0; $k < count($dailyOutages); $k++) {

                            if ($dailyOutages[$k]['grid_voltage_l1']) {
                                if ($dailyOutages[$k]['grid_voltage_l1'] <= 160 && $dailyOutages[$k]['grid_voltage_l2'] <= 160 &&  $dailyOutages[$k]['grid_voltage_l3'] <= 160 && $minValue == 0) {
                                    $minValue = $dailyOutages[$k]['collect_time'];
                                } elseif ($dailyOutages[$k]['grid_voltage_l1'] > 160 && $dailyOutages[$k]['grid_voltage_l2'] > 160 && $dailyOutages[$k]['grid_voltage_l3'] > 160 && $maxValue == 0 && $minValue != 0) {
                                    $maxValue = $dailyOutages[$k]['collect_time'];
                                }
                        $maxtimeramge =    Date('H:i' ,strtotime($dailyOutages[$k]['collect_time']));
                                if($maxtimeramge <= "00:10" && $maxValue == 0){
                                    $maxValue = $dailyOutages[$k]['collect_time'];

                                }
                                if ($minValue != 0 && $maxValue != 0) {
//                                return [$minValue,$maxValue];
                                    $startTime = date('H:i:s', strtotime($minValue));
                                    $endTime = date('H:i:s', strtotime($maxValue));
                                    $start_t = new DateTime($startTime);
                                    $current_t = new DateTime($endTime);
                                    $difference = $start_t->diff($current_t);
                                    $return_time = $difference->format('%H:%I');
                                    array_push($totalValue, $return_time);
                                    $maxValue = 0;
                                    $minValue = 0;
                                }
                            }
                        }

                    }else{
                        $dailyOutages = InverterEnergyLog::Select('collect_time', 'grid_voltage_ln')->where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'desc')->get();

                        for ($k = 0; $k < count($dailyOutages); $k++) {

                            if ($dailyOutages[$k]['grid_voltage_l1']) {
                                if ($dailyOutages[$k]['grid_voltage_ln'] <= 160 && $minValue == 0) {
                                    $minValue = $dailyOutages[$k]['collect_time'];
                                } elseif ($dailyOutages[$k]['grid_voltage_ln'] > 160 && $dailyOutages[$k]['grid_voltage_l2'] > 160 && $dailyOutages[$k]['grid_voltage_l3'] > 160 && $maxValue == 0 && $minValue != 0) {
                                    $maxValue = $dailyOutages[$k]['collect_time'];
                                }
                                $maxtimeramge =    Date('H:i' ,strtotime($dailyOutages[$k]['collect_time']));
                                if($maxtimeramge <= "00:10" && $maxValue == 0){
                                    $maxValue = $dailyOutages[$k]['collect_time'];

                                }
                                if ($minValue != 0 && $maxValue != 0) {
                                    $startTime = date('H:i:s', strtotime($minValue));
                                    $endTime = date('H:i:s', strtotime($maxValue));
                                    $start_t = new DateTime($startTime);
                                    $current_t = new DateTime($endTime);
                                    $difference = $start_t->diff($current_t);
                                    $return_time = $difference->format('%H:%I');
                                    array_push($totalValue, $return_time);
                                    $maxValue = 0;
                                    $minValue = 0;
                                }
                            }
                        }
                    }

                    $dailyDataSum = $this->AddOutagesTime($totalValue);

                    if ($dailyDataSum) {
                        $dailyOutagesHoursData = $dailyDataSum;
                    } else {
                        $dailyOutagesHoursData = '00:00';
                    }
//                    $dailyOutages = InverterEnergyLog::Select('collect_time', 'total_grid_voltage')->where('plant_id', $plantID)->where('total_grid_voltage', '!=', Null)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->get();


                    //                    ->
//                    where('total_grid_voltage', '<=', 160)
//                    $maxValue = 0;
//                    $minValue = 0;
//                    $totalValue = 0;
//                        for ($k = 0; $k < count($dailyOutagesHours); $k++) {
//                            if($dailyOutagesHours[$i]['total_grid_voltage'])
//
//                        }
//                    if ($dailyOutagesHours) {
//                        $outagesDataArray = json_decode(json_encode($dailyOutagesHours), true);
//                        if ($outagesDataArray) {
//                            $minData = min(array_column($outagesDataArray, 'collect_time'));
//                            $maxData = max(array_column($outagesDataArray, 'collect_time'));
//                            $start_date = new DateTime($minData);
//                            $end_date = new DateTime($maxData);
//                            $interval = $start_date->diff($end_date);
//                            $hours = $interval->format('%h');
//                            $minutes = $interval->format('%i');
//                            $dailyOutagesHoursData = $hours . ':' . $minutes;
//                        } else {
//                            $dailyOutagesHoursData = '00:00';
//                        }
//                    }

//                    return [$dailyPeakBatteryDischargeEnergy,$peakStartTimeBatteryDischarge,$peakEndTimeBatteryDischarge];
//                            return [$peakStartTimeDetail,$peakEndTimeDetail];

//                    $stationHistoryParam = [$siteID,$plantID,$plantDataDateToday];
                    $stationHistoryParam =
                        [
                            "stationId" => $siteID,
                            "timeType" => 2,
                            "startTime" => $plantDataDateToday,
                            "endTime" => $plantDataDateToday,
                        ];
                    $stationHistorycurl = curl_init();

                    curl_setopt_array($stationHistorycurl, array(
                        CURLOPT_URL => $solisAPIBaseURL . '/station/v1.0/history',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($stationHistoryParam),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $token,
                            'Content-Type: application/json'
                        ),
                    ));

                    $StationHistoryresponse = curl_exec($stationHistorycurl);

                    curl_close($stationHistorycurl);

                    $stationHistoryData = json_decode($StationHistoryresponse);
//                    return  $stationHistoryData;
                    if (isset($stationHistoryData) && isset($stationHistoryData->stationDataItems)) {

                        $stationDatahistory = $stationHistoryData->stationDataItems;
                        $arrayData = array_values($stationDatahistory);
                        $dailyProcessed['grid_ratio'] = isset($arrayData[0]->gridRatio) ? $arrayData[0]->gridRatio: 0;
                        $dailyProcessed['charge_ratio'] = isset($arrayData[0]->chargeRatio) ? $arrayData[0]->chargeRatio : 0;
                        $dailyProcessed['generation_value'] = isset($arrayData[0]->generationValue) ? $arrayData[0]->generationValue : 0;
                        $dailyProcessed['generation_ratio'] =isset($arrayData[0]->generationRatio) ? $arrayData[0]->generationRatio : 0;
                        $dailyProcessed['use_value'] = isset($arrayData[0]->useValue) ? $arrayData[0]->useValue : 0;
                        $dailyProcessed['use_ratio'] = isset($arrayData[0]->useRatio) ? $arrayData[0]->useRatio : 0;
                        $dailyProcessed['grid_value'] = isset($arrayData[0]->gridValue) ? $arrayData[0]->gridValue : 0;
                        $dailyProcessed['discharge_ratio'] = isset($arrayData[0]->useDischargeRatio) ?  $arrayData[0]->useDischargeRatio : 0;

//                        $processedData = ProcessedCurrentVariable::where(['plant_id' => $plantID])->where('collect_time', $processedData->collect_time)->first();
////                        return [$processedData,$processedData->collect_time];
//                        if($processedData){
//
//                            $processedData['grid_ratio'] = isset($arrayData[0]->gridRatio) ? $arrayData[0]->gridRatio: 0;
//                            $processedData['charge_ratio'] = isset($arrayData[0]->chargeRatio) ? $arrayData[0]->chargeRatio : 0;
//                            $processedData['generation_value'] = isset($arrayData[0]->generationValue) ? $arrayData[0]->generationValue : 0;
//                            $processedData['generation_ratio'] =isset($arrayData[0]->generationRatio) ? $arrayData[0]->generationRatio : 0;
//                            $processedData['use_value'] = isset($arrayData[0]->useValue) ? $arrayData[0]->useValue : 0;
//                            $processedData['use_ratio'] = isset($arrayData[0]->useRatio) ? $arrayData[0]->useRatio : 0;
//                            $processedData['grid_value'] = isset($arrayData[0]->gridValue) ? $arrayData[0]->gridValue : 0;
//                            $processedData['discharge_ratio'] = isset($arrayData[0]->useDischargeRatio) ?  $arrayData[0]->useDischargeRatio : 0;
//
////                            $processedData['grid_ratio'] = $arrayData[0]->gridRatio;
////                            $processedData['charge_ratio'] = $arrayData[0]->chargeRatio;
////                            $processedData['generation_value'] = $arrayData[0]->generationValue;
////                            $processedData['generation_ratio'] = $arrayData[0]->generationRatio;
////                            $processedData['use_value'] = $arrayData[0]->useValue;
////                            $processedData['use_ratio'] = $arrayData[0]->useRatio;
////                            $processedData['grid_value'] = $arrayData[0]->gridValue;
////                            $processedData['discharge_ratio'] = $arrayData[0]->useDischargeRatio;
//                            $processedData->save();
//
//                        }
                    }
                    //Solar Energy Utilization History
                    $SolarBatteryCharge = $dailyProcessed['generation_value'] * $dailyProcessed['charge_ratio'] / 100;
                    $SolarGridExport = $dailyProcessed['generation_value'] * $dailyProcessed['grid_ratio'] / 100;
                    $SolarLoad = $dailyProcessed['generation_value'] * $dailyProcessed['generation_ratio'] / 100;

                     $SolarUtilizationHistory  = new SolarEnergyUtilization();

                    $SolarUtilizationHistory->plant_id = $plantID;
                    $SolarUtilizationHistory->battery_charge = $SolarBatteryCharge;
                    $SolarUtilizationHistory->grid_export = $SolarGridExport;
                    $SolarUtilizationHistory->load_energy = $SolarLoad;
                    $SolarUtilizationHistory->collect_time = Date('Y-m-d H:i:s');
//                    return [$dailyProcessed['generation_value'], $dailyProcessed['charge_ratio'],$dailyProcessed['grid_ratio'],$dailyProcessed['generation_ratio'] , $SolarUtilizationHistory];
                    $SolarUtilizationHistory->save();

                    //PLANT DAILY DATA
                    $dailyProcessed['plant_id'] = $plantID;
                    $dailyProcessed['dailyGeneration'] = $plantDailyTotalGeneration;
                    $dailyProcessed['dailyGridPower'] = $plantDailyTotalBuyEnergy > $plantDailyTotalSellEnergy ? $plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy : $plantDailyTotalSellEnergy - $plantDailyTotalBuyEnergy;
                    $dailyProcessed['dailyBoughtEnergy'] = $plantDailyTotalBuyEnergy;
                    $dailyProcessed['daily_peak_hours_consumption'] = $dailyPeakConsumptionValue;
                    $dailyProcessed['daily_peak_hours_grid_buy'] = $dailyPeakGridImportEnergy;
                    $dailyProcessed['daily_peak_hours_battery_discharge'] = $dailyPeakBatteryDischargeEnergy;
                    $dailyProcessed['dailySellEnergy'] = $plantDailyTotalSellEnergy;
                    $dailyProcessed['daily_outage_grid_voltage'] = $dailyOutagesHoursData;
                    $dailyProcessed['dailyMaxSolarPower'] = 0;
                    $dailyProcessed['dailyConsumption'] = $plantDailyTotalConsumption;
                    $dailyProcessed['dailySaving'] = (double)$plantDailyTotalGeneration * (double)$benchMarkPrice;
                    $dailyProcessed['dailyIrradiance'] = 0;
                    $dailyProcessed['daily_charge_energy'] = $plantDailyChargeEnergy;
                    $dailyProcessed['daily_discharge_energy'] = $plantDailyDischargeEnergy;
                    $dailyProcessed['updated_at'] = $currentTime;

                    $dailyProcessedPlantDetailExist = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->first();

                    if ($dailyProcessedPlantDetailExist) {
                        $dailyProcessedPlantDetailExist->plant_id = $plantID;
                        $dailyProcessedPlantDetailExist->dailyGeneration = $dailyProcessed['dailyGeneration'];
                        $dailyProcessedPlantDetailExist->dailyGridPower = $dailyProcessed['dailyGridPower'];
                        $dailyProcessedPlantDetailExist->dailyBoughtEnergy = $dailyProcessed['dailyBoughtEnergy'];
                        $dailyProcessedPlantDetailExist->daily_peak_hours_consumption = $dailyProcessed['daily_peak_hours_consumption'];
                        $dailyProcessedPlantDetailExist->daily_peak_hours_grid_buy = $dailyProcessed['daily_peak_hours_grid_buy'];
                        $dailyProcessedPlantDetailExist->daily_peak_hours_battery_discharge = $dailyProcessed['daily_peak_hours_battery_discharge'];
                        $dailyProcessedPlantDetailExist->dailySellEnergy = $dailyProcessed['dailySellEnergy'];
                        $dailyProcessedPlantDetailExist->daily_outage_grid_voltage = $dailyProcessed['daily_outage_grid_voltage'];
                        $dailyProcessedPlantDetailExist->dailyMaxSolarPower = $dailyProcessed['dailyMaxSolarPower'];
                        $dailyProcessedPlantDetailExist->dailyConsumption = $dailyProcessed['dailyConsumption'];
                        $dailyProcessedPlantDetailExist->dailySaving = $dailyProcessed['dailySaving'];
                        $dailyProcessedPlantDetailExist->dailyIrradiance = $dailyProcessed['dailyIrradiance'];
                        $dailyProcessedPlantDetailExist->daily_charge_energy = $dailyProcessed['daily_charge_energy'];
                        $dailyProcessedPlantDetailExist->daily_discharge_energy = $dailyProcessed['daily_discharge_energy'];
                        $dailyProcessedPlantDetailExist->grid_ratio = $dailyProcessed['grid_ratio'];
                        $dailyProcessedPlantDetailExist->charge_ratio = $dailyProcessed['charge_ratio'];
                        $dailyProcessedPlantDetailExist->generation_ratio = $dailyProcessed['generation_ratio'];
                        $dailyProcessedPlantDetailExist->generation_value = $dailyProcessed['generation_value'];
                        $dailyProcessedPlantDetailExist->use_value = $dailyProcessed['use_value'];
                        $dailyProcessedPlantDetailExist->use_ratio = $dailyProcessed['use_ratio'];
                        $dailyProcessedPlantDetailExist->grid_value = $dailyProcessed['grid_value'];
                        $dailyProcessedPlantDetailExist->discharge_ratio = $dailyProcessed['discharge_ratio'];
                        $dailyProcessedPlantDetailExist->updated_at = $dailyProcessed['updated_at'];
                        $dailyProcessedPlantDetailExist->save();

                    } else {
                        $dailyProcessed = new DailyProcessedPlantDetail();
                        $dailyProcessed['plant_id'] = $plantID;
                        $dailyProcessed['dailyGeneration'] = $plantDailyTotalGeneration;
                        $dailyProcessed['dailyGridPower'] = $plantDailyTotalBuyEnergy > $plantDailyTotalSellEnergy ? $plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy : $plantDailyTotalSellEnergy - $plantDailyTotalBuyEnergy;
                        $dailyProcessed['dailyBoughtEnergy'] = $plantDailyTotalBuyEnergy;
                        $dailyProcessed['daily_peak_hours_consumption'] = $dailyPeakConsumptionValue;
                        $dailyProcessed['daily_peak_hours_grid_buy'] = $dailyPeakGridImportEnergy;
                        $dailyProcessed['daily_peak_hours_battery_discharge'] = $dailyPeakBatteryDischargeEnergy;
                        $dailyProcessed['dailySellEnergy'] = $plantDailyTotalSellEnergy;
                        $dailyProcessed['daily_outage_grid_voltage'] = $dailyOutagesHoursData;
                        $dailyProcessed['dailyMaxSolarPower'] = 0;
                        $dailyProcessed['dailyConsumption'] = $plantDailyTotalConsumption;
                        $dailyProcessed['dailySaving'] = (double)$plantDailyTotalGeneration * (double)$benchMarkPrice;
                        $dailyProcessed['dailyIrradiance'] = 0;
                        $dailyProcessed['daily_charge_energy'] = $plantDailyChargeEnergy;
                        $dailyProcessed['daily_discharge_energy'] = $plantDailyDischargeEnergy;
                        $dailyProcessed['updated_at'] = $currentTime;
                        $dailyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($plantDataDateToday));
                        $dailyProcessed->save();
//                        $dailyProcessedPlantDetailInsertionResponce = DailyProcessedPlantDetail::create($dailyProcessed);
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
                $monthlyPeakHoursConsumption = array_sum(array_column($dataArray, 'daily_peak_hours_consumption'));
                $monthlyPeakHoursGridBuy = array_sum(array_column($dataArray, 'daily_peak_hours_grid_buy'));
                $monthlyPeakHoursDischargeEnergy = array_sum(array_column($dataArray, 'daily_peak_hours_battery_discharge'));
                $times = array();
                for ($i = 0; $i < count($plantGenerationTableData); $i++) {
                    if ($plantGenerationTableData[$i]['daily_outage_grid_voltage']) {
                        $times[] = date('H:i:s', strtotime($plantGenerationTableData[$i]['daily_outage_grid_voltage']));
                    }
//                    $peakHoursData += date('H:i:s',strtotime($dailyVoltage));
                }

//                return $times;
//                $sum = strtotime('00:00:00');
//                $sum2=0;
//                foreach ($times as $v){
//
//                    $sum1=strtotime($v)-$sum;
//
//                    $sum2 = $sum2+$sum1;
//                }
//
//                $sum3=$sum+$sum2;
//                $monthlyOutagesData = date("H:i",strtotime($sum3));
//                return date("H:i",strtotime($sum3));
                $monthlyOutagesData = $this->AddOutagesTime($times);

//                return $monthlyOutagesData;
//                return $dataDetails;
//                return [$peakHoursData,$dailyVoltage];
//                $monthlyOutagesData = date('h:i',strtotime());
//                return $monthlyOutagesData;

                $plantDailyGenerationDataSum = 0;
                $plantDailyConsumptionDataSum = 0;
                $plantDailyGridDataSum = 0;
                $plantDailyBoughtDataSum = 0;
                $plantDailyGridDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->sum('dailyGridPower');
                $plantDailySellDataSum = 0;
                $plantDailyChargeEnergy = 0;
                $plantDailyDischargeEnegry = 0;
                $monthlyGridRatio = 0;
                $monthlyChargeRatio = 0;
                $monthlyGenerationValue = 0;
                $monthlyGenerationRatio = 0;
                $monthlyUseRatio = 0;
                $monthlyUseValue = 0;
                $monthlyGridValue = 0;
                $monthlyDischargeValue = 0;
                $solisMonthlyYearlyController = new SolisMonthlyYearlyController();
                $solisMonthlyResult = $solisMonthlyYearlyController->SolisPlantMonthlyData($solisAPIBaseURL, $token, $siteID, $lastRecordDate);
                $solisMonthlyResponseData = json_decode($solisMonthlyResult);
                if ($solisMonthlyResponseData && isset($solisMonthlyResponseData->stationDataItems)) {
                    $plantDailyGenerationDataSum = $solisMonthlyResponseData->stationDataItems[0]->generationValue;
                    $plantDailyConsumptionDataSum = $solisMonthlyResponseData->stationDataItems[0]->useValue;
                    $plantDailySellDataSum = $solisMonthlyResponseData->stationDataItems[0]->gridValue;
                    $plantDailyBoughtDataSum = $solisMonthlyResponseData->stationDataItems[0]->buyValue;
                    $plantDailyChargeEnergy = $solisMonthlyResponseData->stationDataItems[0]->chargeValue;
                    $plantDailyDischargeEnegry = $solisMonthlyResponseData->stationDataItems[0]->dischargeValue;
                    $monthlyGridRatio = $solisMonthlyResponseData->stationDataItems[0]->gridRatio;
                    $monthlyChargeRatio = $solisMonthlyResponseData->stationDataItems[0]->chargeRatio;
                    $monthlyGenerationValue = $solisMonthlyResponseData->stationDataItems[0]->generationValue;
                    $monthlyGenerationRatio = $solisMonthlyResponseData->stationDataItems[0]->generationRatio;
                    $monthlyUseRatio = $solisMonthlyResponseData->stationDataItems[0]->useRatio;
                    $monthlyUseValue = $solisMonthlyResponseData->stationDataItems[0]->useValue;
                    $monthlyGridValue = $solisMonthlyResponseData->stationDataItems[0]->gridValue;
                    $monthlyDischargeValue = $solisMonthlyResponseData->stationDataItems[0]->useDischargeRatio;
                }

//                return [$plantDailyGenerationDataSum,$plantDailyConsumptionDataSum,$plantDailyGridDataSum];

//                    return [$plantDailyGenerationDataSum,$plantDailyConsumptionDataSum,$plantDailyGridDataSum,$plantDailyBoughtDataSum,$plantDailySellDataSum,$plantDailySavingDataSum];

                $monthlyProcessed['plant_id'] = $plantID;
                $monthlyProcessed['monthlyGeneration'] = $plantDailyGenerationDataSum;
                $monthlyProcessed['monthlyConsumption'] = $plantDailyConsumptionDataSum;
                $monthlyProcessed['monthlyGridPower'] = $plantDailyGridDataSum;
                $monthlyProcessed['monthlyBoughtEnergy'] = $plantDailyBoughtDataSum;
                $monthlyProcessed['monthlySellEnergy'] = $plantDailySellDataSum;
                $monthlyProcessed['monthlySaving'] = (double)$plantDailyGenerationDataSum * (double)$benchMarkPrice;
                $monthlyProcessed['monthly_charge_energy'] = $plantDailyChargeEnergy;
                $monthlyProcessed['monthly_discharge_energy'] = $plantDailyDischargeEnegry;
                $monthlyProcessed['monthly_peak_hours_discharge_energy'] = $monthlyPeakHoursDischargeEnergy;
                $monthlyProcessed['monthly_peak_hours_grid_import'] = $monthlyPeakHoursGridBuy;
                $monthlyProcessed['monthly_outage_grid_voltage'] = $monthlyOutagesData;
                $monthlyProcessed['monthly_peak_hours_consumption'] = $monthlyPeakHoursConsumption;
                $monthlyProcessed['monthly_grid_ratio'] = $monthlyGridRatio;
                $monthlyProcessed['monthly_charge_ratio'] = $monthlyChargeRatio;
                $monthlyProcessed['monthly_generation_value'] = $monthlyGenerationValue;
                $monthlyProcessed['monthly_generation_ratio'] = $monthlyGenerationRatio;
                $monthlyProcessed['monthly_use_value'] = $monthlyUseValue;
                $monthlyProcessed['monthly_use_ratio'] = $monthlyUseRatio;
                $monthlyProcessed['monthly_grid_value'] = $monthlyGridValue;
                $monthlyProcessed['monthly_discharge_ratio'] = $monthlyDischargeValue;
                $monthlyProcessed['updated_at'] = $currentTime;
//                return $monthlyProcessed;

                $monthlyProcessedPlantDetailExist = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->orderBy('created_at', 'DESC')->first();

                if ($monthlyProcessedPlantDetailExist) {
                    $monthlyProcessedPlantDetailExist['plant_id'] = $plantID;
                    $monthlyProcessedPlantDetailExist['monthlyGeneration'] = $plantDailyGenerationDataSum;
                    $monthlyProcessedPlantDetailExist['monthlyConsumption'] = $plantDailyConsumptionDataSum;
                    $monthlyProcessedPlantDetailExist['monthlyGridPower'] = $plantDailyGridDataSum;
                    $monthlyProcessedPlantDetailExist['monthlyBoughtEnergy'] = $plantDailyBoughtDataSum;
                    $monthlyProcessedPlantDetailExist['monthlySellEnergy'] = $plantDailySellDataSum;
                    $monthlyProcessedPlantDetailExist['monthlySaving'] = (double)$plantDailyGenerationDataSum * (double)$benchMarkPrice;
                    $monthlyProcessedPlantDetailExist['monthly_charge_energy'] = $plantDailyChargeEnergy;
                    $monthlyProcessedPlantDetailExist['monthly_discharge_energy'] = $plantDailyDischargeEnegry;
                    $monthlyProcessedPlantDetailExist['monthly_peak_hours_discharge_energy'] = $monthlyPeakHoursDischargeEnergy;
                    $monthlyProcessedPlantDetailExist['monthly_peak_hours_grid_import'] = $monthlyPeakHoursGridBuy;
                    $monthlyProcessedPlantDetailExist['monthly_outage_grid_voltage'] = $monthlyOutagesData;
                    $monthlyProcessedPlantDetailExist['monthly_peak_hours_consumption'] = $monthlyPeakHoursConsumption;
                    $monthlyProcessedPlantDetailExist['monthly_grid_ratio'] = $monthlyGridRatio;
                    $monthlyProcessedPlantDetailExist['monthly_charge_ratio'] = $monthlyChargeRatio;
                    $monthlyProcessedPlantDetailExist['monthly_generation_value'] = $monthlyGenerationValue;
                    $monthlyProcessedPlantDetailExist['monthly_generation_ratio'] = $monthlyGenerationRatio;
                    $monthlyProcessedPlantDetailExist['monthly_use_value'] = $monthlyUseValue;
                    $monthlyProcessedPlantDetailExist['monthly_use_ratio'] = $monthlyUseRatio;
                    $monthlyProcessedPlantDetailExist['monthly_grid_value'] = $monthlyGridValue;
                    $monthlyProcessedPlantDetailExist['monthly_discharge_ratio'] = $monthlyDischargeValue;
                    $monthlyProcessedPlantDetailExist['updated_at'] = $currentTime;
                    $monthlyProcessedPlantDetailExist->save();
//                    $monthlyProcessedPlantDetailResponse = $monthlyProcessedPlantDetailExist->fill($monthlyProcessed)->save();
                } else {

                    $monthlyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($currentTime));
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
                $yearlyPeakHoursConsumption = array_sum(array_column($monthlyDataArray, 'monthly_peak_hours_consumption'));
                $yearlyPeakHoursGridBuy = array_sum(array_column($monthlyDataArray, 'monthly_peak_hours_grid_import'));
                $yearlyPeakHoursDischargeEnergy = array_sum(array_column($monthlyDataArray, 'monthly_peak_hours_discharge_energy'));
//                $yearlyOutagesGridValue = array_sum(array_column($monthlyDataArray, 'monthly_outage_grid_voltage'));
                $plantmonthlyGenerationDataSum = 0;
                $plantmonthlyConsumptionDataSum = 0;
                $plantmonthlyGridDataSum = 0;
                $plantmonthlyBoughtDataSum = 0;
                $plantmonthlyGridDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->sum('monthlyGridPower');
                $plantmonthlySellDataSum = 0;
                $plantmonthlySavingDataSum = 0;
                $plantMonthlyChargeSum = 0;
                $plantMonthlyDishargeSum = 0;
                $yearlyGridRatio = 0;
                $yearlyChargeRatio = 0;
                $yearlyGenerationValue = 0;
                $yearlyGenerationRatio = 0;
                $yearlyUseRatio = 0;
                $yearlyUseValue = 0;
                $yearlyGridValue = 0;
                $yearlyDischargeValue = 0;
                $yearlyTimes = array();
                for ($i = 0; $i < count($plantMonthlyGenerationTableData); $i++) {
                    if ($plantMonthlyGenerationTableData[$i]['monthly_outage_grid_voltage']) {
                        $monthlyOutagesHoursData = explode(':', $plantMonthlyGenerationTableData[$i]['monthly_outage_grid_voltage']);
                        $yearlyTimes[] = $monthlyOutagesHoursData[0] . ':' . $monthlyOutagesHoursData[1] . ':00';
                    }
//                    $peakHoursData += date('H:i:s',strtotime($dailyVoltage));
                }
//                return $yearlyTimes;
//                $sum = strtotime('00:00:00');
//                $sum2=0;
//                foreach ($times as $v){
//
//                    $sum1=strtotime($v)-$sum;
//
//                    $sum2 = $sum2+$sum1;
//                }
//
//                $sum3=$sum+$sum2;
//                $monthlyOutagesData = date("H:i",strtotime($sum3));
//                return date("H:i",strtotime($sum3));
//                $yearlyOutagesGridValue = $this->AddOutagesTime($yearlyTimes);
                $totalSeconds = 0;
                foreach ($yearlyTimes as $t) {
                    $totalSeconds += $this->toSeconds($t);
                }

                $yearlyOutagesGridValue = $this->toTimeCalculation($totalSeconds);
//                return $yearlyTimes;
                $solisMonthlyYearlyController = new SolisMonthlyYearlyController();
                $solisMonthlyResult = $solisMonthlyYearlyController->SolisPlantYearlyData($solisAPIBaseURL, $token, $siteID, $lastRecordDate);
                $solisMonthlyResponseData = json_decode($solisMonthlyResult);
                if ($solisMonthlyResponseData && isset($solisMonthlyResponseData->stationDataItems)) {
                    $plantmonthlyGenerationDataSum = $solisMonthlyResponseData->stationDataItems[0]->generationValue;
                    $plantmonthlyConsumptionDataSum = $solisMonthlyResponseData->stationDataItems[0]->useValue;
                    $plantmonthlySellDataSum = $solisMonthlyResponseData->stationDataItems[0]->gridValue;
                    $plantmonthlyBoughtDataSum = $solisMonthlyResponseData->stationDataItems[0]->buyValue;
                    $plantMonthlyChargeSum = $solisMonthlyResponseData->stationDataItems[0]->chargeValue;
                    $plantMonthlyDishargeSum = $solisMonthlyResponseData->stationDataItems[0]->dischargeValue;
                    $plantmonthlySavingDataSum = (double)$plantmonthlyGenerationDataSum * (double)$benchMarkPrice;
                    $yearlyGridRatio = $solisMonthlyResponseData->stationDataItems[0]->gridRatio;
                    $yearlyChargeRatio = $solisMonthlyResponseData->stationDataItems[0]->chargeRatio;
                    $yearlyGenerationValue = $solisMonthlyResponseData->stationDataItems[0]->generationValue;
                    $yearlyGenerationRatio = $solisMonthlyResponseData->stationDataItems[0]->generationRatio;
                    $yearlyUseRatio = $solisMonthlyResponseData->stationDataItems[0]->useRatio;
                    $yearlyUseValue = $solisMonthlyResponseData->stationDataItems[0]->useValue;
                    $yearlyGridValue = $solisMonthlyResponseData->stationDataItems[0]->gridValue;
                    $yearlyDischargeValue = $solisMonthlyResponseData->stationDataItems[0]->useDischargeRatio;
                }

                $yearlyProcessed['plant_id'] = $plantID;
                $yearlyProcessed['yearlyGeneration'] = $plantmonthlyGenerationDataSum;
                $yearlyProcessed['yearlyConsumption'] = $plantmonthlyConsumptionDataSum;
                $yearlyProcessed['yearlyGridPower'] = $plantmonthlyGridDataSum;
                $yearlyProcessed['yearlyBoughtEnergy'] = $plantmonthlyBoughtDataSum;
                $yearlyProcessed['yearlySellEnergy'] = $plantmonthlySellDataSum;
                $yearlyProcessed['yearlySaving'] = $plantmonthlySavingDataSum;
                $yearlyProcessed['yearly_charge_energy'] = $plantMonthlyChargeSum;
                $yearlyProcessed['yearly_outage_grid_voltage'] = $yearlyOutagesGridValue;
                $yearlyProcessed['yearly_discharge_energy'] = $plantMonthlyDishargeSum;
                $yearlyProcessed['yearly_peak_hours_discharge_energy'] = $yearlyPeakHoursDischargeEnergy;
                $yearlyProcessed['yearly_peak_hours_grid_import'] = $yearlyPeakHoursGridBuy;
                $yearlyProcessed['yearly_peak_hours_consumption'] = $yearlyPeakHoursConsumption;
                $yearlyProcessed['yearly_grid_ratio'] = $yearlyGridRatio;
                $yearlyProcessed['yearly_charge_ratio'] = $yearlyChargeRatio;
                $yearlyProcessed['yearly_generation_ratio'] = $yearlyGenerationRatio;
                $yearlyProcessed['yearly_generation_value'] = $yearlyGenerationValue;
                $yearlyProcessed['yearly_use_ratio'] = $yearlyUseRatio;
                $yearlyProcessed['yearly_use_value'] = $yearlyUseValue;
                $yearlyProcessed['yearly_grid_value'] = $yearlyGridValue;
                $yearlyProcessed['yearly_discharge_ratio'] = $yearlyDischargeValue;
                $yearlyProcessed['updated_at'] = $currentTime;
//                return $yearlyProcessed;

                $yearlyProcessedPlantDetailExist = yearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->orderBy('created_at', 'DESC')->first();

                if ($yearlyProcessedPlantDetailExist) {
                    $yearlyProcessedPlantDetailExist['plant_id'] = $plantID;
                    $yearlyProcessedPlantDetailExist['yearlyGeneration'] = $plantmonthlyGenerationDataSum;
                    $yearlyProcessedPlantDetailExist['yearlyConsumption'] = $plantmonthlyConsumptionDataSum;
                    $yearlyProcessedPlantDetailExist['yearlyGridPower'] = $plantmonthlyGridDataSum;
                    $yearlyProcessedPlantDetailExist['yearlyBoughtEnergy'] = $plantmonthlyBoughtDataSum;
                    $yearlyProcessedPlantDetailExist['yearlySellEnergy'] = $plantmonthlySellDataSum;
                    $yearlyProcessedPlantDetailExist['yearlySaving'] = $plantmonthlySavingDataSum;
                    $yearlyProcessedPlantDetailExist['yearly_charge_energy'] = $plantMonthlyChargeSum;
                    $yearlyProcessedPlantDetailExist['yearly_outage_grid_voltage'] = $yearlyOutagesGridValue;
                    $yearlyProcessedPlantDetailExist['yearly_discharge_energy'] = $plantMonthlyDishargeSum;
                    $yearlyProcessedPlantDetailExist['yearly_peak_hours_discharge_energy'] = $yearlyPeakHoursDischargeEnergy;
                    $yearlyProcessedPlantDetailExist['yearly_peak_hours_grid_import'] = $yearlyPeakHoursGridBuy;
                    $yearlyProcessedPlantDetailExist['yearly_peak_hours_consumption'] = $yearlyPeakHoursConsumption;
                    $yearlyProcessedPlantDetailExist['yearly_grid_ratio'] = $yearlyGridRatio;
                    $yearlyProcessedPlantDetailExist['yearly_charge_ratio'] = $yearlyChargeRatio;
                    $yearlyProcessedPlantDetailExist['yearly_generation_ratio'] = $yearlyGenerationRatio;
                    $yearlyProcessedPlantDetailExist['yearly_generation_value'] = $yearlyGenerationValue;
                    $yearlyProcessedPlantDetailExist['yearly_use_ratio'] = $yearlyUseRatio;
                    $yearlyProcessedPlantDetailExist['yearly_use_value'] = $yearlyUseValue;
                    $yearlyProcessedPlantDetailExist['yearly_grid_value'] = $yearlyGridValue;
                    $yearlyProcessedPlantDetailExist['yearly_discharge_ratio'] = $yearlyDischargeValue;
                    $yearlyProcessedPlantDetailExist['updated_at'] = $currentTime;
                    $yearlyProcessedPlantDetailExist->save();
//                    $yearlyProcessedPlantDetailResponse = $yearlyProcessedPlantDetailExist->fill($yearlyProcessed)->save();
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
            $stationBattery = StationBattery::where('plant_id', $plantID)->orderBy('collect_time', 'DESC')->first();
            if ($stationBattery) {
                $totalChargeEnergy = $stationBattery->total_charge_energy;
                $totalDischargeEnergy = $stationBattery->total_discharge_energy;
            } else {
                $totalChargeEnergy = 0;
                $totalDischargeEnergy = 0;
            }
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
            $totalProcessed['plant_total_charge_energy'] = $totalChargeEnergy;
            $totalProcessed['plant_total_discharge_energy'] = $totalDischargeEnergy;
            $totalProcessed['updated_at'] = $currentTime;

            $totalProcessedPlantDetailExist = TotalProcessedPlantDetail::where('plant_id', $plantID)->first();

            if ($totalProcessedPlantDetailExist) {

                $totalProcessedPlantDetailResponse = $totalProcessedPlantDetailExist->fill($totalProcessed)->save();
            } else {

                $totalProcessed['created_at'] = $currentTime;
                $totalProcessedPlantDetailResponse = TotalProcessedPlantDetail::create($totalProcessed);
            }
//            }
        }

        $this->plantStatusUpdate();
        print_r('Crone Job End Time');
        print_r(date("Y-m-d H:i:s"));
        print_r("\n");
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

    public function getHistoricalData($solisAPIBaseURL, $smartInverter, $lastRecordDate, $token)
    {
        // return $lastRecordDate;

//		$collectTimeData= $collectTime;
//        try {

        $fiveMinutesDataArray = [];
        // $siteSmartInverterData = [
        //     "devIds" => $smartInverter,
        //     "devTypeId" => 1,
        //     "collectTime" => $collectTime
        // ];
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
        $inverterData = $siteSmartInverterResponse;
        // return $siteSmartInverterResponseData;
        $dataArray = [];
        $todayDate = date('Y-m-d');
        $lastInsertedRecordDate = $lastRecordDate;
        if (isset($siteSmartInverterResponseData->paramDataList)) {
            if (count($siteSmartInverterResponseData->paramDataList) == 0) {

                $lastRecordDateConvert = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordDate)));
//                $collectTimeData = strtotime(date('Y-m-d H:i:s.u', strtotime('+5 hours', strtotime($lastRecordDate)))) . '000';
                // return [$lastRecordDate,$collectTimeData];

                if (($lastRecordDateConvert <= $todayDate)) {
//                   $dataArray = $lastRecordDateConvert;
//                    array_push($dataArray,$todayDate);
                    $this->cronJobCollectTime = $lastRecordDateConvert;
                    sleep(30);
                    $inverterData = self::getHistoricalData($solisAPIBaseURL, $smartInverter, $lastRecordDateConvert, $token);
                }
            }
        }
//        array_push($fiveMinutesDataArray, $siteSmartInverterResponseData);
        $collect_time = '';

        return $inverterData;
//            return json_encode($siteSmartInverterResponseData);
//        }
//        catch (\Exception $e)
//        {
//            sleep(30);
//            $siteSmartInverterResponseData = self::getFiveMinutesData($huaweiAPIBaseURL, $smartInverter, $collectTimeData, $lastRecordDate, $tokenSessionData, $tokenSessionData1);
//            return json_encode($siteSmartInverterResponseData);
//        }
//        return json_encode($siteSmartInverterResponseData);

    }

    function AddOutagesTime($times)
    {
        $hours = '';
        $minutes = '';
        date_default_timezone_set('Asia/Karachi');
        $sum = strtotime('00:00:00');
        $sum1 = 0;
        foreach ($times as $v) {
            $sum1 += strtotime($v) - $sum;
        }
        $hours = $sum1 / 3600;
        $minutes = ($hours - floor($hours)) * 60;

        return floor($hours) . ':' . round($minutes);
    }

    function toTimeCalculation($seconds)
    {
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        return $hours . ':' . $minutes . ':' . $seconds;
    }

    function toSeconds($time)
    {
        $parts = explode(':', $time);
        return 3600 * $parts[0] + 60 * $parts[1] + $parts[2];
    }
}
