<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\CommunicationController;
use App\Http\Controllers\Admin\PlantsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LEDController;
use App\Http\Models\AccumulativeProcessedDetail;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\GenerationLog;
use App\Http\Models\Inverter;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\MicrotechEnergyGenerationLog;
use App\Http\Models\MicrotechPowerGenerationLog;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\Notification;
use App\Http\Models\Plant;
use App\Http\Models\CronJobTime;
use App\Http\Models\PlantDetail;
use App\Http\Models\PlantSite;
use App\Http\Models\PlantType;
use App\Http\Models\PlantUser;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\ProcessedPlantDetail;
use App\Http\Models\Setting;
use App\Http\Models\SystemType;
use App\Http\Models\TotalProcessedPlantDetail;
use App\Http\Models\Weather;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\InverterMPPTDetail;

class PlantSiteDataController2 extends Controller
{

    public $BaseURL = 'http://138.128.189.163:8089';

    public function plant_site_data()
    {

        $early_sunrise = Weather::whereDate('created_at', Date('Y-m-d'))->orderBy('sunrise', 'ASC')->first();
        $sunrise = $early_sunrise && $early_sunrise->sunrise ? explode(':', $early_sunrise->sunrise) : explode(':', '06:00:AM');
        $sunrise_hour = $sunrise[0];
        $sunrise_min = $sunrise[1];
        $BaseURL = 'http://138.128.189.163:8089';
        $micro_itr = 0;
        date_default_timezone_set('Asia/Karachi');
        $time_difference_saltec = Date('Y-m-d H:i:s');
        $time_difference_micro = Date('Y-m-d H:i:s');
        $generation_log_created_time = Date('Y-m-d H:i:s');
        $current_time = Date('Ymd') . 'T' . Date('His');

        $processedCronJobId = ProcessedCurrentVariable::where('processed_cron_job_type','API2')->max('processed_cron_job_id');
        $cronJobTime = new CronJobTime();
        $cronJobTime->start_time = $generation_log_created_time;
        $cronJobTime->type = 'Saltec/Microtech-2';
        $cronJobTime->status = 'in-progress';
        $cronJobTime->processed_cron_job_id = $processedCronJobId + 1;
        $cronJobTime->save();

        // $plant_id_value = Session::get('plant_idd');
        $plant_id_value = NULL;
        $token = '';
        $data = [
            'userName' => 'viper.bel',
            'password' => 'vdotb021',
            'lifeMinutes' => '240',
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $BaseURL . '/api/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                'X-API-Version' => '1.0',
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "CURL Authentication Error 1 #:" . $err;
        }
        $res = json_decode($response);
        if ($res) {
            $token = $res->data;
        }

        if (isset($token) && $token) {

//           $plants = Plant::where('id', 50)->get();
            $plants = Plant::whereIn('meter_type', ['Microtech', 'Saltec','Saltec-Goodwe','Microtech-Goodwe'])->where('id','>',320)->get();
//            $plants = Plant::whereIn('meter_type', ['Saltec','Saltec-Goodwe'])->get();

            print_r('Cron job Start Time');
            print_r(date("Y-m-d H:i:s"));
            print_r("\n");
            $generation_log_cron_job_id = GenerationLog::where('cron_job_type','API2')->max('cron_job_id');
            $processed_plant_cron_job_id = ProcessedPlantDetail::where('cron_job_type','API2')->max('cron_job_id');
            if ($plants) {
                foreach ($plants as $key => $plant) {

                    $dailyGenerationVar = 0;
                    $dailyConsumptionVar = 0;
                    $dailyGridVar = 0;
                    $dailyBoughtEnergyVar = 0;
                    $dailySellEnergyVar = 0;
                    $monthlyGenerationVar = 0;
                    $monthlyConsumptionVar = 0;
                    $monthlyGridVar = 0;
                    $monthlyBoughtEnergyVar = 0;
                    $monthlySellEnergyVar = 0;
                    $yearlyGenerationVar = 0;
                    $yearlyConsumptionVar = 0;
                    $yearlyGridVar = 0;
                    $yearlyBoughtEnergyVar = 0;
                    $yearlySellEnergyVar = 0;
                    $final_processed_Dailygeneration = array();
                    $final_processed_Monthlygeneration = array();

                    $plant_sites = PlantSite::where('plant_id', $plant->id)->get();
//                    return $plant_sites;
//                    return $plant_sites;

                    foreach ($plant_sites as $key1 => $site) {

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $BaseURL . "/api/site/live/" . $site->site_id,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_HTTPHEADER => array(
                                // Set Here Your Requesred Headers
                                'Content-Type: application/json',
                                'X-API-Version: 1.0',
                                'Authorization: Bearer ' . $token,
                            ),
                        ));
                        $response1 = curl_exec($curl);
                        $err = curl_error($curl);
                        curl_close($curl);

                        if ($err) {
                            echo "cURL Error 2 #:" . $err;
                        }
                        $plant_inverter_data = json_decode($response1);
                        //    return [$BaseURL, $response1,$site->site_id];

                        if ($plant_inverter_data && isset($plant_inverter_data->data)) {
                            $plant_inverter_final_data = $plant_inverter_data->data;
                        }

                        if (isset($plant_inverter_final_data) && $plant_inverter_final_data) {

                            $timestm = $plant_inverter_final_data->lastUpdated;
                            $time_difference_saltec = $timestm;

                            date_default_timezone_set("Asia/Karachi");

                            if ($plant_inverter_final_data) {
//                                $input['plant_id'] = $plant->id;
//                                $input['siteId'] = $site->site_id;
//                                $input['l1GridCurrent'] = isset($plant_inverter_final_data->l1GridCurrent) && $plant_inverter_final_data->l1GridCurrent ? $plant_inverter_final_data->l1GridCurrent : 0;
//                                $input['l2GridCurrent'] = isset($plant_inverter_final_data->l2GridCurrent) && $plant_inverter_final_data->l2GridCurrent ? $plant_inverter_final_data->l2GridCurrent : 0;
//                                $input['l3GridCurrent'] = isset($plant_inverter_final_data->l3GridCurrent) && $plant_inverter_final_data->l3GridCurrent ? $plant_inverter_final_data->l3GridCurrent : 0;
//                                $input['l1GridApparentPower'] = isset($plant_inverter_final_data->l1GridApparentPower) && $plant_inverter_final_data->l1GridApparentPower ? $plant_inverter_final_data->l1GridApparentPower : 0;
//                                $input['l2GridApparentPower'] = isset($plant_inverter_final_data->l2GridApparentPower) && $plant_inverter_final_data->l2GridApparentPower ? $plant_inverter_final_data->l2GridApparentPower : 0;
//                                $input['l3GridApparentPower'] = isset($plant_inverter_final_data->l3GridApparentPower) && $plant_inverter_final_data->l3GridApparentPower ? $plant_inverter_final_data->l3GridApparentPower : 0;
//                                $input['l1GridPowerFactor'] = isset($plant_inverter_final_data->l1GridPowerFactor) && $plant_inverter_final_data->l1GridPowerFactor ? $plant_inverter_final_data->l1GridPowerFactor : 0;
//                                $input['l2GridPowerFactor'] = isset($plant_inverter_final_data->l2GridPowerFactor) && $plant_inverter_final_data->l2GridPowerFactor ? $plant_inverter_final_data->l2GridPowerFactor : 0;
//                                $input['l3GridPowerFactor'] = isset($plant_inverter_final_data->l3GridPowerFactor) && $plant_inverter_final_data->l3GridPowerFactor ? $plant_inverter_final_data->l3GridPowerFactor : 0;
//                                $input['gridFrequency'] = isset($plant_inverter_final_data->gridFrequency) && $plant_inverter_final_data->gridFrequency ? $plant_inverter_final_data->gridFrequency : 0;
//                                $input['totalGridApparentPower'] = isset($plant_inverter_final_data->totalGridApparentPower) && $plant_inverter_final_data->totalGridApparentPower ? $plant_inverter_final_data->totalGridApparentPower : 0;
//                                $input['meterUptime'] = isset($plant_inverter_final_data->meterUptime) && $plant_inverter_final_data->meterUptime ? $plant_inverter_final_data->meterUptime : 0;
//                                $input['gecUptime'] = isset($plant_inverter_final_data->gecUptime) && $plant_inverter_final_data->gecUptime ? $plant_inverter_final_data->gecUptime : 0;
//                                $input['installedMeterType'] = isset($plant_inverter_final_data->installedMeterType) && $plant_inverter_final_data->installedMeterType ? $plant_inverter_final_data->installedMeterType : 0;
//                                $input['installedInverterType'] = isset($plant_inverter_final_data->installedInverterType) && $plant_inverter_final_data->installedInverterType ? $plant_inverter_final_data->installedInverterType : 0;
//                                $input['meterCommFail'] = isset($plant_inverter_final_data->meterCommFail) && $plant_inverter_final_data->meterCommFail ? $plant_inverter_final_data->meterCommFail : 0;
//                                $input['exportLimitEnabled'] = isset($plant_inverter_final_data->exportLimitEnabled) && $plant_inverter_final_data->exportLimitEnabled ? $plant_inverter_final_data->exportLimitEnabled : 0;
//                                $input['l1Voltage'] = isset($plant_inverter_final_data->l1Voltage) && $plant_inverter_final_data->l1Voltage ? $plant_inverter_final_data->l1Voltage : 0;
//                                $input['l2Voltage'] = isset($plant_inverter_final_data->l2Voltage) && $plant_inverter_final_data->l2Voltage ? $plant_inverter_final_data->l2Voltage : 0;
//                                $input['l3Voltage'] = isset($plant_inverter_final_data->l3Voltage) && $plant_inverter_final_data->l3Voltage ? $plant_inverter_final_data->l3Voltage : 0;
//                                $input['l1GridPower'] = isset($plant_inverter_final_data->l1GridPower) && $plant_inverter_final_data->l1GridPower ? $plant_inverter_final_data->l1GridPower : 0;
//                                $input['l2GridPower'] = isset($plant_inverter_final_data->l2GridPower) && $plant_inverter_final_data->l2GridPower ? $plant_inverter_final_data->l2GridPower : 0;
//                                $input['l3GridPower'] = isset($plant_inverter_final_data->l3GridPower) && $plant_inverter_final_data->l3GridPower ? $plant_inverter_final_data->l3GridPower : 0;
//                                $input['totalGridPower'] = isset($plant_inverter_final_data->totalGridPower) && $plant_inverter_final_data->totalGridPower ? $plant_inverter_final_data->totalGridPower : 0;
//                                $input['l1LoadPower'] = isset($plant_inverter_final_data->l1LoadPower) && $plant_inverter_final_data->l1LoadPower ? $plant_inverter_final_data->l1LoadPower : 0;
//                                $input['l2LoadPower'] = isset($plant_inverter_final_data->l2LoadPower) && $plant_inverter_final_data->l2LoadPower ? $plant_inverter_final_data->l2LoadPower : 0;
//                                $input['l3LoadPower'] = isset($plant_inverter_final_data->l3LoadPower) && $plant_inverter_final_data->l3LoadPower ? $plant_inverter_final_data->l3LoadPower : 0;
//                                $input['totalLoadPower'] = isset($plant_inverter_final_data->totalLoadPower) && $plant_inverter_final_data->totalLoadPower ? $plant_inverter_final_data->totalLoadPower : 0;
//                                $input['importEnergy'] = isset($plant_inverter_final_data->importEnergy) && $plant_inverter_final_data->importEnergy ? $plant_inverter_final_data->importEnergy : 0;
//                                $input['exportEnergy'] = isset($plant_inverter_final_data->exportEnergy) && $plant_inverter_final_data->exportEnergy ? $plant_inverter_final_data->exportEnergy : 0;
//                                $input['consumedEnergy'] = isset($plant_inverter_final_data->consumedEnergy) && $plant_inverter_final_data->consumedEnergy ? $plant_inverter_final_data->consumedEnergy : 0;
//                                $input['solarEnergy'] = isset($plant_inverter_final_data->solarEnergy) && $plant_inverter_final_data->solarEnergy ? $plant_inverter_final_data->solarEnergy : 0;
//                                $input['l1InverterPower'] = isset($plant_inverter_final_data->l1InverterPower) && $plant_inverter_final_data->l1InverterPower ? $plant_inverter_final_data->l1InverterPower : 0;
//                                $input['l2InverterPower'] = isset($plant_inverter_final_data->l2InverterPower) && $plant_inverter_final_data->l2InverterPower ? $plant_inverter_final_data->l2InverterPower : 0;
//                                $input['l3InverterPower'] = isset($plant_inverter_final_data->l3InverterPower) && $plant_inverter_final_data->l3InverterPower ? $plant_inverter_final_data->l3InverterPower : 0;
//                                // $input['totalInverterPower'] = $plant_inverter_final_data->totalInverterPower;
//                                $input['numberOfInverters'] = isset($plant_inverter_final_data->numberOfInverters) && $plant_inverter_final_data->numberOfInverters ? $plant_inverter_final_data->numberOfInverters : 0;
//                                $input['lastUpdated'] = isset($plant_inverter_final_data->lastUpdated) && $plant_inverter_final_data->lastUpdated ? $plant_inverter_final_data->lastUpdated : '';
//                                $input['created_at'] = Date('Y-m-d H:i:s');
//                                $input['updated_at'] = Date('Y-m-d H:i:s');
//                                // dd($input);
//
//                                $plant_detail_exist = PlantDetail::where('siteId', $plant->site_id)->first();
//                                if ($plant_detail_exist) {
//                                    $plant_detail_insertion_responce = $plant_detail_exist->fill($input)->save();
//                                } else {
//                                    $plant_detail_insertion_responce = PlantDetail::create($input);
//                                }
//
//                                if ($plant_detail_insertion_responce) {
//                                    //echo '1 Plant Detail';
//                                } else {
//                                    // echo '0 Sorry! Plant and inverter data are retrived But plant detail insertion Failed.';
//                                }

                                //Plant Processed Data
                                $curl = curl_init();
//                                return $site->site_id;

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => $BaseURL . "/api/site/processed/" . $site->site_id . '?timestamp=' . $current_time,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_CUSTOMREQUEST => "GET",
                                    CURLOPT_SSL_VERIFYHOST => false,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER => array(
                                        // Set Here Your Requested Headers
                                        'Content-Type: application/json',
                                        'X-API-Version: 1.0',
                                        'Authorization: Bearer ' . $token,
                                    ),
                                ));
                                $response2 = curl_exec($curl);
                                $err = curl_error($curl);
                                curl_close($curl);

                                if ($err) {
                                    echo "cURL Error 3 #:" . $err;
                                }
                                $processed_data = json_decode($response2);

                                if ($processed_data && isset($processed_data->data)) {
                                    $final_processed_data = $processed_data->data;
                                }

                                if (isset($final_processed_data) && $final_processed_data) {

                                    //Plant Processed Data
                                    $processed_plant_detail['plant_id'] = $plant->id;
                                    $processed_plant_detail['siteId'] = $site->site_id;
                                    $processed_plant_detail['dailyGeneration'] = isset($final_processed_data->dailySolarEnergy) && $final_processed_data->dailySolarEnergy ? $final_processed_data->dailySolarEnergy : 0;
                                    $processed_plant_detail['monthlyGeneration'] = isset($final_processed_data->monthlySolarEnergy) && $final_processed_data->monthlySolarEnergy ? $final_processed_data->monthlySolarEnergy : 0;
                                    $processed_plant_detail['yearlyGeneration'] = isset($final_processed_data->yearlySolarEnergy) && $final_processed_data->yearlySolarEnergy ? $final_processed_data->yearlySolarEnergy : 0;
                                    $processed_plant_detail['dailyConsumption'] = isset($final_processed_data->dailyLoadEnergy) && $final_processed_data->dailyLoadEnergy ? $final_processed_data->dailyLoadEnergy : 0;
                                    $processed_plant_detail['monthlyConsumption'] = isset($final_processed_data->monthlyLoadEnergy) && $final_processed_data->monthlyLoadEnergy ? $final_processed_data->monthlyLoadEnergy : 0;
                                    $processed_plant_detail['yearlyConsumption'] = isset($final_processed_data->yearlyLoadEnergy) && $final_processed_data->yearlyLoadEnergy ? $final_processed_data->yearlyLoadEnergy : 0;
                                    $processed_plant_detail['dailyGridPower'] = isset($final_processed_data->dailyAvgGridPower) && $final_processed_data->dailyAvgGridPower ? $final_processed_data->dailyAvgGridPower : 0;
                                    $processed_plant_detail['monthlyGridPower'] = isset($final_processed_data->monthlyAvgGridPower) && $final_processed_data->monthlyAvgGridPower ? $final_processed_data->monthlyAvgGridPower : 0;
                                    $processed_plant_detail['yearlyGridPower'] = isset($final_processed_data->yearlyAvgGridPower) && $final_processed_data->yearlyAvgGridPower ? $final_processed_data->yearlyAvgGridPower : 0;
                                    $processed_plant_detail['dailyBoughtEnergy'] = isset($final_processed_data->dailyImportEnergy) && $final_processed_data->dailyImportEnergy ? $final_processed_data->dailyImportEnergy : 0;
                                    $processed_plant_detail['monthlyBoughtEnergy'] = isset($final_processed_data->monthlyImportEnergy) && $final_processed_data->monthlyImportEnergy ? $final_processed_data->monthlyImportEnergy : 0;
                                    $processed_plant_detail['yearlyBoughtEnergy'] = isset($final_processed_data->yearlyImportEnergy) && $final_processed_data->yearlyImportEnergy ? $final_processed_data->yearlyImportEnergy : 0;
                                    $processed_plant_detail['dailySellEnergy'] = isset($final_processed_data->dailyExportEnergy) && $final_processed_data->dailyExportEnergy ? $final_processed_data->dailyExportEnergy : 0;
                                    $processed_plant_detail['monthlySellEnergy'] = isset($final_processed_data->monthlyExportEnergy) && $final_processed_data->monthlyExportEnergy ? $final_processed_data->monthlyExportEnergy : 0;
                                    $processed_plant_detail['yearlySellEnergy'] = isset($final_processed_data->yearlyExportEnergy) && $final_processed_data->yearlyExportEnergy ? $final_processed_data->yearlyExportEnergy : 0;
                                    $processed_plant_detail['dailyMaxSolarPower'] = isset($final_processed_data->dailyMaxSolarPower) && $final_processed_data->dailyMaxSolarPower ? $final_processed_data->dailyMaxSolarPower : 0;
                                    $processed_plant_detail['monthlyMaxSolarPower'] = isset($final_processed_data->monthlyMaxSolarPower) && $final_processed_data->monthlyMaxSolarPower ? $final_processed_data->monthlyMaxSolarPower : 0;
                                    $processed_plant_detail['yearlyMaxSolarPower'] = isset($final_processed_data->yearlyMaxSolarPower) && $final_processed_data->yearlyMaxSolarPower ? $final_processed_data->yearlyMaxSolarPower : 0;
                                    $processed_plant_detail['lastUpdated'] = isset($final_processed_data->timestamp) && $final_processed_data->timestamp ? $final_processed_data->timestamp : Date('Ymd') . 'T' . Date('His');
                                    $processed_plant_detail['cron_job_id'] = isset($processed_plant_cron_job_id) ? $processed_plant_cron_job_id + 1 : 0;
                                    $processed_plant_detail['cron_job_type'] = 'API2';

                                    $processed_plant_detail_insertion_responce = ProcessedPlantDetail::create($processed_plant_detail);
                                }

                                $inverterMpptData = json_decode(json_encode($plant_inverter_final_data), true);
                                $inverterArrayKeys = array_keys($inverterMpptData);

                                if (isset($plant_inverter_final_data->numberOfInverters) && $plant_inverter_final_data->numberOfInverters != null && (int)$plant_inverter_final_data->numberOfInverters != 0) {

                                    for ($i = 1; $i <= (int)$plant_inverter_final_data->numberOfInverters; $i++) {

                                        $inverters_data = (array)$plant_inverter_final_data;
                                        $inverter_input['plant_id'] = $plant->id;
                                        $inverter_input['site_id'] = $site->site_id;
                                        $inverter_input['dv_inverter'] = 'dv_Inv' . $i;

                                        $length = 4;
                                        $randomString = substr(str_shuffle(str_repeat($x = '123456789', ceil($length / strlen($x)))), 1, $length);

                                        $inverter_input['dv_inverter_serial_no'] = isset($inverters_data['dv_Inv' . $i . 'SerialNumber']) ? $inverters_data['dv_Inv' . $i . 'SerialNumber'] : '000000' . $randomString;
                                        $inverter_input['created_at'] = Date('Y-m-d H:i:s');
                                        $inverter_input['updated_at'] = Date('Y-m-d H:i:s');
                                        // echo '<pre>'; print_r($inverter_input);exit;

                                        $inverter_exist = InverterSerialNo::where('site_id', $inverters_data['siteId'])->where('dv_inverter', $inverter_input['dv_inverter'])->Where('dv_inverter_serial_no', 'like', '000000%')->first();
                                        $inverter_exist_create = InverterSerialNo::where('site_id', $inverters_data['siteId'])->where('dv_inverter', $inverter_input['dv_inverter'])->first();

                                        if ($inverter_exist) {
                                            $inverter_updation_responce = $inverter_exist->fill($inverter_input)->save();
                                        }

                                        if (!$inverter_exist_create) {
                                            $inverter_insertion_responce = InverterSerialNo::create($inverter_input);
                                        }
                                    }

                                    for ($i = 1; $i <= (int)$plant_inverter_final_data->numberOfInverters; $i++) {

                                        $inverters_data = (array)$plant_inverter_final_data;
                                        $inverter_input['plant_id'] = $plant->id;
                                        $inverter_input['siteId'] = $site->site_id;
                                        $inverter_input['dv_inverter'] = 'dv_Inv' . $i;
                                        $inverter_input['ac_output_power'] = isset($inverters_data['inverter' . $i . 'Power']) ? $inverters_data['inverter' . $i . 'Power'] : null;
                                        $inverter_input['total_generation'] = isset($inverters_data['dv_Inv' . $i . 'TotalEnergy']) ? $inverters_data['dv_Inv' . $i . 'TotalEnergy'] : null;
                                        $inverter_input['l_voltage1'] = isset($inverters_data['dv_Inv' . $i . 'Mppt1Voltage']) ? $inverters_data['dv_Inv' . $i . 'Mppt1Voltage'] : null;
                                        $inverter_input['l_current1'] = isset($inverters_data['dv_Inv' . $i . 'Mppt1Current']) ? $inverters_data['dv_Inv' . $i . 'Mppt1Current'] : null;
                                        $inverter_input['l_voltage2'] = isset($inverters_data['dv_Inv' . $i . 'Mppt2Voltage']) ? $inverters_data['dv_Inv' . $i . 'Mppt2Voltage'] : null;
                                        $inverter_input['l_current2'] = isset($inverters_data['dv_Inv' . $i . 'Mppt2Current']) ? $inverters_data['dv_Inv' . $i . 'Mppt2Current'] : null;
                                        $inverter_input['l_voltage3'] = isset($inverters_data['dv_Inv' . $i . 'Mppt3Voltage']) ? $inverters_data['dv_Inv' . $i . 'Mppt3Voltage'] : null;
                                        $inverter_input['l_current3'] = isset($inverters_data['dv_Inv' . $i . 'Mppt3Current']) ? $inverters_data['dv_Inv' . $i . 'Mppt3Current'] : null;
                                        $inverter_input['r_voltage1'] = isset($inverters_data['dv_Inv' . $i . 'L1Voltage']) ? $inverters_data['dv_Inv' . $i . 'L1Voltage'] : null;
                                        $inverter_input['r_current1'] = isset($inverters_data['dv_Inv' . $i . 'L1Current']) ? $inverters_data['dv_Inv' . $i . 'L1Current'] : null;
                                        $inverter_input['r_voltage2'] = isset($inverters_data['dv_Inv' . $i . 'L2Voltage']) ? $inverters_data['dv_Inv' . $i . 'L2Voltage'] : null;
                                        $inverter_input['r_current2'] = isset($inverters_data['dv_Inv' . $i . 'L2Current']) ? $inverters_data['dv_Inv' . $i . 'L2Current'] : null;
                                        $inverter_input['r_voltage3'] = isset($inverters_data['dv_Inv' . $i . 'L3Voltage']) ? $inverters_data['dv_Inv' . $i . 'L3Voltage'] : null;
                                        $inverter_input['r_current3'] = isset($inverters_data['dv_Inv' . $i . 'L3Current']) ? $inverters_data['dv_Inv' . $i . 'L3Current'] : null;
                                        $inverter_input['frequency'] = isset($inverters_data['dv_Inv' . $i . 'Frequency']) ? $inverters_data['dv_Inv' . $i . 'Frequency'] : null;
                                        $inverter_input['dc_power'] = isset($inverters_data['dv_Inv' . $i . 'TotalDcPower']) ? $inverters_data['dv_Inv' . $i . 'TotalDcPower'] : null;
                                        $inverter_input['lastUpdated'] = isset($inverters_data['lastUpdated']) ? $inverters_data['lastUpdated'] : null;
                                        $inverter_input['created_at'] = Date('Y-m-d H:i:s');
                                        $inverter_input['updated_at'] = Date('Y-m-d H:i:s');

                                        $inverter_exist = Inverter::where('siteId', $inverters_data['siteId'])->where('dv_inverter', $inverter_input['dv_inverter'])->delete();

                                        $inverter_insertion_responce = Inverter::create($inverter_input);

                                        // if ($inverter_exist) {
                                        //     $inverter_insertion_responce = $inverter_exist->fill($inverter_input)->save();
                                        // } else {
                                        //     $inverter_insertion_responce = Inverter::create($inverter_input);
                                        // }
                                        $mpptDataArray = [];
                                        $count = 0;
                                        for ($k = 0; $k < count($inverterArrayKeys); $k++) {
                                            $search = 'dv_Inv' . $i . 'Mppt';
                                            if (preg_match("/{$search}/i", $inverterArrayKeys[$k])) {
                                                array_push($mpptDataArray, $count = $count + 1);
                                            }
                                        }
//                                        return $mpptDataArray;

                                        $inverters_data = (array)$plant_inverter_final_data;

                                        $inverter['plant_id'] = $plant->id;
                                        $inverter['siteId'] = $inverters_data['siteId'];
                                        $inverter['dv_inverter'] = 'dv_Inv' . $i;
                                        $inverter['daily_generation'] = isset($inverters_data['dv_Inv' . $i . 'DailyEnergy']) ? $inverters_data['dv_Inv' . $i . 'DailyEnergy'] : null;
                                        $inverter['monthly_generation'] = isset($inverters_data['dv_Inv' . $i . 'MonthlyEnergy']) ? $inverters_data['dv_Inv' . $i . 'MonthlyEnergy'] : null;
                                        $inverter['inverterPower'] = isset($inverters_data['inverter' . $i . 'Power']) ? $inverters_data['inverter' . $i . 'Power'] : null;
                                        $inverter['inverterEnergy'] = isset($inverters_data['inverter' . $i . 'Energy']) ? $inverters_data['inverter' . $i . 'Energy'] : null;
                                        $inverter['totalInverterPower'] = isset($inverters_data['totalInverterPower']) ? $inverters_data['totalInverterPower'] : null;
                                        $inverter['inverterLimitValue'] = isset($inverters_data['inverter' . $i . 'LimitValue']) ? $inverters_data['inverter' . $i . 'LimitValue'] : null;
                                        $inverter['inverterCommFail'] = isset($inverters_data['inverterCommFail']) ? $inverters_data['inverterCommFail'] : null;
                                        $inverter['inverterConfigFail'] = isset($inverters_data['inverterConfigFail']) ? $inverters_data['inverterConfigFail'] : null;
                                        $inverter['inverterUptime'] = isset($inverters_data['inverter' . $i . 'Uptime']) ? $inverters_data['inverter' . $i . 'Uptime'] : null;
                                        $inverter['ac_output_power'] = isset($inverters_data['inverter' . $i . 'Power']) ? $inverters_data['inverter' . $i . 'Power'] : null;
                                        $inverter['total_generation'] = isset($inverters_data['dv_Inv' . $i . 'TotalEnergy']) ? $inverters_data['dv_Inv' . $i . 'TotalEnergy'] : null;
//                                        $inverter['l_voltage1'] = isset($inverters_data['dv_Inv' . $i . 'Mppt1Voltage']) ? $inverters_data['dv_Inv' . $i . 'Mppt1Voltage'] : null;
//                                        $inverter['l_current1'] = isset($inverters_data['dv_Inv' . $i . 'Mppt1Current']) ? $inverters_data['dv_Inv' . $i . 'Mppt1Current'] : null;
//                                        $inverter['l_voltage2'] = isset($inverters_data['dv_Inv' . $i . 'Mppt2Voltage']) ? $inverters_data['dv_Inv' . $i . 'Mppt2Voltage'] : null;
//                                        $inverter['l_current2'] = isset($inverters_data['dv_Inv' . $i . 'Mppt2Current']) ? $inverters_data['dv_Inv' . $i . 'Mppt2Current'] : null;
//                                        $inverter['l_voltage3'] = isset($inverters_data['dv_Inv' . $i . 'Mppt3Voltage']) ? $inverters_data['dv_Inv' . $i . 'Mppt3Voltage'] : null;
//                                        $inverter['l_current3'] = isset($inverters_data['dv_Inv' . $i . 'Mppt3Current']) ? $inverters_data['dv_Inv' . $i . 'Mppt3Current'] : null;
                                        $inverter['phase_voltage_r'] = isset($inverters_data['dv_Inv' . $i . 'L1Voltage']) ? $inverters_data['dv_Inv' . $i . 'L1Voltage'] : null;
                                        $inverter['phase_current_r'] = isset($inverters_data['dv_Inv' . $i . 'L1Current']) ? $inverters_data['dv_Inv' . $i . 'L1Current'] : null;
                                        $inverter['phase_voltage_s'] = isset($inverters_data['dv_Inv' . $i . 'L2Voltage']) ? $inverters_data['dv_Inv' . $i . 'L2Voltage'] : null;
                                        $inverter['phase_current_s'] = isset($inverters_data['dv_Inv' . $i . 'L2Current']) ? $inverters_data['dv_Inv' . $i . 'L2Current'] : null;
                                        $inverter['phase_voltage_t'] = isset($inverters_data['dv_Inv' . $i . 'L3Voltage']) ? $inverters_data['dv_Inv' . $i . 'L3Voltage'] : null;
                                        $inverter['phase_current_t'] = isset($inverters_data['dv_Inv' . $i . 'L3Current']) ? $inverters_data['dv_Inv' . $i . 'L3Current'] : null;
                                        $inverter['frequency'] = isset($inverters_data['dv_Inv' . $i . 'Frequency']) ? $inverters_data['dv_Inv' . $i . 'Frequency'] : null;
                                        $inverter['dc_power'] = isset($inverters_data['dv_Inv' . $i . 'TotalDcPower']) ? $inverters_data['dv_Inv' . $i . 'TotalDcPower'] : null;
                                        $inverter['numberOfInverters'] = isset($inverters_data['numberOfInverters']) ? $inverters_data['numberOfInverters'] : null;
                                        $inverter['lastUpdated'] = isset($inverters_data['lastUpdated']) ? $inverters_data['lastUpdated'] : null;
                                        $inverter['created_at'] = Date('Y-m-d H:i:s');
//                                        $inverter['collect_time'] = date('Y-m-d H:i:s',strtotime($inverters_data['lastUpdated']));
                                        $inverter['collect_time'] = date('Y-m-d H:i:s');
                                        $inverter['updated_at'] = Date('Y-m-d H:i:s');

                                        $inverter_detail_insertion_responce = InverterDetail::create($inverter);
//                                        return json_encode($inverter_detail_insertion_responce);
                                        $inverterDataDEtail = 'dv_Inv' . $i;
                                        $mpptArrayDetails = count($mpptDataArray) / 2;
                                        for ($mi = 1; $mi <= $mpptArrayDetails; $mi++) {
                                            $plantID = $plant->id;
                                            $siteID = $inverters_data['siteId'];
                                            $collectTime = date('Y-m-d H:i:s', strtotime($inverters_data['lastUpdated']));
                                            $mpptData = InverterMPPTDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $inverterDataDEtail, 'mppt_number' => $mi])->where('collect_time', $collectTime)->orderBy('collect_time', 'desc')->first();
                                            if (!$mpptData) {
                                                $inverterMPPTLog['mppt_voltage'] = $inverters_data['dv_Inv' . $i . 'Mppt' . $mi . 'Voltage'];
                                                $inverterMPPTLog['mppt_current'] = $inverters_data['dv_Inv' . $i . 'Mppt' . $mi . 'Current'];
                                                $inverterMPPTLog['collect_time'] = $collectTime;
                                                $inverterMPPTLog['plant_id'] = $plantID;
                                                $inverterMPPTLog['site_id'] = $siteID;
                                                $inverterMPPTLog['dv_inverter'] = $inverterDataDEtail;
                                                $inverterMPPTLog['mppt_number'] = $mi;
                                                $inverterMPPTResponse = InverterMPPTDetail::create($inverterMPPTLog);
                                            }
                                        }

//                                        if ($inverters_data['lastUpdated'] >= date('Ymd') . 'T' . $sunrise_hour . '' . $sunrise_min) {

                                        $daily_inverter_detail_exist = DailyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereDate('created_at', '=', date('Y-m-d'))->first();

                                        $daily_input['plant_id'] = $plant->id;
                                        $daily_input['siteId'] = $site->site_id;
                                        $daily_input['dv_inverter'] = 'dv_Inv' . $i;
                                        $daily_input['lastUpdated'] = $inverters_data['lastUpdated'];
                                        $daily_input['created_at'] = Date('Y-m-d H:i:s', strtotime($inverters_data['lastUpdated']));
                                        $daily_input['updated_at'] = Date('Y-m-d H:i:s');


                                        if(Date('Y-m-d H:i:s') > Date('Y-m-d') ." 00:00:00" && Date('Y-m-d H:i:s') < Date('Y-m-d') ." 00:55:00"){
                                            $daily_input['daily_generation'] = 0;
                                        }else{
                                            if ($daily_inverter_detail_exist) {
                                                if($plant->meter_type == "Saltec-Goodwe" || $plant->meter_type == "Microtech-Goodwe"){

                                                    $daily_input['daily_generation'] = $processed_plant_detail['dailyGeneration'];
                                                }else{
                                                    $daily_input['daily_generation'] = isset($inverters_data['dv_Inv' . $i . 'DailyEnergy']) && !empty($inverters_data['dv_Inv' . $i . 'DailyEnergy']) && $inverters_data['dv_Inv' . $i . 'DailyEnergy'] != null ? $inverters_data['dv_Inv' . $i . 'DailyEnergy'] : $daily_inverter_detail_exist['daily_generation'];

                                                }

                                            } else {
                                                if($plant->meter_type == "Saltec-Goodwe" || $plant->meter_type == "Microtech-Goodwe"){

                                                    $daily_input['daily_generation'] = $processed_plant_detail['dailyGeneration'];

                                                }else{

                                                    $daily_input['daily_generation'] = isset($inverters_data['dv_Inv' . $i . 'DailyEnergy']) && !empty($inverters_data['dv_Inv' . $i . 'DailyEnergy']) && $inverters_data['dv_Inv' . $i . 'DailyEnergy'] != null ? $inverters_data['dv_Inv' . $i . 'DailyEnergy'] : 0;

                                                }
                                            }

                                        }

//                                            return $daily_input;
//                                            if ($daily_inverter_detail_exist) {
//
//                                                $daily_input['daily_generation'] = isset($inverters_data['dv_Inv' . $i . 'DailyEnergy']) && !empty($inverters_data['dv_Inv' . $i . 'DailyEnergy']) && $inverters_data['dv_Inv' . $i . 'DailyEnergy'] != null ? $inverters_data['dv_Inv' . $i . 'DailyEnergy'] : $daily_inverter_detail_exist['daily_generation'];
//                                            } else {
//
//                                                $daily_input['daily_generation'] = isset($inverters_data['dv_Inv' . $i . 'DailyEnergy']) && !empty($inverters_data['dv_Inv' . $i . 'DailyEnergy']) && $inverters_data['dv_Inv' . $i . 'DailyEnergy'] != null ? $inverters_data['dv_Inv' . $i . 'DailyEnergy'] : 0;
//                                            }
                                        if ($daily_inverter_detail_exist != null) {
                                            if($daily_input['daily_generation'] != 0  &&   Date('Y-m-d', strtotime($inverters_data['lastUpdated'])) == Date('Y-m-d')){
                                                $daily_inverter_detail_insertion_responce = $daily_inverter_detail_exist->fill($daily_input)->save();
                                            }
                                            // $daily_inverter_detail_insertion_responce = $daily_inverter_detail_exist->fill($daily_input)->save();
                                        } else {
                                            $daily_inverter_detail_insertion_responce = DailyInverterDetail::create($daily_input);
                                        }
//                                        }

//                                            $daily_inverter_gen_sum_for_month = DailyInverterDetail::where('plant_id',$plant->id)->where('siteId',$inverters_data['siteId'])->where('dv_inverter',$inverter_input['dv_inverter'])->whereYear('created_at', '=', date('Y'))->whereMonth('created_at', '=', date('m'))->sum('daily_generation');
                                        $monthly_inverter_detail_exist = MonthlyInverterDetail::where('plant_id', $plant->id)->where('siteId', $inverters_data['siteId'])->where('dv_inverter', $inverter_input['dv_inverter'])->whereYear('created_at', '=', date('Y'))->whereMonth('created_at', '=', date('m'))->first();
//                                            return $daily_inverter_gen_sum_for_month;
                                        if ($daily_inverter_detail_exist) {

                                            $daily_inverter_gen_sum_for_month = isset($inverters_data['dv_Inv' . $i . 'MonthlyEnergy']) && !empty($inverters_data['dv_Inv' . $i . 'DailyEnergy']) && $inverters_data['dv_Inv' . $i . 'MonthlyEnergy'] != null ? $inverters_data['dv_Inv' . $i . 'MonthlyEnergy'] : $monthly_inverter_detail_exist['monthly_generation'];
                                        } else {

                                            $daily_inverter_gen_sum_for_month = isset($inverters_data['dv_Inv' . $i . 'MonthlyEnergy']) && !empty($inverters_data['dv_Inv' . $i . 'DailyEnergy']) && $inverters_data['dv_Inv' . $i . 'MonthlyEnergy'] != null ? $inverters_data['dv_Inv' . $i . 'MonthlyEnergy'] : 0;
                                        }
//return $daily_inverter_gen_sum_for_month;
                                        $monthly_input['plant_id'] = $plant->id;
                                        $monthly_input['siteId'] = $inverters_data['siteId'];
                                        $monthly_input['dv_inverter'] = 'dv_Inv' . $i;
                                        $monthly_input['lastUpdated'] = $inverters_data['lastUpdated'];
                                        $monthly_input['created_at'] = Date('Y-m-d H:i:s', strtotime($inverters_data['lastUpdated']));
                                        $monthly_input['updated_at'] = Date('Y-m-d H:i:s');
                                        if($monthly_inverter_detail_exist) {
                                            $DB_last_monthly_inverter_Generation = $monthly_inverter_detail_exist->monthly_generation;
                                        }
                                        else{
                                            $DB_last_monthly_inverter_Generation = 0;
                                        }

                                        if($daily_inverter_gen_sum_for_month == 0){

                                            $monthly_input['monthly_generation'] = $DB_last_monthly_inverter_Generation;

                                        }else {

                                            $monthly_input['monthly_generation'] = $daily_inverter_gen_sum_for_month;

                                        }
                                        $monthly_input['monthly_generation'] = $daily_inverter_gen_sum_for_month;

                                        if ($monthly_inverter_detail_exist) {
                                            $monthly_inverter_detail_insertion_responce = $monthly_inverter_detail_exist->fill($monthly_input)->save();
                                        } else {
                                            $monthly_inverter_detail_insertion_responce = MonthlyInverterDetail::create($monthly_input);
                                        }

                                        $yearly_inverter_detail_exist = YearlyInverterDetail::where('plant_id', $plant->id)->where('siteId', $inverters_data['siteId'])->where('dv_inverter', $inverter_input['dv_inverter'])->whereYear('created_at', '=', date('Y'))->first();

                                        $monthly_generation_sum = MonthlyInverterDetail::where('plant_id', $plant->id)->where('siteId', $inverters_data['siteId'])->where('dv_inverter', $inverter_input['dv_inverter'])->whereYear('created_at', '=', date('Y'))->sum('monthly_generation');

                                        $yearly_input['plant_id'] = $plant->id;
                                        $yearly_input['siteId'] = $inverters_data['siteId'];
                                        $yearly_input['dv_inverter'] = 'dv_Inv' . $i;
                                        $yearly_input['yearly_generation'] = $monthly_generation_sum ? (double)$monthly_generation_sum : 0;
                                        $yearly_input['lastUpdated'] = $inverters_data['lastUpdated'];
                                        $yearly_input['created_at'] = Date('Y-m-d H:i:s', strtotime($inverters_data['lastUpdated']));
                                        $yearly_input['updated_at'] = Date('Y-m-d H:i:s');

                                        if ($yearly_inverter_detail_exist) {

                                            $yearly_inverter_detail_insertion_responce = $yearly_inverter_detail_exist->fill($yearly_input)->save();
                                        } else {
                                            $yearly_inverter_detail_insertion_responce = YearlyInverterDetail::create($yearly_input);
                                        }
                                    }
//                                    return $plant_inverter_final_data->numberOfInverters;

//                                    $dataArray = explode(',',$inverters_data);
//                                    return $dataArray;
//                                    return $inverters_data;
//                                    $count = 0;
//                                    for ($i = 1; $i <= (int)$plant_inverter_final_data->numberOfInverters; $i++) {
//                                        $mpptDataArray = [];
//                                        for ($k = 0; $k < count($inverterArrayKeys); $k++) {
//                                            $search = 'dv_Inv' . $i . 'Mppt';
//                                            if (preg_match("/{$search}/i", $inverterArrayKeys[$k])) {
//                                                array_push($mpptDataArray, $count = $count + 1);
//                                            }
//                                        }
////                                        return $mpptDataArray;
//
//                                        $inverters_data = (array)$plant_inverter_final_data;
//
//                                        $inverter['plant_id'] = $plant->id;
//                                        $inverter['siteId'] = $inverters_data['siteId'];
//                                        $inverter['dv_inverter'] = 'dv_Inv' . $i;
//                                        $inverter['daily_generation'] = isset($inverters_data['dv_Inv' . $i . 'DailyEnergy']) ? $inverters_data['dv_Inv' . $i . 'DailyEnergy'] : null;
//                                        $inverter['monthly_generation'] = isset($inverters_data['dv_Inv' . $i . 'MonthlyEnergy']) ? $inverters_data['dv_Inv' . $i . 'MonthlyEnergy'] : null;
//                                        $inverter['inverterPower'] = isset($inverters_data['inverter' . $i . 'Power']) ? $inverters_data['inverter' . $i . 'Power'] : null;
//                                        $inverter['inverterEnergy'] = isset($inverters_data['inverter' . $i . 'Energy']) ? $inverters_data['inverter' . $i . 'Energy'] : null;
//                                        $inverter['totalInverterPower'] = isset($inverters_data['totalInverterPower']) ? $inverters_data['totalInverterPower'] : null;
//                                        $inverter['inverterLimitValue'] = isset($inverters_data['inverter' . $i . 'LimitValue']) ? $inverters_data['inverter' . $i . 'LimitValue'] : null;
//                                        $inverter['inverterCommFail'] = isset($inverters_data['inverterCommFail']) ? $inverters_data['inverterCommFail'] : null;
//                                        $inverter['inverterConfigFail'] = isset($inverters_data['inverterConfigFail']) ? $inverters_data['inverterConfigFail'] : null;
//                                        $inverter['inverterUptime'] = isset($inverters_data['inverter' . $i . 'Uptime']) ? $inverters_data['inverter' . $i . 'Uptime'] : null;
//                                        $inverter['ac_output_power'] = isset($inverters_data['inverter' . $i . 'Power']) ? $inverters_data['inverter' . $i . 'Power'] : null;
//                                        $inverter['total_generation'] = isset($inverters_data['dv_Inv' . $i . 'TotalEnergy']) ? $inverters_data['dv_Inv' . $i . 'TotalEnergy'] : null;
////                                        $inverter['l_voltage1'] = isset($inverters_data['dv_Inv' . $i . 'Mppt1Voltage']) ? $inverters_data['dv_Inv' . $i . 'Mppt1Voltage'] : null;
////                                        $inverter['l_current1'] = isset($inverters_data['dv_Inv' . $i . 'Mppt1Current']) ? $inverters_data['dv_Inv' . $i . 'Mppt1Current'] : null;
////                                        $inverter['l_voltage2'] = isset($inverters_data['dv_Inv' . $i . 'Mppt2Voltage']) ? $inverters_data['dv_Inv' . $i . 'Mppt2Voltage'] : null;
////                                        $inverter['l_current2'] = isset($inverters_data['dv_Inv' . $i . 'Mppt2Current']) ? $inverters_data['dv_Inv' . $i . 'Mppt2Current'] : null;
////                                        $inverter['l_voltage3'] = isset($inverters_data['dv_Inv' . $i . 'Mppt3Voltage']) ? $inverters_data['dv_Inv' . $i . 'Mppt3Voltage'] : null;
////                                        $inverter['l_current3'] = isset($inverters_data['dv_Inv' . $i . 'Mppt3Current']) ? $inverters_data['dv_Inv' . $i . 'Mppt3Current'] : null;
//                                        $inverter['phase_voltage_r'] = isset($inverters_data['dv_Inv' . $i . 'L1Voltage']) ? $inverters_data['dv_Inv' . $i . 'L1Voltage'] : null;
//                                        $inverter['phase_current_r'] = isset($inverters_data['dv_Inv' . $i . 'L1Current']) ? $inverters_data['dv_Inv' . $i . 'L1Current'] : null;
//                                        $inverter['phase_voltage_s'] = isset($inverters_data['dv_Inv' . $i . 'L2Voltage']) ? $inverters_data['dv_Inv' . $i . 'L2Voltage'] : null;
//                                        $inverter['phase_current_s'] = isset($inverters_data['dv_Inv' . $i . 'L2Current']) ? $inverters_data['dv_Inv' . $i . 'L2Current'] : null;
//                                        $inverter['phase_voltage_t'] = isset($inverters_data['dv_Inv' . $i . 'L3Voltage']) ? $inverters_data['dv_Inv' . $i . 'L3Voltage'] : null;
//                                        $inverter['phase_current_t'] = isset($inverters_data['dv_Inv' . $i . 'L3Current']) ? $inverters_data['dv_Inv' . $i . 'L3Current'] : null;
//                                        $inverter['frequency'] = isset($inverters_data['dv_Inv' . $i . 'Frequency']) ? $inverters_data['dv_Inv' . $i . 'Frequency'] : null;
//                                        $inverter['dc_power'] = isset($inverters_data['dv_Inv' . $i . 'TotalDcPower']) ? $inverters_data['dv_Inv' . $i . 'TotalDcPower'] : null;
//                                        $inverter['numberOfInverters'] = isset($inverters_data['numberOfInverters']) ? $inverters_data['numberOfInverters'] : null;
//                                        $inverter['lastUpdated'] = isset($inverters_data['lastUpdated']) ? $inverters_data['lastUpdated'] : null;
//                                        $inverter['created_at'] = Date('Y-m-d H:i:s');
//                                        $inverter['updated_at'] = Date('Y-m-d H:i:s');
//
//                                        $inverter_detail_insertion_responce = InverterDetail::create($inverter);
//                                        $inverterDataDEtail = 'dv_Inv' . $i;
//                                        $mpptArrayDetails = count($mpptDataArray) / 2;
//                                        for ($mi = 1; $mi <= $mpptArrayDetails; $mi++) {
//                                            $plantID = $plant->id;
//                                            $siteID = $inverters_data['siteId'];
//                                            $collectTime = date('Y-m-d H:i:s', strtotime($inverters_data['lastUpdated']));
//                                            $mpptData = InverterMPPTDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $inverterDataDEtail, 'mppt_number' => $mi])->where('collect_time', $collectTime)->orderBy('collect_time', 'desc')->first();
//                                            if (!$mpptData) {
//                                                $inverterMPPTLog['mppt_voltage'] = 'dv_Inv' . $i . 'Mppt' . $mi . 'Voltage';
//                                                $inverterMPPTLog['mppt_current'] = 'dv_Inv' . $i . 'Mppt' . $mi . 'Current';
//                                                $inverterMPPTLog['collect_time'] = $collectTime;
//                                                $inverterMPPTLog['plant_id'] = $plantID;
//                                                $inverterMPPTLog['site_id'] = $siteID;
//                                                $inverterMPPTLog['dv_inverter'] = $inverterDataDEtail;
//                                                $inverterMPPTLog['mppt_number'] = $mi;
//                                                $inverterMPPTResponse = InverterMPPTDetail::create($inverterMPPTLog);
//                                            }
//                                    }
//                                    // echo '<pre>';print_r($inverter_detail_insertion_responce);exit;
//                                    if ($inverter_detail_insertion_responce) {
//                                        //echo '1 Plant Detail';
//                                    } else {
//                                        // echo '0 Sorry! Inverter data are retrived But plant detail insertion Failed.';
//                                    }
//                                }


                                    for ($i = 1; $i <= (int)$plant_inverter_final_data->numberOfInverters; $i++) {
                                        //dd($plant_inverter_final_data);
                                        $inverters_data = (array)$plant_inverter_final_data;
                                        $user_ids = PlantUser::where('plant_id', $plant->id)->get();

                                        if (isset($inverters_data['dv_Inv' . $i . 'OutputType'])/* && $inverters_data['dv_Inv'.$i.'OutputType'] != null*/) {

                                            if ($inverters_data['dv_Inv' . $i . 'OutputType'] > 2) {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['dv_Inv' . $i . 'OutputType'])->where('api_param', 'dv_InvOutputType')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv' . $i, $plant->id, $inverters_data, $fault_data, 'dv_Inv' . $i . 'OutputType', $user_ids);
                                                }
                                            } else {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['dv_Inv' . $i . 'OutputType'])->where('api_param', 'dv_InvOutputType')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                                }
                                            }
                                        }

                                        if (isset($inverters_data['dv_Inv' . $i . 'FaultCode'])/* && $inverters_data['dv_Inv'.$i.'FaultCode'] != null*/) {
                                            if ($inverters_data['dv_Inv' . $i . 'FaultCode'] > 0) {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['dv_Inv' . $i . 'FaultCode'])->where('api_param', 'dv_InvFaultCode')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv' . $i, $plant->id, $inverters_data, $fault_data, 'dv_Inv' . $i . 'FaultCode', $user_ids);
                                                }
                                            } else {
                                                $fault_data = FaultAndAlarm::where('alarm_code', (int)$inverters_data['dv_Inv' . $i . 'FaultCode'])->where('api_param', 'dv_InvFaultCode')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                                }
                                            }
                                        }

                                        if (isset($inverters_data['dv_Inv' . $i . 'PIDAlarmCode'])/* && $inverters_data['dv_Inv'.$i.'PIDAlarmCode'] != null*/) {

                                            if ($inverters_data['dv_Inv' . $i . 'PIDAlarmCode'] > 0) {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['dv_Inv' . $i . 'PIDAlarmCode'])->where('api_param', 'dv_InvPIDAlarmCode')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv' . $i, $plant->id, $inverters_data, $fault_data, 'dv_Inv' . $i . 'PIDAlarmCode', $user_ids);
                                                }
                                            } else {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['dv_Inv' . $i . 'PIDAlarmCode'])->where('api_param', 'dv_InvPIDAlarmCode')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                                }
                                            }
                                        }

                                        if (isset($inverters_data['dv_Inv' . $i . 'PIDWorkState'])/* && $inverters_data['dv_Inv'.$i.'PIDWorkState'] != null*/) {

                                            if ($inverters_data['dv_Inv' . $i . 'PIDWorkState'] > 0) {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['dv_Inv' . $i . 'PIDWorkState'])->where('api_param', 'dv_InvPIDWorkState')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv' . $i, $plant->id, $inverters_data, $fault_data, 'dv_Inv' . $i . 'PIDWorkState', $user_ids);
                                                }
                                            } else {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['dv_Inv' . $i . 'PIDWorkState'])->where('api_param', 'dv_InvPIDWorkState')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                                }
                                            }
                                        }

                                        if (isset($inverters_data['dv_Inv' . $i . 'WorkState1'])/* && $inverters_data['dv_Inv'.$i.'WorkState1'] != null*/) {

                                            if ($inverters_data['dv_Inv' . $i . 'WorkState1'] > 0) {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['dv_Inv' . $i . 'WorkState1'])->where('api_param', 'dv_InvWorkState1')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv' . $i, $plant->id, $inverters_data, $fault_data, 'dv_Inv' . $i . 'WorkState1', $user_ids);
                                                }
                                            } else {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['dv_Inv' . $i . 'WorkState1'])->where('api_param', 'dv_InvWorkState1')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                                }
                                            }
                                        }

                                        if (isset($inverters_data['inverter' . $i . 'CommFail'])/* && $inverters_data['inverter'.$i.'CommFail'] != null*/) {

                                            if ($inverters_data['inverter' . $i . 'CommFail'] > 0) {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['inverter' . $i . 'CommFail'])->where('api_param', 'inverterCommFail')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv' . $i, $plant->id, $inverters_data, $fault_data, 'inverter' . $i . 'CommFail', $user_ids);
                                                }
                                            } else {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['inverter' . $i . 'CommFail'])->where('api_param', 'inverterCommFail')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                                }
                                            }
                                        }

                                        /*if(isset($inverters_data['inverterConfigFail']) && $inverters_data['inverterConfigFail'] != null) {

                                            if($inverters_data['inverterConfigFail'] > 0){
                                                $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['inverterConfigFail'])->where('api_param','inverterConfigFail')->first();
                                                if($fault_data) {

                                                    $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'inverterConfigFail',$user_ids);
                                                }
                                            }else{
                                                $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['inverterConfigFail'])->where('api_param','inverterConfigFail')->first();
                                                if($fault_data) {

                                                    $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                                }
                                            }
                                        }*/
                                    }

                                    if ($plant->system_type != 1) {

                                        if (isset($inverters_data['exportLimitEnabled'])/* && $inverters_data['exportLimitEnabled'] != null*/) {

                                            if ($inverters_data['exportLimitEnabled'] > 0) {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['exportLimitEnabled'])->where('api_param', 'exportLimitEnabled')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_insertion($generation_log_created_time, 'dv_Inv' . $i, $plant->id, $inverters_data, $fault_data, 'exportLimitEnabled', $user_ids);
                                                }
                                            } else {
                                                $fault_data = FaultAndAlarm::where('alarm_code', $inverters_data['exportLimitEnabled'])->where('api_param', 'exportLimitEnabled')->first();
                                                if ($fault_data) {

                                                    $this->faults_data_updation($generation_log_created_time, $plant->id, $fault_data);
                                                }
                                            }
                                        }

                                        /*if($plant->meter_type == 'Saltec'){

                                            if(isset($inverters_data['meterCommFail']) && $inverters_data['meterCommFail'] != null) {

                                                if($inverters_data['meterCommFail'] > 0){
                                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['meterCommFail'])->where('api_param','meterCommFail')->first();
                                                    if($fault_data) {

                                                        $this->faults_data_insertion($generation_log_created_time,'dv_Inv'.$i,$plant->id,$inverters_data,$fault_data,'meterCommFail',$user_ids);
                                                    }
                                                }else{
                                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['meterCommFail'])->where('api_param','meterCommFail')->first();
                                                    if($fault_data) {

                                                        $this->faults_data_updation($generation_log_created_time,$plant->id,$fault_data);
                                                    }
                                                }
                                            }
                                        }*/
                                    }
                                }

                                //Generation Log Data
                                $generation_log['plant_id'] = $plant->id;
                                $generation_log['siteId'] = $site->site_id;
                                $generation_log['current_generation'] = isset($plant_inverter_final_data->totalInverterPower) && $plant_inverter_final_data->totalInverterPower ? $plant_inverter_final_data->totalInverterPower : 0;
                                $generation_log['comm_failed'] = 0;

                                if ($plant_id_value != null) {

                                    $generation_log['cron_job_id'] = 0;
                                } else {

                                    $generation_log['cron_job_id'] = isset($generation_log_cron_job_id) ? (int)$generation_log_cron_job_id + 1 : 0;
                                }
                                $generation_log['cron_job_type'] = "API2";
                                if ($plant->system_type == 1) {

                                    $generation_log['current_consumption'] = isset($plant_inverter_final_data->totalInverterPower) && $plant_inverter_final_data->totalInverterPower ? $plant_inverter_final_data->totalInverterPower : 0;
                                } else if ($plant->system_type == 2) {

                                    $generation_log['current_consumption'] = isset($plant_inverter_final_data->totalLoadPower) && $plant_inverter_final_data->totalLoadPower ? $plant_inverter_final_data->totalLoadPower : 0;
                                }

                                $generation_log['current_grid'] = isset($plant_inverter_final_data->totalGridPower) && $plant_inverter_final_data->totalGridPower ? $plant_inverter_final_data->totalGridPower : 0;
                                $generation_log['lastUpdated'] = isset($plant_inverter_final_data->lastUpdated) && $plant_inverter_final_data->lastUpdated ? $plant_inverter_final_data->lastUpdated : Date('Ymd') . 'T' . Date('His');

                                $generation_log['created_at'] = $generation_log_created_time;
                                $generation_log['updated_at'] = $generation_log_created_time;

                                $generation_log['totalEnergy'] = 0;

                                if (isset($plant_inverter_final_data->numberOfInverters) && $plant_inverter_final_data->numberOfInverters != null && (int)$plant_inverter_final_data->numberOfInverters != 0) {

                                    for ($i = 1; $i <= (int)$plant_inverter_final_data->numberOfInverters; $i++) {

                                        $existing_totalEnergy = DailyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', 'dv_Inv' . $i)->whereDate('created_at', '=', date('Y-m-d'))->first();
                                        // dd($existing_totalEnergy);
                                        $generation_log['totalEnergy'] += $existing_totalEnergy ? $existing_totalEnergy->daily_generation : 0;
                                    }
                                } else {

                                    $existing_totalEnergy = GenerationLog::where('plant_id', $plant->id)->where('siteId', $site->site_id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first();

                                    $generation_log['totalEnergy'] = $existing_totalEnergy ? $existing_totalEnergy->totalEnergy : 0;
                                }

                                $generation_log_responce = new GenerationLog();

                                $generation_log_responce->plant_id = $generation_log['plant_id'];
                                $generation_log_responce->siteId = $generation_log['siteId'];
                                $generation_log_responce->current_generation = $generation_log['current_generation'];
                                $generation_log_responce->current_consumption = $generation_log['current_consumption'];
                                $generation_log_responce->current_grid = $generation_log['current_grid'];
                                $generation_log_responce->cron_job_type = $generation_log['cron_job_type'];
                                $generation_log_responce->cron_job_id = $generation_log['cron_job_id'];
                                $generation_log_responce->lastUpdated = $generation_log['lastUpdated'];
                                $generation_log_responce->created_at = $generation_log['created_at'];
                                $generation_log_responce->collect_time = $generation_log['created_at'];
                                $generation_log_responce->updated_at = $generation_log['updated_at'];
                                $generation_log_responce->totalEnergy = $generation_log['totalEnergy'];

                                $generation_log_responce->save();

                            }
                        }
                        $processed_dailyGeneration_APi = isset($final_processed_data->dailySolarEnergy) && $final_processed_data->dailySolarEnergy ? $final_processed_data->dailySolarEnergy : 0;
                        $processed_monthlyGeneration_APi = isset($final_processed_data->monthlySolarEnergy) && $final_processed_data->monthlySolarEnergy ? $final_processed_data->monthlySolarEnergy : 0;
                        $Daily_processed_generation_api = array_push($final_processed_Dailygeneration,$processed_dailyGeneration_APi);
                        $Monthly_processed_generation_api = array_push($final_processed_Monthlygeneration,$processed_monthlyGeneration_APi);
                    }
                    $sum_Dailyproccessed_generation_Goodwe = array_sum($final_processed_Dailygeneration);
                    $sum_Monthlyproccessed_generation_fromApi = array_sum($final_processed_Monthlygeneration);

                    $dailyGenerationVar = DailyInverterDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d'))->sum('daily_generation');
