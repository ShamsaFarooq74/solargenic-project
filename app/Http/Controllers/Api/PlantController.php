<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\HardwareAPIData\SolisController;
use App\Http\Models\CronJobTime;
use App\Http\Models\DailyProcessedPlantEMIDetail;
use App\Http\Models\Plant;
use App\Http\Models\PlantDetail;
use App\Http\Models\InverterDetail;
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
        $plants = Plant::whereIn('id', $plants_array)->orderBy('plant_name', 'ASC')->get(['id', 'plant_name', 'system_type', 'plant_type', 'company_id']);

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
            $plant_ids = PlantUser::where('user_id', $userID)->pluck('plant_id');
            $userPlants = Plant::whereIn('id', $plant_ids)->pluck('system_type');
            if ($request->type == 'hybrid') {
                $systemType = [4];
            } else {
                $systemType = [1,2];
            }

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
                    $plantsID = Plant::whereIn('id', $plantsID)->whereIn('system_type', $systemType)->pluck('id')->toArray();

                } else {

                    foreach (explode(',', $request->get('plant_id')) as $id) {

                        $plantsID[] = (int)$id;
                    }
                }
            }

            $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->whereIn('system_type', $systemType)->pluck('id')->toArray();


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

            $plants_array = $plantsData->pluck('id')->toArray();


            $monthly_peak_hours_savings = 0;
            $monthly_generation_saving = 0;
            $peak_hours_savings = 0;
            $generation_saving = 0;
            $totalCostSaving = 0;
            $yearly_peak_hours_savings = 0;
            $yearly_generation_saving = 0;

            for ($k = 0; $k < count($plants_array); $k++) {
                $daily_processed_data = DailyProcessedPlantDetail::where('plant_id', $plants_array[$k])->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
                $monthly_processed_data = MonthlyProcessedPlantDetail::where('plant_id', $plants_array[$k])->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
                $yearly_processed_data = YearlyProcessedPlantDetail::where('plant_id', $plants_array[$k])->whereBetween('created_at', [date('Y-01-01 0:00:00'), date('Y-m-d 23:59:00')])->latest()->first();
                $plantData = Plant::where('id', $plants_array[$k])->first();
                if ($daily_processed_data) {
                    $peak_hours_savings += $daily_processed_data ? (double)$daily_processed_data->daily_peak_hours_battery_discharge * (int)$plantData->peak_teriff_rate : 0;
                    $generation_saving += $daily_processed_data ? (double)$daily_processed_data->dailyGeneration * (int)$plantData->benchmark_price : 0;
                    $totalCostSaving += $daily_processed_data ? (double)$daily_processed_data->dailySaving : 0;
                }
                if ($monthly_processed_data) {
                    $monthly_peak_hours_savings += $monthly_processed_data ? (double)$monthly_processed_data->monthly_peak_hours_discharge_energy * (int)$plantData->peak_teriff_rate : 0;
                    $monthly_generation_saving += $monthly_processed_data ? (double)$monthly_processed_data->monthlyGeneration * (int)$plantData->benchmark_price : 0;

                }
                if ($yearly_processed_data) {
                    $yearly_peak_hours_savings += $yearly_processed_data ? (double)$yearly_processed_data->yearly_peak_hours_discharge_energy * (int)$plantData->peak_teriff_rate : 0;
                    $yearly_generation_saving += $yearly_processed_data ? (double)$yearly_processed_data->yearlyGeneration * (int)$plantData->benchmark_price : 0;
                }
            }
            
            $daily_peak_hour_saving = $this->unitConversion((double)$peak_hours_savings , 'PKR');
            $daily_generation_saving_arr = $this->unitConversion((double)$generation_saving , 'PKR');
            $daily_total_saving = $this->unitConversion(((double)$peak_hours_savings + (double)$generation_saving) ,'PKR');
            $daily['dailyPeakHoursSaving'] = round($daily_peak_hour_saving[0],2).str_replace(['PKR', ' '],'',$daily_peak_hour_saving[1]);;
            $daily['dailyGenerationSaving'] = round($daily_generation_saving_arr[0],2).str_replace(['PKR', ' '],'',$daily_generation_saving_arr[1]);
            $daily['dailyTotalSaving'] = round($daily_total_saving[0],2).str_replace(['PKR', ' '],'',$daily_total_saving[1]);
           
            $monthly_peak_hour_savings_arr = $this->unitConversion($monthly_peak_hours_savings, 'PKR');
            $monthly_generation_savings_arr = $this->unitConversion($monthly_generation_saving, 'PKR');
            $monthly_total_savings_arr = $this->unitConversion($monthly_peak_hours_savings + $monthly_generation_saving , 'PKR');

            $monthly['monthlyPeakHoursSaving'] =  round($monthly_peak_hour_savings_arr[0],2).str_replace(['PKR', ' '],'', $monthly_peak_hour_savings_arr[1]);
            $monthly['monthlyGenerationSaving'] = round($monthly_generation_savings_arr[0],2).str_replace(['PKR', ' '],'', $monthly_generation_savings_arr[1]);
            $monthly['monthlyTotalSaving'] = round($monthly_total_savings_arr[0],2).str_replace(['PKR', ' '],'',$monthly_total_savings_arr[1]);

            $yearly_peak_hours_savings_arr = $this->unitConversion($yearly_peak_hours_savings, 'PKR');
            $yearly_generation_saving_arr = $this->unitConversion($yearly_generation_saving, 'PKR');
            $yearly_total_savings_arr = $this->unitConversion($yearly_peak_hours_savings + $yearly_generation_saving ,'PKR');
            
            $yearly['yearlyPeakHoursSaving'] = round($yearly_peak_hours_savings_arr[0], 2).str_replace(['PKR', ' '],'', $yearly_peak_hours_savings_arr[1]);
            $yearly['yearlyGenerationSaving'] = round($yearly_generation_saving_arr[0], 2).str_replace(['PKR', ' '],'',$yearly_generation_saving_arr[1]);
            $yearly['yearlyTotalCostSaving'] = round($yearly_total_savings_arr[0],2).str_replace(['PKR', ' '],'',$yearly_total_savings_arr[1]);
            $total['totalPeakHoursSaving'] = round($yearly_peak_hours_savings_arr[0], 2).str_replace(['PKR', ' '],'', $yearly_peak_hours_savings_arr[1]);
            $total['totalGenerationSaving'] =  round($yearly_generation_saving_arr[0], 2).str_replace(['PKR', ' '],'',$yearly_generation_saving_arr[1]);
            $total['totalTotalCostSaving'] = round($yearly_total_savings_arr[0],2).str_replace(['PKR', ' '],'',$yearly_total_savings_arr[1]);
            $daily_processed_data = DB::table('daily_processed_plant_detail')
                ->selectRaw('SUM(dailyGeneration) as dailyGeneration, SUM(dailyConsumption) as dailyConsumption, SUM(dailyBoughtEnergy) as dailyBoughtEnergy, SUM(dailySellEnergy) as dailySellEnergy, SUM(dailySaving) as dailySaving')
                ->whereIn('daily_processed_plant_detail.plant_id', $plants_array)
                ->whereBetween('created_at', [date('Y-m-d 0:00:00'), date('Y-m-d 23:59:59')])
                ->orderBy('updated_at', 'DESC')->get();

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
            if ($systemType == [1,2]) {

                $current_data = DB::table('processed_current_variables')
                ->select('processed_current_variables.plant_id', DB::raw('SUM(current_generation) as current_generation'), DB::raw('SUM(current_consumption) as current_consumption'))
                ->whereIn('processed_current_variables.plant_id', $plants_array)
                ->whereDate('collect_time',Date('Y-m-d'))
                ->join(
                    DB::raw("(SELECT plant_id, MAX(collect_time) as max_collect_time FROM processed_current_variables GROUP BY plant_id) as latest_records"),
                    function ($join) {
                        $join->on('processed_current_variables.plant_id', '=', 'latest_records.plant_id');
                        $join->on('processed_current_variables.collect_time', '=', 'latest_records.max_collect_time');
                    }
                )
                ->get();
                $gridPositiveValue = DB::table('processed_current_variables')
                ->select( DB::raw('SUM(current_grid) as current_grid'))
                ->whereIn('processed_current_variables.plant_id', $plants_array)
                ->where('grid_type', '+ve')
                ->whereDate('collect_time',Date('Y-m-d'))
                ->join(
                    DB::raw("(SELECT plant_id, MAX(collect_time) as max_collect_time FROM processed_current_variables GROUP BY plant_id) as latest_records"),
                    function ($join) {
                        $join->on('processed_current_variables.plant_id', '=', 'latest_records.plant_id');
                        $join->on('processed_current_variables.collect_time', '=', 'latest_records.max_collect_time');
                    }
                )
                ->get();
                $gridNegativeValue = DB::table('processed_current_variables')
                ->select( DB::raw('SUM(current_grid) as current_grid'))
                ->whereIn('processed_current_variables.plant_id', $plants_array)
                ->where('grid_type', '-ve')
                ->whereDate('collect_time',Date('Y-m-d'))
                ->join(
                    DB::raw("(SELECT plant_id, MAX(collect_time) as max_collect_time FROM processed_current_variables GROUP BY plant_id) as latest_records"),
                    function ($join) {
                        $join->on('processed_current_variables.plant_id', '=', 'latest_records.plant_id');
                        $join->on('processed_current_variables.collect_time', '=', 'latest_records.max_collect_time');
                    }
                )
                ->get();
                $current_grid_pos = $gridPositiveValue[0]->current_grid;
                $current_grid_neg = $gridNegativeValue[0]->current_grid;

                $currents_grid = $current_grid_pos - $current_grid_neg;

                $current_data = $current_data && $current_data[0] ? $current_data[0] : $current_data;
                if ($current_data->current_consumption > 0) {
                    $current_data->current_consumption = $current_data->current_consumption * 1000;
                }

                $result['current_generation'] = $current_data ? number_format((double)$current_data->current_generation, 2) : '0.00';
                if(Plant::whereIn('id',$plants_array)->where('meter_type','Solis')->first()){
                    $result['current_consumption'] = $current_data ? number_format((double)$current_data->current_consumption / 1000, 2) : '0.00';
                }else{
                    $result['current_consumption'] = $current_data ? number_format((double)$current_data->current_consumption, 2) : '0.00';
                }
                $result['battery_power'] = '0.00';
                $result['battery_type'] = '+ve';
                $result['current_gird_import_power'] = round((double)$currents_grid, 2);
                if ((double)$result['current_gird_import_power'] < 0) {
                    $result['current_gird_import_power'] = (string)((double)$result['current_gird_import_power'] * -1);
                } else {
                    $result['current_gird_import_power'] = (string)$result['current_gird_import_power'];
                }
                $result['current_gird_export_power'] = round((double)$currents_grid, 2);
                if ((double)$result['current_gird_export_power'] < 0) {
                    $result['current_gird_export_power'] = (string)((double)$result['current_gird_export_power'] * -1);

                } else {
                    $result['current_gird_export_power'] = (string)$result['current_gird_export_power'];
                }
                $result['current_grid_type'] = $currents_grid >= 0 ? '+ve' : '-ve';
                $result['comm_fail'] = $currents_grid == 0 ? 'Power Outage or Communication Failure' : '';
            } else {
                $currentGeneration = '0.00';
                $currentConsumption = '0.00';
                $battery_power = '0.00';
                $battery_type = '+ve';
                $currentGridPos = '0.00';
                $currentGridNeg = '0.00';
                for ($i = 0; $i < count($plants_array); $i++) {
                    $processedPlantData = ProcessedCurrentVariable::where('plant_id', $plants_array[$i])->whereDate('collect_time', '=', date('Y-m-d'))->orderBy('collect_time', 'desc')->latest()->first();
                    if ($processedPlantData) {
                        $currentGeneration += $processedPlantData['current_generation'];
                        $currentConsumption += $processedPlantData['current_consumption'];
                        $battery_power += $processedPlantData['battery_power'];
                        if ($processedPlantData->grid_type == '+ve') {
                            $currentGridPos += $processedPlantData['current_grid'];
                        } else {
                            $currentGridNeg += $processedPlantData['current_grid'];
                        }

                    }
                }
                if ((double)$battery_power < 0) {
                    $battery_type = '-ve';
                    $battery_power = $battery_power * -1;
                }

                $currents_grid = $currentGridPos - $currentGridNeg;


                $result['current_generation'] = number_format((double)($currentGeneration), 2);
                $result['current_consumption'] = (string)$currentConsumption;


                $result['battery_power'] = number_format((double)($battery_power), 2);
                $result['battery_type'] = $battery_type;

                $result['current_gird_import_power'] = round((double)$currentGridPos, 2);
                if ((double)$result['current_gird_import_power'] < 0) {
                    $result['current_gird_import_power'] = (string)((double)$result['current_gird_import_power'] * -1);
                    $result['current_gird_export_power'] = (string)((double)$result['current_gird_import_power'] * -1);
                } else {
                    $result['current_gird_import_power'] = (string)$result['current_gird_import_power'];
                    $result['current_gird_export_power'] = (string)$result['current_gird_import_power'];
                }

                $result['current_grid_type'] = $currents_grid >= 0 ? '+ve' : '-ve';
                $result['comm_fail'] = $currents_grid == 0 ? 'Power Outage or Communication Failure' : '';
            }


            $result['daily_generation'] = $daily_processed_data ? round($daily_processed_data->dailyGeneration, 2) : '0';
            $result['monthly_generation'] = $monthly_processed_data ? round($monthly_processed_data->monthlyGeneration, 2) : '0';
            if ((double)$result['monthly_generation'] < 0) {
                $result['monthly_generation'] = (string)((double)$result['monthly_generation'] * -1);
            } else {
                $result['monthly_generation'] = (string)$result['monthly_generation'];
            }
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
            $result['last_updated'] = !empty($plants_array) ? date('h:i A, d/m', strtotime(Plant::where('id', $plants_array[0])->first()->updated_at)) : date('h:i A, d/m');

            $result['daily_generation'] = $daily_processed_data ? number_format($daily_processed_data->dailyGeneration, 2) : '0';
            $result['cost_savings'] = ['daily' => $daily, 'monthly' => $monthly, 'yearly' => $yearly, 'total' => $total];
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
            $result['last_updated'] = !empty($plants_array) ? date('h:i A, d/m', strtotime(Plant::where('id', $plants_array[0])->first()->updated_at)) : date('h:i A, d/m');

            return $this->sendResponse(1, 'Showing all data', $result);

        } catch (Exception $e) {

            return $this->sendError(0, 'Something went wrong');
        }
    }
    public
    function allPlants(Request $request)
    {
        $userID = $request->user()->id;
        $userPlants = PlantUser::where('user_id', $userID)->select('plant_id')
            ->get()
            ->pluck('plant_id')->toArray();

        $plants = Plant::whereIn('id', $userPlants)->get();

        $plants = $plants->map(function ($plant) {
            $processed_data = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-m-d 0:00:00'), date('Y-m-d 23:59:00')])->first();
            $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
            $plant['system_type'] = SystemType::find($plant->system_type)->type;
            if ($plant['system_type'] == 'Storage System') {
                $plant['battery_soc'] = StationBattery::where('plant_id', $plant->id)->orderBy('collect_time', 'DESC')->first();
                if ($plant['battery_soc']) {
                    $plant['battery_soc'] = $plant['battery_soc']->battery_capacity;
                } else {
                    $plant['battery_soc'] = '0%';
                }
                $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_discharge_energy', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plant->id)->orderBy('created_at', 'DESC')->first();
                if ($currentProcessedData) {
                    $plant['solar_power'] = round((double)$currentProcessedData->dailyGeneration * (double)$currentProcessedData->dailyConsumption / 100, 2);

                }
            } else {
                $plant['solar_power'] = '';
            }

            $current_data = ProcessedCurrentVariable::select('current_generation', 'battery_type', 'battery_power', 'current_consumption', 'current_grid', 'grid_type', 'collect_time', 'created_at')->where('plant_id', $plant->id)->orderBy('collect_time', 'desc')->first();
            $plant['current_generation'] = $current_data ? number_format($current_data->current_generation, 2) : "0.00";
            $plant['current_consumption'] = $current_data ? number_format($current_data->current_consumption, 2) : "0.00";
            $plant['battery_power'] = $current_data ? number_format($current_data->battery_power, 2) : '0';
            if(isset($current_data->battery_type)) {
                $plant['battery_type'] = $current_data ? $current_data->battery_type : '';
            } else {
                $plant['battery_type'] = '';
            }

            $plant['power'] = isset($processed_data) && isset($processed_data['dailyMaxSolarPower']) ? (string)$processed_data['dailyMaxSolarPower'] : '0';
            $plant['power'] = isset($processed_data) ? $processed_data['dailyMaxSolarPower'] . ' kW' : '0.00 kW';
            $plant['daily_generation'] = isset($processed_data) ? number_format($processed_data['dailyGeneration'], 2) : '0.00';
            $plant['daily_consumption'] = isset($processed_data) ? number_format($processed_data['dailyConsumption'], 2) : '0.00';
            $plant['daily_revenue'] = isset($processed_data) ? number_format($processed_data['dailyGeneration'] * (int)$plant->benchmark_price, 2, '.', ',') : '0.00 ';

            $plant['last_updated'] = isset($processed_data) ? date('h:i A, d/m', strtotime($processed_data['updated_at'])) : '0.00';
            $percentage_value = ($plant['current_generation'] / $plant->capacity * 100);

            $plant['percentage_value'] = number_format($percentage_value, 2, '.', ',');

            if ($plant->plant_pic != null) {

                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/' . $plant->plant_pic;
            } else {

                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/plant_avatar.png';
            }

            return $plant;

        });

        return $this->sendResponse(1, 'Showing all plants', $plants);
    }
