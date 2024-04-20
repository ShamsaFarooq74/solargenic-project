<?php

namespace App\Console\Commands;

use App\Model\Notification;
use Illuminate\Console\Command;
use App\Http\Models\Plant;
use App\Http\Models\PlantDetail;
use App\Http\Models\InverterDetail;
use App\Http\Models\Inverter;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\PlantUser;
use App\Http\Models\ProcessedPlantDetail;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\GenerationLog;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\PlantType;
use App\Http\Models\SystemType;
use App\Http\Models\Weather;
use App\Http\Models\ExpectedGenerationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlantSiteData extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plantsitedata:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command show plant site data after every 5 minutes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

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
            echo "CURL Authentication Error #:" . $err;
        }
        $res = json_decode($response);
        if($res){
            $token = $res->data;
        }


        if(isset($token) && $token){

            $plants = Plant::all();
            // dd($plants);
            if($plants){
                foreach ($plants as $key => $plant) {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://67.23.248.117:8089/api/site/live/".$plant['siteId'],
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
                        echo "cURL Error #:" . $err;
                    }
                    $plant_inverter_data = json_decode($response1);
                    $plant_inverter_final_data = $plant_inverter_data->data;
                    $this->plant_listing_from_saltec($plant_inverter_final_data->lastUpdated);

                    //echo '<pre>';print_r($plant_inverter_final_data);exit;
                    date_default_timezone_set("Asia/Karachi");

                    if($plant_inverter_final_data){
                        $input['plant_id'] = $plant['id'];
                        $input['siteId'] = $plant_inverter_final_data->siteId;
                        $input['l1GridCurrent'] = $plant_inverter_final_data->l1GridCurrent;
                        $input['l2GridCurrent'] = $plant_inverter_final_data->l2GridCurrent;
                        $input['l3GridCurrent'] = $plant_inverter_final_data->l3GridCurrent;
                        $input['l1GridApparentPower'] = $plant_inverter_final_data->l1GridApparentPower;
                        $input['l2GridApparentPower'] = $plant_inverter_final_data->l2GridApparentPower;
                        $input['l3GridApparentPower'] = $plant_inverter_final_data->l3GridApparentPower;
                        $input['l1GridPowerFactor'] = $plant_inverter_final_data->l1GridPowerFactor;
                        $input['l2GridPowerFactor'] = $plant_inverter_final_data->l2GridPowerFactor;
                        $input['l3GridPowerFactor'] = $plant_inverter_final_data->l3GridPowerFactor;
                        $input['gridFrequency'] = $plant_inverter_final_data->gridFrequency;
                        $input['totalGridApparentPower'] = $plant_inverter_final_data->totalGridApparentPower;
                        $input['meterUptime'] = $plant_inverter_final_data->meterUptime;
                        $input['gecUptime'] = $plant_inverter_final_data->gecUptime;
                        $input['installedMeterType'] = $plant_inverter_final_data->installedMeterType;
                        $input['installedInverterType'] = $plant_inverter_final_data->installedInverterType;
                        $input['meterCommFail'] = $plant_inverter_final_data->meterCommFail;
                        $input['exportLimitEnabled'] = $plant_inverter_final_data->exportLimitEnabled;
                        $input['l1Voltage'] = $plant_inverter_final_data->l1Voltage;
                        $input['l2Voltage'] = $plant_inverter_final_data->l2Voltage;
                        $input['l3Voltage'] = $plant_inverter_final_data->l3Voltage;
                        $input['l1GridPower'] = $plant_inverter_final_data->l1GridPower;
                        $input['l2GridPower'] = $plant_inverter_final_data->l2GridPower;
                        $input['l3GridPower'] = $plant_inverter_final_data->l3GridPower;
                        $input['totalGridPower'] = $plant_inverter_final_data->totalGridPower;
                        $input['l1LoadPower'] = $plant_inverter_final_data->l1LoadPower;
                        $input['l2LoadPower'] = $plant_inverter_final_data->l2LoadPower;
                        $input['l3LoadPower'] = $plant_inverter_final_data->l3LoadPower;
                        $input['totalLoadPower'] = $plant_inverter_final_data->totalLoadPower;
                        $input['importEnergy'] = $plant_inverter_final_data->importEnergy;
                        $input['exportEnergy'] = $plant_inverter_final_data->exportEnergy;
                        $input['consumedEnergy'] = $plant_inverter_final_data->consumedEnergy;
                        $input['solarEnergy'] = $plant_inverter_final_data->solarEnergy;
                        $input['l1InverterPower'] = $plant_inverter_final_data->l1InverterPower;
                        $input['l2InverterPower'] = $plant_inverter_final_data->l2InverterPower;
                        $input['l3InverterPower'] = $plant_inverter_final_data->l3InverterPower;
                        // $input['totalInverterPower'] = $plant_inverter_final_data->totalInverterPower;
                        $input['numberOfInverters'] = $plant_inverter_final_data->numberOfInverters;
                        $input['lastUpdated'] = $plant_inverter_final_data->lastUpdated;
                        $input['created_at'] = Date('Y-m-d H:i:s');
                        $input['updated_at'] = Date('Y-m-d H:i:s');
                        // dd($input);

                        $plant_detail_exist = PlantDetail::where('siteId',$plant['siteId'])->first();
                        if($plant_detail_exist){
                            $plant_detail_insertion_responce =  $plant_detail_exist->fill($input)->save();
                        }else{
                            $plant_detail_insertion_responce = PlantDetail::create($input);
                        }

                        if($plant_detail_insertion_responce){
                            //echo '1 Plant Detail';
                        }else{
                            // echo '0 Sorry! Plant and inverter data are retrived But plant detail insertion Failed.';
                        }

                        // dd($plant_inverter_final_data->totalInverterPower);
                        $generation_log['plant_id'] = $plant['id'];
                        $generation_log['siteId'] = $plant_inverter_final_data->siteId;
                        $generation_log['current_generation'] = $plant_inverter_final_data->totalInverterPower ? $plant_inverter_final_data->totalInverterPower : 0 ;
                        $generation_log['current_consumption'] = $plant_inverter_final_data->totalLoadPower ? $plant_inverter_final_data->totalLoadPower : 0 ;
                        $generation_log['current_grid'] = $plant_inverter_final_data->totalGridPower ? $plant_inverter_final_data->totalGridPower : 0 ;
                        $generation_log['lastUpdated'] = $plant_inverter_final_data->lastUpdated;
                        $generation_log['created_at'] = Date('Y-m-d H:i:s',strtotime($plant_inverter_final_data->lastUpdated));
                        $generation_log['updated_at'] = Date('Y-m-d H:i:s');
                        $generation_log['totalEnergy'] = 0;
                        if(isset($plant_inverter_final_data->dv_Inv1SerialNumber)){
                            for ($i=1; $i <= $plant_inverter_final_data->numberOfInverters ; $i++) {
                                $inverter_data = (array)$plant_inverter_final_data;
                                if(date('Y-m-d H:i:s',strtotime($inverter_data['lastUpdated'])) == date('Y-m-d 00:00:00')){
                                    $generation_log['totalEnergy'] = 0;
                                }else {
                                    if($inverter_data['dv_Inv'.$i.'DailyEnergy'] != null && $inverter_data['dv_Inv'.$i.'DailyEnergy'] != 0 && $inverter_data['dv_Inv'.$i.'DailyEnergy'] != '0.0'){
                                        $generation_log['totalEnergy'] = $inverter_data['dv_Inv'.$i.'DailyEnergy'] + $generation_log['totalEnergy'] ;
                                    }else{
                                        $existing_totalEnergy = GenerationLog::where('plant_id',$plant['id'])->whereBetween('created_at', [date('Y-m-d 00:00:01'),date('Y-m-d 23:59:00')])->orderBy('created_at','desc')->first();
                                        // dd($existing_totalEnergy);
                                        $generation_log['totalEnergy'] = $existing_totalEnergy != null ? $generation_log['totalEnergy'] + $existing_totalEnergy->totalEnergy : '0.001';
                                    }
                                }
                            }
                        }else{
                            $existing_totalEnergy = GenerationLog::where('plant_id',$plant['id'])->whereBetween('created_at', [date('Y-m-d 00:00:01'),date('Y-m-d 23:59:00')])->orderBy('created_at','desc')->first();

                            $generation_log['totalEnergy'] = $existing_totalEnergy != null ? $existing_totalEnergy->totalEnergy : '0.002';
                        }
                        $generation_log_exist = GenerationLog::where('lastUpdated',$plant_inverter_final_data->lastUpdated)->where('plant_id',$plant['id'])->get();
                        if(count($generation_log_exist) <= 0){
                            $generation_log_responce = GenerationLog::create($generation_log);
                        }
                        // echo 'ssdsd';exit;

                        if(isset($plant_inverter_final_data->dv_Inv1SerialNumber)){
                            for ($i=1; $i <= $plant_inverter_final_data->numberOfInverters ; $i++) {
                                $inverters_data = (array)$plant_inverter_final_data;
                                $inverter_input['plant_id'] = $plant['id'];
                                $inverter_input['siteId'] = $inverters_data['siteId'];
                                $inverter_input['serial_no'] = $inverters_data['dv_Inv'.$i.'SerialNumber'];
                                $inverter_input['ac_output_power'] = $inverters_data['inverter'.$i.'Power'];
                                $inverter_input['total_generation'] = $inverters_data['dv_Inv'.$i.'TotalEnergy'];

                                $inverter_input['l_voltage1'] = $inverters_data['dv_Inv'.$i.'Mppt1Voltage'];
                                $inverter_input['l_current1'] = $inverters_data['dv_Inv'.$i.'Mppt1Current'];

                                $inverter_input['l_voltage2'] = $inverters_data['dv_Inv'.$i.'Mppt2Voltage'];
                                $inverter_input['l_current2'] = $inverters_data['dv_Inv'.$i.'Mppt2Current'];

                                $inverter_input['l_voltage3'] = $inverters_data['dv_Inv'.$i.'Mppt3Voltage'];
                                $inverter_input['l_current3'] = $inverters_data['dv_Inv'.$i.'Mppt3Current'];

                                $inverter_input['r_voltage1'] = $inverters_data['dv_Inv'.$i.'L1Voltage'];
                                $inverter_input['r_current1'] = $inverters_data['dv_Inv'.$i.'L1Current'];

                                $inverter_input['r_voltage2'] = $inverters_data['dv_Inv'.$i.'L2Voltage'];
                                $inverter_input['r_current2'] = $inverters_data['dv_Inv'.$i.'L2Current'];

                                $inverter_input['r_voltage3'] = $inverters_data['dv_Inv'.$i.'L3Voltage'];
                                $inverter_input['r_current3'] = $inverters_data['dv_Inv'.$i.'L3Current'];

                                $inverter_input['frequency'] = $inverters_data['dv_Inv'.$i.'Frequency'];
                                $inverter_input['dc_power'] = $inverters_data['dv_Inv'.$i.'TotalDcPower'];
                                $inverter_input['lastUpdated'] = $inverters_data['lastUpdated'];
                                $inverter_input['created_at'] = Date('Y-m-d H:i:s');
                                $inverter_input['updated_at'] = Date('Y-m-d H:i:s');
                                // echo '<pre>'; print_r($inverter_input);exit;

                                $inverter_exist = Inverter::where('siteId',$inverters_data['siteId'])->first();
                                if($inverter_exist){
                                    $inverter_insertion_responce =  $inverter_exist->fill($inverter_input)->save();
                                }else{
                                    $inverter_insertion_responce = Inverter::create($inverter_input);
                                }


                                $daily_input['plant_id'] = $plant['id'];
                                $daily_input['siteId'] = $inverters_data['siteId'];
                                $daily_input['serial_no'] = $inverters_data['dv_Inv'.$i.'SerialNumber'];
                                $daily_input['daily_generation'] = $inverters_data['dv_Inv'.$i.'DailyEnergy'];
                                $daily_input['lastUpdated'] = $inverters_data['lastUpdated'];
                                $daily_input['created_at'] = Date('Y-m-d H:i:s',strtotime($inverters_data['lastUpdated']));
                                $daily_input['updated_at'] = Date('Y-m-d H:i:s');

                                // $inverter_input['monthly_generation'] = $inverters_data['dv_Inv'.$i.'MonthlyEnergy'];
                                // $inverter_input['annual_generation'] = 0;

                                $daily_inverter_detail_exist = DailyInverterDetail::where('plant_id',$plant['id'])->whereDay('created_at', '=', date('d'))->first();
                                // dd($processed_plant_detail_exist);
                                if($daily_inverter_detail_exist != null){
                                    $daily_inverter_detail_insertion_responce =  $daily_inverter_detail_exist->fill($daily_input)->save();
                                }else{
                                    $daily_inverter_detail_insertion_responce = DailyInverterDetail::create($daily_input);
                                }


                                $monthly_inverter_detail_exist = MonthlyInverterDetail::where('plant_id',$plant['id'])->whereMonth('created_at', '=', date('m'))->first();

                                $monthly_input['plant_id'] = $plant['id'];
                                $monthly_input['siteId'] = $inverters_data['siteId'];
                                $monthly_input['serial_no'] = $inverters_data['dv_Inv'.$i.'SerialNumber'];
                                $monthly_input['lastUpdated'] = $inverters_data['lastUpdated'];
                                $monthly_input['created_at'] = Date('Y-m-d H:i:s',strtotime($inverters_data['lastUpdated']));
                                $monthly_input['updated_at'] = Date('Y-m-d H:i:s');
                                $monthly_input['monthly_generation'] = isset($inverters_data['dv_Inv'.$i.'MonthlyEnergy']) && !empty($inverters_data['dv_Inv'.$i.'MonthlyEnergy']) && $inverters_data['dv_Inv'.$i.'MonthlyEnergy'] != null ? $inverters_data['dv_Inv'.$i.'MonthlyEnergy'] : $monthly_inverter_detail_exist['monthly_generation'];

                                // dd($monthly_input);


                                // dd($processed_plant_detail_exist);
                                if($monthly_inverter_detail_exist != null){
                                    $monthly_inverter_detail_insertion_responce =  $monthly_inverter_detail_exist->fill($monthly_input)->save();
                                }else{
                                    $monthly_inverter_detail_insertion_responce = MonthlyInverterDetail::create($monthly_input);
                                }

                            }
                        }

                        $inverter['plant_id'] = $plant['id'];
                        $inverter['siteId'] = $plant_inverter_final_data->siteId;
                        $inverter['inverter1Power'] = $plant_inverter_final_data->inverter1Power;
                        $inverter['inverter2Power'] = $plant_inverter_final_data->inverter2Power;
                        $inverter['inverter3Power'] = $plant_inverter_final_data->inverter3Power;
                        $inverter['inverter4Power'] = $plant_inverter_final_data->inverter4Power;
                        $inverter['inverter5Power'] = $plant_inverter_final_data->inverter5Power;
                        $inverter['inverter1Energy'] = $plant_inverter_final_data->inverter1Energy;
                        $inverter['inverter2Energy'] = $plant_inverter_final_data->inverter2Energy;
                        $inverter['inverter3Energy'] = $plant_inverter_final_data->inverter3Energy;
                        $inverter['inverter4Energy'] = $plant_inverter_final_data->inverter4Energy;
                        $inverter['inverter5Energy'] = $plant_inverter_final_data->inverter5Energy;
                        $inverter['inverter1LimitValue'] = $plant_inverter_final_data->inverter1LimitValue;
                        $inverter['inverter2LimitValue'] = $plant_inverter_final_data->inverter2LimitValue;
                        $inverter['inverter3LimitValue'] = $plant_inverter_final_data->inverter3LimitValue;
                        $inverter['inverter4LimitValue'] = $plant_inverter_final_data->inverter4LimitValue;
                        $inverter['inverter5LimitValue'] = $plant_inverter_final_data->inverter5LimitValue;
                        $inverter['inverterCommFail'] = $plant_inverter_final_data->inverterCommFail;
                        $inverter['inverterConfigFail'] = $plant_inverter_final_data->inverterConfigFail;
                        $inverter['inverter1Uptime'] = $plant_inverter_final_data->inverter1Uptime;
                        $inverter['inverter2Uptime'] = $plant_inverter_final_data->inverter2Uptime;
                        $inverter['inverter3Uptime'] = $plant_inverter_final_data->inverter3Uptime;
                        $inverter['inverter4Uptime'] = $plant_inverter_final_data->inverter4Uptime;
                        $inverter['inverter5Uptime'] = $plant_inverter_final_data->inverter5Uptime;
                        $inverter['numberOfInverters'] = $plant_inverter_final_data->numberOfInverters;
                        $inverter['lastUpdated'] = $plant_inverter_final_data->lastUpdated;
                        $inverter['created_at'] = Date('Y-m-d H:i:s');
                        $inverter['updated_at'] = Date('Y-m-d H:i:s');

                        $inverter_detail_insertion_responce = InverterDetail::create($inverter);
                        // echo '<pre>';print_r($inverter_detail_insertion_responce);exit;
                        if($inverter_detail_insertion_responce){
                            //echo '1 Plant Detail';
                        }else{
                            // echo '0 Sorry! Inverter data are retrived But plant detail insertion Failed.';
                        }


                        if(isset($plant_inverter_final_data->dv_Inv1SerialNumber)){
                            for ($i=1; $i <= $plant_inverter_final_data->numberOfInverters ; $i++) {
                                $inverters_data = (array)$plant_inverter_final_data;
                                $user_ids = PlantUser::where('plant_id',$plant['id'])->get();
                                // dd($user_ids);
                                if($inverters_data['dv_Inv'.$i.'OutputType'] > 0){
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'OutputType'])->where('api_param','dv_InvOutputType')->first();
                                    $this->faults_data_insertion($plant['id'],$inverters_data,$fault_data,'dv_Inv'.$i.'OutputType',$user_ids);
                                }else{
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'OutputType'])->where('api_param','dv_InvOutputType')->first();
                                    $this->faults_data_updation($plant['id'],$fault_data);
                                }
                                if($inverters_data['dv_Inv'.$i.'FaultCode'] > 0){
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'FaultCode'])->where('api_param','dv_InvFaultCode')->first();
                                    $this->faults_data_insertion($plant['id'],$inverters_data,$fault_data,'dv_Inv'.$i.'FaultCode',$user_ids);
                                }else{
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'FaultCode'])->where('api_param','dv_InvFaultCode')->first();
                                    $this->faults_data_updation($plant['id'],$fault_data);
                                }

                                if($inverters_data['dv_Inv'.$i.'PIDAlarmCode'] > 0){
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'PIDAlarmCode'])->where('api_param','dv_InvPIDAlarmCode')->first();
                                    $this->faults_data_insertion($plant['id'],$inverters_data,$fault_data,'dv_Inv'.$i.'PIDAlarmCode',$user_ids);
                                }else{
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'PIDAlarmCode'])->where('api_param','dv_InvPIDAlarmCode')->first();
                                    $this->faults_data_updation($plant['id'],$fault_data);
                                }

                                if($inverters_data['dv_Inv'.$i.'PIDWorkState'] > 0){
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'PIDWorkState'])->where('api_param','dv_InvPIDWorkState')->first();
                                    $this->faults_data_insertion($plant['id'],$inverters_data,$fault_data,'dv_Inv'.$i.'PIDWorkState',$user_ids);
                                }else{
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'PIDWorkState'])->where('api_param','dv_InvPIDWorkState')->first();
                                    $this->faults_data_updation($plant['id'],$fault_data);
                                }

                                if($inverters_data['dv_Inv'.$i.'WorkState1'] > 0){
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'WorkState1'])->where('api_param','dv_InvWorkState1')->first();
                                    $this->faults_data_insertion($plant['id'],$inverters_data,$fault_data,'dv_Inv'.$i.'WorkState1',$user_ids);
                                }else{
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['dv_Inv'.$i.'WorkState1'])->where('api_param','dv_InvWorkState1')->first();
                                    $this->faults_data_updation($plant['id'],$fault_data);
                                }

                                if($inverters_data['inverter'.$i.'CommFail'] > 0){
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['inverter'.$i.'CommFail'])->where('api_param','inverterCommFail')->first();
                                    $this->faults_data_insertion($plant['id'],$inverters_data,$fault_data,'inverter'.$i.'CommFail',$user_ids);
                                }else{
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['inverter'.$i.'CommFail'])->where('api_param','inverterCommFail')->first();
                                    $this->faults_data_updation($plant['id'],$fault_data);
                                }

                                if($inverters_data['inverterConfigFail'] > 0){
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['inverterConfigFail'])->where('api_param','inverterConfigFail')->first();
                                    $this->faults_data_insertion($plant['id'],$inverters_data,$fault_data,'inverterConfigFail',$user_ids);
                                }else{
                                    $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['inverterConfigFail'])->where('api_param','inverterConfigFail')->first();
                                    $this->faults_data_updation($plant['id'],$fault_data);
                                }

                                if($plant['system_type'] != 'All on Grid'){
                                    if($inverters_data['exportLimitEnabled'] > 0){
                                        $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['exportLimitEnabled'])->where('api_param','exportLimitEnabled')->first();
                                        $this->faults_data_insertion($plant['id'],$inverters_data,$fault_data,'exportLimitEnabled',$user_ids);
                                    }else{
                                        $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['exportLimitEnabled'])->where('api_param','exportLimitEnabled')->first();
                                        $this->faults_data_updation($plant['id'],$fault_data);
                                    }

                                    if($inverters_data['meterCommFail'] > 0){
                                        $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['meterCommFail'])->where('api_param','meterCommFail')->first();
                                        $this->faults_data_insertion($plant['id'],$inverters_data,$fault_data,'meterCommFail',$user_ids);
                                    }else{
                                        $fault_data = FaultAndAlarm::where('alarm_code',$inverters_data['meterCommFail'])->where('api_param','meterCommFail')->first();
                                        $this->faults_data_updation($plant['id'],$fault_data);
                                    }
                                }
                            }
                        }

                        //Proceess Data getting from Saltec Server
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://67.23.248.117:8089/api/site/processed/".$plant_inverter_final_data->siteId.'?timestamp='.$plant_inverter_final_data->lastUpdated,
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
                        $response2 = curl_exec($curl);
                        $err = curl_error($curl);
                        curl_close($curl);

                        if ($err) {
                            echo "cURL Error #:" . $err;
                        }
                        $processed_data = json_decode($response2);
                        $final_processed_data = $processed_data->data;
                        // dd($processed_data);
                        if($final_processed_data){

                            $processed['plant_id'] = $plant['id'];
                            $processed['siteId'] = $final_processed_data->siteId;
                            $processed['dailyGeneration'] = $final_processed_data->dailySolarEnergy;
                            $processed['dailyConsumption'] = $final_processed_data->dailyLoadEnergy;
                            $processed['dailyGridPower'] = $final_processed_data->dailyMaxGridPower;
                            $processed['dailyBoughtEnergy'] = $final_processed_data->dailyImportEnergy;
                            $processed['dailySellEnergy'] = $final_processed_data->dailyExportEnergy;
                            $processed['dailyMaxSolarPower'] = $final_processed_data->dailyMaxSolarPower;
                            $processed['lastUpdated'] = $final_processed_data->timestamp;
                            $processed['created_at'] = Date('Y-m-d H:i:s');
                            $processed['updated_at'] = Date('Y-m-d H:i:s');

                            $processed_plant_detail_exist = DailyProcessedPlantDetail::where('plant_id',$plant['id'])->whereDay('created_at', '=', date('d'))->first();
                            // dd($processed_plant_detail_exist);
                            if($processed_plant_detail_exist != null){
                                $processed_plant_detail_insertion_responce =  $processed_plant_detail_exist->fill($processed)->save();
                            }else{
                                $processed_plant_detail_insertion_responce = DailyProcessedPlantDetail::create($processed);
                            }


                            $monthlyGeneration['totalMonthlyEnergy'] = 0;
                            if(isset($plant_inverter_final_data->dv_Inv1SerialNumber)){
                                for ($i=1; $i <= $plant_inverter_final_data->numberOfInverters ; $i++) {
                                    $inverter_data = (array)$plant_inverter_final_data;
                                    if($inverter_data['dv_Inv'.$i.'MonthlyEnergy'])
                                    {
                                        $monthlyGeneration['totalMonthlyEnergy'] = $inverter_data['dv_Inv'.$i.'MonthlyEnergy'] + $monthlyGeneration['totalMonthlyEnergy'];
                                    }
                                }
                            }

                            $processed['plant_id'] = $plant['id'];
                            $processed['siteId'] = $final_processed_data->siteId;
                            // $processed['monthlyGeneration'] = $final_processed_data->monthlySolarEnergy;
                            $processed['monthlyGeneration'] = $monthlyGeneration['totalMonthlyEnergy'];
                            $processed['monthlyConsumption'] = $final_processed_data->monthlyLoadEnergy;
                            $processed['monthlyGridPower'] = $final_processed_data->monthlyMaxGridPower;
                            $processed['monthlyBoughtEnergy'] = $final_processed_data->monthlyImportEnergy;
                            $processed['monthlySellEnergy'] = $final_processed_data->monthlyExportEnergy;
                            $processed['monthlyMaxSolarPower'] = $final_processed_data->monthlyMaxSolarPower;
                            $processed['lastUpdated'] = $final_processed_data->timestamp;
                            $processed['created_at'] = Date('Y-m-d H:i:s');
                            $processed['updated_at'] = Date('Y-m-d H:i:s');

                            $processed_plant_detail_exist = MonthlyProcessedPlantDetail::where('plant_id',$plant['id'])->whereMonth('created_at', '=', date('m'))->first();
                            // dd($processed_plant_detail_exist);
                            if($processed_plant_detail_exist != null){
                                if($monthlyGeneration['totalMonthlyEnergy'] <= 0){
                                    $processed['monthlyGeneration'] = $processed_plant_detail_exist->monthlyGeneration;
                                }

                                $processed_plant_detail_insertion_responce =  $processed_plant_detail_exist->fill($processed)->save();
                            }else{

                                $processed_plant_detail_insertion_responce = MonthlyProcessedPlantDetail::create($processed);
                            }


                            $processed['plant_id'] = $plant['id'];
                            $processed['siteId'] = $final_processed_data->siteId;
                            $processed['yearlyGeneration'] = isset($final_processed_data->yearlySolarEnergy) ? $final_processed_data->yearlySolarEnergy: 0;
                            $processed['yearlyConsumption'] = isset($final_processed_data->yearlyLoadEnergy) ? $final_processed_data->yearlyLoadEnergy : 0;
                            $processed['yearlyGridPower'] = isset($final_processed_data->yearlyMaxGridPower) ? $final_processed_data->yearlyMaxGridPower : 0;
                            $processed['yearlyBoughtEnergy'] = isset($final_processed_data->yearlyImportEnergy) ? $final_processed_data->yearlyImportEnergy : 0;
                            $processed['yearlySellEnergy'] = isset($final_processed_data->yearlyExportEnergy) ? $final_processed_data->yearlyExportEnergy : 0;
                            $processed['yearlyMaxSolarPower'] = isset($final_processed_data->yearlyMaxSolarPower) ? $final_processed_data->yearlyMaxSolarPower : 0;
                            $processed['lastUpdated'] = $final_processed_data->timestamp;
                            $processed['created_at'] = Date('Y-m-d H:i:s');
                            $processed['updated_at'] = Date('Y-m-d H:i:s');

                            $processed_plant_detail_exist = YearlyProcessedPlantDetail::where('plant_id',$plant['id'])->whereYear('created_at', '=', date('Y'))->first();
                            // dd($processed_plant_detail_exist);
                            if($processed_plant_detail_exist != null){
                                $processed_plant_detail_insertion_responce =  $processed_plant_detail_exist->fill($processed)->save();
                            }else{
                                $processed_plant_detail_insertion_responce = YearlyProcessedPlantDetail::create($processed);
                            }

                        }

                        $this->plant_offline_alert($plant_inverter_final_data->lastUpdated);
                        app('App\Http\Controllers\Api\NotificationController')->push_notification();
                    }
                }
            }
        }

    }
}