//                    $monthlyGenerationVar = MonthlyInverterDetail::where('plant_id', $plant->id)->whereMonth('created_at', date('m'))->sum('monthly_generation');
                    $monthlyGenerationVar = $sum_Monthlyproccessed_generation_fromApi;
                    $dailyBoughtEnergy =
                        ProcessedPlantDetail::where('plant_id', $plant->id)->where('cron_job_type','API2')->where('cron_job_id', $processed_plant_cron_job_id + 1)->sum('dailyBoughtEnergy');
                    $dailyConsumptionn =
                        ProcessedPlantDetail::where('plant_id', $plant->id)->where('cron_job_type','API2')->where('cron_job_id', $processed_plant_cron_job_id + 1)->sum('dailyConsumption');
                    $dailySellEnergy =
                        ProcessedPlantDetail::where('plant_id', $plant->id)->where('cron_job_type','API2')->where('cron_job_id', $processed_plant_cron_job_id + 1)->sum('dailySellEnergy');
                    $monthlyBoughtEnergy =
                        ProcessedPlantDetail::where('plant_id', $plant->id)->where('cron_job_type','API2')->where('cron_job_id', $processed_plant_cron_job_id + 1)->sum('monthlyBoughtEnergy');
                    $monthlyConsumptionn =
                        ProcessedPlantDetail::where('plant_id', $plant->id)->where('cron_job_type','API2')->where('cron_job_id', $processed_plant_cron_job_id + 1)->sum('monthlyConsumption');
                    $monthlySellEnergy =
                        ProcessedPlantDetail::where('plant_id', $plant->id)->where('cron_job_type','API2')->where('cron_job_id', $processed_plant_cron_job_id + 1)->sum('monthlySellEnergy');
                    $yearlyBoughtEnergy =
                        ProcessedPlantDetail::where('plant_id', $plant->id)->where('cron_job_type','API2')->where('cron_job_id', $processed_plant_cron_job_id + 1)->sum('yearlyBoughtEnergy');
                    $yearlyConsumptionn =
                        ProcessedPlantDetail::where('plant_id', $plant->id)->where('cron_job_type','API2')->where('cron_job_id', $processed_plant_cron_job_id + 1)->sum('yearlyConsumption');
                    $yearlySellEnergy =
                        ProcessedPlantDetail::where('plant_id', $plant->id)->where('cron_job_type','API2')->where('cron_job_id', $processed_plant_cron_job_id + 1)->sum('yearlySellEnergy');

                    if ($plant->system_type == 1) {

                        if ($plant->meter_type == "Saltec-Goodwe"){
                            $dailyGenerationVar = $sum_Dailyproccessed_generation_Goodwe;
                            $dailyConsumptionVar = $dailyGenerationVar;
                            $dailyGridVar = 0;
                            $dailyBoughtEnergyVar = $dailyBoughtEnergy;
                            $dailySellEnergyVar = $dailySellEnergy;

                            $monthlyGenerationVar = $sum_Monthlyproccessed_generation_fromApi;
                            $monthlyConsumptionVar = $monthlyGenerationVar;
                            $monthlyGridVar = 0;
                            $monthlyBoughtEnergyVar = $monthlyBoughtEnergy;
                            $monthlySellEnergyVar = $monthlySellEnergy;

                            $yearlyGridVar = 0;
                            $yearlyBoughtEnergyVar = $yearlyBoughtEnergy;
                            $yearlySellEnergyVar = $yearlySellEnergy;
                        }else{

                            $dailyConsumptionVar = $dailyGenerationVar;
                            $dailyGridVar = 0;
                            $dailyBoughtEnergyVar = $dailyBoughtEnergy;
                            $dailySellEnergyVar = $dailySellEnergy;

                            $monthlyConsumptionVar = $monthlyGenerationVar;
                            $monthlyGridVar = 0;
                            $monthlyBoughtEnergyVar = $monthlyBoughtEnergy;
                            $monthlySellEnergyVar = $monthlySellEnergy;

                            $yearlyGridVar = 0;
                            $yearlyBoughtEnergyVar = $yearlyBoughtEnergy;
                            $yearlySellEnergyVar = $yearlySellEnergy;
                        }

                    } else if ($plant->system_type == 2) {

                        if ($plant->meter_type == "Saltec") {

                            $dailyConsumptionVar = $dailyConsumptionn;
                            $dailyGridVar = $dailyBoughtEnergy;
                            $dailyBoughtEnergyVar = $dailyBoughtEnergy;
                            $dailySellEnergyVar = $dailySellEnergy;

                            $monthlyConsumptionVar = $monthlyConsumptionn;
                            $monthlyGridVar = $monthlyBoughtEnergy;
                            $monthlyBoughtEnergyVar = $monthlyBoughtEnergy;
                            $monthlySellEnergyVar = $monthlySellEnergy;

                            $yearlyConsumptionVar = $yearlyConsumptionn;
                            $yearlyGridVar = $yearlyBoughtEnergy;
                            $yearlyBoughtEnergyVar = $yearlyBoughtEnergy;
                            $yearlySellEnergyVar = $yearlySellEnergy;
                        } elseif ($plant->meter_type == "Saltec-Goodwe"){
                            $dailyGenerationVar = $sum_Dailyproccessed_generation_Goodwe;
                            $dailyConsumptionVar = $dailyConsumptionn;
                            $dailyGridVar = $dailyBoughtEnergy;
                            $dailyBoughtEnergyVar = $dailyBoughtEnergy;
                            $dailySellEnergyVar = $dailySellEnergy;

                            $monthlyGenerationVar = $sum_Monthlyproccessed_generation_fromApi;
                            $monthlyConsumptionVar = $monthlyConsumptionn;
                            $monthlyGridVar = $monthlyBoughtEnergy;
                            $monthlyBoughtEnergyVar = $monthlyBoughtEnergy;
                            $monthlySellEnergyVar = $monthlySellEnergy;

                            $yearlyConsumptionVar = $yearlyConsumptionn;
                            $yearlyGridVar = $yearlyBoughtEnergy;
                            $yearlyBoughtEnergyVar = $yearlyBoughtEnergy;
                            $yearlySellEnergyVar = $yearlySellEnergy;
                        }else if ($plant->meter_type == "Microtech" || $plant->meter_type == "Microtech-Goodwe") {

                            if($plant->meter_type == "Microtech-Goodwe"){
                                $dailyGenerationVar = $sum_Dailyproccessed_generation_Goodwe;
                            }
                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => "http://202.59.74.91:2030/authorization_service",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 10,
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
                            // echo '<pre>';print_r($token);exit;

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
                                    echo "cURL Error 5 #:" . $err . "meter serial #" . $plant->meter_serial_no;
                                }
                                $processed_data = json_decode($response2);
                                //$final_processed_data_micro = json_decode($response2, true)['data'];

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

//                                $latest_start_datetime = MicrotechPowerGenerationLog::where('meter_serial_no', $plant->meter_serial_no)->orderBy('db_datetime', 'DESC')->first();
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
                                    echo "cURL Error 6 #:" . $err;
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
                            //dd($latest_micro_daily_record, $previous_micro_daily_record);

                            $latest_energy_pos_tl_data = $latest_micro_daily_record ? $latest_micro_daily_record->active_energy_pos_tl : 0;
                            $latest_energy_neg_tl_data = $latest_micro_daily_record ? $latest_micro_daily_record->active_energy_neg_tl : 0;
                            $previous_energy_pos_tl_data = $previous_micro_daily_record ? $previous_micro_daily_record->active_energy_pos_tl : 0;
                            $previous_energy_neg_tl_data = $previous_micro_daily_record ? $previous_micro_daily_record->active_energy_neg_tl : 0;

                            $daily_active_energy_pos_tl = $latest_energy_pos_tl_data - $previous_energy_pos_tl_data > 0 ? $latest_energy_pos_tl_data - $previous_energy_pos_tl_data : 0;
                            $daily_active_energy_neg_tl = $latest_energy_neg_tl_data - $previous_energy_neg_tl_data > 0 ? $latest_energy_neg_tl_data - $previous_energy_neg_tl_data : 0;
                            //dd($daily_active_energy_pos_tl, $daily_active_energy_neg_tl);
                            $dailyBoughtEnergyVar = $daily_active_energy_pos_tl * $plant->ratio_factor;
                            $dailySellEnergyVar = $daily_active_energy_neg_tl * $plant->ratio_factor;
                            $dailyGridVar = $dailyBoughtEnergyVar > $dailySellEnergyVar ? $dailyBoughtEnergyVar - $dailySellEnergyVar : $dailySellEnergyVar - $dailyBoughtEnergyVar;
                            $dailyConsumptionVar = ($dailyGenerationVar + ($dailyBoughtEnergyVar - $dailySellEnergyVar)) > 0 ? ($dailyGenerationVar + ($dailyBoughtEnergyVar - $dailySellEnergyVar)) : 0;

                            //Monthly Consumption Value
                            /*$latest_micro_monthly_record = MicrotechEnergyGenerationLog::where('meter_serial_no',$plant->meter_serial_no)->whereMonth('db_datetime', date('m'))->orderBy('db_datetime', 'DESC')->first();
                            $previous_micro_monthly_record = MicrotechEnergyGenerationLog::where('meter_serial_no',$plant->meter_serial_no)->whereMonth('db_datetime', date("m",strtotime("-1 month")))->orderBy('db_datetime', 'DESC')->first();

                            $latest_energy_pos_tl_data_m = $latest_micro_monthly_record ? $latest_micro_monthly_record->active_energy_pos_tl : 0;
                            $latest_energy_neg_tl_data_m = $latest_micro_monthly_record ? $latest_micro_monthly_record->active_energy_neg_tl : 0;
                            $previous_energy_pos_tl_data_m = $previous_micro_monthly_record ? $previous_micro_monthly_record->active_energy_pos_tl : 0;
                            $previous_energy_neg_tl_data_m = $previous_micro_monthly_record ? $previous_micro_monthly_record->active_energy_neg_tl : 0;

                            $monthly_active_energy_pos_tl = $latest_energy_pos_tl_data_m - $previous_energy_pos_tl_data_m > 0 ? $latest_energy_pos_tl_data_m - $previous_energy_pos_tl_data_m : 0;
                            $monthly_active_energy_neg_tl = $latest_energy_neg_tl_data_m - $previous_energy_neg_tl_data_m > 0 ? $latest_energy_neg_tl_data_m - $previous_energy_neg_tl_data_m : 0;*/

                            $plantDailyConsumptionDataSum = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailyConsumption');
                            $plantDailyGridDataSum = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailyGridPower');
                            $plantDailyBoughtDataSum = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailyBoughtEnergy');
                            $plantDailySellDataSum = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailySellEnergy');

                            $monthlyBoughtEnergyVar = $plantDailyBoughtDataSum;
                            $monthlySellEnergyVar = $plantDailySellDataSum;
                            $monthlyGridVar = $plantDailyGridDataSum;
                            $monthlyConsumptionVar = $plantDailyConsumptionDataSum;

                        }
                    }

                    //dd($latest_micro_daily_record, $previous_micro_daily_record, $latest_micro_monthly_record, $previous_micro_monthly_record);

                    //Daily Processed Data