//latest solis optimization
    public
    function plantDetail(Request $request)
    {
        //    return 'true';
        $validator = Validator::make($request->all(), [
            'plant_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }
        $plantID = $request->get('plant_id');
        $userID = $request->user()->id;

        $plants = Plant::where('id', $plantID)->get();
       
        if (count($plants) == 0) {
            return $this->sendError(0, 'Plant not found.');
        }


        $plants = $plants->map(function ($plant) {

            $plant['system_type_data'] = $plant->system_type;

            $daily_processed_data = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
            $dailyTimeArray = [];
            if (!$daily_processed_data) {
                $daily_processed_data = [];
            }
            $dataArray = json_decode(json_encode($daily_processed_data), true);

            for ($i = 0; $i < count($dataArray); $i++) {

                if (isset($dataArray[$i]) && $dataArray[$i]['daily_outage_grid_voltage'] != null) {
                    $dailyOutagesHoursData = explode(':', $dataArray[$i]['daily_outage_grid_voltage']);
                    $dailyTimeArray[] = $dailyOutagesHoursData[0] . ':' . $dailyOutagesHoursData[1] . ':00';
                }
            }
            $totalSeconds = 0;
            foreach ($dailyTimeArray as $t) {
                $totalSeconds += $this->toSeconds($t);
            }

            $dailyOutagesGridValue = $this->toTimeCalculation($totalSeconds);

            $explodeDailyData = explode(':', $dailyOutagesGridValue);
            if ($explodeDailyData[0] == 0) {
                $explodeDailyData[0] = '00';
            }
            if ($explodeDailyData[1] == 0) {
                $explodeDailyData[1] = '00';
            }
            $outagesHours = $explodeDailyData[0] . ':' . $explodeDailyData[1];
            $plant['outages_served'] = $outagesHours;
            if (!empty($daily_processed_data)) {
                $daily_generation = $daily_processed_data ? (double)$daily_processed_data->dailyGeneration : 0;
                $daily_consumption = $daily_processed_data ? (double)$daily_processed_data->dailyConsumption : 0;
                $daily_grid = $daily_processed_data ? (double)$daily_processed_data->dailyGridPower : 0;
                $daily_bought_energy = $daily_processed_data ? (double)$daily_processed_data->dailyBoughtEnergy : 0;
                $daily_sell_energy = $daily_processed_data ? (double)$daily_processed_data->dailySellEnergy : 0;
                $daily_saving = $daily_processed_data ? (double)$daily_processed_data->dailySaving : 0;
                $daily_charge_energy = $daily_processed_data ? (double)$daily_processed_data->daily_charge_energy : 0;
                $daily_discharge_energy = $daily_processed_data ? (double)$daily_processed_data->daily_discharge_energy : 0;
                $peak_hours_savings = $daily_processed_data ? (double)$daily_processed_data->daily_peak_hours_battery_discharge * (int)$plant->peak_teriff_rate : 0;
                $generation_saving = $daily_processed_data ? (double)$daily_processed_data->dailyGeneration * (int)$plant->benchmark_price : 0;
                $totalCostSaving = $daily_processed_data ? (double)$daily_processed_data->dailySaving : 0;
                $daily_gridLoad = $daily_processed_data ? (double)$daily_processed_data->dailyGridLoad : 0;
                $daily_backupLoad = $daily_processed_data ? (double)$daily_processed_data->dailyBackupLoad : 0;
//                $daily['date'] = $daily_processed_data ? $daily_processed_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
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
                $daily_gridLoad = 0;
                $daily_backupLoad = 0;
                $totalCostSaving = 0;

            }
          
            $daily_charge_arr = $this->unitConversion($daily_charge_energy, 'kWh');
            $daily_discharge_arr = $this->unitConversion($daily_discharge_energy, 'kWh');
          
            $daily_peak_hour_saving = $this->unitConversion((double)$peak_hours_savings , 'PKR');
            $daily_generation_saving_arr = $this->unitConversion((double)$generation_saving , 'PKR');
            $daily_total_saving = $this->unitConversion(((double)$peak_hours_savings + (double)$generation_saving) ,'PKR');

            $daily['dailyPeakHoursSaving'] = (string)round($peak_hours_savings, 2);
            $daily['dailyGenerationSaving'] = (string)round($generation_saving, 2);
            $daily['dailyTotalSaving'] = (string)round($daily['dailyPeakHoursSaving'] + $daily['dailyGenerationSaving'], 2);

            $monthly_processed_data = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
            // return $monthly_processed_data;
            $monthly_peak_hours_savings = $monthly_processed_data ? (double)$monthly_processed_data->monthly_peak_hours_discharge_energy * (int)$plant->peak_teriff_rate : 0;
            $monthly_generation_saving = $monthly_processed_data ? (double)$monthly_processed_data->monthlyGeneration * (int)$plant->benchmark_price : 0;

            $monthly_peak_hour_savings_arr = $this->unitConversion($monthly_peak_hours_savings, 'PKR');
            $monthly_generation_savings_arr = $this->unitConversion($monthly_generation_saving, 'PKR');
            $monthly_total_savings_arr = $this->unitConversion($monthly_peak_hours_savings + $monthly_generation_saving , 'PKR');

            $monthly['monthlyPeakHoursSaving'] = (string)round($monthly_peak_hours_savings, 2);
            $monthly['monthlyGenerationSaving'] = (string)round($monthly_generation_saving, 2);
            $monthly['monthlyTotalSaving'] = (string)round($monthly['monthlyPeakHoursSaving'] + $monthly['monthlyGenerationSaving'], 2);

            $yearly_processed_data = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-01-01 0:00:00'), date('Y-m-d 23:59:00')])->latest()->first();
            $yearly_peak_hours_savings = $yearly_processed_data ? (double)$yearly_processed_data->yearly_peak_hours_discharge_energy * (int)$plant->peak_teriff_rate : 0;
            $yearly_generation_saving = $yearly_processed_data ? (double)$yearly_processed_data->yearlyGeneration * (int)$plant->benchmark_price : 0;
            $yearly['yearlyPeakHoursSaving'] = (string)round($yearly_peak_hours_savings, 2);
            $yearly['yearlyGenerationSaving'] = (string)round($yearly_generation_saving, 2);
            $yearly['yearlyTotalCostSaving'] = (string)round($yearly['yearlyPeakHoursSaving'] + $yearly['yearlyGenerationSaving'], 2);
            $total['totalPeakHoursSaving'] = (string)round($yearly_peak_hours_savings, 2);
            $total['totalGenerationSaving'] = (string)round($yearly_generation_saving, 2);
            $total['totalTotalCostSaving'] = (string)round($total['totalPeakHoursSaving'] + $total['totalGenerationSaving'], 2);
            $daily_expected_generation = (ExpectedGenerationLog::where('plant_id', $plant->id)->orderBy('created_at', 'desc')->first()) ? ExpectedGenerationLog::where('plant_id', $plant->id)->orderBy('created_at', 'desc')->first()->daily_expected_generation : 0;
            $plant['cost_savings'] = ['daily' => $daily, 'monthly' => $monthly, 'yearly' => $yearly, 'total' => $total];
            //c-g
            $current_log = GenerationLog::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:00')])->orderBy('created_at', 'desc')->first();
            $plant['company_name'] = Company::where('id', $plant->company_id)->first()->company_name;
            $plant['company_pic'] = Company::where('id', $plant->company_id)->first()->logo;

            $plant['design_capacity'] = $plant->capacity;
            $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
            $plant['system_type'] = SystemType::find($plant->system_type)->type;

            $plant['expected_generation'] = $daily_expected_generation;

            //start
            $solisAppData = Setting::where('perimeter', 'solis_api_app_id')->first();

            if (!empty($solisAppData)) {
                $appID = $solisAppData->value;
            } else {
                $appID = '3yhg';
            }
            $solisAppKey = Setting::where('perimeter', 'solis_api_app_key')->first();
            if (!empty($solisAppKey)) {
                $appKey = $solisAppKey->value;
            } else {
                $appKey = '3yhg';
            }
            $solisUserAccount = Setting::where('perimeter', 'solis_api_user_account')->first();
            if (!empty($solisUserAccount)) {
                $userAccount = $solisUserAccount->value;
            } else {
                $userAccount = '3yhg';
            }
            $solisPassword = Setting::where('perimeter', 'solis_api_user_password')->first();
            $userPassword = !empty($solisPassword) ? $solisPassword->value : '3yhg';
            $solisOrgId = Setting::where('perimeter', 'solis_api_orgID')->first();
            $OrgId = !empty($solisOrgId) ? $solisOrgId->value : '3yhg';
            $solisApiBaseUrl = Setting::where('perimeter', 'solis_api_base_url')->first();
            $solisAPIBaseURL = !empty($solisApiBaseUrl) ? $solisApiBaseUrl->value : '';
            $solisController = new SolisController();
            $token = $solisController->getToken($solisAPIBaseURL, $appID, $appKey, $userAccount, $userPassword, $OrgId);
            $pl_sites_array = PlantSite::where('plant_id', $plant->id)->pluck('site_id')->toArray();
            $plant_inverters = InverterSerialNo::whereIn('site_id', $pl_sites_array)->get();
            $currentGeneration = 0;
            $currentConsumption = 0;
            $gridPower = 0;
            $batteryPower = 0;
            $batteryCapacity = 0;
            $gridImportEnergy = 0;
            $gridExportEnergy = 0;
            $dailyGeneration = 0;
            $dailyConsumption = 0;
            $daily_grid = 0;
            $daily_bought_energy = 0;
            $daily_sell_energy = 0;
            $daily_saving = 0;
            $daily_charge_energy = 0;
            $daily_discharge_energy = 0;
            $peak_hours_savings = 0;
            $generation_saving = 0;
            $totalCostSaving = 0;


            $plant['power'] = $daily_processed_data ? (string)$daily_processed_data['dailyMaxSolarPower'] : '0';

            $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $plant->id)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $plant->id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
            $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
            $current_data = ProcessedCurrentVariable::select('total_backup_Load','grid_Load','current_generation', 'current_consumption', 'current_grid', 'grid_type', 'comm_failed', 'battery_type', 'battery_power', 'battery_capacity', 'created_at')->whereDate('collect_time', date('Y-m-d'))->where('plant_id', $plant->id)->orderBy('collect_time', 'desc')->latest()->first();
         
            if($token && $plant->meter_type == "Solis" && $plant->grid_type != "Three-phase-string"){
                $inverterStatusArray = [];
                $updateSiteStatusArray = array();
                foreach ($plant_inverters as $smartKey => $smartInverter) {

                    $inverterData =
                        [
                            "deviceSn" => $smartInverter->dv_inverter,
                        ];


                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $solisAPIBaseURL . '/device/v1.0/currentData',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($inverterData),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $token,
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    $invertDataList = json_decode($response);

                    if(isset($invertDataList->deviceState)){
                        array_push($inverterStatusArray, $invertDataList->deviceState);
                    }

                    $lastRecordDate = Date('Y-m-d');
                    $collectTime = strtotime(Date('Y-m-d H:i:s'));
                    $solisHistoricalData = $solisController->getHistoricalData($solisAPIBaseURL, $smartInverter->dv_inverter, $lastRecordDate, $token);
                    $siteSmartInverterResponseData = json_decode($solisHistoricalData);

                    if ($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->paramDataList)) {
                        $siteSmartInverterFinalData = $siteSmartInverterResponseData->paramDataList;
                        $siteSmartInverterFinalData =  array_reverse($siteSmartInverterFinalData);
                        if ($siteSmartInverterFinalData) {
                            foreach ($siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {
                                $responseData = isset($smartInverterFinalData->dataList) ? $smartInverterFinalData->dataList : [];
                                 $collectTime = $smartInverterFinalData->collectTime;

                                if ($plant->grid_type == 'Three-phase') {
                                    $keys = array_keys(array_column($responseData, 'key'), 'S_P_T');
                                    if ($keys && isset($responseData[$keys[0]]->value)) {
                                        $currentGeneration += ($responseData[$keys[0]]->value / 1000);
                                    }

                                } else {
                                    $keys = array_keys(array_column($responseData, 'key'), 'DPi_t1');
                                    if ($keys && isset($responseData[$keys[0]]->value)) {
                                        $currentGeneration += ($responseData[$keys[0]]->value / 1000);
                                    }
                                }
                                $dailyCurrentCons = array_keys(array_column($responseData, 'key'), 'E_Puse_t1');
                                if ($dailyCurrentCons && isset($responseData[$dailyCurrentCons[0]]->value)) {
                                    $currentConsumption += $responseData[$dailyCurrentCons[0]]->value;
                                }
                                //                                $keys = array_keys(array_column($responseData, 'key'), 'B_left_cap1');
                                //                                if ($keys) {
                                //                                    $batteryCapacity = $responseData[$keys[0]]->value . $responseData[$keys[0]]->unit;
                                //                                }
                                //                                $batteryPower = array_keys(array_column($responseData, 'key'), 'B_P1');
                                //                                if ($batteryPower) {
                                //                                    $batteryPower = $responseData[$batteryPower[0]]->value;
                                //                                }

                                $keys = array_keys(array_column($responseData, 'key'), 'B_left_cap1');
                                if ($keys && isset($responseData[$keys[0]]->value)) {
                                    $batteryCapac= (int)$responseData[$keys[0]]->value;
                                    $batteryCapacity += $batteryCapac;
                                }
                                $batteryPowerd = array_keys(array_column($responseData, 'key'), 'B_P1');
                                if ($batteryPowerd && isset($responseData[$batteryPowerd[0]]->value)) {
                                    $batteryPower += $responseData[$batteryPowerd[0]]->value;
                                }
                                $totalGridPowerData = array_keys(array_column($responseData, 'key'), 'PG_Pt1');
                                if ($totalGridPowerData && isset($responseData[$totalGridPowerData[0]]->value)) {
                                    $gridPower += $responseData[$totalGridPowerData[0]]->value;
                                }
                                if ($gridPower) {
                                    $gridPower = (($gridPower / 1000));
                                }
                                $dailyGen = array_keys(array_column($responseData, 'key'), 'Etdy_ge1');
                                if ($dailyGen && isset($responseData[$dailyGen[0]]->value)) {
                                    $dailyGeneration += $responseData[$dailyGen[0]]->value;
                                }
                                $dailyCons = array_keys(array_column($responseData, 'key'), 'Etdy_use1');
                                if ($dailyCons && isset($responseData[$dailyCons[0]]->value)) {
                                    $dailyConsumption += $responseData[$dailyCons[0]]->value;
                                }
                                $dailyChargeEnergy = array_keys(array_column($responseData, 'key'), 'Etdy_cg1');
                                if ($dailyChargeEnergy && isset($responseData[$dailyChargeEnergy[0]]->value)) {
                                    $daily_charge_energy += $responseData[$dailyChargeEnergy[0]]->value;
                                }
                                $dailyDischargeEnergy = array_keys(array_column($responseData, 'key'), 'Etdy_dcg1');
                                if ($dailyDischargeEnergy && isset($responseData[$dailyDischargeEnergy[0]]->value)) {
                                    $daily_discharge_energy += $responseData[$dailyDischargeEnergy[0]]->value;
                                }
                                if ($plant->grid_type == "Three-phase") {
                                    $totalGridDailyEnergyData = array_keys(array_column($responseData, 'key'), 'E_B_D');
                                    if ($totalGridDailyEnergyData && isset($responseData[$totalGridDailyEnergyData[0]]->value)) {
                                        $gridImportEnergy += $responseData[$totalGridDailyEnergyData[0]]->value;
                                    }
                                } else {
                                    $totalGridDailyEnergyData = array_keys(array_column($responseData, 'key'), 'Etdy_pu1');
                                    if ($totalGridDailyEnergyData && isset($responseData[$totalGridDailyEnergyData[0]]->value)) {
                                        $gridImportEnergy += $responseData[$totalGridDailyEnergyData[0]]->value;
                                    }
                                }

                                if ($plant->grid_type == "Three-phase") {
                                    $totalGridDailyFeedData = array_keys(array_column($responseData, 'key'), 'E_S_D');
                                    if ($totalGridDailyFeedData && isset($responseData[$totalGridDailyFeedData[0]]->value)) {
                                        $gridExportEnergy += $responseData[$totalGridDailyFeedData[0]]->value;
                                    }
                                } else {
                                    $totalGridDailyFeedData = array_keys(array_column($responseData, 'key'), 't_gc_tdy1');
                                    if ($totalGridDailyFeedData && isset($responseData[$totalGridDailyFeedData[0]]->value)) {
                                        $gridExportEnergy += $responseData[$totalGridDailyFeedData[0]]->value;
                                    }
                                }
                                break;
                            }
                        }
                    }
                }

                $updateSiteStatusArray['online_status'] = 'Y';


                if (in_array(1, $inverterStatusArray)) {

                    $updateSiteStatusArray['online_status'] = 'Y';
                }

                if (in_array(2, $inverterStatusArray)) {

                    $updateSiteStatusArray['online_status'] = 'A';
                }

                if (in_array(2, $inverterStatusArray) && in_array(1, $inverterStatusArray)) {

                    $updateSiteStatusArray['online_status'] = 'P_Y';
                }

                if (in_array(3, $inverterStatusArray)) {

                    $updateSiteStatusArray['online_status'] = 'N';
                }
                $status = 'default';
                if ($inverterStatusArray) {
                    $statusArray = array_unique($inverterStatusArray);
                    if ($statusArray) {
                        $status = $statusArray[0];
                    }

                }
                if (in_array(1, $inverterStatusArray)) {

                    $updateSiteStatusArray['online_status'] = 'Y';
                }

                $plant['is_online'] = $updateSiteStatusArray['online_status'];
                $plantDailyBoughtEnergy = $gridImportEnergy;
                $plantDailySell = $gridExportEnergy;

                $current['date'] = date('Y-m-d H:i:s');
                $current['comm_fail'] = 0;
                $current_grid_type = $gridPower > 0 ? '+ve' : '-ve';
                $batteryType = $batteryPower > 0 ? '+ve' : '-ve';
                $daily_charge_energy = (double)$daily_charge_energy;
                $daily_discharge_energy = (double)$daily_discharge_energy;
                $daily_grid_data = $plantDailyBoughtEnergy > $plantDailySell ? $plantDailyBoughtEnergy - $plantDailySell : $plantDailySell - $plantDailyBoughtEnergy;
                $daily_grid = $daily_grid_data;
                $daily_bought_energy = (double)$plantDailyBoughtEnergy;
                $daily_sell_energy = (double)$plantDailySell;
                $plant['current_generation'] = $currentGeneration ? number_format((double)$currentGeneration, 2) : '0';
                if ($plant->system_type_data == 2 || $plant->system_type_data == 1) {
                    if($plant->meter_type == "Solis"){
                        $plant['current_consumption'] = (string)round(isset($currentConsumption) ? $currentConsumption : 0, 2);
                    }
                } else {
                    $plant['current_consumption'] = (string)round(isset($currentConsumption) ? $currentConsumption : 0, 2);

                }
                $plant['battery_power'] = $batteryPower ? number_format($batteryPower, 2) : '0';
                $plant['battery_type'] = $batteryType ? $batteryType : '';
                if(empty($batteryCapacity)){
                    $plant['battery_soc'] = '0%';
                }else{
                    if($batteryCapacity > 100){
                        $plant['battery_soc'] = '100%';
                    }else{
                        $plant['battery_soc'] = isset($batteryCapacity) ? $batteryCapacity."%" : '100%';
                    }
                }
                $plant['current_gird_import_power'] = $gridPower ? number_format(abs($gridPower), 2) : '0';
                $plant['current_gird_export_power'] = $gridPower ? number_format(abs($gridPower), 2) : '0';
                $plant['current_grid_type'] = $current_grid_type ? $current_grid_type : '';
                $plant['daily_generation'] = $dailyGeneration ? number_format($dailyGeneration, 2) : '0.00';
                $plant['daily_consumption'] = $dailyConsumption ? number_format($dailyConsumption, 2) : '0.00';
                $plant['daily_energy_bought'] = $plantDailyBoughtEnergy ? number_format($plantDailyBoughtEnergy, 2) : '0.00';
                $plant['daily_energy_sell'] = $plantDailySell ? number_format($plantDailySell, 2) : '0.00';
                $dailySaving =  $dailyGeneration * $plant->benchmark_price;
                $plant['daily_revenue'] = $dailySaving ? number_format($dailySaving, 2) : '0.00';
                $plant['last_updated'] = date('h:i A, d/m',$collectTime);
            }else{
                $plant['battery_power'] = $current_data ? number_format($current_data->battery_power, 2) : '0';
                if ($current_data) {
                    $plant['battery_type'] = $current_data ? $current_data->battery_type : '';
                } else {
                    $plant['battery_type'] = '';
                }
                $plant['battery_soc'] = StationBattery::where('plant_id', $plant->id)->orderBy('collect_time', 'DESC')->first();
                if ($plant['battery_soc']) {
                    $plant['battery_soc'] = $plant['battery_soc']->battery_capacity;
                } else {
                    $plant['battery_soc'] = '';
                }
                $current_backup_load = $current_data ? (double)$current_data->total_backup_Load : 0;
                $current_grid_load = $current_data ? (double)$current_data->grid_Load : 0;
                $current_backup_load_arr = $this->unitConversion($current_backup_load, 'kW');
                $current_grid_load_arr = $this->unitConversion($current_grid_load, 'kW');
                $plant['current_grid_load']= round($current_grid_load_arr[0], 2) . ' ' . $current_grid_load_arr[1];
                $plant['current_backup_load'] = round($current_backup_load_arr[0], 2) . ' ' . $current_backup_load_arr[1];
                $plant['current_generation'] = $current_data ? number_format((double)$current_data->current_generation, 2) : '0';
      
                $curr_con_arr = $this->unitConversion(isset($current_data->current_consumption) ? $current_data->current_consumption : 0, 'W');

                if ($plant->system_type_data == 2 || $plant->system_type_data == 1) {
                    if($plant->meter_type == "Solis"){
                        $plant['current_consumption'] = (string)round(isset($current_data->current_consumption) ? $current_data->current_consumption : 0, 2);
                    }else{
                        if ($curr_con_arr[0] > 0) {
                            $curr_con_arr[0] = $curr_con_arr[0] * 1000;
                        }
                        $plant['current_consumption'] = (string)round($curr_con_arr[0], 2);
                    }
                } else {
                    $plant['current_consumption'] = (string)round(isset($current_data->current_consumption) ? $current_data->current_consumption : 0, 2);
                }

                $plant['current_gird_import_power'] = $current_data ? number_format((double)$current_data->current_grid, 2) : '0';
                $plant['current_gird_export_power'] = $current_data ? number_format((double)$current_data->current_grid, 2) : '0';
                $plant['current_grid_type'] = $current_data ? $current_data->grid_type : '';
                $plant['daily_generation'] = $daily_processed_data ? number_format($daily_processed_data->dailyGeneration, 2) : '0';
                $plant['daily_consumption'] = $daily_processed_data ? number_format($daily_processed_data->dailyConsumption, 2) : '0';
                $plant['daily_energy_bought'] = $daily_processed_data ? number_format($daily_processed_data->dailyBoughtEnergy, 2) : '0';
                $plant['daily_energy_sell'] = $daily_processed_data ? number_format($daily_processed_data->dailySellEnergy, 2) : '0';
                $plant['daily_revenue'] = $daily_processed_data ? number_format($daily_processed_data->dailySaving, 2) : '0';
                // return 'true';
               
                $daily_gridLoad_arr = $this->unitConversion($daily_gridLoad, 'kWh');
                $daily_backupLoad_arr = $this->unitConversion($daily_backupLoad, 'kWh');
                $plant['daily_grid_load'] = round($daily_gridLoad_arr[0], 2) . ' ' . $daily_gridLoad_arr[1];
                $plant['daily_backup_load'] = round($daily_backupLoad_arr[0], 2) . ' ' . $daily_backupLoad_arr[1];
                $plant['last_updated'] = date('h:i A, d/m', strtotime($plant->updated_at));
            }

            $batteryValues = StationBattery::where('plant_id', $plant->id)->orderBy('collect_time', 'desc')->first();
            $batteryData = StationBatteryData::where('plant_id', $plant->id)->latest()->first();
            $plantBatteryAh = isset($batteryData) ? (int)$batteryData['battery_ah'] : 0;
            $plantBatteryVoltage = isset($batteryData) ? (double)$batteryData['battery_voltage'] : 0;
            $batteryDOD = isset($batteryData) ? (int)$batteryData['battery_dod'] : 0;
            $batteryCapacity = isset($batteryValues) ? (int)$batteryValues['battery_capacity'] : 0;
            $batteryRatedPower = isset($batteryValues) ? (int)$batteryValues['rated_power'] : 0;

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

            if ($batteryRatedPower == 0) {
                $batteryBackupMaxLoad = ($batteryRemaining / (0.9));
            } else {
                $batteryBackupMaxLoad = ($batteryRemaining / (0.9 * ($batteryRatedPower / 1000)));
            }

            $batteryBackupFormula = round($batteryBackup, 2);
            $batteryBackupMaxLoadFormula = round($batteryBackupMaxLoad, 2);

            if ($plant['current_consumption'] < 30) {
                $batteryBackupFormula = 'No Load State';
            }
            $plant['battery_information'] = ['batter-remaining' => (string)$batteryRemaining, 'battery_back_up_current_load' => (string)$batteryBackupFormula, 'battery_backup_max_load' => (string)$batteryBackupMaxLoadFormula];

            $plant['comm_fail'] = $current_data ? ($current_data->comm_failed == 1 ? 'Power Outage or Communication Failure' : '') : '';


            $percentage_value = $current_log ? ($current_log->current_generation / $plant->capacity * 100) : 0;
            $plant['percentage_value'] = number_format($percentage_value, 2);
            $total_processed_data = TotalProcessedPlantDetail::where('plant_id', $plant->id)->select('plant_total_grid_load','plant_total_backup_load')->first();
        
            $total_processed_grid_load = $total_processed_data ? (double)$total_processed_data->plant_total_grid_load : 0;
            $total_processed_backup_load = $total_processed_data ? (double)$total_processed_data->plant_total_backup_load : 0;
            $total_processed_grid_Load_arr = $this->unitConversion($total_processed_grid_load, 'kWh');
            $total_processed_backup_load_arr = $this->unitConversion($total_processed_backup_load, 'kWh');
            $plant['total_processed_grid_load'] = round($total_processed_grid_Load_arr[0], 2) . ' ' . $total_processed_grid_Load_arr[1];
            $plant['total_processed_backup_load'] = round($total_processed_backup_load_arr[0], 2) . ' ' . $total_processed_backup_load_arr[1];

           
            $monthly_grid_load = $monthly_processed_data ? (double)$monthly_processed_data->monthlyGridLoad : 0;
            $monthly_backup_load = $monthly_processed_data ? (double)$monthly_processed_data->monthlyBackupLoad : 0;
            $monthly_grid_Load_arr = $this->unitConversion($monthly_grid_load, 'kWh');
            $monthly_backup_load_arr = $this->unitConversion($monthly_backup_load, 'kWh');
            $plant['monthly_backup_load'] = round($monthly_backup_load_arr[0], 2) . ' ' . $monthly_backup_load_arr[1];
            $plant['monthly_grid_load'] = round($monthly_grid_Load_arr[0], 2) . ' ' . $monthly_grid_Load_arr[1];
           
            $plant['monthly_generation'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyGeneration, 2) : '0';
           
             $yearly_grid_load = $yearly_processed_data ? (double)$yearly_processed_data->yearlyGridLoad : 0;
             $yearly_backup_Load = $yearly_processed_data ? (double)$yearly_processed_data->yearlyBackupLoad : 0;
             $yearly_grid_load_arr = $this->unitConversion($yearly_grid_load, 'kWh');
             $yearly_backup_load_arr = $this->unitConversion($yearly_backup_Load, 'kWh');
            $plant['yearly_backup_load'] = round($yearly_backup_load_arr[0], 2) . ' ' . $yearly_backup_load_arr[1];
            $plant['yearly_grid_load'] = round($yearly_grid_load_arr[0], 2) . ' ' . $yearly_grid_load_arr[1];
            $plant['yearly_generation'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlyGeneration, 2) : '0';
            $plant['monthly_consumption'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyConsumption, 2) : '0';
            $plant['yearly_consumption'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlyConsumption, 2) : '0';
            $plant['monthly_energy_bought'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyBoughtEnergy, 2) : '0';
            $plant['monthly_energy_sell'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlySellEnergy, 2) : '0';
            $plant['monthly_revenue'] = $monthly_processed_data ? number_format(abs($monthly_processed_data->monthlySaving), 2) : '0';
            $plant['yearly_revenue'] = $yearly_processed_data ? number_format(abs($yearly_processed_data->yearlySaving), 2) : '0';



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

            if ($plant->plant_pic != null) {

                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/' . $plant->plant_pic;
            } else {
                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/plant_avatar.png';
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


    //Plant Detail Latest Api V1
    public
    function plantDetailV1(Request $request)
    {
        //    return 'true';
        $validator = Validator::make($request->all(), [
            'plant_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }
        $plantID = $request->get('plant_id');
        $userID = $request->user()->id;

        $plants = Plant::where('id', $plantID)->get();
       
        if (count($plants) == 0) {
            return $this->sendError(0, 'Plant not found.');
        }


        $plants = $plants->map(function ($plant) {

            $plant['system_type_data'] = $plant->system_type;

            $daily_processed_data = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
            $dailyTimeArray = [];
            if (!$daily_processed_data) {
                $daily_processed_data = [];
            }
            $dataArray = json_decode(json_encode($daily_processed_data), true);

            for ($i = 0; $i < count($dataArray); $i++) {

                if (isset($dataArray[$i]) && $dataArray[$i]['daily_outage_grid_voltage'] != null) {
                    $dailyOutagesHoursData = explode(':', $dataArray[$i]['daily_outage_grid_voltage']);
                    $dailyTimeArray[] = $dailyOutagesHoursData[0] . ':' . $dailyOutagesHoursData[1] . ':00';
                }
            }
            $totalSeconds = 0;
            foreach ($dailyTimeArray as $t) {
                $totalSeconds += $this->toSeconds($t);
            }

            $dailyOutagesGridValue = $this->toTimeCalculation($totalSeconds);

            $explodeDailyData = explode(':', $dailyOutagesGridValue);
            if ($explodeDailyData[0] == 0) {
                $explodeDailyData[0] = '00';
            }
            if ($explodeDailyData[1] == 0) {
                $explodeDailyData[1] = '00';
            }
            $outagesHours = $explodeDailyData[0] . ':' . $explodeDailyData[1];
            $plant['outages_served'] = $outagesHours;
            if (!empty($daily_processed_data)) {
                $daily_generation = $daily_processed_data ? (double)$daily_processed_data->dailyGeneration : 0;
                $daily_consumption = $daily_processed_data ? (double)$daily_processed_data->dailyConsumption : 0;
                $daily_grid = $daily_processed_data ? (double)$daily_processed_data->dailyGridPower : 0;
                $daily_bought_energy = $daily_processed_data ? (double)$daily_processed_data->dailyBoughtEnergy : 0;
                $daily_sell_energy = $daily_processed_data ? (double)$daily_processed_data->dailySellEnergy : 0;
                $daily_saving = $daily_processed_data ? (double)$daily_processed_data->dailySaving : 0;
                $daily_charge_energy = $daily_processed_data ? (double)$daily_processed_data->daily_charge_energy : 0;
                $daily_discharge_energy = $daily_processed_data ? (double)$daily_processed_data->daily_discharge_energy : 0;
                $peak_hours_savings = $daily_processed_data ? (double)$daily_processed_data->daily_peak_hours_battery_discharge * (int)$plant->peak_teriff_rate : 0;
                $generation_saving = $daily_processed_data ? (double)$daily_processed_data->dailyGeneration * (int)$plant->benchmark_price : 0;
                $totalCostSaving = $daily_processed_data ? (double)$daily_processed_data->dailySaving : 0;
                $daily_gridLoad = $daily_processed_data ? (double)$daily_processed_data->dailyGridLoad : 0;
                $daily_backupLoad = $daily_processed_data ? (double)$daily_processed_data->dailyBackupLoad : 0;
//                $daily['date'] = $daily_processed_data ? $daily_processed_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
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
                $daily_gridLoad = 0;
                $daily_backupLoad = 0;
                $totalCostSaving = 0;

            }
          
            $daily_charge_arr = $this->unitConversion($daily_charge_energy, 'kWh');
            $daily_discharge_arr = $this->unitConversion($daily_discharge_energy, 'kWh');
          
            $daily_peak_hour_saving = $this->unitConversion((double)$peak_hours_savings , 'PKR');
            $daily_generation_saving_arr = $this->unitConversion((double)$generation_saving , 'PKR');
            $daily_total_saving = $this->unitConversion(((double)$peak_hours_savings + (double)$generation_saving) ,'PKR');

            $daily['dailyPeakHoursSaving'] = round($daily_peak_hour_saving[0],2).$daily_peak_hour_saving[1];
            $daily['dailyGenerationSaving'] = round($daily_generation_saving_arr[0],2).$daily_generation_saving_arr[1];
            $daily['dailyTotalSaving'] = round($daily_total_saving[0],2).$daily_total_saving[1];

            $monthly_processed_data = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
            // return $monthly_processed_data;
            $monthly_peak_hours_savings = $monthly_processed_data ? (double)$monthly_processed_data->monthly_peak_hours_discharge_energy * (int)$plant->peak_teriff_rate : 0;
            $monthly_generation_saving = $monthly_processed_data ? (double)$monthly_processed_data->monthlyGeneration * (int)$plant->benchmark_price : 0;

            $monthly_peak_hour_savings_arr = $this->unitConversion($monthly_peak_hours_savings, 'PKR');
            $monthly_generation_savings_arr = $this->unitConversion($monthly_generation_saving, 'PKR');
            $monthly_total_savings_arr = $this->unitConversion($monthly_peak_hours_savings + $monthly_generation_saving , 'PKR');

            $monthly['monthlyPeakHoursSaving'] =  round($monthly_peak_hour_savings_arr[0],2).$monthly_peak_hour_savings_arr[1];
            $monthly['monthlyGenerationSaving'] =round($monthly_generation_savings_arr[0],2).$monthly_generation_savings_arr[1];
            $monthly['monthlyTotalSaving'] = round($monthly_total_savings_arr[0],2).$monthly_total_savings_arr[1];

            $yearly_processed_data = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-01-01 0:00:00'), date('Y-m-d 23:59:00')])->latest()->first();
            $yearly_peak_hours_savings = $yearly_processed_data ? (double)$yearly_processed_data->yearly_peak_hours_discharge_energy * (int)$plant->peak_teriff_rate : 0;
            $yearly_generation_saving = $yearly_processed_data ? (double)$yearly_processed_data->yearlyGeneration * (int)$plant->benchmark_price : 0;

            $yearly_peak_hours_savings_arr = $this->unitConversion($yearly_peak_hours_savings, 'PKR');
            $yearly_generation_saving_arr = $this->unitConversion($yearly_generation_saving, 'PKR');
            $yearly_total_savings_arr = $this->unitConversion($yearly_peak_hours_savings + $yearly_generation_saving , 'PKR');

            $yearly['yearlyPeakHoursSaving'] =  round($yearly_peak_hours_savings_arr[0], 2) . '' . $yearly_peak_hours_savings_arr[1];
            $yearly['yearlyGenerationSaving'] = round($yearly_generation_saving_arr[0], 2) . '' . $yearly_generation_saving_arr[1];
            $yearly['yearlyTotalCostSaving'] = round($yearly_total_savings_arr[0],2).$yearly_total_savings_arr[1];
           
              

            $total['totalPeakHoursSaving'] =  round($yearly_peak_hours_savings_arr[0], 2) . '' . $yearly_peak_hours_savings_arr[1];
            $total['totalGenerationSaving'] =  round($yearly_generation_saving_arr[0], 2) . '' . $yearly_generation_saving_arr[1];
            $total['totalTotalCostSaving'] = round($yearly_total_savings_arr[0],2).$yearly_total_savings_arr[1];
            $daily_expected_generation = (ExpectedGenerationLog::where('plant_id', $plant->id)->orderBy('created_at', 'desc')->first()) ? ExpectedGenerationLog::where('plant_id', $plant->id)->orderBy('created_at', 'desc')->first()->daily_expected_generation : 0;
            $plant['cost_savings'] = ['daily' => $daily, 'monthly' => $monthly, 'yearly' => $yearly, 'total' => $total];
            //c-g
            $current_log = GenerationLog::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:00')])->orderBy('created_at', 'desc')->first();
            $plant['company_name'] = Company::where('id', $plant->company_id)->first()->company_name;
            $plant['company_pic'] = Company::where('id', $plant->company_id)->first()->logo;

            $plant['design_capacity'] = $plant->capacity;
            $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
            $plant['system_type'] = SystemType::find($plant->system_type)->type;

            $plant['expected_generation'] = $daily_expected_generation;

            //start
            $solisAppData = Setting::where('perimeter', 'solis_api_app_id')->first();

            if (!empty($solisAppData)) {
                $appID = $solisAppData->value;
            } else {
                $appID = '3yhg';
            }
            $solisAppKey = Setting::where('perimeter', 'solis_api_app_key')->first();
            if (!empty($solisAppKey)) {
                $appKey = $solisAppKey->value;
            } else {
                $appKey = '3yhg';
            }
            $solisUserAccount = Setting::where('perimeter', 'solis_api_user_account')->first();
            if (!empty($solisUserAccount)) {
                $userAccount = $solisUserAccount->value;
            } else {
                $userAccount = '3yhg';
            }
            $solisPassword = Setting::where('perimeter', 'solis_api_user_password')->first();
            $userPassword = !empty($solisPassword) ? $solisPassword->value : '3yhg';
            $solisOrgId = Setting::where('perimeter', 'solis_api_orgID')->first();
            $OrgId = !empty($solisOrgId) ? $solisOrgId->value : '3yhg';
            $solisApiBaseUrl = Setting::where('perimeter', 'solis_api_base_url')->first();
            $solisAPIBaseURL = !empty($solisApiBaseUrl) ? $solisApiBaseUrl->value : '';
            $solisController = new SolisController();
            $token = $solisController->getToken($solisAPIBaseURL, $appID, $appKey, $userAccount, $userPassword, $OrgId);
            $pl_sites_array = PlantSite::where('plant_id', $plant->id)->pluck('site_id')->toArray();
            $plant_inverters = InverterSerialNo::whereIn('site_id', $pl_sites_array)->get();
            $currentGeneration = 0;
            $currentConsumption = 0;
            $gridPower = 0;
            $batteryPower = 0;
            $batteryCapacity = 0;
            $gridImportEnergy = 0;
            $gridExportEnergy = 0;
            $dailyGeneration = 0;
            $dailyConsumption = 0;
            $daily_grid = 0;
            $daily_bought_energy = 0;
            $daily_sell_energy = 0;
            $daily_saving = 0;
            $daily_charge_energy = 0;
            $daily_discharge_energy = 0;
            $peak_hours_savings = 0;
            $generation_saving = 0;
            $totalCostSaving = 0;


            $plant['power'] = $daily_processed_data ? (string)$daily_processed_data['dailyMaxSolarPower'] : '0';

            $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $plant->id)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $plant->id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
            $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
            $current_data = ProcessedCurrentVariable::select('total_backup_Load','grid_Load','current_generation', 'current_consumption', 'current_grid', 'grid_type', 'comm_failed', 'battery_type', 'battery_power', 'battery_capacity', 'created_at')->whereDate('collect_time', date('Y-m-d'))->where('plant_id', $plant->id)->orderBy('collect_time', 'desc')->latest()->first();
         
            if($token && $plant->meter_type == "Solis" && $plant->grid_type != "Three-phase-string"){
                $inverterStatusArray = [];
                $updateSiteStatusArray = array();
                foreach ($plant_inverters as $smartKey => $smartInverter) {

                    $inverterData =
                        [
                            "deviceSn" => $smartInverter->dv_inverter,
                        ];


                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $solisAPIBaseURL . '/device/v1.0/currentData',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($inverterData),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $token,
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    $invertDataList = json_decode($response);

                    if(isset($invertDataList->deviceState)){
                        array_push($inverterStatusArray, $invertDataList->deviceState);
                    }

                    $lastRecordDate = Date('Y-m-d');
                    $collectTime = strtotime(Date('Y-m-d H:i:s'));
                    $solisHistoricalData = $solisController->getHistoricalData($solisAPIBaseURL, $smartInverter->dv_inverter, $lastRecordDate, $token);
                    $siteSmartInverterResponseData = json_decode($solisHistoricalData);

                    if ($siteSmartInverterResponseData && isset($siteSmartInverterResponseData->paramDataList)) {
                        $siteSmartInverterFinalData = $siteSmartInverterResponseData->paramDataList;
                        $siteSmartInverterFinalData =  array_reverse($siteSmartInverterFinalData);
                        if ($siteSmartInverterFinalData) {
                            foreach ($siteSmartInverterFinalData as $smartKeyData => $smartInverterFinalData) {
                                $responseData = isset($smartInverterFinalData->dataList) ? $smartInverterFinalData->dataList : [];
                                 $collectTime = $smartInverterFinalData->collectTime;

                                if ($plant->grid_type == 'Three-phase') {
                                    $keys = array_keys(array_column($responseData, 'key'), 'S_P_T');
                                    if ($keys && isset($responseData[$keys[0]]->value)) {
                                        $currentGeneration += ($responseData[$keys[0]]->value / 1000);
                                    }

                                } else {
                                    $keys = array_keys(array_column($responseData, 'key'), 'DPi_t1');
                                    if ($keys && isset($responseData[$keys[0]]->value)) {
                                        $currentGeneration += ($responseData[$keys[0]]->value / 1000);
                                    }
                                }
                                $dailyCurrentCons = array_keys(array_column($responseData, 'key'), 'E_Puse_t1');
                                if ($dailyCurrentCons && isset($responseData[$dailyCurrentCons[0]]->value)) {
                                    $currentConsumption += $responseData[$dailyCurrentCons[0]]->value;
                                }
                                //                                $keys = array_keys(array_column($responseData, 'key'), 'B_left_cap1');
                                //                                if ($keys) {
                                //                                    $batteryCapacity = $responseData[$keys[0]]->value . $responseData[$keys[0]]->unit;
                                //                                }
                                //                                $batteryPower = array_keys(array_column($responseData, 'key'), 'B_P1');
                                //                                if ($batteryPower) {
                                //                                    $batteryPower = $responseData[$batteryPower[0]]->value;
                                //                                }

                                $keys = array_keys(array_column($responseData, 'key'), 'B_left_cap1');
                                if ($keys && isset($responseData[$keys[0]]->value)) {
                                    $batteryCapac= (int)$responseData[$keys[0]]->value;
                                    $batteryCapacity += $batteryCapac;
                                }
                                $batteryPowerd = array_keys(array_column($responseData, 'key'), 'B_P1');
                                if ($batteryPowerd && isset($responseData[$batteryPowerd[0]]->value)) {
                                    $batteryPower += $responseData[$batteryPowerd[0]]->value;
                                }
                                $totalGridPowerData = array_keys(array_column($responseData, 'key'), 'PG_Pt1');
                                if ($totalGridPowerData && isset($responseData[$totalGridPowerData[0]]->value)) {
                                    $gridPower += $responseData[$totalGridPowerData[0]]->value;
                                }
                                if ($gridPower) {
                                    $gridPower = (($gridPower / 1000));
                                }
                                $dailyGen = array_keys(array_column($responseData, 'key'), 'Etdy_ge1');
                                if ($dailyGen && isset($responseData[$dailyGen[0]]->value)) {
                                    $dailyGeneration += $responseData[$dailyGen[0]]->value;
                                }
                                $dailyCons = array_keys(array_column($responseData, 'key'), 'Etdy_use1');
                                if ($dailyCons && isset($responseData[$dailyCons[0]]->value)) {
                                    $dailyConsumption += $responseData[$dailyCons[0]]->value;
                                }
                                $dailyChargeEnergy = array_keys(array_column($responseData, 'key'), 'Etdy_cg1');
                                if ($dailyChargeEnergy && isset($responseData[$dailyChargeEnergy[0]]->value)) {
                                    $daily_charge_energy += $responseData[$dailyChargeEnergy[0]]->value;
                                }
                                $dailyDischargeEnergy = array_keys(array_column($responseData, 'key'), 'Etdy_dcg1');
                                if ($dailyDischargeEnergy && isset($responseData[$dailyDischargeEnergy[0]]->value)) {
                                    $daily_discharge_energy += $responseData[$dailyDischargeEnergy[0]]->value;
                                }
                                if ($plant->grid_type == "Three-phase") {
                                    $totalGridDailyEnergyData = array_keys(array_column($responseData, 'key'), 'E_B_D');
                                    if ($totalGridDailyEnergyData && isset($responseData[$totalGridDailyEnergyData[0]]->value)) {
                                        $gridImportEnergy += $responseData[$totalGridDailyEnergyData[0]]->value;
                                    }
                                } else {
                                    $totalGridDailyEnergyData = array_keys(array_column($responseData, 'key'), 'Etdy_pu1');
                                    if ($totalGridDailyEnergyData && isset($responseData[$totalGridDailyEnergyData[0]]->value)) {
                                        $gridImportEnergy += $responseData[$totalGridDailyEnergyData[0]]->value;
                                    }
                                }

                                if ($plant->grid_type == "Three-phase") {
                                    $totalGridDailyFeedData = array_keys(array_column($responseData, 'key'), 'E_S_D');
                                    if ($totalGridDailyFeedData && isset($responseData[$totalGridDailyFeedData[0]]->value)) {
                                        $gridExportEnergy += $responseData[$totalGridDailyFeedData[0]]->value;
                                    }
                                } else {
                                    $totalGridDailyFeedData = array_keys(array_column($responseData, 'key'), 't_gc_tdy1');
                                    if ($totalGridDailyFeedData && isset($responseData[$totalGridDailyFeedData[0]]->value)) {
                                        $gridExportEnergy += $responseData[$totalGridDailyFeedData[0]]->value;
                                    }
                                }
                                break;
                            }
                        }
                    }
                }

                $updateSiteStatusArray['online_status'] = 'Y';


                if (in_array(1, $inverterStatusArray)) {

                    $updateSiteStatusArray['online_status'] = 'Y';
                }

                if (in_array(2, $inverterStatusArray)) {

                    $updateSiteStatusArray['online_status'] = 'A';
                }

                if (in_array(2, $inverterStatusArray) && in_array(1, $inverterStatusArray)) {

                    $updateSiteStatusArray['online_status'] = 'P_Y';
                }

                if (in_array(3, $inverterStatusArray)) {

                    $updateSiteStatusArray['online_status'] = 'N';
                }
                $status = 'default';
                if ($inverterStatusArray) {
                    $statusArray = array_unique($inverterStatusArray);
                    if ($statusArray) {
                        $status = $statusArray[0];
                    }

                }
                if (in_array(1, $inverterStatusArray)) {

                    $updateSiteStatusArray['online_status'] = 'Y';
                }

                $plant['is_online'] = $updateSiteStatusArray['online_status'];
                $plantDailyBoughtEnergy = $gridImportEnergy;
                $plantDailySell = $gridExportEnergy;

                $current['date'] = date('Y-m-d H:i:s');
                $current['comm_fail'] = 0;
                $current_grid_type = $gridPower > 0 ? '+ve' : '-ve';
                $batteryType = $batteryPower > 0 ? '+ve' : '-ve';
                $daily_charge_energy = (double)$daily_charge_energy;
                $daily_discharge_energy = (double)$daily_discharge_energy;
                $daily_grid_data = $plantDailyBoughtEnergy > $plantDailySell ? $plantDailyBoughtEnergy - $plantDailySell : $plantDailySell - $plantDailyBoughtEnergy;
                $daily_grid = $daily_grid_data;
                $daily_bought_energy = (double)$plantDailyBoughtEnergy;
                $daily_sell_energy = (double)$plantDailySell;
                $plant['current_generation'] = $currentGeneration ? number_format((double)$currentGeneration, 2) : '0';
                if ($plant->system_type_data == 2 || $plant->system_type_data == 1) {
                    if($plant->meter_type == "Solis"){
                        $plant['current_consumption'] = (string)round(isset($currentConsumption) ? $currentConsumption : 0, 2);
                    }
                } else {
                    $plant['current_consumption'] = (string)round(isset($currentConsumption) ? $currentConsumption : 0, 2);

                }
                $plant['battery_power'] = $batteryPower ? number_format($batteryPower, 2) : '0';
                $plant['battery_type'] = $batteryType ? $batteryType : '';
                if(empty($batteryCapacity)){
                    $plant['battery_soc'] = '0%';
                }else{
                    if($batteryCapacity > 100){
                        $plant['battery_soc'] = '100%';
                    }else{
                        $plant['battery_soc'] = isset($batteryCapacity) ? $batteryCapacity."%" : '100%';
                    }
                }
                $plant['current_gird_import_power'] = $gridPower ? number_format(abs($gridPower), 2) : '0';
                $plant['current_gird_export_power'] = $gridPower ? number_format(abs($gridPower), 2) : '0';
                $plant['current_grid_type'] = $current_grid_type ? $current_grid_type : '';
                $plant['daily_generation'] = $dailyGeneration ? number_format($dailyGeneration, 2) : '0.00';
                $plant['daily_consumption'] = $dailyConsumption ? number_format($dailyConsumption, 2) : '0.00';
                $plant['daily_energy_bought'] = $plantDailyBoughtEnergy ? number_format($plantDailyBoughtEnergy, 2) : '0.00';
                $plant['daily_energy_sell'] = $plantDailySell ? number_format($plantDailySell, 2) : '0.00';
                $dailySaving =  $dailyGeneration * $plant->benchmark_price;
                $plant['daily_revenue'] = $dailySaving ? number_format($dailySaving, 2) : '0.00';
                $plant['last_updated'] = date('h:i A, d/m',$collectTime);
            }else{
                $plant['battery_power'] = $current_data ? number_format($current_data->battery_power, 2) : '0';
                if ($current_data) {
                    $plant['battery_type'] = $current_data ? $current_data->battery_type : '';
                } else {
                    $plant['battery_type'] = '';
                }
                $plant['battery_soc'] = StationBattery::where('plant_id', $plant->id)->orderBy('collect_time', 'DESC')->first();
                if ($plant['battery_soc']) {
                    $plant['battery_soc'] = $plant['battery_soc']->battery_capacity;
                } else {
                    $plant['battery_soc'] = '';
                }
                $current_backup_load = $current_data ? (double)$current_data->total_backup_Load : 0;
                $current_grid_load = $current_data ? (double)$current_data->grid_Load : 0;
                $current_backup_load_arr = $this->unitConversion($current_backup_load, 'kW');
                $current_grid_load_arr = $this->unitConversion($current_grid_load, 'kW');
                $plant['current_grid_load']= round($current_grid_load_arr[0], 2) . ' ' . $current_grid_load_arr[1];
                $plant['current_backup_load'] = round($current_backup_load_arr[0], 2) . ' ' . $current_backup_load_arr[1];
                $plant['current_generation'] = $current_data ? number_format((double)$current_data->current_generation, 2) : '0';
      
                $curr_con_arr = $this->unitConversion(isset($current_data->current_consumption) ? $current_data->current_consumption : 0, 'W');

                if ($plant->system_type_data == 2 || $plant->system_type_data == 1) {
                    if($plant->meter_type == "Solis"){
                        $plant['current_consumption'] = (string)round(isset($current_data->current_consumption) ? $current_data->current_consumption : 0, 2);
                    }else{
                        if ($curr_con_arr[0] > 0) {
                            $curr_con_arr[0] = $curr_con_arr[0] * 1000;
                        }
                        $plant['current_consumption'] = (string)round($curr_con_arr[0], 2);
                    }
                } else {
                    $plant['current_consumption'] = (string)round(isset($current_data->current_consumption) ? $current_data->current_consumption : 0, 2);
                }

                $plant['current_gird_import_power'] = $current_data ? number_format((double)$current_data->current_grid, 2) : '0';
                $plant['current_gird_export_power'] = $current_data ? number_format((double)$current_data->current_grid, 2) : '0';
                $plant['current_grid_type'] = $current_data ? $current_data->grid_type : '';
                $plant['daily_generation'] = $daily_processed_data ? number_format($daily_processed_data->dailyGeneration, 2) : '0';
                $plant['daily_consumption'] = $daily_processed_data ? number_format($daily_processed_data->dailyConsumption, 2) : '0';
                $plant['daily_energy_bought'] = $daily_processed_data ? number_format($daily_processed_data->dailyBoughtEnergy, 2) : '0';
                $plant['daily_energy_sell'] = $daily_processed_data ? number_format($daily_processed_data->dailySellEnergy, 2) : '0';
                $plant['daily_revenue'] = $daily_processed_data ? number_format($daily_processed_data->dailySaving, 2) : '0';
                // return 'true';
               
                $daily_gridLoad_arr = $this->unitConversion($daily_gridLoad, 'kWh');
                $daily_backupLoad_arr = $this->unitConversion($daily_backupLoad, 'kWh');
                $plant['daily_grid_load'] = round($daily_gridLoad_arr[0], 2) . ' ' . $daily_gridLoad_arr[1];
                $plant['daily_backup_load'] = round($daily_backupLoad_arr[0], 2) . ' ' . $daily_backupLoad_arr[1];
                $plant['last_updated'] = date('h:i A, d/m', strtotime($plant->updated_at));
            }

            $batteryValues = StationBattery::where('plant_id', $plant->id)->orderBy('collect_time', 'desc')->first();
            $batteryData = StationBatteryData::where('plant_id', $plant->id)->latest()->first();
            $plantBatteryAh = isset($batteryData) ? (int)$batteryData['battery_ah'] : 0;
            $plantBatteryVoltage = isset($batteryData) ? (double)$batteryData['battery_voltage'] : 0;
            $batteryDOD = isset($batteryData) ? (int)$batteryData['battery_dod'] : 0;
            $batteryCapacity = isset($batteryValues) ? (int)$batteryValues['battery_capacity'] : 0;
            $batteryRatedPower = isset($batteryValues) ? (int)$batteryValues['rated_power'] : 0;

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

            if ($batteryRatedPower == 0) {
                $batteryBackupMaxLoad = ($batteryRemaining / (0.9));
            } else {
                $batteryBackupMaxLoad = ($batteryRemaining / (0.9 * ($batteryRatedPower / 1000)));
            }

            $batteryBackupFormula = round($batteryBackup, 2);
            $batteryBackupMaxLoadFormula = round($batteryBackupMaxLoad, 2);

            if ($plant['current_consumption'] < 30) {
                $batteryBackupFormula = 'No Load State';
            }
            $plant['battery_information'] = ['batter-remaining' => (string)$batteryRemaining, 'battery_back_up_current_load' => (string)$batteryBackupFormula, 'battery_backup_max_load' => (string)$batteryBackupMaxLoadFormula];

            $plant['comm_fail'] = $current_data ? ($current_data->comm_failed == 1 ? 'Power Outage or Communication Failure' : '') : '';


            $percentage_value = $current_log ? ($current_log->current_generation / $plant->capacity * 100) : 0;
            $plant['percentage_value'] = number_format($percentage_value, 2);
            $total_processed_data = TotalProcessedPlantDetail::where('plant_id', $plant->id)->select('plant_total_grid_load','plant_total_backup_load')->first();
        
            $total_processed_grid_load = $total_processed_data ? (double)$total_processed_data->plant_total_grid_load : 0;
            $total_processed_backup_load = $total_processed_data ? (double)$total_processed_data->plant_total_backup_load : 0;
            $total_processed_grid_Load_arr = $this->unitConversion($total_processed_grid_load, 'kWh');
            $total_processed_backup_load_arr = $this->unitConversion($total_processed_backup_load, 'kWh');
            $plant['total_processed_grid_load'] = round($total_processed_grid_Load_arr[0], 2) . ' ' . $total_processed_grid_Load_arr[1];
            $plant['total_processed_backup_load'] = round($total_processed_backup_load_arr[0], 2) . ' ' . $total_processed_backup_load_arr[1];

           
            $monthly_grid_load = $monthly_processed_data ? (double)$monthly_processed_data->monthlyGridLoad : 0;
            $monthly_backup_load = $monthly_processed_data ? (double)$monthly_processed_data->monthlyBackupLoad : 0;
            $monthly_grid_Load_arr = $this->unitConversion($monthly_grid_load, 'kWh');
            $monthly_backup_load_arr = $this->unitConversion($monthly_backup_load, 'kWh');
            $plant['monthly_backup_load'] = round($monthly_backup_load_arr[0], 2) . ' ' . $monthly_backup_load_arr[1];
            $plant['monthly_grid_load'] = round($monthly_grid_Load_arr[0], 2) . ' ' . $monthly_grid_Load_arr[1];
           
            $plant['monthly_generation'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyGeneration, 2) : '0';
           
             $yearly_grid_load = $yearly_processed_data ? (double)$yearly_processed_data->yearlyGridLoad : 0;
             $yearly_backup_Load = $yearly_processed_data ? (double)$yearly_processed_data->yearlyBackupLoad : 0;
             $yearly_grid_load_arr = $this->unitConversion($yearly_grid_load, 'kWh');
             $yearly_backup_load_arr = $this->unitConversion($yearly_backup_Load, 'kWh');
            $plant['yearly_backup_load'] = round($yearly_backup_load_arr[0], 2) . ' ' . $yearly_backup_load_arr[1];
            $plant['yearly_grid_load'] = round($yearly_grid_load_arr[0], 2) . ' ' . $yearly_grid_load_arr[1];
            $plant['yearly_generation'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlyGeneration, 2) : '0';
            $plant['monthly_consumption'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyConsumption, 2) : '0';
            $plant['yearly_consumption'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlyConsumption, 2) : '0';
            $plant['monthly_energy_bought'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyBoughtEnergy, 2) : '0';
            $plant['monthly_energy_sell'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlySellEnergy, 2) : '0';
            $plant['monthly_revenue'] = $monthly_processed_data ? number_format(abs($monthly_processed_data->monthlySaving), 2) : '0';
            $plant['yearly_revenue'] = $yearly_processed_data ? number_format(abs($yearly_processed_data->yearlySaving), 2) : '0';



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

            if ($plant->plant_pic != null) {

                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/' . $plant->plant_pic;
            } else {
                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/plant_avatar.png';
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


//plant detail normal
    public
    function plantDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'plant_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }
        $plantID = $request->get('plant_id');
        $userID = $request->user()->id;

        $plants = Plant::where('id', $plantID)->get();

        if (count($plants) == 0) {
            return $this->sendError(0, 'Plant not found.');
        }


        $plants = $plants->map(function ($plant) {

            $plant['system_type_data'] = $plant->system_type;

            $daily_processed_data = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
            $dailyTimeArray = [];
            if (!$daily_processed_data) {
                $daily_processed_data = [];
            }
            $dataArray = json_decode(json_encode($daily_processed_data), true);

            for ($i = 0; $i < count($dataArray); $i++) {

                if (isset($dataArray[$i]) && $dataArray[$i]['daily_outage_grid_voltage'] != null) {
                    $dailyOutagesHoursData = explode(':', $dataArray[$i]['daily_outage_grid_voltage']);
                    $dailyTimeArray[] = $dailyOutagesHoursData[0] . ':' . $dailyOutagesHoursData[1] . ':00';
                }
            }
            $totalSeconds = 0;
            foreach ($dailyTimeArray as $t) {
                $totalSeconds += $this->toSeconds($t);
            }

            $dailyOutagesGridValue = $this->toTimeCalculation($totalSeconds);

            $explodeDailyData = explode(':', $dailyOutagesGridValue);
            if ($explodeDailyData[0] == 0) {
                $explodeDailyData[0] = '00';
            }
            if ($explodeDailyData[1] == 0) {
                $explodeDailyData[1] = '00';
            }
            $outagesHours = $explodeDailyData[0] . ':' . $explodeDailyData[1];
            $plant['outages_served'] = $outagesHours;
            if (!empty($daily_processed_data)) {
                $daily_generation = $daily_processed_data ? (double)$daily_processed_data->dailyGeneration : 0;
                $daily_consumption = $daily_processed_data ? (double)$daily_processed_data->dailyConsumption : 0;
                $daily_grid = $daily_processed_data ? (double)$daily_processed_data->dailyGridPower : 0;
                $daily_bought_energy = $daily_processed_data ? (double)$daily_processed_data->dailyBoughtEnergy : 0;
                $daily_sell_energy = $daily_processed_data ? (double)$daily_processed_data->dailySellEnergy : 0;
                $daily_saving = $daily_processed_data ? (double)$daily_processed_data->dailySaving : 0;
                $daily_charge_energy = $daily_processed_data ? (double)$daily_processed_data->daily_charge_energy : 0;
                $daily_discharge_energy = $daily_processed_data ? (double)$daily_processed_data->daily_discharge_energy : 0;
                $peak_hours_savings = $daily_processed_data ? (double)$daily_processed_data->daily_peak_hours_battery_discharge * (int)$plant->peak_teriff_rate : 0;
                $generation_saving = $daily_processed_data ? (double)$daily_processed_data->dailyGeneration * (int)$plant->benchmark_price : 0;
                $totalCostSaving = $daily_processed_data ? (double)$daily_processed_data->dailySaving : 0;
//                $daily['date'] = $daily_processed_data ? $daily_processed_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
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

            }
            $daily_charge_arr = $this->unitConversion($daily_charge_energy, 'kWh');
            $daily_discharge_arr = $this->unitConversion($daily_discharge_energy, 'kWh');

            $daily['dailyPeakHoursSaving'] = (string)round($peak_hours_savings, 2);
            $daily['dailyGenerationSaving'] = (string)round($generation_saving, 2);
            $daily['dailyTotalSaving'] = (string)round($daily['dailyPeakHoursSaving'] + $daily['dailyGenerationSaving'], 2);

            $monthly_processed_data = MonthlyProcessedPlantDetail::where('plant_id', $plant->id)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
            $monthly_peak_hours_savings = $monthly_processed_data ? (double)$monthly_processed_data->monthly_peak_hours_discharge_energy * (int)$plant->peak_teriff_rate : 0;
            $monthly_generation_saving = $monthly_processed_data ? (double)$monthly_processed_data->monthlyGeneration * (int)$plant->benchmark_price : 0;
            $monthly['monthlyPeakHoursSaving'] = (string)round($monthly_peak_hours_savings, 2);
            $monthly['monthlyGenerationSaving'] = (string)round($monthly_generation_saving, 2);
            $monthly['monthlyTotalSaving'] = (string)round($monthly['monthlyPeakHoursSaving'] + $monthly['monthlyGenerationSaving'], 2);
            $yearly_processed_data = YearlyProcessedPlantDetail::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-01-01 0:00:00'), date('Y-m-d 23:59:00')])->latest()->first();
            $yearly_peak_hours_savings = $yearly_processed_data ? (double)$yearly_processed_data->yearly_peak_hours_discharge_energy * (int)$plant->peak_teriff_rate : 0;
            $yearly_generation_saving = $yearly_processed_data ? (double)$yearly_processed_data->yearlyGeneration * (int)$plant->benchmark_price : 0;
            $yearly['yearlyPeakHoursSaving'] = (string)round($yearly_peak_hours_savings, 2);
            $yearly['yearlyGenerationSaving'] = (string)round($yearly_generation_saving, 2);
            $yearly['yearlyTotalCostSaving'] = (string)round($yearly['yearlyPeakHoursSaving'] + $yearly['yearlyGenerationSaving'], 2);
            $total['totalPeakHoursSaving'] = (string)round($yearly_peak_hours_savings, 2);
            $total['totalGenerationSaving'] = (string)round($yearly_generation_saving, 2);
            $total['totalTotalCostSaving'] = (string)round($total['totalPeakHoursSaving'] + $total['totalGenerationSaving'], 2);
            $daily_expected_generation = (ExpectedGenerationLog::where('plant_id', $plant->id)->orderBy('created_at', 'desc')->first()) ? ExpectedGenerationLog::where('plant_id', $plant->id)->orderBy('created_at', 'desc')->first()->daily_expected_generation : 0;
            $plant['cost_savings'] = ['daily' => $daily, 'monthly' => $monthly, 'yearly' => $yearly, 'total' => $total];
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
            $current_data = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type', 'comm_failed', 'battery_type', 'battery_power', 'battery_capacity', 'created_at')->whereDate('collect_time', date('Y-m-d'))->where('plant_id', $plant->id)->orderBy('collect_time', 'desc')->latest()->first();
            $plant['battery_power'] = $current_data ? number_format($current_data->battery_power, 2) : '0';
            if ($current_data) {
                $plant['battery_type'] = $current_data ? $current_data->battery_type : '';
            } else {
                $plant['battery_type'] = '';
            }
            $plant['battery_soc'] = StationBattery::where('plant_id', $plant->id)->orderBy('collect_time', 'DESC')->first();
            if ($plant['battery_soc']) {
                $plant['battery_soc'] = $plant['battery_soc']->battery_capacity;
            } else {
                $plant['battery_soc'] = '';
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

            if ($batteryRatedPower == 0) {
                $batteryBackupMaxLoad = ($batteryRemaining / (0.9));
            } else {
                $batteryBackupMaxLoad = ($batteryRemaining / (0.9 * ($batteryRatedPower / 1000)));
            }

            // $batteryBackupFormula = round($batteryBackup, 2);
             if($plant->meter_type = "Solis-Cloud" && $current_backup_load != 0){
             $batteryBackupFormula = ($batteryRemaining / $current_backup_load);
             }else{
             $batteryBackupFormula = round($batteryBackup, 2);
             }
            $batteryBackupMaxLoadFormula = round($batteryBackupMaxLoad, 2);

            $curr_con_arr = $this->unitConversion(isset($current_data->current_consumption) ? $current_data->current_consumption : 0, 'W');

            if ($plant->system_type_data == 2 || $plant->system_type_data == 1) {
                if($plant->meter_type == "Solis"){
                    $plant['current_consumption'] = (string)round(isset($current_data->current_consumption) ? $current_data->current_consumption : 0, 2);
                }else{
                    if ($curr_con_arr[0] > 0) {
                        $curr_con_arr[0] = $curr_con_arr[0] * 1000;
                    }
                    $plant['current_consumption'] = (string)round($curr_con_arr[0], 2);
                }
            } else {
                $plant['current_consumption'] = (string)round(isset($current_data->current_consumption) ? $current_data->current_consumption : 0, 2);
            }


            $plant['current_generation'] = $current_data ? number_format((double)$current_data->current_generation, 2) : '0';

            if ($plant['current_consumption'] < 30) {
                $batteryBackupFormula = 'No Load State';
            }
            $plant['battery_information'] = ['batter-remaining' => (string)$batteryRemaining, 'battery_back_up_current_load' => (string)$batteryBackupFormula, 'battery_backup_max_load' => (string)$batteryBackupMaxLoadFormula];

            $plant['current_gird_import_power'] = $current_data ? number_format((double)$current_data->current_grid, 2) : '0';
            $plant['current_gird_export_power'] = $current_data ? number_format((double)$current_data->current_grid, 2) : '0';
            $plant['current_grid_type'] = $current_data ? $current_data->grid_type : '';
            $plant['comm_fail'] = $current_data ? ($current_data->comm_failed == 1 ? 'Power Outage or Communication Failure' : '') : '';


            $percentage_value = $current_log ? ($current_log->current_generation / $plant->capacity * 100) : 0;
            $plant['percentage_value'] = number_format($percentage_value, 2);

            $plant['daily_generation'] = $daily_processed_data ? number_format($daily_processed_data->dailyGeneration, 2) : '0';
            $plant['monthly_generation'] = $monthly_processed_data ? number_format($monthly_processed_data->monthlyGeneration, 2) : '0';
            $plant['yearly_generation'] = $yearly_processed_data ? number_format($yearly_processed_data->yearlyGeneration, 2) : '0';
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

            if ($plant->plant_pic != null) {

                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/' . $plant->plant_pic;
            } else {
                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/plant_avatar.png';
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

    public function plantInverterDetail(Request $request)
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

        if ($plants == null) {
            return $this->sendError(0, 'Plant not found.');
        }

        $pl_sites_array = PlantSite::where('plant_id', $plants->id)->pluck('site_id')->toArray();
        $inverters = InverterSerialNo::whereIn('site_id', $pl_sites_array)->get();

        for ($k = 0; $k < count($inverters); $k++) {
            if (gettype($inverters[$k]['plant_id']) == 'integer') {
                $inverters[$k]['plant_id'] = (string)$inverters[$k]['plant_id'];
            }
            $inverters[$k]['inverter_type_id'] = (string)$inverters[$k]['inverter_type_id'];
        }
        $inverterTotalACOutputPower = 0;
        $plant['inverters'] = $inverters->map(function ($inverter) {

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

            $inverterFinalCurrentDataDateTime = $inverterCurrentDataLogTime;
//            $inverterFinalCurrentDataDateTime = $this->previousTenMinutesDateTime($inverterCurrentDataLogTime);
            $inverterDetailObject = InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $inverterFinalCurrentDataDateTime)->orderBy('collect_time', 'DESC')->first();
            if ($inverterDetailObject) {
                $inverter['ac_output_power'] = (string)$inverterDetailObject->inverterPower;
            } else {
                $inverter['ac_output_power'] = '0';
            }

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

        return $this->sendResponse(1, 'Showing plant inverter details', $plant);
    }

    public
    function plant_chart(Request $request)
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
        if (isset($input['parameter'])) {
            $parameter = ucfirst($input['parameter']);
        } else {
            $parameter = '';
        }
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
//            return $res;

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

    private
    function getDailyData($plant_id, $parameter, $date)
    {
//        return $date;

        $hourly_data = array();
        $daily_total = 0;
        $plant_detail = Plant::whereIn('id', $plant_id)->get(['id', 'meter_type', 'benchmark_price']);
        $benchmark_price = count($plant_detail) > 0 ? $plant_detail[0]->benchmark_price : 0;
        $meter_type = count($plant_detail) > 0 ? $plant_detail[0]->meter_type : '';

        if (strtolower($parameter) == 'generation') {

            $current_generation_start_time = ProcessedCurrentVariable::select('collect_time')->where('plant_id', $plant_id)->whereDate('collect_time', $date)->where('current_generation', '>', 0)->orderBy('collect_time', 'ASC')->first();
            $start_date_time = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->collect_time)) : '06:00:00';

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
//
        } else if (strtolower($parameter) == 'consumption') {

            $current_generation_start_time = ProcessedCurrentVariable::select('collect_time')->where('plant_id', $plant_id)->whereDate('collect_time', $date)->where('current_generation', '>', 0)->orderBy('collect_time', 'ASC')->first();
            $start_date_time = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->collect_time)) : '06:00:00';

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
            $start_date_time = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->collect_time)) : '06:00:00';

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

    private function getDailyGraphData($SystemType, $plants, $plant_id, $plantType, $parameter, $date)
    {

        $hourly_data = array();
        $daily_total = 0;
        $plant_detail = Plant::whereIn('id', $plant_id)->get(['id', 'meter_type', 'benchmark_price']);
        $benchmark_price = count($plant_detail) > 0 ? $plant_detail[0]->benchmark_price : 0;
        $meter_type = count($plant_detail) > 0 ? $plant_detail[0]->meter_type : '';
        if($plants == "all" && $SystemType == '4'){
            $timearrayNew = [];
            $DataArray = [];
            $StartTime = $date . ' 00:00:00';

            for($k = 0; $k< 288 ;$k++){

                $Date = strtotime($StartTime)+(60*5);
                $EndTime = date("Y-m-d H:i:s",$Date);

                if($date == Date('Y-m-d')){
                    $isExsistData = ProcessedCurrentVariable::Select('collect_time', 'plant_id')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->exists();
                }else{
                    $isExsistData = ProcessedCurrentVariableHistory::Select('collect_time', 'plant_id')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->exists();
                }
                if($isExsistData){
                    $current_gen['plant_id'] = $plants;
                    $current_gen['plant_type'] = $plantType;
                    $current_gen['time_type'] = 'Daily';
                    $current_gen['collect_time'] = Date('Y-m-d', strtotime($StartTime));
                    if($date == Date('Y-m-d')){
                        $current_gen['generation'] = (double)round(ProcessedCurrentVariable::Select('collect_time', 'plant_id')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_generation'),2);
                        $consumption = (double)round(ProcessedCurrentVariable::Select('collect_time', 'plant_id')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_consumption'),2);
                        $current_gen['consumption'] = (double)round($consumption/1000, 2);
                        $current_gen['buy_energy'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->where('grid_type', '+ve')->sum('current_grid'), 2);
                        $current_gen['sell_energy'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->where('grid_type', '-ve')->sum('current_grid'), 2);
                        $current_gen['grid'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_grid'), 2);
                        $current_gen['saving'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_saving'), 2);
                        $batteryPower = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->latest()->first();
                    }else{
                        $current_gen['generation'] = (double)round(ProcessedCurrentVariableHistory::Select('collect_time', 'plant_id')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_generation'),2);
                        $consumption = (double)round(ProcessedCurrentVariableHistory::Select('collect_time', 'plant_id')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_consumption'),2);
                        $current_gen['consumption'] = (double)round($consumption/1000, 2);
                        $current_gen['buy_energy'] = (double)round(ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->where('grid_type', '+ve')->sum('current_grid'), 2);
                        $current_gen['sell_energy'] = (double)round(ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->where('grid_type', '-ve')->sum('current_grid'), 2);
                        $current_gen['grid'] = (double)round(ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_grid'), 2);
                        $current_gen['saving'] = (double)round(ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_saving'), 2);
                        $batteryPower = ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->latest()->first();
                    }

                    $batteryData = 0.00;
                    $batterySoc = 0.00;
                    if ($batteryPower) {
                        if ($batteryPower->battery_power != null) {
                            $batteryData = $batteryPower->battery_power;
                        }
                        $batterySoc = $batteryPower->battery_capacity;
                    }
                    $current_gen['battery_power'] = (double)round($batteryData, 2);
                    $current_gen['battery_soc'] = (double)$batterySoc;
                }else{
                    $current_gen['plant_id'] = $plants;
                    $current_gen['plant_type'] = $plantType;
                    $current_gen['time_type'] = 'Daily';
                    $current_gen['collect_time'] = Date('Y-m-d', strtotime($StartTime));
                    $current_gen['generation'] = null;
                    $current_gen['consumption'] = null;
                    $current_gen['buy_energy'] = null;
                    $current_gen['sell_energy'] = null;
                    $current_gen['grid'] = null;
                    $current_gen['saving'] = null;
                    $current_gen['battery_power'] = null;
                    $current_gen['battery_soc'] = null;
                }

                $current_gen['time'] = Date('H:i' ,strtotime($StartTime));
                array_push($DataArray , $current_gen);
                $Date = strtotime($StartTime)+(60*5);
                $StartTime = date("Y-m-d H:i:s",$Date);
                array_push($timearrayNew,$StartTime);

            }

            if (DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->exists()) {

                $dataResponse = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->get();

                $DailyDataArray = json_decode(json_encode($dataResponse), true);
                $dailyGenera = array_sum(array_column($DailyDataArray, 'dailyGeneration'));
                $dailyConsum = array_sum(array_column($DailyDataArray, 'dailyConsumption'));
                $dailyGrid = array_sum(array_column($DailyDataArray, 'dailyGridPower'));
                $dailyBought = array_sum(array_column($DailyDataArray, 'dailyBoughtEnergy'));
                $dailySell = array_sum(array_column($DailyDataArray, 'dailySellEnergy'));
                $dailySav = array_sum(array_column($DailyDataArray, 'dailySaving'));
                $dailyCharge_ener = array_sum(array_column($DailyDataArray, 'daily_charge_energy'));
                $dailyDis_charge = array_sum(array_column($DailyDataArray, 'daily_discharge_energy'));

                $totalGeneration = (string)round($dailyGenera, 2);
                $totalConsumption = (string)round($dailyConsum, 2);
                $totalBuy = (string)round($dailyBought, 2);
                $totalSell = (string)round($dailySell, 2);
                $totalGrid = (string)round($totalBuy - $totalSell,2);
                $totalSaving = (string)$dailySav;
                $totalChargeEnergy = (string)round($dailyCharge_ener, 2);
                $totalDischargeEnergy = (string)round($dailyDis_charge, 2);
            } else {
                $totalGeneration = "0.00";
                $totalConsumption = "0.00";
                $totalGrid = "0.00";
                $totalBuy = "0.00";
                $totalSell = "0.00";
                $totalSaving = "0";
                $totalChargeEnergy = "0.00";
                $totalDischargeEnergy = "0.00";
            }

            $daily_total = ['generation' => $totalGeneration, 'consumption' => $totalConsumption, 'grid' => $totalGrid, 'buy' => $totalBuy, 'sell' => $totalSell, 'saving' => $totalSaving, 'charge' => $totalChargeEnergy, 'discharge' => $totalDischargeEnergy];
            return [$DataArray, $daily_total];

        }else {

//            return $date;
            if($date == Date('Y-m-d')){
                $hourly_val = ProcessedCurrentVariable::Select('collect_time', 'plant_id','grid_type')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:00')])->groupBy('collect_time')->get();;
            }else{
                $hourly_val = ProcessedCurrentVariableHistory::Select('collect_time', 'plant_id','grid_type')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:00')])->groupBy('collect_time')->get();;
            }
//            $hourly_val = ProcessedCurrentVariable::Select('collect_time', 'plant_id','grid_type')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:00')])->groupBy('collect_time')->get();;

            if (DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->exists()) {

                $dataResponse = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->get();
                $batteryDetail = StationBattery::where('plant_id', $plant_id)->orderBy('collect_time', 'DESC')->first();
                if (isset($batteryDetail->battery_capacity)) {
                    $totalBatterySoc = $batteryDetail->battery_capacity;
                }
                $DailyDataArray = json_decode(json_encode($dataResponse), true);
                $dailyGenera = array_sum(array_column($DailyDataArray, 'dailyGeneration'));
                $dailyConsum = array_sum(array_column($DailyDataArray, 'dailyConsumption'));
                $dailyGrid = array_sum(array_column($DailyDataArray, 'dailyGridPower'));
                $dailyBought = array_sum(array_column($DailyDataArray, 'dailyBoughtEnergy'));
                $dailySell = array_sum(array_column($DailyDataArray, 'dailySellEnergy'));
                $dailySav = array_sum(array_column($DailyDataArray, 'dailySaving'));
                $dailyCharge_ener = array_sum(array_column($DailyDataArray, 'daily_charge_energy'));
                $dailyDis_charge = array_sum(array_column($DailyDataArray, 'daily_discharge_energy'));

                $totalGeneration = (string)round($dailyGenera, 2);
                $totalConsumption = (string)round($dailyConsum, 2);
                $totalGrid = (string)round($dailyGrid, 2);
                $totalBuy = (string)round($dailyBought, 2);
                $totalSell = (string)round($dailySell, 2);
                $totalSaving = (string)$dailySav;
                $totalChargeEnergy = (string)round($dailyCharge_ener, 2);
                $totalDischargeEnergy = (string)round($dailyDis_charge, 2);
            } else {
                $totalGeneration = "0.00";
                $totalConsumption = "0.00";
                $totalGrid = "0.00";
                $totalBuy = "0.00";
                $totalSell = "0.00";
                $totalSaving = "0";
                $totalChargeEnergy = "0.00";
                $totalDischargeEnergy = "0.00";
                $totalBatterySoc = "0%";
            }

            $daily_total = ['generation' => $totalGeneration, 'consumption' => $totalConsumption, 'grid' => $totalGrid, 'buy' => $totalBuy, 'sell' => $totalSell, 'saving' => $totalSaving, 'charge' => $totalChargeEnergy, 'discharge' => $totalDischargeEnergy, 'battery_soc' => isset($totalBatterySoc) ? $totalBatterySoc : ""];

            $todayLogTime = [];
            foreach ($hourly_val as $key => $value) {

                $todayLogTime[] = date('H:i', strtotime($value->collect_time));
                $current_gen['time'] = date('H:i', strtotime($value->collect_time));
                $current_gen['plant_id'] = $plants;
                $current_gen['plant_type'] = $plantType;
                $current_gen['time_type'] = 'Daily';
                $current_gen['collect_time'] = date('Y-m-d', strtotime($value->collect_time));
//                return $value->collect_time;
                $dataGetDate = Date('Y-m-d',strtotime($value->collect_time));
                if($dataGetDate == Date('Y-m-d')){
                    $current_gen['generation'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_generation'), 2);
                    $consumption = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_consumption');

                }else{
                    $current_gen['generation'] = (double)round(ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_generation'), 2);
                    $consumption = ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_consumption');
                }
//                $current_gen['generation'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_generation'), 2);

                if($SystemType == '4'){
//                    $consumption = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_consumption');
                    $current_gen['consumption'] = (double)round($consumption/1000, 2);
                }else{
                    if($meter_type == "Solis"){
                        $current_gen['consumption'] = (double)round($consumption/1000, 2);
                    }else{
                        $current_gen['consumption'] = (double)round($consumption, 2);
                    }
//                    $consumption = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_consumption');
                }
                if($dataGetDate == Date('Y-m-d')){
                    $current_gen['buy_energy'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->where('grid_type', '+ve')->sum('current_grid'), 2);
                    $current_gen['sell_energy'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->where('grid_type', '-ve')->sum('current_grid'), 2);
                    $current_gen['saving'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_saving'), 2);
                    $current_gen['grid'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_grid'), 2);
                    $batteryPower = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->latest()->first();

                }else{
                    $current_gen['buy_energy'] = (double)round(ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->where('grid_type', '+ve')->sum('current_grid'), 2);
                    $current_gen['sell_energy'] = (double)round(ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->where('grid_type', '-ve')->sum('current_grid'), 2);
                    $current_gen['saving'] = (double)round(ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_saving'), 2);
                    $current_gen['grid'] = (double)round(ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_grid'), 2);
                    $batteryPower = ProcessedCurrentVariableHistory::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->latest()->first();
                }
//                $current_gen['buy_energy'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->where('grid_type', '+ve')->sum('current_grid'), 2);
//                $current_gen['sell_energy'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->where('grid_type', '-ve')->sum('current_grid'), 2);
                if($value->grid_type == '-ve'){
//                    $current_gen['grid'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_grid'), 2);
                    $current_gen['grid'] = -1 * $current_gen['grid'];
                }else{
//                    $current_gen['grid'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_grid'), 2);
                }
                $current_gen['grid_type'] = $value->grid_type;
//                $current_gen['saving'] = (double)round(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_saving'), 2);
//                $batteryPower = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->latest()->first();
                $batteryData = 0.00;
                $batterySoc = 0.00;
                if ($batteryPower) {
                    if ($batteryPower->battery_power != null) {
                        $batteryData = $batteryPower->battery_power / 1000;
                    }
                    $batterySoc = $batteryPower->battery_capacity;
                }
                $current_gen['battery_power'] = (double)round((double)$batteryData, 2);
                $current_gen['battery_soc'] = (double)$batterySoc;
                array_push($hourly_data, $current_gen);
            }

            if (!empty($todayLogTime)) {

                $startTime = new \DateTime(date('Y-m-d ' . end($todayLogTime), strtotime($date)));
            } else {

                $startTime = new \DateTime(date('Y-m-d 00:00', strtotime($date)));
            }

            $endTime = new \DateTime(date('Y-m-d 23:55', strtotime($date)));
            $timeStep = 5;

            while ($startTime <= $endTime) {
                $todayLogTime[] = $startTime->format('H:i');
                $startTime->add(new \DateInterval('PT' . $timeStep . 'M'));
            }

            $timeArray = $todayLogTime;

            return [$hourly_data, $daily_total, $timeArray];

        }
    }
    public function plantChartHybridData(Request $request)
    {
//        return $request->all();
        $input = $request->all();
        $plantsID = array();
        $plantsIDD = array();
        $companyID = array();
        $userID = $request->user()->id;
        $plantType = $request->type;


        if ($plantType == 'hybrid') {
            $systemType = 4;
        } else {
            $systemType = 2;
        }

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
//                $plant_ids = PlantUser::where('user_id', $userID)->pluck('plant_id');
//                $userPlants = Plant::whereIn('id', $plant_ids)->pluck('system_type');
//                $resulArray = count(array_unique(json_decode(json_encode($userPlants))));
//                $systemType = [4, 2];
//                if ($resulArray == 1) {
//                    if (in_array(4, json_decode(json_encode($userPlants), true))) {
//                        $systemType = [4];
//                    } elseif (in_array(2, json_decode(json_encode($userPlants), true))) {
//                        $systemType = [2];
//                    }
//                }
//                return $systemType;

                $plantsID = PlantUser::where('user_id', $userID)->pluck('plant_id')->toArray();
                $plantsID = Plant::whereIn('id', $plantsID)->where('system_type', $systemType)->pluck('id')->toArray();

            } else {

                foreach (explode(',', $request->get('plant_id')) as $id) {

                    $plantsID[] = (int)$id;
                }
            }
        }
//        return $plantsID;

        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->where('system_type', $systemType)->pluck('id')->toArray();
//
        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;
        }
        if (empty($plantsIDD)) {
            $response = [
                'status' => 1,
                'message' => 'Plant not found',
                //'daily' => $result[0],
                'result' => '0',
                'labels' => '0'
            ];
            return response()->json($response, 200);

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
        if (isset($input['parameter'])) {
            $parameter = ucfirst($input['parameter']);
        } else {
            $parameter = '';
        }
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
//        return $plants;

        if (count($plants) == 0) {
            return $this->sendError(0, 'Plant not found.');
        }

        if ($duration == 'Daily') {

            $result = $this->getDailyHybridData($systemType, $request->plant_id,$plant_id, $parameter, $date);

            if($request->plant_id == "all" && $systemType == '4'){
                $response = [
                    'status' => 1,
                    'message' => $duration . ' ' . $parameter,
                    //'daily' => $result[0],
                    'result' => $result[0],
                    'labels' => $result[1]
                ];
            }else {


                $finalArrayData = [];
                $arrayData = array_values(array_unique($result[2]));


                for ($i = 0; $i < count($arrayData); $i++) {
                    if (count($result[0]) == 0) {

                        $dataResult = ['time' => $arrayData[$i], 'generation' => '0.00', 'consumption' => '0.00', 'buy_energy' => '0.00', 'sell_energy' => '0.00', 'grid' => '0.00', 'saving' => '0', 'battery_power' => '0.00', 'battery_soc' => null];

                    } else {

                        foreach ($result[0] as $key => $data) {
                            if ($data['time'] == $arrayData[$i]) {
                                $dataResult = $data;
                                break;
                            } else {
                                $dataResult = ['time' => $arrayData[$i], 'generation' => '0.00', 'consumption' => '0.00', 'buy_energy' => '0.00', 'sell_energy' => '0.00', 'grid' => '0.00', 'saving' => '0', 'battery_power' => '0.00', 'battery_soc' => null];

                            }
                        }


                    }

                    array_push($finalArrayData, $dataResult);

                }

                $response = [
                    'status' => 1,
                    'message' => $duration . ' ' . $parameter,
                    //'daily' => $result[0],
                    'result' => $finalArrayData,
                    'labels' => $result[1]
                ];
            }
            return response()->json($response, 200);
        } else if ($duration == 'Weekly') {
            return $this->sendResponse(1, $duration . ' ' . $parameter, $this->getWeeklyData($plant_id, $parameter));
        } else if ($duration == 'Monthly') {
//            return [$plant_id, $date];
            $res = $this->getMonthlyHybridData($plant_id, $parameter, $date);
//            return $res;

            $response = [
                'status' => 1,
                'message' => $duration . ' ' . $parameter,
                'result' => $res[0],
                'labels' => $res[1]
            ];
            return response()->json($response, 200);

        } else if ($duration == 'Yearly') {
//            return [$plant_id, $parameter, $date];

            $res = $this->getYearlyHybridData($plant_id, $parameter, $date);
//            return $res;

            $response = [
                'status' => 1,
                'message' => $duration . ' ' . $parameter,
                'result' => $res[0],
                'labels' => $res[1]
            ];
            return response()->json($response, 200);
        }
    }

    private function get_graph_buy_energy_daily_data($plant_id, $date)
    {
        $hourly_data = array();

        $current_generation_start_time = ProcessedCurrentVariable::select('collect_time')->whereIn('plant_id', $plant_id)->whereDate('collect_time', $date)->where('grid_type', '+ve')->where('current_grid', '>', 0)->orderBy('collect_time', 'ASC')->first();
        $start_date = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->collect_time)) : '06:00:00';

        $current_generation = ProcessedCurrentVariable::select('created_at')->where('plant_id', $plant_id[0])->whereBetween('created_at', [date($date . ' ' . $start_date), date($date . ' 23:59:59')])->groupBy('created_at')->get();

        foreach ($current_generation as $key => $today_log) {

            $today_log_data = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('grid_type', '+ve')->where('created_at', $today_log->created_at)->sum('current_grid');

            $current_gen['value'] = (string)round($today_log_data, 2);
            $current_gen['time'] = date('H:i', strtotime($today_log->created_at));
            array_push($hourly_data, $current_gen);
        }

        return $hourly_data;
    }

    private
    function get_graph_sell_energy_daily_data($plant_id, $date)
    {
        $hourly_data = array();

        $current_generation_start_time = ProcessedCurrentVariable::select('collect_time')->whereIn('plant_id', $plant_id)->whereDate('collect_time', $date)->where('grid_type', '+ve')->where('current_grid', '>', 0)->orderBy('collect_time', 'ASC')->first();
        $start_date = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->collect_time)) : '06:00:00';

        $current_generation = ProcessedCurrentVariable::select('collect_time')->where('plant_id', $plant_id[0])->whereBetween('collect_time', [date($date . ' ' . $start_date), date($date . ' 23:59:59')])->groupBy('collect_time')->get();

        foreach ($current_generation as $key => $today_log) {

            $today_log_data = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('grid_type', '-ve')->where('collect_time', $today_log->collect_time)->sum('current_grid');

            $current_gen['value'] = (string)round($today_log_data, 2);
            $current_gen['time'] = date('H:i', strtotime($today_log->collect_time));
            array_push($hourly_data, $current_gen);
        }

        return $hourly_data;
    }

    public
    function round_off_data($value)
    {
        $number = (float)$value;
        return number_format($number, 2);
    }

    private
    function getWeeklyData($plant_id, $parameter)
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

    private
    function getMonthlyData($plant_id, $parameter, $date)
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

            $array_data = array('time' => (string)$i, 'value' => ($daily > 0) ? number_format($daily, 2) : '0'
            );
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

    private
    function getMonthlyGraphData($systemType , $plants,$plant_id, $plantType, $parameter, $date)
    {
        $plant_detail = Plant::whereIn('id', $plant_id)->get(['meter_type', 'benchmark_price']);

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

            $dailyGeneration = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyGeneration');
            $dailyConsumption = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyConsumption');
            $dailyGridPower = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyGridPower');
            $dailyBuyEnergy = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyBoughtEnergy');
            $dailySellEnergy = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailySellEnergy');
            $dailySaving = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailySaving');

            $array_data = array('time' => (string)$i,
                'plant_id' => $plants,
                'plant_type' => $plantType,
                'time_type' => "Monthly",
                'collect_time' => $date,
                'generation' => ($dailyGeneration > 0) ? (double)round($dailyGeneration, 2) : 0.00,
                'consumption' => ($dailyConsumption > 0) ? (double)round($dailyConsumption, 2) : 0.00,
                'buy_energy' => ($dailyBuyEnergy > 0) ? (double)round($dailyBuyEnergy, 2) : 0.00,
                'sell_energy' => ($dailySellEnergy > 0) ? (double)round($dailySellEnergy, 2) : 0.00,
                'saving' => ($dailySaving > 0) ? (double)round($dailySaving, 2) : 0.00,
                'grid' => ($dailyGridPower > 0) ? (double)round($dailyGridPower, 2) : 0.00,
                'battery_power' => 0.00,
                'battery_soc' => 0.00
            );
            array_push($monthly_data, $array_data);
        }

        if (MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->exists()) {

//            $dataRespmonthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->get();

            if(count($plant_id) > 1){
                $dataRespmonthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->get();
                $MonthlyDataArray = json_decode(json_encode($dataRespmonthly), true);
                $monthlyGenera = array_sum(array_column($MonthlyDataArray, 'monthlyGeneration'));
                $monthlyConsum = array_sum(array_column($MonthlyDataArray, 'monthlyConsumption'));
                $monthlyGrid = array_sum(array_column($MonthlyDataArray, 'monthlyGridPower'));
                $monthlyBought = array_sum(array_column($MonthlyDataArray, 'monthlyBoughtEnergy'));
                $monthlySell = array_sum(array_column($MonthlyDataArray, 'monthlySellEnergy'));
                $monthlySav = array_sum(array_column($MonthlyDataArray, 'monthlySaving'));
                $monthlyCharge_ener = array_sum(array_column($MonthlyDataArray, 'monthly_charge_energy'));
                $monthlyDis_charge = array_sum(array_column($MonthlyDataArray, 'monthly_discharge_energy'));
            }else{
                $dataRespmonthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->first();
                $MonthlyDataArray = $dataRespmonthly;
                $monthlyGenera = $MonthlyDataArray->monthlyGeneration;
                $monthlyConsum = $MonthlyDataArray->monthlyConsumption;
                $monthlyGrid = $MonthlyDataArray->monthlyGridPower;
                $monthlyBought = $MonthlyDataArray->monthlyBoughtEnergy;
                $monthlySell = $MonthlyDataArray->monthlySellEnergy;
                $monthlySav = $MonthlyDataArray->monthlySaving;
                $monthlyCharge_ener = $MonthlyDataArray->monthly_charge_energy;
                $monthlyDis_charge = $MonthlyDataArray->monthly_discharge_energy;
            }

//            return [$plant_id,$dataRespmonthly,$date,$plant_id[0],$MonthlyDataArray];


            $totalGeneration = (string)round($monthlyGenera, 2);
            $totalConsumption = (string)round($monthlyConsum, 2);
            $totalGrid = (string)round($monthlyGrid, 2);
            $totalBuy = (string)round($monthlyBought, 2);
            $totalSell = (string)round($monthlySell, 2);
            $totalSaving = (string)$monthlySav;
            $totalChargeEnergy = (string)round($monthlyCharge_ener, 2);
            $totalDischargeEnergy = (string)round($monthlyDis_charge, 2);
        } else {
            $totalGeneration = "0.00";
            $totalConsumption = "0.00";
            $totalGrid = "0.00";
            $totalBuy = "0.00";
            $totalSell = "0.00";
            $totalSaving = "0";
            $totalChargeEnergy = "0.00";
            $totalDischargeEnergy = "0.00";
        }
        $monthly_total = ['generation' => $totalGeneration, 'consumption' => $totalConsumption, 'grid' => $totalGrid, 'buy' => $totalBuy, 'sell' => $totalSell, 'saving' => $totalSaving, 'charge' => $totalChargeEnergy, 'discharge' => $totalDischargeEnergy];

        return [$monthly_data, $monthly_total];
    }

    private
    function getYearlyData($plant_id, $parameter, $date)
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
            $array_data = array('time' => (string)$i,
                'value' => number_format($monthly, 2),
            );
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

    private
    function getYearlyGraphData($plants ,$plant_id, $plantType,$parameter, $date)
    {
        $monthly_data = array();
        $yearly_total = 0;
        $plant_detail = Plant::whereIn('id', $plant_id)->get(['meter_type', 'benchmark_price']);
        $benchmark_price = count($plant_detail) > 0 ? $plant_detail[0]->benchmark_price : 0;

        for ($i = 1; $i <= 12; $i++) {
            if ($i < 10) {
                $i = '0' . $i;
            }
            if(count($plant_id) > 1){
                $dataRespmonthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->get();
                $MonthlyDataArray = json_decode(json_encode($dataRespmonthly), true);
                $monthlyGeneration = array_sum(array_column($MonthlyDataArray, 'monthlyGeneration'));
                $monthlyConsumption = array_sum(array_column($MonthlyDataArray, 'monthlyConsumption'));
                $monthlyGridPower = array_sum(array_column($MonthlyDataArray, 'monthlyGridPower'));
                $monthlyBuyEnergy = array_sum(array_column($MonthlyDataArray, 'monthlyBoughtEnergy'));
                $monthlySellEnergy = array_sum(array_column($MonthlyDataArray, 'monthlySellEnergy'));
                $monthlySaving = array_sum(array_column($MonthlyDataArray, 'monthlySaving'));
            }else{
                $dataRespmonthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->first();
                if($dataRespmonthly){
                    $MonthlyDataArray = $dataRespmonthly;
                    $monthlyGeneration = $MonthlyDataArray->monthlyGeneration;
                    $monthlyConsumption = $MonthlyDataArray->monthlyConsumption;
                    $monthlyGridPower = $MonthlyDataArray->monthlyGridPower;
                    $monthlyBuyEnergy = $MonthlyDataArray->monthlyBoughtEnergy;
                    $monthlySellEnergy = $MonthlyDataArray->monthlySellEnergy;
                    $monthlySaving = $MonthlyDataArray->monthlySaving;
                }else{
                    $monthlyGeneration = 0;
                    $monthlyConsumption = 0;
                    $monthlyGridPower = 0;
                    $monthlyBuyEnergy = 0;
                    $monthlySellEnergy = 0;
                    $monthlySaving = 0;
                }

            }
//            $monthlyGeneration = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGeneration');
//            $monthlyConsumption = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyConsumption');
//            $monthlyGridPower = MonthlyProcessedPlantDetail::whereIN('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGridPower');
//            $monthlyBuyEnergy = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyBoughtEnergy');
//            $monthlySellEnergy = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySellEnergy');
//            $monthlySaving = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySaving');
            $array_data = array('time' => (string)$i,
                'plant_id' => $plants,
                'plant_type' => $plantType,
                'time_type' => 'Yearly',
                'collect_time' => $date,
                'generation' => (double)round($monthlyGeneration, 2),
                'consumption' => (double)round($monthlyConsumption, 2),
                'grid' => (double)round($monthlyGridPower, 2),
                'buy_energy' => (double)round($monthlyBuyEnergy, 2),
                'sell_energy' => (double)round($monthlySellEnergy, 2),
                'saving' => (double)round($monthlySaving, 2),
                'battery_power' => 0.00,
                'battery_soc' => 0.00
            );
            array_push($monthly_data, $array_data);
        }
        if (YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->exists()) {

            $yearlydataResponse = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->get();

            $YearlyDataArray = json_decode(json_encode($yearlydataResponse), true);
            $YearlyGenera = array_sum(array_column($YearlyDataArray, 'yearlyGeneration'));
            $YearlyConsum = array_sum(array_column($YearlyDataArray, 'yearlyConsumption'));
            $YearlyGrid = array_sum(array_column($YearlyDataArray, 'yearlyGridPower'));
            $YearlyBought = array_sum(array_column($YearlyDataArray, 'yearlyBoughtEnergy'));
            $YearlySell = array_sum(array_column($YearlyDataArray, 'yearlySellEnergy'));
            $YearlySav = array_sum(array_column($YearlyDataArray, 'yearlySaving'));
            $YearlyCharge_ener = array_sum(array_column($YearlyDataArray, 'yearly_charge_energy'));
            $YearlyDis_charge = array_sum(array_column($YearlyDataArray, 'yearly_discharge_energy'));

            $totalGeneration = (string)round($YearlyGenera, 2);
            $totalConsumption = (string)round($YearlyConsum, 2);
            $totalGrid = (string)round($YearlyGrid, 2);
            $totalBuy = (string)round($YearlyBought, 2);
            $totalSell = (string)round($YearlySell, 2);
            $totalSaving = (string)$YearlySav;
            $totalChargeEnergy = (string)round($YearlyCharge_ener, 2);
            $totalDischargeEnergy = (string)round($YearlyDis_charge, 2);
        } else {
            $totalGeneration = "0.00";
            $totalConsumption = "0.00";
            $totalGrid = "0.00";
            $totalBuy = "0.00";
            $totalSell = "0.00";
            $totalSaving = "0";
            $totalChargeEnergy = "0.00";
            $totalDischargeEnergy = "0.00";
        }
        $yearly_total = ['generation' => $totalGeneration, 'consumption' => $totalConsumption, 'grid' => $totalGrid, 'buy' => $totalBuy, 'sell' => $totalSell, 'saving' => $totalSaving, 'charge' => $totalChargeEnergy, 'discharge' => $totalDischargeEnergy];
        return [$monthly_data, $yearly_total];

    }

    public
    function plantDetails_backup(Request $request)
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


        $plants = $this->plantAdditionalFields($plants);
        if ($response == true) {
            return $this->sendResponse(1, 'Showing plant details', $plants);
        } else {
            return $this->sendError(0, 'Sorry! Saltech API\'s error.', $plants);
        }

    }

    public
    function addPlant(Request $request)
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

    public
    function plant_chart13(Request $request)
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

    public
    function plantAdditionalFields($plants)
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


    public function plantExpectedActualChart(Request $request)
    {

        $input = $request->all();
        $plantsID = array();
        $plantsIDD = array();
        $companyID = array();
        $userID = $request->user()->id;
        if ($request->type == 'hybrid') {
            $systemType = 4;
        } else {
            $systemType = 2;
        }

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

        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->where('system_type', $systemType)->pluck('id')->toArray();

        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;

        }

        if (empty($plantsIDD)) {
            $response = [
                'status' => 1,
                'message' => 'Plant not found.',
                'time' => '0',
                'date' => "0",
                'actual' => '0',
                'expected' => '0',
                'percentage' => '0'
            ];
            return response()->json($response, 200);
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
            $response = [
                'status' => 1,
                'message' => 'Plant not found.',
                'time' => '0',
                'date' => $date,
                'actual' => '0',
                'expected' => '0',
                'percentage' => '0'
            ];
            return response()->json($response, 200);

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

    public
    function plantEnvironmentChart(Request $request)
    {

        $input = $request->all();
        $userID = $request->user()->id;

        $envPlanting = Setting::where('perimeter', 'env_planting')->first()->value;
        $envReduction = Setting::where('perimeter', 'env_reduction')->first()->value;

        $plantsID = array();
        $plantsIDD = array();
        $companyID = array();
        if ($request->type == 'hybrid') {
            $systemType = 4;
        } else {
            $systemType = 2;
        }

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
                $plantsID = Plant::whereIn('id', $plantsID)->where('system_type', $systemType)->pluck('id')->toArray();

            } else {

                foreach (explode(',', $request->get('plant_id')) as $id) {

                    $plantsID[] = (int)$id;
                }
            }
        }


        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->where('system_type', $systemType)->pluck('id')->toArray();
        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;;
        }


        if (empty($plantsIDD)) {

            $response = [
                'status' => 1,
                'message' => "No Plant Found",
                'time' => "0",
                'date' => "0",
                'planting' => "0",
                'reduction' => "0",
                'total' => "0"
            ];

            return response()->json($response, 200);
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
            $response = [
                'status' => 1,
                'message' => "No Plant Found",
                'time' => "0",
                'date' => "0",
                'planting' => "0",
                'reduction' => "0",
                'total' => "0"
            ];

            return response()->json($response, 200);
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

    public
    function previousTenMinutesDateTime($date)
    {

        $currentDataDateTime = new \DateTime($date);
        $currentDataDateTime->modify('-10 minutes');
        $finalCurrentDataDateTime = $currentDataDateTime->format('Y-m-d H:i:s');

        return $finalCurrentDataDateTime;
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
                $unit = '';
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
                $unit = 'K';
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
                $unit = 'M';
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
                $unit = 'B';
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
                $unit = 'T';
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
                $unit = 'Q';
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
        $timeArray = ['day', 'month', 'year'];
        if (!in_array($request->time, $timeArray)) {
            return $this->sendError(0, 'Please enter a valid time!');
        }
        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plant_id;
        $plantHistoryGraphYAxis = [];

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
                $plantsID = Plant::whereIn('id', $plantsID)->where('system_type', 4)->pluck('id')->toArray();

            } else {

                foreach (explode(',', $request->get('plant_id')) as $id) {

                    $plantsID[] = (int)$id;
                }
            }
        }
        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->where('system_type', 4)->pluck('id')->toArray();

        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;
        }


        if (empty($plantsIDD)) {
            $data = ['outagesHours' => '0', 'date' => '0', 'time' => $request->time];
            return ['status' => 1, 'data' => $data];

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

        $plantsIdData = $plantsData->pluck('id')->toArray();

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
        $outagesHours = 0;
        $yearlyTimes = [];

        for ($q = 0; $q < count($plantsIdData); $q++) {
            $currentProcessedData = DailyProcessedPlantDetail::select('daily_outage_grid_voltage', 'created_at')->where('plant_id', $plantsIdData[$q])->whereDate('created_at',  $date)->orderBy('created_at', 'DESC')->first();

            if ($currentProcessedData) {
                if ($currentProcessedData->daily_outage_grid_voltage) {
                    $dailyOutagesHoursData = explode(':', $currentProcessedData->daily_outage_grid_voltage);
                    $yearlyTimes[] = $dailyOutagesHoursData[0] . ':' . $dailyOutagesHoursData[1] . ':00';
                }
            }
        }
        $totalSeconds = 0;
        foreach ($yearlyTimes as $t) {
            $totalSeconds += $this->toSeconds($t);
        }

        $dailyOutagesGridValue = $this->toTimeCalculation($totalSeconds);

        $explodeDailyData = explode(':', $dailyOutagesGridValue);
        if ($explodeDailyData[0] == 0 && $explodeDailyData[1] == 0) {
            $outagesHours = '00:00';
        } else {

            if ($explodeDailyData[0] < 10) {
                $outagesHours = '0' . $explodeDailyData[0] . ':' . $explodeDailyData[1];


            }
            if ($explodeDailyData[1] < 10) {

                $outagesHours = $explodeDailyData[0] . ':' . '0' . $explodeDailyData[1];

            }
            if ($explodeDailyData[0] < 10 && $explodeDailyData[1] < 10) {

                $outagesHours = '0' . $explodeDailyData[0] . ':' . '0' . $explodeDailyData[1];
            } else {
                $outagesHours = $explodeDailyData[0] . ':' . $explodeDailyData[1];

            }
        }

        if ($time == 'day') {
            $data = ['outagesHours' => $outagesHours, 'date' => $request->date, 'time' => $request->time];
        } else if ($time == 'month') {
            $monthlyOutagesHours = 0;
            $monthlyTime = [];
            for ($q = 0; $q < count($plantsIdData); $q++) {
                $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantsIdData[$q])->where('created_at', 'LIKE',  $date . '%')->orderBy('created_at', 'DESC')->first();
                if ($monthlyProcessedData) {
                    $monthlyOutagesHoursData = explode(':', $monthlyProcessedData->monthly_outage_grid_voltage);
                    $monthlyTime[] = $monthlyOutagesHoursData[0] . ':' . $monthlyOutagesHoursData[1] . ':00';

                }
                if ($monthlyOutagesHours == 0) {
                    $monthlyOutagesHours = '00:00';
                }
            }
            $totalSeconds = 0;
            foreach ($monthlyTime as $t) {
                $totalSeconds += $this->toSeconds($t);
            }

            $dailyOutagesGridValue = $this->toTimeCalculation($totalSeconds);
            $explodeDailyData = explode(':', $dailyOutagesGridValue);
            if ($explodeDailyData[0] == 0) {

                $monthlyOutagesHours = '00:00';
            } else {
                if ($explodeDailyData[0] < 10) {
                    $monthlyOutagesHours = '0' . $explodeDailyData[0] . ':' . $explodeDailyData[1];


                } elseif ($explodeDailyData[1] < 10) {

                    $monthlyOutagesHours = $explodeDailyData[0] . ':' . $explodeDailyData[1] . '0';

                } elseif ($explodeDailyData[0] < 10 && $explodeDailyData[1] < 10) {

                    $monthlyOutagesHours = '0' . $explodeDailyData[0] . ':' . $explodeDailyData[1] . '0';
                } else {
                    $monthlyOutagesHours = $explodeDailyData[0] . ':' . $explodeDailyData[1];

                }
            }
            $data = ['outagesHours' => $monthlyOutagesHours, 'date' => $request->date, 'time' => $request->time];

        } else {
            $yearlyOutagesHours = 0;
            $yearlyTimes = [];
            for ($q = 0; $q < count($plantsIdData); $q++) {
                $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_outage_grid_voltage', 'created_at')->where('plant_id', $plantsIdData[$q])->whereYear('created_at',  $date)->orderBy('created_at', 'DESC')->first();
                if ($yearlyProcessedData) {
                    if ($yearlyProcessedData->yearly_outage_grid_voltage) {
                        $yearlyOutagesHoursData = explode(':', $yearlyProcessedData->yearly_outage_grid_voltage);
                        $yearlyTimes[] = $yearlyOutagesHoursData[0] . ':' . $yearlyOutagesHoursData[1] . ':00';
                    } else {
                        $yearlyOutagesHours = '00:00';
                    }
                }
                if ($yearlyOutagesHours == 0) {
                    $yearlyOutagesHours = '00:00';
                }
            }
            $totalSeconds = 0;
            foreach ($yearlyTimes as $t) {
                $totalSeconds += $this->toSeconds($t);
            }

            $yearlyOutagesGridValue = $this->toTimeCalculation($totalSeconds);
            $explodeYearlyData = explode(':', $yearlyOutagesGridValue);
            $yearlyOutagesGridValue = $explodeYearlyData[0] . ':' . $explodeYearlyData[1];

            $data = ['outagesHours' => $yearlyOutagesGridValue, 'date' => $request->date, 'time' => $request->time];

        }
        return ['status' => 1, 'data' => $data];
    }

    public
    function consumptionInPeakHours(Request $request)
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
        $timeArray = ['day', 'month', 'year'];
        if (!in_array($request->time, $timeArray)) {
            return $this->sendError(1, 'Please enter a valid time!');
        }

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plant_id;
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
        $plantHistoryGraph = [];
        $legendArray = [];
        $todayLogData = [];
        $batteryDischargeEnergy = 0;
        $gridImport = 0;
        $current = ['daily_discharge_energy', 'grid_import'];
        $gridImport = 0;
        $dailyDischargeEnergy = 0;
        $consumption = 0;
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
                $plantsID = Plant::whereIn('id', $plantsID)->where('system_type', 4)->pluck('id')->toArray();

            } else {

                foreach (explode(',', $request->get('plant_id')) as $id) {

                    $plantsID[] = (int)$id;
                }
            }
        }

        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->where('system_type', 4)->pluck('id')->toArray();

        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;
        }

        if (empty($plantsIDD)) {

            $data = ['batteryDischarge' => '0', 'gridImport' => '0', 'consumption' => '0'];
            return ['status' => 1, 'data' => $data];
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

        $plantsIdData = $plantsData->pluck('id')->toArray();

        for ($i = 0; $i < count($plantsIdData); $i++) {
            $currentProcessedData = DailyProcessedPlantDetail::select('daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge','daily_discharge_energy', 'created_at')->where('plant_id', $plantsIdData[$i])->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->first();
            $plantDetail = Plant::select('id','plant_name','meter_type','system_type')->where("id",$plantsIdData[$i])->first();

            if ($currentProcessedData) {
                if($plantDetail->meter_type =  "Solis-Cloud"){
                    $dailyDischargeEnergy += $currentProcessedData->daily_discharge_energy;
                    $consumption += $currentProcessedData->daily_peak_hours_consumption;
                }else{
                    $dailyDischargeEnergy += $currentProcessedData->daily_peak_hours_battery_discharge;
                    $consumption += $currentProcessedData->daily_peak_hours_consumption;
                }
                $gridImport += $currentProcessedData->daily_peak_hours_grid_buy;
            }
        }
        foreach ($current as $key => $currentData) {

            $todayLogDataSum = array();
            if ($time == 'day') {

                if ($currentData == 'daily_discharge_energy') {

                    array_push($todayLogData, ['value' => (double)$dailyDischargeEnergy, 'name' => 'Battery Discharging: ' . (double)$dailyDischargeEnergy . ' ' . 'kWh']);
                    $batteryDischargeEnergy = (double)$dailyDischargeEnergy;

                } else if ($currentData == 'grid_import') {

                    array_push($todayLogData, ['value' => (double)$gridImport, 'name' => 'Grid Import: ' . (double)$gridImport . ' ' . 'kWh']);
                    $gridImport = (double)$gridImport;
                }

            } else if ($time == 'month') {
                $month = date('Y-m', strtotime($date));
                $consumption = 0;
                $monthlyDischargeValue = 0;
                $monthlyGridValue = 0;
                for ($i = 0; $i < count($plantsIdData); $i++) {
                    $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantsIdData[$i])->where('created_at', 'LIKE', $month . '%')->first();

                    if ($monthlyProcessedData) {
                        $monthlyGridValue += $monthlyProcessedData->monthly_peak_hours_grid_import;
                        $monthlyDischargeValue += $monthlyProcessedData->monthly_peak_hours_discharge_energy;
                        $consumption += $monthlyProcessedData->monthly_peak_hours_consumption;
                    }
                }
                if ($currentData == 'daily_discharge_energy') {

                    array_push($todayLogData, ['value' => (double)$monthlyDischargeValue, 'name' => 'Battery Discharging: ' . (double)$monthlyDischargeValue . ' ' . 'kWh']);
                    $batteryDischargeEnergy = (double)$monthlyDischargeValue;

                } else if ($currentData == 'grid_import') {

                    array_push($todayLogData, ['value' => (double)$monthlyGridValue, 'name' => 'Grid Import: ' . (double)$monthlyGridValue . ' ' . 'kWh']);
                    $gridImport = (double)$monthlyGridValue;
                }

            } else if ($time == 'year') {
                $year = date('Y', strtotime('Y'));
                $consumption = 0;
                $yearlyDischargeValue = 0;
                $yearlyGridValue = 0;
                for ($i = 0; $i < count($plantsIdData); $i++) {
                    $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_peak_hours_discharge_energy', 'yearly_peak_hours_grid_import', 'yearly_peak_hours_consumption', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $plantsIdData[$i])->whereYear('created_at', $year)->orderBy('created_at', 'DESC')->first();

                    if ($yearlyProcessedData) {
                        $yearlyGridValue += $yearlyProcessedData->yearly_peak_hours_grid_import;
                        $yearlyDischargeValue += $yearlyProcessedData->yearly_peak_hours_discharge_energy;
                        $consumption += $yearlyProcessedData->yearly_peak_hours_consumption;
                    }
                }

                if ($currentData == 'daily_discharge_energy') {
                    array_push($todayLogData, ['value' => (double)$yearlyDischargeValue, 'name' => 'Battery Discharging: ' . (double)$yearlyDischargeValue . ' ' . 'kWh']);
                    $batteryDischargeEnergy = (double)$yearlyDischargeValue;

                } else if ($currentData == 'grid_import') {

                    array_push($todayLogData, ['value' => (double)$yearlyGridValue, 'name' => 'Grid Import: ' . (double)$yearlyGridValue . ' ' . 'kWh']);
                    $gridImport = (double)$yearlyGridValue;
                }
            }
        }
        if ($time == 'day') {

            $legendArray[] = 'Battery Discharge' . ': ' . (double)$dailyDischargeEnergy . ' ' . 'KW';
            $legendArray[] = 'Grid Import' . ': ' . (double)$gridImport . ' ' . 'KW';
        } else if ($time == 'month') {
            $legendArray = ['Battery Discharge' => (double)$monthlyDischargeValue . 'KW', 'Grid Import' => $monthlyGridValue . 'KW'];
        } else if ($time == 'year') {
            $legendArray = ['Battery Discharge' => (double)$yearlyDischargeValue . 'KW', 'Grid Import' => $yearlyGridValue . 'KW'];
        }
        $data = ['batteryDischarge' => (string)$batteryDischargeEnergy, 'gridImport' => (string)$gridImport, 'consumption' => (string)$consumption];
        return ['status' => 1, 'data' => $data];
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
        $timeArray = ['day', 'month', 'year'];
        if (!in_array($request->time, $timeArray)) {
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
        $plantHistoryGraph = [];
        $legendArray = [];
        $todayLogData = [];
        $batteryDischargeEnergy = 0;

        $current = ['battery-discharging', 'solar', 'grid_import'];
        $gridImport = 0;
        $solar = 0;
        $batteryDischarging = 0;
        $consumption = 0;
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
                $plantsID = Plant::whereIn('id', $plantsID)->where('system_type', 4)->pluck('id')->toArray();

            } else {

                foreach (explode(',', $request->get('plant_id')) as $id) {

                    $plantsID[] = (int)$id;
                }
            }
        }

        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->where('system_type', 4)->pluck('id')->toArray();

        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;

        }

        if (empty($plantsIDD)) {
            $todayLogData = ['battery-discharge' => '0', 'solar' => '0', 'grid-import' => '0', 'total-consumption' => '0'];

            $data = ['status' => 1, 'data' => $todayLogData];
            return $data;
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

        $plantHybridIds = $plantsData->pluck('id')->toArray();

        for ($i = 0; $i < count($plantHybridIds); $i++) {

            $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'use_value', 'use_ratio', 'discharge_ratio', 'grid_ratio', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_discharge_energy', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantHybridIds[$i])->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->first();

            if ($currentProcessedData) {
                $batteryDischarging += round((double)$currentProcessedData->use_value * (double)$currentProcessedData->discharge_ratio / 100, 2);
                $solar += round((double)$currentProcessedData->use_value * (double)$currentProcessedData->use_ratio / 100, 2);
                $consumption += round($currentProcessedData->use_value, 2);
            }
        }
        $gridImport = round((double)$consumption - ($batteryDischarging + $solar), 2);

        foreach ($current as $key => $currentData) {

            $todayLogDataSum = array();
            if ($time == 'day') {
                $todayLogData = ['battery-discharge' => (string)(double)$batteryDischarging, 'solar' => (string)(double)$solar, 'grid-import' => (string)(double)$gridImport, 'total-consumption' => (string)$consumption];


            } else if ($time == 'month') {
                $monthlyDischargeValue = 0;
                $monthlyGridImportValue = 0;
                $monthlySolar = 0;
                $consumption = 0;
                $month = date('Y-m', strtotime($date));
                for ($i = 0; $i < count($plantHybridIds); $i++) {
                    $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantHybridIds[$i])->where('created_at', 'LIKE', $month . '%')->first();

                    if ($monthlyProcessedData) {
                        $consumption += $monthlyProcessedData->monthly_use_value;
                        $monthlyDischargeValue += round((double)$monthlyProcessedData->monthly_use_value * (double)$monthlyProcessedData->monthly_discharge_ratio / 100, 2);
                        $monthlySolar += round((double)$monthlyProcessedData->monthly_use_value * (double)$monthlyProcessedData->monthly_use_ratio / 100, 2);
                    }
                }
                $monthlyGridImportValue = round((double)$consumption - ($monthlyDischargeValue + $monthlySolar), 2);

                $todayLogData = ['battery-discharge' => (string)(double)$monthlyDischargeValue, 'solar' => (string)(double)$monthlySolar, 'grid-import' => (string)(double)$monthlyGridImportValue, 'total-consumption' => (string)$consumption];
            } else if ($time == 'year') {
                $year = date('Y', strtotime($date));
                $yearlyDischargeValue = 0;
                $yearlyGridImportValue = 0;
                $yearlySolar = 0;
                $consumption = 0;
                for ($i = 0; $i < count($plantHybridIds); $i++) {

                    $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_peak_hours_discharge_energy', 'yearly_use_value', 'yearly_use_ratio', 'yearly_discharge_ratio', 'yearly_grid_ratio', 'yearlyGeneration', 'yearly_peak_hours_grid_import', 'yearlyConsumption', 'yearly_peak_hours_consumption', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $plantHybridIds[$i])->whereYear('created_at', $year)->orderBy('created_at', 'DESC')->first();

                    if ($yearlyProcessedData) {
                        $yearlyDischargeValue += round((double)$yearlyProcessedData->yearly_use_value * (double)$yearlyProcessedData->yearly_discharge_ratio / 100, 2);
                        $yearlySolar += round((double)$yearlyProcessedData->yearly_use_value * (double)$yearlyProcessedData->yearly_use_ratio / 100, 2);
                        $consumption += round($yearlyProcessedData->yearly_use_value, 2);
                    }
                }
                $yearlyGridImportValue = round((double)$consumption - ($yearlyDischargeValue + $yearlySolar), 2);

                $todayLogData = ['battery-discharge' => (string)(double)$yearlyDischargeValue, 'solar' => (string)(double)$yearlySolar, 'grid-import' => (string)(double)$yearlyGridImportValue, 'total-consumption' => (string)$consumption];
            }

            $data = ['status' => 1, 'data' => $todayLogData];
            return $data;

        }
    }

    public
    function solarEnergyUtilization(Request $request)
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
            $date = date('Y', $requestDate);
        }

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
                $plantsID = Plant::whereIn('id', $plantsID)->where('system_type', 4)->pluck('id')->toArray();

            } else {

                foreach (explode(',', $request->get('plant_id')) as $id) {

                    $plantsID[] = (int)$id;
                }
            }
        }

        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->where('system_type', 4)->pluck('id')->toArray();

        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;
        }


        if (empty($plantsIDD)) {
               //            $batteryDataArray = [];
            $batteryDataArray =['hour' => '0', 'battery-charge-energy' => '0', 'grid-export' => '0' ,'load' => '0'];
            return ['status' => 1, 'data' => $batteryDataArray];
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

        $plantIdsData = $plantsData->pluck('id')->toArray();


        $plantHistoryGraph = [];
        $legendArray = [];
        $todayLogData = [];
        $batteryDischargeEnergy = 0;
        $current = ['battery-charging', 'load', 'grid_export'];
        $gridExport = 0;
        $load = 0;
        $batteryCharging = 0;
        $dailyChargingEnergy = 0;
        $generation = 0;
        $plantDetail = Plant::select('id','plant_name','meter_type','system_type')->where("id",$request->plant_id)->first();
      
        for ($i = 0; $i < count($plantIdsData); $i++) {
            $currentProcessedData = DailyProcessedPlantDetail::select('generation_value','generation_ratio','grid_ratio','charge_ratio','dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantIdsData[$i])->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->first();

            if ($currentProcessedData) {
                if($plantDetail->meter_type == "Solis-Cloud"){
                    $generation = $currentProcessedData->dailyGeneration;
                    $gridExport = round((double)$currentProcessedData->dailySellEnergy, 2);
                    $dailyChargingEnergy = round((double)$currentProcessedData->daily_charge_energy, 2);
                    $load = round(((double)$currentProcessedData->dailyGeneration - (double)$currentProcessedData->dailySellEnergy), 2);
                }else{
                $dailyChargingEnergy += round(((double)$currentProcessedData->generation_value * (double)$currentProcessedData->charge_ratio) / 100, 2);
                $gridExport += round(((double)$currentProcessedData->generation_value * (double)$currentProcessedData->grid_ratio) / 100, 2);
                $generation += $currentProcessedData->generation_value;
                $load += round(((double)$currentProcessedData->generation_value * (double)$currentProcessedData->generation_ratio) / 100, 2);
            }
          }
        }
        foreach ($current as $key => $currentData) {

            $todayLogDataSum = array();
            if ($time == 'day') {
                $todayLogData = ['battery-charging' => (string)(double)$dailyChargingEnergy, 'Load' => (string)(double)$load, 'grid-export' => (string)(double)$gridExport, 'generation' => (string)$generation];


            } else if ($time == 'month') {
                $month = date('Y-m', strtotime($date));
                $monthlyChargeValue = 0;
                $monthlyGridExportValue = 0;
                $monthlyLoadValue = 0;
                $generation = 0;
                for ($i = 0; $i < count($plantIdsData); $i++) {

                    $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantIdsData[$i])->where('created_at', 'LIKE', $month . '%')->first();

                    if ($monthlyProcessedData) {
                        if($plantDetail->meter_type == "Solis-Cloud"){
                            $generation = $monthlyProcessedData->monthlyGeneration;
                            $monthlyGridExportValue = round((double)$monthlyProcessedData->monthlySellEnergy, 2);
                            $monthlyLoadValue = round(((double)$monthlyProcessedData->monthlyGeneration - (double)$monthlyProcessedData->monthlySellEnergy), 2);
                        }else{
                        $monthlyGridExportValue += round(((double)$monthlyProcessedData->monthly_generation_value * (double)$monthlyProcessedData->monthly_grid_ratio) / 100, 2);
                        $monthlyChargeValue += round(((double)$monthlyProcessedData->monthly_generation_value * (double)$monthlyProcessedData->monthly_charge_ratio) / 100, 2);
                        $monthlyLoadValue += round(((double)$monthlyProcessedData->monthly_generation_value * (double)$monthlyProcessedData->monthly_generation_ratio) / 100, 2);
                        $generation += $monthlyProcessedData->monthly_generation_value;
                       }
                    }
                }
                $todayLogData = ['battery-charging' => (string)(double)$monthlyChargeValue, 'Load' => (string)(double)$monthlyLoadValue, 'grid-export' => (string)(double)$monthlyGridExportValue, 'generation' => (string)$generation];

            } else if ($time == 'year') {
                $year = date('Y', strtotime($date));
                $yearlyChargeValue = 0;
                $yearlyGridExportValue = 0;
                $yearlyLoadValue = 0;
                for ($i = 0; $i < count($plantIdsData); $i++) {

                    $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_generation_value', 'yearly_generation_ratio', 'yearly_charge_ratio', 'yearly_grid_ratio', 'yearly_peak_hours_discharge_energy', 'yearlyGeneration', 'yearly_peak_hours_grid_import', 'yearlyConsumption', 'yearly_peak_hours_consumption', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $plantIdsData[$i])->whereYear('created_at', $year)->orderBy('created_at', 'DESC')->first();
                      
                    if ($yearlyProcessedData) {
                        if($plantDetail->meter_type == "Solis-Cloud"){
                            $generation = $yearlyProcessedData->yearlyGeneration;
                            $yearlyGridExportValue = round((double)$yearlyProcessedData->yearlySellEnergy, 2);
                            $yearlyLoadValue = round(((double)$yearlyProcessedData->yearlyGeneration - (double)$yearlyProcessedData->yearlySellEnergy), 2);
                            // return [$generation,$yearlyGridExportValue,$yearlyLoadValue];
                        }else{
                         
                        $yearlyChargeValue += round(((double)$yearlyProcessedData->yearly_generation_value * (double)$yearlyProcessedData->yearly_charge_ratio) / 100, 2);
                        $yearlyGridExportValue += round(((double)$yearlyProcessedData->yearly_generation_value * (double)$yearlyProcessedData->yearly_grid_ratio) / 100, 2);
                        $yearlyLoadValue += round(((double)$yearlyProcessedData->yearly_generation_value * (double)$yearlyProcessedData->yearly_generation_ratio) / 100, 2);
                        $generation += $yearlyProcessedData->yearly_generation_value;
                        }
                     }
                }
                $todayLogData = ['battery-charging' => (string)(double)$yearlyChargeValue, 'Load' => (string)(double)$yearlyLoadValue, 'grid-export' => (string)(double)$yearlyGridExportValue, 'generation' => (string)$generation];

            }

            $data = ['status' => 1, 'data' => $todayLogData];
            return $data;

        }
    }

    public
    function PlantsList(Request $request)
    {
        $userID = $request->user()->id;
        $userPlants = PlantUser::where('user_id', $userID)->select('plant_id')
            ->get()
            ->pluck('plant_id')->toArray();

        $plants = Plant::whereIn('id', $userPlants)->get();

        $plants = $plants->map(function ($plant) {
            $processed_data = DailyProcessedPlantDetail::where('plant_id', $plant->id)->whereBetween('created_at', [date('Y-m-d 0:00:00'), date('Y-m-d 23:59:00')])->first();

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

            $current_data = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type', 'collect_time', 'created_at')->where('plant_id', $plant->id)->orderBy('collect_time', 'desc')->first();
            $plant['current_generation'] = $current_data ? number_format($current_data->current_generation, 2) : 0;
            $plant['current_consumption'] = $current_data ? number_format($current_data->current_consumption, 2) : 0;
            $plant['battery_power'] = $current_data ? number_format($current_data->battery_power, 2) : '0';
            if ($plant['battery_type']) {
                $plant['battery_type'] = $current_data ? $current_data->battery_type : '0';
            } else {
                $plant['battery_type'] = '0';
            }

//            $plant['current_generation'] = $current_generation;
            $plant['power'] = isset($processed_data) && isset($processed_data['dailyMaxSolarPower']) ? (string)$processed_data['dailyMaxSolarPower'] : '0';


//            $plant['current_generation'] = $current_generation;
            $plant['power'] = isset($processed_data) ? $processed_data['dailyMaxSolarPower'] . ' kW' : '0.00 kW';
            $plant['daily_generation'] = isset($processed_data) ? number_format($processed_data['dailyGeneration'], 2) : '0.00 kW';
            $plant['daily_revenue'] = isset($processed_data) ? number_format($processed_data['dailyGeneration'] * $plant->benchmark_price, 2, '.', ',') : '0.00 kW';

            $plant['last_updated'] = isset($processed_data) ? date('h:i A, d/m', strtotime($processed_data['lastUpdated'])) : '0.00 kW';
            $percentage_value = ($plant['current_generation'] / $plant->capacity * 100);

            $plant['percentage_value'] = number_format($percentage_value, 2, '.', ',');

            if ($plant->plant_pic != null) {

                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/' . $plant->plant_pic;
            } else {

                $plant->plant_pic = $this->url_scheme . '://' . $this->domain . '/public/plant_photo/plant_avatar.png';
            }

            return $plant;

            // $plant['progress_bar'] = '1000 PKR';
            // $plant['percentage'] = '7%';
            // $plant['plant_efficiency'] = 50;
        });

        return $this->sendResponse(1, 'Showing all plants', $plants);
    }
// Latest history graph chart
    public function HistoryGraphData(Request $request)
    {

        $input = $request->all();
        $plantsID = array();
        $plantsIDD = array();
        $companyID = array();
        $userID = $request->user()->id;
        $plantType = $request->type;


        if ($plantType == 'hybrid') {
            $systemType = 4;
        } else {
            $systemType = 2;
        }

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
                $plantsID = Plant::whereIn('id', $plantsID)->where('system_type', $systemType)->pluck('id')->toArray();

            } else {

                foreach (explode(',', $request->get('plant_id')) as $id) {

                    $plantsID[] = (int)$id;
                }
            }
        }


        $plantsCompanyArray = Plant::whereIn('company_id', $companyID)->where('system_type', $systemType)->pluck('id')->toArray();

        if ($request->has('company_id')) {

            $plantsIDD = array_intersect($plantsCompanyArray, $plantsID);
        } else {

            $plantsIDD = $plantsID;
        }
        if (empty($plantsIDD)) {
            $response = [
                'status' => 1,
                'message' => 'Plant not found',
                //'daily' => $result[0],
                'result' => '0',
                'labels' => '0'
            ];
            return response()->json($response, 200);

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
        if (isset($input['parameter'])) {
            $parameter = ucfirst($input['parameter']);
        } else {
            $parameter = '';
        }

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
//        return $plants;

        if (count($plants) == 0) {
            return $this->sendError(0, 'Plant not found.');
        }

        if ($duration == 'Daily') {

            $result = $this->getDailyGraphData($systemType, $request->plant_id, $plant_id,$plantType, $parameter, $date);

            if($request->plant_id == "all" && $systemType == '4'){
                $response = [
                    'status' => 1,
                    'message' => $duration . ' ' . $parameter,
                    //'daily' => $result[0],
                    'result' => $result[0],
                    'labels' => $result[1]
                ];
            }else {


                $finalArrayData = [];
                $arrayData = array_values(array_unique($result[2]));


                for ($i = 0; $i < count($arrayData); $i++) {
                    if (count($result[0]) == 0) {

                        $dataResult = ['time' => $arrayData[$i], 'plant_id' => $request->plant_id, 'system_type' => $systemType, 'time_type' => 'Daily', 'generation' => null, 'consumption' => null, 'buy_energy' => null, 'sell_energy' => null, 'grid' => null, 'saving' => null, 'battery_power' => null, 'battery_soc' => null];

                    } else {

                        foreach ($result[0] as $key => $data) {
                            if ($data['time'] == $arrayData[$i]) {
                                $dataResult = $data;
                                break;
                            } else {
                                $dataResult = ['time' => $arrayData[$i], 'plant_id' => $request->plant_id, 'system_type' => $systemType, 'time_type' => 'Daily', 'generation' => null, 'consumption' => null, 'buy_energy' => null, 'sell_energy' => null, 'grid' => null, 'saving' => null, 'battery_power' => null, 'battery_soc' => null];
                            }
                        }


                    }

                    array_push($finalArrayData, $dataResult);

                }

                $response = [
                    'status' => 1,
                    'message' => $duration . ' ' . $parameter,
                    //'daily' => $result[0],
                    'result' => $finalArrayData,
                    'labels' => $result[1]
                ];
            }
            return response()->json($response, 200);
        } else if ($duration == 'Weekly') {
            return $this->sendResponse(1, $duration . ' ' . $parameter, $this->getWeeklyData($plant_id, $parameter));
        } else if ($duration == 'Monthly') {

            $res = $this->getMonthlyGraphData($systemType, $request->plant_id, $plant_id, $plantType, $parameter, $date);


            $response = [
                'status' => 1,
                'message' => $duration . ' ' . $parameter,
                'result' => $res[0],
                'labels' => $res[1]
            ];
            return response()->json($response, 200);

        } else if ($duration == 'Yearly') {

            $res = $this->getYearlyGraphData( $request->plant_id, $plant_id, $plantType, $parameter, $date);

            $response = [
                'status' => 1,
                'message' => $duration . ' ' . $parameter,
                'result' => $res[0],
                'labels' => $res[1]
            ];
            return response()->json($response, 200);
        }
    }

    private function getDailyHybridData($SystemType,$plants,$plant_id, $parameter, $date)
    {


        $hourly_data = array();
        $daily_total = 0;
        $plant_detail = Plant::whereIn('id', $plant_id)->get(['id', 'meter_type', 'benchmark_price']);
        $benchmark_price = count($plant_detail) > 0 ? $plant_detail[0]->benchmark_price : 0;
        $meter_type = count($plant_detail) > 0 ? $plant_detail[0]->meter_type : '';
        if($plants == "all" && $SystemType == '4'){
            $timearrayNew = [];
            $DataArray = [];
            $StartTime = $date . ' 00:00:00';

            for($k = 0; $k< 288 ;$k++){

                $Date = strtotime($StartTime)+(60*5);
                $EndTime = date("Y-m-d H:i:s",$Date);

                $current_gen['generation'] = (string)round((double)ProcessedCurrentVariable::Select('collect_time', 'plant_id')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_generation'),2);
                $current_gen['consumption'] = (string)round((double)ProcessedCurrentVariable::Select('collect_time', 'plant_id')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_consumption'),2);
                $current_gen['buy_energy'] = (string)round((double)ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->where('grid_type', '+ve')->sum('current_grid'), 2);
                $current_gen['sell_energy'] = (string)round((double)ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->where('grid_type', '-ve')->sum('current_grid'), 2);
                $current_gen['grid'] = (string)round((double)ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_grid'), 2);
                $current_gen['saving'] = (string)round((double)ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->sum('current_saving'), 2);
                $batteryPower = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($StartTime), date($EndTime)])->latest()->first();
                $batteryData = '0.00';
                $batterySoc = '0';
                if ($batteryPower) {
                    if ($batteryPower->battery_power != null) {
                        $batteryData = $batteryPower->battery_power;
                    }
                    $batterySoc = $batteryPower->battery_capacity;
                }
                $current_gen['battery_power'] = (string)round($batteryData, 2);
                $current_gen['battery_soc'] = $batterySoc;
                $current_gen['time'] = Date('H:i' ,strtotime($StartTime));
                array_push($DataArray , $current_gen);
                $Date = strtotime($StartTime)+(60*5);
                $StartTime = date("Y-m-d H:i:s",$Date);
                array_push($timearrayNew,$StartTime);

            }

            if (DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->exists()) {

                $dataResponse = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->get();

                $DailyDataArray = json_decode(json_encode($dataResponse), true);
                $dailyGenera = array_sum(array_column($DailyDataArray, 'dailyGeneration'));
                $dailyConsum = array_sum(array_column($DailyDataArray, 'dailyConsumption'));
                $dailyGrid = array_sum(array_column($DailyDataArray, 'dailyGridPower'));
                $dailyBought = array_sum(array_column($DailyDataArray, 'dailyBoughtEnergy'));
                $dailySell = array_sum(array_column($DailyDataArray, 'dailySellEnergy'));
                $dailySav = array_sum(array_column($DailyDataArray, 'dailySaving'));
                $dailyCharge_ener = array_sum(array_column($DailyDataArray, 'daily_charge_energy'));
                $dailyDis_charge = array_sum(array_column($DailyDataArray, 'daily_discharge_energy'));

                $totalGeneration = (string)round($dailyGenera, 2);
                $totalConsumption = (string)round($dailyConsum, 2);
//                $totalGrid = (string)round($dailyGrid, 2);
                $totalBuy = (string)round($dailyBought, 2);
                $totalSell = (string)round($dailySell, 2);
                $totalGrid = (string)round($totalBuy - $totalSell,2);
                $totalSaving = (string)$dailySav;
                $totalChargeEnergy = (string)round($dailyCharge_ener, 2);
                $totalDischargeEnergy = (string)round($dailyDis_charge, 2);
            } else {
                $totalGeneration = "0.00";
                $totalConsumption = "0.00";
                $totalGrid = "0.00";
                $totalBuy = "0.00";
                $totalSell = "0.00";
                $totalSaving = "0";
                $totalChargeEnergy = "0.00";
                $totalDischargeEnergy = "0.00";
            }

            $daily_total = ['generation' => $totalGeneration, 'consumption' => $totalConsumption, 'grid' => $totalGrid, 'buy' => $totalBuy, 'sell' => $totalSell, 'saving' => $totalSaving, 'charge' => $totalChargeEnergy, 'discharge' => $totalDischargeEnergy];
            return [$DataArray, $daily_total];

        }else {

            $hourly_val = ProcessedCurrentVariable::Select('collect_time', 'plant_id')->whereIn('plant_id', $plant_id)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:00')])->groupBy('collect_time')->get();;

            if (DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->exists()) {

                $dataResponse = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->get();

                $DailyDataArray = json_decode(json_encode($dataResponse), true);
                $dailyGenera = array_sum(array_column($DailyDataArray, 'dailyGeneration'));
                $dailyConsum = array_sum(array_column($DailyDataArray, 'dailyConsumption'));
                $dailyGrid = array_sum(array_column($DailyDataArray, 'dailyGridPower'));
                $dailyBought = array_sum(array_column($DailyDataArray, 'dailyBoughtEnergy'));
                $dailySell = array_sum(array_column($DailyDataArray, 'dailySellEnergy'));
                $dailySav = array_sum(array_column($DailyDataArray, 'dailySaving'));
                $dailyCharge_ener = array_sum(array_column($DailyDataArray, 'daily_charge_energy'));
                $dailyDis_charge = array_sum(array_column($DailyDataArray, 'daily_discharge_energy'));

                $totalGeneration = (string)round($dailyGenera, 2);
                $totalConsumption = (string)round($dailyConsum, 2);
                $totalGrid = (string)round($dailyGrid, 2);
                $totalBuy = (string)round($dailyBought, 2);
                $totalSell = (string)round($dailySell, 2);
                $totalSaving = (string)$dailySav;
                $totalChargeEnergy = (string)round($dailyCharge_ener, 2);
                $totalDischargeEnergy = (string)round($dailyDis_charge, 2);
            } else {
                $totalGeneration = "0.00";
                $totalConsumption = "0.00";
                $totalGrid = "0.00";
                $totalBuy = "0.00";
                $totalSell = "0.00";
                $totalSaving = "0";
                $totalChargeEnergy = "0.00";
                $totalDischargeEnergy = "0.00";
            }

            $daily_total = ['generation' => $totalGeneration, 'consumption' => $totalConsumption, 'grid' => $totalGrid, 'buy' => $totalBuy, 'sell' => $totalSell, 'saving' => $totalSaving, 'charge' => $totalChargeEnergy, 'discharge' => $totalDischargeEnergy];

            $todayLogTime = [];
            foreach ($hourly_val as $key => $value) {
                $todayLogTime[] = date('H:i', strtotime($value->collect_time));
                $current_gen['time'] = date('H:i', strtotime($value->collect_time));
                $current_gen['generation'] = (string)round((double)ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_generation'), 2);
                if($SystemType == '4'){
                    $consumption = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_consumption');
                    $current_gen['consumption'] = (string)round((double)$consumption/1000, 2);
                }else{
                    $consumption = $this->unitConversion(ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_consumption'), 'W');
                    $current_gen['consumption'] = (string)round((double)$consumption[0], 2);
                }
                $current_gen['buy_energy'] = (string)round((double)ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->where('grid_type', '+ve')->sum('current_grid'), 2);
                $current_gen['sell_energy'] = (string)round((double)ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->where('grid_type', '-ve')->sum('current_grid'), 2);
                $current_gen['grid'] = (string)round((double)ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_grid'), 2);
                $current_gen['saving'] = (string)round((double)ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->sum('current_saving'), 2);
                $batteryPower = ProcessedCurrentVariable::whereIn('plant_id', $plant_id)->where('collect_time', $value->collect_time)->latest()->first();
                $batteryData = '0.00';
                $batterySoc = '0';
                if ($batteryPower) {
                    if ($batteryPower->battery_power != null) {
                        $batteryData = $batteryPower->battery_power;
                    }
                    $batterySoc = $batteryPower->battery_capacity;
                }
                $current_gen['battery_power'] = (string)round($batteryData, 2);
                $current_gen['battery_soc'] = $batterySoc;
                array_push($hourly_data, $current_gen);
            }

            if (!empty($todayLogTime)) {

                $startTime = new \DateTime(date('Y-m-d ' . end($todayLogTime), strtotime($date)));
            } else {

                $startTime = new \DateTime(date('Y-m-d 00:00', strtotime($date)));
            }

            $endTime = new \DateTime(date('Y-m-d 23:55', strtotime($date)));
            $timeStep = 5;

            while ($startTime <= $endTime) {
                $todayLogTime[] = $startTime->format('H:i');
                $startTime->add(new \DateInterval('PT' . $timeStep . 'M'));
            }

            $timeArray = $todayLogTime;

            return [$hourly_data, $daily_total, $timeArray];

        }
    }
    private
    function getMonthlyHybridData($plant_id, $parameter, $date)
    {
        $plant_detail = Plant::whereIn('id', $plant_id)->get(['meter_type', 'benchmark_price']);

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

            $dailyGeneration = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyGeneration');
            $dailyConsumption = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyConsumption');
            $dailyGridPower = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyGridPower');
            $dailyBuyEnergy = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyBoughtEnergy');
            $dailySellEnergy = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailySellEnergy');
            $dailySaving = DailyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailySaving');

            $array_data = array('time' => (string)$i, 'generation' => ($dailyGeneration > 0) ? (string)round($dailyGeneration, 2) : '0.00',
                'consumption' => ($dailyConsumption > 0) ? (string)round($dailyConsumption, 2) : '0.00',
                'buy_energy' => ($dailyBuyEnergy > 0) ? (string)round($dailyBuyEnergy, 2) : '0.00',
                'sell_energy' => ($dailySellEnergy > 0) ? (string)round($dailySellEnergy, 2) : '0.00',
                'saving' => ($dailySaving > 0) ? (string)round($dailySaving, 2) : '0.00',
                'grid' => ($dailyGridPower > 0) ? (string)round($dailyGridPower, 2) : '0.00',
                'battery_power' => '',
                'battery_soc' => ''
            );
            array_push($monthly_data, $array_data);
        }

        if (MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->exists()) {

            $dataRespmonthly = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->where('created_at', 'LIKE', $date . '%')->get();

            $MonthlyDataArray = json_decode(json_encode($dataRespmonthly), true);
            $monthlyGenera = array_sum(array_column($MonthlyDataArray, 'monthlyGeneration'));
            $monthlyConsum = array_sum(array_column($MonthlyDataArray, 'monthlyConsumption'));
            $monthlyGrid = array_sum(array_column($MonthlyDataArray, 'monthlyGridPower'));
            $monthlyBought = array_sum(array_column($MonthlyDataArray, 'monthlyBoughtEnergy'));
            $monthlySell = array_sum(array_column($MonthlyDataArray, 'monthlySellEnergy'));
            $monthlySav = array_sum(array_column($MonthlyDataArray, 'monthlySaving'));
            $monthlyCharge_ener = array_sum(array_column($MonthlyDataArray, 'monthly_charge_energy'));
            $monthlyDis_charge = array_sum(array_column($MonthlyDataArray, 'monthly_discharge_energy'));

            $totalGeneration = (string)round($monthlyGenera, 2);
            $totalConsumption = (string)round($monthlyConsum, 2);
            $totalGrid = (string)round($monthlyGrid, 2);
            $totalBuy = (string)round($monthlyBought, 2);
            $totalSell = (string)round($monthlySell, 2);
            $totalSaving = (string)$monthlySav;
            $totalChargeEnergy = (string)round($monthlyCharge_ener, 2);
            $totalDischargeEnergy = (string)round($monthlyDis_charge, 2);
        } else {
            $totalGeneration = "0.00";
            $totalConsumption = "0.00";
            $totalGrid = "0.00";
            $totalBuy = "0.00";
            $totalSell = "0.00";
            $totalSaving = "0";
            $totalChargeEnergy = "0.00";
            $totalDischargeEnergy = "0.00";
        }
        $monthly_total = ['generation' => $totalGeneration, 'consumption' => $totalConsumption, 'grid' => $totalGrid, 'buy' => $totalBuy, 'sell' => $totalSell, 'saving' => $totalSaving, 'charge' => $totalChargeEnergy, 'discharge' => $totalDischargeEnergy];


        return [$monthly_data, $monthly_total];
    }

    private
    function getYearlyHybridData($plant_id, $parameter, $date)
    {
        $monthly_data = array();
        $yearly_total = 0;
        $plant_detail = Plant::whereIn('id', $plant_id)->get(['meter_type', 'benchmark_price']);
        $benchmark_price = count($plant_detail) > 0 ? $plant_detail[0]->benchmark_price : 0;

        for ($i = 1; $i <= 12; $i++) {
            if ($i < 10) {
                $i = '0' . $i;
            }
            $monthlyGeneration = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGeneration');
            $monthlyConsumption = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyConsumption');
            $monthlyGridPower = MonthlyProcessedPlantDetail::whereIN('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGridPower');
            $monthlyBuyEnergy = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyBoughtEnergy');
            $monthlySellEnergy = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySellEnergy');
            $monthlySaving = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySaving');
            $array_data = array('time' => (string)$i,
                'generation' => (string)round($monthlyGeneration, 2),
                'consumption' => (string)round($monthlyConsumption, 2),
                'grid' => (string)round($monthlyGridPower, 2),
                'buy_energy' => (string)round($monthlyBuyEnergy, 2),
                'sell_energy' => (string)round($monthlySellEnergy, 2),
                'saving' => (string)round($monthlySaving, 2),
                'battery_power' => '',
                'battery_soc' => ''
            );
            array_push($monthly_data, $array_data);
        }
        if (YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->exists()) {

            $yearlydataResponse = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_id)->whereYear('created_at', $date)->get();

            $YearlyDataArray = json_decode(json_encode($yearlydataResponse), true);
            $YearlyGenera = array_sum(array_column($YearlyDataArray, 'yearlyGeneration'));
            $YearlyConsum = array_sum(array_column($YearlyDataArray, 'yearlyConsumption'));
            $YearlyGrid = array_sum(array_column($YearlyDataArray, 'yearlyGridPower'));
            $YearlyBought = array_sum(array_column($YearlyDataArray, 'yearlyBoughtEnergy'));
            $YearlySell = array_sum(array_column($YearlyDataArray, 'yearlySellEnergy'));
            $YearlySav = array_sum(array_column($YearlyDataArray, 'yearlySaving'));
            $YearlyCharge_ener = array_sum(array_column($YearlyDataArray, 'yearly_charge_energy'));
            $YearlyDis_charge = array_sum(array_column($YearlyDataArray, 'yearly_discharge_energy'));

            $totalGeneration = (string)round($YearlyGenera, 2);
            $totalConsumption = (string)round($YearlyConsum, 2);
            $totalGrid = (string)round($YearlyGrid, 2);
            $totalBuy = (string)round($YearlyBought, 2);
            $totalSell = (string)round($YearlySell, 2);
            $totalSaving = (string)$YearlySav;
            $totalChargeEnergy = (string)round($YearlyCharge_ener, 2);
            $totalDischargeEnergy = (string)round($YearlyDis_charge, 2);
        } else {
            $totalGeneration = "0.00";
            $totalConsumption = "0.00";
            $totalGrid = "0.00";
            $totalBuy = "0.00";
            $totalSell = "0.00";
            $totalSaving = "0";
            $totalChargeEnergy = "0.00";
            $totalDischargeEnergy = "0.00";
        }
        $yearly_total = ['generation' => $totalGeneration, 'consumption' => $totalConsumption, 'grid' => $totalGrid, 'buy' => $totalBuy, 'sell' => $totalSell, 'saving' => $totalSaving, 'charge' => $totalChargeEnergy, 'discharge' => $totalDischargeEnergy];
        return [$monthly_data, $yearly_total];

    }

    function PlantCo2Reduction(){

        $plant_data_ids = Plant::where('company_id', 7)->pluck('id')->toArray();

        if(!empty($plant_data_ids)) {
            $PlantCo2Reduction = TotalProcessedPlantDetail::whereIn('plant_id', $plant_data_ids)->sum('plant_total_reduction');
        }
        return round((double)$PlantCo2Reduction,2);

    }
    function PlantTotalCurrentPower(){

        $plant_data_ids = Plant::where('company_id', 7)->pluck('id')->toArray();

        if(!empty($plant_data_ids)) {
            $AllPlantCurrentPower = TotalProcessedPlantDetail::whereIn('plant_id', $plant_data_ids)->sum('plant_total_current_power');
        }
        return round((double)$AllPlantCurrentPower,2);

    }
    function PlantTotalGeneration(){

        $plant_data_ids = Plant::where('company_id', 7)->pluck('id')->toArray();

        if(!empty($plant_data_ids)) {
            $AllPlantTotalGeneration = (TotalProcessedPlantDetail::whereIn('plant_id', $plant_data_ids)->sum('plant_total_generation')) / 1000;
        }
        return round((double)$AllPlantTotalGeneration,2);

    }
    function singlePlantCo2Reduction($PlantID){

        $PlantCo2Reduction = 0;
        $plant_data_ids = Plant::where('company_id', 7)->where('id',$PlantID)->pluck('id');

        if(count($plant_data_ids) != 0) {
            $PlantCo2Reduction = TotalProcessedPlantDetail::where('plant_id', $plant_data_ids)->sum('plant_total_reduction');
        }else{
            return 'Plant Not Found';
        }

        return round((double)$PlantCo2Reduction,2);

    }
    function singlePlantTotalCurrentPower($PlantID){

        $AllPlantCurrentPower = 0;
        $plant_data_ids = Plant::where('company_id', 7)->where('id',$PlantID)->pluck('id');

        if(count($plant_data_ids) != 0) {
            $AllPlantCurrentPower = TotalProcessedPlantDetail::where('plant_id', $plant_data_ids)->sum('plant_total_current_power');
        }else{
            return 'Plant Not Found';
        }

        return round((double)$AllPlantCurrentPower,2);

    }
    function singlePlantTotalGeneration($PlantID){

        $AllPlantTotalGeneration = 0;
        $plant_data_ids = Plant::where('company_id', 7)->where('id',$PlantID)->pluck('id');

        if(count($plant_data_ids) != 0) {
            $AllPlantTotalGeneration = (TotalProcessedPlantDetail::where('plant_id', $plant_data_ids)->sum('plant_total_generation')) / 1000;
        }else{
            return 'Plant Not Found';
        }

        return round((double)$AllPlantTotalGeneration,2);

    }
    function  getPlantID(Request $request){
//        return $request->all();
        $inverterDetail  = InverterSerialNo::select('plant_id')->where('dv_inverter',$request->inverter_sn)->first();
        if ($inverterDetail) {
            return $this->sendResponse(1, 'Showing plant ID', $inverterDetail);
        } else {
            return $this->sendError(0, 'No Detail Found', $inverterDetail);
        }


    }
}
