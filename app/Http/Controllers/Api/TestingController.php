<?php

namespace App\Http\Controllers\Api;

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
use App\Http\Models\PlantType;
use App\Http\Models\PlantSite;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\SystemType;
use App\Http\Models\Setting;
use App\Http\Models\Weather;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\AccumulativeProcessedDetail;
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

class TestingController extends Controller
{
    public function plant_site_data() {

        $early_sunrise = Weather::whereDate('created_at', Date('Y-m-d'))->orderBy('sunrise', 'ASC')->first();
        $sunrise = $early_sunrise && $early_sunrise->sunrise ? explode(':', $early_sunrise->sunrise) : explode(':', '06:00:AM');
        $sunrise_hour = $sunrise[0];
        $sunrise_min = $sunrise[1];

        $micro_itr = 0;
        date_default_timezone_set('Asia/Karachi');
        $time_difference_saltec = Date('Y-m-d H:i:s');
        $time_difference_micro = Date('Y-m-d H:i:s');
        $generation_log_created_time = Date('Y-m-d H:i:s');
        $current_time = Date('Ymd').'T'.Date('His');
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
            CURLOPT_URL => "https://67.23.248.117:8089/api/token",
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
        if($res){
            $token = $res->data;
        }

        if(isset($token) && $token){

            $plants = Plant::whereIn('id', [8,65])->get();

            $generation_log_cron_job_id = GenerationLog::max('cron_job_id');
            $processed_plant_cron_job_id = ProcessedPlantDetail::max('cron_job_id');
            if($plants){
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

                    $plant_sites = PlantSite::where('plant_id', $plant->id)->get();

                        foreach($plant_sites as $key1 => $site) {

                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => "https://67.23.248.117:8089/api/site/live/".$site->site_id,
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

                            if($plant_inverter_data && isset($plant_inverter_data->data)) {
                                $plant_inverter_final_data = $plant_inverter_data->data;
                            }

                            if(isset($plant_inverter_final_data) && $plant_inverter_final_data) {

                                $timestm = $plant_inverter_final_data->lastUpdated;
                                $time_difference_saltec = $timestm;

                                date_default_timezone_set("Asia/Karachi");

                                if($plant_inverter_final_data){

                                    //Plant Processed Data
                                    $curl = curl_init();

                                    curl_setopt_array($curl, array(
                                        CURLOPT_URL => "https://67.23.248.117:8089/api/site/processed/".$site->site_id.'?timestamp='.$current_time,
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

                                    if($processed_data && isset($processed_data->data)) {
                                        $final_processed_data = $processed_data->data;
                                    }
                                }
                            }
                        }

                        $dailyGenerationVar = DailyInverterDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d'))->sum('daily_generation');
                        $monthlyGenerationVar = MonthlyInverterDetail::where('plant_id', $plant->id)->whereMonth('created_at', date('m'))->sum('monthly_generation');
                        $dailyBoughtEnergy =
                        ProcessedPlantDetail::where('plant_id',$plant->id)->where('cron_job_id',$processed_plant_cron_job_id + 1)->sum('dailyBoughtEnergy');
                        $dailyConsumptionn =
                        ProcessedPlantDetail::where('plant_id',$plant->id)->where('cron_job_id',$processed_plant_cron_job_id + 1)->sum('dailyConsumption');
                        $dailySellEnergy =
                        ProcessedPlantDetail::where('plant_id',$plant->id)->where('cron_job_id',$processed_plant_cron_job_id + 1)->sum('dailySellEnergy');
                        $monthlyBoughtEnergy =
                        ProcessedPlantDetail::where('plant_id',$plant->id)->where('cron_job_id',$processed_plant_cron_job_id + 1)->sum('monthlyBoughtEnergy');
                        $monthlyConsumptionn =
                        ProcessedPlantDetail::where('plant_id',$plant->id)->where('cron_job_id',$processed_plant_cron_job_id + 1)->sum('monthlyConsumption');
                        $monthlySellEnergy =
                        ProcessedPlantDetail::where('plant_id',$plant->id)->where('cron_job_id',$processed_plant_cron_job_id + 1)->sum('monthlySellEnergy');
                        $yearlyBoughtEnergy =
                        ProcessedPlantDetail::where('plant_id',$plant->id)->where('cron_job_id',$processed_plant_cron_job_id + 1)->sum('yearlyBoughtEnergy');
                        $yearlyConsumptionn =
                        ProcessedPlantDetail::where('plant_id',$plant->id)->where('cron_job_id',$processed_plant_cron_job_id + 1)->sum('yearlyConsumption');
                        $yearlySellEnergy =
                        ProcessedPlantDetail::where('plant_id',$plant->id)->where('cron_job_id',$processed_plant_cron_job_id + 1)->sum('yearlySellEnergy');

                        if($plant->system_type == 1) {

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

                        else if($plant->system_type == 2) {

                            if($plant->meter_type == "Saltec") {

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
                            }

                            else if($plant->meter_type == "Microtech") {

                                $curl = curl_init();

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => "http://202.59.74.91:2030/authorization_service",
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => "",
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
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
                                if($res){
                                    $privatekey = $res->privatekey;
                                }
                                // echo '<pre>';print_r($token);exit;

                                if(isset($privatekey) && $privatekey){

                                    $latest_start_datetime = MicrotechEnergyGenerationLog::where('meter_serial_no',$plant->meter_serial_no)->orderBy('db_datetime','DESC')->first();

                                    $data1 = [
                                        'global_device_id' => $plant->meter_serial_no,
                                        // 'start_datetime' => $plant->created_at,
                                        'start_datetime' => $latest_start_datetime ? $latest_start_datetime->db_datetime : $plant->created_at,
                                        'end_datetime' => date('Y-m-d H:i:s'),
                                    ];

                                    //Proceess Data getting from Microtech Server
                                    $curl = curl_init();

                                    curl_setopt_array($curl, array(
                                            CURLOPT_URL => "http://202.59.74.91:2030/billing_data",
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => "",
                                            CURLOPT_MAXREDIRS => 10,
                                            CURLOPT_TIMEOUT => 0,
                                            CURLOPT_FOLLOWLOCATION => true,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => "POST",
                                            CURLOPT_POSTFIELDS => $data1,
                                            CURLOPT_HTTPHEADER => array(
                                                "Privatekey:" .$privatekey
                                            ),
                                        ));

                                    $response2 = curl_exec($curl);
                                    $err = curl_error($curl);
                                    curl_close($curl);

                                    if ($err) {
                                        echo "cURL Error 5 #:" . $err;
                                    }
                                    $processed_data = json_decode($response2);
                                    //$final_processed_data_micro = json_decode($response2, true)['data'];

                                    if($processed_data) {

                                        $final_processed_data_micro = $processed_data->data;

                                        foreach($final_processed_data_micro as $key => $micro_data) {

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

                                    $latest_start_datetime = MicrotechPowerGenerationLog::where('meter_serial_no',$plant->meter_serial_no)->orderBy('db_datetime','DESC')->first();

                                    $data1 = [
                                        'global_device_id' => $plant->meter_serial_no,
                                        // 'start_datetime' => $plant->created_at,
                                        'start_datetime' => $latest_start_datetime ? $latest_start_datetime->db_datetime : $plant->created_at,
                                        'end_datetime' => date('Y-m-d H:i:s'),
                                    ];

                                    //Proceess Data getting from Microtech Server
                                    $curl = curl_init();

                                    curl_setopt_array($curl, array(
                                            CURLOPT_URL => "http://202.59.74.91:2030/instantaneous_data",
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_ENCODING => "",
                                            CURLOPT_MAXREDIRS => 10,
                                            CURLOPT_TIMEOUT => 0,
                                            CURLOPT_FOLLOWLOCATION => true,
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_CUSTOMREQUEST => "POST",
                                            CURLOPT_POSTFIELDS => $data1,
                                            CURLOPT_HTTPHEADER => array(
                                                "Privatekey:" .$privatekey
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

                                    if($processed_data) {

                                        $final_processed_data_micro = $processed_data->data;

                                        foreach($final_processed_data_micro as $key => $micro_data) {

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
                                $latest_micro_daily_record = MicrotechEnergyGenerationLog::where('meter_serial_no',$plant->meter_serial_no)->whereDate('db_datetime', date('Y-m-d'))->orderBy('db_datetime', 'DESC')->first();
                                $previous_micro_daily_record = MicrotechEnergyGenerationLog::where('meter_serial_no',$plant->meter_serial_no)->whereDate('db_datetime', date('Y-m-d',strtotime("-1 days")))->orderBy('db_datetime', 'DESC')->first();
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



                        $plantDailySavingDataSum = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('dailySaving');


                        //Yearly Processed Data
                        $yearly_processed_plant_detail_exist = MonthlyProcessedPlantDetail::where('plant_id',$plant->id)->whereYear('created_at', '=', date('Y'))->orderBy('created_at', 'DESC')->sum('monthlyGeneration');

                        if($yearly_processed_plant_detail_exist != null && $yearly_processed_plant_detail_exist != 0) {
                            $yearlyGenerationVar = $yearly_processed_plant_detail_exist;
                        }
                        else {
                            $yearlyGenerationVar = 0;
                        }

                        if($plant->system_type == 1) {

                            $yearlyConsumptionVar = $yearlyGenerationVar;
                        }

                        else if($plant->system_type == 2) {

                            if($plant->meter_type == "Microtech") {

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

                }

            }

            $plant_data = Plant::get(['id', 'plant_name', 'plant_type', 'system_type', 'meter_type', 'meter_serial_no', 'ratio_factor', 'benchmark_price']);


            $generation_log_cron_job_id = GenerationLog::max('cron_job_id');
            $processed_cron_job_id = ProcessedCurrentVariable::max('processed_cron_job_id');


        }

        //$this->plant_listing_from_saltec($generation_log_created_time,$token);
        //$this->plant_online_status();
        //$this->total_generation_record();
        //$weatherController = new PlantsController();
        //$weatherController->get_weather();

        Session::forget('plant_idd');

    }

}
