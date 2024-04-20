<?php

namespace App\Http\Controllers\HardwareAPIData\SolisCloud;
use App\Http\Controllers\Controller;
use App\Http\Models\CronJobTime;
use App\Http\Models\InverterDetailHistory;
use App\Http\Models\InverterStatusCode;
use App\Http\Models\InverterVersionInformation;
use App\Http\Models\SolarEnergyUtilization;
use App\Http\Models\StationBattery;
use App\Http\Models\StationBatteryHistory;
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

class SolisCloudController extends Controller
{
    // public $keySecret = '9b67232b9c8643b9bb2a4456ca6e37ac';
    // public $baseUrl = 'https://www.soliscloud.com:13333';
    // public $key = '1300386381676569025';
    public $keySecret;
    public $baseUrl;
    public $key;
    public function __construct()
    {
        $this->keySecret = Setting::where('perimeter', 'solis_cloud_secret_key')->value('value');
        $this->baseUrl = Setting::where('perimeter', 'solis_cloud_api_url')->value('value');
        $this->key = Setting::where('perimeter', 'solis_cloud_api_key')->value('value');

    }
    

    public function solis()
    {

        date_default_timezone_set('Asia/Karachi');
        $currentTime = date('Y-m-d H:i:s');
        // print_r('Crone Job Start Time');
        // print_r(date("Y-m-d H:i:s"));
        // print_r("\n");
        $cronJobTime = new CronJobTime();
        $cronJobTime->start_time = $currentTime;
        $cronJobTime->status = "in-progress";
        $cronJobTime->type = 'Solis-Cloud';
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
        $allPlantsData = Plant::where('meter_type', 'Solis-Cloud')->where('id',725)->get();

        $dataArrayValues = array();

        if ($allPlantsData) {

            foreach ($allPlantsData as $key => $plant) {

                $siteAllInverterLogStartTime = array();
                $plantID = $plant->id;
                $benchMarkPrice = $plant->benchmark_price;
                $plantSites = PlantSite::where('plant_id', $plantID)->get();
                if ($plantSites) {
                    foreach ($plantSites as $site) {
                        $siteSmartInverterArray = [];
                        $siteID = $site->site_id;
                        $inverterList = $this->inverterList($site);
                        $inverterDetailListArray = json_decode($inverterList);
                        $finalInverterList = $inverterDetailListArray->data->page->records;
                        foreach ($finalInverterList as $dev) {
                            //Site Inverter Detail
                            $invSerial = SiteInverterDetail::updateOrCreate(
                            ['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $dev->sn,
                            'dv_inverter_type' => 1],
                            ['dv_inverter_serial_no' => $dev->sn, 'dv_inverter_name' => $dev->sn]
                            );
                            $siteSmartInverterArray[] = $dev->sn;
                            if(isset($dev->state) && $dev->state == 1) {
                                $status = "Online";
                            }else if(isset($dev->state) && $dev->state == 2){
                                $status = "Offline";
                            }else{
                                $status = "Alarm";
                            }
                            //INVERTER SERIAL NO
                            $invSerial = InverterSerialNo::updateOrCreate(
                            ['plant_id' => $plantID, 'site_id' => $siteID,
                            'dv_inverter' => $dev->sn, 'inverter_type_id' =>
                            1],
                            ['dv_inverter_serial_no' => $dev->sn,
                            'inverter_name' => $dev->sn, 'status' => $status]
                            );
                        } 
                        $lastRecordTimeStamp = $plant->last_cron_job_date;
                        if (isset($lastRecordTimeStamp) && $lastRecordTimeStamp) {
                          
                            if (strtotime(date('Y-m-d', strtotime($lastRecordTimeStamp))) == strtotime(date('Y-m-d'))) {
                            $lastRecordDate = date('Y-m-d', strtotime($lastRecordTimeStamp));
                            $this->cronJobCollectTime = $lastRecordDate;
                            } else {
                            $lastRecordDate = date('Y-m-d', strtotime("+1 days",
                            strtotime($lastRecordTimeStamp)));
                            $this->cronJobCollectTime = $lastRecordDate;
                            }
                        } else {
                            $lastRecordDate = $plant->data_collect_date;
                            $this->cronJobCollectTime = $lastRecordDate;
                        }
                        $logYear = date('Y', strtotime($lastRecordDate));
                        $logMonth = date('m', strtotime($lastRecordDate));
                        foreach ($siteSmartInverterArray as $smartKey => $smartInverter) {
                            $inverterDetailResponse = $this->inverterDetail($smartInverter);
                            $inverterDetail = json_decode($inverterDetailResponse, true);
                            $inverterDetailFinal = $inverterDetail['data'];
                            $deviceTimeZone = $inverterDetailFinal['timeZone'];

                            if($plant->system_type == 4){
                                $inverterVersionInformation = InverterVersionInformation::updateOrCreate(
                                ['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter],
                                [
                                  'general_settings' => $inverterDetailFinal['productModel'],
                                  'rated_power' => $inverterDetailFinal['power'],
                                  'HMI'=>$inverterDetailFinal['version'],
                                  'main_1' => $inverterDetailFinal['nationalStandardstr'],
                                    // 'production_compliance' => $inverterDetailFinal['production_compliance'],
                                    // 'protocol_version'=>$inverterDetailFinal['version'],
                                    // 'communication_cpu_software' => $inverterDetailFinal['communication_cpu_software'],
                                    // 'lithium_battery_version' => $inverterDetailFinal['lithium_battery_version'],
                                    // 'main_2' => $inverterDetailFinal['communication_cpu_software']
                                ]
                                );
                            }

                             $inverterDailyData = $this->inverterDay($siteID,$smartInverter,$this->cronJobCollectTime,$deviceTimeZone);
                             $inverterFinalDailyData = json_decode($inverterDailyData, true);
                             $inverterFinalDailyData = $inverterFinalDailyData['data'];
                            //  return $inverterFinalDailyData;
                             foreach($inverterFinalDailyData as $inverterDetailLog){

                                $invertDetailExist = InverterDetail::where(['plant_id' => $plantID, 'siteId' => $siteID, 'dv_inverter' => $smartInverter, 'collect_time' => $inverterDetailLog['timeStr']])->orderBy('collect_time','desc')->first();
                                if (empty($invertDetailExist) || $inverterDetailLog['timeStr'] > ($invertDetailExist['collect_time'])) {
                                $invertDetails = new InverterDetail();
                                $invertDetails->plant_id = $plantID;
                                $invertDetails->siteId = $siteID;
                                $invertDetails->dv_inverter = $smartInverter;
                                $invertDetails->inverterPower = $inverterDetailLog['pac'];
                                $invertDetails->daily_generation = $inverterDetailLog['eToday'];
                                $invertDetails->daily_consumption = $inverterDetailLog['homeLoadTodayEnergy'];
                                $invertDetails->current_consumption = $inverterDetailLog['eToday'];
                                $invertDetails->mpptPower = isset($inverterDetailLog['mpptPower']) ? $inverterDetailLog['mpptPower']: "";
                                $invertDetails->frequency = $inverterDetailLog['fac'];
                                $invertDetails->inverterTemperature = $inverterDetailLog['inverterTemperature'];
                                $invertDetails->phase_voltage_r = $inverterDetailLog['uAc1'];
                                $invertDetails->phase_voltage_s = $inverterDetailLog['uAc2'];
                                $invertDetails->phase_voltage_t = $inverterDetailLog['uAc3'];
                                $invertDetails->phase_current_r = $inverterDetailLog['iAc1'];
                                $invertDetails->phase_current_s = $inverterDetailLog['iAc2'];
                                $invertDetails->phase_current_t = $inverterDetailLog['iAc3'];
                                // $invertDetails->total_grid_voltage = $inverterDetailLog['total_grid_voltage'];
                                // $invertDetails->consumption_voltage = $inverterDetailLog['consumption_voltage'];
                                // $invertDetails->consumption_frequency = $inverterDetailLog['consumption_frequency'];
                                // $invertDetails->consumption_active_power_r = $inverterDetailLog['consumption_active_power_r'];
                                // $invertDetails->total_consumption_energy = $inverterDetailLog['total_consumption_energy'];
                                // $invertDetails->inverter_output_voltage = $inverterDetailLog['inverter_output_voltage'];
                                // $invertDetails->ac_power_r_u_a = $inverterDetailLog['ac_power_r_u_a'];
                                $invertDetails->total_production = $inverterDetailLog['eTotal'];
                                $invertDetails->total_consumption = $inverterDetailLog['homeLoadTotalEnergy'];
                                // $invertDetails->battery_temperature = $inverterDetailLog['total_consumption'];
                                $invertDetails->dc_temperature = $inverterDetailLog['inverterTemperature'];
                                // $invertDetails->battery_temperature = $inverterDetailLog['BatteryTemperature'];
                                $invertDetails->output_power_l1 = $inverterDetailLog['iPv1'];
                                $invertDetails->output_power_l2 = $inverterDetailLog['iPv2'];
                                $invertDetails->output_power_l3 = $inverterDetailLog['iPv3'];
                                $invertDetails->load_voltage_l1 = $inverterDetailLog['uPv1'];
                                $invertDetails->load_voltage_l2 = $inverterDetailLog['uPv2'];
                                $invertDetails->load_voltage_l3 = $inverterDetailLog['uPv3'];
                                // $invertDetails->load_voltage_ln = $loadVoltagelNData['load_voltage_ln'];
                                // $invertDetails->total_output_power =
                                // $totalinverterOutputPowerData['total_output_power'];
                                // $invertDetails->inverter_output_power_ln =
                                // $InverterOutputPowerlNData['inverter_output_power_ln'];
                                // $invertDetails->Gene_Input_Load_Enable =
                                // $GeneInputLoadEnableData['Gene_Input_Load_Enable'];
                                // $invertDetails->consump_apparent_power =
                                // $ConsumpApparentPowerData['Consump_Apparent_Power'];
                                // $invertDetails->load_frequency = $LoadFrequencyData['load_frequency'];

                                $invertDetails->collect_time = $inverterDetailLog['timeStr'];
                                $invertDetails->save();
                                }
                                if($plant->system_type == 4){
                                    //Station Batter Detail 
                                    $batteryStationData = StationBattery::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter, 'collect_time' => $inverterDetailLog['timeStr']])->orderBy('collect_time', 'desc')->first();
                                    if (empty($batteryStationData)) {
                                        $stationBattery = new StationBattery();
                                        $stationBattery->plant_id = $plantID;
                                        $stationBattery->site_id = $siteID;
                                        $stationBattery->dv_inverter = $smartInverter;
                                        $stationBattery->battery_capacity = $inverterDetailLog['batteryCapacitySoc'] . '%';
                                        $stationBattery->battery_power = $inverterDetailLog['batteryPower'];
                                        $stationBattery->battery_type = $inverterDetailLog['batteryPower'] >= 0 ? '+ve' : '-ve';
                                        $stationBattery->total_charge_energy = $inverterDetailLog['batteryTotalChargeEnergy'];
                                        $stationBattery->total_discharge_energy = $inverterDetailLog['batteryTotalDischargeEnergy'];
                                        $stationBattery->daily_charge_energy = $inverterDetailLog['batteryTodayChargeEnergy'];
                                        $stationBattery->daily_discharge_energy = $inverterDetailLog['batteryTodayDischargeEnergy'];
                                        $stationBattery->inverter_real_time_consumption = $inverterDetailFinal['homeLoadEnergy'];
                                        $stationBattery->rated_power = $inverterDetailFinal['power'] * 1000;
                                        $stationBattery->collect_time = $inverterDetailLog['timeStr'];
                                        // $stationBattery->battery_temperature = $inverterBatteryDetail['battery_temperature'];
                                        // $stationBattery->battery_status = $inverterDetailLog['battery_status'];
                                        $stationBattery->battery_current = $inverterDetailLog['storageBatteryCurrent'];
                                        $stationBattery->battery_type_data = $inverterDetailLog['batteryType'];
                                        // $stationBattery->battery_charging_voltage = $inverterBatteryDetail['battery_charging_voltage'];
                                        // $stationBattery->battery_bms_current = $inverterDetailFinal['storageBatteryCurrent'];
                                        $stationBattery->battery_bms_current_limiting_charging = $inverterDetailLog['batteryChargingCurrent'];
                                        // $stationBattery->battery_bms_temperature = $inverterBatteryDetail['battery_bms_temperature'];
                                        $stationBattery->battery_bms_current_limiting_discharging = $inverterDetailLog['batteryDischargeLimiting'];
                                        $stationBattery->battery_voltage = $inverterDetailLog['storageBatteryVoltage'];
                                        $stationBattery->battery_bms_voltage = $inverterDetailLog['batteryVoltage'];
                                        // $stationBattery->bms_discharge_voltage = $inverterBatteryDetail['battery_bms_charge_voltage'];
                                        $stationBattery->battery_bms_soc = $inverterDetailLog['batteryCapacitySoc'];
                                        $result = $stationBattery->save();
                                    }
                                     $inverterEnergyLog = InverterEnergyLog::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter, 'collect_time' => $inverterDetailLog['timeStr']])->orderBy('collect_time', 'desc')->first();
                                     if(empty($inverterEnergyLog)){
                                           $inverterEnergyLog = new InverterEnergyLog();
                                           $inverterEnergyLog['plant_id'] = $plantID;
                                           $inverterEnergyLog['site_id'] = $siteID;
                                           $inverterEnergyLog['dv_inverter'] = $smartInverter;
                                           $inverterEnergyLog['grid_power'] = $inverterDetailLog['pac'];
                                           $inverterEnergyLog['import_energy'] =$inverterDetailLog['gridPurchasedTodayEnergy'];
                                           $inverterEnergyLog['export_energy'] = $inverterDetailLog['gridSellTodayEnergy'];
                                           $inverterEnergyLog['collect_time'] = $inverterDetailLog['timeStr'];
                                           $inverterEnergyLog->save();
                                    }
                            
                                }

                                for ($mi = 1; $mi <= 6; $mi++) {
                                    $mpptData = InverterMPPTDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $smartInverter, 'mppt_number' => $mi])->where('collect_time', $inverterDetailLog['timeStr'])->exists();
                                    if (!$mpptData) {
                                        $inverterMPPTLog = array();
                                        $voltageParam = 'uPv'.$mi;
                                        $inverterMPPTLog['mppt_voltage'] = $inverterDetailLog[$voltageParam];
                                        $currentParam = 'iPv'.$mi;
                                        $inverterMPPTLog['mppt_current'] = $inverterDetailLog[$currentParam];
                                        $inverterMPPTLog['mppt_power'] = $inverterDetailLog[$voltageParam] * $inverterDetailLog[$currentParam];
                                        $inverterMPPTLog['collect_time'] = $inverterDetailLog['timeStr'];
                                        $inverterMPPTLog['plant_id'] = $plantID;
                                        $inverterMPPTLog['site_id'] = $siteID;
                                        $inverterMPPTLog['dv_inverter'] = $smartInverter;
                                        $inverterMPPTLog['mppt_number'] = $mi;
                                        $inverterMPPTResponse = InverterMPPTDetail::create($inverterMPPTLog);

                                    }

                                }
                            }
                            //Daily Inverter Detail
                            $inverterMonthlyData = $this->inverterMonth($siteID,$smartInverter,$this->cronJobCollectTime);
                            $inverterMonthlyDataFinal = json_decode($inverterMonthlyData, true);
                            $inverterMonthlyDataFinal = $inverterMonthlyDataFinal['data'];
                            // return [$inverterMonthlyDataFinal];
                            if($inverterMonthlyDataFinal != null){
                                foreach($inverterMonthlyDataFinal as $inverterMonthData){
                                    if($inverterMonthData['dateStr'] == $this->cronJobCollectTime){
                                        $dailyInvData['plant_id'] = $plantID;
                                        $dailyInvData['siteId'] = $siteID;
                                        $dailyInvData['dv_inverter'] = $smartInverter;
                                        $dailyInvData['updated_at'] = $currentTime;
                                        $dailyInvData['daily_generation'] = $inverterMonthData['energy'];
                                        $dailyInvData['daily_consumption'] = $inverterMonthData['consumeEnergy'];
                                        $dailyInvData['daily_charge_energy'] = $inverterMonthData['batteryChargeEnergy'];
                                        $dailyInvData['daily_energy_purchased'] = $inverterMonthData['gridPurchasedEnergy'];
                                        $dailyInvData['daily_grid_feed_in'] = $inverterMonthData['gridSellEnergy'];
                                        $dailyInvData['daily_discharge_energy'] = $inverterMonthData['batteryDischargeEnergy'];
                                        $DailyInvDataExist = DailyInverterDetail::where('plant_id', $plantID)->where('siteId',$siteID)->where('dv_inverter', $smartInverter)->whereDate('created_at',$lastRecordDate)->first();

                                        if ($DailyInvDataExist) {
                                            $dailyInvDataResponse = $DailyInvDataExist->fill($dailyInvData)->save();
                                        } else {
                                            $dailyInvData['created_at'] = date('Y-m-d H:i:s', strtotime($this->cronJobCollectTime));
                                            $dailyInvDataResponse = DailyInverterDetail::create($dailyInvData);
                                        }
                                        break;
                                    }
                                }
                            }



                            //Monthly Inverter Detail
                            $inverterYearlyData = $this->inverterYear($siteID,$smartInverter,$this->cronJobCollectTime);
                            $inverterYearlyDataFinal = json_decode($inverterYearlyData, true);
                            $inverterYearlyDataFinal = $inverterYearlyDataFinal['data'];
                            // return $inverterYearlyDataFinal;
                            if(count($inverterYearlyDataFinal) > 0){
                                foreach($inverterYearlyDataFinal as $inverterDetailFinal){
                                    $inverterMonthYear = Date('Y-m',strtotime($this->cronJobCollectTime));
                                    if($inverterDetailFinal['dateStr'] == $inverterMonthYear){
                                        $monthlyInvData = array();
                                        $monthlyInvData['plant_id'] = $plantID;
                                        $monthlyInvData['siteId'] = $siteID;
                                        $monthlyInvData['dv_inverter'] = $smartInverter;
                                        $monthlyInvData['updated_at'] = $currentTime;
                                        $monthlyInvData['monthly_generation'] = $inverterDetailFinal['energy'];
                                        $monthlyInvData['monthly_energy_purchased'] =$inverterDetailFinal['gridPurchasedEnergy'];
                                        $monthlyInvData['monthly_grid_feed_in'] =$inverterDetailFinal['gridSellEnergy'];
                                        $monthlyInvData['monthly_consumption_energy'] =$inverterDetailFinal['consumeEnergy'];
                                        $monthlyInvData['monthly_charge_energy'] = $inverterDetailFinal['batteryChargeEnergy'];
                                        $monthlyInvData['monthly_discharge_energy'] = $inverterDetailFinal['batteryDischargeEnergy'];

                                        $monthlyInvDataExist = MonthlyInverterDetail::where('plant_id',$plantID)->where('siteId', $siteID)->where('dv_inverter',$smartInverter)->whereYear('created_at',$logYear)->whereMonth('created_at', $logMonth)->first();
                                        if ($monthlyInvDataExist) {
                                            $monthlyInvDataResponse = $monthlyInvDataExist->fill((array)$monthlyInvData)->save();
                                        } else {
                                            $monthlyInvData['created_at'] = date('Y-m-d H:i:s',strtotime($this->cronJobCollectTime));
                                            $monthlyInvDataResponse = MonthlyInverterDetail::create((array)$monthlyInvData);
                                        }
                                        break;
                                    }
                                }
                            }
                                
                            //YearlyInverterDetail
                                $inverterAllData =$this->inverterAll($siteID,$smartInverter,$this->cronJobCollectTime);
                                $inverterAllDataFinal = json_decode($inverterAllData, true);
                                $inverterAllDataFinal = $inverterAllDataFinal['data'];
                                if(count($inverterAllDataFinal) > 0){
                                    foreach($inverterAllDataFinal as $inverterDetailFinal){
                                        if($inverterDetailFinal['year'] == $this->cronJobCollectTime){
                                            $yearlyInvData = array();
                                            $yearlyInvData['plant_id'] = $plantID;
                                            $yearlyInvData['siteId'] = $siteID;
                                            $yearlyInvData['dv_inverter'] = $smartInverter;
                                            $yearlyInvData['updated_at'] = $currentTime;
                                            $yearlyInvData['yearly_generation'] = $inverterDetailFinal['energy'];
                                            $yearlyInvData['yearly_charge_energy'] =$inverterDetailFinal['batteryChargeEnergy'];
                                            $yearlyInvData['yearly_discharge_energy'] =$inverterDetailFinal['batteryDischargeEnergy'];
                                            $yearlyInvDataExist = YearlyInverterDetail::where('plant_id',$plantID)->where('siteId',$siteID)->where('dv_inverter',$smartInverter)->whereYear('created_at', $logYear)->first();
                                            if ($yearlyInvDataExist) {
                                            $yearlyInvDataResponse = $yearlyInvDataExist->fill((array)$yearlyInvData)->save();
                                            } else {
                                            $yearlyInvData['created_at'] = date('Y-m-d H:i:s',strtotime($this->cronJobCollectTime));
                                            $yearlyInvDataResponse = YearlyInverterDetail::create((array)$yearlyInvData);
                                            }
                                            break;
                                        }
                                    }
                                    
                                }


                            $dailyOutagesHoursData = '00:00';
                            $monthlyOutagesData = '00:00';
                            $yearlyOutagesGridValue = '00:00';
                            $dailyPeakConsumptionValue = 0;
                            $dailyPeakGridImportEnergy = 0;
                            $dailyPeakBatteryDischargeEnergy = 0;
                            $monthlyPeakHoursConsumption = 0 ;
                            $monthlyPeakHoursGridBuy =  0;
                            $monthlyPeakHoursDischargeEnergy = 0;
                            $yearlyPeakHoursConsumption = 0;
                            $yearlyPeakHoursGridBuy = 0;
                            $yearlyPeakHoursDischargeEnergy = 0;
                            if($plant->system_type == 4){
                                    
                                //Peak Hous Calculation
                                $peakTimeStart = $plant->peak_time_start;
                                $dailyPeakConsumptionValue = 0;
                                $peakTimeEnd = $plant->peak_time_end;
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
                                if(!($peakStartTimeBatteryDischarge)){
                                    $peakStartTimeBatteryDischarge = StationBatteryHistory::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '<=', $peakEndTimeDetail)->whereTime('collect_time', '>=', $peakStartTimeDetail)->first();
                                }
                                $peakEndTimeBatteryDischarge = StationBattery::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '>=', $peakStartTimeDetail)->whereTime('collect_time', '<=', $peakEndTimeDetail)->orderBy('collect_time', 'DESC')->latest()->first();
                                if(!($peakEndTimeBatteryDischarge)){
                                    $peakEndTimeBatteryDischarge = StationBatteryHistory::where('plant_id', $plantID)->where('site_id', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->whereTime('collect_time', '>=', $peakStartTimeDetail)->whereTime('collect_time', '<=', $peakEndTimeDetail)->orderBy('collect_time', 'DESC')->latest()->first();
                                }
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

                                 //Daily Ouage Served
                                $maxValue = 0;
                                $minValue = 0;
                                $totalValue = [];
                                $totalValuesData = 0;

                                $dailyOutages = inverterDetail::Select('collect_time', 'frequency')->where('plant_id', $plantID)->where('siteId', $siteID)->where('dv_inverter', $smartInverter)->whereDate('collect_time', $this->cronJobCollectTime)->orderBy('collect_time', 'desc')->get();
                                //   return $dailyOutages;
                                for ($k = 0; $k < count($dailyOutages); $k++) {
                                  
                                    if ($dailyOutages[$k]) {
                                        
                                        if ($dailyOutages[$k]['frequency'] <= 1 && $minValue==0) {
                                             
                                            $minValue = $dailyOutages[$k]['collect_time'];
                                    
                                        } elseif ($dailyOutages[$k]['frequency'] > 1 && $maxValue == 0 && $minValue != 0) {
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
                                $dailyDataSum = $this->AddOutagesTime($totalValue);

                                if ($dailyDataSum) {
                                    $dailyOutagesHoursData = $dailyDataSum;
                                }

                                // Monthly Outage Served 
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
                                    }
                                $monthlyOutagesData = $this->AddOutagesTime($times);

                                // Yearly Outage Served
                                $plantMonthlyGenerationTableData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->get();
                                $monthlyDataArray = json_decode(json_encode($plantMonthlyGenerationTableData), true);
                                $yearlyPeakHoursConsumption = array_sum(array_column($monthlyDataArray, 'monthly_peak_hours_consumption'));
                                $yearlyPeakHoursGridBuy = array_sum(array_column($monthlyDataArray, 'monthly_peak_hours_grid_import'));
                                $yearlyPeakHoursDischargeEnergy = array_sum(array_column($monthlyDataArray, 'monthly_peak_hours_discharge_energy'));
                                $yearlyTimes = array();
                                for ($i = 0; $i < count($plantMonthlyGenerationTableData); $i++) {
                                    if ($plantMonthlyGenerationTableData[$i]['monthly_outage_grid_voltage']) {

                                        $monthlyOutagesHoursData = explode(':', $plantMonthlyGenerationTableData[$i]['monthly_outage_grid_voltage']);
                                        $yearlyTimes[] = $monthlyOutagesHoursData[0] . ':' . $monthlyOutagesHoursData[1] . ':00';
                                       
                                    }
                                }
                                    $totalSeconds = 0;
                                    foreach ($yearlyTimes as $t) {
                                        $totalSeconds += $this->toSeconds($t);
                                        
                                    }

                                $yearlyOutagesGridValue = $this->toTimeCalculation($totalSeconds);
                              
                             
                            }

                        }
                        //  return $yearlyOutagesGridValue;
                      
                        $plantDailyData = $this->stationDay($siteID, $this->cronJobCollectTime,$deviceTimeZone);
                        $plantDailyHistoryData = json_decode($plantDailyData, true);
                        $plantDailyHistoryDataFinal = $plantDailyHistoryData['data'];
                        //   return $plantDailyHistoryDataFinal;
                        if($plantDailyHistoryDataFinal != null){
                            foreach ($plantDailyHistoryDataFinal as $smartKeyPlant => $plantHistoryData) {
                                $timestamp = $plantHistoryData['time'];
                                $milliseconds = substr($timestamp, 0, 10); // Extract the seconds part
                                $plantHistoryDataDate = $this->cronJobCollectTime." ";
                                $processedCurrentDataExist = ProcessedCurrentVariable::where(['plant_id' =>$plantID])->where('collect_time', $plantHistoryDataDate.$plantHistoryData['timeStr'])->orderBy('collect_time','desc')->first();
                                if (!$processedCurrentDataExist) {
                                    $startTime =  $plantHistoryDataDate.$plantHistoryData['timeStr'];
                                    $endTime =  Date("Y-m-d H:i:s",strtotime('+5 minutes',strtotime($startTime)));
                                    if($plant->system_type == 4){
                                        $batteryData = StationBattery::where(['plant_id' => $plantID])->whereBetween('collect_time', [$startTime, $endTime])->groupBy('collect_time')->first();
                                        if($batteryData){
                                            // $processedCurrentData['battery_power'] = $batteryData->battery_power;
                                            $processedCurrentData['battery_capacity'] = $batteryData->battery_capacity;
                                            // $processedCurrentData['battery_type'] = $batteryData->battery_type;
                                            $processedCurrentData['total_discharge_energy'] = $batteryData->total_discharge_energy;
                                            $processedCurrentData['total_charge_energy'] = $batteryData->total_charge_energy;
                                            $processedCurrentData['battery_charge'] = $batteryData->daily_charge_energy;
                                            $processedCurrentData['battery_discharge'] = $batteryData->daily_discharge_energy;
                                        }
                                    }

                                    $processedCurrentData['plant_id'] = $plantID;
                                    $processedCurrentData['battery_power'] = $plantHistoryData['batteryPower'];
                                    $processedCurrentData['battery_type']  = $plantHistoryData["batteryPower"] >= 0 ? '-ve' : '+ve';
                                    $processedCurrentData['total_backup_Load'] = $plantHistoryData["bypassLoadPower"]/1000;
                                    $processedCurrentData['grid_Load'] = $plantHistoryData["familyLoadPower"]/1000;
                                    $processedCurrentData['current_generation'] = $plantHistoryData["power"]/1000;
                                    $processedCurrentData['current_consumption'] = $plantHistoryData["bypassLoadPower"] + $plantHistoryData["familyLoadPower"];
                                    $processedCurrentData['current_grid'] = abs($plantHistoryData["psum"]/1000);
                                    $processedCurrentData['grid_type'] = $plantHistoryData["psum"] >= 0 ? '-ve' : '+ve';
                                    $processedCurrentData['current_irradiance'] = 0;
                                    $processedCurrentData['totalEnergy'] = 0;
                                    $processedCurrentData['current_saving'] =  $plantHistoryData["power"] * (double)$benchMarkPrice;
                                    $processedCurrentData['collect_time'] = $plantHistoryDataDate.$plantHistoryData['timeStr'];
                                    $processedCurrentData['created_at'] = $currentTime;
                                    $processedCurrentData['updated_at'] = $currentTime;

                                    $processedCurrentDataResponse = ProcessedCurrentVariable::create($processedCurrentData);
                                }
                            }
                        }
                      
                        //Daily Processed Plant Data
                        $plantDetailData = $this->stationMonth($siteID,$this->cronJobCollectTime);
                        $plantDetail = json_decode($plantDetailData, true);
                        $plantDetailFinalArray = $plantDetail['data'];
                        // return $plantDetail;
                        if(count($plantDetailFinalArray) > 0){
                            foreach ($plantDetailFinalArray as $plantDetailFinal) {
                                if ($this->cronJobCollectTime == $plantDetailFinal['dateStr']) {
                                    //if condition here if both dates match
                                    $dailyProcessed = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $plantDetailFinal['dateStr'])->orderBy('created_at', 'DESC')->first();
                                    if(!$dailyProcessed){
                                        $dailyProcessed = new DailyProcessedPlantDetail();
                                    }
                                    $dailyProcessed['plant_id'] = $plantID;
                                    $dailyProcessed['dailyGeneration'] = $plantDetailFinal['energy'];
                                    $dailyProcessed['dailyGridPower'] = $plantDetailFinal['gridPurchasedEnergy'] > $plantDetailFinal['gridSellEnergy'] ? $plantDetailFinal['gridPurchasedEnergy'] - $plantDetailFinal['gridSellEnergy'] : $plantDetailFinal['gridSellEnergy'] - $plantDetailFinal['gridPurchasedEnergy'];
                                    $dailyProcessed['dailyBoughtEnergy'] = $plantDetailFinal['gridPurchasedEnergy'];
                                    $dailyProcessed['dailyGridLoad'] = $plantDetailFinal['homeGridEnergy'];
                                    $dailyProcessed['dailyBackupLoad'] = $plantDetailFinal['backUpEnergy'];
                                    $dailyProcessed['daily_peak_hours_consumption'] = $dailyPeakConsumptionValue;
                                    $dailyProcessed['daily_peak_hours_grid_buy'] = $dailyPeakGridImportEnergy;
                                    $dailyProcessed['daily_peak_hours_battery_discharge'] =$dailyPeakBatteryDischargeEnergy;
                                    $dailyProcessed['dailySellEnergy'] = $plantDetailFinal['gridSellEnergy'];
                                    $dailyProcessed['daily_outage_grid_voltage'] = $dailyOutagesHoursData;
                                    $dailyProcessed['dailyMaxSolarPower'] = 0;
                                    $dailyProcessed['dailyConsumption'] = abs($plantDetailFinal['homeGridEnergy']) + abs($plantDetailFinal['backUpEnergy']);
                                    $dailyProcessed['dailySaving'] = (double)$plantDetailFinal['energy'] * (double)$benchMarkPrice;
                                    $dailyProcessed['dailyIrradiance'] = 0;
                                    $dailyProcessed['daily_charge_energy'] = $plantDetailFinal['batteryChargeEnergy'];
                                    $dailyProcessed['daily_discharge_energy'] = $plantDetailFinal['batteryDischargeEnergy'];
                                    $dailyProcessed['updated_at'] = $currentTime;
                                    $dailyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($plantDetailFinal['dateStr']));
                                    $dailyProcessed->save();
                                    break;
                                }
                            }
                        }


                        //Monthly Processed Plant Detail
                   
                        $plantYearlyData = $this->stationYear($siteID,$this->cronJobCollectTime);
                        $plantYearlyDataDetail = json_decode($plantYearlyData, true);
                   
                        $plantYearlyDataDetailFinalArray = $plantYearlyDataDetail['data'];
                        if(count($plantYearlyDataDetailFinalArray) > 0){
                            foreach ($plantYearlyDataDetailFinalArray as $plantYearlyDataDetailFinal) {
                                // return [$this->cronJobCollectTime , $plantYearlyDataDetailFinal['dateStr']];
                                if (Date('Y-m',strtotime($this->cronJobCollectTime)) == Date('Y-m',strtotime($plantYearlyDataDetailFinal['dateStr']))) {
                                    $monthlyProcessedPlantDetailExist = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->whereMonth('created_at', $logMonth)->orderBy('created_at', 'DESC')->first();

                                    if (!$monthlyProcessedPlantDetailExist) {
                                        $monthlyProcessedPlantDetailExist = new MonthlyProcessedPlantDetail();
                                    }
                                    $monthlyProcessedPlantDetailExist['plant_id'] = $plantID;
                                    $monthlyProcessedPlantDetailExist['monthlyGeneration'] = $plantYearlyDataDetailFinal['energy'];
                                    $monthlyProcessedPlantDetailExist['monthlyConsumption'] = abs($plantYearlyDataDetailFinal['backUpEnergy']) + abs($plantYearlyDataDetailFinal['homeGridEnergy']);
                                    $monthlyProcessedPlantDetailExist['monthlyGridPower'] = $plantYearlyDataDetailFinal['gridPurchasedEnergy'] >  $plantYearlyDataDetailFinal['gridSellEnergy'] ? $plantYearlyDataDetailFinal['gridPurchasedEnergy'] -  $plantYearlyDataDetailFinal['gridSellEnergy'] :  $plantYearlyDataDetailFinal['gridSellEnergy'] - $plantYearlyDataDetailFinal['gridPurchasedEnergy'];
                                    $monthlyProcessedPlantDetailExist['monthlyBoughtEnergy'] = $plantYearlyDataDetailFinal['gridPurchasedEnergy'];
                                    $monthlyProcessedPlantDetailExist['monthlySellEnergy'] = $plantYearlyDataDetailFinal['gridSellEnergy'];
                                    $monthlyProcessedPlantDetailExist['monthlySaving'] = (double)$plantYearlyDataDetailFinal['energy'] * (double)$benchMarkPrice;
                                    $monthlyProcessedPlantDetailExist['monthly_charge_energy'] = $plantYearlyDataDetailFinal['batteryChargeEnergy'];
                                    $monthlyProcessedPlantDetailExist['monthly_discharge_energy'] = $plantYearlyDataDetailFinal['batteryDischargeEnergy'];
                                    $monthlyProcessedPlantDetailExist['monthlyGridLoad'] = $plantYearlyDataDetailFinal['homeGridEnergy'];
                                    $monthlyProcessedPlantDetailExist['monthlyBackupLoad'] = $plantYearlyDataDetailFinal['backUpEnergy'];
                                     $monthlyProcessedPlantDetailExist['monthly_peak_hours_discharge_energy'] = $monthlyPeakHoursDischargeEnergy;
                                     $monthlyProcessedPlantDetailExist['monthly_peak_hours_grid_import'] = $monthlyPeakHoursGridBuy;
                                     $monthlyProcessedPlantDetailExist['monthly_outage_grid_voltage'] = $monthlyOutagesData;
                                     $monthlyProcessedPlantDetailExist['monthly_peak_hours_consumption'] = $monthlyPeakHoursConsumption;
                                    // $monthlyProcessedPlantDetailExist['monthly_grid_ratio'] = $monthlyGridRatio;
                                    // $monthlyProcessedPlantDetailExist['monthly_charge_ratio'] = $monthlyChargeRatio;
                                    // $monthlyProcessedPlantDetailExist['monthly_generation_value'] = $monthlyGenerationValue;
                                    // $monthlyProcessedPlantDetailExist['monthly_generation_ratio'] = $monthlyGenerationRatio;
                                    // $monthlyProcessedPlantDetailExist['monthly_use_value'] = $monthlyUseValue;
                                    // $monthlyProcessedPlantDetailExist['monthly_use_ratio'] = $monthlyUseRatio;
                                    // $monthlyProcessedPlantDetailExist['monthly_grid_value'] = $monthlyGridValue;
                                    // $monthlyProcessedPlantDetailExist['monthly_discharge_ratio'] = $monthlyDischargeValue;
                                    $monthlyProcessedPlantDetailExist['updated_at'] = $currentTime;
                                    $monthlyProcessedPlantDetailExist['created_at'] = $plantYearlyDataDetailFinal['dateStr'];
                                    $monthlyProcessedPlantDetailExist->save();
                                    break;
                                }
                            }
                        }


                        //Yearly Processed Plant Detail
                        $plantAllYearData = $this->stationAll($siteID,$this->cronJobCollectTime);
                        $plantAllYearDataDetail = json_decode($plantAllYearData, true);
                        $plantAllYearDataDetailFinalArray = $plantAllYearDataDetail['data'];
                            //   return $yearlyPeakHoursConsumption;
                        if(count($plantAllYearDataDetailFinalArray) > 0){
                            foreach ($plantAllYearDataDetailFinalArray as $plantAllYearDataDetailFinal) {
                                if ($logYear == $plantAllYearDataDetailFinal['year']) {
                                    $yearlyProcessedPlantDetailExist = yearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $logYear)->orderBy('created_at', 'DESC')->first();

                                    if (!$yearlyProcessedPlantDetailExist) {
                                        $yearlyProcessedPlantDetailExist = new yearlyProcessedPlantDetail();
                                    }
                                    $yearlyProcessedPlantDetailExist['plant_id'] = $plantID;
                                    $yearlyProcessedPlantDetailExist['yearlyGeneration'] = $plantAllYearDataDetailFinal['energy'];
                                    $yearlyProcessedPlantDetailExist['yearlyConsumption'] = abs($plantAllYearDataDetailFinal['homeGridEnergy']) + abs($plantAllYearDataDetailFinal['backUpEnergy']);
                                    $yearlyProcessedPlantDetailExist['yearlyGridPower'] =  $plantAllYearDataDetailFinal['gridPurchasedEnergy'] >  $plantAllYearDataDetailFinal['gridSellEnergy'] ? $plantAllYearDataDetailFinal['gridPurchasedEnergy'] -  $plantAllYearDataDetailFinal['gridSellEnergy'] :  $plantAllYearDataDetailFinal['gridSellEnergy'] - $plantAllYearDataDetailFinal['gridPurchasedEnergy'];;
                                    $yearlyProcessedPlantDetailExist['yearlyBoughtEnergy'] = $plantAllYearDataDetailFinal['gridPurchasedEnergy'];
                                    $yearlyProcessedPlantDetailExist['yearlySellEnergy'] = $plantAllYearDataDetailFinal['gridSellEnergy'];
                                    $yearlyProcessedPlantDetailExist['yearlySaving'] = $plantAllYearDataDetailFinal['energy'] * (double)$benchMarkPrice;
                                    $yearlyProcessedPlantDetailExist['yearly_charge_energy'] = $plantAllYearDataDetailFinal['batteryChargeEnergy'];
                                    $yearlyProcessedPlantDetailExist['yearlyGridLoad'] = $plantAllYearDataDetailFinal['homeGridEnergy'];
                                    $yearlyProcessedPlantDetailExist['yearlyBackupLoad'] = $plantAllYearDataDetailFinal['backUpEnergy'];
                                    $yearlyProcessedPlantDetailExist['yearly_outage_grid_voltage'] = $yearlyOutagesGridValue;
                                    $yearlyProcessedPlantDetailExist['yearly_discharge_energy'] = $plantAllYearDataDetailFinal['batteryDischargeEnergy'];
                                    $yearlyProcessedPlantDetailExist['yearly_peak_hours_discharge_energy'] = $yearlyPeakHoursDischargeEnergy;
                                    $yearlyProcessedPlantDetailExist['yearly_peak_hours_grid_import'] = $yearlyPeakHoursGridBuy;
                                    $yearlyProcessedPlantDetailExist['yearly_peak_hours_consumption'] = $yearlyPeakHoursConsumption;
                                    // $yearlyProcessedPlantDetailExist['yearly_grid_ratio'] = $yearlyGridRatio;
                                    // $yearlyProcessedPlantDetailExist['yearly_charge_ratio'] = $yearlyChargeRatio;
                                    // $yearlyProcessedPlantDetailExist['yearly_generation_ratio'] = $yearlyGenerationRatio;
                                    // $yearlyProcessedPlantDetailExist['yearly_generation_value'] = $yearlyGenerationValue;
                                    // $yearlyProcessedPlantDetailExist['yearly_use_ratio'] = $yearlyUseRatio;
                                    // $yearlyProcessedPlantDetailExist['yearly_use_value'] = $yearlyUseValue;
                                    // $yearlyProcessedPlantDetailExist['yearly_grid_value'] = $yearlyGridValue;
                                    // $yearlyProcessedPlantDetailExist['yearly_discharge_ratio'] = $yearlyDischargeValue;
                                    $yearlyProcessedPlantDetailExist['updated_at'] = $currentTime;
                                    $yearlyProcessedPlantDetailExist->save();
                                    break;
                                }
                            }
                        }
                        //Total ProcessedPlantDetail
                        $plantTotalProcessedData = $this->stationDetail($siteID);
                        $plantTotalData = json_decode($plantTotalProcessedData, true);
                        $plantTotalFinalData = $plantTotalData['data'];
                      
                        $totalGeneration =  $this->energyToKwh($plantTotalFinalData['allEnergy'], $plantTotalFinalData['allEnergyStr']);
                        $totalConsumption =  $this->energyToKwh($plantTotalFinalData['homeLoadTotalEnergy'], $plantTotalFinalData['homeLoadTotalEnergyStr']);
                        $totalBuy =  $this->energyToKwh($plantTotalFinalData['gridPurchasedTotalEnergy'], $plantTotalFinalData['gridPurchasedTotalEnergyStr']);
                        $totalSell =  $this->energyToKwh($plantTotalFinalData['gridSellTotalEnergy'], $plantTotalFinalData['gridSellTotalEnergyStr']);
                        $totalGrid =  $this->energyToKwh($plantTotalFinalData['homeGridTotalEnergy'], $plantTotalFinalData['homeGridTotalEnergyStr']);
                        $totalBatteryCharge =  $this->energyToKwh($plantTotalFinalData['batteryChargeTotalEnergy'], $plantTotalFinalData['batteryChargeTotalEnergyStr']);
                        $totalBatteryDisCharge =  $this->energyToKwh($plantTotalFinalData['batteryDischargeTotalEnergy'], $plantTotalFinalData['batteryDischargeTotalEnergyStr']);
                        $totalGridLoad =  $this->energyToKwh($plantTotalFinalData['homeGridTotalEnergy'], $plantTotalFinalData['homeGridTotalEnergyStr']);
                        $totalBackupLoad =  $this->energyToKwh($plantTotalFinalData['backupTotalEnergy'], $plantTotalFinalData['backupTotalEnergyStr']);
                        $totalProcessed['plant_id'] = $plantID;
                        $totalProcessed['plant_total_current_power'] = $plantTotalFinalData['power'];
                        $totalProcessed['plant_total_generation'] = $totalGeneration;
                        $totalProcessed['plant_total_consumption'] = $totalConsumption;
                        $totalProcessed['plant_total_grid'] = $totalGrid;
                        $totalProcessed['plant_total_buy_energy'] = $totalBuy;
                        $totalProcessed['plant_total_sell_energy'] = $totalSell;
                        $totalProcessed['plant_total_saving'] = $plantTotalFinalData['allInCome1'];
                        $totalProcessed['plant_total_charge_energy'] = $totalBatteryCharge;
                        $totalProcessed['plant_total_discharge_energy'] = $totalBatteryDisCharge;
                        $totalProcessed['plant_total_grid_load'] = $totalGridLoad;
                        $totalProcessed['plant_total_backup_load'] = $totalBackupLoad;

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

                //Update Last cronjob Date
                if($plantTotalFinalData['state'] == 1){
                    $plantStatus = 'Y';
                }elseif($plantTotalFinalData['state'] == 2){
                    $plantStatus = 'N';
                }elseif($plantTotalFinalData['state'] == 3){
                    $plantStatus = 'P_Y';
                }
                $plant->is_online = $plantStatus;
                $plant->last_cron_job_date = $this->cronJobCollectTime;
                $plant->save();
            }
        }
        $CronJobDetail = CronJobTime::where('id',$cronJobTime->id)->first();
        $CronJobDetail->end_time = date('Y-m-d H:i:s');
        $CronJobDetail->status = 'completed';
        $CronJobDetail->save();
        return "CronJob End Successfully...!!";
    }

      public function solisCloud($path, $data)
      {
      $body = json_encode($data);
      $contentMd5 = base64_encode(md5($body, true));
      $date = gmdate('D, d M Y H:i:s \G\M\T');

      $param = "POST\n{$contentMd5}\napplication/json\n{$date}\n{$path}";
      $sign = $this->sha1Encrypt($param, $this->keySecret);

      $auth = "API {$this->key}:{$sign}";

      $Content_type = 'application/json;charset=UTF-8';

      $apiPath = $this->baseUrl.$path;

      $curl = curl_init();

      curl_setopt_array($curl, array(
      CURLOPT_URL => $apiPath,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>$body,
      CURLOPT_HTTPHEADER => array(
      'Authorization:'.$auth,
      'Date:'.$date,
      'Content-MD5:'.$contentMd5,
      'Content-Type: application/json;charset=UTF-8',
      'Cookie: aliyungf_tc=86e5f010ac48375aa18c16560ae66ab80ab2718d47bc970471c4995d7036ef9e'
      ),
      ));

      $response = curl_exec($curl);
      if ($response === false) {
      $error = curl_error($curl);
      return "cURL error: $error";
      }
      curl_close($curl);
      return $response;
      }
    private function sha1Encrypt(string $encryptText, string $keySecret): string
    {
        return base64_encode(hash_hmac('sha1', $encryptText, $keySecret, true));
    }

    public function userStationList() {
         $pageNo = '1';
         $pageSize = '10';
        //  $nmiCode = '';

        //  if($nmiCode!= null){
        //      $data['nmiCode'] = $nmiCode;
        //  }
         if($pageNo!= null){
             $data['pageNo'] = $pageNo;
         }
         if($pageSize!= null){
             $data['pageSize'] = $pageSize;
         }
            $path = '/v1/api/userStationList';
            $body = json_encode($data);
            $contentMd5 = base64_encode(md5($body, true));
            $date = gmdate('D, d M Y H:i:s \G\M\T');
            $param = "POST\n{$contentMd5}\napplication/json\n{$date}\n{$path}";
            $sign = $this->sha1Encrypt($param, $this->keySecret);
            $auth = "API {$this->key}:{$sign}";
            $Content_type = 'application/json;charset=UTF-8';
            $apiPath = $this->baseUrl.$path;
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $apiPath,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$body,
            CURLOPT_HTTPHEADER => array(
            'Authorization:'.$auth,
            'Date:'.$date,
            'Content-MD5:'.$contentMd5,
            'Content-Type: application/json;charset=UTF-8',
            'Cookie: aliyungf_tc=86e5f010ac48375aa18c16560ae66ab80ab2718d47bc970471c4995d7036ef9e'
            ),
        ));

        $response = curl_exec($curl);
        if ($response === false) {
            $error = curl_error($curl);
            return "cURL error: $error";
        }
        curl_close($curl);
            return [$response,"okkk"];
    }
    public function inverterList($siteDetail) {
      $data['pageNo'] = '1';
      $data['pageSize'] = '10';
      $data['stationId'] = $siteDetail->site_id;
      $path = '/v1/api/inverterList';
       return $this->solisCloud($path, $data);
    }
    public function inverterDetail($inverterSn){
         $data = [];
         $data['sn'] = $inverterSn;
         $path = '/v1/api/inverterDetail';
         return $this->solisCloud($path, $data);
    }
    public function inverterDetailList(){
         $pageNo = '1';
         $pageSize = '100';
         if($pageNo!= null){
             $data['pageNo'] = $pageNo;
         }
         if($pageSize!= null){
             $data['pageSize'] = $pageSize;
        }
         $path = '/v1/api/inverterDetailList';
            $body = json_encode($data);
            $contentMd5 = base64_encode(md5($body, true));
            $date = gmdate('D, d M Y H:i:s \G\M\T');
            $param = "POST\n{$contentMd5}\napplication/json\n{$date}\n{$path}";
            $sign = $this->sha1Encrypt($param, $this->keySecret);
            $auth = "API {$this->key}:{$sign}";
            $Content_type = 'application/json;charset=UTF-8';
            $apiPath = $this->baseUrl.$path;
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $apiPath,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$body,
            CURLOPT_HTTPHEADER => array(
                'Authorization:'.$auth,
                'Date:'.$date,
                'Content-MD5:'.$contentMd5,
                'Content-Type: application/json;charset=UTF-8',
                'Cookie: aliyungf_tc=86e5f010ac48375aa18c16560ae66ab80ab2718d47bc970471c4995d7036ef9e'
            ),
         ));

         $response = curl_exec($curl);
         if ($response === false) {
            $error = curl_error($curl);
         return "cURL error: $error";
         }
            curl_close($curl);
         return $response;
    }
    public function inverterDay($siteDetail, $devDetail,$dataFetchDate,$timeZone){
        $data['sn'] = $devDetail;
        $data['money'] ='CNY';
        $data['time'] = $dataFetchDate;
        $data['timeZone'] = $timeZone;
        $path = '/v1/api/inverterDay';
        return $this->solisCloud($path, $data);
    }
    public function inverterMonth($siteDetail, $sn,$dataFetchDate){

        $date = Date('Y-m',strtotime($dataFetchDate));
        $data['sn'] = $sn;
        $data['money'] = 'CNY';
        $data['month'] = $date;
        $path = '/v1/api/inverterMonth';
        return $this->solisCloud($path ,$data );       
    }
    public function inverterYear($siteDetail, $sn,$dataFetchDate ){
            $date = Date('Y',strtotime($dataFetchDate));
            $data['sn'] = $sn;
            $data['money'] = 'CNY';
            $data['year'] = $date;
            $path = '/v1/api/inverterYear';
           return $this->solisCloud($path ,$data );
    }
     public function inverterAll($siteDetail, $sn,$dataFetchDate){
        $data['sn'] = $sn;
        $data['money'] = 'CNY';
        $path = '/v1/api/inverterAll';
        return $this->solisCloud($path ,$data );
     }
     public function shelfTime(){
         $pageNo = '1';
         $pageSize = '20';
         $sn = '11822222C110105';
         if($pageNo!= null){
            $data['pageNo'] = $pageNo;
         }
         if($pageSize!= null){
            $data['pageSize'] = $pageSize;
         }
        if($sn != null){
            $data['sn'] = $sn;
         }
        $path = '/v1/api/inverter/shelfTime';
        return $this->solisCloud($path, $data);
     }
     public function alarmList(){
          $pageNo     = '1';
          $pageSize   = '10';
          $stationId = '1298491919449344877';
          $alarmDeviceSn = '11822222C110105';
         
         if($pageNo!= null){
           $data['pageNo'] = $pageNo;
         }
         if($pageSize!= null){
           $data['pageSize'] = $pageSize;
         }
         if($stationId != null){
           $data['stationId'] = $stationId;
         }
         if($alarmDeviceSn != null){
            $data['alarmDeviceSn'] = $alarmDeviceSn;
         }
         $path = '/v1/api/alarmList';
         return $this->solisCloud($path, $data);
     }
     public function collectorList(){
          $pageNo     = '1';
          $pageSize   = '10';
        //   $stationId = '1298491919449378787';
        //    $nmiCode = '41028459350';
         if($pageNo!= null){
           $data['pageNo'] = $pageNo;
         }
         if($pageSize!= null){
           $data['pageSize'] = $pageSize;
         }
        //  if($stationId != null){
        //    $data['stationId'] = $stationId;
        //  }
        //  if($nmiCode != null){
        //     $data['nmiCode'] = $nmiCode;
        //  }
         $path = '/v1/api/collectorList';
         return $this->solisCloud($path, $data);
     }
     public function collectorDetail(){
        // $id = '1306858901386141423';
        $sn = '404314859';
        // if($id != null){
        // $data['id'] = $id;
        // }
        if($sn != null){
        $data['sn'] = $sn;
        }
        $path = '/v1/api/collectorDetail';
        return $this->solisCloud($path ,$data );
       }
      public function stationDetail($plantID){
   
        $data['id'] = $plantID;
        $path = '/v1/api/stationDetail';
        return $this->solisCloud($path ,$data );
    }
     public function stationMonth($plantID,$dataFetchDate){

        $fetchDateMonthlyPlantData = Date('Y-m',strtotime($dataFetchDate));
        $data['id'] = $plantID;
        $data['month'] = $fetchDateMonthlyPlantData;
        $data['timeZone'] = '8';
        $path = '/v1/api/stationMonth';
        return $this->solisCloud($path ,$data );
    }
     public function stationYear($plantID,$dataFetchDate){

        $fetchDateMonthlyPlantData = Date('Y',strtotime($dataFetchDate));
        $data['id'] = $plantID;
        $data['year'] = $fetchDateMonthlyPlantData;
        $data['timeZone'] = '8';
        $data['money'] = 'CNY';
        $path = '/v1/api/stationYear';
        return $this->solisCloud($path ,$data );
    }
    public function stationAll($plantID,$dataFetchDate){

        $data['id'] = $plantID;
        $data['timeZone'] = '8';
        $data['money'] = 'CNY';
        $path = '/v1/api/stationAll';
        return $this->solisCloud($path ,$data );
    }
     public function stationDay($plantID, $dataFetchDate,$timeZone){

         $data = [];
         $data['pageNo'] = '1';
         $data['pageSize'] = '100';
         $data['id'] = $plantID;
         $data['time'] = $dataFetchDate;
         $data['timeZone'] = $timeZone;
    
        $path = '/v1/api/stationDay';
      
        return $this->solisCloud($path ,$data );
       }

       public function energyToKwh($value, $unit)
       {
           $unit = strtolower($unit);
           switch ($unit) {
               case 'mwh':
                   return $value * 1000; // 1 MWh = 1000 kWh
               case 'kwh':
                   return $value;
               case 'wh':
                   return $value / 1000; // 1 Wh = 0.001 kWh
               case 'mw':
                   return $value * 1000; // 1 MW = 1000 kW = 1000 kWh
               case 'kw':
                   return $value; // 1 kW = 1 kWh
               case 'w':
                   return $value / 1000; // 1 W = 0.001 kW = 0.001 kWh
               default:
                   throw new \InvalidArgumentException("Unsupported unit. Supported units are: MWh, kWh, Wh, MW, kW, W");
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