//                    return $plant;
                    $daily_processed['plant_id'] = $plant->id;
//                    if($plant->meter_type == "Saltec-Goodwe"){
//                        $daily_processed['dailyGeneration'] = $sum_Dailyproccessed_generation_Goodwe;
//
//                    }else{
//                        $daily_processed['dailyGeneration'] = $dailyGenerationVar;
//
//                    }
//                    return $dailyGenerationVar;
                    $daily_processed['dailyGeneration'] = $dailyGenerationVar;
                    $daily_processed['dailyConsumption'] = $dailyConsumptionVar;
                    $daily_processed['dailyGridPower'] = $dailyGridVar;
                    $daily_processed['dailyBoughtEnergy'] = $dailyBoughtEnergyVar >= 0 ? $dailyBoughtEnergyVar : 0;
                    $daily_processed['dailySellEnergy'] = $dailySellEnergyVar >= 0 ? $dailySellEnergyVar : 0;
                    $daily_processed['dailyMaxSolarPower'] = 0;
                    $daily_processed['dailySaving'] = (double)$dailyGenerationVar * (double)$plant->benchmark_price;
                    $daily_processed['lastUpdated'] = Date('Ymd') . 'T' . Date('His');
                    $daily_processed['updated_at'] = Date('Y-m-d H:i:s');
                    //dd($daily_processed);
                    $processed_plant_detail_exist = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', '=', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
                    // dd($processed_plant_detail_exist);
                    if ($processed_plant_detail_exist != null) {
                        $processed_plant_detail_insertion_responce = $processed_plant_detail_exist->fill($daily_processed)->save();
                        //dd($processed_plant_detail_insertion_responce);
                    } else {
                        $daily_processed['created_at'] = Date('Y-m-d H:i:s');
                        $processed_plant_detail_insertion_responce = DailyProcessedPlantDetail::create($daily_processed);
                    }

                    $plantDailySavingDataSum = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailySaving');
