<?php

namespace App\Http\Controllers\HardwareAPIData;

use App\Http\Controllers\Controller;
use App\Http\Models\InverterDetailHistory;
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


class HybridRealTimeController extends Controller
{
    public $cronJobCollectTime;

    public function hybrid($plant, $plantId, $token, $solisAPIBaseURL, $processedMaxCronJobID, $dataCollectDate, $plantHasGridMeter, $currentTime, $envReductionValue, $benchMarkPrice, $generationLogMaxCronJobID)
    {

//        return [$plantId, $token, $solisAPIBaseURL, $processedMaxCronJobID, $dataCollectDate, $plantHasGridMeter, $currentTime, $envReductionValue, $benchMarkPrice, $generationLogMaxCronJobID];
        date_default_timezone_set('Asia/Karachi');
        if ($plantId) {

            $siteAllInverterLogStartTime = array();
            $plantID = $plantId;
            $plantData = Plant::findOrFail($plantID);

            $plantSites = PlantSite::where('plant_id', $plantID)->get();

            if ($plantSites) {

                foreach ($plantSites as $site) {


                    $siteSmartInverterArray = array();
                    $siteSmartInverterLogStartTime = array();

                    $siteID = $site->site_id;

//                    $alertController = new SolisAlertsController();
//                    $alertData = $alertController->AlarmAndFault($token, $plantID, $siteID);

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

                            if(isset($invertDataList->deviceState)){
                                array_push($inverterStatusArray, $invertDataList->deviceState);
                            }

                        }
                        $updateSiteStatusArray['online_status'] = 'Y';


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
                        if (in_array(1, $inverterStatusArray)) {

                            $updateSiteStatusArray['online_status'] = 'Y';
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

                    $lastRecordTimeStamp = $plantData->last_cron_job_date;
                    if (isset($lastRecordTimeStamp) && $lastRecordTimeStamp) {

                        if (strtotime(date('Y-m-d', strtotime($lastRecordTimeStamp))) == strtotime(date('Y-m-d'))) {

                            $lastRecordDate = date('Y-m-d', strtotime($lastRecordTimeStamp));
                            $this->cronJobCollectTime = $lastRecordDate;

                        } else {

                            $lastRecordDate = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordTimeStamp)));
                            $this->cronJobCollectTime = $lastRecordDate;

                        }

                    } else {

                        $lastRecordDate = $plantData->data_collect_date;
                        $this->cronJobCollectTime = $lastRecordDate;

                    }

                    //INVERTER LOG
                    foreach ($siteSmartInverterArray as $smartKey => $smartInverter) {
                        $inverterSerialNo = InverterSerialNo::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter])->first();
                        if ($inverterSerialNo) {
                            $inverterSerialNo->status = $status;
                            $inverterSerialNo->save();
                        }

                        $siteSmartInverterLogStartTime[] = strtotime($lastRecordDate);
                        $siteAllInverterLogStartTime[] = strtotime($lastRecordDate);

