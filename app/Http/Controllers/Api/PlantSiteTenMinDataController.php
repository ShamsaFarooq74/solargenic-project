<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Models\Plant;
use App\Http\Models\PlantDetail;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\Inverter;
use App\Http\Models\PlantSiteTenMinuteData;
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

//use Elementor\Settings;
//use Faker\Provider\DateTime;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class PlantSiteTenMinDataController extends Controller
{
    public function plantSiteTenMinutesData()
    {
        $cronJobTime = Setting::where('perimeter', 'cron_job_time')->first()['value'];
//        $dataArray = [];
//        for ($i = 0; $i < $cronJobTime; $i++) {
//            array_push($dataArray, $i);
//        }
//        $currentDate = Date('Y-m');
//        $startRangeData1 = Date('Y-m');
////        $startRangeData1 = $startRangeData1 . '-' . '01';
//        $currentDateTime = $currentDate.'-00';
//        $currentTimeDate = date('Y-m-d', strtotime($currentDateTime . ' + ' . $cronJobTime . 'days'));
//        $rangeDbData = PlantSiteTenMinuteData::where(['start_date_range' => $startRangeData1,'cron_job_date' => $currentTimeDate])->first();
//        if($rangeDbData)
//        {
//            $startRangeData1 = date('Y-m-d', strtotime($rangeDbData->cron_job_date . ' + 1days'));;
//            $currentTimeDate = date('Y-m-d', strtotime($rangeDbData->cron_job_date . ' + ' . $cronJobTime . 'days'));
//        }
//        else
//        {
//            $startRangeData1 = $startRangeData1 . '-' . '01';
//            $currentDateTime = $currentDate.'-00';
//            $currentTimeDate = date('Y-m-d', strtotime($currentDateTime . ' + ' . $cronJobTime . 'days'));
//        }
//return ['start_date_range' => $startRangeData1,'cron_job_date' => $currentTimeDate];
//
//        $maxDays=date('t');
//        $plantSiteDateData = PlantSiteTenMinuteData::where(['start_date_range' => $startRangeData1,'cron_job_date' => $currentTimeDate])->latest()->first();
//        if ($plantSiteDateData) {
//            $startRangeData = date('Y-m-d', strtotime($plantSiteDateData->cron_job_date . ' + 1days'));;
//            $endingRange = date('Y-m-d', strtotime($plantSiteDateData->cron_job_date . ' + ' . $cronJobTime . 'days'));
////            return [$startRangeData,$endingRange];
//            if(!PlantSiteTenMinuteData::where(['start_date_range' => $startRangeData,'cron_job_date' => $endingRange])->exists()) {
//                $plantSiteData = new PlantSiteTenMinuteData();
//                $plantSiteData->cron_job_date = $endingRange;
//                $plantSiteData->start_date_range = $startRangeData;
//                $plantSiteData->save();
//            }
//        } else {
//            $startRangeDat = Date('Y-m');
//            $startRangeData = $startRangeDat . '-' . '01';
//            $endingRange = date('Y-m-d', strtotime($currentDateTime . ' + ' . $cronJobTime . 'days'));
//            $plantSiteData = new PlantSiteTenMinuteData();
//            $plantSiteData->cron_job_date = $endingRange;
//            $plantSiteData->start_date_range = $startRangeData;
//            $plantSiteData->save();
//        }
////        $countDataOfSpecificMonth = PlantSiteTenMinuteData::where(['start_date_range' => $startRangeData1,'cron_job_date' => $currentTimeDate])->latest()->first();
//        return [$startRangeData,$endingRange];
////        return $currentDate;
//        $period = CarbonPeriod::create("2021-07-01", $currentTimeDate);
//        foreach ($period as $date) {
//            $listOfDates[] = $date->format('Y-m-d');
//        }
//        return $listOfDates;
//
//// Now You Can Review This Array
//        dd($listOfDates);
//        $maxDays = date('t');
//        return gettype((int)$maxDays);
        $early_sunrise = Weather::whereDate('created_at', Date('Y-m-d'))->orderBy('sunrise', 'ASC')->first();
        $sunrise = $early_sunrise && $early_sunrise->sunrise ? explode(':', $early_sunrise->sunrise) : explode(':', '06:00:AM');
        $sunrise_hour = $sunrise[0];
        $sunrise_min = $sunrise[1];

        $micro_itr = 0;
        date_default_timezone_set('Asia/Karachi');
        $time_difference_saltec = Date('Y-m-d H:i:s');
        $time_difference_micro = Date('Y-m-d H:i:s');
        $generation_log_created_time = Date('Y-m-d H:i:s');

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
        if ($res) {
            $token = $res->data;
        }

        if (isset($token) && $token) {

//            $plants = Plant::inRandomOrder()->get();
//            return $plants;
            $plants = Plant::where('id', 119)->get();
            $generation_log_cron_job_id = GenerationLog::max('cron_job_id');
            $processed_plant_cron_job_id = ProcessedPlantDetail::max('cron_job_id');
//            return $plants;

            $generation_log_cron_job_id = GenerationLog::max('cron_job_id');
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
                    $plantDate = date('Ymd', strtotime($plant->created_at));

                    $plant_sites = PlantSite::where('plant_id', $plant->id)->get();
                    $plantTenDaysData = PlantSiteTenMinuteData::where('plant_id', $plant->id)->latest()->first();
                    if ($plantTenDaysData) {
                        $cronJobDate = $plantTenDaysData->end_cron_job_date;
                        $startTime = $plantTenDaysData->end_cron_job_date;
                    } elseif ($plant->created_at != null) {
                        $cronJobDate = $plant->created_at;
                        $startTime = $plant->created_at;
//                            $current_time = date('Ymd', strtotime($plants->created_at)) . 'T' . date('His', strtotime($plants->created_at));
                    } else {
                        $cronJobDate = date('Y-m-d');
                        $startTime = date('Y-m-d');
                    }

                    for ($j = 0; $j < $cronJobTime; $j++) {

                        $endingDateRange = '';
                        $startDateRange = '';
                        $monthlyEnergy = 0;
                        $createdAtDate = date('Y-m-d', strtotime($cronJobDate));
                        $current_time = date('Ymd', strtotime($cronJobDate)) . 'T' . date('His', strtotime($cronJobDate));
                        $startDateRange = $startTime;
                        if ($createdAtDate <= date('Y-m-d')) {
                            foreach ($plant_sites as $key1 => $site) {

//                                return $createdAtDate;
                                $curl = curl_init();

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => "https://67.23.248.117:8089/api/site/processed/" . $site->site_id . '?timestamp=' . $current_time,
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
                                    $processed_plant_detail['created_at'] = Date('Y-m-d H:i:s', strtotime($createdAtDate));

                                    $processed_plant_detail_insertion_responce = ProcessedPlantDetail::create($processed_plant_detail);
                                    $monthlyEnergy = isset($final_processed_data->monthlySolarEnergy) && $final_processed_data->monthlySolarEnergy ? $final_processed_data->monthlySolarEnergy : 0;
                                    $yearlyEnergy = isset($final_processed_data->yearlySolarEnergy) && $final_processed_data->yearlySolarEnergy ? $final_processed_data->yearlySolarEnergy : 0;
                                    $plantInvertersData = Inverter::where(['plant_id' => $plant->id, 'siteId' => $site->site_id])->get();
                                    for ($i = 0; $i < count($plantInvertersData); $i++) {
                                        $inverters_data = date('Ymd', strtotime($createdAtDate)) . 'T' . date('His', strtotime($createdAtDate));
                                        $inverter_input['dv_inverter'] = $plantInvertersData[$i]['dv_inverter'];
                                        $daily_input['plant_id'] = $plant->id;
                                        $daily_input['siteId'] = $site->site_id;
                                        $daily_input['lastUpdated'] = $inverters_data;
                                        $daily_input['created_at'] = Date('Y-m-d H:i:s', strtotime($createdAtDate));
                                        $daily_input['updated_at'] = Date('Y-m-d H:i:s');
                                        $daily_input['dv_inverter'] = $plantInvertersData[$i]['dv_inverter'];
                                        $daily_input['daily_generation'] = isset($final_processed_data->dailySolarEnergy) && $final_processed_data->dailySolarEnergy ? $final_processed_data->dailySolarEnergy : 0;
                                        $daily_inverter_detail_exist = DailyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', $inverter_input['dv_inverter'])->whereDate('created_at', '=', $createdAtDate)->first();
                                        if ($daily_inverter_detail_exist != null) {
                                            $daily_inverter_detail_insertion_response = $daily_inverter_detail_exist->fill($daily_input)->save();

                                        } else {
                                            $daily_inverter_detail_insertion_response = DailyInverterDetail::create($daily_input);
                                        }


                                        $month = date('Y-m', strtotime($cronJobDate));
                                        $monthDay = intval(date('t', strtotime($cronJobDate)));
                                        $lastDayOfMonth = $month . '-' . $monthDay;
                                        $lastDayRange = date('Y-m-d', strtotime($cronJobDate));
                                        if ($lastDayRange == $lastDayOfMonth) {
                                            $monthly_input['plant_id'] = $plant->id;
                                            $monthly_input['siteId'] = $site->site_id;
                                            $monthly_input['dv_inverter'] = $plantInvertersData[$i]['dv_inverter'];
                                            $monthly_input['lastUpdated'] = $inverters_data;
                                            $monthly_input['created_at'] = Date('Y-m-d H:i:s', strtotime($lastDayRange));
                                            $monthly_input['updated_at'] = Date('Y-m-d H:i:s');
                                            $monthly_input['monthly_generation'] = $monthlyEnergy;
                                            $monthly_inverter_detail_exist = MonthlyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', $plantInvertersData[$i]['dv_inverter'])->whereDate('created_at', '=', $lastDayOfMonth)->first();
                                            if ($monthly_inverter_detail_exist) {
                                                $monthly_inverter_detail_insertion_responce = $monthly_inverter_detail_exist->fill($monthly_input)->save();
                                            } else {
                                                $monthly_inverter_detail_insertion_responce = MonthlyInverterDetail::create($monthly_input);
                                            }

                                        }
                                        $yearLastDate = date('Y', strtotime($cronJobDate));
                                        $yearLastDate = $yearLastDate . '-12-31';
                                        if ($lastDayRange == $yearLastDate) {
                                            $yearly_input['plant_id'] = $plant->id;
                                            $yearly_input['siteId'] = $site->site_id;
                                            $yearly_input['dv_inverter'] = $plantInvertersData[$i]['dv_inverter'];
                                            $yearly_input['lastUpdated'] = $inverters_data;
                                            $yearly_input['created_at'] = Date('Y-m-d H:i:s', strtotime($lastDayRange));
                                            $yearly_input['updated_at'] = Date('Y-m-d H:i:s');
                                            $yearly_input['yearly_generation'] = $yearlyEnergy;
                                            $yearly_inverter_detail_exist = YearlyInverterDetail::where('plant_id', $plant->id)->where('siteId', $site->site_id)->where('dv_inverter', $plantInvertersData[$i]['dv_inverter'])->whereDate('created_at', '=', $lastDayOfMonth)->first();
                                            if ($yearly_inverter_detail_exist) {
                                                $yearly_inverter_detail_exist = $yearly_inverter_detail_exist->fill($yearly_input)->save();
                                            } else {
                                                $yearly_inverter_detail_exist = YearlyInverterDetail::create($yearly_input);
                                            }
                                        }
                                    }


                                }


                            }
                            $dailyGenerationVar = DailyInverterDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d', strtotime($createdAtDate)))->sum('daily_generation');
//                            return $dailyGenerationVar;
                            $monthlyGenerationVar = MonthlyInverterDetail::where('plant_id', $plant->id)->whereMonth('created_at', date('m', strtotime($createdAtDate)))->sum('monthly_generation');
                            $dailyBoughtEnergy =
                                ProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d', strtotime($createdAtDate)))->sum('dailyBoughtEnergy');
                            $dailyConsumptionn =
                                ProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d', strtotime($createdAtDate)))->sum('dailyConsumption');

                            $dailySellEnergy =
                                ProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d', strtotime($createdAtDate)))->sum('dailySellEnergy');
                            $monthlyBoughtEnergy =
                                ProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d', strtotime($createdAtDate)))->sum('monthlyBoughtEnergy');
                            $monthlyConsumptionn =
                                ProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d', strtotime($createdAtDate)))->sum('monthlyConsumption');
                            $monthlySellEnergy =
                                ProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d', strtotime($createdAtDate)))->sum('monthlySellEnergy');
                            $yearlyBoughtEnergy =
                                ProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d', strtotime($createdAtDate)))->sum('yearlyBoughtEnergy');
                            $yearlyConsumptionn =
                                ProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d', strtotime($createdAtDate)))->sum('yearlyConsumption');
                            $yearlySellEnergy =
                                ProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d', strtotime($createdAtDate)))->sum('yearlySellEnergy');
return $plant->meter_type;
                            if ($plant->system_type == 1) {

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
                                }
//                        $endingDateRange = $cronJobDate;
//                        print_r($endingDateRange);

                            }
                            $daily_processed['plant_id'] = $plant->id;
                            $daily_processed['dailyGeneration'] = $dailyGenerationVar;
                            $daily_processed['dailyConsumption'] = $dailyConsumptionVar;
                            return $dailyConsumptionVar;
                            $daily_processed['dailyGridPower'] = $dailyGridVar;
                            $daily_processed['dailyBoughtEnergy'] = $dailyBoughtEnergyVar >= 0 ? $dailyBoughtEnergyVar : 0;
                            $daily_processed['dailySellEnergy'] = $dailySellEnergyVar >= 0 ? $dailySellEnergyVar : 0;
                            $daily_processed['dailyMaxSolarPower'] = 0;
                            $daily_processed['dailySaving'] = (double)$dailyGenerationVar * (double)$plant->benchmark_price;
                            $daily_processed['lastUpdated'] = Date('Ymd', strtotime($createdAtDate)) . 'T' . Date('His', strtotime($createdAtDate));
                            $daily_processed['updated_at'] = Date('Y-m-d H:i:s');
                            $daily_processed['created_at'] = date('Y-m-d', strtotime($createdAtDate));
                            //dd($daily_processed);
                            $processed_plant_detail_exist = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', '=', date('Y-m-d', strtotime($createdAtDate)))->orderBy('created_at', 'DESC')->first();
                            // dd($processed_plant_detail_exist);
                            if ($processed_plant_detail_exist != null) {
                                $processed_plant_detail_insertion_responce = $processed_plant_detail_exist->fill($daily_processed)->save();
                                //dd($processed_plant_detail_insertion_responce);
                            } else {

                                $processed_plant_detail_insertion_responce = DailyProcessedPlantDetail::create($daily_processed);
                            }

                            $plantDailySavingDataSum = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m', strtotime($createdAtDate)))->sum('dailySaving');

                            //Monthly Processed Data
                            $monthly_processed['plant_id'] = $plant->id;
                            $monthly_processed['monthlyGeneration'] = $monthlyGenerationVar;
                            $monthly_processed['monthlyConsumption'] = $monthlyConsumptionVar;
                            $monthly_processed['monthlyGridPower'] = $monthlyGridVar;
                            $monthly_processed['monthlyBoughtEnergy'] = $monthlyBoughtEnergyVar >= 0 ? $monthlyBoughtEnergyVar : 0;
                            $monthly_processed['monthlySellEnergy'] = $monthlySellEnergyVar >= 0 ? $monthlySellEnergyVar : 0;
                            $monthly_processed['monthlyMaxSolarPower'] = 0;
                            $monthly_processed['monthlySaving'] = $plantDailySavingDataSum;
                            $monthly_processed['lastUpdated'] = Date('Ymd', strtotime($createdAtDate)) . 'T' . Date('His', strtotime($createdAtDate));
                            $monthly_processed['updated_at'] = Date('Y-m-d H:i:s');
                            $monthly_processed['created_at'] = date('Y-m-d', strtotime($createdAtDate));
                            //dd($monthly_processed);
                            $processed_plant_detail_exist = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->where('created_at', 'LIKE', date('Y-m', strtotime($createdAtDate)) . '%')->orderBy('created_at', 'DESC')->first();
                            // dd($processed_plant_detail_exist);
                            if ($processed_plant_detail_exist != null) {
                                $processed_plant_detail_insertion_responce = $processed_plant_detail_exist->fill($monthly_processed)->save();
                            } else {
//                                $monthly_processed['created_at'] = Date('Y-m-d H:i:s');
                                $processed_plant_detail_insertion_responce = MonthlyProcessedPlantDetail::create($monthly_processed);
                            }
                            $yearly_processed_plant_detail_exist = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y', strtotime($createdAtDate)))->orderBy('created_at', 'DESC')->sum('monthlyGeneration');

                            if ($yearly_processed_plant_detail_exist != null && $yearly_processed_plant_detail_exist != 0) {
                                $yearlyGenerationVar = $yearly_processed_plant_detail_exist;
                            } else {
                                $yearlyGenerationVar = 0;
                            }

                            if ($plant->system_type == 1) {

                                $yearlyConsumptionVar = $yearlyGenerationVar;
                            }

                            $plantMonthlySavingDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', date('Y', strtotime($createdAtDate)))->sum('monthlySaving');

                            $yearly_processed['plant_id'] = $plant->id;
                            $yearly_processed['yearlyGeneration'] = $yearlyGenerationVar;
                            $yearly_processed['yearlyConsumption'] = $yearlyConsumptionVar;
                            $yearly_processed['yearlyGridPower'] = $yearlyGridVar;
                            $yearly_processed['yearlyBoughtEnergy'] = $yearlyBoughtEnergyVar >= 0 ? $yearlyBoughtEnergyVar : 0;
                            $yearly_processed['yearlySellEnergy'] = $yearlySellEnergyVar >= 0 ? $yearlySellEnergyVar : 0;
                            $yearly_processed['yearlyMaxSolarPower'] = 0;
                            $yearly_processed['yearlySaving'] = $plantMonthlySavingDataSum;
                            $yearly_processed['lastUpdated'] = Date('Ymd',strtotime($createdAtDate)) . 'T' . Date('His',strtotime($createdAtDate));
                            $yearly_processed['updated_at'] = Date('Y-m-d H:i:s');
                            $yearly_processed['created_at'] = date('Y-m-d', strtotime($createdAtDate));
                            $processed_plant_detail_exist = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->whereYear('created_at', '=', date('Y',strtotime($createdAtDate)))->orderBy('created_at', 'DESC')->first();
                            // dd($processed_plant_detail_exist);
                            if ($processed_plant_detail_exist != null) {
                                $processed_plant_detail_insertion_responce = $processed_plant_detail_exist->fill($yearly_processed)->save();
                            } else {
                                $yearly_processed['created_at'] = Date('Y-m-d H:i:s');
                                $processed_plant_detail_insertion_responce = YearlyProcessedPlantDetail::create($yearly_processed);
                            }
                            $cronJobDate = date('Y-m-d H:i:s', strtotime($cronJobDate . ' + 1days'));
                        }
                    }
//                    return date('Y-m-d',strtotime($cronJobDate));
                    if (date('Y-m-d', strtotime($cronJobDate)) > date('Y-m-d')) {
                        $cronJobDate = date('Y-m-d H:i:s', strtotime($cronJobDate . ' - 1days'));
                    }
                    $plantTenDayDetail = new PlantSiteTenMinuteData();
                    $plantTenDayDetail->plant_id = $plant->id;
                    $plantTenDayDetail->start_cron_job_date = $startDateRange;
                    $plantTenDayDetail->end_cron_job_date = $cronJobDate;
                    $plantTenDayDetail->save();
                }

            }
        }
    }
}
