<?php


namespace App\Http\Controllers\Api;


use App\Http\Models\Plant;
use App\Http\Models\PlantDetail;
use App\Http\Models\InverterDetail;
use App\Http\Models\PlantSite;
use App\Http\Models\Inverter;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\PlantUser;
use App\Http\Models\ProcessedCurrentVariable;
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

class PlantController extends ResponseController
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

    public function dashboardFilterData(Request $request)
    {

        $userID = $request->user()->id;

        $plants_array = PlantUser::where('user_id', $userID)->pluck('plant_id')->toArray();
        $plants = Plant::whereIn('id', $plants_array)->get(['id', 'plant_name', 'system_type', 'plant_type']);

        $filter_data['company_array'] = UserCompany::join('companies', 'user_companies.company_id', 'companies.id')
            ->select('user_companies.*', 'companies.company_name')
            ->where('user_companies.user_id', $userID)
            ->get();
        $filter_data['province_array'] = Plant::select('province')->whereIn('id', $plants_array)->where('province', '!=', NULL)->groupBy('province')->get();
        $filter_data['city_array'] = Plant::select('city')->whereIn('id', $plants_array)->where('city', '!=', NULL)->groupBy('city')->get();

        foreach ($plants as $plant) {
            $system_types[] = $plant->system_type;
            $plant_types[] = $plant->plant_type;
        }

        $filter_data['system_type'] = SystemType::whereIn('id', $system_types)->get();
        $filter_data['plant_type'] = PlantType::whereIn('id', $plant_types)->get();
        $filter_data['plants'] = $plants;

        return $this->sendResponse(1, 'Dashboard filters data', $filter_data);
    }

    public function dashboard(Request $request)
    {

        try {

            $companyID = array();
            $plantsIDD = array();
            $plantsID = array();
            $result = array();
            $userID = $request->user()->id;

            if ($request->get('company_id')) {

                if ($request->get('company_id') == 'all') {

                    $companyID = UserCompany::where('user_id', $userID)->pluck('company_id')->toArray();
                } else {

                    foreach (explode(',', $request->get('company_id')) as $id) {

                        $companyID[] = (int)$id;
                    }
                }
            }

            if ($request->get('plant_id')) {

                if ($request->get('plant_id') == 'all') {

                    $plantsID = PlantUser::where('user_id', $userID)->pluck('plant_id')->toArray();
                } else {

                    foreach (explode(',', $request->get('plant_id')) as $id) {

                        $plantsID[] = (int)$id;
                    }
                }
            }

            $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->pluck('id')->toArray();

            if ($request->has('company_id')) {

                $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
            } else {

                $plantsIDD = $plantsID;
            }

            if (!empty($plantsIDD)) {

                $plantsData = Plant::whereIn('id', $plantsIDD);

                if ($request->get('plant_type') && $request->get('plant_type') != 'all' && $request->get('plant_type') != '') {

                    $plantsData->where('plant_type', $request->get('plant_type'));
                }
                if ($request->get('province') && $request->get('province') != 'all' && $request->get('province') != '') {

                    $plantsData->where('province', $request->get('province'));
                }
                if ($request->get('city') && $request->get('city') != 'all' && $request->get('city') != '') {

                    $plantsData->where('city', $request->get('city'));
                }

                $plants_array = $plantsData->pluck('id')->toArray();

                //$daily_processed_data = DailyProcessedPlantDetail::whereIn('plant_id',$plants_array)->whereBetween('created_at',[date('Y-m-d 0:00:00'),date('Y-m-d 23:59:00')])->first();

                $daily_processed_data = DB::table('daily_processed_plant_detail')
                    ->selectRaw('SUM(dailyGeneration) as dailyGeneration, SUM(dailyConsumption) as dailyConsumption, SUM(dailyBoughtEnergy) as dailyBoughtEnergy, SUM(dailySellEnergy) as dailySellEnergy, SUM(dailySaving) as dailySaving')
                    ->whereIn('daily_processed_plant_detail.plant_id', $plants_array)
                    ->whereBetween('created_at', [date('Y-m-d 0:00:00'), date('Y-m-d 23:59:59')])
                    ->orderBy('updated_at', 'DESC')->get();
//                return $daily_processed_data;

                $daily_processed_data = $daily_processed_data && $daily_processed_data[0] ? $daily_processed_data[0] : $daily_processed_data;

                $monthly_processed_data = DB::table('monthly_processed_plant_detail')
                    ->selectRaw('SUM(monthlyGeneration) as monthlyGeneration, SUM(monthlyConsumption) as monthlyConsumption, SUM(monthlyBoughtEnergy) as monthlyBoughtEnergy, SUM(monthlySellEnergy) as monthlySellEnergy, SUM(monthlySaving) as monthlySaving')
                    ->whereIn('monthly_processed_plant_detail.plant_id', $plants_array)
                    ->whereBetween('created_at', [date('Y-m-01 0:00:00'), date('Y-m-31 23:59:00')])
                    ->orderBy('updated_at', 'DESC')->get();

                $monthly_processed_data = $monthly_processed_data && $monthly_processed_data[0] ? $monthly_processed_data[0] : $monthly_processed_data;

                $yearly_processed_data = DB::table('yearly_processed_plant_detail')
                    ->selectRaw('SUM(yearlyGeneration) as yearlyGeneration, SUM(yearlyConsumption) as yearlyConsumption, SUM(yearlyBoughtEnergy) as yearlyBoughtEnergy, SUM(yearlySellEnergy) as yearlySellEnergy, SUM(yearlySaving) as yearlySaving')
                    ->whereIn('yearly_processed_plant_detail.plant_id', $plants_array)
                    ->whereBetween('created_at', [date('Y-01-01 0:00:00'), date('Y-m-d 23:59:00')])
                    ->orderBy('updated_at', 'DESC')->get();

                $yearly_processed_data = $yearly_processed_data && $yearly_processed_data[0] ? $yearly_processed_data[0] : $yearly_processed_data;

                $processed_cron_job_id_max = ProcessedCurrentVariable::max('processed_cron_job_id');

                $current_data = DB::table('processed_current_variables')
                    ->selectRaw('SUM(current_generation) as current_generation, SUM(current_consumption) as current_consumption')
                    ->whereIn('processed_current_variables.plant_id', $plants_array)
                    ->where('processed_cron_job_id', $processed_cron_job_id_max)
                    ->get();

                $current_grid_pos = ProcessedCurrentVariable::whereIn('plant_id', $plants_array)->where('processed_cron_job_id', $processed_cron_job_id_max)->where('grid_type', '+ve')->sum('current_grid');
                $current_grid_neg = ProcessedCurrentVariable::whereIn('plant_id', $plants_array)->where('processed_cron_job_id', $processed_cron_job_id_max)->where('grid_type', '-ve')->sum('current_grid');
                $currents_grid = $current_grid_pos - $current_grid_neg;

                $current_data = $current_data && $current_data[0] ? $current_data[0] : $current_data;

                $result['current_generation'] = $current_data ? number_format((double)$current_data->current_generation, 2) : '0';
                $result['current_consumption'] = $current_data ? number_format((double)$current_data->current_consumption, 2) : '0';

                $result['current_gird_import_power'] = number_format((double)$currents_grid, 2);
                $result['current_gird_export_power'] = number_format((double)$currents_grid, 2);
                $result['current_grid_type'] = $currents_grid >= 0 ? '+ve' : '-ve';
                $result['comm_fail'] = $currents_grid == 0 ? 'Power Outage or Communication Failure' : '';

                /*$percentage_value = $current_log ? ($current_log->current_generation / $plant->capacity * 100) : 0;
                $result['percentage_value'] = number_format($percentage_value, 2, '.', ',');*/

                $result['daily_generation'] = $daily_processed_data ? number_format($daily_processed_data->dailyGeneration, 2) : '0';
                $result['monthly_generation'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyGeneration, 2) : '0';
                $result['yearly_generation'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlyGeneration, 2) : '0';

                $result['daily_consumption'] = $daily_processed_data ? number_format($daily_processed_data->dailyConsumption, 2) : '0';
                $result['monthly_consumption'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyConsumption, 2) : '0';
                $result['yearly_consumption'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlyConsumption, 2) : '0';

                $result['daily_energy_bought'] = $daily_processed_data ? number_format($daily_processed_data->dailyBoughtEnergy, 2) : '0';
                $result['monthly_energy_bought'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyBoughtEnergy, 2) : '0';

                $result['daily_energy_sell'] = $daily_processed_data ? number_format($daily_processed_data->dailySellEnergy, 2) : '0';
                $result['monthly_energy_sell'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlySellEnergy, 2) : '0';

                $result['daily_revenue'] = $daily_processed_data ? number_format($daily_processed_data->dailySaving, 2) : '0';
                $result['monthly_revenue'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlySaving, 2) : '0';
                $result['yearly_revenue'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlySaving, 2) : '0';
                $result['currency'] = 'PKR';
                $result['id'] = 99999;
                $result['plant_pic'] = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/plant_avatar.png';
            } else {

                return $this->sendResponse(1, 'No data found', $result);
            }
            //$result['last_updated'] = date('h:i A, d/m', strtotime($plant->updated_at));

            if (!empty($result)) {
                return $this->sendResponse(1, 'Showing all data', $result);
            }

            return $this->sendResponse(1, 'No data found', $result);
        } catch (Exception $e) {

            return $this->sendError(0, 'Something went wrong');
        }
    }

    public function allPlants(Request $request)
    {
////        return $request->user()->id;
//        $userID = $request->user()->id;
//        $userPlants = PlantUser::where('user_id', $userID)->select('plant_id')
//            ->get()
//            ->pluck('plant_id')->toArray();
//
//        if (empty($userPlants)) {
//
//            return $this->sendResponse(1, 'No plant found', null);
//        }
//
//        $plants = Plant::whereIn('id', $userPlants)->get();
////        return $this->sendResponse(1, 'Showing all plants', $plants);
//        $plants = $plants->map(function ($plant) {
//            $processed_data = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-m-d 0:00:00'), date('Y-m-d 23:59:00')])->first();
//            //$current_generation = GenerationLog::where('plant_id',$plant->id)->whereBetween('created_at',[date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->orderBy('created_at','desc')->first()->current_generation;
////            print_r($current_generation);
////            exit();
//            $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
//            $plant['system_type'] = SystemType::find($plant->system_type)->type;
//            if ($plant['system_type'] == 4) {
//                $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_discharge_energy', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plant->id)->orderBy('created_at', 'DESC')->first();
//                if ($currentProcessedData) {
//                    $plant['solar_power'] = round((double)$currentProcessedData->dailyGeneration * (double)$currentProcessedData->dailyConsumption / 100, 2) . ' kWh';
//                    $plant['battery_soc'] = StationBattery::where('plant_id', $plant->id)->orderBy('collect_time', 'DESC')->first()['battery_capacity'] . '%';
//                }
//            } else {
//                $plant['solar_power'] = '';
//                $plant['battery_soc'] = '';
//            }
////            $plant['totalPlants'] = $userPlants;
//
//            $current_data = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type', 'battery_type', 'battery_capacity', 'battery_power', 'created_at')->where('plant_id', $plant->id)->orderBy('collect_time', 'desc')->first();
//            $plant['current_generation'] = $current_data ? number_format($current_data->current_generation, 2) : '0';
//            $plant['current_consumption'] = $current_data ? number_format($current_data->current_consumption, 2) : '0';
//            $plant['battery_power'] = $current_data ? number_format($current_data->battery_power, 2) : '0';
//            if ($plant['battery_type']) {
//                $plant['battery_type'] = $current_data ? $current_data->battery_type : '0';
//            } else {
//                $plant['battery_type'] = '0';
//            }
//
////            $plant['current_generation'] = $current_generation;
//            $plant['power'] = isset($processed_data) && isset($processed_data['dailyMaxSolarPower']) ? (string)$processed_data['dailyMaxSolarPower'] : '0';
//            $plant['daily_generation'] = isset($processed_data) && isset($processed_data['dailyGeneration']) ? (string)number_format($processed_data['dailyGeneration'], 2) : '0';
//            $plant['daily_consumption'] = isset($processed_data) && isset($processed_data['dailyConsumption']) ? (string)number_format($processed_data['dailyConsumption'], 2) : '0';
//            $plant['daily_revenue'] = isset($processed_data) && isset($processed_data['dailyGeneration']) ? (string)number_format($processed_data['dailyGeneration'] * $plant->benchmark_price, 2, '.', ',') : '0';
//
//            $plant['last_updated'] = isset($processed_data) && isset($processed_data['lastUpdated']) ? date('h:i A, d/m', strtotime($processed_data['lastUpdated'])) : date('h:i A, d/m');
//            $percentage_value = ($plant['current_generation'] / $plant->capacity * 100);
//
//            $plant['percentage_value'] = number_format($percentage_value, 2, '.', ',');
//
//            if ($plant->plant_pic != null) {
//
//                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/' . $plant->plant_pic;
//            } else {
//
//                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/plant_avatar.png';
//            }
////            $plant['currency'] = DailyProcessedPlantDetail::where('plant_id',$plant->id)
//
//            return $plant;
//
//            // $plant['progress_bar'] = '1000 PKR';
//            // $plant['percentage'] = '7%';
//            // $plant['plant_efficiency'] = 50;
//        });
//        $plantTime = Plant::Select('updated_at')->latest()->first();
//        $time = date('H:i A', strtotime($plantTime->updated_at));
//        $date = date('d', strtotime($plantTime->updated_at)) . '/' . date('m', strtotime($plantTime->updated_at));
////        return $userPlants;
//        if (count($userPlants) !== 0) {
//            $power = 0;
//            $totalGeneration = TotalProcessedPlantDetail::whereIn('plant_id', $userPlants)->sum('plant_total_generation');
//            $totalSaving = TotalProcessedPlantDetail::whereIn('plant_id', $userPlants)->sum('plant_total_saving');
//            $dailyGeneration = DailyProcessedPlantDetail::whereIn('plant_id', $userPlants)->whereBetween('created_at', [date('Y-m-d 0:00:00'), date('Y-m-d 23:59:00')])->sum('dailyGeneration');
//            $monthlyGeneration = MonthlyProcessedPlantDetail::whereIn('plant_id', $userPlants)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('monthlyGeneration');
//            $yearlyGeneration = YearlyProcessedPlantDetail::whereIn('plant_id', $userPlants)->whereYear('created_at', date('Y'))->sum('yearlyGeneration');
//            $dailySaving = DailyProcessedPlantDetail::whereIn('plant_id', $userPlants)->whereDate('created_at', date('Y-m-d'))->sum('dailySaving');
//            $monthlySaving = MonthlyProcessedPlantDetail::whereIn('plant_id', $userPlants)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('monthlySaving');;
//            $yearlySaving = YearlyProcessedPlantDetail::whereIn('plant_id', $userPlants)->whereYear('created_at', date('Y'))->sum('yearlySaving');
//            $croneId = ProcessedCurrentVariable::whereIn('plant_id', $userPlants)->max('processed_cron_job_id');
//            $capacity = Plant::whereIn('id', $userPlants)->sum('capacity');
//            $userPlantsData['totalGeneration'] = $totalGeneration ? number_format($totalGeneration, 2) : '0';
//            $userPlantsData['totalSaving'] = $totalSaving ? number_format($totalSaving, 2) : '0';
//            $userPlantsData['dailyGeneration'] = $dailyGeneration ? number_format($dailyGeneration, 2) : '0';
//            $userPlantsData['monthlyGeneration'] = $monthlyGeneration ? number_format($monthlyGeneration, 2) : '0';
//            $userPlantsData['yearlyGeneration'] = $yearlyGeneration ? number_format($yearlyGeneration, 2) : '0';
//            $userPlantsData['dailySaving'] = $dailySaving ? number_format($dailySaving, 2) : '0';
//            $userPlantsData['monthlySaving'] = $monthlySaving ? number_format($monthlySaving, 2) : '0';
//            $userPlantsData['yearlySaving'] = $yearlySaving ? number_format($yearlySaving, 2) : '0';
//            $userPlantsData['numberOfOnlines'] = Plant::whereIn('id', $userPlants)->whereDate('created_at', date('Y-m-d'))->where('is_online', 'Y')->sum('is_online');
//            $userPlantsData['numberOfOfflines'] = Plant::whereIn('id', $userPlants)->whereDate('created_at', date('Y-m-d'))->where('is_online', 'N')->sum('is_online');
//            $userPlantsData['numberOfFaults'] = Plant::whereIn('id', $userPlants)->whereDate('created_at', date('Y-m-d'))->where('faultLevel', 1)->sum('faultLevel');
//
//            $userPlantsData['capacity'] = $capacity ? number_format($capacity, 2) : '0';
//            $userPlantsData['currency'] = 'PKR';
//            $userPlantsData['lastUpdated'] = $time . ',' . $date;
//            if (Plant::where('id', $userPlants[0])->whereDate('created_at', date('Y-m-d'))->exists()) {
//                $userPlantsData['lastUpdated'] = Plant::where('id', $userPlants[0])->whereDate('created_at', date('Y-m-d'))->latest()->first()['updated_at'];
//            }
//            $benchMarkPrice = Plant::whereIn('id', $userPlants)->whereDate('created_at', date('Y-m-d'))->sum('benchmark_price');
//            $totalExpectedGeneration = 0;
//            for ($i = 0; $i < count($userPlants); $i++) {
//                if (Plant::where('id', $userPlants[$i])->exists()) {
//                    $plantData = Plant::where('id', $userPlants[$i])->first();
//                    $dailyPlantData = DailyProcessedPlantDetail::where('plant_id', $userPlants[$i])->whereDate('created_at', date('Y-m-d'))->sum('dailyGeneration');
//                    $totalExpectedGeneration += (double)$dailyPlantData * (double)$plantData['benchmark_price'];
//                }
//                $plantCurrentDataLogTime = ProcessedCurrentVariable::where('plant_id', $userPlants[$i])->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $userPlants[$i])->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
//                $plantFinalCurrentDataDateTime = $this->previousTenMinutesDateTime($plantCurrentDataLogTime);
//                $power += ProcessedCurrentVariable::where('plant_id', $userPlants[$i])->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $userPlants[$i])->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC')->first()->current_generation : '0';
//            }
//            $userPlantsData['Power'] = $power ? number_format($power, 2) : '0';
//
//            $userPlantsData['totalPlants'] = count($userPlants);
//            $userPlantsData['plantsExpectedGeneration'] = $totalExpectedGeneration ? number_format($totalExpectedGeneration, 2) : '0';
//            if ((double)$totalSaving != 0 && (double)$totalExpectedGeneration != 0) {
//                $todayPercentage = ((double)$userPlantsData['dailySaving'] / (double)$totalExpectedGeneration) * 100;
//                $userPlantsData['todayPercentage'] = $todayPercentage ? number_format($todayPercentage, 2) : 0;
//            }
//        }
//        $result = ['plantList' => $plants, 'dashboard' => $userPlantsData];
//
//        return $this->sendResponse(1, 'Showing all plants', $result);
        $userID = $request->user()->id;
        $userPlants = PlantUser::where('user_id', $userID)->select('plant_id')
            ->get()
            ->pluck('plant_id')->toArray();

        $plants = Plant::whereIn('id', $userPlants)->get();
//        return $this->sendResponse(1, 'Showing all plants', $plants);
        $plants = $plants->map(function ($plant) {
            $processed_data = DailyProcessedPlantDetail::where('plant_id',$plant->id)->whereBetween('created_at',[date('Y-m-d 0:00:00'),date('Y-m-d 23:59:00')])->first();
            //$current_generation = GenerationLog::where('plant_id',$plant->id)->whereBetween('created_at',[date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->orderBy('created_at','desc')->first()->current_generation;
//            print_r($current_generation);
//            exit();
            $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
            $plant['system_type'] = SystemType::find($plant->system_type)->type;
            if ($plant['system_type'] == 4) {
                $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_discharge_energy', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plant->id)->orderBy('created_at', 'DESC')->first();
                if ($currentProcessedData) {
                    $plant['solar_power'] = round((double)$currentProcessedData->dailyGeneration * (double)$currentProcessedData->dailyConsumption / 100, 2) . ' kWh';
                    $plant['battery_soc'] = StationBattery::where('plant_id', $plant->id)->orderBy('collect_time', 'DESC')->first()['battery_capacity'] . '%';
                }
            } else {
                $plant['solar_power'] = '';
                $plant['battery_soc'] = '';
            }

            $current_data  = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type','collect_time','created_at')->where('plant_id', $plant->id)->orderBy('collect_time','desc')->first();
            $plant['current_generation'] = $current_data ? number_format($current_data->current_generation,2) : 0;
            $plant['current_consumption'] = $current_data ? number_format($current_data->current_consumption,2) : 0;
            $plant['battery_power'] = $current_data ? number_format($current_data->battery_power, 2) : '0';
            if ($plant['battery_type']) {
                $plant['battery_type'] = $current_data ? $current_data->battery_type : '0';
            } else {
                $plant['battery_type'] = '0';
            }

//            $plant['current_generation'] = $current_generation;
            $plant['power'] = isset($processed_data) && isset($processed_data['dailyMaxSolarPower']) ? (string)$processed_data['dailyMaxSolarPower'] : '0';


//            $plant['current_generation'] = $current_generation;
            $plant['power'] = $processed_data['dailyMaxSolarPower'].' kW';
            $plant['daily_generation'] = number_format($processed_data['dailyGeneration'],2);
            $plant['daily_revenue'] = number_format($processed_data['dailyGeneration'] * $plant->benchmark_price,2, '.', ',');

            $plant['last_updated'] = date('h:i A, d/m', strtotime($processed_data['lastUpdated']));
            $percentage_value = ($plant['current_generation'] / $plant->capacity * 100);

            $plant['percentage_value'] = number_format($percentage_value, 2, '.', ',');

            if($plant->plant_pic != null) {

                $plant->plant_pic = $this->url_scheme.'://'.$this->domain.'/public/plant_photo/'.$plant->plant_pic;
            }
            else {

                $plant->plant_pic = $this->url_scheme.'://'.$this->domain.'/public/plant_photo/plant_avatar.png';
            }

            return $plant;

            // $plant['progress_bar'] = '1000 PKR';
            // $plant['percentage'] = '7%';
            // $plant['plant_efficiency'] = 50;
        });

        return $this->sendResponse(1, 'Showing all plants', $plants);
    }

    public function plantDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'plant_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }
        $plantID = $request->get('plant_id');
        $userID = $request->user()->id;
        // $response = $this->plant_site_data($plantID);

        // $plant = Plant::where('id', $plantID)->first();
        $plants = Plant::with(['logger'])
            ->where('id', $plantID)->get();
        // print_r(count($plants));exit;
        if (count($plants) == 0) {
            return $this->sendError(0, 'Plant not found.');
        }