                        while (strtotime($lastRecordDate) != strtotime(date('Y-m-d', strtotime("+1 days")))) {

                            $collectTime = date('Y-m-d', strtotime($lastRecordDate));
                            $dailyGenerationData = 0;

                            $solisHistoricalData = $this->getHistoricalData($solisAPIBaseURL, $smartInverter, $lastRecordDate, $token);

                            $siteSmartInverterLogStartTime[] = strtotime($this->cronJobCollectTime);
                            $siteAllInverterLogStartTime[] = strtotime($this->cronJobCollectTime);
                            $siteSmartInverterResponseData = json_decode($solisHistoricalData);


                            if ($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->paramDataList)) {

                                $siteSmartInverterFinalData = $siteSmartInverterResponseData->paramDataList;
                                $siteSmartInverterFinalData =  array_reverse($siteSmartInverterFinalData);

                                if ($siteSmartInverterFinalData) {
                                    $dataArrayDetails = [];

                                    $InverterLoopStop = 'N';
                                    $InverterEnergyLoopStop = 'N';
                                    $BatteryLoopStop = 'N';
                                    foreach ($siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {
                                        $responseData = isset($smartInverterFinalData->dataList) ? $smartInverterFinalData->dataList : [];
                                        $invertDetailExist = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter, 'collect_time' => date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))])->orderBy('collect_time', 'desc')->first();
                                        $batteryStationData = StationBattery::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter, 'collect_time' => date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime))])->orderBy('collect_time', 'desc')->first();

                                        if ($responseData) {

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

                                                $invertDetails->collect_time = $inverterDetailLog['collect_time'];
                                                $invertDetails->save();

                                            }else{
                                                $InverterLoopStop = 'Y';
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
                                                $result = $stationBattery->save();


                                            }
                                            else{
                                                $BatteryLoopStop = 'Y';
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
                                                    $collectTime = date('Y-m-d H:i:s', ($smartInverterFinalData->collectTime));
                                                    $inverterEnergyLog['collect_time'] = $collectTime;

                                                    $inverterEnergyLog->save();
                                                }
                                                else{
                                                    $InverterEnergyLoopStop = 'Y';
                                                }
                                            }
                                        }

                                        if($InverterLoopStop == 'Y' && $BatteryLoopStop == 'Y' && $InverterEnergyLoopStop == 'Y'){
                                            break;
                                        }

                                    }

                                    print_r('Invert Detail Loop Time');
                                    print_r(date("Y-m-d H:i:s"));
                                    print_r("\n");
                                }
                            }

                            //DAILY INVERTER DATA
                            $dailyInvData = array();


                            $dailyGenerationData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->exists() ? InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;
                            $dailyConsumptionData = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->exists() ? InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->first()->daily_consumption : 0;

                            $dailyBatteryInverterData = StationBattery::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->first();
                            $inverterEnergyLogData = InverterEnergyLog::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->first();

                            $dailyDischargeEnergy = 0;
                            $dailyChargeEnergy = 0;
                            if ($dailyBatteryInverterData) {
                                $dailyDischargeEnergy = $dailyBatteryInverterData->daily_discharge_energy;
                                $dailyChargeEnergy = $dailyBatteryInverterData->daily_charge_energy;
                            }

                            if ($inverterEnergyLogData) {
                                $dailyEnergyPurchased = $inverterEnergyLogData->import_energy;
                                $dailyGridFeedIn = $inverterEnergyLogData->export_energy;
                            } else {
                                $dailyEnergyPurchased = 0;
                                $dailyGridFeedIn = 0;
                            }

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

                            $DailyInvDataExist = DailyInverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('created_at', $lastRecordDate)->first();

                            if ($DailyInvDataExist) {

                                $dailyInvDataResponse = $DailyInvDataExist->fill($dailyInvData)->save();
                            } else {

                                $dailyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($this->cronJobCollectTime));
                                $dailyInvDataResponse = DailyInverterDetail::create($dailyInvData);
                            }

                            break;
                        }

                    }

                    //SMART INVERTER GENERATION LOG DATA

                    if (!(empty($siteSmartInverterLogStartTime))) {

                        $minTimeSmartInverter = date('Y-m-d', min($siteSmartInverterLogStartTime));

                        $SitesInverterDataGet = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID])->whereDate('collect_time',$minTimeSmartInverter)->orderBy('collect_time','asc')->first();

                        if($SitesInverterDataGet) {
                            $generationInverterDataStartTime = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->whereDate('collect_time',  $minTimeSmartInverter)->orderBy('collect_time','desc')->first();
                            if($generationInverterDataStartTime){
                                $generationInverterDataStartTime =   Date('Y-m-d H',strtotime($generationInverterDataStartTime->collect_time)).":00:00";
                            }else{
                                $generationInverterDataStartTime = date($minTimeSmartInverter . ' 00:00:00');
                            }

                            $smartInverterStartTimeData = InverterDetail::select(DB::raw('SUM(inverterPower) as current_generation,SUM(current_consumption) as current_consumption, daily_generation as totalEnergy'), 'collect_time')->where(['plant_id' => $plantID, 'siteId' => $siteID])->where('dv_inverter', $SitesInverterDataGet->dv_inverter)->whereBetween('collect_time', [$generationInverterDataStartTime, date($minTimeSmartInverter . ' 23:59:59')])->groupBy('collect_time')->get();

                            foreach ($smartInverterStartTimeData as $generationLogKey => $generationLogDataDetail) {

                                $InverterDataStartTime = $generationLogDataDetail->collect_time;
                                $endTime = strtotime("+4 minutes", strtotime($InverterDataStartTime));
                                $InverterDataEndTime = date('Y-m-d H:i:s', $endTime);

                                $generationLogData = InverterDetail::select(DB::raw('SUM(inverterPower) as current_generation,SUM(current_consumption) as current_consumption, daily_generation as totalEnergy, MAX(collect_time) AS collect_time'))->where(['plant_id' => $plantID, 'siteId' => $siteID])->whereBetween('collect_time', [$InverterDataStartTime, $InverterDataEndTime])->get();

                                if (GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time',  $generationLogData[0]->collect_time)->exists()) {

                                    $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time',  $generationLogData[0]->collect_time)->first();

                                    $generationData->current_generation = $generationLogData[0]->current_generation;
                                    $generationData->totalEnergy = $generationLogData[0]->totalEnergy;
                                    $generationData->current_consumption = $generationLogData[0]->current_consumption;
                                    $generationData->save();
                                } else {

                                    $generationLog['plant_id'] = $plantID;
                                    $generationLog['siteId'] = $siteID;
                                    $generationLog['current_generation'] = $generationLogData[0]->current_generation;
                                    $generationLog['comm_failed'] = 0;
                                    $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
                                    $generationLog['current_consumption'] = $generationLogData[0]->current_consumption;
                                    $generationLog['current_grid'] = 0;
                                    $generationLog['current_irradiance'] = 0;
                                    $generationLog['totalEnergy'] = $generationLogData[0]->totalEnergy;
                                    $generationLog['collect_time'] = $generationLogData[0]->collect_time;
                                    $generationLog['created_at'] = $currentTime;
                                    $generationLog['updated_at'] = $currentTime;


                                    $generationLogResponse = GenerationLog::create($generationLog);
                                }
                            }
                        }
                    }

                    if (!(empty($siteAllInverterLogStartTime))) {

                        $minTimeGridInverter = date('Y-m-d', min($siteAllInverterLogStartTime));

                        $gridInverterData = InverterEnergyLog::select('dv_inverter','collect_time')->where(['plant_id' => $plantID, 'site_id' => $siteID])->whereDate('collect_time',$minTimeGridInverter)->orderBy('collect_time','asc')->first();
                        $generationGridDataStartTime = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->whereDate('collect_time',  $minTimeGridInverter)->orderBy('collect_time','desc')->first();
                        $generationGridDataStartTime =   Date('Y-m-d H',strtotime($generationGridDataStartTime->collect_time)).":00:00";
                        if($gridInverterData) {
                            $gridInverterStartTimeData = InverterEnergyLog::select(DB::raw('SUM(grid_power) as grid_power'), 'collect_time')->where(['plant_id' => $plantID, 'site_id' => $siteID])->where('dv_inverter', $gridInverterData->dv_inverter)->whereBetween('collect_time', [$generationGridDataStartTime, date($minTimeGridInverter . ' 23:59:59')])->groupBy('collect_time')->get();

                            foreach ($gridInverterStartTimeData as $gridLogKey => $gridLogData) {

                                $gridDataStartTime = $gridLogData->collect_time;
                                $gridendTime = strtotime("+4 minutes", strtotime($gridDataStartTime));
                                $GridDataEndTime = date('Y-m-d H:i:s', $gridendTime);
                                $gridInvertersSumData = InverterEnergyLog::select(DB::raw('SUM(grid_power) as grid_power ,MAX(collect_time) as collect_time'))->where(['plant_id' => $plantID, 'site_id' => $siteID])->whereBetween('collect_time', [$gridDataStartTime, $GridDataEndTime])->get();

                                if (GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $gridInvertersSumData[0]->collect_time)->exists()) {

                                    $generationData = GenerationLog::where(['plant_id' => $plantID, 'siteId' => $siteID])->where('collect_time', $gridInvertersSumData[0]->collect_time)->first();

                                    //                                $generationData->current_consumption = ($generationData->current_generation + $gridLogData->grid_power) > 0 ? ($generationData->current_generation + $gridLogData->grid_power) : 0;
                                    $generationData->current_grid = ($gridInvertersSumData[0]->grid_power);
                                    $generationData->save();
                                } else {

                                    $generationLog['plant_id'] = $plantID;
                                    $generationLog['siteId'] = $siteID;
                                    $generationLog['current_generation'] = 0;
                                    $generationLog['current_consumption'] = 0;
                                    $generationLog['comm_failed'] = 0;
                                    $generationLog['cron_job_id'] = $generationLogMaxCronJobID;
                                    //                                $generationLog['current_consumption'] = $gridLogData->grid_power > 0 ? $gridLogData->grid_power : 0;
                                    $generationLog['current_grid'] = $gridInvertersSumData[0]->grid_power;
                                    $generationLog['current_irradiance'] = 0;
                                    $generationLog['totalEnergy'] = 0;
                                    $generationLog['collect_time'] = $gridInvertersSumData[0]->collect_time;
                                    $generationLog['created_at'] = $currentTime;
                                    $generationLog['updated_at'] = $currentTime;

                                    $generationLogResponse = GenerationLog::create($generationLog);
                                }
                            }
                        }
                    }

                }
            }

            if (!(empty($siteAllInverterLogStartTime))) {

                $minTimeInverter = date('Y-m-d', min($siteAllInverterLogStartTime));
                $maxTimeInverter = date('Y-m-d', max($siteAllInverterLogStartTime));

                $generationLogInverterStartTimeData = GenerationLog::select(DB::raw('SUM(current_generation) as current_generation, SUM(current_consumption) as current_consumption, SUM(current_grid) as current_grid, SUM(current_irradiance) as current_irradiance, totalEnergy as totalEnergy'), 'collect_time')->where(['plant_id' => $plantID])->whereBetween('collect_time', [$generationGridDataStartTime, date($maxTimeInverter . ' 23:59:59')])->groupBy('collect_time')->get();


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
                        $processedCurrentData['current_saving'] = $processedData->current_generation * (double)$benchMarkPrice;
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
                        $processedCurrentData['current_saving'] = $processedData->current_generation * (double)$benchMarkPrice;
                        $processedCurrentData['processed_cron_job_id'] = $processedMaxCronJobID;
                        $processedCurrentData['collect_time'] = $processedData->collect_time;
                        $processedCurrentData['created_at'] = $currentTime;
                        $processedCurrentData['updated_at'] = $currentTime;

                        $processedCurrentDataResponse = ProcessedCurrentVariable::create($processedCurrentData);
                    }
                }
                if($generationGridDataStartTime){
                    $batteryData = StationBattery::where(['plant_id' => $plantID])->whereBetween('collect_time', [$generationGridDataStartTime, date($maxTimeInverter . ' 23:59:59')])->groupBy('collect_time')->get();

                }else{
                    $batteryData = StationBattery::where(['plant_id' => $plantID])->whereBetween('collect_time', [date($minTimeInverter . ' 00:00:00'), date($maxTimeInverter . ' 23:59:59')])->groupBy('collect_time')->get();

                }

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


                while ($minTimeInverter != date('Y-m-d', strtotime("+1 day", strtotime($maxTimeInverter)))) {


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


                    $plantDailyTotalGeneration = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->sum('daily_generation') : 0;
                    $plantDailyTotalConsumption = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->sum('daily_consumption') : 0;
                    $plantDailyChargeEnergy = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->sum('daily_charge_energy') : 0;
                    $plantDailyDischargeEnergy = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->sum('daily_discharge_energy') : 0;
                    $plantDailyBoughtEnergy = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->sum('daily_energy_purchased') : 0;
                    $plantDailySell = DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->exists() ? DailyInverterDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDataDateToday)->orderBy('created_at', 'DESC')->sum('daily_grid_feed_in') : 0;

                    $peakTimeStart = $plantData->peak_time_start;
                    $dailyPeakConsumptionValue = 0;
                    $peakTimeEnd = $plantData->peak_time_end;
                    $peakStartTimeDetail = $peakTimeStart . ':00:00';
                    $peakEndTimeDetail = $peakTimeEnd . ':00:00';
                    $peakStartTimeConsumptionValue = 0;
                    $peakEndTimeConsumptionValue = 0;

                    $peakStartTimeConsumption = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '<=', $peakEndTimeDetail)->whereTime('collect_time', '>=', $peakStartTimeDetail)->orderBy('collect_time')->first();
                    $peakEndTimeConsumption = InverterDetail::where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '>=', $peakStartTimeDetail)->whereTime('collect_time', '<=', $peakEndTimeDetail)->orderBy('collect_time','DESC')->latest()->first();

                    if ($peakStartTimeConsumption) {
                        $peakStartTimeConsumptionValue = $peakStartTimeConsumption->daily_consumption;
                    }
                    if ($peakEndTimeConsumption) {
                        $peakEndTimeConsumptionValue = $peakEndTimeConsumption->daily_consumption;
                    }
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
//                    $dailyOutagesHours = InverterDetail::Select('collect_time', 'total_grid_voltage')->where('plant_id', $plantID)->where('total_grid_voltage', '!=', Null)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'DESC')->get();

                    $maxValue = 0;
                    $minValue = 0;
                    $totalValue = [];
                    $totalValuesData = 0;

                    //Daily Outage Served
