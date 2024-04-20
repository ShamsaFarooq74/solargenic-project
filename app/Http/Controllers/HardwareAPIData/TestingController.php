<?php

namespace App\Http\Controllers\HardwareAPIData;

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

class TestingController extends Controller
{
    public function index($globalGenerationLogMaxID = null, $globalProcessedLogMaxID = null, $globalInverterDetailMaxID = null) {

        //try {

            date_default_timezone_set('Asia/Karachi');
            $currentTime = date('Y-m-d H:i:s');
            $generationLogMaxCronJobID = $globalGenerationLogMaxID + 1;
            $arTI = array();
            $arYI = array();
            $arTE = array();
            $arYE = array();
            $processedMaxCronJobID = $globalProcessedLogMaxID + 1;
            $inverterMaxCronJobID = $globalInverterDetailMaxID + 1;
            $inverterEnergyLogMaxCronJobID = InverterEnergyLog::max('cron_job_id') + 1;
            $envReductionValue = Setting::where('perimeter', 'env_reduction')->exists() ? Setting::where('perimeter', 'env_reduction')->first()->value : 0;
            $irradianceValue = Setting::where('perimeter', 'irradiance')->exists() ? Setting::where('perimeter', 'irradiance')->first()->value : 0;

            //ALL PLANTS DATA
            $allPlantsData = Plant::where('meter_type', 'Huawei')->get();
            // $allPlantsData = Plant::where('id', 66)->where('meter_type', 'Huawei')->get();
            // $allPlantsData = Plant::where('id', 65)->where('meter_type', 'Huawei')->get();

            if($allPlantsData) {

                foreach($allPlantsData as $key => $plant) {

                    $plantID = $plant->id;

                    $dd = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

                    /*for($i = 1; $i <= $dd; $i++) {

                        $plantDailyTotalGeneration = 0;
                        $plantDailyTotalBuyEnergy = 0;
                        $plantDailyTotalIrradiance = 0;
                        $plantDailyTotalSellEnergy = 0;
                        $plantGridInverterArray = array();

                        if($i <= (int)date('d')) {

                            if($i < 10) {
                                $i = '0'.$i;
                            }

                            $plantInverterListData = SiteInverterDetail::where('plant_id', $plantID)->get();
                            $plantDataDate = date('Y-m-d', strtotime(date('Y-m-'.$i)));
                            $plantDataDateYesterday = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-'.$i))));

                            foreach($plantInverterListData as $invListData) {

                                if($invListData->dv_inverter_type == 1) {

                                    $plantDailyTotalGeneration += InverterDetail::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDate)->orderBy('collect_time', 'DESC')->exists() ? InverterDetail::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDate)->orderBy('collect_time', 'DESC')->first()->daily_generation : 0;
                                }
                                else if($invListData->dv_inverter_type == 10) {

                                    $plantDailyTotalIrradiance += InverterEMIDetail::where(['plant_id' => $plantID, 'dv_inverter' => $invListData->dv_inverter])->whereDate('collect_time', $plantDataDate)->exists() ? (double)(InverterEMIDetail::where(['plant_id' => $plantID, 'dv_inverter' => $invListData->dv_inverter])->whereDate('collect_time', $plantDataDate)->orderBy('collect_time', 'DESC')->first()->radiant_total) * (double)($irradianceValue) : 0;
                                }
                                else if($invListData->dv_inverter_type == 17) {

                                    $inverterEnergyTodayImportData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDate)->orderBy('collect_time', 'DESC')->exists() ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDate)->orderBy('collect_time', 'DESC')->whereNotNull('import_energy')->first()->import_energy : 0;
                                    $inverterEnergyYesterdayImportData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->exists() ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->whereNotNull('import_energy')->first()->import_energy : 0;
                            
                                    $inverterEnergyTodayExportData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDate)->orderBy('collect_time', 'DESC')->exists() ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDate)->orderBy('collect_time', 'DESC')->whereNotNull('export_energy')->first()->export_energy : 0;
                                    $inverterEnergyYesterdayExportData = InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->exists() ? InverterEnergyLog::where('plant_id', $plantID)->where('dv_inverter', $invListData->dv_inverter)->whereDate('collect_time', $plantDataDateYesterday)->orderBy('collect_time', 'DESC')->whereNotNull('export_energy')->first()->export_energy : 0;
                    
                                    $plantDailyTotalBuyEnergy += (double)$inverterEnergyTodayImportData - (double)$inverterEnergyYesterdayImportData;
                                    $plantDailyTotalSellEnergy += (double)$inverterEnergyTodayExportData - (double)$inverterEnergyYesterdayExportData;
                                }
                            }

                            //PLANT DAILY DATA
                            $dailyProcessed['plant_id'] = $plantID;
                            $dailyProcessed['dailyGeneration'] = $plantDailyTotalGeneration;
                            $dailyProcessed['dailyGridPower'] = $plantDailyTotalBuyEnergy > $plantDailyTotalSellEnergy ? $plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy : $plantDailyTotalSellEnergy - $plantDailyTotalBuyEnergy;
                            $dailyProcessed['dailyBoughtEnergy'] = $plantDailyTotalBuyEnergy > 0 ? $plantDailyTotalBuyEnergy : 0;
                            $dailyProcessed['dailySellEnergy'] = $plantDailyTotalSellEnergy > 0 ? $plantDailyTotalSellEnergy : 0;
                            $dailyProcessed['dailyMaxSolarPower'] = 0; // Will be set in future
                            $dailyProcessed['dailyConsumption'] = $plantDailyTotalGeneration + ($plantDailyTotalBuyEnergy - $plantDailyTotalSellEnergy);
                            $dailyProcessed['dailySaving'] = (double)$plantDailyTotalGeneration * (double)$plant->benchmark_price;
                            $dailyProcessed['dailyIrradiance'] = 0;
                            $dailyProcessed['updated_at'] = $currentTime;

                            $dailyProcessedPlantDetailExist = DailyProcessedPlantDetail::where('plant_id',$plantID)->whereDate('created_at', $plantDataDate)->orderBy('created_at', 'DESC')->first();
                            
                            if($dailyProcessedPlantDetailExist){

                                $dailyProcessedPlantDetailInsertionResponce =  $dailyProcessedPlantDetailExist->fill($dailyProcessed)->save();
                            }
                            else {

                                $dailyProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($plantDataDate));
                                $dailyProcessedPlantDetailInsertionResponce = DailyProcessedPlantDetail::create($dailyProcessed);
                            }

                            //PLANT DAILY EMI DATA
                            $dailyEMIProcessed['plant_id'] = $plantID;
                            $dailyEMIProcessed['daily_irradiance'] = $plantDailyTotalIrradiance;
                            $dailyEMIProcessed['updated_at'] = $currentTime;

                            $dailyProcessedPlantEMIDetailExist = DailyProcessedPlantEMIDetail::where('plant_id',$plantID)->whereDate('created_at', $plantDataDate)->orderBy('created_at', 'DESC')->first();
                      
                            if($dailyProcessedPlantEMIDetailExist) {

                                $dailyProcessedPlantEMIDetailInsertionResponce =  $dailyProcessedPlantEMIDetailExist->fill($dailyEMIProcessed)->save();
                            }
                            else {

                                $dailyEMIProcessed['created_at'] = date('Y-m-d H:i:s', strtotime($plantDataDate));
                                $dailyProcessedPlantEMIDetailInsertionResponce = DailyProcessedPlantEMIDetail::create($dailyEMIProcessed);
                            }
                        }
                    }

                    $plantDailyGenerationDataSum = 0;
                    $plantDailyConsumptionDataSum = 0;
                    $plantDailyGridDataSum = 0;
                    $plantDailyBoughtDataSum = 0;
                    $plantDailySellDataSum = 0;
                    $plantDailySavingDataSum = 0;
                    $plantDailyIrradianceDataSum = 0;

                    $monthDays = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

                    for($mD = 1; $mD <= $monthDays; $mD++) {

                        if($mD < 10) {

                            $mD = '0'.$mD;
                        }

                        $plantDailyGenerationDataSum += DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->orderBy('updated_at', 'DESC')->first()->dailyGeneration : 0;
                        $plantDailyConsumptionDataSum += DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->orderBy('updated_at', 'DESC')->first()->dailyConsumption : 0;
                        $plantDailyGridDataSum += DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->orderBy('updated_at', 'DESC')->first()->dailyGridPower : 0;
                        $plantDailyBoughtDataSum += DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->orderBy('updated_at', 'DESC')->first()->dailyBoughtEnergy : 0;
                        $plantDailySellDataSum += DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->orderBy('updated_at', 'DESC')->first()->dailySellEnergy : 0;
                        $plantDailySavingDataSum += DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->orderBy('updated_at', 'DESC')->first()->dailySaving : 0;
                        $plantDailyIrradianceDataSum += DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->exists() ? DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m-'.$mD))->orderBy('updated_at', 'DESC')->first()->daily_irradiance : 0;
                    }

                    //PLANT MONTHLY DATA
                    $monthlyProcessed['plant_id'] = $plantID;
                    $monthlyProcessed['monthlyGeneration'] = $plantDailyGenerationDataSum;
                    $monthlyProcessed['monthlyConsumption'] = $plantDailyConsumptionDataSum;
                    $monthlyProcessed['monthlyGridPower'] = $plantDailyGridDataSum;
                    $monthlyProcessed['monthlyBoughtEnergy'] = $plantDailyBoughtDataSum;
                    $monthlyProcessed['monthlySellEnergy'] = $plantDailySellDataSum;
                    $monthlyProcessed['monthlySaving'] = $plantDailySavingDataSum;
                    $monthlyProcessed['monthlyIrradiance'] = 0;
                    $monthlyProcessed['updated_at'] = $currentTime;

                    $monthlyProcessedPlantDetailExist = MonthlyProcessedPlantDetail::where('plant_id',$plantID)->where('created_at', 'LIKE', date('Y-m').'%')->orderBy('created_at', 'DESC')->first();

                    if($monthlyProcessedPlantDetailExist){

                        $monthlyProcessedPlantDetailResponse =  $monthlyProcessedPlantDetailExist->fill($monthlyProcessed)->save();
                    }
                    else {

                        $monthlyProcessedPlantDetailResponse = MonthlyProcessedPlantDetail::create($monthlyProcessed);
                    }

                    //PLANT MONTHLY EMI DATA
                    $monthlyEMIProcessed['plant_id'] = $plantID;
                    $monthlyEMIProcessed['monthly_irradiance'] = $plantDailyIrradianceDataSum;
                    $monthlyEMIProcessed['updated_at'] = $currentTime;

                    $monthlyProcessedPlantEMIDetailExist = MonthlyProcessedPlantEMIDetail::where('plant_id',$plantID)->where('created_at', 'LIKE', date('Y-m').'%')->orderBy('created_at', 'DESC')->first();

                    if($monthlyProcessedPlantEMIDetailExist) {

                        $monthlyProcessedPlantEMIDetailResponse =  $monthlyProcessedPlantEMIDetailExist->fill($monthlyEMIProcessed)->save();
                    }
                    else {

                        $monthlyProcessedPlantEMIDetailResponse = MonthlyProcessedPlantEMIDetail::create($monthlyEMIProcessed);
                    }

                    //PLANT YEARLY DATA
                    $plantMonthlyGenerationDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', date('Y'))->sum('monthlyGeneration');
                    $plantMonthlyConsumptionDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', date('Y'))->sum('monthlyConsumption');
                    $plantMonthlyGridDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', date('Y'))->sum('monthlyGridPower');
                    $plantMonthlyBoughtDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', date('Y'))->sum('monthlyBoughtEnergy');
                    $plantMonthlySellDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', date('Y'))->sum('monthlySellEnergy');
                    $plantMonthlySavingDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', date('Y'))->sum('monthlySaving');

                    $yearlyProcessed['plant_id'] = $plantID;
                    $yearlyProcessed['yearlyGeneration'] = $plantMonthlyGenerationDataSum;
                    $yearlyProcessed['yearlyConsumption'] = $plantMonthlyConsumptionDataSum;
                    $yearlyProcessed['yearlyGridPower'] = $plantMonthlyGridDataSum;
                    $yearlyProcessed['yearlyBoughtEnergy'] = $plantMonthlyBoughtDataSum;
                    $yearlyProcessed['yearlySellEnergy'] = $plantMonthlySellDataSum;
                    $yearlyProcessed['yearlySaving'] = $plantMonthlySavingDataSum;
                    $yearlyProcessed['yearlyIrradiance'] = 0;
                    $yearlyProcessed['updated_at'] = $currentTime;

                    $yearlyProcessedPlantDetailExist = YearlyProcessedPlantDetail::where('plant_id',$plantID)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();

                    if($yearlyProcessedPlantDetailExist){

                        $yearlyProcessedPlantDetailResponse =  $yearlyProcessedPlantDetailExist->fill($yearlyProcessed)->save();
                    }
                    else {

                        $yearlyProcessedPlantDetailResponse = YearlyProcessedPlantDetail::create($yearlyProcessed);
                    }

                    //PLANT YEARLY EMI DATA
                    $plantMonthlyIrradianceDataSum = MonthlyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', date('Y'))->sum('monthly_irradiance');

                    $yearlyEMIProcessed['plant_id'] = $plantID;
                    $yearlyEMIProcessed['yearly_irradiance'] = $plantMonthlyIrradianceDataSum;
                    $yearlyEMIProcessed['updated_at'] = $currentTime;

                    $yearlyProcessedPlantEMIDetailExist = YearlyProcessedPlantEMIDetail::where('plant_id',$plantID)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();

                    if($yearlyProcessedPlantEMIDetailExist){

                        $yearlyProcessedPlantEMIDetailResponse =  $yearlyProcessedPlantEMIDetailExist->fill($yearlyEMIProcessed)->save();
                    }
                    else {

                        $yearlyProcessedPlantEMIDetailResponse = YearlyProcessedPlantEMIDetail::create($yearlyEMIProcessed);
                    }*/

                    //PLANT TOTAL DATA
                    $plantTotalCurrentDataSum = ProcessedCurrentVariable::where('plant_id', $plantID)->where('processed_cron_job_id', $generationLogMaxCronJobID)->sum('current_generation');
                    $plantTotalGenerationDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyGeneration');
                    $plantTotalConsumptionDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyConsumption');
                    $plantTotalGridDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyGridPower');
                    $plantTotalBoughtDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlyBoughtEnergy');
                    $plantTotalSellDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlySellEnergy');
                    $plantTotalSavingDataSum = YearlyProcessedPlantDetail::where('plant_id', $plantID)->sum('yearlySaving');

                    $totalProcessed['plant_id'] = $plantID;
                    $totalProcessed['plant_total_current_power'] = $plantTotalCurrentDataSum;
                    $totalProcessed['plant_total_generation'] = $plantTotalGenerationDataSum;
                    $totalProcessed['plant_total_consumption'] = $plantTotalConsumptionDataSum;
                    $totalProcessed['plant_total_grid'] = $plantTotalGridDataSum;
                    $totalProcessed['plant_total_buy_energy'] = $plantTotalBoughtDataSum;
                    $totalProcessed['plant_total_sell_energy'] = $plantTotalSellDataSum;
                    $totalProcessed['plant_total_saving'] = $plantTotalSavingDataSum;
                    $totalProcessed['plant_total_reduction'] = (double)$plantTotalGenerationDataSum * (double)$envReductionValue;
                    $totalProcessed['plant_total_irradiance'] = 0;
                    $totalProcessed['updated_at'] = $currentTime;

                    $totalProcessedPlantDetailExist = TotalProcessedPlantDetail::where('plant_id',$plantID)->first();

                    if($totalProcessedPlantDetailExist){

                        $totalProcessedPlantDetailResponse =  $totalProcessedPlantDetailExist->fill($totalProcessed)->save();
                    }
                    else {

                        $totalProcessedPlantDetailResponse = TotalProcessedPlantDetail::create($totalProcessed);
                    }

                    //PLANT TOTAL EMI DATA
                    $plantTotalIrradianceDataSum = YearlyProcessedPlantEMIDetail::where('plant_id', $plantID)->sum('yearly_irradiance');

                    $totalEMIProcessed['plant_id'] = $plantID;
                    $totalEMIProcessed['total_irradiance'] = (double)$plantTotalIrradianceDataSum;
                    $totalEMIProcessed['updated_at'] = $currentTime;

                    $totalProcessedPlantEMIDetailExist = TotalProcessedPlantEMIDetail::where('plant_id',$plantID)->first();

                    if($totalProcessedPlantEMIDetailExist){

                        $totalProcessedPlantEMIDetailResponse =  $totalProcessedPlantEMIDetailExist->fill($totalEMIProcessed)->save();
                    }
                    else {

                        $totalProcessedPlantEMIDetailResponse = TotalProcessedPlantEMIDetail::create($totalEMIProcessed);
                    }

                    //PLANT EMI DATA
                    /*$monthTotalDay = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

                    for($a = 1; $a <= $monthTotalDay; $a++) {

                        if(strtotime(date('Y-m').'-'.$a) < strtotime(date('Y-m-d'))) {

                            $plantTotalIrradiance = 0;

                            if(!(DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m').'-'.$a)->exists())) {

                                foreach($totalEMIInverterArray as $emiArrayKey => $emiInverter) {

                                    $plantTotalIrradiance += InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $emiInverter])->whereDate('collect_time', date('Y-m').'-'.$a)->exists() ? (double)(InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $emiInverter])->whereDate('collect_time', date('Y-m').'-'.$a)->orderBy('collect_time', 'DESC')->first()->radiant_total) * (double)($irradianceValue) : 0;
                                }

                                //DAILY EMI DATA
                                $dailyEMIProcessed['plant_id'] = $plantID;
                                $dailyEMIProcessed['daily_irradiance'] = $plantTotalIrradiance;
                                $dailyEMIProcessed['created_at'] = date('Y-m').'-'.$a.' '.date('H:i:s');
                                $dailyEMIProcessed['updated_at'] = $currentTime;

                                $dailyProcessedPlantEMIDetailInsertionResponce = DailyProcessedPlantEMIDetail::create($dailyEMIProcessed);
                            }
                            else if((DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m').'-'.$a)->first()->daily_irradiance) == 0) {

                                foreach($totalEMIInverterArray as $emiArrayKey => $emiInverter) {

                                    $plantTotalIrradiance += InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $emiInverter])->whereDate('collect_time', date('Y-m').'-'.$a)->exists() ? (double)(InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $emiInverter])->whereDate('collect_time', date('Y-m').'-'.$a)->orderBy('collect_time', 'DESC')->first()->radiant_total) * (double)($irradianceValue) : 0;
                                }

                                //DAILY EMI DATA
                                $dailyEMIProcessed['plant_id'] = $plantID;
                                $dailyEMIProcessed['daily_irradiance'] = $plantTotalIrradiance;
                                $dailyEMIProcessed['updated_at'] = $currentTime;

                                $dailyProcessedPlantEMIDetailExist = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', date('Y-m').'-'.$a)->first();

                                if($dailyProcessedPlantEMIDetailExist) {

                                    $dailyProcessedPlantEMIDetailResponse =  $dailyProcessedPlantEMIDetailExist->fill($dailyEMIProcessed)->save();
                                }
                            }
                        }
                    }

                    $plantTotalIrradiance = 0;

                    foreach($totalEMIInverterArray as $emiArrayKey => $emiInverter) {

                        $plantTotalIrradiance += InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $emiInverter])->whereDate('collect_time', date('Y-m-d'))->exists() ? (double)(InverterEMIDetail::where(['plant_id' => $plantID, 'site_id' => $siteID, 'dv_inverter' => $emiInverter])->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC')->first()->radiant_total) * (double)($irradianceValue) : 0;
                    }

                    //PLANT DAILY EMI DATA
                    $dailyEMIProcessed['plant_id'] = $plantID;
                    $dailyEMIProcessed['daily_irradiance'] = $plantTotalIrradiance;
                    $dailyEMIProcessed['updated_at'] = $currentTime;

                    $dailyProcessedPlantEMIDetailExist = DailyProcessedPlantEMIDetail::where('plant_id',$plantID)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();

                    if($dailyProcessedPlantEMIDetailExist){

                        $dailyProcessedPlantEMIDetailResponse =  $dailyProcessedPlantEMIDetailExist->fill($dailyEMIProcessed)->save();
                    }
                    else {

                        $dailyProcessedPlantEMIDetailResponse = DailyProcessedPlantEMIDetail::create($dailyEMIProcessed);
                    }


                    //PLANT MONTHLY EMI DATA
                    $plantDailyIrradianceDataSum = DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('daily_irradiance');

                    $monthlyEMIProcessed['plant_id'] = $plantID;
                    $monthlyEMIProcessed['monthly_irradiance'] = $plantDailyIrradianceDataSum;
                    $monthlyEMIProcessed['updated_at'] = $currentTime;

                    $monthlyProcessedPlantEMIDetailExist = MonthlyProcessedPlantEMIDetail::where('plant_id',$plantID)->where('created_at', 'LIKE', date('Y-m').'%')->orderBy('created_at', 'DESC')->first();

                    if($monthlyProcessedPlantEMIDetailExist){

                        $monthlyProcessedPlantEMIDetailResponse =  $monthlyProcessedPlantEMIDetailExist->fill($monthlyEMIProcessed)->save();
                    }
                    else {

                        $monthlyProcessedPlantEMIDetailResponse = MonthlyProcessedPlantEMIDetail::create($monthlyEMIProcessed);
                    }

                    //PLANT YEARLY DATA
                    $plantMonthlyIrradianceDataSum = MonthlyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', date('Y'))->sum('monthly_irradiance');

                    $yearlyEMIProcessed['plant_id'] = $plantID;
                    $yearlyEMIProcessed['yearly_irradiance'] = $plantMonthlyIrradianceDataSum;
                    $yearlyEMIProcessed['updated_at'] = $currentTime;

                    $yearlyProcessedPlantEMIDetailExist = YearlyProcessedPlantEMIDetail::where('plant_id',$plantID)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();

                    if($yearlyProcessedPlantEMIDetailExist){

                        $yearlyProcessedPlantEMIDetailResponse =  $yearlyProcessedPlantEMIDetailExist->fill($yearlyEMIProcessed)->save();
                    }
                    else {

                        $yearlyProcessedPlantEMIDetailResponse = YearlyProcessedPlantEMIDetail::create($yearlyEMIProcessed);
                    }*/
                }

                //ACCUMULATIVE DATA
                $accumulativeCurrentDataSum = TotalProcessedPlantDetail::sum('plant_total_current_power');
                $accumulativeGenerationDataSum = TotalProcessedPlantDetail::sum('plant_total_generation');
                $accumulativeReductionDataSum = TotalProcessedPlantDetail::sum('plant_total_reduction');

                $accumulativeProcessed['total_current_power'] = $accumulativeCurrentDataSum;
                $accumulativeProcessed['total_generation'] = $accumulativeGenerationDataSum;
                $accumulativeProcessed['total_reduction'] = $accumulativeReductionDataSum;
                $accumulativeProcessed['updated_at'] = $currentTime;

                $accumulativeProcessedPlantDetailExist = AccumulativeProcessedDetail::first();

                if($accumulativeProcessedPlantDetailExist){

                    $accumulativeProcessedPlantDetailResponse =  $accumulativeProcessedPlantDetailExist->fill($accumulativeProcessed)->save();
                }
                else {

                    $accumulativeProcessedPlantDetailResponse = AccumulativeProcessedDetail::create($accumulativeProcessed);
                }
            }
        /*}

        catch (Exception $e) {

            return $e->getMessage();
        }*/
// return [$arTI, $arYI, $arTE, $arYE];
    }