//        $plants[0]->created_at = date('d-m-y', strtotime($plants[0]->created_at));
//        return $plants[0]->created_at;

        $plants = $plants->map(function ($plant) {
//            $plant->created_at = date('d-m-Y', strtotime($plant->created_at));

            $daily_processed_data = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-m-d 0:00:00'), date('Y-m-d 23:59:00')])->first();
            $dailyTimeArray = [];
            if (!$daily_processed_data) {
                $daily_processed_data = [];
            }
            $dataArray = json_decode(json_encode($daily_processed_data), true);
//            if ($dataArray) {
//                $plantDailyOutagesVoltageSum = array_sum(array_column($dataArray, 'daily_outage_grid_voltage'));
//                $outagesHours = date('H:i', strtotime($plantDailyOutagesVoltageSum));
//            } else {
//                $outagesHours = '00:00';
//            }
            for ($i = 0; $i < count($dataArray); $i++) {
                if ($dataArray[$i]['daily_outage_grid_voltage']) {
                    $dailyOutagesHoursData = explode(':', $dataArray[$i]['daily_outage_grid_voltage']);
                    $dailyTimeArray[] = $dailyOutagesHoursData[0] . ':' . $dailyOutagesHoursData[1] . ':00';
                }
            }
            $totalSeconds = 0;
            foreach ($dailyTimeArray as $t) {
                $totalSeconds += $this->toSeconds($t);
            }

            $dailyOutagesGridValue = $this->toTimeCalculation($totalSeconds);
//                return $dailyOutagesGridValue;
            $explodeDailyData = explode(':', $dailyOutagesGridValue);
            if ($explodeDailyData[0] == 0) {
                $explodeDailyData[0] = '00';
            }
            if ($explodeDailyData[1] == 0) {
                $explodeDailyData[1] = '00';
            }
            $outagesHours = $explodeDailyData[0] . ':' . $explodeDailyData[1];
            $plant['outages_served'] = ['outagesHours' => $outagesHours];
            if (!empty($daily_data)) {
                $daily_generation = $daily_data ? (double)$daily_data->dailyGeneration : 0;
                $daily_consumption = $daily_data ? (double)$daily_data->dailyConsumption : 0;
                $daily_grid = $daily_data ? (double)$daily_data->dailyGridPower : 0;
                $daily_bought_energy = $daily_data ? (double)$daily_data->dailyBoughtEnergy : 0;
                $daily_sell_energy = $daily_data ? (double)$daily_data->dailySellEnergy : 0;
                $daily_saving = $daily_data ? (double)$daily_data->dailySaving : 0;
                $daily_charge_energy = $daily_data ? (double)$daily_data->daily_charge_energy : 0;
                $daily_discharge_energy = $daily_data ? (double)$daily_data->daily_discharge_energy : 0;
                $peak_hours_savings = $daily_data ? (double)$daily_data->daily_peak_hours_battery_discharge * (int)$plant->peak_teriff_rate : 0;
                $generation_saving = $daily_data ? (double)$daily_data->dailyGeneration * (int)$plant->benchmark_price : 0;
                $totalCostSaving = $daily_data ? (double)$daily_data->dailySaving : 0;
//                $daily['date'] = $daily_data ? $daily_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
            } else {
                $daily_generation = 0;
                $daily_consumption = 0;
                $daily_grid = 0;
                $daily_bought_energy = 0;
                $daily_sell_energy = 0;
                $daily_saving = 0;
                $daily_charge_energy = 0;
                $daily_discharge_energy = 0;
                $peak_hours_savings = 0;
                $generation_saving = 0;
                $totalCostSaving = 0;
//                $daily['date'] = date('Y-m-d H:i:s');
            }
            $daily_charge_arr = $this->unitConversion($daily_charge_energy, 'kWh');
            $daily_discharge_arr = $this->unitConversion($daily_discharge_energy, 'kWh');
            $daily['dailyPeakHoursSaving'] = round($peak_hours_savings, 2);
//        return $daily['dailyPeakHoursSaving'];
            $daily['dailyGenerationSaving'] = round($generation_saving, 2);
            $daily['dailyTotalSaving'] = round($daily['dailyPeakHoursSaving'] + $daily['dailyGenerationSaving']);

            $monthly_processed_data = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-m-01 0:00:00'), date('Y-m-31 23:59:00')])->first();
            $monthly_peak_hours_savings = $monthly_processed_data ? (double)$monthly_processed_data->monthly_peak_hours_discharge_energy * (int)$plant->peak_teriff_rate : 0;
            $monthly_generation_saving = $monthly_processed_data ? (double)$monthly_processed_data->monthlyGeneration * (int)$plant->benchmark_price : 0;
            $monthly['monthlyPeakHoursSaving'] = round($monthly_peak_hours_savings, 2);
            $monthly['monthlyGenerationSaving'] = round($monthly_generation_saving, 2);
            $monthly['monthlyTotalSaving'] = round($monthly['monthlyPeakHoursSaving'] + $monthly['monthlyGenerationSaving']);
            $yearly_processed_data = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-01-01 0:00:00'), date('Y-m-d 23:59:00')])->first();
            $yearly_peak_hours_savings = $yearly_processed_data ? (double)$yearly_processed_data->yearly_peak_hours_discharge_energy * (int)$plant->peak_teriff_rate : 0;
            $yearly_generation_saving = $yearly_processed_data ? (double)$yearly_processed_data->yearlyGeneration * (int)$plant->benchmark_price : 0;
            $yearly['yearlyPeakHoursSaving'] = round($yearly_peak_hours_savings, 2);
            $yearly['yearlyGenerationSaving'] = round($yearly_generation_saving, 2);
            $yearly['yearlyTotalCostSaving'] = round($yearly['yearlyPeakHoursSaving'] + $yearly['yearlyGenerationSaving']);
            $daily_expected_generation = (ExpectedGenerationLog::where('plant_id', $plant->id)->orderBy('created_at', 'desc')->first()) ? ExpectedGenerationLog::where('plant_id', $plant->id)->orderBy('created_at', 'desc')->first()->daily_expected_generation : 0;
            $plant['cost_savings'] = ['daily' => $daily, 'monthly' => $monthly, 'yearly' => $yearly, 'total' => $yearly];
            //c-g
            $current_log = GenerationLog::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:00')])->orderBy('created_at', 'desc')->first();
            $plant['company_name'] = Company::where('id', $plant->company_id)->first()->company_name;
            $plant['company_pic'] = Company::where('id', $plant->company_id)->first()->logo;

            $plant['design_capacity'] = $plant->capacity;
            $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
            $plant['system_type'] = SystemType::find($plant->system_type)->type;

            $plant['expected_generation'] = $daily_expected_generation;
            $plant['power'] = $daily_processed_data ? (string)$daily_processed_data['dailyMaxSolarPower'] : '0';
            $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $plant->id)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $plant->id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
            $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
            $current_data = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type', 'comm_failed', 'battery_type', 'battery_power', 'battery_capacity', 'created_at')->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $finalCurrentDataDateTime)->where('plant_id', $plant->id)->orderBy('collect_time', 'desc')->first();
            $plant['battery_power'] = $current_data ? number_format($current_data->battery_power, 2) : '0';
            if ($plant['battery_type']) {
                $plant['battery_type'] = $current_data ? $current_data->battery_type : '0';
            } else {
                $plant['battery_type'] = '0';
            }
            $batteryValues = StationBattery::where('plant_id', $plant->id)->orderBy('collect_time', 'desc')->first();
//            $batteryRemaining = isset($batteryValues) ? (double)$batteryValues['battery_remaining'] : 0;
            $batteryData = StationBatteryData::where('plant_id', $plant->id)->latest()->first();
            $plantBatteryAh = isset($batteryData) ? (int)$batteryData['battery_ah'] : 0;
            $plantBatteryVoltage = isset($batteryData) ? (double)$batteryData['battery_voltage'] : 0;
            $batteryDOD = isset($batteryData) ? (int)$batteryData['battery_dod'] : 0;
            $batteryCapacity = isset($batteryValues) ? (int)$batteryValues['battery_capacity'] : 0;
            $batteryRatedPower = isset($batteryValues) ? (int)$batteryValues['rated_power'] : 0;
//        return $plant['battery_dod'] / 100;
            $batteryRemainingFormula = (($plantBatteryAh * $plantBatteryVoltage * ($batteryDOD / 100)) / 1000) * ($batteryCapacity / 100);
            $batteryRemaining = round($batteryRemainingFormula, 2);
            $inverterConsumption = isset($batteryValues) ? $batteryValues['inverter_real_time_consumption'] : 0;
            $inverterConsumptionData = $this->unitConversion($inverterConsumption, 'W');


            if ($inverterConsumptionData[0] == 0) {
                $inverterConsumptionData[0] = 1;
            }
            $inverterRatedPower = $this->unitConversion($batteryRatedPower, 'W');
            if ($inverterRatedPower[0] == 0) {
                $inverterRatedPower[0] = 1;
            }
            if ($inverterConsumption == 0) {
                $batteryBackup = $batteryRemaining;
            } else {
                $batteryBackup = ($batteryRemaining / ($inverterConsumption / 1000));
            }
//        return [$batteryRemaining,$batteryBackup,$inverterConsumption];
//        return $batteryRemaining;
            if ($batteryRatedPower == 0) {
                $batteryBackupMaxLoad = ($batteryRemaining / (0.9));
            } else {
                $batteryBackupMaxLoad = ($batteryRemaining / (0.9 * ($batteryRatedPower / 1000)));
            }
//        return $inverterConsumptionData[0];
            $batteryBackupFormula = round($batteryBackup, 2);
            $batteryBackupMaxLoadFormula = round($batteryBackupMaxLoad, 2);


//            return $current_data;
            $plant['current_generation'] = $current_data ? number_format((double)$current_data->current_generation, 2) : '0';
            $plant['current_consumption'] = $current_data ? number_format((double)$current_data->current_consumption, 2) : '0';
//            return
            if ($plant['current_consumption'] < 30) {
                $batteryBackupFormula = 'No Load State';
            }
            $plant['battery_information'] = ['batter-remaining' => $batteryRemaining, 'battery_back_up_current_load' => $batteryBackupFormula, 'battery_backup_max_load' => $batteryBackupMaxLoadFormula];

            $plant['current_gird_import_power'] = $current_data ? number_format((double)$current_data->current_grid, 2) : '0';
            $plant['current_gird_export_power'] = $current_data ? number_format((double)$current_data->current_grid, 2) : '0';
            $plant['current_grid_type'] = $current_data ? $current_data->grid_type : '';
            $plant['comm_fail'] = $current_data ? ($current_data->comm_failed == 1 ? 'Power Outage or Communication Failure' : '') : '';


            $percentage_value = $current_log ? ($current_log->current_generation / $plant->capacity * 100) : 0;
            $plant['percentage_value'] = number_format($percentage_value, 2);

//                $plant['current_generation'] = $current_log->current_generation;
            $plant['daily_generation'] = $daily_processed_data ? number_format($daily_processed_data->dailyGeneration, 2) : '0';
            $plant['monthly_generation'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyGeneration, 2) : '0';
            $plant['yearly_generation'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlyGeneration, 2) : '0';

//                $plant['current_consumption'] = $current_log->current_consumption;
            $plant['daily_consumption'] = $daily_processed_data ? number_format($daily_processed_data->dailyConsumption, 2) : '0';
            $plant['monthly_consumption'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyConsumption, 2) : '0';
            $plant['yearly_consumption'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlyConsumption, 2) : '0';

//                $plant['current_gird_import_power'] = $current_log->current_grid;
//                $plant['current_gird_export_power'] = $current_log->current_grid;

            $plant['daily_energy_bought'] = $daily_processed_data ? number_format($daily_processed_data->dailyBoughtEnergy, 2) : '0';
            $plant['monthly_energy_bought'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyBoughtEnergy, 2) : '0';

            $plant['daily_energy_sell'] = $daily_processed_data ? number_format($daily_processed_data->dailySellEnergy, 2) : '0';
            $plant['monthly_energy_sell'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlySellEnergy, 2) : '0';

            $plant['daily_revenue'] = $daily_processed_data ? number_format($daily_processed_data->dailySaving, 2) : '0';
            $plant['monthly_revenue'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlySaving, 2) : '0';
            $plant['yearly_revenue'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlySaving, 2) : '0';

            $plant['last_updated'] = date('h:i A, d/m', strtotime($plant->updated_at));

//                $plant['total_processed_generation'] = TotalProcessedPlantDetail::where('plant_id',$plant->id)->sum('plant_total_generation');


            $minus_3_hours = date('Y-m-d H:i:s', strtotime('-3 hours', strtotime(date('Y-m-d H:i:s'))));
            $weather = Weather::where('city', $plant->city)->whereBetween('created_at', [$minus_3_hours, date('Y-m-d H:i:s')])->first();

            if (isset($weather) && $weather && isset($weather->sunrise)) {

                $sunrise = explode(':', $weather->sunrise);
                $sunrise_hour = $sunrise[0];
                $sunrise_min = $sunrise[1];
                $sunrise_am = $sunrise[2];
            }

            if (isset($weather) && $weather && isset($weather->sunset)) {

                $sunset = explode(':', $weather->sunset);
                $sunset_hour = $sunset[0];
                $sunset_min = $sunset[1];
                $sunset_am = $sunset[2];
            }

            $plant['icon'] = $weather ? 'http://openweathermap.org/img/w/' . $weather->icon . '.png' : '';
            $plant['temperature'] = $weather ? $weather->temperature : '--';
            $plant['sunset'] = $weather ? $sunset_hour . ':' . $sunset_min . ' ' . $sunset_am : '--';
            $plant['sunrise'] = $weather ? $sunrise_hour . ':' . $sunrise_min . ' ' . $sunrise_am : '--';
            $plant['condition'] = $weather ? $weather->condition : '--';

//                $plant['inverters'] =[];

            $pl_sites_array = PlantSite::where('plant_id', $plant->id)->pluck('site_id')->toArray();
            $inverters = InverterSerialNo::whereIn('site_id', $pl_sites_array)->get();
//            $inverters = InverterSerialNo::where('plant_id',$plant->id)->get();
//            $plant['inverters'] = $inverters->map(function ($inverter);
            for ($k = 0; $k < count($inverters); $k++) {
                if (gettype($inverters[$k]['plant_id']) == 'integer') {
                    $inverters[$k]['plant_id'] = (string)$inverters[$k]['plant_id'];
                }
                $inverters[$k]['inverter_type_id'] = (string)$inverters[$k]['inverter_type_id'];
            }
            $inverterTotalACOutputPower = 0;
            $plant['inverters'] = $inverters->map(function ($inverter) {

//                $daily_inverter_data = DailyInverterDetail::where('plant_id', $inverter->plant_id)->where('siteId', $inverter->siteId)->where('dv_inverter', $inverter->dv_inverter)->whereDate('created_at', date('Y-m-d'))->sum('daily_generation');
//                $monthly_inverter_data = MonthlyInverterDetail::where('plant_id', $inverter->plant_id)->where('siteId', $inverter->siteId)->where('dv_inverter', $inverter->dv_inverter)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('monthly_generation');
//                $yearly_inverter_data = YearlyInverterDetail::where('plant_id', $inverter->plant_id)->where('siteId', $inverter->siteId)->where('dv_inverter', $inverter->dv_inverter)->whereYear('created_at', date('Y'))->sum('yearly_generation');
//                $inverter_detail = InverterDetail::Select('inverterPower','lastUpdated')->where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->orderBy('created_at', 'desc')->first();

                $daily_inverter_data = DailyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('created_at', date('Y-m-d'))->sum('daily_generation');
                $monthly_inverter_data = MonthlyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('monthly_generation');
                $yearly_inverter_data = YearlyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereYear('created_at', date('Y'))->sum('yearly_generation');
                $pvVoltage1 = InverterMPPTDetail::where('site_id', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->where('mppt_number', 1)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
                $pvCurrent1 = InverterMPPTDetail::where('site_id', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->where('mppt_number', 1)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
                $pvVoltage2 = InverterMPPTDetail::where('site_id', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->where('mppt_number', 2)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
                $pvCurrent2 = InverterMPPTDetail::where('site_id', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->where('mppt_number', 2)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
                $pvVoltage3 = InverterMPPTDetail::where('site_id', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->where('mppt_number', 3)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
                $pvCurrent3 = InverterMPPTDetail::where('site_id', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->where('mppt_number', 3)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
                $totalGeneration = YearlyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->sum('yearly_generation');
                $inverter['serial_no'] = $inverter->dv_inverter_serial_no != null ? $inverter->dv_inverter_serial_no : '';
                $inverter['daily_generation'] = number_format($daily_inverter_data, 2);
                $inverter['monthly_generation'] = number_format($monthly_inverter_data, 2);
                $inverter['annual_generation'] = number_format($yearly_inverter_data, 2);
                $inverter['total_generation'] = number_format($totalGeneration, 2);
                $inverterCurrentDataLogTime = InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->exists() ? InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');

                $inverterFinalCurrentDataDateTime = $this->previousTenMinutesDateTime($inverterCurrentDataLogTime);
                $inverterDetailObject = InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $inverterFinalCurrentDataDateTime)->orderBy('collect_time', 'DESC')->first();
                if ($inverterDetailObject) {
                    $inverter['ac_output_power'] = (string)$inverterDetailObject->inverterPower;
                } else {
                    $inverter['ac_output_power'] = '0';
                }
                //                $inverterTotalACOutputPower += $inverterDetailObject && $inverterDetailObject->inverterPower ? $inverterDetailObject->inverterPower : 0;
//                $invertersOutputPowerConverted = $this->unitConversion($inverterTotalACOutputPower, 'kW');
                $inverter['r_voltage1'] = $inverterDetailObject && isset($inverterDetailObject->phase_voltage_r) ? (string)round($inverterDetailObject->phase_voltage_r, 2) : (string)0;
                $inverter['r_voltage2'] = $inverterDetailObject && isset($inverterDetailObject->phase_voltage_s) ? (string)round($inverterDetailObject->phase_voltage_s, 2) : (string)0;
                $inverter['r_voltage3'] = $inverterDetailObject && isset($inverterDetailObject->phase_voltage_t) ? (string)round($inverterDetailObject->phase_voltage_t, 2) : (string)0;
                $inverter['r_current1'] = $inverterDetailObject && isset($inverterDetailObject->phase_current_r) ? (string)round($inverterDetailObject->phase_current_r, 2) : (string)0;
                $inverter['r_current2'] = $inverterDetailObject && isset($inverterDetailObject->phase_current_s) ? (string)round($inverterDetailObject->phase_current_s, 2) : (string)0;
                $inverter['r_current3'] = $inverterDetailObject && isset($inverterDetailObject->phase_current_t) ? (string)round($inverterDetailObject->phase_current_t, 2) : (string)0;
                $inverter ['frequency'] = $inverterDetailObject && isset($inverterDetailObject->frequency) ? (string)round($inverterDetailObject->frequency, 2) : (string)0;
                $inverter ['l_voltage1'] = $pvVoltage1 && isset($pvVoltage1->mppt_voltage) ? (string)round($pvVoltage1->mppt_voltage, 2) : (string)0;
                $inverter ['l_current1'] = $pvCurrent1 && isset($pvCurrent1->mppt_current) ? (string)round($pvCurrent1->mppt_current, 2) : (string)0;
                $inverter ['l_voltage2'] = $pvVoltage2 && isset($pvVoltage2->mppt_voltage) ? (string)round($pvVoltage2->mppt_voltage, 2) : (string)0;
                $inverter ['l_current2'] = $pvCurrent2 && isset($pvCurrent2->mppt_current) ? (string)round($pvCurrent2->mppt_current, 2) : (string)0;
                $inverter ['l_voltage3'] = $pvVoltage3 && isset($pvVoltage3->mppt_power) ? (string)round($pvVoltage3->mppt_power, 2) : (string)0;
                $inverter ['l_current3'] = $pvCurrent3 && isset($pvCurrent3->mppt_power) ? (string)round($pvCurrent3->mppt_power, 2) : (string)0;

                return $inverter;
            });

            if ($plant->plant_pic != null) {

                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/' . $plant->plant_pic;
            }

            if ($plant->company_pic != null) {

                $plant->company_pic = $this->url_scheme . '://' . $this->domain . '/public/company_logo/' . $plant->company_pic;
            }

            $plant->created_at = date('d-m-y', strtotime($plant->created_at));

            return $plant;
        });

        if ($plants) {
            return $this->sendResponse(1, 'Showing plant details', $plants);
        } else {
            return $this->sendError(0, 'Sorry! Saltech API\'s error.', $plants);
        }

    }

    public function plant_chart(Request $request)
    {
        $input = $request->all();
        $plantsID = array();
        $plantsIDD = array();
        $companyID = array();
        $userID = $request->user()->id;

        if ($request->get('company_id')) {

            if ($request->get('company_id') == 'all') {

                $companyID = UserCompany::where('user_id', $userID)->pluck('company_id')->toArray();
            } else {

                foreach (explode(',', $request->get('company_id')) as $id) {

                    $companyID[] = (int)$id;
                }
            }
        }

        if ($request->get('plant_id')) {

            if ($request->get('plant_id') == 'all') {

                $plantsID = PlantUser::where('user_id', $userID)->pluck('plant_id')->toArray();
            } else {

                foreach (explode(',', $request->get('plant_id')) as $id) {

                    $plantsID[] = (int)$id;
                }
            }
        }

        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->pluck('id')->toArray();

        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;
        }

        if (empty($plantsIDD)) {

            return $this->sendResponse(1, 'No data found', null);
        }

        $plantsData = Plant::whereIn('id', $plantsIDD);

        if ($request->get('plant_type') && $request->get('plant_type') != 'all' && $request->get('plant_type') != '') {

            $plantsData->where('plant_type', $request->get('plant_type'));
        }
        if ($request->get('province') && $request->get('province') != 'all' && $request->get('province') != '') {

            $plantsData->where('province', $request->get('province'));
        }
        if ($request->get('city') && $request->get('city') != 'all' && $request->get('city') != '') {

            $plantsData->where('city', $request->get('city'));
        }

        $plant_id = $plantsData->pluck('id')->toArray();

        $duration = ucfirst($input['time']);
        $parameter = ucfirst($input['parameter']);
        //$plant_id = $input['plant_id'];

        $plants = Plant::whereIn('id', $plant_id)->get();
        $dates = strtotime($input['date']);
        $date = '';
        if ($duration == 'Daily') {
            $date = date('Y-m-d', $dates);
        } else if ($duration == 'Monthly') {
            $date = date('Y-m', $dates);
        } else if ($duration == 'Yearly') {
            $date = $request->date;
        }

        if (count($plants) == 0) {
            return $this->sendError(0, 'Plant not found.');
        }

        if ($duration == 'Daily') {
            // return $this->sendResponse(1, $duration.' '.$parameter, $this->getDailyData($plant_id,$parameter));
            $result = $this->getDailyData($plant_id, $parameter, $date);
//            return $result;
            $response = [
                'status' => 1,
                'message' => $duration . ' ' . $parameter,
                //'daily' => $result[0],
                'result' => $result[0],
                'total' => round($result[1], 2)
            ];
            return response()->json($response, 200);
        } else if ($duration == 'Weekly') {
            return $this->sendResponse(1, $duration . ' ' . $parameter, $this->getWeeklyData($plant_id, $parameter));
        } else if ($duration == 'Monthly') {

            $res = $this->getMonthlyData($plant_id, $parameter, $date);

            $response = [
                'status' => 1,
                'message' => $duration . ' ' . $parameter,
                'result' => $res[0],
                'total' => round($res[1], 2)
            ];
            return response()->json($response, 200);

        } else if ($duration == 'Yearly') {

            $res = $this->getYearlyData($plant_id, $parameter, $date);

            $response = [
                'status' => 1,
                'message' => $duration . ' ' . $parameter,
                'result' => $res[0],
                'total' => round($res[1], 2)
            ];
            return response()->json($response, 200);
        }
    }

    private function getDailyData($plant_id, $parameter, $date)
    {
//        return $date;

        $hourly_data = array();
        $daily_total = 0;
        $plant_detail = Plant::whereIn('id', $plant_id)->get(['id', 'meter_type', 'benchmark_price']);
        $benchmark_price = count($plant_detail) > 0 ? $plant_detail[0]->benchmark_price : 0;
        $meter_type = count($plant_detail) > 0 ? $plant_detail[0]->meter_type : '';

        if (strtolower($parameter) == 'generation') {

            $current_generation_start_time = ProcessedCurrentVariable::select('created_at')->where('plant_id', $plant_id)->whereDate('collect_time', $date)->where('current_generation', '>', 0)->orderBy('created_at', 'DESC')->first();
            $start_date_time = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->created_at)) : '06:00:00';

            // $hourly_val  = ProcessedCurrentVariable::where('plant_id',$plant_id)->whereBetween('created_at', [date($date.' '.$start_date_time),date($date.' 23:59:00')])->get();
            $daily_total = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->sum('dailyGeneration');
//            date($minTimeSmartInverter . ' 00:00:00')
            $hourly_val = ProcessedCurrentVariable::Select('collect_time')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:00')])->groupBy('collect_time')->get();
//            return $hourly_val;
//            count($hourly_val) > 0 ? $hourly_val : "0";
            foreach ($hourly_val as $key => $value) {
                $current_gen['time'] = date('H:i', strtotime($value->collect_time));
                $current_gen['value'] = number_format(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_generation'), 2);
                array_push($hourly_data, $current_gen);
            }

        } else if (strtolower($parameter) == 'consumption') {

            $current_generation_start_time = ProcessedCurrentVariable::select('created_at')->where('plant_id', $plant_id)->whereDate('created_at', $date)->where('current_generation', '>', 0)->orderBy('created_at', 'ASC')->first();
            $start_date_time = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->created_at)) : '06:00:00';

            // $hourly_val  = ProcessedCurrentVariable::where('plant_id',$plant_id)->whereBetween('created_at', [date($date.' '.$start_date_time),date($date.' 23:59:00')])->get();
            $daily_total = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->sum('dailyConsumption');

            $hourly_val = ProcessedCurrentVariable::select('collect_time')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:00')])->groupBy('collect_time')->get();

            count($hourly_val) > 0 ? $hourly_val : "0";
            foreach ($hourly_val as $key => $value) {
                $current_gen['time'] = date('H:i', strtotime($value->collect_time));
                $current_gen['value'] = number_format(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_consumption'), 2);
                array_push($hourly_data, $current_gen);
            }

        } else if (strtolower($parameter) == 'buy_energy') {
            $hourly_data = $this->get_graph_buy_energy_daily_data($plant_id, $date);

            $daily_total = DailyProcessedPlantDetail::where('plant_id', $plant_id)->whereDate('created_at', $date)->sum('dailyBoughtEnergy');

        } else if (strtolower($parameter) == 'sell_energy') {
            $hourly_data = $this->get_graph_sell_energy_daily_data($plant_id, $date);

            $daily_total = DailyProcessedPlantDetail::where('plant_id', $plant_id)->whereDate('created_at', $date)->sum('dailySellEnergy');

        } else if (strtolower($parameter) == 'saving') {

            $current_generation_start_time = ProcessedCurrentVariable::select('collect_time')->where('plant_id', $plant_id)->whereDate('collect_time', $date)->where('current_generation', '>', 0)->orderBy('collect_time', 'ASC')->first();
            $start_date_time = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->created_at)) : '06:00:00';

            // $hourly_val  = ProcessedCurrentVariable::where('plant_id',$plant_id)->whereBetween('created_at', [date($date.' '.$start_date_time),date($date.' 23:59:00')])->get();
            $daily_total = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->sum('dailySaving');

            $hourly_val = ProcessedCurrentVariable::select('collect_time')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:00')])->groupBy('collect_time')->get();

            count($hourly_val) > 0 ? $hourly_val : "0";
            foreach ($hourly_val as $key => $value) {
                $current_gen['time'] = date('H:i', strtotime($value->collect_time));
                $current_gen['value'] = number_format(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_saving'), 2);
                array_push($hourly_data, $current_gen);
            }
        }

        return [$hourly_data, $daily_total];
    }


    private function get_graph_buy_energy_daily_data($plant_id, $date)
    {
        $hourly_data = array();

        $current_generation_start_time = ProcessedCurrentVariable::select('created_at')->whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->where('grid_type', '+ve')->where('current_grid', '>', 0)->orderBy('created_at', 'ASC')->first();
        $start_date = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->created_at)) : '06:00:00';

        $current_generation = ProcessedCurrentVariable::select('created_at')->where('plant_id', $plant_id[0])->whereBetween('created_at', [date($date . ' ' . $start_date), date($date . ' 23:59:59')])->groupBy('created_at')->get();

        foreach ($current_generation as $key => $today_log) {

            $today_log_data = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('grid_type', '+ve')->where('created_at', $today_log->created_at)->sum('current_grid');

            $current_gen['value'] = (string)round($today_log_data, 2);
            $current_gen['time'] = date('H:i', strtotime($today_log->created_at));
            array_push($hourly_data, $current_gen);
        }

        return $hourly_data;
    }

    private function get_graph_sell_energy_daily_data($plant_id, $date)
    {
        $hourly_data = array();

        $current_generation_start_time = ProcessedCurrentVariable::select('created_at')->whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->where('grid_type', '+ve')->where('current_grid', '>', 0)->orderBy('created_at', 'ASC')->first();
        $start_date = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->created_at)) : '06:00:00';

        $current_generation = ProcessedCurrentVariable::select('created_at')->where('plant_id', $plant_id[0])->whereBetween('created_at', [date($date . ' ' . $start_date), date($date . ' 23:59:59')])->groupBy('created_at')->get();

        foreach ($current_generation as $key => $today_log) {

            $today_log_data = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('grid_type', '-ve')->where('created_at', $today_log->created_at)->sum('current_grid');

            $current_gen['value'] = (string)round($today_log_data, 2);
            $current_gen['time'] = date('H:i', strtotime($today_log->created_at));
            array_push($hourly_data, $current_gen);
        }

        return $hourly_data;
    }

    public function round_off_data($value)
    {
        $number = (float)$value;
        return number_format($number, 2);
    }

    private function getWeeklyData($plant_id, $parameter)
    {
        $weekly_data = array();
        //Carbon::setWeekStartsAt(Carbon::FRIDAY);
        $start = Carbon::now()->startOfWeek();
        for ($i = 1; $i <= Carbon::now()->dayOfWeek + 1; $i++) {
            $d = $i;
            $sdate = date('Y-m-d 00:00:00', strtotime($start . "+" . $d . "Days"));
            $endate = date('Y-m-d 23:59:00', strtotime($start . "+" . $d . "Days"));
            if ($parameter == 'Generation') {
                $weekly = DailyProcessedPlantDetail::where('plant_id', $plant_id)->whereBetween('created_at', [$sdate, $endate])->sum('dailyGeneration');
            } else if ($parameter == 'Consumption') {
                $weekly = DailyProcessedPlantDetail::where('plant_id', $plant_id)->whereBetween('created_at', [$sdate, $endate])->sum('dailyConsumption');
            } else if ($parameter == 'Grid') {
                $weekly = DailyProcessedPlantDetail::where('plant_id', $plant_id)->whereBetween('created_at', [$sdate, $endate])->sum('dailyGridPower');
            }
            $weekly == 0 ? $weekly = "0" : $weekly;
            array_push($weekly_data, $weekly);
        }
        return $weekly_data;
    }

    private function getMonthlyData($plant_id, $parameter, $date)
    {
        $plant_detail = Plant::where('id', $plant_id)->get(['meter_type', 'benchmark_price']);
        $benchmark_price = count($plant_detail) > 0 ? $plant_detail[0]->benchmark_price : 0;
        $explode_data = explode('-', $date);
        $mon = $explode_data[1];
        $yer = $explode_data[0];
        $monthly_data = array();
        $monthly_total = 0;
        $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);
        for ($i = 1; $i <= $dd; $i++) {

            if ($i < 10) {
                $i = '0' . $i;
            }
            $daily = 0;
            if (strtolower($parameter) == 'generation') {
                $daily = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyGeneration');
                $monthly_total = MonthlyProcessedPlantDetail::where('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGeneration');
            } else if (strtolower($parameter) == 'consumption') {
                $daily = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyConsumption');
                $monthly_total = MonthlyProcessedPlantDetail::where('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyConsumption');
            } else if (strtolower($parameter) == 'grid') {
                $daily = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyGridPower');
                $monthly_total = MonthlyProcessedPlantDetail::where('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGridPower');
            } else if (strtolower($parameter) == 'buy_energy') {
                $daily = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyBoughtEnergy');
                $monthly_total = MonthlyProcessedPlantDetail::where('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyBoughtEnergy');
            } else if (strtolower($parameter) == 'sell_energy') {
                $daily = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailySellEnergy');
                $monthly_total = MonthlyProcessedPlantDetail::where('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlySellEnergy');
            } else if (strtolower($parameter) == 'saving') {
                $daily = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailySaving');
                //$daily= $daily > 0 ? round(($daily * $benchmark_price), 2) : '0' ;
                $monthly_total = MonthlyProcessedPlantDetail::where('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlySaving');
            }

            $array_data = array('time' => (string)$i, 'value' => ($daily > 0) ? number_format($daily, 2) : '0');
            array_push($monthly_data, $array_data);
        }

        if (strtolower($parameter) == 'generation') {
            $monthly_total = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGeneration');
        } else if (strtolower($parameter) == 'consumption') {
            $monthly_total = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyConsumption');
        } else if (strtolower($parameter) == 'grid') {
            $monthly_total = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGridPower');
        } else if (strtolower($parameter) == 'buy_energy') {
            $monthly_total = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyBoughtEnergy');
        } else if (strtolower($parameter) == 'sell_energy') {
            $monthly_total = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlySellEnergy');
        } else if (strtolower($parameter) == 'saving') {
            $monthly_total = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlySaving');
        }


        return [$monthly_data, $monthly_total];
    }

    private function getYearlyData($plant_id, $parameter, $date)
    {
        $monthly_data = array();
        $yearly_total = 0;
        $plant_detail = Plant::where('id', $plant_id)->get(['meter_type', 'benchmark_price']);
        $benchmark_price = count($plant_detail) > 0 ? $plant_detail[0]->benchmark_price : 0;

        for ($i = 1; $i <= 12; $i++) {
            if ($i < 10) {
                $i = '0' . $i;
            }

            if (strtolower($parameter) == 'generation') {
                $monthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGeneration');
            } else if (strtolower($parameter) == 'consumption') {
                $monthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyConsumption');
            } else if (strtolower($parameter) == 'grid') {
                $monthly = MonthlyProcessedPlantDetail::whereIN('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGridPower');
            } else if (strtolower($parameter) == 'buy_energy') {
                $monthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyBoughtEnergy');
            } else if (strtolower($parameter) == 'sell_energy') {
                $monthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySellEnergy');
            } else if (strtolower($parameter) == 'saving') {
                $monthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySaving');
            }
            $array_data = array('time' => (string)$i, 'value' => number_format($monthly, 2));
            array_push($monthly_data, $array_data);
        }

        if (strtolower($parameter) == 'generation') {
            $yearly_total = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->sum('yearlyGeneration');
        } else if (strtolower($parameter) == 'consumption') {
            $yearly_total = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->sum('yearlyConsumption');
        } else if (strtolower($parameter) == 'grid') {
            $yearly_total = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->sum('yearlyGridPower');
        } else if (strtolower($parameter) == 'buy_energy') {
            $yearly_total = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->sum('yearlyBoughtEnergy');
        } else if (strtolower($parameter) == 'sell_energy') {
            $yearly_total = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->sum('yearlySellEnergy');
        } else if (strtolower($parameter) == 'saving') {
            $yearly_total = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->sum('yearlySaving');
        }

        return [$monthly_data, $yearly_total];
    }

    public function plantDetails_backup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plant_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }
        $plantID = $request->get('plant_id');
        $userID = $request->user()->id;
        $response = $this->plant_site_data($plantID);

        $userPlants = PlantUser::select('plant_id')
            ->where('user_id', $userID)
            ->where('plant_id', $plantID)
            ->get()
            ->pluck('plant_id')->toArray();

        $plants = Plant::with(['inverters', 'logger'])
            ->whereIn('id', $userPlants)->get();

        $plants = $plants->map(function ($plant) {
            $siteId = $plant->siteId;
            $inverter_detail = DB::table("inverter_details")->select('inverter1Power', 'lastUpdated')->where('siteId', $siteId)->orderBy('created_at', 'desc')->first();
            $plant['ac_output_power'] = $inverter_detail->inverter1Power;

            $daily_generation = DB::table("inverter_details")->select(DB::raw("SUM(inverter1Energy) as daily_gen"))->where('siteId', $siteId)->whereDate('created_at', date('Y-m-d'))->get();
            $plant['daily_generation'] = number_format($daily_generation[0]->daily_gen, 2, '.', ',');
            $monthly_generation = DB::table("inverter_details")->select(DB::raw("SUM(inverter1Energy) as monthly_gen"))->where('siteId', $siteId)->whereMonth('created_at', date('m'))->get();
            $plant['monthly_generation'] = number_format($monthly_generation[0]->monthly_gen, 2, '.', ',');
            $annual_generation = DB::table("inverter_details")->select(DB::raw("SUM(inverter1Energy) as annual_gen"))->where('siteId', $siteId)->whereYear('created_at', date('Y'))->get();
            $plant['annual_generation'] = number_format($annual_generation[0]->annual_gen, 2, '.', ',');
            $total_generation = DB::table("inverter_details")->select(DB::raw("SUM(inverter1Energy) as total_gen"))->where('siteId', $siteId)->get();
            $plant['total_generation'] = number_format($total_generation[0]->total_gen, 2, '.', ',');

            $plant['last_updated'] = $inverter_detail->lastUpdated;
            return $plant;
        });
        // echo 'dasdas';exit;

        $plants = $this->plantAdditionalFields($plants);
        if ($response == true) {
            return $this->sendResponse(1, 'Showing plant details', $plants);
        } else {
            return $this->sendError(0, 'Sorry! Saltech API\'s error.', $plants);
        }

    }

    public function addPlant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serial_no' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $serialNo = $request->get('serial_no');

        $plantValidation = Plant::where('serial_no', $serialNo)->first();

        if (empty($plantValidation)) {
            return $this->sendError(0, "No plant is associated with the given serial no", null);
        }

        $userID = $request->user()->id;

        PlantUser::updateOrCreate(
            [
                "user_id" => $userID,
                "plant_id" => $plantValidation->id
            ],
            [
                "user_id" => $userID,
                "plant_id" => $plantValidation->id
            ]
        );

        $plantDetails = Plant::with(['inverters', 'logger'])
            ->where('id', $plantValidation->id)->get();

        $plants = $this->plantAdditionalFields($plantDetails);

        return $this->sendResponse(1, 'Plant associated successfully', $plants);
    }

    public function plant_chart13(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plant_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }
        $plantID = $request->get('plant_id');
        $userID = $request->user()->id;
        $plants = Plant::where('id', $plantID)->first();
        $siteId = $plants->siteId;

        $plant_collections = PlantDetail::groupBy('created_at')
            ->selectRaw('SUM(totalInverterPower) as totalInverterPower,created_at')
            ->where('created_at', '>=', DB::raw('DATE(NOW()) - INTERVAL 365 DAY'))
            ->orderBy('created_at', 'desc')
            ->get()->toArray();

        foreach ($plant_collections as $key => $plant_collection) {
            $plant_collections[$key]['type'] = 'generation';
        }

        $inverter_collections = PlantDetail::groupBy('created_at')
            ->selectRaw('SUM(totalLoadPower) as totalLoadPower,created_at')
            ->where('created_at', '>=', DB::raw('DATE(NOW()) - INTERVAL 365 DAY'))
            ->orderBy('created_at', 'desc')
            ->get()->toArray();
        foreach ($inverter_collections as $key => $inverter_collection) {
            $inverter_collections[$key]['type'] = 'consumption';
        }

        $chart_data = array_merge($plant_collections, $inverter_collections);
        // dd($chart_data);
        return $this->sendResponse(1, 'Showing Daily Chart', $chart_data);
    }

    public function plantAdditionalFields($plants)
    {
        $plants = $plants->map(function ($plant) {

            $siteId = $plant->siteId;
            $plant_detail = DB::table("plant_details")->select('totalInverterPower', 'totalLoadPower', 'totalGridApparentPower', 'totalGridPower', 'lastUpdated')->where('siteId', $siteId)->orderBy('created_at', 'desc')->first();
            $processed_plant_detail = DB::table("processed_plant_detail")->where('siteId', $siteId)->orderBy('created_at', 'desc')->first();

            $plant['creator'] = 'Kyle Stanley';
            $plant['installer'] = 'Viper';
            $plant['distributor'] = 'Alex Garret';
            $plant['last_updated'] = $plant_detail->lastUpdated;
            $plant['plant_efficiency'] = 50;

            $summary = array();
            $summary['current_generation'] = number_format($plant_detail->totalInverterPower, 2, '.', ',');
            // $summary['daily_generation'] = number_format($processed_plant_detail->dailySolarEnergy, 2, '.', ',');
            /*$summary['monthly_generation'] = number_format($processed_plant_detail->monthlySolarEnergy, 2, '.', ',');
            $summary['annual_generation'] = number_format($processed_plant_detail->yearlySolarEnergy, 2, '.', ',');*/

            $summary['current_consumption'] = number_format($plant_detail->totalLoadPower, 2, '.', ',');
            // $summary['daily_consumption'] = number_format($processed_plant_detail->dailyLoadEnergy, 2, '.', ',');
            // dd($summary);
            /*$summary['monthly_consumption'] = number_format($processed_plant_detail->monthlyLoadEnergy, 2, '.', ',');
            $summary['annual_consumption'] = number_format($processed_plant_detail->yearlyLoadEnergy, 2, '.', ',');*/

            $summary['current_grid_export_power'] = $plant_detail->totalGridApparentPower;
            $summary['current_grid_import_power'] = $plant_detail->totalGridPower;

            // $summary['daily_energy_bought'] = number_format($processed_plant_detail->dailyBoughtEnergy, 2, '.', ',');
            // $summary['monthly_energy_bought'] = number_format($processed_plant_detail->monthlyBoughtEnergy, 2, '.', ',');

            // $summary['daily_energy_sell'] = number_format($processed_plant_detail->dailySoldEnergy, 2, '.', ',');
            // $summary['monthly_energy_sell'] = number_format($processed_plant_detail->monthlySoldEnergy, 2, '.', ',');

            $summary['current_self_use_rate'] = '';//number_format($processed_plant_detail->dailyConsumedEnergy, 2, '.', ',');
            $summary['daily_self_use_rate'] = '';//number_format($processed_plant_detail->dailyConsumedEnergy, 2, '.', ',');
            $summary['monthly_self_use_rate'] = '';//number_format($processed_plant_detail->monthlyConsumedEnergy, 2, '.', ',');

            $summary['daily_revenue'] = '5.33';
            $summary['monthly_revenue'] = '10.5';
            $summary['annual_revenue'] = '10.5';

            $plant['summary'] = $summary;
            return $plant;
        });

        return $plants;
    }

    /*public function plantExpectedActualChart(Request $request) {

        $input = $request->all();
        $duration=  ucfirst($input['time']);
        $plant_id = $input['plant_id'];

        $plants = Plant::where('id', $plant_id)->get();
        $date='';
        $date = $request->date;

        if(count($plants) == 0){
            return $this->sendError(0, 'Plant not found.');
        }
        if($duration == 'Yearly'){
            $result = $this->getExpectedActualYearlyData($plant_id,$date);

            $data = [
                'actual_generation' => $result[0],
                'expected_generation' => $result[1],
            ];

            $response = [
                'status' => 1,
                'message' => $duration.' Data',
                'result' => $data,
                'total_actual_generation' => $result[2],
                'total_expected_generation' => $result[3]
            ];
            return response()->json($response, 200);
        }
    }*/

    /*private function getExpectedActualYearlyData($plant_id,$date){

        if($request->get('plant_id') && $request->get('plant_id') != 'all' && $request->get('plant_id') != '') {

            foreach(explode(',', $request->get('plant_id')) as $id) {

                $plants_id[] = (int)$id;
            }
        }

        else {

            $plants_id = PlantUser::where('user_id', $userID)->pluck('plant_id')->toArray();
        }

        for($i = 1; $i <= 12; $i++) {

            if($i < 10) {
                $i = '0'.$i;
            }

            $arr_sum=[];

            $dd = cal_days_in_month(CAL_GREGORIAN,$i,$date);

            $today_log_data_sum = MonthlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGeneration');

            $today_log_data[] = (object) [
                "time" => (string)$i,
                "value" => $today_log_data_sum > 0 ? round($today_log_data_sum, 2) : 0,
            ];

            foreach($plants_id as $pl_id) {

                for($j = 1; $j <= $dd; $j++) {

                    if($j < 10) {
                        $j = '0'.$j;
                    }

                    $yesterday_log_data_sum = ExpectedGenerationLog::where('plant_id', $pl_id)->where('created_at','<=', $date.'-'.$i.'-'.$j.' 23:59:59')->orderBy('created_at', 'DESC')->first();
                    $arr_sum[] = $yesterday_log_data_sum ? ($yesterday_log_data_sum->daily_expected_generation) : 0;
                }
            }

            $yesterday_log_data[] = (object) [
                "time" => (string)$j,
                "value" => array_sum($arr_sum) ? round(array_sum($arr_sum), 2) : 0,
            ];

        }

        $total_actual_generation = YearlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->whereYear('created_at', $date)->sum('yearlyGeneration');

        $sum_data = 0;

        foreach($yesterday_log_data as $key=>$value){
            if(isset($value->value))
            $sum_data += $value->value;
        }

        $total_actual_generation = round($total_actual_generation, 2);
        $total_expected_generation = round($sum_data, 2);

        return [$today_log_data, $yesterday_log_data, $total_actual_generation, $total_expected_generation];
    }*/

    public function plantExpectedActualChart(Request $request)
    {

        $input = $request->all();
        $plantsID = array();
        $plantsIDD = array();
        $companyID = array();
        $userID = $request->user()->id;

        if ($request->get('company_id')) {

            if ($request->get('company_id') == 'all') {

                $companyID = UserCompany::where('user_id', $userID)->pluck('company_id')->toArray();
            } else {

                foreach (explode(',', $request->get('company_id')) as $id) {

                    $companyID[] = (int)$id;
                }
            }
        }

        if ($request->get('plant_id')) {

            if ($request->get('plant_id') == 'all') {

                $plantsID = PlantUser::where('user_id', $userID)->pluck('plant_id')->toArray();
            } else {

                foreach (explode(',', $request->get('plant_id')) as $id) {

                    $plantsID[] = (int)$id;
                }
            }
        }

        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->pluck('id')->toArray();

        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;
        }

        if (empty($plantsIDD)) {

            return $this->sendResponse(1, 'No data found', null);
        }

        $plantsData = Plant::whereIn('id', $plantsIDD);

        if ($request->get('plant_type') && $request->get('plant_type') != 'all' && $request->get('plant_type') != '') {

            $plantsData->where('plant_type', $request->get('plant_type'));
        }
        if ($request->get('province') && $request->get('province') != 'all' && $request->get('province') != '') {

            $plantsData->where('province', $request->get('province'));
        }
        if ($request->get('city') && $request->get('city') != 'all' && $request->get('city') != '') {

            $plantsData->where('city', $request->get('city'));
        }

        $plant_id = $plantsData->pluck('id')->toArray();

        $duration = ucfirst($input['time']);

        $plants = Plant::whereIn('id', $plant_id)->get();
        $dates = strtotime($input['date']);
        $date = '';
        if ($duration == 'Daily') {
            $date = date('Y-m-d', $dates);
        } else if ($duration == 'Monthly') {
            $date = date('Y-m', $dates);
        } else if ($duration == 'Yearly') {
            $date = $request->date;
        }

        if (count($plants) == 0) {
            return $this->sendError(0, 'Plant not found.');
        }

        if ($duration == 'Daily') {

            $dailyGene = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->sum('dailyGeneration');

            foreach ($plant_id as $pl_id) {

                $yesterday_log_data_sum = ExpectedGenerationLog::where('plant_id', $pl_id)->whereDate('created_at', '<=', $date)->orderBy('created_at', 'DESC')->first();
                $arr_sum[] = $yesterday_log_data_sum ? $yesterday_log_data_sum->daily_expected_generation : 0;
            }

            $ExpGene = array_sum($arr_sum);

            if ($ExpGene == 0 || $dailyGene == 0) {

                $actual_percentage = 0;
            } else {

                $actual_percentage = ((double)$dailyGene / (double)$ExpGene) * 100;
            }

            $response = [
                'status' => 1,
                'message' => $duration,
                'time' => $duration,
                'date' => $date,
                'actual' => (string)round($dailyGene, 2),
                'expected' => (string)round($ExpGene, 2),
                'percentage' => (string)round($actual_percentage, 2)
            ];

            return response()->json($response, 200);

        } else if ($duration == 'Monthly') {

            $monthlyGene = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGeneration');

            $arr_sum = [];

            $explode_data = explode('-', $date);
            $mon = $explode_data[1];
            $yer = $explode_data[0];

            $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);

            for ($i = 1; $i <= $dd; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                foreach ($plant_id as $pl_id) {

                    $yesterday_log_data_sum = ExpectedGenerationLog::where('plant_id', $pl_id)->where('created_at', '<=', $date . '-' . $i . ' 23:59:59')->orderBy('created_at', 'DESC')->first();
                    $arr_sum[] = $yesterday_log_data_sum ? $yesterday_log_data_sum->daily_expected_generation : 0;
                }
            }

            $ExpGene = array_sum($arr_sum);

            if ($ExpGene == 0 || $monthlyGene == 0) {

                $actual_percentage = 0;
            } else {

                $actual_percentage = ((double)$monthlyGene / (double)$ExpGene) * 100;
            }

            $response = [
                'status' => 1,
                'message' => $duration,
                'time' => $duration,
                'date' => $date,
                'actual' => (string)round($monthlyGene, 2),
                'expected' => (string)round($ExpGene, 2),
                'percentage' => (string)round($actual_percentage, 2)
            ];

            return response()->json($response, 200);

        } else if ($duration == 'Yearly') {

            $yearlyGene = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('yearlyGeneration');

            $arr_sum = [];

            for ($i = 1; $i <= 12; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $dd = cal_days_in_month(CAL_GREGORIAN, $i, $date);

                foreach ($plant_id as $pl_id) {

                    for ($j = 1; $j <= $dd; $j++) {

                        if ($j < 10) {
                            $j = '0' . $j;
                        }

                        $yesterday_log_data_sum = ExpectedGenerationLog::where('plant_id', $pl_id)->where('created_at', '<=', $date . '-' . $i . '-' . $j . ' 23:59:59')->orderBy('created_at', 'DESC')->first();
                        $arr_sum[] = $yesterday_log_data_sum ? ($yesterday_log_data_sum->daily_expected_generation) : 0;
                    }
                }
            }

            $ExpGene = array_sum($arr_sum);

            if ($ExpGene == 0 || $yearlyGene == 0) {

                $actual_percentage = 0;
            } else {

                $actual_percentage = ((double)$yearlyGene / (double)$ExpGene) * 100;
            }

            $response = [
                'status' => 1,
                'message' => $duration,
                'time' => $duration,
                'date' => $date,
                'actual' => (string)round($yearlyGene, 2),
                'expected' => (string)round($ExpGene, 2),
                'percentage' => (string)round($actual_percentage, 2)
            ];

            return response()->json($response, 200);

        }
    }

    public function plantEnvironmentChart(Request $request)
    {

        $input = $request->all();
        $userID = $request->user()->id;

        $envPlanting = Setting::where('perimeter', 'env_planting')->first()->value;
        $envReduction = Setting::where('perimeter', 'env_reduction')->first()->value;

        $plantsID = array();
        $plantsIDD = array();
        $companyID = array();

        if ($request->get('company_id')) {

            if ($request->get('company_id') == 'all') {

                $companyID = UserCompany::where('user_id', $userID)->pluck('company_id')->toArray();
            } else {

                foreach (explode(',', $request->get('company_id')) as $id) {

                    $companyID[] = (int)$id;
                }
            }
        }

        if ($request->get('plant_id')) {

            if ($request->get('plant_id') == 'all') {

                $plantsID = PlantUser::where('user_id', $userID)->pluck('plant_id')->toArray();
            } else {

                foreach (explode(',', $request->get('plant_id')) as $id) {

                    $plantsID[] = (int)$id;
                }
            }
        }

        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->pluck('id')->toArray();

        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;
        }

        if (empty($plantsIDD)) {

            return $this->sendResponse(1, 'No data found', null);
        }

        $plantsData = Plant::whereIn('id', $plantsIDD);

        if ($request->get('plant_type') && $request->get('plant_type') != 'all' && $request->get('plant_type') != '') {

            $plantsData->where('plant_type', $request->get('plant_type'));
        }
        if ($request->get('province') && $request->get('province') != 'all' && $request->get('province') != '') {

            $plantsData->where('province', $request->get('province'));
        }
        if ($request->get('city') && $request->get('city') != 'all' && $request->get('city') != '') {

            $plantsData->where('city', $request->get('city'));
        }

        $plant_id = $plantsData->pluck('id')->toArray();

        $duration = ucfirst($input['time']);

        $plants = Plant::whereIn('id', $plant_id)->get();
        $dates = strtotime($input['date']);
        $date = '';
        if ($duration == 'Daily') {
            $date = date('Y-m-d', $dates);
        } else if ($duration == 'Monthly') {
            $date = date('Y-m', $dates);
        } else if ($duration == 'Yearly') {
            $date = $request->date;
        }

        if (count($plants) == 0) {
            return $this->sendError(0, 'Plant not found.');
        }

        if ($duration == 'Daily') {

            $dailyGene = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->sum('dailyGeneration');

            $dailyPlanting = $dailyGene && $envPlanting ? $dailyGene * (double)$envPlanting : 0;
            $dailyReduction = $dailyGene && $envReduction ? $dailyGene * (double)$envReduction : 0;

            $response = [
                'status' => 1,
                'message' => $duration,
                'time' => $duration,
                'date' => $date,
                'planting' => (string)round($dailyPlanting, 2),
                'reduction' => (string)round($dailyReduction, 2),
                'total' => (string)round($dailyGene, 2)
            ];

            return response()->json($response, 200);

        } else if ($duration == 'Monthly') {

            $monthlyGene = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGeneration');

            $monthlyPlanting = $monthlyGene && $envPlanting ? $monthlyGene * (double)$envPlanting : 0;
            $monthlyReduction = $monthlyGene && $envReduction ? $monthlyGene * (double)$envReduction : 0;

            $response = [
                'status' => 1,
                'message' => $duration,
                'time' => $duration,
                'date' => $date,
                'planting' => (string)round($monthlyPlanting, 2),
                'reduction' => (string)round($monthlyReduction, 2),
                'total' => (string)round($monthlyGene, 2)
            ];

            return response()->json($response, 200);

        } else if ($duration == 'Yearly') {

            $yearlyGene = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->sum('yearlyGeneration');

            $yearlyPlanting = $yearlyGene && $envPlanting ? $yearlyGene * (double)$envPlanting : 0;
            $yearlyReduction = $yearlyGene && $envReduction ? $yearlyGene * (double)$envReduction : 0;

            $response = [
                'status' => 1,
                'message' => $duration,
                'time' => $duration,
                'date' => $date,
                'planting' => (string)round($yearlyPlanting, 2),
                'reduction' => (string)round($yearlyReduction, 2),
                'total' => (string)round($yearlyGene, 2)
            ];

            return response()->json($response, 200);

        }
    }

    public function previousTenMinutesDateTime($date)
    {

        $currentDataDateTime = new \DateTime($date);
        $currentDataDateTime->modify('-10 minutes');
        $finalCurrentDataDateTime = $currentDataDateTime->format('Y-m-d H:i:s');

        return $finalCurrentDataDateTime;
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

    function toSeconds($time)
    {
        $parts = explode(':', $time);
        return 3600 * $parts[0] + 60 * $parts[1] + $parts[2];
    }

    function toTimeCalculation($seconds)
    {
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        return $hours . ':' . $minutes . ':' . $seconds;
    }

    public function outagesServed(Request $request)
    {
        date_default_timezone_set('Asia/Karachi');
        $validator = Validator::make($request->all(), [
            'plant_id' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, $validator->errors()->first());
        }
        $timeArray = ['day','month','year'];
        if(!in_array($request->time,$timeArray))
        {
            return $this->sendError(0, 'Please enter a valid time!');
        }
        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plant_id;
//        $plantMeterType = $request->plantMeterType;
        $plantHistoryGraphYAxis = [];

        if ($time == 'day') {
            $date = date('Y-m-d', $requestDate);
        } else if ($time == 'month') {
            $date = date('Y-m', $requestDate);
        } else if ($time == 'year') {
            $date = $request->date;
        }
        $data = [];

        $historyArray = json_decode($request->historyCheckBoxArray);
        $historyArray = (array)$historyArray;
//        return json_encode(in_array("soc", $historyArray));
//return json_encode($plantHistoryGraphYAxis);
        $outagesHours = 0;
//        $data = [];
//        $todayLogDataSum
//        $bought
//        $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_charge_energy', 'daily_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', date('Y-m-d'))->whereTime('created_at', '>=', $peakStartDate)->whereTime('created_at', '<=', $peakEndDate)->get();
        $currentProcessedData = DailyProcessedPlantDetail::select('daily_outage_grid_voltage', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
//        return $currentProcessedData;
        if ($currentProcessedData) {
            $outagesHours = date('H:i', strtotime($currentProcessedData->daily_outage_grid_voltage));
        }
        if ($time == 'day') {
            if($outagesHours == 0)
            {
                $outagesHours = '00:00';
            }
            $data = ['outagesHours' => $outagesHours];
        } else if ($time == 'month') {
            $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
            $monthlyOutagesHours = 0;
            if ($monthlyProcessedData) {
                $monthlyOutagesHoursData = explode(':', $monthlyProcessedData->monthly_outage_grid_voltage);
//                return $monthlyOutagesHoursData;
                $monthlyOutagesHours = $monthlyOutagesHoursData[0] . ':' . $monthlyOutagesHoursData[1];

            }
            if($monthlyOutagesHours == 0)
            {
                $monthlyOutagesHours = '00:00';
            }
            $data = ['outagesHours' => $monthlyOutagesHours];

        } else {
            $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_outage_grid_voltage', 'created_at')->where('plant_id', $plantID)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
            $yearlyOutagesHours = 0;
            if ($yearlyProcessedData) {
                $yearlyOutagesHoursData = explode(':', $yearlyProcessedData->yearly_outage_grid_voltage);
                $yearlyOutagesHours = $yearlyOutagesHoursData[0] . ':' . $yearlyOutagesHoursData[1];
            }
            if($yearlyOutagesHours == 0)
            {
                $yearlyOutagesHours = '00:00';
            }
            $data = ['outagesHours' => $yearlyOutagesHours];

        }
        return ['status' => 1,'data' => $data];
    }
     public function consumptionInPeakHours(Request $request)
     {
         date_default_timezone_set('Asia/Karachi');
         $validator = Validator::make($request->all(), [
             'plant_id' => 'required',
             'date' => 'required',
             'time' => 'required',
         ]);

         if ($validator->fails()) {
             return $this->sendError(0, $validator->errors()->first());
         }
         $timeArray = ['day','month','year'];
         if(!in_array($request->time,$timeArray))
         {
             return $this->sendError(1, 'Please enter a valid time!');
         }

         $time = $request->time;
         $requestDate = strtotime($request->date);
         $plantID = $request->plant_id;
//        $plantMeterType = $request->plantMeterType;
         $plantHistoryGraphYAxis = [];

         if ($time == 'day') {
             $date = date('Y-m-d', $requestDate);
         } else if ($time == 'month') {
             $date = date('Y-m', $requestDate);
         } else if ($time == 'year') {
             $date = $request->date;
         }

         $historyArray = json_decode($request->historyCheckBoxArray);
         $historyArray = (array)$historyArray;
//        return json_encode(in_array("soc", $historyArray));
//return json_encode($plantHistoryGraphYAxis);
         $plantHistoryGraph = [];
         $legendArray = [];
         $todayLogData = [];
         $batteryDischargeEnergy = 0;
         $gridImport = 0;
//        $data = [];
//        $todayLogDataSum
         $current = ['daily_discharge_energy', 'grid_import'];
         $gridImport = 0;
         $dailyDischargeEnergy = 0;
         $consumption = 0;
//        $bought
//        $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_charge_energy', 'daily_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', date('Y-m-d'))->whereTime('created_at', '>=', $peakStartDate)->whereTime('created_at', '<=', $peakEndDate)->get();
         $currentProcessedData = DailyProcessedPlantDetail::select('daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->first();
//        return $currentProcessedData;
         if ($currentProcessedData) {
             $dailyDischargeEnergy = $currentProcessedData->daily_peak_hours_battery_discharge;
             $gridImport = $currentProcessedData->daily_peak_hours_grid_buy;
             $consumption = $currentProcessedData->daily_peak_hours_consumption;
         }
         foreach ($current as $key => $currentData) {

             $todayLogDataSum = array();
             if ($time == 'day') {

                 if ($currentData == 'daily_discharge_energy') {

//                    $graphColor = '#F6A944';
//                    $todayLogData[] = 'Daily Discharge';
//                    $todayLogData[] = ['value' => (double)$dailyDischargeEnergy .'kW', 'name' => 'Daily Discharge:'. (double)$dailyDischargeEnergy .' '.'kW'];
//                    array_push($todayLogData,'Daily Discharge');
                     array_push($todayLogData, ['value' => (double)$dailyDischargeEnergy, 'name' => 'Battery Discharging: ' . (double)$dailyDischargeEnergy . ' ' . 'kWh']);
                     $batteryDischargeEnergy = (double)$dailyDischargeEnergy;
//                    $todayLogDataSum = ['value' => $dailyDischargeEnergy, 'name' => 'Daily Discharge Eneregy'];
                 } else if ($currentData == 'grid_import') {

//                    $graphColor = '#46C1AB';
//                    $todayLogData[] = 'Grid Import';
//                    $todayLogData[] = 'name:' .'Daily Discharge:'. (double)$dailyDischargeEnergy .' '.'kW';
//                    $todayLogData[] = 'Grid Import';
//                    $todayLogData[] = ['value' => (double)$gridImport .'kW', 'name' => 'Daily Discharge:'. (double)$gridImport .' '.'kW'];
                     array_push($todayLogData, ['value' => (double)$gridImport, 'name' => 'Grid Import: ' . (double)$gridImport . ' ' . 'kWh']);
                     $gridImport = (double)$gridImport;
                 }

//                $todayLogData[] = $todayLogDataSum;
//            }
//                return $todayLogDataSum;
             } else if ($time == 'month') {
                 $month = date('Y-m',strtotime($date));
//                 return $month;
                 $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $month . '%')->orderBy('created_at', 'DESC')->first();
                 $monthlyDischargeValue = 0;
                 $monthlyGridValue = 0;
                 if ($monthlyProcessedData) {
                     $monthlyGridValue = $monthlyProcessedData->monthly_peak_hours_grid_import;
                     $monthlyDischargeValue = $monthlyProcessedData->monthly_peak_hours_discharge_energy;
                     $consumption = $monthlyProcessedData->monthly_peak_hours_consumption;
                 }
                 if ($currentData == 'daily_discharge_energy') {

//                    $graphColor = '#F6A944';
                     array_push($todayLogData, ['value' => (double)$monthlyDischargeValue, 'name' => 'Battery Discharging: ' . (double)$monthlyDischargeValue . ' ' . 'kWh']);
                     $batteryDischargeEnergy = (double)$monthlyDischargeValue;
//                    $todayLogDataSum = ['value' => $dailyDischargeEnergy, 'name' => 'Daily Discharge Eneregy'];
                 } else if ($currentData == 'grid_import') {

//                    $graphColor = '#46C1AB';
                     array_push($todayLogData, ['value' => (double)$monthlyGridValue, 'name' => 'Grid Import: ' . (double)$monthlyGridValue . ' ' . 'kWh']);
                     $gridImport = (double)$monthlyGridValue;
                 }

//                $tooltipDate = date('m-Y', strtotime($date));
//
//                $graphType = 'bar';
//
//                $explodeData = explode('-', $date);
//                $mon = $explodeData[1];
//                $yer = $explodeData[0];
//
//                $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);
//
//                for ($i = 1; $i <= $dd; $i++) {
//
//                    if ($i < 10) {
//                        $i = '0' . $i;
//                    }
//
//                    $todayLogTime[] = $i;
//
//                    if ($current == 'generation') {
//
//                        $graphColor = '#F6A944';
//                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailyGeneration : 0;
//                    } else if ($current == 'consumption') {
//
//                        $graphColor = '#46C1AB';
//                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailyConsumption : 0;
//                    } else if ($current == 'grid') {
//
//                        $graphColor = '#E38595';
//                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailyGridPower : 0;
//                    } else if ($current == 'buy') {
//
//                        $graphColor = '#8FC34D';
//                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailyBoughtEnergy : 0;
//                    } else if ($current == 'sell') {
//
//                        $graphColor = '#435EBE';
//                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailySellEnergy : 0;
//                    } else if ($current == 'saving') {
//
//                        $graphColor = '#009FFD';
//                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailySaving : 0;
//                    } else if ($current == 'irradiance') {
//
//                        $graphColor = '#F933C8';
//                        $todayLogDataSum = DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->daily_irradiance : 0;
//                    } else if ($current == 'battery-charge') {
//
//                        $graphColor = '#f2b610';
//                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->daily_charge_energy : 0;
//                    } else if ($current == 'battery-discharge') {
//
//                        $graphColor = '#31bfbf';
//                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->daily_discharge_energy : 0;
//                    }
//
//                    $todayLogData[] = round($todayLogDataSum, 2);
//                }
             } else if ($time == 'year') {
                 $year = date('Y',strtotime('Y'));
                 $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_peak_hours_discharge_energy', 'yearly_peak_hours_grid_import', 'yearly_peak_hours_consumption', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereYear('created_at', $year)->orderBy('created_at', 'DESC')->first();
                 $yearlyDischargeValue = 0;
                 $yearlyGridValue = 0;
                 if ($yearlyProcessedData) {
                     $yearlyGridValue = $yearlyProcessedData->yearly_peak_hours_grid_import;
                     $yearlyDischargeValue = $yearlyProcessedData->yearly_peak_hours_discharge_energy;
                     $consumption = $yearlyProcessedData->yearly_peak_hours_consumption;
                 }
                 if ($currentData == 'daily_discharge_energy') {

//                    $graphColor = '#F6A944';
                     array_push($todayLogData, ['value' => (double)$yearlyDischargeValue, 'name' => 'Battery Discharging: ' . (double)$yearlyDischargeValue . ' ' . 'kWh']);
                     $batteryDischargeEnergy = (double)$yearlyDischargeValue;
//                    $todayLogDataSum = ['value' => $dailyDischargeEnergy, 'name' => 'Daily Discharge Eneregy'];
                 } else if ($currentData == 'grid_import') {

//                    $graphColor = '#46C1AB';
                     array_push($todayLogData, ['value' => (double)$yearlyGridValue, 'name' => 'Grid Import: ' . (double)$yearlyGridValue . ' ' . 'kWh']);
                     $gridImport = (double)$yearlyGridValue;
                 }
             }
         }
         if ($time == 'day') {
//            $t_m . ': ' . round($yearly_gen_arr[0], 2) . ' ' . $yearly_gen_arr[1]
//            $legendArray = ['Battery Discharge' => (double)$dailyDischargeEnergy . 'KW', 'Grid Import' => $gridImport . 'KW'];
             $legendArray[] = 'Battery Discharge' . ': ' . (double)$dailyDischargeEnergy . ' ' . 'KW';
             $legendArray[] = 'Grid Import' . ': ' . (double)$gridImport . ' ' . 'KW';
         } else if ($time == 'month') {
             $legendArray = ['Battery Discharge' => (double)$monthlyDischargeValue . 'KW', 'Grid Import' => $monthlyGridValue . 'KW'];
         } else if ($time == 'year') {
             $legendArray = ['Battery Discharge' => (double)$yearlyDischargeValue . 'KW', 'Grid Import' => $yearlyGridValue . 'KW'];
         }
//         $data = ['legendArray' => $legendArray, 'logData' => $todayLogData, 'batteryDischarge' => $batteryDischargeEnergy, 'gridImport' => $gridImport, 'consumption' => $consumption];
         $data = ['batteryDischarge' => $batteryDischargeEnergy, 'gridImport' => $gridImport, 'consumption' => $consumption];
         return ['status' => 1,'data' => $data];
     }
    public function energySources(Request $request)
    {
        date_default_timezone_set('Asia/Karachi');
        $validator = Validator::make($request->all(), [
            'plant_id' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, $validator->errors()->first());
        }
        $timeArray = ['day','month','year'];
        if(!in_array($request->time,$timeArray))
        {
            return $this->sendError(1, 'Please enter a valid time!');
        }
        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plant_id;
//        $plantMeterType = $request->plantMeterType;
        $plantHistoryGraphYAxis = [];

        if ($time == 'day') {
            $date = date('Y-m-d', $requestDate);
        } else if ($time == 'month') {
            $date = date('Y-m', $requestDate);
        } else if ($time == 'year') {
            $date = $request->date;
        }

        $historyArray = json_decode($request->historyCheckBoxArray);
        $historyArray = (array)$historyArray;
//        return json_encode(in_array("soc", $historyArray));
//return json_encode($plantHistoryGraphYAxis);
        $plantHistoryGraph = [];
        $legendArray = [];
        $todayLogData = [];
        $batteryDischargeEnergy = 0;
//        $data = [];
//        $todayLogDataSum
        $current = ['battery-discharging', 'solar', 'grid_import'];
        $gridImport = 0;
        $solar = 0;
        $batteryDischarging = 0;
        $consumption = 0;
//        $bought
//        $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_charge_energy', 'daily_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', date('Y-m-d'))->whereTime('created_at', '>=', $peakStartDate)->whereTime('created_at', '<=', $peakEndDate)->get();
        $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_discharge_energy', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->first();
//        return $currentProcessedData;
        if ($currentProcessedData) {
            $batteryDischarging = round((double)$currentProcessedData->daily_discharge_energy * (double)$currentProcessedData->dailyConsumption / 100, 2);
            $gridImport = round((double)$currentProcessedData->dailyBoughtEnergy * (double)$currentProcessedData->dailyConsumption / 100, 2);
            $solar = round((double)$currentProcessedData->dailyGeneration * (double)$currentProcessedData->dailyConsumption / 100, 2);
            $consumption = $currentProcessedData->dailyConsumption;
        }
//        $todayLogData = ['battery-discharge' => (double)$batteryDischarging,'solar' => (double)$solar,'grid-import' => (double)$gridImport];
        foreach ($current as $key => $currentData) {

            $todayLogDataSum = array();
            if ($time == 'day') {
                $todayLogData = ['battery-discharge' => (double)$batteryDischarging, 'solar' => (double)$solar, 'grid-import' => (double)$gridImport];

//                $todayLogData[] = $todayLogDataSum;
//            }
//                return $todayLogDataSum;
            } else if ($time == 'month') {
                $month = date('Y-m', strtotime($date));
                $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $month . '%')->orderBy('created_at', 'DESC')->first();
                $monthlyDischargeValue = 0;
                $monthlyGridImportValue = 0;
                $monthlySolar = 0;
                if ($monthlyProcessedData) {
                    $consumption = $monthlyProcessedData->monthlyConsumption;
                    $monthlyDischargeValue = round((double)$monthlyProcessedData->monthly_discharge_energy * (double)$monthlyProcessedData->monthlyConsumption / 100, 2);
                    $monthlyGridImportValue = round((double)$monthlyProcessedData->monthlyBoughtEnergy * (double)$monthlyProcessedData->monthlyConsumption / 100, 2);
                    $monthlySolar = round((double)$monthlyProcessedData->monthlyGeneration * (double)$monthlyProcessedData->monthlyConsumption / 100, 2);
                }
                $todayLogData = ['battery-discharge' => (double)$monthlyDischargeValue, 'solar' => (double)$monthlySolar, 'grid-import' => (double)$monthlyGridImportValue];
            } else if ($time == 'year') {
                $year = date('Y', strtotime($date));

                $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_peak_hours_discharge_energy', 'yearlyGeneration', 'yearly_peak_hours_grid_import', 'yearlyConsumption', 'yearly_peak_hours_consumption', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereYear('created_at', $year)->orderBy('created_at', 'DESC')->first();
                $yearlyDischargeValue = 0;
                $yearlyGridImportValue = 0;
                $yearlySolar = 0;
                if ($yearlyProcessedData) {
                    $yearlyDischargeValue = round((double)$yearlyProcessedData->yearly_discharge_energy * (double)$yearlyProcessedData->yearlyConsumption / 100, 2);
                    $yearlyGridImportValue = round((double)$yearlyProcessedData->yearlyBoughtEnergy * (double)$yearlyProcessedData->yearlyConsumption / 100, 2);
                    $yearlySolar = round((double)$yearlyProcessedData->yearlyGeneration * (double)$yearlyProcessedData->yearlyConsumption / 100, 2);
                    $consumption = $yearlyProcessedData->yearlyConsumption;
                }
                $todayLogData = ['battery-discharge' => (double)$yearlyDischargeValue, 'solar' => (double)$yearlySolar, 'grid-import' => (double)$yearlyGridImportValue];
            }
//
//        $data = ['energy-source' => $consumption, 'logData' => $todayLogData];
            $data = ['status' => 1, 'data' => $todayLogData];
            return $data;

        }
    }
    public function solarEnergyUtilization(Request $request)
    {
        date_default_timezone_set('Asia/Karachi');
        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plant_id;
        $plantHistoryGraphYAxis = [];

        if ($time == 'day') {
            $date = date('Y-m-d', $requestDate);
        } else if ($time == 'month') {
            $date = date('Y-m', $requestDate);
        } else if ($time == 'year') {
            $date = date('Y',$requestDate);
        }
//        return json_encode(in_array("soc", $historyArray));

//return json_encode($plantHistoryGraphYAxis);
        $plantHistoryGraph = [];
        $legendArray = [];
        $generationValue = 0;
        $consumptionValue = 0;
        $buyValue = 0;
        $sellValue = 0;
        $savingValue = 0;
        $graphType = '';
        $tooltipDate = date('Y-m-d');
        $totalGeneration = 0;
        $totalConsumption = 0;
        $totalGrid = 0;
        $totalBuy = 0;
        $totalSell = 0;
        $totalSaving = 0;
        $totalIrradiance = 0;
        $generationDataArray = array();
        $consumptionDataArray = array();
        $gridEnergyDataArray = array();
        $buyEnergyDataArray = array();
        $sellEnergyDataArray = array();
        $irradianceEnergyDataArray = array();
        $savingEnergyDataArray = array();
        $historyArray = ['battery-charge', 'grid-export', 'load'];
//        $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
////        return $currentProcessedData;
//        if ($currentProcessedData) {
//            $dailyChargingEnergy = round(((double)$currentProcessedData->dailyGeneration * (double)$currentProcessedData->daily_charge_energy) / 100, 2);
//            $gridExport = round(((double)$currentProcessedData->dailyGeneration * (double)$currentProcessedData->dailySellEnergy) / 100, 2);
//            $generation = $currentProcessedData->dailyGeneration;
////            $dailyChargingEnergy = $currentProcessedData->daily_charge_energy;
////            $gridExport = $currentProcessedData->dailySellEnergy;
////            $generation = $currentProcessedData->dailyGeneration;
//            $load = round(((double)$currentProcessedData->dailyGeneration * (double)$currentProcessedData->dailyConsumption) / 100, 2);
//        }

        if (strtotime($date) == strtotime(date('Y-m-d'))) {
//            $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
//            $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
//            $currentGeneration = ProcessedCurrentVariable::whereIn('plant_id', $plants_id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC')->where(MINUTE('collect_time') > 54)->orWhere(MINUTE('collect_time') < 6) AND updated_at >= NOW() - INTERVAL 1 DAY;
//            $currentGeneration = ProcessedCurrentVariable::whereIn('plant_id', $plants_id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC')->where(MINUTE('collect_time') > 54)->orWhere(MINUTE('collect_time') < 6) AND updated_at >= NOW() - INTERVAL 1 DAY;
//            $currentGeneration = ProcessedCurrentVariable::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->get()->groupBy(function ($date) {
//                return Carbon::parse($date->collect_time)->format('H');
//            });
//            $batteryData = StationBattery::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->get()->groupBy(function ($date) {
//                return Carbon::parse($date->collect_time)->format('H');
//            });
            $currentGeneration = DB::table('processed_current_variables')
                ->select(DB::raw('hour(collect_time),current_generation as current_generation,current_consumption as current_consumption,current_grid as current_grid,grid_type as grid_type'))->whereDate('collect_time', date('Y-m-d'))
                ->groupBy(DB::raw('hour(collect_time)'))
                ->get();
//            $currentGeneration =  DB::table('processed_current_variables')
//                ->select(DB::raw('HOUR(collect_time) as hour,current_generation as current_generation,current_consumption as current_consumption'))
//                ->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])
//                ->groupBy('hour')
//                ->get();
//            $batteryData = StationBattery::where('plant_id', $plantID)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])->get()->groupBy(function ($date) {
//                return Carbon::parse($date->collect_time)->format('H');
//            });
            $batteryData = DB::table('station_battery')
                ->select(DB::raw('hour(collect_time),daily_charge_energy as daily_charge_energy'))->whereDate('collect_time', date('Y-m-d'))
                ->groupBy(DB::raw('hour(collect_time)'))
                ->get();
//            return $currentGeneration;
//            $batteryData = StationBattery::whereIn('plant_id', $plants_id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC')->groupBy('collect_time')->get();
        } else {

//            $currentGeneration = ProcessedCurrentVariable::whereIn('plant_id', $plants_id)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])->groupBy('collect_time')->get();
//            $batteryData = StationBattery::whereIn('plant_id', $plants_id)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])->groupBy('collect_time')->get();


//            $currentGeneration = ProcessedCurrentVariable::where('plant_id', $plantID)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])->get()->groupBy(function ($date) {
//                return Carbon::parse($date->collect_time)->format('H');
//            });
            $currentGeneration = DB::table('processed_current_variables')
                ->select(DB::raw('hour(collect_time),current_generation as current_generation,current_consumption as current_consumption,current_grid as current_grid,grid_type as grid_type'))->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])
                ->groupBy(DB::raw('hour(collect_time)'))
                ->get();
//            $currentGeneration =  DB::table('processed_current_variables')
//                ->select(DB::raw('HOUR(collect_time) as hour,current_generation as current_generation,current_consumption as current_consumption'))
//                ->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])
//                ->groupBy('hour')
//                ->get();
//            $batteryData = StationBattery::where('plant_id', $plantID)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])->get()->groupBy(function ($date) {
//                return Carbon::parse($date->collect_time)->format('H');
//            });
            $batteryData = DB::table('station_battery')
                ->select(DB::raw('hour(collect_time),daily_charge_energy as daily_charge_energy'))->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])
                ->groupBy(DB::raw('hour(collect_time)'))
                ->get();
        }