//                    if ($plant->grid_type == 'Three-phase') {
//
//                        $dailyOutages = InverterEnergyLog::Select('collect_time', 'grid_voltage_l1','grid_voltage_l2','grid_voltage_l3')->where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'desc')->get();
//
//                        for ($k = 0; $k < count($dailyOutages); $k++) {
//
//                            if ($dailyOutages[$k]['grid_voltage_l1']) {
//                                if ($dailyOutages[$k]['grid_voltage_l1'] <= 160 && $dailyOutages[$k]['grid_voltage_l2'] <= 160 &&  $dailyOutages[$k]['grid_voltage_l3'] <= 160 && $minValue == 0) {
//                                    $minValue = $dailyOutages[$k]['collect_time'];
//                                } elseif ($dailyOutages[$k]['grid_voltage_l1'] > 160 && $dailyOutages[$k]['grid_voltage_l2'] > 160 && $dailyOutages[$k]['grid_voltage_l3'] > 160 && $maxValue == 0 && $minValue != 0) {
//                                    $maxValue = $dailyOutages[$k]['collect_time'];
//                                }
//                                $maxtimeramge =    Date('H:i' ,strtotime($dailyOutages[$k]['collect_time']));
//                                if($maxtimeramge <= "00:10" && $maxValue == 0){
//                                    $maxValue = $dailyOutages[$k]['collect_time'];
//
//                                }
//                                if ($minValue != 0 && $maxValue != 0) {
////                                return [$minValue,$maxValue];
//                                    $startTime = date('H:i:s', strtotime($minValue));
//                                    $endTime = date('H:i:s', strtotime($maxValue));
//                                    $start_t = new DateTime($startTime);
//                                    $current_t = new DateTime($endTime);
//                                    $difference = $start_t->diff($current_t);
//                                    $return_time = $difference->format('%H:%I');
//                                    array_push($totalValue, $return_time);
//                                    $maxValue = 0;
//                                    $minValue = 0;
//                                }
//                            }
//                        }
//
//                    }else{
//                        $dailyOutages = InverterEnergyLog::Select('collect_time', 'grid_voltage_ln')->where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'desc')->get();
//
//                        for ($k = 0; $k < count($dailyOutages); $k++) {
//
//                            if ($dailyOutages[$k]['grid_voltage_l1']) {
//                                if ($dailyOutages[$k]['grid_voltage_ln'] <= 160 && $minValue == 0) {
//                                    $minValue = $dailyOutages[$k]['collect_time'];
//                                } elseif ($dailyOutages[$k]['grid_voltage_ln'] > 160 && $dailyOutages[$k]['grid_voltage_l2'] > 160 && $dailyOutages[$k]['grid_voltage_l3'] > 160 && $maxValue == 0 && $minValue != 0) {
//                                    $maxValue = $dailyOutages[$k]['collect_time'];
//                                }
//                                $maxtimeramge =    Date('H:i' ,strtotime($dailyOutages[$k]['collect_time']));
//                                if($maxtimeramge <= "00:10" && $maxValue == 0){
//                                    $maxValue = $dailyOutages[$k]['collect_time'];
//
//                                }
//                                if ($minValue != 0 && $maxValue != 0) {
//                                    $startTime = date('H:i:s', strtotime($minValue));
//                                    $endTime = date('H:i:s', strtotime($maxValue));
//                                    $start_t = new DateTime($startTime);
//                                    $current_t = new DateTime($endTime);
//                                    $difference = $start_t->diff($current_t);
//                                    $return_time = $difference->format('%H:%I');
//                                    array_push($totalValue, $return_time);
//                                    $maxValue = 0;
//                                    $minValue = 0;
//                                }
//                            }
//                        }
//                    }
//
//                    $dailyDataSum = $this->AddOutagesTime($totalValue);