    public function getTokenAndSessionID() {

        $huaweiAPIBaseURL = Setting::where('perimeter', 'huawei_api_base_url')->exists() ? Setting::where('perimeter', 'huawei_api_base_url')->first()->value : '';
        $huaweiAPIUserName = Setting::where('perimeter', 'huawei_api_user_name')->exists() ? Setting::where('perimeter', 'huawei_api_user_name')->first()->value : '';
        $huaweiAPISystemCode = Setting::where('perimeter', 'huawei_api_system_code')->exists() ? Setting::where('perimeter', 'huawei_api_user_name')->first()->value : '';

        $loginData = [
            "userName" => "FbenergyLTD",
            "systemCode" => "Huawei@2021",
        ];

        $url = 'https://13.251.20.171/thirdData/login';

        $loginCurl = curl_init($url);

        $loginDataEncoded = json_encode($loginData);

        $fp = fopen(__DIR__ . "/cookieFileError.txt", 'w');
        $fp1 = fopen(__DIR__ . "/cookieFileData.txt", 'w');

        chmod(__DIR__ . "/cookieFileError.txt", 0777);
        chmod(__DIR__ . "/cookieFileData.txt", 0777);

        curl_setopt($loginCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($loginCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($loginCurl, CURLOPT_COOKIEFILE, __DIR__ . "/cookieFileData.txt");
        curl_setopt($loginCurl, CURLOPT_COOKIESESSION, 1);
        curl_setopt($loginCurl, CURLOPT_POST, 1);
        curl_setopt($loginCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($loginCurl, CURLINFO_HEADER_OUT, true);
        curl_setopt($loginCurl, CURLOPT_POSTFIELDS, $loginDataEncoded);
        curl_setopt($loginCurl, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
        curl_setopt($loginCurl, CURLOPT_DNS_CACHE_TIMEOUT, 2 );
        curl_setopt($loginCurl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($loginCurl, CURLOPT_VERBOSE, true);
        curl_setopt($loginCurl, CURLOPT_STDERR, $fp);
        curl_setopt($loginCurl, CURLOPT_COOKIEJAR, __DIR__ . "/cookieFileData.txt");

        $data=curl_exec($loginCurl);
        curl_close($loginCurl);

        $lines = file(__DIR__ . "/cookieFileData.txt");

        $tokenLine = '';
        $jSessionLine = '';
        $csrfToken = '';
        $jSessionID = '';

        foreach ($lines as $line_num => $line) {

            if($line_num == 4) {
                $jSessionLine = $line;
            }
            if($line_num == 5) {
                $tokenLine = $line;
            }
        }

        $TokenArr = preg_split("@[\s+　]@u", $tokenLine);
        $sessionIDArr = preg_split("@[\s+　]@u", $jSessionLine);

        if(!empty($TokenArr) && isset($TokenArr[6])) {

            $csrfToken = $TokenArr[6];
        }

        if(!empty($sessionIDArr) && isset($sessionIDArr[6])) {

            $jSessionID = $sessionIDArr[6];
        }

        return [$csrfToken, $jSessionID];
    }
}
