<?php

namespace App\Http\Controllers\HardwareAPIData;

use App\Http\Controllers\Controller;
use App\Http\Models\CronJobTime;
use App\Http\Models\InverterStatusCode;
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

//use App\Http\Models\InverterEnergyLog;

class SolisController extends Controller
{
    public $cronJobCollectTime;

//    public $arrayDifference = [];
    public function solis($globalGenerationLogMaxID = null, $globalProcessedLogMaxID = null, $globalInverterDetailMaxID = null)
    {

        date_default_timezone_set('Asia/Karachi');
        $currentTime = date('Y-m-d H:i:s');
        print_r('Crone Job Start Time');
        print_r(date("Y-m-d H:i:s"));
        print_r("\n");
        $cronJobTime = new CronJobTime();
        $cronJobTime->start_time = $currentTime;
        $cronJobTime->status = "in-progress";
        $cronJobTime->type = 'Solis-Hybrid';
        $cronJobTime->save();
        $solisAppData = Setting::where('perimeter', 'solis_api_app_id')->first();

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

        $allPlantsData = Plant::where('meter_type', 'Solis')->whereBetween('id',[1,500])->inRandomOrder()->get();
//        $allPlantsData = Plant::where('meter_type', 'Solis')->whereIn('id',[514])->get();
        $dataArrayValues = array();

        if ($allPlantsData) {

            foreach ($allPlantsData as $key => $plant) {

                $siteAllInverterLogStartTime = array();
                $plantID = $plant->id;

                $plantSites = PlantSite::where('plant_id', $plantID)->get();

                if ($plantSites) {


                    foreach ($plantSites as $site) {
                        if ($plant->system_type == 4 || $plant->system_type == 2) {
                            $hybridController = new HybridController();
                            $result = $hybridController->hybrid($plant ,$plant->id, $token, $solisAPIBaseURL, $processedMaxCronJobID, $plant->data_collect_date, $plant->plant_has_grid_meter, $currentTime, $envReductionValue, $plant->benchmark_price, $generationLogMaxCronJobID);
//                            return $result;

                        }
                        else {
                            $benchMarkPrice = $plant->benchmark_price;
                            $siteSmartInverterArray = array();
                            $siteSmartInverterLogStartTime = array();

                            $siteID = $site->site_id;
                            $alertController = new SolisAlertsController();

                            $alertData = $alertController->AlarmAndFault($token, $plantID, $siteID);

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

                                }


                                if (in_array(1, $inverterStatusArray)) {

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

                                if ($inverterStatusCode) {
                                    $status = $inverterStatusCode->description;
                                }

                                //SITE STATUS UPDATE DATA

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
//                                InverterEnergyLog::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])->whereDate('collect_time', date('Y-m-d'))->delete();

                                $lastRecordTimeStamp = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->exists() ? InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter])->orderBy('collect_time', 'DESC')->first()->collect_time : null;

                                if (isset($lastRecordTimeStamp) && $lastRecordTimeStamp) {

                                    if (strtotime(date('Y-m-d', strtotime($lastRecordTimeStamp))) == strtotime(date('Y-m-d'))) {

                                        $lastRecordDate = date('Y-m-d', strtotime($lastRecordTimeStamp));
                                        $this->cronJobCollectTime = $lastRecordDate;
                                    } else {

                                        $lastRecordDate = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordTimeStamp)));
                                        $this->cronJobCollectTime = $lastRecordDate;
                                    }
                                } else {

                                    $lastRecordDate = $plant->data_collect_date;
                                    $this->cronJobCollectTime = $lastRecordDate;
                                }


                                $siteSmartInverterLogStartTime[] = strtotime($lastRecordDate);
                                $siteAllInverterLogStartTime[] = strtotime($lastRecordDate);


                                while (strtotime($lastRecordDate) != strtotime(date('Y-m-d', strtotime("+1 days")))) {

                                    $solisHistoricalData = $this->getHistoricalData($solisAPIBaseURL, $smartInverter, $lastRecordDate, $token);
                                    $siteSmartInverterLogStartTime[] = strtotime($this->cronJobCollectTime);
                                    $siteAllInverterLogStartTime[] = strtotime($this->cronJobCollectTime);


                                    $collectTime = date('Y-m-d', strtotime($this->cronJobCollectTime));
                                    $dailyGenerationData = 0;

                                    $siteSmartInverterResponseData = json_decode($solisHistoricalData);

                                    if ($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->paramDataList)) {

                                        $siteSmartInverterFinalData = $siteSmartInverterResponseData->paramDataList;

                                        if ($siteSmartInverterFinalData) {
                                            $dataArrayDetails = [];

                                            foreach ($siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {
                                                $responseData = isset($smartInverterFinalData->dataList) ? $smartInverterFinalData->dataList : [];
                                                $invertDetailExist = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter, 'collect_time' => date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))])->orderBy('collect_time', 'desc')->first();

                                                if ($responseData) {


                                                    if (empty($invertDetailExist) || (date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))) > ($invertDetailExist['collect_time'])) {
                                                        $inverterDetailLog = array();

                                                        $inverterDetailLog['plant_id'] = $plantID;
                                                        $inverterDetailLog['siteId'] = $siteID;
                                                        $inverterDetailLog['dv_inverter'] = $smartInverter;
                                                        $keys = array_keys(array_column($responseData, 'key'), 'APo_t1');
                                                        if ($keys) {
                                                            $inverterDetailLog['inverterPower'] = ($responseData[$keys[0]]->value / 1000);
                                                        } else {
                                                            $inverterDetailLog['inverterPower'] = 0;
                                                        }
                                                        $dailyGen = array_keys(array_column($responseData, 'key'), 'Etdy_ge1');
                                                        if ($dailyGen) {
                                                            $inverterDetailLog['daily_generation'] = $responseData[$dailyGen[0]]->value;
                                                        } else {
                                                            $inverterDetailLog['daily_generation'] = 0;
                                                        }

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
                                                        $invertTemp = array_keys(array_column($responseData, 'key'), 'T_in1');
                                                        if ($invertTemp) {
                                                            $inverterDetailLog['inverterTemperature'] = $responseData[$invertTemp[0]]->value;
                                                        } else {
                                                            $inverterDetailLog['inverterTemperature'] = 0;
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

                                                            }

                                                        }

                                                    }
                                                    if ($plant->plant_has_grid_meter == 'Y') {
                                                        $todayLastTime = InverterEnergyLog::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])->whereDate('collect_time', $collectTime)->orderBy('collect_time', 'desc')->first();
                                                        if (empty($todayLastTime) || date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime)) > ($todayLastTime['collect_time'])) {

                                                            $totalGridPowerData = array_keys(array_column($responseData, 'key'), 'PG_Pt1');
                                                            if ($totalGridPowerData) {
                                                                $gridPower = $responseData[$totalGridPowerData[0]]->value;
                                                            } else {
                                                                $gridPower = 0;
                                                            }
                                                            $totalGridDailyEnergyData = array_keys(array_column($responseData, 'key'), 'Etdy_pu1');
                                                            if ($totalGridDailyEnergyData) {
                                                                $gridImportEnergy = $responseData[$totalGridDailyEnergyData[0]]->value;
                                                            } else {
                                                                $gridImportEnergy = 0;
                                                            }
                                                            $totalGridDailyFeedData = array_keys(array_column($responseData, 'key'), 't_gc_tdy1');
                                                            if ($totalGridDailyFeedData) {
                                                                $gridExportEnergy = $responseData[$totalGridDailyFeedData[0]]->value;
                                                            } else {
                                                                $gridExportEnergy = 0;
                                                            }
                                                            if ($gridPower) {
                                                                $gridPower = (($gridPower / 1000) * (-1));
                                                            } else {

                                                                $gridPower = 0;
                                                            }


                                                            $inverterEnergyLog['plant_id'] = $plantID;
                                                            $inverterEnergyLog['site_id'] = $siteID;
                                                            $inverterEnergyLog['dv_inverter'] = $smartInverter;
                                                            $inverterEnergyLog['grid_power'] = $gridPower;
                                                            $inverterEnergyLog['import_energy'] = $gridImportEnergy;
                                                            $inverterEnergyLog['export_energy'] = $gridExportEnergy;
                                                            $inverterEnergyLog['cron_job_id'] = $processedMaxCronJobID;
                                                            $collectTime = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
                                                            $inverterEnergyLog['collect_time'] = $collectTime;

                                                            $inverterEnergyLogResponse = InverterEnergyLog::create($inverterEnergyLog);
                                                        }
                                                    }

                                                }


                                            }

                                            print_r('Invert Detail Loop Time');
                                            print_r(date("Y-m-d H:i:s"));
                                            print_r("\n");
                                        }
                                    }

                                    //DAILY INVERTER DATA
                                    $dailyInvData = array();

                                    $dailyGenerationData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->exists() ? InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $lastRecordDate)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;

                                    $dailyInvData['plant_id'] = $plantID;
                                    $dailyInvData['siteId'] = $siteID;
                                    $dailyInvData['dv_inverter'] = $smartInverter;
                                    $dailyInvData['updated_at'] = $currentTime;
                                    $dailyInvData['daily_generation'] = $dailyGenerationData;

                                    $DailyInvDataExist = DailyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('created_at', $this->cronJobCollectTime)->first();

                                    if ($DailyInvDataExist) {

                                        $dailyInvDataResponse = $DailyInvDataExist->fill($dailyInvData)->save();
                                    } else {

                                        $dailyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($this->cronJobCollectTime));
                                        $dailyInvDataResponse = DailyInverterDetail::create($dailyInvData);
                                    }

                                    break;
                                }

                                $logYear = date('Y', strtotime($this->cronJobCollectTime));
                                $logMonth = date('m', strtotime($this->cronJobCollectTime));

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

                                $yearlyInvData['yearly_generation'] = isset($monthlyInverterData) ? $monthlyInverterData : 0;

                                $yearlyInvDataExist = YearlyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereYear('created_at', $logYear)->first();

                                if ($yearlyInvDataExist) {

                                    $yearlyInvDataResponse = $yearlyInvDataExist->fill((array)$yearlyInvData)->save();
                                } else {

                                    $yearlyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($this->cronJobCollectTime));
                                    $yearlyInvDataResponse = YearlyInverterDetail::create((array)$yearlyInvData);
                                }
                            }

                            //SMART INVERTER GENERATION LOG DATA

                            if (!(empty($siteSmartInverterLogStartTime))) {

                                $minTimeSmartInverter = date('Y-m-d', min($siteSmartInverterLogStartTime));

                                $smartInverterStartTimeData = InverterDetail::select(DB::raw('SUM(inverterPower) as current_generation, SUM(daily_generation) as totalEnergy'), 'collect_time')->where(['plant_id' => $plantID, 'siteId' => $siteID])->whereBetween('collect_time', [date($minTimeSmartInverter . ' 00:00:00'), date($minTimeSmartInverter . ' 23:59:59')])->groupBy('collect_time')->get();

                                foreach ($smartInverterStartTimeData as $generationLogKey => $generationLogData) {

                                    if (GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $generationLogData->collect_time)->exists()) {

                                        $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $generationLogData->collect_time)->first();

                                        $generationData->current_generation = $generationLogData->current_generation;
                                        $generationData->totalEnergy = $generationLogData->totalEnergy;
                                        $generationData->save();
                                    } else {

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
                            if (!(empty($siteAllInverterLogStartTime))) {

                                $minTimeGridInverter = date('Y-m-d', min($siteAllInverterLogStartTime));
                                // $minTimeGridInverter = date('Y-m-d');

                                $gridInverterStartTimeData = InverterEnergyLog::select(DB::raw('SUM(grid_power) as grid_power'), 'collect_time')->where(['plant_id' => $plantID, 'site_id' => $siteID])->whereBetween('collect_time', [date($minTimeGridInverter . ' 00:00:00'), date($minTimeGridInverter . ' 23:59:59')])->groupBy('collect_time')->get();

                                foreach ($gridInverterStartTimeData as $gridLogKey => $gridLogData) {

                                    if (GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $gridLogData->collect_time)->exists()) {

                                        $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $gridLogData->collect_time)->first();

                                        $generationData->current_consumption = ($generationData->current_generation + $gridLogData->grid_power) > 0 ? ($generationData->current_generation + $gridLogData->grid_power) : 0;
                                        $generationData->current_grid = ($gridLogData->grid_power);
                                        $generationData->save();
                                    } else {

                                        $generationLog['plant_id'] = $plantID;
                                        $generationLog['siteId'] = $siteID;
                                        $generationLog['current_generation'] = 0;
                                        $generationLog['comm_failed'] = 0;
                                        $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
                                        $generationLog['current_consumption'] = $gridLogData->grid_power > 0 ? $gridLogData->grid_power : 0;
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
                    }
                }

