<?php


namespace App\Http\Controllers\Api;


use App\Http\Models\CronJobTime;
use App\Http\Models\DailyProcessedPlantEMIDetail;
use App\Http\Models\InverterEnergyLog;
use App\Http\Models\InverterVersionInformation;
use App\Http\Models\Plant;
use App\Http\Models\PlantDetail;
use App\Http\Models\InverterDetail;
use App\Http\Models\PlantMeterType;
use App\Http\Models\PlantSite;
use App\Http\Models\Inverter;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\PlantUser;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\ProcessedCurrentVariableHistory;
use App\Http\Models\ProcessedPlantDetail;
use App\Http\Models\Notification;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\StationBattery;
use App\Http\Models\StationBatteryData;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\GenerationLog;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\PlantType;
use App\Http\Models\SystemType;
use App\Http\Models\Setting;
use App\Http\Models\Weather;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Controllers\Controller;
use App\Http\Models\Company;
use App\Http\Models\UserCompany;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Models\TotalProcessedPlantDetail;
use App\Http\Models\InverterMPPTDetail;

use Carbon\Carbon;
use function GuzzleHttp\Psr7\str;

class IntrixController extends ResponseController
{

    private $url_scheme;
    private $domain;

    public function __construct()
    {
        $full_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->url_scheme = parse_url($full_url, PHP_URL_SCHEME);

        $this->domain = request()->getHttpHost();

        date_default_timezone_set("Asia/Karachi");
    }
    public function banners(Request $request){

        $banner = DB::table('intrix_banners')->where("plant_id",$request->plant_id)->where('read_status','N' )->first();

        $response = [
            'status' => 1,
            'message' => "success",
            'Data' => $banner,
        ];
        return response()->json($response, 200);
    }
    public function bannerStatus(Request $request){

        $banner = DB::table('intrix_banners')
            ->where('id', $request->banner_id)
            ->update(['read_status' => "Y"]);

        $response = [
            'status' => 1,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function plantEnvBenefitGraph(Request $request)
    {

        $time = $request->time;
        $id = $request->plant_id;
        $id = (int)$id;
        $dates = strtotime($request->date);
        $envPlanting = Setting::where('perimeter', 'env_planting')->first();
        $envPlanting = !empty($envPlanting) ? $envPlanting->value : 0.00131;
        $envReduction = Setting::where('perimeter', 'env_reduction')->first();
        $envReduction = !empty($envReduction) ? $envReduction->value : 0.000646155;
        $envCoal = Setting::where('perimeter', 'env_coal')->first();
        $envCoal = !empty($envCoal) ? $envCoal->value : 0.538;
        if ($time == 'day') {

            $dailyGene = 0;
            $date = date('Y-m-d', $dates);
            if ($id != 0) {
                $dailyGene = DailyProcessedPlantDetail::where('plant_id', $id)->whereDate('created_at', $date)->latest()->first();
                if($dailyGene){
                    $dailyGene = isset($dailyGene->dailyGeneration) ? $dailyGene->dailyGeneration : 0;
                }
            }
            $treesPlanting =  $dailyGene * $envPlanting;
            $envreduction =  $dailyGene * $envReduction;
            $envCoal =  $dailyGene * $envCoal;

            $response = [
                'status' => 1,
                'message' => "success",
                'time' => $time,
                'date' => $date,
                'trees' => (string)round($treesPlanting, 2),
                'Co2' => (string)round($envreduction, 2),
                'Coal' => (string)round($envCoal, 2)
            ];

        } else if ($time == 'month') {

            $date = date('Y-m', $dates);

            if ($id != 0) {
                $monthlyGene = MonthlyProcessedPlantDetail::where('plant_id', $id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGeneration');
            }

            $monthlyGene = $monthlyGene ? $monthlyGene : 0;
            $treesPlanting =  $monthlyGene * $envPlanting;
            $envreduction =  $monthlyGene * $envReduction;
            $envCoal =  $monthlyGene * $envCoal;

            $response = [
                'status' => 1,
                'message' => "success",
                'time' => $time,
                'date' => $date,
                'trees' => (string)round($treesPlanting, 2),
                'Co2' => (string)round($envreduction, 2),
                'Coal' => (string)round($envCoal, 2)
            ];

        } else if ($time == 'year') {

            $date = $request->date;
            if ($id != 0) {
                $yearlyGene = YearlyProcessedPlantDetail::where('plant_id', $id)->where('created_at', 'LIKE', $date . '%')->sum('yearlyGeneration');
            }

            $yearlyGene = $yearlyGene ? $yearlyGene : 0;
            $treesPlanting =  $yearlyGene * $envPlanting;
            $envreduction =  $yearlyGene * $envReduction;
            $envCoal =  $yearlyGene * $envCoal;

            $response = [
                'status' => 1,
                'message' => "success",
                'time' => $time,
                'date' => $date,
                'trees' => (string)round($treesPlanting, 2),
                'Co2' => (string)round($envreduction, 2),
                'Coal' => (string)round($envCoal, 2)
            ];

        }else if($time == 'custom'){
            $dailyGene = 0;
            $startDate = $request->fromDate;
            $todate = $request->toDate;

            $fromdate =strtotime($startDate);
            $todate = strtotime($todate);
            $datediff = $todate - $fromdate;

            $noofDays = round($datediff / (60 * 60 * 24));
            $date = $startDate;
            for($i= 0; $i < $noofDays ; $i++) {
                if ($id != 0) {
                    $GeneData = DailyProcessedPlantDetail::where('plant_id', $id)->whereDate('created_at', $startDate)->latest()->first();
                    if ($GeneData) {
                        $dailyGene += isset($GeneData->dailyGeneration) ? $GeneData->dailyGeneration : 0;
                    }
                }
                $startDate = date('Y-m-d', strtotime($startDate . "+1 days"));
            }
            $treesPlanting =  $dailyGene * $envPlanting;
            $envreduction =  $dailyGene * $envReduction;
            $envCoal =  $dailyGene * $envCoal;

            $response = [
                'status' => 1,
                'message' => "success",
                'time' => $time,
                'date' => $date,
                'trees' => (string)round($treesPlanting, 2),
                'Co2' => (string)round($envreduction, 2),
                'Coal' => (string)round($envCoal, 2)
            ];

        }
        return response()->json($response, 200);

    }

    public function plantCostSaving(Request $request)
    {
        date_default_timezone_set('Asia/Karachi');

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plant_id;

        if ($time == 'day') {
            $date = date('Y-m-d', $requestDate);
        } else if ($time == 'month') {
            $date = date('Y-m', $requestDate);
        } else if ($time == 'year') {
            $date = $request->date;
        }

        $benchMarkPrice = 0;
        $peakTeriffRate = 0;
        $plantData = Plant::where('id', $plantID)->first();
        if ($plantData) {
            $benchMarkPrice = $plantData->benchmark_price;
            $peakTeriffRate = $plantData->peak_teriff_rate;
            $capacity = $plantData->capacity;
            $expectedGeneration = $capacity * 3.64 ;
        }

        $todayLogData = [];
        $current = ['peak-hours-savings', 'generation-saving'];
        $peakHoursSaving = 0;
        $generationSaving = 0;
        $totalSaving = 0;


        foreach ($current as $key => $currentData) {

            if ($time == 'day') {
                $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_discharge_energy', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->first();
                if ($currentProcessedData) {
                    $peakHoursSaving = (double)$currentProcessedData->daily_peak_hours_battery_discharge * (int)$peakTeriffRate;
                    $generationSaving = $currentProcessedData->dailyGeneration * (int)$benchMarkPrice;
                    $totalSaving = round($peakHoursSaving + $generationSaving, 2);
                }
//                return[ $plantData->expected_generation,$benchMarkPrice];
                $expectedSaving = $expectedGeneration *(int)$benchMarkPrice;
                if ($currentData == 'peak-hours-savings') {
                    $peakHoursSaving = (double)$peakHoursSaving;
                } else if ($currentData == 'generation-saving') {
                    $generationSaving = (double)$generationSaving;
                    $expectedSaving = (double)$expectedSaving;
                }
            } else if ($time == 'month') {
                $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
                $monthlyHoursSaving = 0;
                $monthlyGenerationSaving = 0;
                if ($monthlyProcessedData) {
                    $monthlyHoursSaving = (double)$monthlyProcessedData->monthly_peak_hours_discharge_energy * (int)$peakTeriffRate;
                    $monthlyGenerationSaving = $monthlyProcessedData->monthlyGeneration * (int)$benchMarkPrice;
                    $totalSaving = round($monthlyHoursSaving + $monthlyGenerationSaving, 2);
                }
                $expectedSaving = $expectedGeneration * 30 * (int)$benchMarkPrice;
                if ($currentData == 'peak-hours-savings') {
                    $peakHoursSaving = (double)$monthlyHoursSaving;
                } else if ($currentData == 'generation-saving') {
                    $generationSaving = (double)$monthlyGenerationSaving;
                }
            } else if ($time == 'year') {

                $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearlySaving', 'yearly_peak_hours_discharge_energy', 'yearlyGeneration', 'yearly_peak_hours_grid_import', 'yearlyConsumption', 'yearly_peak_hours_consumption', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
                $yearlyPeakHours = 0;
                $yearlyGenerationSaving = 0;
                $yearlySolar = 0;
                if ($yearlyProcessedData) {
                    $yearlyPeakHours = $yearlyProcessedData->yearly_peak_hours_discharge_energy * (int)$peakTeriffRate;
                    $yearlyGenerationSaving = $yearlyProcessedData->yearlyGeneration * (int)$benchMarkPrice;
                    $totalSaving = round($yearlyPeakHours + $yearlyGenerationSaving, 2);
                }
                $expectedSaving = $expectedGeneration * 365 * (int)$benchMarkPrice;
                if ($currentData == 'peak-hours-savings') {
                    $peakHoursSaving = (double)$yearlyPeakHours;
                } else if ($currentData == 'generation-saving') {
                    $generationSaving = (double)$yearlyGenerationSaving;
                }
            }else if($time == 'custom'){
                $startDate = $request->fromDate;
                $todate = $request->toDate;

                $fromdate =strtotime($startDate);
                $todate = strtotime($todate);
                $datediff = $todate - $fromdate;

                $noofDays = round($datediff / (60 * 60 * 24));
                $expectedSaving = $expectedGeneration * $noofDays * (int)$benchMarkPrice;

                $date = $startDate;
                for($i= 0; $i < $noofDays ; $i++) {
                    $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_discharge_energy', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantID)->whereDate('created_at',$startDate)->orderBy('created_at', 'DESC')->first();
                    if ($currentProcessedData) {
                        $peakHoursSaving += (double)$currentProcessedData->daily_peak_hours_battery_discharge * (int)$peakTeriffRate;
                        $generationSaving += $currentProcessedData->dailyGeneration * (int)$benchMarkPrice;
                        $totalSaving += round($peakHoursSaving + $generationSaving, 2);
                    }
                    $startDate = date('Y-m-d', strtotime($startDate . "+1 days"));
                }
                if ($currentData == 'peak-hours-savings') {
                    $peakHoursSaving = (double)$peakHoursSaving;
                } else if ($currentData == 'generation-saving') {
                    $generationSaving = (double)round($generationSaving,2);
                }
            }
        }
        $response = [
            'status' => 1,
            'message' => "success",
            'time' => $time,
            'date' => $date,
            'totalSaving' => $totalSaving,
            'expectedSaving' => round($expectedSaving,2),
            'peak_hour_savings' => $peakHoursSaving,
            'generation_saving' => round($generationSaving,3)
        ];
        return response()->json($response, 200);

    }
    public function systemInfo(Request $request){

        $stationBatteryData = StationBatteryData::where('plant_id',$request->plant_id)->first();
        $plantDetail = InverterVersionInformation::select('HMI','protocol_version','rated_power')->where('plant_id', $request->plant_id)->first();
        $gridData = InverterEnergyLog::select('grid_type')->where('plant_id', $request->plant_id)->where('dv_inverter', $request->inverter_sn)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC')->first();
        $stationBattery = StationBattery::where('plant_id', $request->plant_id)->where('dv_inverter', $request->inverter_sn)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC')->first();


        $plantBatteryAh = isset($stationBatteryData) ? (int)$stationBatteryData['battery_ah'] : 0;
        $plantBatteryVoltage = isset($stationBatteryData) ? (double)$stationBatteryData['battery_voltage'] : 0;
        $batteryDOD = isset($stationBatteryData) ? (int)$stationBatteryData['battery_dod'] : 0;
        $noOfBattery = StationBatteryData::where('plant_id',$request->plant_id)->count();

        $batteryRemainingFormula = (($plantBatteryAh * $plantBatteryVoltage * $noOfBattery * ($batteryDOD / 100)) / 1000);
        $batteryRemaining = round($batteryRemainingFormula, 2);


        $batteryRatedPower = $this->unitConversion(isset($stationBattery->rated_power) ? $stationBattery->rated_power : 0 , 'W');
        $plantDetail['battery_type'] = isset($stationBattery->battery_type_data) ? $stationBattery->battery_type_data : "";
        $inverterRatedPower = $this->unitConversion($plantDetail['rated_power'], 'W');
        $plantDetail['inverter_rated_power'] = round($inverterRatedPower[0], 2) . ' ' . $inverterRatedPower[1];
        $plantDetail['inverter_type'] = isset($gridData->grid_type) ? $gridData->grid_type : "";
        $plantDetail['battery_sn'] = isset($stationBatteryData->serial_no) ? $stationBatteryData->serial_no : "";
//            $plantDetail['battery_rated_power'] = round($batteryRatedPower[0], 2) . ' ' . $batteryRatedPower[1];
        $plantDetail['battery_rated_power'] =(string)$batteryRemaining ." kWh";
        $response = [
            'status' => 1,
            'message' => "success",
            'Data' => $plantDetail,
        ];
        return response()->json($response, 200);

    }
    public function batteryInfo(Request $request){

        $stationBatteryData = StationBatteryData::where('plant_id',$request->plant_id)->first();
        $stationBatteryData["no_of_batteries"] = StationBatteryData::where('plant_id',$request->plant_id)->count();
        $response = [
            'status' => 1,
            'message' => "success",
            'Data' => $stationBatteryData,
        ];
        return response()->json($response, 200);

    }
    public
    function unitConversion($num, $unit)
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
}