//                    $monthly_Gene_FromAPI = isset($final_processed_data->monthlySolarEnergy) && $final_processed_data->monthlySolarEnergy ? $final_processed_data->monthlySolarEnergy : 0;

                    //Monthly Processed Data
                    $monthly_processed['plant_id'] = $plant->id;
                    $monthly_processed['monthlyGeneration'] = $monthlyGenerationVar;
                    $monthly_processed['monthlyConsumption'] = $monthlyConsumptionVar;
                    $monthly_processed['monthlyGridPower'] = $monthlyGridVar;
                    $monthly_processed['monthlyBoughtEnergy'] = $monthlyBoughtEnergyVar >= 0 ? $monthlyBoughtEnergyVar : 0;
                    $monthly_processed['monthlySellEnergy'] = $monthlySellEnergyVar >= 0 ? $monthlySellEnergyVar : 0;
                    $monthly_processed['monthlyMaxSolarPower'] = 0;
                    $monthly_processed['monthlySaving'] = $plantDailySavingDataSum;
                    $monthly_processed['lastUpdated'] = Date('Ymd') . 'T' . Date('His');
                    $monthly_processed['updated_at'] = Date('Y-m-d H:i:s');
                    //dd($monthly_processed);
                    $processed_plant_detail_exist = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
                    // dd($processed_plant_detail_exist);
                    if ($processed_plant_detail_exist != null) {
                        $processed_plant_detail_insertion_responce = $processed_plant_detail_exist->fill($monthly_processed)->save();
                    } else {
                        $monthly_processed['created_at'] = Date('Y-m-d H:i:s');
                        $processed_plant_detail_insertion_responce = MonthlyProcessedPlantDetail::create($monthly_processed);
                    }


                    //Yearly Processed Data
                    $yearly_processed_plant_detail_exist = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->sum('monthlyGeneration');

                    if ($yearly_processed_plant_detail_exist != null && $yearly_processed_plant_detail_exist != 0) {
                        $yearlyGenerationVar = $yearly_processed_plant_detail_exist;
                    } else {
                        $yearlyGenerationVar = 0;
                    }

                    if ($plant->system_type == 1) {

                        $yearlyConsumptionVar = $yearlyGenerationVar;
                    } else if ($plant->system_type == 2) {

                        if ($plant->meter_type == "Microtech" || $plant->meter_type == "Microtech-Goodwe") {

                            //Yearly Consumption Value
                            /*$latest_micro_yearly_record = MicrotechEnergyGenerationLog::where('meter_serial_no',$plant->meter_serial_no)->whereYear('db_datetime', date('Y'))->orderBy('db_datetime', 'DESC')->first();
                            $previous_micro_yearly_record = MicrotechEnergyGenerationLog::where('meter_serial_no',$plant->meter_serial_no)->whereYear('db_datetime', date("Y",strtotime("-1 year")))->orderBy('db_datetime', 'DESC')->first();

                            $latest_energy_pos_tl_data = $latest_micro_yearly_record ? $latest_micro_yearly_record->active_energy_pos_tl : 0;
                            $latest_energy_neg_tl_data = $latest_micro_yearly_record ? $latest_micro_yearly_record->active_energy_neg_tl : 0;
                            $previous_energy_pos_tl_data = $previous_micro_yearly_record ? $previous_micro_yearly_record->active_energy_pos_tl : 0;
                            $previous_energy_neg_tl_data = $previous_micro_yearly_record ? $previous_micro_yearly_record->active_energy_neg_tl : 0;

                            $yearly_active_energy_pos_tl = $latest_energy_pos_tl_data - $previous_energy_pos_tl_data > 0 ? $latest_energy_pos_tl_data - $previous_energy_pos_tl_data : 0;
                            $yearly_active_energy_neg_tl = $latest_energy_neg_tl_data - $previous_energy_neg_tl_data > 0 ? $latest_energy_neg_tl_data - $previous_energy_neg_tl_data : 0;*/

                            $plantMonthlyConsumptionDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->sum('monthlyConsumption');
                            $plantMonthlyGridDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->sum('monthlyGridPower');
                            $plantMonthlyBoughtDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->sum('monthlyBoughtEnergy');
                            $plantMonthlySellDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->sum('monthlySellEnergy');

                            $yearlyBoughtEnergyVar = $plantMonthlyBoughtDataSum;
                            $yearlySellEnergyVar = $plantMonthlySellDataSum;
                            $yearlyGridVar = $plantMonthlyGridDataSum;
                            $yearlyConsumptionVar = $plantMonthlyConsumptionDataSum;
                        }
                    }

                    $plantMonthlySavingDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->sum('monthlySaving');

                    $yearly_processed['plant_id'] = $plant->id;
                    $yearly_processed['yearlyGeneration'] = $yearlyGenerationVar;
                    $yearly_processed['yearlyConsumption'] = $yearlyConsumptionVar;
                    $yearly_processed['yearlyGridPower'] = $yearlyGridVar;
                    $yearly_processed['yearlyBoughtEnergy'] = $yearlyBoughtEnergyVar >= 0 ? $yearlyBoughtEnergyVar : 0;
                    $yearly_processed['yearlySellEnergy'] = $yearlySellEnergyVar >= 0 ? $yearlySellEnergyVar : 0;
                    $yearly_processed['yearlyMaxSolarPower'] = 0;
                    $yearly_processed['yearlySaving'] = $plantMonthlySavingDataSum;
                    $yearly_processed['lastUpdated'] = Date('Ymd') . 'T' . Date('His');
                    $yearly_processed['updated_at'] = Date('Y-m-d H:i:s');
                    $processed_plant_detail_exist = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->first();
                    // dd($processed_plant_detail_exist);
                    if ($processed_plant_detail_exist != null) {
                        $processed_plant_detail_insertion_responce = $processed_plant_detail_exist->fill($yearly_processed)->save();
                    } else {
                        $yearly_processed['created_at'] = Date('Y-m-d H:i:s');
                        $processed_plant_detail_insertion_responce = YearlyProcessedPlantDetail::create($yearly_processed);
                    }
                }

            }

            $plant_data = Plant::whereIn('meter_type', ['Microtech', 'Saltec','Saltec-Goodwe','Microtech-Goodwe'])->where('id','>',320)->get(['id', 'plant_name', 'plant_type', 'system_type', 'meter_type', 'meter_serial_no', 'ratio_factor', 'benchmark_price']);