//                if (!(empty($siteAllInverterLogStartTime))) {
//
//                    $minTimeInverter = date('Y-m-d', min($siteAllInverterLogStartTime));
//
//                    $maxTimeInverter = date('Y-m-d', max($siteAllInverterLogStartTime));
//
//                    $generationLogInverterStartTimeData = GenerationLog::select(DB::raw('SUM(current_generation) as current_generation, SUM(current_consumption) as current_consumption, SUM(current_grid) as current_grid, SUM(current_irradiance) as current_irradiance, SUM(totalEnergy) as totalEnergy'), 'collect_time')->where(['plant_id' => $plantID])->whereBetween('collect_time', [date($minTimeInverter . ' 00:00:00'), date($maxTimeInverter . ' 23:59:59')])->groupBy('collect_time')->get();
//
//                    foreach ($generationLogInverterStartTimeData as $key45 => $processedData) {
//
//                        $processedCurrentDataExist = ProcessedCurrentVariable::where(['plant_id' => $plantID])->where('collect_time', $processedData->collect_time)->first();
//                        if ($processedCurrentDataExist) {
//
//                            $processedCurrentData['plant_id'] = $plantID;
//                            $processedCurrentData['current_generation'] = $processedData->current_generation;
//                            $processedCurrentData['current_consumption'] = $processedData->current_consumption;
//                            $processedCurrentData['current_grid'] = abs($processedData->current_grid);
//                            $processedCurrentData['grid_type'] = $processedData->current_grid >= 0 ? '+ve' : '-ve';
//                            $processedCurrentData['current_irradiance'] = $processedData->current_irradiance;
//                            $processedCurrentData['totalEnergy'] = $processedData->totalEnergy ? $processedData->totalEnergy : 0;
//                            $processedCurrentData['current_saving'] = $processedData->current_generation * (double)$plant->benchmark_price;
//                            $processedCurrentData['processed_cron_job_id'] = $processedMaxCronJobID;
//                            $processedCurrentData['collect_time'] = $processedData->collect_time;
//                            $processedCurrentData['created_at'] = $currentTime;
//                            $processedCurrentData['updated_at'] = $currentTime;
//
//                            $processedCurrentDataResponse = $processedCurrentDataExist->fill($processedCurrentData)->save();
//                        } else {
//
//                            $processedCurrentData['plant_id'] = $plantID;
//                            $processedCurrentData['current_generation'] = $processedData->current_generation;
//                            $processedCurrentData['current_consumption'] = $processedData->current_consumption;
//                            $processedCurrentData['current_grid'] = abs($processedData->current_grid);
//                            $processedCurrentData['grid_type'] = $processedData->current_grid >= 0 ? '+ve' : '-ve';
//                            $processedCurrentData['current_irradiance'] = $processedData->current_irradiance;
//                            $processedCurrentData['totalEnergy'] = $processedData->totalEnergy ? $processedData->totalEnergy : 0;
//                            $processedCurrentData['current_saving'] = $processedData->current_generation * (double)$plant->benchmark_price;
//                            $processedCurrentData['processed_cron_job_id'] = $processedMaxCronJobID;
//                            $processedCurrentData['collect_time'] = $processedData->collect_time;
//                            $processedCurrentData['created_at'] = $currentTime;
//                            $processedCurrentData['updated_at'] = $currentTime;
//
//                            $processedCurrentDataResponse = ProcessedCurrentVariable::create($processedCurrentData);
//                        }
//                    }
//
//
//                    while ($minTimeInverter != date('Y-m-d', strtotime("+1 day", strtotime($maxTimeInverter)))) {
//
//                        $plantDataDateToday = $minTimeInverter;
//
//                        $plantDailyTotalBuyEnergy = 0;
//
//                        $plantDailyTotalSellEnergy = 0;
//
//                        $plantInverterListData = SiteInverterDetail::where('plant_id', $plantID)->get();
//
//                        foreach ($plantInverterListData as $invListData) {
//
//                            $inverterEnergyData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $minTimeInverter)->orderBy('collect_time', 'DESC')->whereNotNull('import_energy')->first();
//                            if ($inverterEnergyData) {
//                                $inverterEnergyTodayImportData = $inverterEnergyData->import_energy;
//                            } else {
//                                $inverterEnergyTodayImportData = 0;
//                            }
//
//                            $inverterEnergyExportData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $minTimeInverter)->orderBy('collect_time', 'DESC')->whereNotNull('export_energy')->first();
//                            if ($inverterEnergyExportData) {
//                                $inverterEnergyTodayExportData = $inverterEnergyExportData->export_energy;
//                            } else {
//                                $inverterEnergyTodayExportData = 0;
//                            }
//
//                            $plantDailyTotalBuyEnergy += (double)$inverterEnergyTodayImportData;
//                            $plantDailyTotalSellEnergy += (double)$inverterEnergyTodayExportData;
//
//                        }
//
//
//                        $plantDailyTotalGeneration = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $minTimeInverter)->sum('daily_generation');
//
//                        //PLANT DAILY DATA
//                        $dailyProcessed['plant_id'] = $plantID;
//                        $dailyProcessed['dailyGeneration'] = $plantDailyTotalGeneration;
//                        $dailyProcessed['dailyGridPower'] = $plantDailyTotalBuyEnergy > $plantDailyTotalSellEnergy ? $plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy : $plantDailyTotalSellEnergy - $plantDailyTotalBuyEnergy;
//                        $dailyProcessed['dailyBoughtEnergy'] = $plantDailyTotalBuyEnergy;
//                        $dailyProcessed['dailySellEnergy'] = $plantDailyTotalSellEnergy;
//                        $dailyProcessed['dailyMaxSolarPower'] = 0;
//                        $dailyProcessed['dailyConsumption'] = $plantDailyTotalGeneration;
//                        $dailyProcessed['dailySaving'] = (double)$plantDailyTotalGeneration * (double)$plant->benchmark_price;
//                        $dailyProcessed['dailyIrradiance'] = 0;
//                        $dailyProcessed['updated_at'] = $minTimeInverter;
//
//                        $dailyProcessedPlantDetailExist = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $minTimeInverter)->orderBy('created_at', 'DESC')->first();
//
//                        if ($dailyProcessedPlantDetailExist) {
//
//                            $dailyProcessedPlantDetailInsertionResponce = $dailyProcessedPlantDetailExist->fill($dailyProcessed)->save();
//                        } else {
//
//                            $dailyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
//                            $dailyProcessedPlantDetailInsertionResponce = DailyProcessedPlantDetail::create($dailyProcessed);
//                        }
//
//                        $minTimeInverter = date('Y-m-d', strtotime("+1 day", strtotime($minTimeInverter)));
//                    }
//
//                    $logYear = date('Y', strtotime($minTimeInverter));
//                    $logMonth = date('m', strtotime($minTimeInverter));
//
//
//                    $plantGenerationTableData = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->get();
//                    $dataArray = json_decode(json_encode($plantGenerationTableData), true);
//
//                    $plantDailyGridDataSum = array_sum(array_column($dataArray, 'dailyGridPower'));
//                    $plantDailySavingDataSum = array_sum(array_column($dataArray, 'dailySaving'));
//
//                    $solisMonthlyYearlyController = new SolisMonthlyYearlyController();
//                    $solisMonthlyResult = $solisMonthlyYearlyController->SolisPlantMonthlyData($solisAPIBaseURL, $token, $siteID, $lastRecordDate);
//                    $solisMonthlyResponseData = json_decode($solisMonthlyResult);
//                    if ($solisMonthlyResponseData && isset($solisMonthlyResponseData->stationDataItems)) {
//                        $plantDailyGenerationDataSum = $solisMonthlyResponseData->stationDataItems[0]->generationValue;
//                        $plantDailyConsumptionDataSum = $solisMonthlyResponseData->stationDataItems[0]->useValue;
//                        $plantDailySellDataSum = $solisMonthlyResponseData->stationDataItems[0]->gridValue;
//                        $plantDailyBoughtDataSum = $solisMonthlyResponseData->stationDataItems[0]->buyValue;
//                    }
//
//                    $monthlyProcessed['plant_id'] = $plantID;
//                    $monthlyProcessed['monthlyGeneration'] = $plantDailyGenerationDataSum;
//                    $monthlyProcessed['monthlyConsumption'] = $plantDailyConsumptionDataSum;
//                    $monthlyProcessed['monthlyGridPower'] = $plantDailyGridDataSum;
//                    $monthlyProcessed['monthlyBoughtEnergy'] = $plantDailyBoughtDataSum;
//                    $monthlyProcessed['monthlySellEnergy'] = $plantDailySellDataSum;
//                    $monthlyProcessed['monthlySaving'] = $plantDailySavingDataSum;
//                    $monthlyProcessed['updated_at'] = $currentTime;
//
//
//                    $monthlyProcessedPlantDetailExist = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->orderBy('created_at', 'DESC')->first();
//
//                    if ($monthlyProcessedPlantDetailExist) {
//
//                        $monthlyProcessedPlantDetailResponse = $monthlyProcessedPlantDetailExist->fill($monthlyProcessed)->save();
//                    } else {
//
//                        $monthlyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
//                        $monthlyProcessedPlantDetailResponse = MonthlyProcessedPlantDetail::create($monthlyProcessed);
//                    }
//
//                    $plantMonthlyGenerationTableData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->get();
//                    $monthlyDataArray = json_decode(json_encode($plantMonthlyGenerationTableData), true);
//
//                    $plantmonthlyGridDataSum = array_sum(array_column($monthlyDataArray, 'monthlyGridPower'));
//
//                    $solisMonthlyYearlyController = new SolisMonthlyYearlyController();
//                    $solisMonthlyResult = $solisMonthlyYearlyController->SolisPlantYearlyData($solisAPIBaseURL, $token, $siteID, $lastRecordDate);
//                    $solisMonthlyResponseData = json_decode($solisMonthlyResult);
//                    if ($solisMonthlyResponseData && isset($solisMonthlyResponseData->stationDataItems)) {
//                        $plantmonthlyGenerationDataSum = $solisMonthlyResponseData->stationDataItems[0]->generationValue;
//                        $plantmonthlyConsumptionDataSum = $solisMonthlyResponseData->stationDataItems[0]->useValue;
//                        $plantmonthlySellDataSum = $solisMonthlyResponseData->stationDataItems[0]->gridValue;
//                        $plantmonthlyBoughtDataSum = $solisMonthlyResponseData->stationDataItems[0]->buyValue;
//                        $plantmonthlySavingDataSum = (double)$plantmonthlyGenerationDataSum * (double)$benchMarkPrice;
//                    }
//
//                    $yearlyProcessed['plant_id'] = $plantID;
//                    $yearlyProcessed['yearlyGeneration'] = $plantmonthlyGenerationDataSum;
//                    $yearlyProcessed['yearlyConsumption'] = $plantmonthlyConsumptionDataSum;
//                    $yearlyProcessed['yearlyGridPower'] = $plantmonthlyGridDataSum;
//                    $yearlyProcessed['yearlyBoughtEnergy'] = $plantmonthlyBoughtDataSum;
//                    $yearlyProcessed['yearlySellEnergy'] = $plantmonthlySellDataSum;
//                    $yearlyProcessed['yearlySaving'] = $plantmonthlySavingDataSum;
//                    $yearlyProcessed['updated_at'] = $currentTime;
//
//                    $yearlyProcessedPlantDetailExist = yearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->orderBy('created_at', 'DESC')->first();
//
//                    if ($yearlyProcessedPlantDetailExist) {
//
//                        $yearlyProcessedPlantDetailResponse = $yearlyProcessedPlantDetailExist->fill($yearlyProcessed)->save();
//                    } else {
//
//                        $yearlyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($minTimeInverter));
//                        $yearlyProcessedPlantDetailResponse = yearlyProcessedPlantDetail::create($yearlyProcessed);
//                    }
//                }
//
//                //PLANT Total DATA
//                $plantyearlyCurrentPowerDataSum = ProcessedCurrentVariable::where('plant_id', $plantID)->exists() ? ProcessedCurrentVariable::where('plant_id', $plantID)->orderBy('collect_time', 'DESC')->first()->current_generation : 0;
//
//                $plantYearlyGenerationTableData = YearlyProcessedPlantDetail::where('plant_id', $plantID)->get();
//                $yearlyDataArray = json_decode(json_encode($plantYearlyGenerationTableData), true);
//                $plantyearlyGenerationDataSum = array_sum(array_column($yearlyDataArray, 'yearlyGeneration'));
//                $plantyearlyConsumptionDataSum = array_sum(array_column($yearlyDataArray, 'yearlyConsumption'));
//                $plantyearlyGridDataSum = array_sum(array_column($yearlyDataArray, 'yearlyGridPower'));
//                $plantyearlyBoughtDataSum = array_sum(array_column($yearlyDataArray, 'yearlyBoughtEnergy'));
//                $plantyearlySellDataSum = array_sum(array_column($yearlyDataArray, 'yearlySellEnergy'));
//                $plantyearlySavingDataSum = array_sum(array_column($yearlyDataArray, 'yearlySaving'));
//                $plantyearlyIrradianceDataSum = array_sum(array_column($yearlyDataArray, 'yearly_irradiance'));
//
//                $totalProcessed['plant_id'] = $plantID;
//                $totalProcessed['plant_total_current_power'] = $plantyearlyCurrentPowerDataSum;
//                $totalProcessed['plant_total_generation'] = $plantyearlyGenerationDataSum;
//                $totalProcessed['plant_total_consumption'] = $plantyearlyConsumptionDataSum;
//                $totalProcessed['plant_total_grid'] = $plantyearlyGridDataSum;
//                $totalProcessed['plant_total_buy_energy'] = $plantyearlyBoughtDataSum;
//                $totalProcessed['plant_total_sell_energy'] = $plantyearlySellDataSum;
//                $totalProcessed['plant_total_saving'] = $plantyearlySavingDataSum;
//                $totalProcessed['plant_total_reduction'] = $plantyearlyGenerationDataSum * $envReductionValue;
//                $totalProcessed['plant_total_irradiance'] = $plantyearlyIrradianceDataSum;
//                $totalProcessed['updated_at'] = $currentTime;
//
//                $totalProcessedPlantDetailExist = TotalProcessedPlantDetail::where('plant_id', $plantID)->first();
//
//                if ($totalProcessedPlantDetailExist) {
//
//                    $totalProcessedPlantDetailResponse = $totalProcessedPlantDetailExist->fill($totalProcessed)->save();
//                } else {
//
//                    $totalProcessed['created_at'] = $currentTime;
//                    $totalProcessedPlantDetailResponse = TotalProcessedPlantDetail::create($totalProcessed);
//                }
            }
        }

        $this->plantStatusUpdate();
        $cronJobTime = CronJobTime::where('id',$cronJobTime->id)->first();
        $cronJobTime->end_time = date('Y-m-d H:i:s');
        $cronJobTime->status = "completed";
        $cronJobTime->save();
        print_r('Crone Job End Time');
        print_r(date("Y-m-d H:i:s"));
        print_r("\n");
    }

    public function getOrgToken($solisAPIBaseURL, $appID, $appKey, $userAccount, $userPassword, $orgId)
    {

        $curl = curl_init();

        $userCredentials = [

            "appSecret" => $appKey,
            "username" => $userAccount,
            "password" => $userPassword,
            "orgId" => (int)$orgId
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
//        return $response;
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


    public function getPlantList($solisAPIBaseURL, $token, $pageIndex)
    {

        $curl = curl_init();

        $getpLantsApiBody = [
            "page" => $pageIndex,
            "size" => 100,
        ];

        curl_setopt_array($curl, array(

            CURLOPT_URL => $solisAPIBaseURL . '/station/v1.0/list',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($getpLantsApiBody),
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

    public function getHistoricalData($solisAPIBaseURL, $smartInverter, $lastRecordDate, $token)
    {

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

                if (($lastRecordDateConvert <= $todayDate)) {

                    print_r("\n");
                    print_r('historic data');
                    $this->cronJobCollectTime = $lastRecordDateConvert;
                    sleep(30);
                    $inverterData = self::getHistoricalData($solisAPIBaseURL, $smartInverter, $lastRecordDateConvert, $token);
                }
            }
        }

        $collect_time = '';

        return $inverterData;

    }
}