//        return $currentGeneration['00'];
//        return [count($currentGeneration),count($batteryData)];

//        return $currentGeneration;
        $finalDataArray = [];
        $batteryDischargeData = 0;
        $gridExportData = 0;
        $batteryDataArray = [];
        $gridDataArray = [];
        $loadDataArray = [];

//        foreach ($historyArray as $key => $current) {

            $todayLogData = [];
            $todayLogTime = [];
            $todayLogDataSum = 0;
            $graphColor = '';


            if ($time == 'day') {

                $tooltipDate = date('d-m-Y', strtotime($date));

                $graphType = 'bar';
                $variablesSum = 0;


                for ($k = 0; $k < count($currentGeneration); $k++) {

                    $dischargeEnergy = 0;
                    $dailyGeneration = 0;
//                    if ($k < 10) {
//                        $k = '0' . $k;
//                    }
//                    $todayLogTime[] = $k . ':00';
                    $dailyGeneration = array_sum(array_column(json_decode(json_encode($currentGeneration[$k]), true), 'current_generation'));

//                    if ($current == 'battery-charge') {
//                        $dischargeEnergy = array_sum(array_column(json_decode(json_encode($batteryData[$k]), true), 'daily_charge_energy'));
                    if(count($batteryData) > 0) {
//                        return json_encode($batteryData[9]);
                        if(isset($batteryData[$k])) {
                            $dailyChargeEnergy = round(((double)$dailyGeneration * (double)$batteryData[$k]->daily_charge_energy) / 100, 2);
                        }
                        else
                        {
                            $dailyChargeEnergy = 0;
                        }
                    }
                    else
                    {
                        $dailyChargeEnergy = 0;
                    }
//                        array_push($batteryDataArray, $dailyChargeEnergy);
//                        $todayLogDataSum = $dailyChargeEnergy;
//                    }
//                    if ($current == 'grid-export') {
//                        $gridImportData = [];
//                        $gridData = json_decode(json_encode($currentGeneration[$k]), true);
//                        return $gridData;
//                        for ($m = 0; $m < count($gridData); $m++) {
//                            if ($currentGeneration[$k]->grid_type == '+ve') {
//                                return $currentGeneration[$k];
//                                array_push($gridImportData, ['gridExport' => $currentGeneration[$k]->current_grid]);
//                            }
//
//                        }
//                        $gridExport = array_sum(array_column(json_decode(json_encode($currentGeneration[$k]), true), 'current_grid'));
                        $dailyGridExport = round(((double)$dailyGeneration * (double)$currentGeneration[$k]->current_grid) / 100, 2);
//                        array_push($gridDataArray, $dailyGridExport);
//                    }
//                    if ($current == 'load') {
//                        $dailyConsumption = array_sum(array_column(json_decode(json_encode($currentGeneration[$k]), true), 'current_consumption'));
                        $load = round((double)$currentGeneration[$k]->current_consumption * 0.01 / 100, 2);
                        array_push($batteryDataArray, ['hour' => $k+1,'battery-charge-energy' => $dailyChargeEnergy,'grid-export' => $dailyGridExport,'load' => $load]);
//                    }
                    $todayLogData[] = round($todayLogDataSum, 2);
                }
            } else if ($time == 'month') {

                $tooltipDate = date('m-Y', strtotime($date));


                $explodeData = explode('-', $date);
                $mon = $explodeData[1];
                $yer = $explodeData[0];

                $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);

                for ($i = 1; $i <= $dd; $i++) {

                    if ($i < 10) {
                        $i = '0' . $i;
                    }

                    $todayLogTime[] = $i;


                        $dailyProcessedDetails = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->get();
                        $batteryProcessedDataArray = [];
                        for ($k = 0; $k < count($dailyProcessedDetails); $k++) {
                            $dailyGeneration = $dailyProcessedDetails[$k]['dailyGeneration'];
                            $dailyChargeEnergyDetail = $dailyProcessedDetails[$k]['daily_charge_energy'];
                            $dailyChargeEnergy = round(((double)$dailyGeneration * (double)$dailyChargeEnergyDetail) / 100, 2);
                            array_push($batteryProcessedDataArray, ['daily-charge-data' => $dailyChargeEnergy]);

                        }
                        $dailyBatteryData = round(array_sum(array_column($batteryProcessedDataArray, 'daily-charge-data')),2);

                        $dailyProcessedDetails = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->get();
                        $gridImportProcessedDataArray = [];
                        for ($k = 0; $k < count($dailyProcessedDetails); $k++) {
                            $dailyGeneration = $dailyProcessedDetails[$k]['dailyGeneration'];
                            $dailySellEnergy = $dailyProcessedDetails[$k]['dailySellEnergy'];
                            $dailySellEnergyData = round(((double)$dailyGeneration * (double)$dailySellEnergy) / 100, 2);
                            array_push($gridImportProcessedDataArray, ['grid-import' => $dailySellEnergyData]);

                        }
                        $dailyGridData = round(array_sum(array_column($gridImportProcessedDataArray, 'grid-import')),2);

                        $dailyProcessedDetails = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->get();
                        $gridImportProcessedDataArray = [];
                        for ($k = 0; $k < count($dailyProcessedDetails); $k++) {
                            $dailyGeneration = $dailyProcessedDetails[$k]['dailyGeneration'];
                            $dailyConsumption = $dailyProcessedDetails[$k]['dailyConsumption'];
                            $dailyLoadEnergyData = round(((double)$dailyGeneration * (double)$dailyConsumption) / 100, 2);
                            array_push($gridImportProcessedDataArray, ['load' => $dailyLoadEnergyData]);

                        }
                        $dailyLoadData = array_sum(array_column($gridImportProcessedDataArray, 'load'));
                        array_push($batteryDataArray, ['hour' => $i,'battery-charge-energy' => $dailyBatteryData,'grid-export' => $dailyGridData,'load' => $dailyLoadData]);
                    }

            } else if ($time == 'year') {

                $tooltipDate = $date;

                $graphType = 'bar';

                for ($i = 1; $i <= 12; $i++) {

                    if ($i < 10) {
                        $i = '0' . $i;
                    }

                    $todayLogTime[] = substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3);

                        $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->get();
                        $batteryProcessedDataArray = [];
                        for ($k = 0; $k < count($monthlyProcessedData); $k++) {
                            $monthlyGeneration = $monthlyProcessedData[$k]['monthlyGeneration'];
                            $monhtlyChargeEnergyDetail = $monthlyProcessedData[$k]['monthly_charge_energy'];
                            $monhtlyChargeEnergy = round(((double)$monthlyGeneration * (double)$monhtlyChargeEnergyDetail) / 100, 2);
                            array_push($batteryProcessedDataArray, ['monthly-charge-energy' => $monhtlyChargeEnergy]);

                        }
                        $monthlyBatteryData = array_sum(array_column($batteryProcessedDataArray, 'monthly-charge-energy'));

                        $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->get();
                        $batteryProcessedDataArray = [];
                        for ($k = 0; $k < count($monthlyProcessedData); $k++) {
                            $monthlyGeneration = $monthlyProcessedData[$k]['monthlyGeneration'];
                            $monhtlyChargeEnergyDetail = $monthlyProcessedData[$k]['monthlySellEnergy'];
                            $monhtlyChargeEnergy = round(((double)$monthlyGeneration * (double)$monhtlyChargeEnergyDetail) / 100, 2);
                            array_push($batteryProcessedDataArray, ['monthly-sell-energy' => $monhtlyChargeEnergy]);

                        }
                        $monthlyGridImport = array_sum(array_column($batteryProcessedDataArray, 'monthly-sell-energy'));
                        $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->get();
                        $batteryProcessedDataArray = [];
                        for ($k = 0; $k < count($monthlyProcessedData); $k++) {
                            $monthlyGeneration = $monthlyProcessedData[$k]['monthlyGeneration'];
                            $monhtlyChargeEnergyDetail = $monthlyProcessedData[$k]['monthlyConsumption'];
                            $monhtlyChargeEnergy = round(((double)$monthlyGeneration * (double)$monhtlyChargeEnergyDetail) / 100, 2);
                            array_push($batteryProcessedDataArray, ['monthly-load-energy' => $monhtlyChargeEnergy]);

                        }
                        $monthlyLoad = array_sum(array_column($batteryProcessedDataArray, 'monthly-load-energy'));
                         array_push($batteryDataArray, ['hour' => $i,'battery-charge-energy' => $monthlyBatteryData,'grid-export' => $monthlyGridImport,'load' => $monthlyLoad]);
                }
            }