//                    if ($dailyDataSum) {
//                        $dailyOutagesHoursData = $dailyDataSum;
//                    } else {
//                        $dailyOutagesHoursData = '00:00';
//                    }

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

                    }

                    //PLANT DAILY DATA
                    $dailyProcessed['plant_id'] = $plantID;
                    $dailyProcessed['dailyGeneration'] = $plantDailyTotalGeneration;
                    $dailyProcessed['dailyGridPower'] = $plantDailyBoughtEnergy > $plantDailySell ? $plantDailyBoughtEnergy - $plantDailySell : $plantDailySell - $plantDailyBoughtEnergy;
                    $dailyProcessed['dailyBoughtEnergy'] = $plantDailyBoughtEnergy;
                    $dailyProcessed['daily_peak_hours_consumption'] = $dailyPeakConsumptionValue;
                    $dailyProcessed['daily_peak_hours_grid_buy'] = $dailyPeakGridImportEnergy;
                    $dailyProcessed['daily_peak_hours_battery_discharge'] = $dailyPeakBatteryDischargeEnergy;
                    $dailyProcessed['dailySellEnergy'] = $plantDailySell;
//                    $dailyProcessed['daily_outage_grid_voltage'] = $dailyOutagesHoursData;
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
//                        $dailyProcessedPlantDetailExist->daily_outage_grid_voltage = $dailyProcessed['daily_outage_grid_voltage'];
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
                    }

                    $minTimeInverter = date('Y-m-d', strtotime("+1 day", strtotime($minTimeInverter)));
                }

            }


        }

//        $this->plantStatusUpdate();
        $plant->last_cron_job_date = $this->cronJobCollectTime;
        $plant->save();

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

            $plantRes = Plant::where('id', $plant->id)->update($updateStatus);
        }
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

        $dataArray = [];
        $todayDate = date('Y-m-d');
        $lastInsertedRecordDate = $lastRecordDate;
        if (isset($siteSmartInverterResponseData->paramDataList)) {
            if (count($siteSmartInverterResponseData->paramDataList) == 0) {

                $lastRecordDateConvert = date('Y-m-d', strtotime("+1 days", strtotime($lastRecordDate)));

                if (($lastRecordDateConvert <= $todayDate)) {

                    $this->cronJobCollectTime = $lastRecordDateConvert;
                    sleep(30);
                    $inverterData = self::getHistoricalData($solisAPIBaseURL, $smartInverter, $lastRecordDateConvert, $token);
                }
            }
        }
        return $inverterData;

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
