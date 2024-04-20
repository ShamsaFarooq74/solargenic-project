<?php

namespace App\Http\Controllers\HardwareAPIData;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HardwareAPIData\SaltecLogger\MGCELoggerController;
use App\Http\Controllers\HardwareAPIData\SaltecLogger\MGCWLoggerController;
use App\Http\Models\AllPlantsCumulativeData;
use App\Http\Models\CronJobTime;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\SaltecDailyCumulativeData;
use App\Http\Models\SaltecInverterDailyCumulativeData;
use App\Http\Models\SaltecPushData;
use App\Http\Models\GenerationLog;
use App\Http\Models\UnBuildSites;
use App\Http\Models\Inverter;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterMPPTDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\MicrotechEnergyGenerationLog;
use App\Http\Models\MicrotechPowerGenerationLog;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\Plant;
use App\Http\Models\PlantSite;
use App\Http\Models\PlantUser;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\ProcessedPlantDetail;
use App\Http\Models\Setting;
use App\Http\Models\TotalProcessedPlantDetail;
use App\Http\Models\Weather;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;


class SaltecMultipleSiteController extends Controller
{

    function transferDataSaltec()
    {

        date_default_timezone_set('Asia/Karachi');
        $currentDate = date('Y-m-d');
        $cronJobTime = new CronJobTime();
        $cronJobTime->start_time = Date('Y-m-d H:i:s');
        $cronJobTime->type = 'Saltec-Transfer-Data';
        $cronJobTime->status = 'in-progress';
        $cronJobTime->save();
        $Date = date('Y-m-d', strtotime('-1 day'));
        // return $Date;
        $saltecDataQuery = DB::statement("INSERT INTO `saltec_push_response_history`(`id`, `site_id`, `response`, `status`, `collect_time`, `created_at`, `updated_at`)   SELECT `id`, `site_id`, `response`, `status`, `collect_time`, `created_at`, `updated_at` FROM saltec_push_response WHERE date(created_at) < '$currentDate' ON DUPLICATE KEY UPDATE updated_at = '$currentDate' ");
        if ($saltecDataQuery == true) {
            SaltecPushData::whereDate('created_at', '<=', $Date)->delete();
            $saltechistoryDeleteDate = date('Y-m-d', strtotime('-5 day'));
            $saltecHistoryData = DB::statement("DELETE FROM `saltec_push_response_history` WHERE `collect_time` < '$saltechistoryDeleteDate'  ");
        }
        $CronJobDetail = CronJobTime::where('id',$cronJobTime->id)->first();
        $CronJobDetail->end_time = date('Y-m-d H:i:s');
        $CronJobDetail->status = 'completed';
        $CronJobDetail->save();
        return "All Data Transfer Successfully";
        return "ok";
    }

