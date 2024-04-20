<?php

namespace App\Http\Controllers\HardwareAPIData\SaltecLogger;

use App\Http\Controllers\Controller;
use App\Http\Models\CronJobTime;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\SaltecDailyCumulativeData;
use App\Http\Models\SaltecPushData;
use App\Http\Models\GenerationLog;
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


class MGCEAndMGCWLoggerController extends Controller
{


    function MGCEC($plants,$plant,$site,$benchmark,$plant_final_processed_data,$final_processed_data,$plantSites,$Total_energy_MGCE,$Total_Saving_MGCE,$processed_cron_job_id)
    {

        date_default_timezone_set('Asia/Karachi');
        $generation_log_created_time = Date('Y-m-d H:i:s');
        $mergeArray = [];
//        foreach ($final_processed_data as $key => $plant_final_processed_data) {
//            if($plant_final_processed_data->DeviceType == "MGCW") {
//                $mergeArray =  (object)array_merge((array)$mergeArray, (array)$plant_final_processed_data);
//            }
//        }
//         $plant_final_processed_data = $mergeArray;
        $salteclatestData = DB::table('saltec_push_response')
            ->where('site_id', $plantSites->site_id)
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

        if ($plant_final_processed_data->DeviceType == "MGCE" || $plant_final_processed_data->DeviceType == "MGCW") {

            if (isset($plant_final_processed_data->numberOfInverters) && $plant_final_processed_data->numberOfInverters != null && (int)$plant_final_processed_data->numberOfInverters != 0) {

                for ($i = 1; $i <= (int)$plant_final_processed_data->numberOfInverters; $i++) {

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
                }

                for ($i = 1; $i <= (int)$plant_final_processed_data->numberOfInverters; $i++) {

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

                    $yesterday_inv_generation =  SaltecDailyCumulativeData::where('plant_id', $plant->id)->where('site_id',$site->site_id)->whereDate('created_at', Date("Y-m-d", strtotime("-1 day")))->first();
                    $daily_inv_generation = (isset($inverters_data['inverter' . $i . 'Energy']) ? $inverters_data['inverter' . $i . 'Energy'] : 0) - (isset($yesterday_inv_generation->total_generation) ? $yesterday_inv_generation->total_generation : 0);
//                    $last_month_inv_generation = MonthlyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereYear('created_at', '=', date('Y'))->whereMonth('created_at', '=', date('m', strtotime('-1 month')))->sum('monthly_generation');
                    $monthly_inv_generation = DailyInverterDetail::where('plant_id',$plant->id)->where('siteId',$site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->sum('daily_generation');
//                    $last_year_inverter_generation = YearlyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereYear('created_at', '=', date('Y', strtotime('-1 Year')))->sum('yearly_generation');
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


//            $salteclatestData = DB::table('saltec_push_response')->where('site_id', $plantSites->site_id)->orderBy('collect_time', "desc")->first();
            $salteclatestData = DB::table('saltec_push_response')
                ->where('site_id', $plantSites->site_id)
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
            $Time  = Date("Y-m-d H:i:s", strtotime('-10 minutes'));
            $plantProccessedData = SaltecPushData::where('site_id',$plantSites->site_id)->where('collect_time', '>=', $Time)->first();
            if($plantProccessedData){
                $updatePlantStatus['is_online'] = 'Y';
                $siteStatusUpdateResponse = PlantSite::where(['plant_id' => $plant->id, 'site_id' => $plantSites->site_id])->update(['online_status' => 'Y']);
                $plantRes = Plant::where('id', $plant->id)->update($updatePlantStatus);
            }else{
                $updatePlantStatus['is_online'] = 'N';
                $siteStatusUpdateResponse = PlantSite::where(['plant_id' => $plant->id, 'site_id' => $plantSites->site_id])->update(['online_status' => 'N']);
                $plantRes = Plant::where('id', $plant->id)->update($updatePlantStatus);
            }
            if (isset($plant_final_processed_data->Generated_Energy_kWh)) {
                $finalProccessedGeneration = $plant_final_processed_data->Generated_Energy_kWh;
                $finalProccessedConsumption = $plant_final_processed_data->Consumed_Energy_kWh;
                $finalProccessedImport = $plant_final_processed_data->Mains_Import_Energy_kWh;
                $finalProccessedExport = $plant_final_processed_data->Mains_Export_Energy_kWh;
            }else if( isset($plant_final_processed_data->solarEnergy) ) {
                $finalProccessedGeneration = $plant_final_processed_data->solarEnergy;
                $finalProccessedConsumption = $plant_final_processed_data->consumedEnergy;
                $finalProccessedImport = $plant_final_processed_data->importEnergy;
                $finalProccessedExport = $plant_final_processed_data->exportEnergy;
            }
            //Temporary Daily, Monthly, Yearly
            // DailyProccessed
            $Previoun_daily = SaltecDailyCumulativeData::where('plant_id', $plant->id)->whereDate('created_at', Date("Y-m-d", strtotime("-1 day")))->first();
            if ($Previoun_daily) {
                $dailyGeneration = $finalProccessedGeneration - (isset($Previoun_daily->total_generation) ? $Previoun_daily->total_generation : 0);
                $dailyConsumption = $finalProccessedConsumption - (isset($Previoun_daily->total_consumption) ? $Previoun_daily->total_consumption : 0);
                $dailyImport = $finalProccessedImport - (isset($Previoun_daily->total_bought) ? $Previoun_daily->total_bought : 0);
                $dailyExport = $finalProccessedExport - (isset($Previoun_daily->total_sell) ? $Previoun_daily->total_sell : 0);
                $dailyGrid = $dailyImport > $dailyExport ? $dailyImport - $dailyExport : $dailyExport - $dailyImport;
                $dailySaving = $dailyGeneration * $benchmark;
            }  else if(SaltecDailyCumulativeData::where('plant_id', $plant->id)->where('site_id',$site->site_id)->whereDate('created_at',"<=", Date("Y-m-d", strtotime("-1 day")))->orderBy('created_at',"DESC")->latest()->exists()){
                $Previoun_daily = SaltecDailyCumulativeData::where('plant_id', $plant->id)->where('site_id',$site->site_id)->whereDate('created_at',"<=", Date("Y-m-d", strtotime("-1 day")))->orderBy('created_at',"DESC")->latest()->first();
                $dailyGeneration = $finalProccessedGeneration - (isset($Previoun_daily->total_generation) ? $Previoun_daily->total_generation : 0);
                $dailyConsumption = $finalProccessedConsumption - (isset($Previoun_daily->total_consumption) ? $Previoun_daily->total_consumption : 0);
                $dailyImport = $finalProccessedImport - (isset($Previoun_daily->total_bought) ? $Previoun_daily->total_bought : 0);
                $dailyExport = $finalProccessedExport - (isset($Previoun_daily->total_sell) ? $Previoun_daily->total_sell : 0);
                $dailyGrid = $dailyImport > $dailyExport ? $dailyImport - $dailyExport : $dailyExport - $dailyImport;
                $dailySaving = $dailyGeneration * $benchmark;
            }else{
                $dailyGeneration = $finalProccessedGeneration;
                $dailyConsumption = $finalProccessedConsumption;
                $dailyImport = $finalProccessedImport;
                $dailyExport = $finalProccessedExport;
                $dailyGrid = $dailyImport > $dailyExport ? $dailyImport - $dailyExport : $dailyExport - $dailyImport;
                $dailySaving = $dailyGeneration * $benchmark;
            }

            // MonthlyProccessed
            $monthlyGeneration = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailyGeneration');;
            $monthlyConsumption = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailyConsumption');
            $monthlyImport = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailyBoughtEnergy');
            $monthlyExport = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailySellEnergy');
            $monthlyGrid = $monthlyImport > $monthlyExport ? $monthlyImport - $monthlyExport : $monthlyExport - $monthlyImport;
//            $monthlySaving = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailySaving');;
            $monthlySaving = $monthlyGeneration * $benchmark;

            //YearlyProccessed
            $yearlyGeneration = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->sum('monthlyGeneration');
            $yearlyConsumption = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->sum('monthlyConsumption');
            $yearlyImport = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->sum('monthlyBoughtEnergy');
            $yearlyExport = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->sum('monthlySellEnergy');
            $yearlyGrid = $yearlyImport > $yearlyExport ? $yearlyImport - $yearlyExport : $yearlyExport - $yearlyImport;
            $yearlySaving = $yearlyGeneration * $benchmark;

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
                    echo "CURL Authentication Error 4 #:" . $err;
                }
                $res = json_decode($response);
                if ($res) {
                    $privatekey = $res->privatekey;
                }

                if (isset($privatekey) && $privatekey) {

                    if(MicrotechEnergyGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->exists()){
                        $latest_start_datetime = MicrotechEnergyGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->first();
                        $latest_start_datetime = $latest_start_datetime ? $latest_start_datetime->db_datetime : $plant->created_at;
                    }else{
//                                    $latest_start_datetime = DB::table('microtech_energy_generation_log_history')->where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->first();
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
                        echo "cURL Error 5 #:" . $err;
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

                    if(MicrotechPowerGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->exists()){
                        $latest_start_datetime = MicrotechPowerGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->first();
                        $latest_start_datetime = $latest_start_datetime ? $latest_start_datetime->db_datetime : $plant->created_at;
                    }else{
//                                    $latest_start_datetime = DB::table('microtech_power_generation_log_history')->where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->first();
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
                        echo "cURL Error 6 #:" . $err;
                    }
                    $processed_data = json_decode($response2);

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
                //dd($latest_micro_daily_record, $previous_micro_daily_record);

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
                $curr_grid = isset($plant_final_processed_data->totalGridPower) ? $plant_final_processed_data->totalGridPower : 0;
                $tot_energy = 0;
                $curr_saving = (isset($plant_final_processed_data->totalInverterPower) ? $plant_final_processed_data->totalInverterPower : 0) * $benchmark;
            }
            $generation_log_data = MicrotechPowerGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->whereDate('db_datetime', Date("Y-m-d"))->orderBy('db_datetime', 'asc')->get();

            if (($plant->meter_type == "Microtech" || $plant->meter_type == "Microtech-Goodwe") && count($generation_log_data) > 0) {

                if (count($generation_log_data) > 0) {
                    foreach ($generation_log_data as $key22 => $micro_power_data) {
                        $curr_gen = 0;
                        $meterTime = Date("Y-m-d H:i:s", strtotime($micro_power_data->db_datetime) - (10 * 60));

//                        $PushfinalResponse = DB::table('saltec_push_response')->where('site_id', $site->site_id)->whereBetween('collect_time', [$meterTime, $micro_power_data->db_datetime])->orderBy('collect_time', 'Desc')->first();
                        $PushfinalResponse = DB::table('saltec_push_response')
                            ->where('site_id', $plantSites->site_id)
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

                        if ($micro_power_data->aggregate_active_pwr_pos >= $micro_power_data->aggregate_active_pwr_neg) {

                            $curr_grid = $micro_power_data->aggregate_active_pwr_pos * $plant->ratio_factor;
                            $curr_con = $curr_gen + $curr_grid;
                            $processed_curr_data['grid_type'] = '+ve';
                        } else {
                            $curr_grid = $micro_power_data->aggregate_active_pwr_neg * $plant->ratio_factor;
                            $curr_con = ($curr_gen - $curr_grid) > 0 ? $curr_gen - $curr_grid : 0;
                            $processed_curr_data['grid_type'] = '-ve';
                            $plantStatus = Plant::where('id', $plant->id)->first('is_online');
                            if ($plantStatus->is_online == 'N' && $curr_gen == 0) {
                                $curr_grid = 0;
                            }
                        }

                        $processed_curr_data['plant_id'] = $plant->id;
                        $processed_curr_data['current_generation'] = $curr_gen;
                        $processed_curr_data['current_consumption'] = $curr_con;
                        $processed_curr_data['current_grid'] = abs($curr_grid);
                        $processed_curr_data['totalEnergy'] = $dailyGeneration;
                        $processed_curr_data['current_saving'] = (double)$curr_gen * (double)$plant->benchmark_price;
                        $processed_curr_data['collect_time'] = $micro_power_data->db_datetime;
                        $check_time_diffrnc = ProcessedCurrentVariable::where('plant_id', $plant->id)->where('collect_time', 'LIKE', date('Y-m-d H:i', strtotime($micro_power_data->db_datetime)) . '%')->first();
                        if (!$check_time_diffrnc) {

                            $processed_current_variable_response = ProcessedCurrentVariable::create($processed_curr_data);
                        }
                    }
                } else{

                    $PushfinalResponsedPlant = DB::table('saltec_push_response')->where('site_id', $site->site_id)->where('status', 'N')->orderBy('collect_time', 'Desc')->get();

                    foreach ($PushfinalResponsedPlant as $key22 => $PushfinalResponse) {

                        $responseFinal = json_decode($PushfinalResponse->response);
                        $MGCELoggerResponse = [];
                        foreach ($responseFinal->data as $key => $device_response) {
                            if($device_response->DeviceType == "MGCE" || $device_response->DeviceType == "MGCW") {
                                $MGCELoggerResponse =  (object)array_merge((array)$MGCELoggerResponse, (array)$device_response);
                                $MCMT_Logger = $MGCELoggerResponse;
                            }
                        }

                        if (isset($MCMT_Logger->Timestamp) && $MCMT_Logger->Timestamp && isset($MCMT_Logger->totalInverterPower)) {

                            $curr_gen = isset($MCMT_Logger->totalInverterPower) ? $MCMT_Logger->totalInverterPower : 0;
                            $curr_con = isset($MCMT_Logger->totalLoadPower) ? $MCMT_Logger->totalLoadPower : 0;
                            $curr_grid = isset($MCMT_Logger->totalGridPower) ? $MCMT_Logger->totalGridPower : 0;
                            if ($curr_grid > 0) {
                                $processed_curr_data['grid_type'] = '+ve';
                            } else {
                                $processed_curr_data['grid_type'] = '-ve';
                            }
                            $tot_energy = 0;
                            $curr_saving = (isset($MCMT_Logger->totalInverterPower) ? $MCMT_Logger->totalInverterPower : 0) * $benchmark;
                            $data_collect_time = $MCMT_Logger->Timestamp;
                            $processed_curr_data['plant_id'] = $plant->id;
                            $processed_curr_data['current_generation'] = $curr_gen;
                            $processed_curr_data['current_consumption'] = $curr_gen;
                            $processed_curr_data['current_grid'] = abs($curr_grid);
                            $processed_curr_data['totalEnergy'] = $tot_energy;
                            $processed_curr_data['current_saving'] = (double)$curr_saving;
                            $processed_curr_data['processed_cron_job_id'] = $processed_cron_job_id + 1;
                            $processed_curr_data['collect_time'] = $data_collect_time;
                            $processed_curr_data['created'] = $generation_log_created_time;
                            $processed_curr_data['updated_at'] = $generation_log_created_time;

                            $check_time_diffrnc = ProcessedCurrentVariable::where('plant_id', $plant->id)->where('collect_time', 'LIKE', date('Y-m-d H:i', strtotime($data_collect_time)) . '%')->first();

                            if (!$check_time_diffrnc) {

                                $processed_current_variable_response = ProcessedCurrentVariable::create($processed_curr_data);
                            }
                        }
                    }
                }

            } else {

                $PushfinalResponsedPlant = DB::table('saltec_push_response')->where('site_id', $site->site_id)->where('status', 'N')->orderBy('collect_time', 'Desc')->get();

                foreach ($PushfinalResponsedPlant as $key22 => $PushfinalResponse) {

                    $responseFinal = json_decode($PushfinalResponse->response);
                    $MGCELoggerResponse = [];
                    foreach ($responseFinal->data as $key => $device_response) {
                        if($device_response->DeviceType == "MGCE" || $device_response->DeviceType == "MGCW") {
                            $MGCELoggerResponse =  (object)array_merge((array)$MGCELoggerResponse, (array)$device_response);
                            $MCMT_Logger = $MGCELoggerResponse;
                        }
                    }

                    if (isset($MCMT_Logger->Timestamp) && $MCMT_Logger->Timestamp && isset($MCMT_Logger->totalInverterPower)) {

                        $curr_gen = isset($MCMT_Logger->totalInverterPower) ? $MCMT_Logger->totalInverterPower : 0;
                        $curr_con = isset($MCMT_Logger->totalLoadPower) ? $MCMT_Logger->totalLoadPower : 0;
                        $curr_grid = isset($MCMT_Logger->totalGridPower) ? $MCMT_Logger->totalGridPower : 0;
                        if ($curr_grid > 0) {
                            $processed_curr_data['grid_type'] = '+ve';
                        } else {
                            $processed_curr_data['grid_type'] = '-ve';
                        }
                        $tot_energy = 0;
                        $curr_saving = (isset($MCMT_Logger->totalInverterPower) ? $MCMT_Logger->totalInverterPower : 0) * $benchmark;
                        $data_collect_time = $MCMT_Logger->Timestamp;
                        $processed_curr_data['plant_id'] = $plant->id;
                        $processed_curr_data['current_generation'] = $curr_gen;
                        if (($plant->meter_type == "Microtech" || $plant->meter_type == "Microtech-Goodwe")) {
                            $processed_curr_data['current_consumption'] = $curr_gen;
                        }else{
                            $processed_curr_data['current_consumption'] = $curr_con;
                        }
                        $processed_curr_data['current_grid'] = abs($curr_grid);
                        $processed_curr_data['totalEnergy'] = $tot_energy;
                        $processed_curr_data['current_saving'] = (double)$curr_saving;
                        $processed_curr_data['processed_cron_job_id'] = $processed_cron_job_id + 1;
                        $processed_curr_data['collect_time'] = $data_collect_time;
                        $processed_curr_data['created'] = $generation_log_created_time;
                        $processed_curr_data['updated_at'] = $generation_log_created_time;

                        $check_time_diffrnc = ProcessedCurrentVariable::where('plant_id', $plant->id)->where('collect_time', 'LIKE', date('Y-m-d H:i', strtotime($data_collect_time)) . '%')->first();

                        if (!$check_time_diffrnc) {

                            $processed_current_variable_response = ProcessedCurrentVariable::create($processed_curr_data);
                        }
                    }
                }
            }

            if (isset($plant_final_processed_data->Generated_Energy_kWh) || isset($plant_final_processed_data->solarEnergy)) {
                $Total_energy_MGCE += $dailyGeneration;
                $Total_Saving_MGCE += $dailySaving;

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

                //Total Processed Data
                $total_generation_exist = TotalProcessedPlantDetail::where('plant_id', $plant->id)->first();
                $envReduction = Setting::where('perimeter', 'env_reduction')->pluck('value')[0];
                $totalGeneration = YearlyProcessedPlantDetail::where('plant_id',$plant->id)->sum('yearlyGeneration');
                $totalConsumption =  YearlyProcessedPlantDetail::where('plant_id',$plant->id)->sum('yearlyConsumption');
//                if($plant->meter_type == "Microtech"){
                    $totalGrid = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->sum('yearlyGridPower');
                    $totalImport = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->sum('yearlyBoughtEnergy');
                    $totalExport = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->sum('yearlySellEnergy');
//                }else {
//                    $totalImport = $finalProccessedImport;
//                    $totalExport = $finalProccessedExport;
//                    $totalGrid = $totalImport > $totalExport ? $totalImport - $totalExport : $totalExport - $totalImport;
//                }
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

                //Daily Total Processed Data
                $total_daily_generation_exist = SaltecDailyCumulativeData::where('plant_id', $plant->id)->whereDate('created_at', Date("Y-m-d", strtotime($plant_final_processed_data->Timestamp)))->first();
                $totalDailySaltecGeneration = $finalProccessedGeneration;
                $totalDailySaltecConsumption = $finalProccessedConsumption;
                $totalDailySaltecImport = $finalProccessedImport;
                $totalDailySaltecExport = $finalProccessedExport;
                $totalDailySaltecGrid = $totalDailySaltecImport > $totalDailySaltecExport ? $totalDailySaltecImport - $totalDailySaltecExport : $totalDailySaltecExport - $totalDailySaltecImport;
                $totalSaving = $totalDailySaltecGeneration * $benchmark;
                $input_gen1['site_id'] = $plantSites->site_id;
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

                return [$Total_energy_MGCE,$Total_Saving_MGCE];
//            }
        }

    }

}
