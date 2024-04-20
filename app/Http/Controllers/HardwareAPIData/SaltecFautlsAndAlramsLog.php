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


class SaltecFautlsAndAlramsLog extends Controller
{

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