    function saltec(Request $request)
    {

        date_default_timezone_set('Asia/Karachi');
        $time_difference_saltec = Date('Y-m-d H:i:s');
        $time_difference_micro = Date('Y-m-d H:i:s');
        $generation_log_created_time = Date('Y-m-d H:i:s');
        $current_time = Date('Ymd') . 'T' . Date('His');
        $cumulative_total_energy = 0;
        $cumulative_total_saving = 0;
        $Total_energy_MGCE = 0;
        $Total_Saving_MGCE = 0;
        $MGCWController = [];
        $MGCEController = [];
        $generation_log_cron_job_id2 = 0;
        $generation_log_cron_job_id2 = GenerationLog::max('cron_job_id');
        $processed_cron_job_id = ProcessedCurrentVariable::max('processed_cron_job_id');
        $processedCronJobId = ProcessedCurrentVariable::max('processed_cron_job_id');
        $cronJobTime = new CronJobTime();
        $cronJobTime->start_time = $generation_log_created_time;
        $cronJobTime->type = 'Saltec/Microtech-Multiple';
        $cronJobTime->status = 'in-progress';
        $cronJobTime->processed_cron_job_id = $processedCronJobId + 1;
        $cronJobTime->save();
        $all_plants = Plant::whereIn('id',[8,48,51,59,63,65,67,69,71,73,85,88,91,98,102,613])->whereIn('meter_type', ['Microtech', 'Saltec', 'Saltec-Goodwe', 'Microtech-Goodwe'])->get();

        foreach ($all_plants as $plant) {

            $benchmark = isset($plant->benchmark_price) ? (int)$plant->benchmark_price : 1;

            $plant_sites = PlantSite::where('plant_id', $plant->id)->get();

            $dailyGeneration = 0;
            $dailyConsumption = 0;
            $dailyImport = 0;
            $dailyExport = 0;
            $dailyGrid = 0;
            $dailySaving = 0;
            $monthlyImport = 0;
            $monthlyExport = 0;
            $yearlyGeneration = 0;
            $yearlyConsumption = 0;
            $yearlyImport = 0;
            $yearlyExport = 0;
            $yearlyGrid = 0;
            $yearlySaving = 0;
            $finalProccessedGeneration = 0;
            $finalProccessedConsumption = 0;
            $finalProccessedImport = 0;
            $finalProccessedExport = 0;
            foreach ($plant_sites as $key1 => $site) {

                $data = DB::table('saltec_push_response')->where('site_id', $site->site_id)->where('device_type','!=','')->where('status', "N")->whereDate('collect_time',Date('Y-m-d'))->orderBy('collect_time',"desc")->first();

                if ($data) {
                    $response = json_decode($data->response);
                    $final_processed_data = $response->data;
                    $plant_inverter_final_data = $final_processed_data;
                    if (isset($final_processed_data) && $final_processed_data) {

                        foreach ($final_processed_data as $key => $plant_final_processed_data) {

                                $siteId = $site->site_id;
                            if ($plant_final_processed_data->DeviceType == "MCMT" || $plant_final_processed_data->DeviceType == "MGCW" || $plant_final_processed_data->DeviceType == "MGCE") {

                                if($plant_final_processed_data->DeviceType == 'MGCE' || $plant_final_processed_data->DeviceType == 'MGCW'){
                                    $salteclatestData = DB::table('saltec_push_response')
                                    ->where('site_id', $siteId)
                                    ->orderBy('collect_time', 'desc');
                                    if ($plant_final_processed_data->DeviceType == 'MGCE') {
                                        $salteclatestData->skip(1); // offset by 1 to get the second last response
                                    }
                                    $salteclatestData = $salteclatestData->take(1) // get only 1 result
                                        ->value('response');
                        
                                    if ($salteclatestData) {
                            
                                        $latestResponse = json_decode($salteclatestData);
                                        $latest_final_processed_data = $latestResponse->data;
                            
                                        if (isset($latest_final_processed_data) && $latest_final_processed_data) {
                                            $MGCEResponse = [];
                                            foreach ($latest_final_processed_data as $key18 => $plant_latest_final_processed_data) {
                            
                                                if($plant_latest_final_processed_data->DeviceType == "MGCE" || $plant_latest_final_processed_data->DeviceType == "MGCW") {
                                                    $MGCEResponse =  (object)array_merge((array)$MGCEResponse, (array)$plant_latest_final_processed_data);
                                                    $plant_final_processed_data = $MGCEResponse;
                                                }
                                            }
                                        }
                                    }
                                }


                                if (isset($plant_final_processed_data->numberOfInverters) && $plant_final_processed_data->numberOfInverters != null && (int)$plant_final_processed_data->numberOfInverters != 0) {

                                    for ($i = 1; $i <= (int)$plant_final_processed_data->numberOfInverters; $i++) {
                                        //Inverter Serial No
                                        $inverters_data = (array)$plant_final_processed_data;
                                        $inverter_input['plant_id'] = $plant->id;
                                        $inverter_input['site_id'] = $site->site_id;
                                        $inverter_input['dv_inverter'] = 'dv_Inv' . $i;
                                        $length = 4;
                                        $randomString = substr(str_shuffle(str_repeat($x = '123456789', ceil($length / strlen($x)))), 1, $length);
                                        $inverter_input['dv_inverter_serial_no'] = isset($inverters_data['dv_Inv' . $i . 'SerialNumber']) ? $inverters_data['dv_Inv' . $i . 'SerialNumber'] : '000000' . $randomString;
                                        $inverter_input['created_at'] = Date('Y-m-d H:i:s');
                                        $inverter_input['updated_at'] = Date('Y-m-d H:i:s');
                                        $inverter_exist = InverterSerialNo::where('site_id', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->Where('dv_inverter_serial_no', 'like', '000000%')->first();
                                        $inverter_exist_create = InverterSerialNo::where('site_id', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->first();

                                        if ($inverter_exist) {
                                            $inverter_updation_responce = $inverter_exist->fill($inverter_input)->save();
                                        }
                                        if (!$inverter_exist_create) {
                                            $inverter_insertion_responce = InverterSerialNo::create($inverter_input);
                                        }
                                        //Inverter Data
                                        $inverters_data = (array)$plant_final_processed_data;
                                        $inverter_input['plant_id'] = $plant->id;
                                        $inverter_input['siteId'] = $site->site_id;
                                        $inverter_input['dv_inverter'] = 'dv_Inv' . $i;
                                        $inverter_input['ac_output_power'] = isset($inverters_data['inverter' . $i . 'Power']) ? $inverters_data['inverter' . $i . 'Power'] : null;
                                        $inverter_input['total_generation'] = isset($inverters_data['inverter' . $i . 'Energy']) ? $inverters_data['inverter' . $i . 'Energy'] : null;
                                        $inverter_input['r_voltage1'] = isset($inverters_data['l1Voltage']) ? $inverters_data['l1Voltage'] : null;
                                        $inverter_input['r_current1'] = isset($inverters_data['l1GridCurrent']) ? $inverters_data['l1GridCurrent'] : null;
                                        $inverter_input['r_voltage2'] = isset($inverters_data['l2Voltage']) ? $inverters_data['l2Voltage'] : null;
                                        $inverter_input['r_current2'] = isset($inverters_data['l2GridCurrent']) ? $inverters_data['l2GridCurrent'] : null;
                                        $inverter_input['r_voltage3'] = isset($inverters_data['l3Voltage']) ? $inverters_data['l3Voltage'] : null;
                                        $inverter_input['r_current3'] = isset($inverters_data['l3GridCurrent']) ? $inverters_data['l3GridCurrent'] : null;
                                        $inverter_input['frequency'] = isset($inverters_data['gridFrequency']) ? $inverters_data['gridFrequency'] : null;
                                        $inverter_input['dc_power'] = isset($inverters_data['inverter' . $i . 'Power']) ? $inverters_data['inverter' . $i . 'Power'] : null;
                                        $inverter_input['lastUpdated'] = isset($inverters_data['Timestamp']) ? $inverters_data['Timestamp'] : null;
                                        $inverter_input['created_at'] = Date('Y-m-d H:i:s');
                                        $inverter_input['updated_at'] = Date('Y-m-d H:i:s');

                                        $inverter_exist = Inverter::where('siteId', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->delete();

                                        $inverter_insertion_responce = Inverter::create($inverter_input);
                                        //Saltec Inverter Commulative
                                        $InverterTotalGeneration = 0;
                                        $InverterTotalGeneration = isset($inverters_data['inverter' . $i . 'Energy']) ? $inverters_data['inverter' . $i . 'Energy'] : 0;

                                        $inverterDailyComulative = SaltecInverterDailyCumulativeData::where('plant_id', $plant->id)->where('site_id',$site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereDate('created_at', Date("Y-m-d"))->first();
                                        $inputInvDailyData['plant_id'] = $plant->id;
                                        $inputInvDailyData['site_id'] = $site->site_id;
                                        $inputInvDailyData['dv_inverter'] = $inverter_input['dv_inverter'];
                                        $inputInvDailyData['generation'] = (double)$InverterTotalGeneration;
                                        $inputInvDailyData['created_at'] = Date("Y-m-d H:i:s", strtotime($plant_final_processed_data->Timestamp));
                                        if ($inverterDailyComulative) {
                                            $inputInvDailyData['updated_at'] = date('Y-m-d H:i:s');
                                            $inverterDailyComulative->fill($inputInvDailyData)->save();
                                        } else {
                                            SaltecInverterDailyCumulativeData::create($inputInvDailyData);
                                        }

                                        $yesterday_inv_generation =  SaltecInverterDailyCumulativeData::where('plant_id', $plant->id)->where('site_id',$site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereDate('created_at', Date("Y-m-d", strtotime("-1 day")))->first();
                                        $daily_inv_generation = (isset($inverters_data['inverter' . $i . 'Energy']) ? $inverters_data['inverter' . $i . 'Energy'] : 0) - (isset($yesterday_inv_generation->generation) ? $yesterday_inv_generation->generation : 0);
                                        $monthly_inv_generation = DailyInverterDetail::where('plant_id',$plant->id)->where('siteId',$site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->sum('daily_generation');
                                        $yearly_inv_generation = MonthlyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereYear('created_at', '=', date('Y'))->sum('monthly_generation');
                                        $inverters_data = (array)$plant_final_processed_data;

                                        $inverter['plant_id'] = $plant->id;
                                        $inverter['siteId'] = $site->site_id;
                                        $inverter['dv_inverter'] = 'dv_Inv' . $i;
                                        $inverter['daily_generation'] = isset($daily_inv_generation) ? $daily_inv_generation : null;
                                        $inverter['monthly_generation'] = isset($monthly_inv_generation) ? $monthly_inv_generation : null;
                                        $inverter['inverterPower'] = isset($inverters_data['inverter' . $i . 'Power']) ? $inverters_data['inverter' . $i . 'Power'] : null;
                                        $inverter['inverterEnergy'] = isset($inverters_data['inverter' . $i . 'Energy']) ? $inverters_data['inverter' . $i . 'Energy'] : null;
                                        $inverter['totalInverterPower'] = isset($inverters_data['totalInverterPower']) ? $inverters_data['totalInverterPower'] : null;
                                        $inverter['inverterLimitValue'] = isset($inverters_data['inverter' . $i . 'LimitValue']) ? $inverters_data['inverter' . $i . 'LimitValue'] : null;
                                        $inverter['inverterCommFail'] = isset($inverters_data['inverter' . $i . 'CommFail']) ? $inverters_data['inverter' . $i . 'CommFail'] : null;
                                        $inverter['inverterConfigFail'] = isset($inverters_data['Inv_' . $i . '_Config_Fail']) ? $inverters_data['Inv_' . $i . '_Config_Fail'] : null;
                                        $inverter['inverterUptime'] = isset($inverters_data['inverter' . $i . 'Uptime']) ? $inverters_data['inverter' . $i . 'Uptime'] : null;
                                        $inverter['ac_output_power'] = isset($inverters_data['inverter' . $i . 'Power']) ? $inverters_data['inverter' . $i . 'Power'] : null;
                                        $inverter['total_generation'] = isset($inverters_data['inverter' . $i . 'Energy']) ? $inverters_data['inverter' . $i . 'Energy'] : null;
                                        $inverter['phase_voltage_r'] = isset($inverters_data['l1Voltage']) ? $inverters_data['l1Voltage'] : null;
                                        $inverter['phase_current_r'] = isset($inverters_data['l1GridCurrent']) ? $inverters_data['l1GridCurrent'] : null;
                                        $inverter['phase_voltage_s'] = isset($inverters_data['l2Voltage']) ? $inverters_data['l2Voltage'] : null;
                                        $inverter['phase_current_s'] = isset($inverters_data['l2GridCurrent']) ? $inverters_data['l2GridCurrent'] : null;
                                        $inverter['phase_voltage_t'] = isset($inverters_data['l3Voltage']) ? $inverters_data['l3Voltage'] : null;
                                        $inverter['phase_current_t'] = isset($inverters_data['l3GridCurrent']) ? $inverters_data['l3GridCurrent'] : null;
                                        $inverter['frequency'] = isset($inverters_data['gridFrequency']) ? $inverters_data['gridFrequency'] : null;
                                        $inverter['dc_power'] = isset($inverters_data['inverter' . $i . 'Power']) ? $inverters_data['inverter' . $i . 'Power'] : null;
                                        $inverter['numberOfInverters'] = isset($inverters_data['numberOfInverters']) ? $inverters_data['numberOfInverters'] : null;
                                        $inverter['lastUpdated'] = isset($inverters_data['Timestamp']) ? $inverters_data['Timestamp'] : null;
                                        $inverter['created_at'] = Date('Y-m-d H:i:s');
                                        $inverter['collect_time'] = date('Y-m-d H:i:s', strtotime($inverters_data['Timestamp']));
                                        $inverter['updated_at'] = Date('Y-m-d H:i:s');
                                        $inverter_detail_insertion_responce = InverterDetail::create($inverter);

                                        $daily_inverter_detail_exist = DailyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereDate('created_at', '=', Date('Y-m-d', strtotime($inverters_data['Timestamp'])))->first();

                                        $daily_input['plant_id'] = $plant->id;
                                        $daily_input['siteId'] = $site->site_id;
                                        $daily_input['dv_inverter'] = 'dv_Inv' . $i;
                                        $daily_input['lastUpdated'] = $inverters_data['Timestamp'];
                                        $daily_input['created_at'] = Date('Y-m-d H:i:s', strtotime($inverters_data['Timestamp']));
                                        $daily_input['updated_at'] = Date('Y-m-d H:i:s');
                                        $daily_input['daily_generation'] = $daily_inv_generation;

                                        if ($daily_inverter_detail_exist != null) {
                                            $daily_inverter_detail_insertion_responce = $daily_inverter_detail_exist->fill($daily_input)->save();
                                        } else {
                                            $daily_inverter_detail_insertion_responce = DailyInverterDetail::create($daily_input);
                                        }

                                        $monthly_inverter_detail_exist = MonthlyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereYear('created_at', '=', date('Y', strtotime($inverters_data['Timestamp'])))->whereMonth('created_at', '=', date('m', strtotime($inverters_data['Timestamp'])))->first();

                                        $monthly_input['plant_id'] = $plant->id;
                                        $monthly_input['siteId'] = $site->site_id;
                                        $monthly_input['dv_inverter'] = 'dv_Inv' . $i;
                                        $monthly_input['lastUpdated'] = $inverters_data['Timestamp'];
                                        $monthly_input['created_at'] = Date('Y-m-d H:i:s', strtotime($inverters_data['Timestamp']));
                                        $monthly_input['updated_at'] = Date('Y-m-d H:i:s');
                                        $monthly_input['monthly_generation'] = $monthly_inv_generation;

                                        if ($monthly_inverter_detail_exist) {
                                            $monthly_inverter_detail_insertion_responce = $monthly_inverter_detail_exist->fill($monthly_input)->save();
                                        } else {
                                            $monthly_inverter_detail_insertion_responce = MonthlyInverterDetail::create($monthly_input);
                                        }

                                        $yearly_inverter_detail_exist = YearlyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereYear('created_at', '=', date('Y', strtotime($inverters_data['Timestamp'])))->first();

                                        $yearly_input['plant_id'] = $plant->id;
                                        $yearly_input['siteId'] = $site->site_id;
                                        $yearly_input['dv_inverter'] = 'dv_Inv' . $i;
                                        $yearly_input['yearly_generation'] = $yearly_inv_generation ? (double)$yearly_inv_generation : 0;
                                        $yearly_input['lastUpdated'] = $inverters_data['Timestamp'];
                                        $yearly_input['created_at'] = Date('Y-m-d H:i:s', strtotime($inverters_data['Timestamp']));
                                        $yearly_input['updated_at'] = Date('Y-m-d H:i:s');

                                        if ($yearly_inverter_detail_exist) {
                                            $yearly_inverter_detail_insertion_responce = $yearly_inverter_detail_exist->fill($yearly_input)->save();
                                        } else {
                                            $yearly_inverter_detail_insertion_responce = YearlyInverterDetail::create($yearly_input);
                                        }
                                    }
                                }

                                if($plant_final_processed_data->DeviceType == "MCMT"){
                                    $salteclatestData = DB::table('saltec_push_response')->where('site_id', $site->site_id)->where('device_type','MCMT')->orderBy('collect_time', "desc")->first();

                                    if ($salteclatestData) {

                                        $latestResponse = json_decode($salteclatestData->response);
                                        $latest_final_processed_data = $latestResponse->data;

                                        if (isset($latest_final_processed_data) && $latest_final_processed_data) {

                                            foreach ($latest_final_processed_data as $key18 => $plant_latest_final_processed_data) {

                                                if ($plant_latest_final_processed_data->DeviceType == "MCMT") {
                                                    $plant_final_processed_data = $plant_latest_final_processed_data;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }else if($plant_final_processed_data->DeviceType == "MGCE" || $plant_final_processed_data->DeviceType == "MGCW"){
                                    $salteclatestData = DB::table('saltec_push_response')
                                    ->where('site_id', $siteId)
                                    ->where('device_type', $plant_final_processed_data->DeviceType)
                                    ->orderBy('collect_time', 'desc');
                                    if ($plant_final_processed_data->DeviceType == 'MGCE') {
                                        $salteclatestData->skip(1); // offset by 1 to get the second last response
                                    }
                                    $salteclatestData = $salteclatestData->take(1) // get only 1 result
                                    ->value('response');
                    
                                if ($salteclatestData) {
                    
                                    $latestResponse = json_decode($salteclatestData);
                                    $latest_final_processed_data = $latestResponse->data;
                    
                                    if (isset($latest_final_processed_data) && $latest_final_processed_data) {
                                        $MGCEResponse = [];
                                        foreach ($latest_final_processed_data as $key18 => $plant_latest_final_processed_data) {
                    
                                            if($plant_latest_final_processed_data->DeviceType == "MGCE" || $plant_latest_final_processed_data->DeviceType == "MGCW") {
                                                $MGCEResponse =  (object)array_merge((array)$MGCEResponse, (array)$plant_latest_final_processed_data);
                                                $plant_final_processed_data = $MGCEResponse;
                                            }
                                        }
                                    }
                                }
                                } 

                                if($plant->parameter_use != null){
                                    $plantParameter = $plant->parameter_use;
                                    if (isset($plant_final_processed_data->Generated_Energy_kWh) && $plantParameter == "Generated_Energy_kWh") {
                                        $finalProccessedGeneration = $plant_final_processed_data->Generated_Energy_kWh;
                                        $finalProccessedConsumption = $plant_final_processed_data->Consumed_Energy_kWh;
                                        $finalProccessedImport = $plant_final_processed_data->Mains_Import_Energy_kWh;
                                        $finalProccessedExport = $plant_final_processed_data->Mains_Export_Energy_kWh;
                                    }else{
                                        $todayDailyData = SaltecDailyCumulativeData::where('plant_id', $plant->id)->where('site_id',$site->site_id)->whereDate('created_at', Date("Y-m-d"))->first();
                                        $finalProccessedGeneration = $todayDailyData->total_generation;
                                        $finalProccessedConsumption = $todayDailyData->total_consumption;
                                        $finalProccessedImport = $todayDailyData->total_bought;
                                        $finalProccessedExport = $todayDailyData->total_sell;
                                    }
                                    if(isset($plant_final_processed_data->solarEnergy) && $plantParameter == "solarEnergy") {
                                        $finalProccessedGeneration = $plant_final_processed_data->solarEnergy;
                                        $finalProccessedConsumption = $plant_final_processed_data->consumedEnergy;
                                        $finalProccessedImport = $plant_final_processed_data->importEnergy;
                                        $finalProccessedExport = $plant_final_processed_data->exportEnergy;
                                    }

                                }else{
                                    if (isset($plant_final_processed_data->Generated_Energy_kWh)) {
                                        $finalProccessedGeneration = $plant_final_processed_data->Generated_Energy_kWh;
                                        $finalProccessedConsumption = $plant_final_processed_data->Consumed_Energy_kWh;
                                        $finalProccessedImport = $plant_final_processed_data->Mains_Import_Energy_kWh;
                                        $finalProccessedExport = $plant_final_processed_data->Mains_Export_Energy_kWh;
                                    }else if(isset($plant_final_processed_data->solarEnergy) ) {
                                        $finalProccessedGeneration = $plant_final_processed_data->solarEnergy;
                                        $finalProccessedConsumption = $plant_final_processed_data->consumedEnergy;
                                        $finalProccessedImport = $plant_final_processed_data->importEnergy;
                                        $finalProccessedExport = $plant_final_processed_data->exportEnergy;
                                    }
                                }

                                // DailyProccessed
                                $Previoun_daily = SaltecDailyCumulativeData::where('plant_id', $plant->id)->where('site_id',$site->site_id)->whereDate('created_at', Date("Y-m-d", strtotime("-1 day")))->first();
                                if ($Previoun_daily) {
                                    $dailyGeneration += $finalProccessedGeneration - (isset($Previoun_daily->total_generation) ? $Previoun_daily->total_generation : 0);
                                    $dailyConsumption += $finalProccessedConsumption - (isset($Previoun_daily->total_consumption) ? $Previoun_daily->total_consumption : 0);
                                    $dailyImport += $finalProccessedImport - (isset($Previoun_daily->total_bought) ? $Previoun_daily->total_bought : 0);
                                    $dailyExport += $finalProccessedExport - (isset($Previoun_daily->total_sell) ? $Previoun_daily->total_sell : 0);
                                    $dailyGrid += $dailyImport > $dailyExport ? $dailyImport - $dailyExport : $dailyExport - $dailyImport;
                                    $dailySaving += $dailyGeneration * $benchmark;
                                }  else if(SaltecDailyCumulativeData::where('plant_id', $plant->id)->where('site_id',$site->site_id)->whereDate('created_at',"<=", Date("Y-m-d", strtotime("-1 day")))->orderBy('created_at',"DESC")->latest()->exists()){
                                    $Previoun_daily = SaltecDailyCumulativeData::where('plant_id', $plant->id)->where('site_id',$site->site_id)->whereDate('created_at',"<=", Date("Y-m-d", strtotime("-1 day")))->orderBy('created_at',"DESC")->latest()->first();
                                    $dailyGeneration += $finalProccessedGeneration - (isset($Previoun_daily->total_generation) ? $Previoun_daily->total_generation : 0);
                                    $dailyConsumption += $finalProccessedConsumption - (isset($Previoun_daily->total_consumption) ? $Previoun_daily->total_consumption : 0);
                                    $dailyImport += $finalProccessedImport - (isset($Previoun_daily->total_bought) ? $Previoun_daily->total_bought : 0);
                                    $dailyExport += $finalProccessedExport - (isset($Previoun_daily->total_sell) ? $Previoun_daily->total_sell : 0);
                                    $dailyGrid += $dailyImport > $dailyExport ? $dailyImport - $dailyExport : $dailyExport - $dailyImport;
                                    $dailySaving += $dailyGeneration * $benchmark;
                                }else{
                                    $dailyGeneration += $finalProccessedGeneration;
                                    $dailyConsumption += $finalProccessedConsumption;
                                    $dailyImport += $finalProccessedImport;
                                    $dailyExport += $finalProccessedExport;
                                    $dailyGrid += $dailyImport > $dailyExport ? $dailyImport - $dailyExport : $dailyExport - $dailyImport;
                                    $dailySaving += $dailyGeneration * $benchmark;
                                }

                                if (isset($plant_final_processed_data->Generated_Energy_kWh) || isset($plant_final_processed_data->solarEnergy)) {
                                    $cumulative_total_energy += $dailyGeneration;
                                    $cumulative_total_saving += $dailySaving;


                                    //Daily Total Processed Data
                                    $total_daily_generation_exist = SaltecDailyCumulativeData::where('plant_id', $plant->id)->where('site_id',$site->site_id)->whereDate('created_at', Date("Y-m-d", strtotime($plant_final_processed_data->Timestamp)))->first();
                                    $totalDailySaltecGeneration = $finalProccessedGeneration;
                                    $totalDailySaltecConsumption = $finalProccessedConsumption;
                                    $totalDailySaltecImport = $finalProccessedImport;
                                    $totalDailySaltecExport = $finalProccessedExport;
                                    $totalDailySaltecGrid = $totalDailySaltecImport > $totalDailySaltecExport ? $totalDailySaltecImport - $totalDailySaltecExport : $totalDailySaltecExport - $totalDailySaltecImport;
                                    $totalSaving = $totalDailySaltecGeneration * $benchmark;
                                    $input_gen1['plant_id'] = $plant->id;
                                    $input_gen1['site_id'] = $site->site_id;
                                    $input_gen1['total_generation'] = (double)$totalDailySaltecGeneration;
                                    $input_gen1['total_consumption'] = (double)$totalDailySaltecConsumption;
                                    $input_gen1['total_grid'] = (double)$totalDailySaltecGrid;
                                    $input_gen1['total_bought'] = (double)$totalDailySaltecImport;
                                    $input_gen1['total_sell'] = (double)$totalDailySaltecExport;
                                    $input_gen1['created_at'] = Date("Y-m-d H:i:s", strtotime($plant_final_processed_data->Timestamp));
                                    if ($total_daily_generation_exist) {
                                        $input_gen1['updated_at'] = date('Y-m-d H:i:s');
                                        $total_daily_generation_exist->fill($input_gen1)->save();
                                    } else {
                                        $input_gen1['plant_id'] = $plant->id;
                                        SaltecDailyCumulativeData::create($input_gen1);
                                    }
                                }

                                if ($plant->meter_type == "Microtech" || $plant->meter_type == "Microtech-Goodwe") {

                                    $curl = curl_init();
                
                                    curl_setopt_array($curl, array(
                                        CURLOPT_URL => "http://202.59.74.91:2030/authorization_service",
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => "",
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 4,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => "POST",
                                        CURLOPT_HTTPHEADER => array(
                                            "username: mdm",
                                            "password: admin786",
                                            "code: 71",
                                            "Content-Length: 0"
                                        ),
                                    ));
                
                                    $response = curl_exec($curl);
                                    $err = curl_error($curl);
                                    curl_close($curl);
                                    if ($err) {
                                        // echo "CURL Authentication Error 4 #:" . $err . $plant->id;
                                    }
                                    $res = json_decode($response);
                                    if ($res) {
                                        $privatekey = $res->privatekey;
                                    }
                
                                    if (isset($privatekey) && $privatekey) {
                
                                        if (MicrotechEnergyGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->exists()) {
                                            $latest_start_datetime = MicrotechEnergyGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->first();
                                            $latest_start_datetime = $latest_start_datetime ? $latest_start_datetime->db_datetime : $plant->created_at;
                                        } else {
                                            $latest_start_datetime = date('Y-m-d H:i:s',strtotime('-1 day'));
                                        }
                                        $data1 = [
                                            'global_device_id' => $plant->meter_serial_no,
                                            'start_datetime' => $latest_start_datetime,
                                            'end_datetime' => date('Y-m-d H:i:s'),
                                        ];
                
                                        //Proceess Data getting from Microtech Server
                                        $curl = curl_init();
                
                                        curl_setopt_array($curl, array(
                                            CURLOPT_URL => "http://202.59.74.91:2030/billing_data",
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => "",
                                            CURLOPT_MAXREDIRS => 10,
                                            CURLOPT_TIMEOUT => 10,
                                            CURLOPT_FOLLOWLOCATION => true,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => "POST",
                                            CURLOPT_POSTFIELDS => $data1,
                                            CURLOPT_HTTPHEADER => array(
                                                "Privatekey:" . $privatekey
                                            ),
                                        ));
                
                                        $response2 = curl_exec($curl);
                                        $err = curl_error($curl);
                                        curl_close($curl);
                
                                        if ($err) {
                                            // echo "cURL Error 5 #:" . $err ." Plant # ".$plant->id ;
                                        }
                                        $processed_data = json_decode($response2);
                
                                        if ($processed_data) {
                
                                            $final_processed_data_micro = $processed_data->data;
                
                                            foreach ($final_processed_data_micro as $key => $micro_data) {
                
                                                $generation_log_microtech['meter_serial_no'] = $plant->meter_serial_no;
                                                $generation_log_microtech['active_energy_pos_tl'] = $micro_data->active_energy_pos_tl;
                                                $generation_log_microtech['active_energy_neg_tl'] = $micro_data->active_energy_neg_tl;
                                                $generation_log_microtech['active_energy_abs_tl'] = $micro_data->active_energy_abs_tl;
                                                $generation_log_microtech['reactive_energy_pos_tl'] = $micro_data->reactive_energy_pos_tl;
                                                $generation_log_microtech['reactive_energy_neg_tl'] = $micro_data->reactive_energy_neg_tl;
                                                $generation_log_microtech['reactive_energy_abs_tl'] = $micro_data->reactive_energy_abs_tl;
                                                $generation_log_microtech['active_mdi_pos_tl'] = $micro_data->active_mdi_pos_tl;
                                                $generation_log_microtech['active_mdi_neg_tl'] = $micro_data->active_mdi_neg_tl;
                                                $generation_log_microtech['active_mdi_abs_tl'] = $micro_data->active_mdi_abs_tl;
                                                $generation_log_microtech['cumulative_mdi_pos_tl'] = $micro_data->cumulative_mdi_pos_tl;
                                                $generation_log_microtech['cumulative_mdi_neg_tl'] = $micro_data->cumulative_mdi_neg_tl;
                                                $generation_log_microtech['cumulative_mdi_abs_tl'] = $micro_data->cumulative_mdi_abs_tl;
                                                $generation_log_microtech['meter_datetime'] = $micro_data->meter_datetime;
                                                $generation_log_microtech['mdc_read_datetime'] = $micro_data->mdc_read_datetime;
                                                $generation_log_microtech['db_datetime'] = $micro_data->db_datetime;
                
                                                $generation_log_microtech_response = MicrotechEnergyGenerationLog::create($generation_log_microtech);
                
                                            }
                
                                        }
                
                                        if (MicrotechPowerGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->exists()) {
                                            $latest_start_datetime = MicrotechPowerGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->first();
                                            $latest_start_datetime = $latest_start_datetime ? $latest_start_datetime->db_datetime : $plant->created_at;
                
                                        } else {
                                            $latest_start_datetime = date('Y-m-d H:i:s',strtotime('-1 day'));
                                        }
                                        $data1 = [
                                            'global_device_id' => $plant->meter_serial_no,
                                            // 'start_datetime' => $plant->created_at,
                                            'start_datetime' => $latest_start_datetime,
                                            'end_datetime' => date('Y-m-d H:i:s'),
                                        ];
                
                                        //Proceess Data getting from Microtech Server
                                        $curl = curl_init();
                
                                        curl_setopt_array($curl, array(
                                            CURLOPT_URL => "http://202.59.74.91:2030/instantaneous_data",
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => "",
                                            CURLOPT_MAXREDIRS => 10,
                                            CURLOPT_TIMEOUT => 4,
                                            CURLOPT_FOLLOWLOCATION => true,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => "POST",
                                            CURLOPT_POSTFIELDS => $data1,
                                            CURLOPT_HTTPHEADER => array(
                                                "Privatekey:" . $privatekey
                                            ),
                                        ));
                
                                        $response2 = curl_exec($curl);
                                        $err = curl_error($curl);
                                        curl_close($curl);
                
                                        if ($err) {
                                            // echo "cURL Error 6 #:" . $err;
                                        }
                                        $processed_data = json_decode($response2);
                                        //$final_processed_data_micro = json_decode($response2, true)['data'];
                
                                        if ($processed_data) {
                
                                            $final_processed_data_micro = $processed_data->data;
                
                                            foreach ($final_processed_data_micro as $key => $micro_data) {
                
                                                $generation_log_microtech['meter_serial_no'] = $plant->meter_serial_no;
                                                $generation_log_microtech['current_tariff_register'] = $micro_data->current_tariff_register;
                                                $generation_log_microtech['signal_strength'] = $micro_data->signal_strength;
                                                $generation_log_microtech['frequency'] = $micro_data->frequency;
                                                $generation_log_microtech['meter_datetime'] = $micro_data->meter_datetime;
                                                $generation_log_microtech['current_phase_a'] = $micro_data->current_phase_a;
                                                $generation_log_microtech['current_phase_b'] = $micro_data->current_phase_b;
                                                $generation_log_microtech['current_phase_c'] = $micro_data->current_phase_c;
                                                $generation_log_microtech['voltage_phase_a'] = $micro_data->voltage_phase_a;
                                                $generation_log_microtech['voltage_phase_b'] = $micro_data->voltage_phase_b;
                                                $generation_log_microtech['voltage_phase_c'] = $micro_data->voltage_phase_c;
                                                $generation_log_microtech['aggregate_active_pwr_pos'] = $micro_data->aggregate_active_pwr_pos;
                                                $generation_log_microtech['aggregate_active_pwr_neg'] = $micro_data->aggregate_active_pwr_neg;
                                                $generation_log_microtech['aggregate_reactive_pwr_pos'] = $micro_data->aggregate_reactive_pwr_pos;
                                                $generation_log_microtech['aggregate_reactive_pwr_neg'] = $micro_data->aggregate_reactive_pwr_neg;
                                                $generation_log_microtech['average_pf'] = $micro_data->average_pf;
                                                $generation_log_microtech['mdc_read_datetime'] = $micro_data->mdc_read_datetime;
                                                $generation_log_microtech['db_datetime'] = $micro_data->db_datetime;
                
                                                $generation_log_microtech_response = MicrotechPowerGenerationLog::create($generation_log_microtech);
                
                                            }
                                        }
                
                                    }
                
                                    //Daily Consumption Value
                                    $latest_micro_daily_record = MicrotechEnergyGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->whereDate('db_datetime', date('Y-m-d'))->orderBy('db_datetime', 'DESC')->first();
                                    $previous_micro_daily_record = MicrotechEnergyGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->whereDate('db_datetime', date('Y-m-d', strtotime("-1 days")))->orderBy('db_datetime', 'DESC')->first();
                                    $latest_energy_pos_tl_data = $latest_micro_daily_record ? $latest_micro_daily_record->active_energy_pos_tl : 0;
                                    $latest_energy_neg_tl_data = $latest_micro_daily_record ? $latest_micro_daily_record->active_energy_neg_tl : 0;
                                    $previous_energy_pos_tl_data = $previous_micro_daily_record ? $previous_micro_daily_record->active_energy_pos_tl : 0;
                                    $previous_energy_neg_tl_data = $previous_micro_daily_record ? $previous_micro_daily_record->active_energy_neg_tl : 0;
                
                                    $daily_active_energy_pos_tl = $latest_energy_pos_tl_data - $previous_energy_pos_tl_data > 0 ? $latest_energy_pos_tl_data - $previous_energy_pos_tl_data : 0;
                                    $daily_active_energy_neg_tl = $latest_energy_neg_tl_data - $previous_energy_neg_tl_data > 0 ? $latest_energy_neg_tl_data - $previous_energy_neg_tl_data : 0;
                
                                    $dailyImport = 0;
                                    $dailyExport = 0;
                                    $dailyGrid = 0;
                                    $dailyConsumption = 0;
                
                                    $dailyImport = $daily_active_energy_pos_tl * $plant->ratio_factor;
                                    $dailyExport = $daily_active_energy_neg_tl * $plant->ratio_factor;
                                    $dailyGrid = $dailyImport > $dailyExport ? $dailyImport - $dailyExport : $dailyExport - $dailyImport;
                                    $dailyConsumption = ($dailyGeneration + ($dailyImport - $dailyExport)) > 0 ? ($dailyGeneration + ($dailyImport - $dailyExport)) : 0;
                
                                } else {
                                    $curr_gen = isset($plant_final_processed_data->totalInverterPower) ? $plant_final_processed_data->totalInverterPower : 0;
                                    $curr_con = isset($plant_final_processed_data->totalLoadPower) ? $plant_final_processed_data->totalLoadPower : 0;
                                    $curr_grid = isset($plant_final_processed_data->Total_Active_Power_kW) ? $plant_final_processed_data->Total_Active_Power_kW : 0;
                                    $tot_energy = 0;
                                    $curr_saving = (isset($plant_final_processed_data->totalInverterPower) ? $plant_final_processed_data->totalInverterPower : 0) * $benchmark;
                                }
                                $microtech_response_data = MicrotechPowerGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->whereDate('db_datetime', Date("Y-m-d"))->orderBy('db_datetime', 'asc')->get();
                
                                if (($plant->meter_type == "Microtech" || $plant->meter_type == "Microtech-Goodwe")  && count($microtech_response_data) > 0) {

                                    foreach ($microtech_response_data as $key22 => $micro_power_data) {
                                        $curr_gen = 0;
                                        $meterTime = Date("Y-m-d H:i:s", strtotime($micro_power_data->db_datetime) - (10 * 60));
            
                                        if ($plant_final_processed_data->DeviceType == "MCMT"){
                                            $PushfinalResponse = DB::table('saltec_push_response')->where('site_id', $site->site_id)->whereBetween('collect_time', [$meterTime, $micro_power_data->db_datetime])->orderBy('collect_time', 'Desc')->first();
                                            if ($PushfinalResponse) {
                                                $responseFinal = json_decode($PushfinalResponse->response);
                                                foreach ($responseFinal->data as $key => $device_response) {
                                                    if ($device_response->DeviceType == "MCMT") {
                                                        $MCMT_Logger = $device_response;
                                                        break;
                                                    }
                                                }
                                                $curr_gen = (double)isset($MCMT_Logger->totalInverterPower) ? $MCMT_Logger->totalInverterPower : 0;
                                                $tot_energy = (double)isset($MCMT_Logger->Generated_Energy_kWh) ? $MCMT_Logger->Generated_Energy_kWh : 0;
                                            }
                                        }else if($plant_final_processed_data->DeviceType == "MGCE" || $plant_final_processed_data->DeviceType == "MGCW"){
                                            $PushfinalResponse = DB::table('saltec_push_response')
                                            ->where('site_id', $siteId)
                                            ->whereBetween('collect_time', [$meterTime, $micro_power_data->db_datetime])
                                            ->orderBy('collect_time', 'desc');
                                            if ($plant_final_processed_data->DeviceType == 'MGCE') {
                                                $PushfinalResponse->skip(1); // offset by 1 to get the second last response
                                            }
                                            $PushfinalResponse = $PushfinalResponse->take(1) // get only 1 result
                                            ->value('response');
                                            if ($PushfinalResponse) {
                                                $responseFinal = json_decode($PushfinalResponse);
                                                $MGCELoggerResponse = [];
                                                foreach ($responseFinal->data as $key => $device_response) {
                                                    if($device_response->DeviceType == "MGCE" || $device_response->DeviceType == "MGCW") {
                                                        $MGCELoggerResponse =  (object)array_merge((array)$MGCELoggerResponse, (array)$device_response);
                                                        $MCMT_Logger = $MGCELoggerResponse;
                                                    }
                                                }
                    
                                                $curr_gen = (double)isset($MCMT_Logger->totalInverterPower) ? $MCMT_Logger->totalInverterPower : 0;
                                                $tot_energy = (double)isset($MCMT_Logger->Generated_Energy_kWh) ? $MCMT_Logger->Generated_Energy_kWh : 0;
                                            }
                                        }

            
                                        if ($micro_power_data->aggregate_active_pwr_pos >= $micro_power_data->aggregate_active_pwr_neg) {
            
                                            $curr_grid = $micro_power_data->aggregate_active_pwr_pos * $plant->ratio_factor;
                                            $curr_con = $curr_gen + $curr_grid;
                                            $generation_log_data['grid_type'] = '+ve';
                                        } else {
                                            $curr_grid = $micro_power_data->aggregate_active_pwr_neg * $plant->ratio_factor;
                                            $curr_con = ($curr_gen - $curr_grid) > 0 ? $curr_gen - $curr_grid : 0;
                                            $generation_log_data['grid_type'] = '-ve';
                                            $plantStatus = Plant::where('id', $plant->id)->first('is_online');
                                            if ($plantStatus->is_online == 'N' && $curr_gen == 0) {
                                                $curr_grid = 0;
                                            }
                                        }
            
                                        $generation_log_data['plant_id'] = $plant->id;
                                        $generation_log_data['siteId'] = $site->site_id;
                                        $generation_log_data['current_generation'] = $curr_gen;
                                        $generation_log_data['current_consumption'] = $curr_con;
                                        $generation_log_data['current_grid'] = abs($curr_grid);
                                        $generation_log_data['totalEnergy'] = $dailyGeneration;
                                        $generation_log_data['current_saving'] = (double)$curr_gen * (double)$plant->benchmark_price;
                                        $generation_log_data['collect_time'] = $micro_power_data->db_datetime;
                                        $check_time_diffrnc = GenerationLog::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('collect_time', 'LIKE', date('Y-m-d H:i', strtotime($micro_power_data->db_datetime)) . '%')->first();
                                        if (!$check_time_diffrnc) {
            
                                            $processed_current_variable_response = GenerationLog::create($generation_log_data);
                                        }
                                    }

                
                                } else {
                
                                    $PushfinalResponsedPlant = DB::table('saltec_push_response')->where('site_id', $site->site_id)->where('status', 'N')->orderBy('collect_time', 'Desc')->get();
                
                                    foreach ($PushfinalResponsedPlant as $key22 => $PushfinalResponse) {
                
                                        $responseFinal = json_decode($PushfinalResponse->response);
                                        $MGCELoggerResponse = [];
                                        foreach ($responseFinal->data as $key => $device_response) {
                                            $MCMT_Logger = "";
                                            if ($device_response->DeviceType == "MCMT") {
                                                $MCMT_Logger = $device_response;
                                                break;
                                            }
                                            if($device_response->DeviceType == "MGCE" || $device_response->DeviceType == "MGCW") {
                                                $MGCELoggerResponse =  (object)array_merge((array)$MGCELoggerResponse, (array)$device_response);
                                                $MCMT_Logger = $MGCELoggerResponse;
                                            }
                                        }
                                        if (isset($MCMT_Logger->Timestamp) && $MCMT_Logger->Timestamp) {
                
                                            $curr_gen = isset($MCMT_Logger->totalInverterPower) ? $MCMT_Logger->totalInverterPower : 0;
                                            $curr_con = isset($MCMT_Logger->totalLoadPower) ? $MCMT_Logger->totalLoadPower : 0;
                                            $curr_grid = isset($MCMT_Logger->Total_Active_Power_kW) ? $MCMT_Logger->Total_Active_Power_kW : 0;
                                            if ($curr_grid > 0) {
                                                $generation_log_data['grid_type'] = '+ve';
                                            } else {
                                                $generation_log_data['grid_type'] = '-ve';
                                            }
                                            $tot_energy = 0;
                                            $curr_saving = (isset($MCMT_Logger->totalInverterPower) ? $MCMT_Logger->totalInverterPower : 0) * $benchmark;
                                            $data_collect_time = $MCMT_Logger->Timestamp;
                                            $generation_log_data['plant_id'] = $plant->id;
                                            $generation_log_data['siteId'] = $site->site_id;
                                            $generation_log_data['current_generation'] = $curr_gen;
                                            $generation_log_data['current_consumption'] = $curr_con;
                                            $generation_log_data['current_grid'] = abs($curr_grid);
                                            $generation_log_data['totalEnergy'] = $tot_energy;
                                            $generation_log_data['current_saving'] = (double)$curr_saving;
                                            $generation_log_data['processed_cron_job_id'] = $processed_cron_job_id + 1;
                                            $generation_log_data['collect_time'] = $data_collect_time;
                                            $generation_log_data['created'] = $generation_log_created_time;
                                            $generation_log_data['updated_at'] = $generation_log_created_time;
                
                                            $check_time_diffrnc = GenerationLog::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('collect_time', 'LIKE', date('Y-m-d H:i', strtotime($data_collect_time)) . '%')->first();
                
                                            if (!$check_time_diffrnc) {
                
                                                $processed_current_variable_response = GenerationLog::create($generation_log_data);
                                            }
                                        }
                                    }
                                }
                                if($plant_final_processed_data->DeviceType == "MGCW" || $plant_final_processed_data->DeviceType == "MGCE"){
                                    break;
                                }
                            }
                        }
                    }
                    $data = DB::table('saltec_push_response')->where('id', $data->id)->where('site_id', $site->site_id)->where('status', "N")->update(["status" => "Y"]);
                }
            }
                //ProcessedCurrentVariable
                $generationLogData = GenerationLog::where('plant_id', $plant->id)->where('siteId',$siteId)->whereDate('collect_time', Date('Y-m-d'))->get();

                if ($generationLogData) {
                    foreach($generationLogData as $key => $log_data){
                        $startTime = date('Y-m-d H:i:s', strtotime("$log_data->collect_time -1 minute"));
                        $endTime = date('Y-m-d H:i:s', strtotime("$log_data->collect_time +1 minute"));
                        $generationLogData = GenerationLog::where('plant_id', $plant->id)->whereBetween('collect_time',[$startTime,$endTime])->groupBy('siteId')->get();
                        $processed_curr_data['plant_id'] = $plant->id;
                        $processed_curr_data['current_generation'] = $generationLogData->sum('current_generation');
                        $processed_curr_data['current_consumption'] = $generationLogData->sum('current_consumption');
                        $processed_curr_data['current_grid'] = abs($generationLogData->sum('current_grid'));
                        // $processed_curr_data['totalEnergy'] = $tot_energy;
                        $processed_curr_data['current_saving'] = (double)$generationLogData->sum('current_generation') * (double)$plant->benchmark_price;
                        $processed_curr_data['collect_time'] = $log_data->collect_time;
    
                        $check_time_diffrnc = ProcessedCurrentVariable::where('plant_id', $plant->id)->where('collect_time', 'LIKE', date('Y-m-d H:i', strtotime($log_data->collect_time)) . '%')->first();
                        if (!$check_time_diffrnc) {
    
                            $processed_current_variable_response = ProcessedCurrentVariable::create($processed_curr_data);
                        }

                    }
                }
                //Daily Processed Data
                $daily_processed['plant_id'] = $plant->id;
                $daily_processed['dailyGeneration'] = round((double)$dailyGeneration, 2);
                $daily_processed['dailyConsumption'] = round((double)$dailyConsumption, 2);
                $daily_processed['dailyGridPower'] = round((double)$dailyGrid, 2);
                $daily_processed['dailyBoughtEnergy'] = $dailyImport >= 0 ? round((double)$dailyImport, 2) : 0;
                $daily_processed['dailySellEnergy'] = $dailyExport >= 0 ? round((double)$dailyExport, 2) : 0;
                $daily_processed['dailyMaxSolarPower'] = 0;
                $daily_processed['dailySaving'] = (double)$dailySaving;
                $daily_processed['lastUpdated'] = Date('Ymd') . 'T' . Date('His');
                $daily_processed['updated_at'] = Date('Y-m-d H:i:s');
                $processed_plant_detail_exist = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', '=', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
                if ($processed_plant_detail_exist != null) {
                    $processed_plant_detail_insertion_responce = $processed_plant_detail_exist->fill($daily_processed)->save();
                } else {

                    $daily_processed['created_at'] = Date('Y-m-d H:i:s');
                    $processed_plant_detail_insertion_responce = DailyProcessedPlantDetail::create($daily_processed);
                }
                // MonthlyProccessed
                $monthlyGeneration = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailyGeneration');;
                $monthlyConsumption = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailyConsumption');
                $monthlyImport = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailyBoughtEnergy');
                $monthlyExport = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailySellEnergy');
                $monthlyGrid = $monthlyImport > $monthlyExport ? $monthlyImport - $monthlyExport : $monthlyExport - $monthlyImport;
                $monthlySaving = $monthlyGeneration * $benchmark;
                //Monthly Processed Data
                $monthly_processed['plant_id'] = $plant->id;
                $monthly_processed['monthlyGeneration'] = isset($monthlyGeneration) ? round((double)$monthlyGeneration, 2) : 0;
                $monthly_processed['monthlyConsumption'] = isset($monthlyConsumption) ? round((double)$monthlyConsumption, 2) : 0;
                $monthly_processed['monthlyGridPower'] = isset($monthlyGrid) ? round((double)$monthlyGrid, 2) : 0;
                $monthly_processed['monthlyBoughtEnergy'] = $monthlyImport >= 0 ? round((double)$monthlyImport, 2) : 0;
                $monthly_processed['monthlySellEnergy'] = $monthlyExport >= 0 ? round((double)$monthlyExport, 2) : 0;
                $monthly_processed['monthlyMaxSolarPower'] = 0;
                $monthly_processed['monthlySaving'] = isset($monthlySaving) ? (double)$monthlySaving : 0;
                $monthly_processed['lastUpdated'] = Date('Ymd') . 'T' . Date('His');
                $monthly_processed['updated_at'] = Date('Y-m-d H:i:s');
                $processed_plant_detail_exist = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
                if ($processed_plant_detail_exist != null) {
                    $processed_plant_detail_insertion_responce = $processed_plant_detail_exist->fill($monthly_processed)->save();
                } else {
                    $monthly_processed['created_at'] = Date('Y-m-d H:i:s');
                    $processed_plant_detail_insertion_responce = MonthlyProcessedPlantDetail::create($monthly_processed);
                }
                
                //YearlyProccessed
                $yearlyGeneration = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->sum('monthlyGeneration');
                $yearlyConsumption = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->sum('monthlyConsumption');
                $yearlyImport = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->sum('monthlyBoughtEnergy');
                $yearlyExport = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->sum('monthlySellEnergy');
                $yearlyGrid = $yearlyImport > $yearlyExport ? $yearlyImport - $yearlyExport : $yearlyExport - $yearlyImport;
                $yearlySaving = $yearlyGeneration * $benchmark;
                
                $yearly_processed['plant_id'] = $plant->id;
                $yearly_processed['yearlyGeneration'] = round((double)$yearlyGeneration, 2);
                $yearly_processed['yearlyConsumption'] = round((double)$yearlyConsumption, 2);
                $yearly_processed['yearlyGridPower'] = round((double)$yearlyGrid, 2);
                $yearly_processed['yearlyBoughtEnergy'] = $yearlyImport >= 0 ? round((double)$yearlyImport, 2) : 0;
                $yearly_processed['yearlySellEnergy'] = $yearlyExport >= 0 ? round((double)$yearlyExport, 2) : 0;
                $yearly_processed['yearlyMaxSolarPower'] = 0;
                $yearly_processed['yearlySaving'] = (double)$yearlySaving;
                $yearly_processed['lastUpdated'] = Date('Ymd') . 'T' . Date('His');
                $yearly_processed['updated_at'] = Date('Y-m-d H:i:s');
                $processed_plant_detail_exist = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->first();
                if ($processed_plant_detail_exist != null) {
                    $processed_plant_detail_insertion_responce = $processed_plant_detail_exist->fill($yearly_processed)->save();
                } else {
                    $yearly_processed['created_at'] = Date('Y-m-d H:i:s');
                    $processed_plant_detail_insertion_responce = YearlyProcessedPlantDetail::create($yearly_processed);
                }
                
                //Total Processed Data
                $total_generation_exist = TotalProcessedPlantDetail::where('plant_id', $plant->id)->first();
                $envReduction = Setting::where('perimeter', 'env_reduction')->pluck('value')[0];
                $totalGeneration = YearlyProcessedPlantDetail::where('plant_id',$plant->id)->sum('yearlyGeneration');
                $totalConsumption =  YearlyProcessedPlantDetail::where('plant_id',$plant->id)->sum('yearlyConsumption');
                $totalGrid = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->sum('yearlyGridPower');
                $totalImport = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->sum('yearlyBoughtEnergy');
                $totalExport = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->sum('yearlySellEnergy');

                $totalSaving = $totalGeneration * $benchmark;
                $input_gen['plant_total_generation'] = (double)$totalGeneration;
                $input_gen['plant_total_consumption'] = (double)$totalConsumption;
                $input_gen['plant_total_grid'] = (double)$totalGrid;
                $input_gen['plant_total_buy_energy'] = (double)$totalImport;
                $input_gen['plant_total_sell_energy'] = (double)$totalExport;
                $input_gen['plant_total_saving'] = (double)$totalSaving;
                $input_gen['plant_total_reduction'] = (double)$totalGeneration * (double)$envReduction;

                if ($total_generation_exist) {

                    $input_gen['updated_at'] = date('Y-m-d H:i:s');

                    $res = $total_generation_exist->fill($input_gen)->save();
                } else {

                    $input_gen['plant_id'] = $plant->id;

                    $res = TotalProcessedPlantDetail::create($input_gen);
                }
        }
        
        $Cumulative_Plants_Data_Exsist = null;
        $Cumulative_Plants_Data['total_energy'] = $cumulative_total_energy;
        $Cumulative_Plants_Data['total_saving'] = $cumulative_total_saving;
        if ($Cumulative_Plants_Data_Exsist != null) {
            $processed_all_plant = $Cumulative_Plants_Data_Exsist->fill($Cumulative_Plants_Data)->save();
        } else {
            $processed_all_plant = AllPlantsCumulativeData::create($Cumulative_Plants_Data);
        }

        $CronJobDetail = CronJobTime::where('id', $cronJobTime->id)->first();
        $CronJobDetail->end_time = date('Y-m-d H:i:s');
        $CronJobDetail->status = 'completed';
        $CronJobDetail->save();
        return "All Data Transfer Successfully";
    }

    function saltecss()
    {

//        $allPlants = DB::table('saltec_push_response_history')->whereDate('collect_time',Date('Y-m-d'))->groupBy('site_id')->pluck('site_id');
        $allPlants = DB::table('saltec_push_response_history')->groupBy('site_id')->pluck('site_id')->toArray();
        $alreadyBuildPlant = PlantSite::pluck('site_id')->toArray();
        $result = array_diff($allPlants, $alreadyBuildPlant);
        $indexesresult = array_values($result);
//        print_r($indexesresult);
        return [$indexesresult, $result, $allPlants, $alreadyBuildPlant];

    }
    function faultAndAlarm(){

        
        date_default_timezone_set('Asia/Karachi');

        // $cronJobTime->start_time = $generation_log_created_time;
        // $cronJobTime->type = 'Saltec/Microtech Fault And Alarm';
        // $cronJobTime->status = 'in-progress';
        // $cronJobTime->processed_cron_job_id = $processedCronJobId + 1;
        // $cronJobTime->save();
        $all_plants = Plant::whereIn('meter_type', ['Microtech', 'Saltec', 'Saltec-Goodwe', 'Microtech-Goodwe'])->where('id',881)->get();

        foreach ($all_plants as $plant) {

            $plantSites = PlantSite::where('plant_id', $plant->id)->get();

            foreach ($plantSites as $key1 => $site) {

                $data = DB::table('saltec_push_response')->where('site_id', $site->site_id)->whereDate('collect_time',Date('Y-m-d'))->orderBy('collect_time',"desc")->first();
// return json_encode($data,true);
                if ($data) {

                    $response = json_decode($data->response);
                    $final_processed_data = $response->data;

                    if (isset($final_processed_data) && $final_processed_data) {
                        $MSGWResponse = [];
                        foreach ($final_processed_data as $key => $plant_final_processed_data) {
                            if($plant_final_processed_data->DeviceType == "MSGW" || $plant_final_processed_data->DeviceType == "MH2M" ) {
                                $MSGWResponse =  (object)array_merge((array)$MSGWResponse, (array)$plant_final_processed_data);
                                $inverterData = $MSGWResponse;
                            }

                        }
print_r($inverterData);
exit();
                        $user_ids = PlantUser::where('plant_id', $plant->id)->get();

                        if (isset($inverterData->OutputType)/* && $inverters_data['dv_Inv'.$i.'OutputType'] != null*/) {

                            if ($inverterData->OutputType > 2) {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->OutputType)->where('api_param', 'dv_InvOutputType')->first();
                                if ($fault_data) {

                                    $this->faults_data_insertion($inverterData->Timestamp, 'dv_Inv1', $plant->id, $inverterData, $fault_data, $inverterData->OutputType, $user_ids);
                                }
                            } else {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->OutputType)->where('api_param', 'dv_InvOutputType')->first();
                                if ($fault_data) {

                                    $this->faults_data_updation($inverterData->Timestamp, $plant->id, $fault_data);
                                }
                            }
                        }
                        if (isset($inverterData->FaultCode)/* && $inverters_data['dv_Inv'.$i.'FaultCode'] != null*/) {
                            if ($inverterData->FaultCode > 0) {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->FaultCode)->where('api_param', 'dv_InvFaultCode')->first();
                                if ($fault_data) {

                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv1', $plant->id, $inverters_data, $fault_data, $inverterData->FaultCode, $user_ids);
                                }
                            } else {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->FaultCode)->where('api_param', 'dv_InvFaultCode')->first();
                                if ($fault_data) {

                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                }
                            }
                        }

                        if (isset($inverterData->PIDAlarmCode)/* && $inverters_data['dv_Inv'.$i.'PIDAlarmCode'] != null*/) {

                            if ($inverterData->PIDAlarmCode > 0) {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->PIDAlarmCode)->where('api_param', 'dv_InvPIDAlarmCode')->first();
                                if ($fault_data) {

                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv1', $plant->id, $inverters_data, $fault_data, $inverterData->PIDAlarmCode, $user_ids);
                                }
                            } else {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->PIDAlarmCode)->where('api_param', 'dv_InvPIDAlarmCode')->first();
                                if ($fault_data) {

                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                }
                            }
                        }

                        if (isset($inverterData->PIDWorkState)/* && $inverters_data['dv_Inv'.$i.'PIDWorkState'] != null*/) {

                            if ($inverterData->PIDWorkState > 0) {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->PIDWorkState)->where('api_param', 'dv_InvPIDWorkState')->first();
                                if ($fault_data) {

                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv1', $plant->id, $inverters_data, $fault_data, $inverterData->PIDWorkState, $user_ids);
                                }
                            } else {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->PIDWorkState)->where('api_param', 'dv_InvPIDWorkState')->first();
                                if ($fault_data) {

                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                }
                            }
                        }

                        if (isset($inverterData->WorkState1)/* && $inverters_data['dv_Inv'.$i.'WorkState1'] != null*/) {

                            if ($inverterData->WorkState1 > 0) {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->WorkState1)->where('api_param', 'dv_InvWorkState1')->first();
                                if ($fault_data) {

                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv1', $plant->id, $inverters_data, $fault_data, $inverterData->WorkState1, $user_ids);
                                }
                            } else {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->WorkState1)->where('api_param', 'dv_InvWorkState1')->first();
                                if ($fault_data) {

                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                }
                            }
                        }

                        if (isset($inverterData->CommFail)/* && $inverters_data['inverter'.$i.'CommFail'] != null*/) {

                            if ($inverterData->CommFail > 0) {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->CommFail)->where('api_param', 'inverterCommFail')->first();
                                if ($fault_data) {

                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv1', $plant->id, $inverters_data, $fault_data, $inverterData->CommFail, $user_ids);
                                }
                            } else {
                                $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->CommFail)->where('api_param', 'inverterCommFail')->first();
                                if ($fault_data) {

                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                }
                            }
                        }
                        
                        if ($plant->system_type != 1) {

                            if (isset($inverterData->exportLimitEnabled)/* && $inverters_data['exportLimitEnabled'] != null*/) {

                                if ($inverterData->exportLimitEnabled > 0) {
                                    $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->exportLimitEnabled)->where('api_param', 'exportLimitEnabled')->first();
                                    if ($fault_data) {

                                        $this->faults_data_insertion($generation_log_created_time, 'dv_Inv1', $plant->id, $inverters_data, $fault_data, 'exportLimitEnabled', $user_ids);
                                    }
                                } else {
                                    $fault_data = FaultAndAlarm::where('alarm_code', $inverterData->exportLimitEnabled)->where('api_param', 'exportLimitEnabled')->first();
                                    if ($fault_data) {

                                        $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                    }
                                }
                            }
                        }

                    }
                }
                return "okay";
            }
        }
        // $CronJobDetail = CronJobTime::where('id', $cronJobTime->id)->first();
        // $CronJobDetail->end_time = date('Y-m-d H:i:s');
        // $CronJobDetail->status = 'completed';
        // $CronJobDetail->save();
    }

    
    public function faults_data_insertion($curr_Date, $dv_inv, $plant_id, $inverters_data, $fault_data, $string, $users)
    {
        $curr_Date = date('Y-m-d H:i:s');

        $existing_data = DB::table('fault_and_alarms')
            ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
            ->select('fault_alarm_log.plant_id', 'fault_alarm_log.fault_and_alarm_id',
                'fault_alarm_log.siteId', 'fault_alarm_log.status', 'fault_alarm_log.created_at',
                'fault_and_alarms.id', 'fault_and_alarms.alarm_code')
            ->where(['fault_alarm_log.plant_id' => $plant_id, 'fault_alarm_log.fault_and_alarm_id' => $fault_data['id'], 'fault_and_alarms.alarm_code' => $fault_data['alarm_code'], 'fault_alarm_log.status' => 'Y'])
            ->orderBy('fault_alarm_log.created_at', 'DESC')
            ->first();

        if ($existing_data == null) {
            $fault_log['plant_id'] = $plant_id;
            $fault_log['siteId'] = $inverters_data->siteId;
            $fault_log['dv_inverter'] = $dv_inv;
            $fault_log['fault_and_alarm_id'] = $fault_data['id'];
            $fault_log['status'] = $string > 0 ? 'Y' : 'N';
            $fault_log['lastUpdated'] = $inverters_data->Timestamp;
            $fault_log['created_at'] = $curr_Date;
            $fault_log['updated_at'] = NUll;
            // dd($fault_log);
            $fault_log_responce = FaultAlarmLog::create($fault_log);
            //dd($fault_log_responce);

            if ($users && $fault_data['type'] != 'Status') {
                foreach ($users as $key => $user) {
                    $notification['plant_id'] = $plant_id;
                    $notification['user_id'] = $user['user_id'];
                    $notification['fault_and_alarm_id'] = $fault_data['id'];
                    $notification['title'] = $fault_data['type'];
                    $notification['description'] = $fault_data['description'];
                    $notification['entry_date'] = $curr_Date;
                    $notification['schedule_date'] = $curr_Date;
                    $notification['notification_type'] = $fault_data['severity'];
                    $notification['alarm_log_id'] = $fault_log_responce->id;
                    $notification['is_msg_app'] = 'Y';
                    $notification['is_msg_sms'] = 'N';
                    $notification['is_msg_email'] = 'N';
                    $notification['is_notification_required'] = 'N';
                    //dd($notification);
                    $notification_responce = Notification::create($notification);
                }
            }
        }
    }
    public
    function faults_data_updation($curr_Date, $plant_id, $fault_data)
    {
        $curr_Date = date('Y-m-d H:i:s');

        $existing_data = DB::table('fault_alarm_log')
            ->join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
            ->select('fault_alarm_log.id as log_id', 'fault_alarm_log.plant_id', 'fault_alarm_log.fault_and_alarm_id',
                'fault_alarm_log.siteId', 'fault_alarm_log.dv_inverter', 'fault_alarm_log.status', 'fault_alarm_log.created_at',
                'fault_and_alarms.id', 'fault_and_alarms.alarm_code', 'fault_and_alarms.api_param')
            ->where(['fault_alarm_log.plant_id' => $plant_id, 'fault_and_alarms.api_param' => $fault_data['api_param'], 'fault_alarm_log.status' => 'Y'])
            ->orderBy('fault_alarm_log.created_at', 'DESC')
            ->first();

        if ($existing_data != null) {
            $fault_obj = FaultAlarmLog::findOrFail($existing_data->log_id);

            $fault_obj->plant_id = $existing_data->plant_id;
            $fault_obj->siteId = $existing_data->siteId;
            $fault_obj->dv_inverter = $existing_data->dv_inverter;
            $fault_obj->fault_and_alarm_id = $existing_data->fault_and_alarm_id;
            $fault_obj->status = 'N';
            $fault_obj->created_at = $existing_data->created_at;
            $fault_obj->updated_at = $curr_Date;

            $fault_obj->save();
        }
        return true;
    }

    function newSite(){
	 $plantSiteAll = DB::table('saltec_push_response')->groupBy('site_id')->pluck('site_id')->toArray();
	 $plantSites = PlantSite::pluck('site_id')->toArray();
         $unbuildSites = array_diff($plantSiteAll,$plantSites);
	 $allunBuildSites = array_values($unbuildSites);
	 foreach($allunBuildSites as $unbuildSite){
	    $plantSite = new UnBuildSites;
	    $plantSite->site_id = $unbuildSite;
	    $plantSite->updated_by_at = Date('Y-m-d H:i:s');
	    $plantSite->save();
	}
	return "All Unbuild Sites are done";
    }
    function UpdatePrevDayData(){

        $all_plants = Plant::whereIn('meter_type', ['Microtech', 'Saltec', 'Saltec-Goodwe', 'Microtech-Goodwe'])->where('id',747)->get();
// return $all_plants;
        foreach ($all_plants as $plants) {
            $plantSites = PlantSite::where('plant_id', $plants->id)->first();
            // return $plantSites;
            $plantSitesDataPrev = DB::table('saltec_push_response_history')->where('site_id',$plantSites->site_id)->whereDate('collect_time',Date("Y-m-d", strtotime("-1 day")))->orderBy('collect_time','desc')->first();
            if ($plantSitesDataPrev) {

                $prevResponse = json_decode($plantSitesDataPrev->response);
                $prev_final_processed_data = $prevResponse->data;

                if (isset($prev_final_processed_data) && $prev_final_processed_data) {

                    foreach ($prev_final_processed_data as $key18 => $plant_prev_final_processed_data) {

                        if ($plant_prev_final_processed_data->DeviceType == "MCMT") {
                            $plant_prev_processed_data = $plant_prev_final_processed_data;
                            break;
                        }
                    }
                }
            }
            if (isset($plant_prev_processed_data->Generated_Energy_kWh)) {
                $finalprevProccessedGeneration = $plant_prev_processed_data->Generated_Energy_kWh;
                $finalprevProccessedConsumption = $plant_prev_processed_data->Consumed_Energy_kWh;
                $finalprevProccessedImport = $plant_prev_processed_data->Mains_Import_Energy_kWh;
                $finalprevProccessedExport = $plant_prev_processed_data->Mains_Export_Energy_kWh;
            }else if( isset($plant_prev_processed_data->solarEnergy) ) {
                $finalprevProccessedGeneration = $plant_prev_processed_data->solarEnergy;
                $finalprevProccessedConsumption = $plant_prev_processed_data->consumedEnergy;
                $finalprevProccessedImport = $plant_prev_processed_data->importEnergy;
                $finalprevProccessedExport = $plant_prev_processed_data->exportEnergy;
            }
            //Daily Total Processed Data
            $total_daily_generation_exist = SaltecDailyCumulativeData::where('plant_id', $plants->id)->where('site_id',$plantSites->site_id)->whereDate('created_at', Date("Y-m-d", strtotime("-1 day")))->first();
            // return  $total_daily_generation_exist;
            $totalDailySaltecGeneration = $finalprevProccessedGeneration;
            $totalDailySaltecConsumption = $finalprevProccessedConsumption;
            $totalDailySaltecImport = $finalprevProccessedImport;
            $totalDailySaltecExport = $finalprevProccessedExport;
            $totalDailySaltecGrid = $totalDailySaltecImport > $totalDailySaltecExport ? $totalDailySaltecImport - $totalDailySaltecExport : $totalDailySaltecExport - $totalDailySaltecImport;
            $totalSaving = $totalDailySaltecGeneration * $plants->benchmark_price;
            $input_gen1['plant_id'] = $plants->id;
            $input_gen1['site_id'] = $plantSites->site_id;
            $input_gen1['total_generation'] = (double)$totalDailySaltecGeneration;
            $input_gen1['total_consumption'] = (double)$totalDailySaltecConsumption;
            $input_gen1['total_grid'] = (double)$totalDailySaltecGrid;
            $input_gen1['total_bought'] = (double)$totalDailySaltecImport;
            $input_gen1['total_sell'] = (double)$totalDailySaltecExport;
            $input_gen1['created_at'] = Date("Y-m-d H:i:s", strtotime($plant_prev_processed_data->Timestamp));
            if ($total_daily_generation_exist) {
                $input_gen1['updated_at'] = date('Y-m-d H:i:s');
                $total_daily_generation_exist->fill($input_gen1)->save();
            } else {
                SaltecDailyCumulativeData::create($input_gen1);
            }
        }

    }

}