//            $plantHistoryGraph[] = $historyObject;
//        }
//        return [$gridDataArray, $batteryDataArray, $loadDataArray];
        $batteryChargeValue = 0;
        $gridExportValue = 0;
        $loadValue = 0;
//        if ($time == 'day') {
//            if ($batteryDataArray) {
//                $batteryChargeValue = end($batteryDataArray);
//            }
//            if ($gridDataArray) {
//                $gridExportValue = end($gridDataArray);
//            }
//            if ($loadDataArray) {
//                $loadValue = end($loadDataArray);
//            }
//        } else if ($time == 'month') {
//            $dateDetail = date('d');
//            if ($batteryDataArray) {
//                $batteryChargeValue = $batteryDataArray[$dateDetail - 1];
//            }
//            if ($gridDataArray) {
//                $gridExportValue = $gridDataArray[$dateDetail - 1];
//            }
//            if ($loadDataArray) {
//                $loadValue = $loadDataArray[$dateDetail - 1];
//            }
//
//        } else if ($time == 'year') {
//            $dateDetail = date('m');
//            if ($batteryDataArray) {
//                $batteryChargeValue = $batteryDataArray[$dateDetail - 1];
//            }
//            if ($gridDataArray) {
//                $gridExportValue = $gridDataArray[$dateDetail - 1];
//            }
//            if ($loadDataArray) {
//                $loadValue = $loadDataArray[$dateDetail - 1];
//            }
//
//        }