//            $plant_data = Plant::whereIn('meter_type', ['Saltec','Saltec-Goodwe'])->get(['id', 'plant_name', 'plant_type', 'system_type', 'meter_type', 'meter_serial_no', 'ratio_factor', 'benchmark_price']);
//            $plant_data = Plant::where('id',58)->get();

            $generation_log_cron_job_id2 = 0;
            $generation_log_cron_job_id2 = GenerationLog::where('cron_job_type','API2')->max('cron_job_id');
            $processed_cron_job_id = ProcessedCurrentVariable::where('processed_cron_job_type','API2')->max('processed_cron_job_id');

            foreach ($plant_data as $key => $pl_data) {

                $curr_gen = 0;
                $curr_con = 0;
                $curr_grid = 0;
                $tot_energy = 0;
                $processed_curr_data['comm_failed'] = 0;

                $generation_log_data = GenerationLog::where('plant_id', $pl_data->id)->where('cron_job_type','API2')->where('cron_job_id', $generation_log_cron_job_id2)->get();

                if ($generation_log_data) {

                    if ($pl_data->system_type == 1) {

                        foreach ($generation_log_data as $key => $log_data) {

                            $curr_gen += (double)$log_data->current_generation;
                            $curr_con += (double)$log_data->current_consumption;
                            $curr_grid += (double)$log_data->current_grid;
                            $tot_energy += (double)$log_data->totalEnergy;
                        }
                        //dd($curr_gen);
                    } else if ($pl_data->system_type == 2) {

                        if (($pl_data->meter_type == "Saltec") || ($pl_data->meter_type == "Saltec-Goodwe")) {

                            foreach ($generation_log_data as $key => $log_data) {

                                $curr_gen += (double)$log_data->current_generation;
                                $curr_con += (double)$log_data->current_consumption;
                                $curr_grid += (double)$log_data->current_grid;
                                $tot_energy += (double)$log_data->totalEnergy;
                            }

                            if ($curr_grid > 0) {
                                $processed_curr_data['grid_type'] = '+ve';
                            } else {
                                $processed_curr_data['grid_type'] = '-ve';
                            }
                        } else if ($pl_data->meter_type == "Microtech" || $pl_data->meter_type == "Microtech-Goodwe") {

                            foreach ($generation_log_data as $key => $log_data) {

                                $curr_gen += (double)$log_data->current_generation;
                                $tot_energy += (double)$log_data->totalEnergy;
                            }

                            $micro_power_data = MicrotechPowerGenerationLog::where('meter_serial_no', $pl_data->meter_serial_no)->whereDate('db_datetime', date('Y-m-d'))->orderBy('db_datetime', 'DESC')->first();

                            if ($micro_power_data) {

                                $time_difference_micro = $micro_power_data->db_datetime;

                                $interval = abs(strtotime($time_difference_saltec) - strtotime($time_difference_micro));
                                $minutes = round($interval / 60);

                                if ($minutes > 30) {

                                    $micro_power_data->aggregate_active_pwr_pos = 0;
                                    $micro_power_data->aggregate_active_pwr_neg = 0;
                                    $processed_curr_data['comm_failed'] = 1;

                                }

                                if ($micro_power_data->aggregate_active_pwr_pos >= $micro_power_data->aggregate_active_pwr_neg) {

                                    $curr_grid = $micro_power_data->aggregate_active_pwr_pos * $pl_data->ratio_factor;
                                    $curr_con = $curr_gen + $curr_grid;
                                    $processed_curr_data['grid_type'] = '+ve';
                                } else {

                                    $curr_grid = $micro_power_data->aggregate_active_pwr_neg * $pl_data->ratio_factor;
                                    $curr_con = ($curr_gen - $curr_grid) > 0 ? $curr_gen - $curr_grid : 0;
                                    $processed_curr_data['grid_type'] = '-ve';
                                }
                            } else {

                                $curr_con = $curr_gen;
                            }
                        }
                    }
//                    return $curr_gen;
                    $processed_curr_data['plant_id'] = $pl_data->id;
                    $processed_curr_data['current_generation'] = $curr_gen;
                    $processed_curr_data['current_consumption'] = $curr_con;
                    $processed_curr_data['current_grid'] = abs($curr_grid);
                    $processed_curr_data['totalEnergy'] = $tot_energy;
                    $processed_curr_data['current_saving'] = (double)$tot_energy * (double)$pl_data->benchmark_price;
                    $processed_curr_data['processed_cron_job_type'] = 'API2';
                    $processed_curr_data['processed_cron_job_id'] = $processed_cron_job_id + 1;
                    $processed_curr_data['collect_time'] = $generation_log_created_time;
                    $processed_curr_data['created'] = $generation_log_created_time;
                    $processed_curr_data['updated_at'] = $generation_log_created_time;

                    $check_time_diffrnc = ProcessedCurrentVariable::where('plant_id', $pl_data->id)->where('collect_time', 'LIKE', date('Y-m-d H:i', strtotime($generation_log_created_time)) . '%')->first();
                    if (!$check_time_diffrnc) {

                        $processed_current_variable_response = ProcessedCurrentVariable::create($processed_curr_data);
                    }
                }

                $total_generation_exist = TotalProcessedPlantDetail::where('plant_id', $pl_data->id)->first();

                if ($total_generation_exist) {

                    $total_generation_exist->plant_total_current_power = (double)$curr_gen;
                    $total_generation_exist->updated_at = date('Y-m-d H:i:s');

                    $total_generation_exist->save();
                } else {

                    $total_processed = new TotalProcessedPlantDetail();

                    $total_processed->plant_id = $pl_data->id;
                    $total_processed->plant_total_current_power = (double)$curr_gen;

                    $total_processed->save();
                }

            }

        }

        $this->plant_listing_from_saltec($generation_log_created_time, $token);