//        $data['solar_energy_utilization_graph'] = $plantHistoryGraph;
//        $data['time_type'] = $time;
//        $data['time_details'] = [];
//
//        $data['time_array'] = $todayLogTime;
////            return $data['time_array']
////        return $data['time_details'];
//
//
//        $data['legend_array'] = $legendArray;
//        $data['tooltip_date'] = $tooltipDate;
//        $data['batteryChargeValue'] = $batteryChargeValue;
//        $data['gridExportValue'] = $gridExportValue;
//        $data['loadValue'] = $loadValue;
//
//        $data['y_axis_array'] = $plantHistoryGraphYAxis;

        return ['status' => 1,'data' => $batteryDataArray];
    }
    public function PlantsList(Request $request)
    {
        $userID = $request->user()->id;
        $userPlants = PlantUser::where('user_id', $userID)->select('plant_id')
            ->get()
            ->pluck('plant_id')->toArray();

        $plants = Plant::whereIn('id', $userPlants)->get();
//        return $this->sendResponse(1, 'Showing all plants', $plants);
        $plants = $plants->map(function ($plant) {
            $processed_data = DailyProcessedPlantDetail::where('plant_id',$plant->id)->whereBetween('created_at',[date('Y-m-d 0:00:00'),date('Y-m-d 23:59:00')])->first();
            //$current_generation = GenerationLog::where('plant_id',$plant->id)->whereBetween('created_at',[date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->orderBy('created_at','desc')->first()->current_generation;
//            print_r($current_generation);
//            exit();
            $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
            $plant['system_type'] = SystemType::find($plant->system_type)->type;
            if ($plant['system_type'] == 4) {
                $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_discharge_energy', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plant->id)->orderBy('created_at', 'DESC')->first();
                if ($currentProcessedData) {
                    $plant['solar_power'] = round((double)$currentProcessedData->dailyGeneration * (double)$currentProcessedData->dailyConsumption / 100, 2) . ' kWh';
                    $plant['battery_soc'] = StationBattery::where('plant_id', $plant->id)->orderBy('collect_time', 'DESC')->first()['battery_capacity'] . '%';
                }
            } else {
                $plant['solar_power'] = '';
                $plant['battery_soc'] = '';
            }

            $current_data  = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type','collect_time','created_at')->where('plant_id', $plant->id)->orderBy('collect_time','desc')->first();
            $plant['current_generation'] = $current_data ? number_format($current_data->current_generation,2) : 0;
            $plant['current_consumption'] = $current_data ? number_format($current_data->current_consumption,2) : 0;
            $plant['battery_power'] = $current_data ? number_format($current_data->battery_power, 2) : '0';
            if ($plant['battery_type']) {
                $plant['battery_type'] = $current_data ? $current_data->battery_type : '0';
            } else {
                $plant['battery_type'] = '0';
            }

//            $plant['current_generation'] = $current_generation;
            $plant['power'] = isset($processed_data) && isset($processed_data['dailyMaxSolarPower']) ? (string)$processed_data['dailyMaxSolarPower'] : '0';


//            $plant['current_generation'] = $current_generation;
            $plant['power'] = $processed_data['dailyMaxSolarPower'].' kW';
            $plant['daily_generation'] = number_format($processed_data['dailyGeneration'],2);
            $plant['daily_revenue'] = number_format($processed_data['dailyGeneration'] * $plant->benchmark_price,2, '.', ',');

            $plant['last_updated'] = date('h:i A, d/m', strtotime($processed_data['lastUpdated']));
            $percentage_value = ($plant['current_generation'] / $plant->capacity * 100);

            $plant['percentage_value'] = number_format($percentage_value, 2, '.', ',');

            if($plant->plant_pic != null) {

                $plant->plant_pic = $this->url_scheme.'://'.$this->domain.'/public/plant_photo/'.$plant->plant_pic;
            }
            else {

                $plant->plant_pic = $this->url_scheme.'://'.$this->domain.'/public/plant_photo/plant_avatar.png';
            }

            return $plant;

            // $plant['progress_bar'] = '1000 PKR';
            // $plant['percentage'] = '7%';
            // $plant['plant_efficiency'] = 50;
        });

        return $this->sendResponse(1, 'Showing all plants', $plants);
    }

}