//        $this->plant_online_status();
        $this->total_generation_record();
//        $weatherController = new PlantsController();
//        $weatherController->get_weather();

        Session::forget('plant_idd');

        $CronJobDetail = CronJobTime::where('id',$cronJobTime->id)->first();
        $CronJobDetail->end_time = date('Y-m-d H:i:s');
        $CronJobDetail->status = 'completed';
        $CronJobDetail->save();

        print_r('Cron Job End Time');
        print_r(date("Y-m-d H:i:s"));
        print_r("\n");

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
            $fault_log['siteId'] = $inverters_data['siteId'];
            $fault_log['dv_inverter'] = $dv_inv;
            $fault_log['fault_and_alarm_id'] = $fault_data['id'];
            $fault_log['status'] = $inverters_data[$string] > 0 ? 'Y' : 'N';
            $fault_log['lastUpdated'] = $inverters_data['lastUpdated'];
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


    private
    function plant_listing_from_saltec($curr_date, $token)
    {
        if ($token) {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->BaseURL ."/api/sites/list?size=1000&startIndex=0&sortProperty&sortOrder&isOnline",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    // Set Here Your Requesred Headers
                    'Content-Type: application/json',
                    'X-API-Version: 1.0',
                    'Authorization: Bearer ' . $token,
                ),
            ));
            $response1 = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                echo "cURL Error 7 #:" . $err;
            }
            $all_plants_data = json_decode($response1);

            if ($all_plants_data) {
                $all_plants_data_final = $all_plants_data->data;

                date_default_timezone_set("Asia/Karachi");

                foreach ($all_plants_data_final as $key => $plant_data) {

                    $plant_exist = PlantSite::where('site_id', $plant_data->siteId)->first();
                    if ($plant_exist) {
                        $plant_input = array(
                            'online_status' => $plant_data->isOnline == (bool)1 ? 'Y' : 'N',
                            'updated_at' => Date('Y-m-d H:i:s', strtotime($curr_date)),
                        );
//                        $this->plant_offline_alert($curr_date, $plant_exist->plant_id, $plant_data->siteId, $plant_input['online_status']);
                        $responce = $plant_exist->fill($plant_input)->save();
                    }

                }

                $this->plant_online_status();

                return true;
            }
        }
    }

    public
    function plant_offline_alert($curr_Date, $plant_id, $site_id, $plant_online)
    {

        $existing_data = FaultAlarmLog::where('plant_id', $plant_id)->where('siteId', $site_id)->where('fault_and_alarm_id', 175)->orderBy('created_at', 'DESC')->first();

        if ($existing_data == Null && $plant_online == 'N') {
            $fault_log['plant_id'] = $plant_id;
            $fault_log['siteId'] = $site_id;
            $fault_log['fault_and_alarm_id'] = 175;
            $fault_log['status'] = 'Y';
            $fault_log['lastUpdated'] = Date('Ymd') . 'T' . Date('His');
            $fault_log['created_at'] = $curr_Date;
            $fault_log['updated_at'] = NUll;
            // dd($fault_log);
            $fault_log_responce = FaultAlarmLog::create($fault_log);

            $users = PlantUser::where('plant_id', $plant_id)->get();
            if ($users) {
                foreach ($users as $key => $user) {
                    $notification['plant_id'] = $plant_id;
                    $notification['user_id'] = $user['user_id'];
                    $notification['fault_and_alarm_id'] = 175;
                    $notification['title'] = 'Alarm';
                    $notification['description'] = 'Site ID-' . $site_id . ' is Offline';
                    $notification['entry_date'] = date('Y-m-d H:i:s');
                    $notification['schedule_date'] = date('Y-m-d H:i:s');
                    $notification['notification_type'] = 'Normal';
                    $notification['alarm_log_id'] = $fault_log_responce->id;
                    $notification['is_msg_app'] = 'Y';
                    $notification['is_msg_sms'] = 'N';
                    $notification['is_msg_email'] = 'N';
                    $notification['is_notification_required'] = 'N';
                    // dd($notification);
                    $notification_responce = Notification::create($notification);
                }
            }
        } else if ($existing_data != Null && $plant_online == 'Y') {
            $fault_log['plant_id'] = $plant_id;
            $fault_log['siteId'] = $site_id;
            $fault_log['fault_and_alarm_id'] = 175;
            $fault_log['status'] = 'N';
            $fault_log['lastUpdated'] = Date('Ymd') . 'T' . Date('His');
            $fault_log['updated_at'] = $curr_Date;
            // dd($fault_log);
            $fault_log_responce = $existing_data->fill($fault_log)->save();
        }

        return 'true';

    }

    public
    function plant_online_status()
    {

        $plant_id_value = Session::get('plant_idd');
        if ($plant_id_value != null) {

            $plants = DB::table('plants')
                ->join('system_type', 'system_type.id', 'plants.system_type')
                ->join('plant_sites', 'plants.id', 'plant_sites.plant_id')
                ->select('plants.*', 'system_type.type as system_type_name', 'plant_sites.site_id')
                ->where('plants.id', '=', $plant_id_value)
                ->get();
        } else {

            $plants = DB::table('plants')
                ->join('system_type', 'system_type.id', 'plants.system_type')
                ->join('plant_sites', 'plants.id', 'plant_sites.plant_id')
                ->select('plants.*', 'system_type.type as system_type_name', 'plant_sites.site_id')
                ->get();
        }

        foreach ($plants as $key => $plant) {

            $plant_status = PlantSite::where('plant_id', $plant->id)->get('online_status');

            $plant_alert_status = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
                ->select('fault_alarm_log.*')
                ->where('fault_alarm_log.plant_id', $plant->id)
                ->where('fault_alarm_log.status', 'Y')
                ->where('fault_and_alarms.type', 'Fault')
                ->count();

            if ($plant_status->contains('online_status', 'Y') && $plant_status->contains('online_status', 'N')) {

                $plant_res = Plant::where('id', $plant->id)->update(array('is_online' => 'P_Y'));
            } else if ($plant_status->contains('online_status', 'Y')) {

                $plant_res = Plant::where('id', $plant->id)->update(array('is_online' => 'Y'));
            } else {

                $plant_res = Plant::where('id', $plant->id)->update(array('is_online' => 'N'));
            }

            if ((int)$plant_alert_status > 0) {

                $plant_res1 = Plant::where('id', $plant->id)->update(array('alarmLevel' => 1));
            } else {

                $plant_res1 = Plant::where('id', $plant->id)->update(array('alarmLevel' => 0));
            }
        }
    }

    public
    function total_generation_record()
    {

        $plants = Plant::get(['id']);
        $envReduction = Setting::where('perimeter', 'env_reduction')->pluck('value')[0];

        foreach ($plants as $key => $pl) {

            $plant_total_generation = YearlyProcessedPlantDetail::where('plant_id', $pl->id)->sum('yearlyGeneration');
            $plant_total_consumption = YearlyProcessedPlantDetail::where('plant_id', $pl->id)->sum('yearlyConsumption');
            $plant_total_grid = YearlyProcessedPlantDetail::where('plant_id', $pl->id)->sum('yearlyGridPower');
            $plant_total_buy_energy = YearlyProcessedPlantDetail::where('plant_id', $pl->id)->sum('yearlyBoughtEnergy');
            $plant_total_sell_energy = YearlyProcessedPlantDetail::where('plant_id', $pl->id)->sum('yearlySellEnergy');
            $plant_total_saving = YearlyProcessedPlantDetail::where('plant_id', $pl->id)->sum('yearlySaving');

            $total_generation_exist = TotalProcessedPlantDetail::where('plant_id', $pl->id)->first();

            $input_gen['plant_total_generation'] = (double)$plant_total_generation;
            $input_gen['plant_total_consumption'] = (double)$plant_total_consumption;
            $input_gen['plant_total_grid'] = (double)$plant_total_grid;
            $input_gen['plant_total_buy_energy'] = (double)$plant_total_buy_energy;
            $input_gen['plant_total_sell_energy'] = (double)$plant_total_sell_energy;
            $input_gen['plant_total_saving'] = (double)$plant_total_saving;
            $input_gen['plant_total_reduction'] = (double)$plant_total_generation * (double)$envReduction;

            if ($total_generation_exist) {

                $input_gen['updated_at'] = date('Y-m-d H:i:s');

                $res = $total_generation_exist->fill($input_gen)->save();
            } else {

                $input_gen['plant_id'] = $pl->id;

                $res = TotalProcessedPlantDetail::create($input_gen);
            }
        }

        $total_acc_generation = TotalProcessedPlantDetail::sum('plant_total_generation');
        $total_acc_reduction = TotalProcessedPlantDetail::sum('plant_total_reduction');
        $total_acc_power = TotalProcessedPlantDetail::sum('plant_total_current_power');

        $total_acc_generation_exist = AccumulativeProcessedDetail::first();

        $input_data['total_current_power'] = $total_acc_power;
        $input_data['total_generation'] = $total_acc_generation;
        $input_data['total_reduction'] = $total_acc_reduction;

        if ($total_acc_generation_exist) {

            $res = $total_acc_generation_exist->fill($input_data)->save();
        } else {

            $res = AccumulativeProcessedDetail::create($input_data);
        }

    }

}
