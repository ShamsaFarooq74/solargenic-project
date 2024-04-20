<?php

namespace App\Http\Controllers\Admin;

use App\Http\Models\DailyWeatherModel;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Models\StationBattery;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Plant;
use App\Http\Models\PlantSite;
use App\Http\Models\PlantMPPT;
use App\Http\Models\PlantDetail;
use App\Http\Models\User;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\DailyProcessedPlantEMIDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\MonthlyProcessedPlantEMIDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\YearlyProcessedPlantEMIDetail;
use App\Http\Models\TotalProcessedPlantDetail;
use App\Http\Models\TotalProcessedPlantEMIDetail;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterEMIDetail;
use App\Http\Models\InverterMPPTDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\Inverter;
use App\Http\Models\GenerationLog;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\Company;
use App\Http\Models\UserCompany;
use App\Http\Models\PlantUser;
use App\Http\Models\Notification;
use App\Http\Models\SystemType;
use App\Http\Models\Ticket;
use App\Http\Models\TicketSource;
use App\Http\Models\Setting;
use App\Http\Models\TicketAgent;
use App\Http\Models\Employee;
use App\Http\Models\PlantType;
use App\Http\Models\PlantMeterType;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\SiteInverterDetail;
use App\Http\Models\Weather;
use Spatie\Permission\Models\Role;
use \GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Api\PlantSiteDataController;
use App\Http\Controllers\HardwareAPIData\HuaweiController;
use App\Http\Controllers\HardwareAPIData\SunGrowController;
use App\Http\Controllers\HardwareAPIData\SolisController;

class PlantsController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set("Asia/Karachi");
        Session::put(['plant_name' => '']);
    }

    public function allPlants(Request $request)
    {

        return redirect()->route('admin.plants');

        abort(404);

        $com_arr = [];

        $input = $request->all();

        //dd($request->plant_name);
        $plant_nam = !isset($request->plant_name) || $request->plant_name == null || $request->plant_name == "all" ? 'all' : $request->plant_name;
        $company = !isset($request->company) || $request->company == null || $request->company == "all" ? 'all' : $request->company;
        $plant_status = $request->plant_status == "all" ? '' : $request->plant_status;
        $plant_type = $request->plant_type == "all" ? '' : $request->plant_type;
        $system_type = $request->system_type == "all" ? '' : $request->system_type;
        $province = $request->province == "all" ? '' : $request->province;
        $city = $request->city == "all" ? '' : $request->city;
        $plants_input = $request->plants == "all" ? '' : $request->plants;

        $plant_names = [];
        $plant_name = [];
        $company_arr = array();

        if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
            //dd('Auth::user()->roles == 1');
            $plant_names = Plant::pluck('id');
            $plant_names = $plant_names->toArray();

            $plants = Plant::all();
            $plant_type_id = Plant::groupBy('plant_type')->pluck('plant_type');
            $system_type_id = Plant::groupBy('system_type')->pluck('system_type');

            $filter_data['company_array'] = Company::all();
            $filter_data['province_array'] = Plant::select('province')->where('province', '!=', NULL)->groupBy('province')->get();
            $filter_data['city_array'] = Plant::select('city')->where('city', '!=', NULL)->groupBy('city')->get();

            $system_types = array();
            $plant_types = array();
            foreach ($plants as $plant) {
                $system_types[] = $plant->system_type;
                $plant_types[] = $plant->plant_type;
            }
            $filter_data['system_type'] = SystemType::whereIn('id', $system_types)->get();
            $filter_data['plant_type'] = PlantType::whereIn('id', $plant_types)->get();

            $filter_data['plants'] = Plant::get(['id', 'plant_name', 'company_id']);
        } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
            //dd('Auth::user()->roles == 3');
            $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
            $plant_names = $plant_names->toArray();

            if (empty($plant_names) && Auth::user()->roles == 3) {
                return redirect()->route('admin.build.plant');
            } else if (empty($plant_names) && Auth::user()->roles == 4) {
                return redirect()->route('admin.plants');
            }

            $plants = Plant::whereIn('id', $plant_names)->get();
            $plant_type_id = Plant::groupBy('plant_type')->whereIn('id', $plant_names)->pluck('plant_type');
            $system_type_id = Plant::groupBy('system_type')->whereIn('id', $plant_names)->pluck('system_type');

            $filter_data['province_array'] = Plant::select('province')->where('province', '!=', NULL)->whereIn('id', $plant_names)->groupBy('province')->get();
            $filter_data['city_array'] = Plant::select('city')->where('city', '!=', NULL)->whereIn('id', $plant_names)->groupBy('city')->get();

            $system_types = array();
            $plant_types = array();
            foreach ($plants as $plant) {
                $system_types[] = $plant->system_type;
                $plant_types[] = $plant->plant_type;
            }
            $filter_data['system_type'] = SystemType::whereIn('id', $system_types)->get();
            $filter_data['plant_type'] = PlantType::whereIn('id', $plant_types)->get();
            $filter_data['plants'] = Plant::whereIn('id', $plant_names)->get();
        }

        if ($plant_nam == 'all') {

            $plant_name = $plant_names;

        } else {

            $plant_name = $plant_nam;

            $input['plant_name'] = $plant_name;
        }

        if ($company == 'all') {

            $company_arr = UserCompany::where('user_id', Auth::user()->id)->pluck('company_id')->toArray();
            $company_arr = (array)$company_arr;
            $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
            $plant_name = array_intersect($plant_name, $company_plant_arr);

            if (isset($request->company)) {

                $input['company'] = $company_arr;
                //$input['plant_name'] = $plant_name;
            }
        } else {

            $company_arr = $company;
            $company_arr = (array)$company_arr;
            $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
            $plant_name = array_intersect($plant_name, $company_plant_arr);

            if (isset($request->company)) {

                $input['company'] = $company_arr;
                $input['plant_name'] = $plant_name;
            }
        }

        Session::put(['filter' => $input]);

        $plant_name = (array)$plant_name;
        //$company_arr = (array)$company_arr;
        //return [$plant_name, $company_arr];

        $where_array = array();
        if ($system_type) {
            $where_array['plants.system_type'] = $system_type;
        }
        if ($plant_type) {
            $where_array['plants.plant_type'] = $plant_type;
        }
        if ($plant_status) {

            /*if($plant_status == 'fault') {

                $where .= $where ? " AND " : '';
                $where .= "plants.alarmLevel != '0'";
                $where_array['plants.alarmLevel'] = 0;
            }*/

            if ($plant_status == 'Y' || $plant_status == 'N') {

                $where_array['plants.is_online'] = $plant_status;
            }
        }
        if ($province) {
            $where_array['plants.province'] = $province;
        }
        if ($city) {
            $where_array['plants.city'] = $city;
        }

        $plants_dashboard = Plant::with(['latest_processed_current_variables', 'latest_fault_alarm_log', 'latest_yearly_processed_plant_detail'])->where($where_array)->whereIn('id', $plant_name)->get();

        $plants_dashboard = $plants_dashboard->map(function ($plant_dashboard) {

            $plant_dashboard['plant_type'] = PlantType::find($plant_dashboard->plant_type)->type;
            $plant_dashboard['system_type'] = SystemType::find($plant_dashboard->system_type)->type;
            $conv_gen = $this->unitConversion((double)$plant_dashboard->yearly_expected_generation, 'kWh');
            $conv_gen_1 = $plant_dashboard->latest_yearly_processed_plant_detail != null ? $this->unitConversion((double)$plant_dashboard->latest_yearly_processed_plant_detail->yearlyGeneration, 'kWh') : [0, 'kWh'];
            $plant_dashboard['yearly_expected_generation'] = round($conv_gen[0], 2) . ' ' . $conv_gen[1];
            $plant_dashboard['yearly_processed_detail'] = round($conv_gen_1[0], 2) . ' ' . $conv_gen_1[1];

            return $plant_dashboard;
        });

        $online = Plant::where($where_array)->whereIn('id', $plant_name)->where('is_online', '!=', 'N')->count();
        $offline = Plant::where($where_array)->whereIn('id', $plant_name)->where('is_online', 'N')->count();
        // $alarmLevel = Plant::where($where_array)->whereIn('id', $plant_name)->where('alarmLevel','!=','0')->count();
        $alarmLevel = 0;

        $plants_donut_graph = collect([[
            "value" => $online,
            "name" => "Online",
        ],
            [
                "value" => $offline,
                "name" => "Offline",
            ],
            [
                "value" => $alarmLevel,
                "name" => "Fault",
            ]]);

        $on_grid = Plant::where($where_array)->whereIn('id', $plant_name)->whereIn('system_type', [1, 2])->count();
        $off_grid = Plant::where($where_array)->whereIn('id', $plant_name)->where('system_type', 3)->count();
        $hybrid = Plant::where($where_array)->whereIn('id', $plant_name)->whereIn('system_type', [4, 5])->count();

        $max_capacity = Plant::max('capacity');
        $capacity_chunk = ceil((double)$max_capacity / 5);
        $total_plant = Plant::where($where_array)->whereIn('id', $plant_name)->count();

        $capacity_1 = Plant::where($where_array)->whereIn('id', $plant_name)->whereBetween('capacity', [0, $capacity_chunk])->count();
        $capacity_2 = Plant::where($where_array)->whereIn('id', $plant_name)->whereBetween('capacity', [$capacity_chunk + 1, $capacity_chunk * 2])->count();
        $capacity_3 = Plant::where($where_array)->whereIn('id', $plant_name)->whereBetween('capacity', [($capacity_chunk * 2) + 1, $capacity_chunk * 3])->count();
        $capacity_4 = Plant::where($where_array)->whereIn('id', $plant_name)->whereBetween('capacity', [($capacity_chunk * 3) + 1, $capacity_chunk * 4])->count();
        $capacity_5 = Plant::where($where_array)->whereIn('id', $plant_name)->whereBetween('capacity', [($capacity_chunk * 4) + 1, $capacity_chunk * 5])->count();

        Session::put(['capacity_chunk' => $capacity_chunk, 'capacity_1' => $capacity_1, 'capacity_2' => $capacity_2, 'capacity_3' => $capacity_3, 'capacity_4' => $capacity_4, 'capacity_5' => $capacity_5]);

        $plant_ids = array();

        $plantss = Plant::whereIn('id', $plant_name)->where($where_array)->get();

        foreach ($plantss as $key => $plant) {
            array_push($plant_ids, $plant->id);
        }

        $plant_city = Plant::select('city')->where($where_array)->whereIn('id', $plant_name)->groupBy('city')->get();

        $minus_3_hours = date('Y-m-d H:i:s', strtotime('-3 hours', strtotime(date('Y-m-d H:i:s'))));
        $weather = Weather::whereIn('city', $plant_city)->whereBetween('created_at', [$minus_3_hours, date('Y-m-d H:i:s')])->get();

        $date = date('Y');

        $tickets = DB::table('tickets')
            ->join('ticket_sources', 'tickets.source', 'ticket_sources.id')
            ->join('ticket_priority', 'tickets.priority', 'ticket_priority.id')
            ->join('ticket_status', 'tickets.status', 'ticket_status.id')
            ->join('plants', 'tickets.plant_id', 'plants.id')
            ->select('tickets.id', 'tickets.title', 'tickets.closed_time', 'tickets.created_at',
                'ticket_sources.name as source_name', 'ticket_priority.priority as priority_name',
                'ticket_status.status as status_name', 'plants.plant_name')
            ->whereIn('tickets.plant_id', $plant_name)
            ->where('tickets.status', '!=', 6)
            ->orderBy('tickets.created_at', 'DESC')
            ->get();

        foreach ($tickets as $key => $ticket) {
            $agents_array = [];
            $ticket_agents = TicketAgent::where('ticket_id', '=', $ticket->id)->get();
            //dd($ticket_agents);
            foreach ($ticket_agents as $key => $ticket_agent) {
                $ticket_agents_name = User::where('id', '=', $ticket_agent->employee_id)->first();
                array_push($agents_array, $ticket_agents_name->name);
            }
            $ticket->agents = implode(',', $agents_array);
        }

        $compactData = [

            'plants_donut_graph' => $plants_donut_graph,
            //'plants' => $plants,
            'total_plant' => $total_plant,
            'plants_dashboard' => $plants_dashboard,
            'filter_data' => $filter_data,
            'online' => $online,
            'offline' => $offline,
            'alarmLevel' => $alarmLevel,
            'on_grid' => $on_grid,
            'off_grid' => $off_grid,
            'hybrid' => $hybrid,
            'capacity_1' => $capacity_1,
            'capacity_2' => $capacity_2,
            'capacity_3' => $capacity_3,
            'capacity_4' => $capacity_4,
            'capacity_5' => $capacity_5,
            'weather' => $weather,
            'tickets' => $tickets
        ];

        return view('admin.dashboard', $compactData);
    }

    public function userDashboard(Request $request)
    {

        return redirect()->route('admin.plants');

        abort(404);

        $input = $request->all();

        $plant_nam = $request->plant_name == null || $request->plant_name == "all" ? 'all' : $request->plant_name;
        $company = $request->company == null || $request->company == "all" ? 'all' : $request->company;
        $plant_status = $request->plant_status == "all" ? '' : $request->plant_status;
        $plant_type = $request->plant_type == "all" ? '' : $request->plant_type;
        $system_type = $request->system_type == "all" ? '' : $request->system_type;
        $capacity = $request->capacity == "all" ? '' : $request->capacity;
        $province = $request->province == "all" ? '' : $request->province;
        $city = $request->city == "all" ? '' : $request->city;
        $plants_input = $request->plants == "all" ? '' : $request->plants;

        $plants = Plant::all();

        $company_plant_arr = [];
        $plant_names = [];
        $plant_name = [];

        $ticket_plant_arr = $plant_names = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id');
        $plant_names = $plant_names->toArray();

        $filter_data['company_array'] = UserCompany::join('companies', 'user_companies.company_id', 'companies.id')
            ->select('companies.company_name', 'companies.id', 'user_companies.user_id', 'user_companies.company_id')
            ->where('user_companies.user_id', Auth::user()->id)
            ->get();
        $filter_data['capacity_array'] = Plant::select('capacity')->whereIn('id', $plant_names)->orderBy('capacity', 'ASC')->get()->unique('capacity');
        $filter_data['province_array'] = Plant::select('province')->whereIn('id', $plant_names)->where('province', '!=', NULL)->groupBy('province')->get();
        $filter_data['city_array'] = Plant::select('city')->whereIn('id', $plant_names)->where('city', '!=', NULL)->groupBy('city')->get();

        $system_types = array();
        $plant_types = array();
        foreach ($plants as $plant) {
            $system_types[] = $plant->system_type;
            $plant_types[] = $plant->plant_type;
        }
        $filter_data['system_type'] = SystemType::whereIn('id', $system_types)->get(['id', 'type']);
        $filter_data['plant_type'] = PlantType::whereIn('id', $plant_types)->get(['id', 'type']);

        $filter_data['plants'] = Plant::whereIn('id', $plant_names)->orderBy('plant_name', 'ASC')->get(['id', 'plant_name', 'company_id']);

        if ($plant_nam == 'all') {

            $plant_name = $plant_names;
        } else {

            $plant_name = $plant_nam;
        }

        if ($company == 'all') {

            $company_arr = UserCompany::where('user_id', Auth::user()->id)->pluck('company_id')->toArray();

        } else {

            $company_arr = $company;
        }

        $input['company'] = $company_arr;

        $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
        $plant_name = array_intersect($plant_name, $company_plant_arr);

        $where_array = array();

        if ($system_type) {
            $where_array['plants.system_type'] = $system_type;
        }
        if ($plant_type) {
            $where_array['plants.plant_type'] = $plant_type;
        }
        if ($plant_status) {

            /*if($plant_status == 'fault') {

                $where .= $where ? " AND " : '';
                $where .= "plants.alarmLevel != '0'";
                $where_array['plants.alarmLevel !='] = 0;
            }*/

            if ($plant_status == 'Y' || $plant_status == 'N') {

                $where_array['plants.is_online'] = $plant_status;
            }
        }
        if ($capacity) {
            $where_array['plants.capacity'] = $capacity;
        }
        if ($province) {
            $where_array['plants.province'] = $province;
        }
        if ($city) {
            $where_array['plants.city'] = $city;
        }

        $input['plant_name'] = $plant_final = Plant::whereIn('id', $plant_name)->where($where_array)->pluck('id')->toArray();

        Session::put(['filter' => $input]);

        $current_generation = 0;
        $current_consumption = 0;
        $current_grid = 0;
        $current_grid_type = '';

        $current = array();
        $daily = array();
        $monthly = array();
        $yearly = array();
        $total = array();

        $max_cron_id = ProcessedCurrentVariable::max('processed_cron_job_id');

        $current_generation = ProcessedCurrentVariable::whereIn('plant_id', $plant_final)->where('processed_cron_job_id', $max_cron_id)->sum('current_generation');
        $current_consumption = ProcessedCurrentVariable::whereIn('plant_id', $plant_final)->where('processed_cron_job_id', $max_cron_id)->sum('current_consumption');
        $current_grid_pos = ProcessedCurrentVariable::whereIn('plant_id', $plant_final)->where('processed_cron_job_id', $max_cron_id)->where('grid_type', '+ve')->sum('current_grid');
        $current_grid_neg = ProcessedCurrentVariable::whereIn('plant_id', $plant_final)->where('processed_cron_job_id', $max_cron_id)->where('grid_type', '-ve')->sum('current_grid');
        $current_grid = $current_grid_pos - $current_grid_neg;
        $current_grid_type = $current_grid >= 0 ? '+ve' : '-ve';

        $daily_generation = DailyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereDate('created_at', date('Y-m-d'))->sum('dailyGeneration');
        $daily_consumption = DailyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereDate('created_at', date('Y-m-d'))->sum('dailyConsumption');
        $daily_grid = DailyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereDate('created_at', date('Y-m-d'))->sum('dailyGridPower');
        $daily_bought_energy = DailyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereDate('created_at', date('Y-m-d'))->sum('dailyBoughtEnergy');
        $daily_sell_energy = DailyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereDate('created_at', date('Y-m-d'))->sum('dailySellEnergy');
        $daily_saving = DailyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereDate('created_at', date('Y-m-d'))->sum('dailySaving');

        $monthly_generation = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->where('created_at', 'LIKE', date('Y-m') . '%')->sum('monthlyGeneration');
        $monthly_consumption = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->where('created_at', 'LIKE', date('Y-m') . '%')->sum('monthlyConsumption');
        $monthly_grid = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->where('created_at', 'LIKE', date('Y-m') . '%')->sum('monthlyGridPower');
        $monthly_bought_energy = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->where('created_at', 'LIKE', date('Y-m') . '%')->sum('monthlyBoughtEnergy');
        $monthly_sell_energy = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->where('created_at', 'LIKE', date('Y-m') . '%')->sum('monthlySellEnergy');
        $monthly_saving = MonthlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->where('created_at', 'LIKE', date('Y-m') . '%')->sum('monthlySaving');

        $yearly_generation = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereYear('created_at', date('Y'))->sum('yearlyGeneration');
        $yearly_consumption = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereYear('created_at', date('Y'))->sum('yearlyConsumption');
        $yearly_grid = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereYear('created_at', date('Y'))->sum('yearlyGridPower');
        $yearly_bought_energy = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereYear('created_at', date('Y'))->sum('yearlyBoughtEnergy');
        $yearly_sell_energy = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereYear('created_at', date('Y'))->sum('yearlySellEnergy');
        $yearly_saving = YearlyProcessedPlantDetail::whereIn('plant_id', $plant_final)->whereYear('created_at', date('Y'))->sum('yearlySaving');

        $total_generation = TotalProcessedPlantDetail::whereIn('plant_id', $plant_final)->sum('plant_total_generation');
        $total_consumption = TotalProcessedPlantDetail::whereIn('plant_id', $plant_final)->sum('plant_total_consumption');
        $total_grid = TotalProcessedPlantDetail::whereIn('plant_id', $plant_final)->sum('plant_total_grid');
        $total_bought_energy = TotalProcessedPlantDetail::whereIn('plant_id', $plant_final)->sum('plant_total_buy_energy');
        $total_sell_energy = TotalProcessedPlantDetail::whereIn('plant_id', $plant_final)->sum('plant_total_sell_energy');
        $total_saving = TotalProcessedPlantDetail::whereIn('plant_id', $plant_final)->sum('plant_total_saving');


        $curr_gen_arr = $this->unitConversion($current_generation, 'kW');
        $curr_con_arr = $this->unitConversion($current_consumption, 'kW');
        $curr_grid_arr = $this->unitConversion($current_grid, 'kW');
        $current['generation'] = round($curr_gen_arr[0], 2) . ' ' . $curr_gen_arr[1];
        $current['consumption'] = round($curr_con_arr[0], 2) . ' ' . $curr_con_arr[1];
        $current['grid'] = round($curr_grid_arr[0], 2) . ' ' . $curr_grid_arr[1];
        $current['grid_type'] = $current_grid_type;

        $daily_gen_arr = $this->unitConversion($daily_generation, 'kWh');
        $daily_con_arr = $this->unitConversion($daily_consumption, 'kWh');
        $daily_grid_arr = $this->unitConversion($daily_grid, 'kWh');
        $daily_buy_arr = $this->unitConversion($daily_bought_energy, 'kWh');
        $daily_sell_arr = $this->unitConversion($daily_sell_energy, 'kWh');
        $daily_revenue_arr = $this->unitConversion($daily_saving, 'PKR');
        $daily_net_grid_arr = $this->unitConversion(((double)$daily_bought_energy - (double)$daily_sell_energy), 'kWh');
        $daily['generation'] = round($daily_gen_arr[0], 2) . ' ' . $daily_gen_arr[1];
        $daily['consumption'] = round($daily_con_arr[0], 2) . ' ' . $daily_con_arr[1];
        $daily['grid'] = round($daily_grid_arr[0], 2) . ' ' . $daily_grid_arr[1];
        $daily['boughtEnergy'] = round($daily_buy_arr[0], 2) . ' ' . $daily_buy_arr[1];
        $daily['sellEnergy'] = round($daily_sell_arr[0], 2) . ' ' . $daily_sell_arr[1];
        $daily['netGrid'] = round($daily_net_grid_arr[0], 2) . ' ' . $daily_net_grid_arr[1];
        $daily['netGridSign'] = ((double)$daily_bought_energy - (double)$daily_sell_energy) < 0 ? '-' : '';
        $daily['revenue'] = round($daily_revenue_arr[0], 2) . '' . $daily_revenue_arr[1];

        $monthly_gen_arr = $this->unitConversion($monthly_generation, 'kWh');
        $monthly_con_arr = $this->unitConversion($monthly_consumption, 'kWh');
        $monthly_grid_arr = $this->unitConversion($monthly_grid, 'kWh');
        $monthly_buy_arr = $this->unitConversion($monthly_bought_energy, 'kWh');
        $monthly_sell_arr = $this->unitConversion($monthly_sell_energy, 'kWh');
        $monthly_revenue_arr = $this->unitConversion($monthly_saving, 'PKR');
        $monthly_net_grid_arr = $this->unitConversion(((double)$monthly_bought_energy - (double)$monthly_sell_energy), 'kWh');
        $monthly['generation'] = round($monthly_gen_arr[0], 2) . ' ' . $monthly_gen_arr[1];
        $monthly['consumption'] = round($monthly_con_arr[0], 2) . ' ' . $monthly_con_arr[1];
        $monthly['grid'] = round($monthly_grid_arr[0], 2) . ' ' . $monthly_grid_arr[1];
        $monthly['boughtEnergy'] = round($monthly_buy_arr[0], 2) . ' ' . $monthly_buy_arr[1];
        $monthly['sellEnergy'] = round($monthly_sell_arr[0], 2) . ' ' . $monthly_sell_arr[1];
        $monthly['netGrid'] = round($monthly_net_grid_arr[0], 2) . ' ' . $monthly_net_grid_arr[1];
        $monthly['netGridSign'] = ((double)$monthly_bought_energy - (double)$monthly_sell_energy) < 0 ? '-' : '';
        $monthly['revenue'] = round($monthly_revenue_arr[0], 2) . '' . $monthly_revenue_arr[1];

        $yearly_gen_arr = $this->unitConversion($yearly_generation, 'kWh');
        $yearly_con_arr = $this->unitConversion($yearly_consumption, 'kWh');
        $yearly_grid_arr = $this->unitConversion($yearly_grid, 'kWh');
        $yearly_buy_arr = $this->unitConversion($yearly_bought_energy, 'kWh');
        $yearly_sell_arr = $this->unitConversion($yearly_sell_energy, 'kWh');
        $yearly_revenue_arr = $this->unitConversion($yearly_saving, 'PKR');
        $yearly_net_grid_arr = $this->unitConversion(((double)$yearly_bought_energy - (double)$yearly_sell_energy), 'kWh');
        $yearly['generation'] = round($yearly_gen_arr[0], 2) . ' ' . $yearly_gen_arr[1];
        $yearly['consumption'] = round($yearly_con_arr[0], 2) . ' ' . $yearly_con_arr[1];
        $yearly['grid'] = round($yearly_grid_arr[0], 2) . ' ' . $yearly_grid_arr[1];
        $yearly['boughtEnergy'] = round($yearly_buy_arr[0], 2) . ' ' . $yearly_buy_arr[1];
        $yearly['sellEnergy'] = round($yearly_sell_arr[0], 2) . ' ' . $yearly_sell_arr[1];
        $yearly['netGrid'] = round($yearly_net_grid_arr[0], 2) . ' ' . $yearly_net_grid_arr[1];
        $yearly['netGridSign'] = ((double)$yearly_bought_energy - (double)$yearly_sell_energy) < 0 ? '-' : '';
        $yearly['revenue'] = round($yearly_revenue_arr[0], 2) . '' . $yearly_revenue_arr[1];

        $total_gen_arr = $this->unitConversion($total_generation, 'kWh');
        $total_con_arr = $this->unitConversion($total_consumption, 'kWh');
        $total_grid_arr = $this->unitConversion($total_grid, 'kWh');
        $total_buy_arr = $this->unitConversion($total_bought_energy, 'kWh');
        $total_sell_arr = $this->unitConversion($total_sell_energy, 'kWh');
        $total_revenue_arr = $this->unitConversion($total_saving, 'PKR');
        $total_net_grid_arr = $this->unitConversion(((double)$total_bought_energy - (double)$total_sell_energy), 'kWh');
        $total['generation'] = round($total_gen_arr[0], 2) . ' ' . $total_gen_arr[1];
        $total['consumption'] = round($total_con_arr[0], 2) . ' ' . $total_con_arr[1];
        $total['grid'] = round($total_grid_arr[0], 2) . ' ' . $total_grid_arr[1];
        $total['boughtEnergy'] = round($total_buy_arr[0], 2) . ' ' . $total_buy_arr[1];
        $total['sellEnergy'] = round($total_sell_arr[0], 2) . ' ' . $total_sell_arr[1];
        $total['netGrid'] = round($total_net_grid_arr[0], 2) . ' ' . $total_net_grid_arr[1];
        $total['netGridSign'] = ((double)$total_bought_energy - (double)$total_sell_energy) < 0 ? '-' : '';
        $total['revenue'] = round($total_revenue_arr[0], 2) . '' . $total_revenue_arr[1];

        $plant_city = Plant::select('city')->whereIn('id', $plant_final)->groupBy('city')->get();

        $minus_3_hours = date('Y-m-d H:i:s', strtotime('-3 hours', strtotime(date('Y-m-d H:i:s'))));
        $weather = Weather::whereIn('city', $plant_city)->whereBetween('created_at', [$minus_3_hours, date('Y-m-d H:i:s')])->get();

        $tickets = DB::table('tickets')
            ->join('ticket_sources', 'tickets.source', 'ticket_sources.id')
            ->join('ticket_priority', 'tickets.priority', 'ticket_priority.id')
            ->join('ticket_status', 'tickets.status', 'ticket_status.id')
            ->join('plants', 'tickets.plant_id', 'plants.id')
            ->select('tickets.id', 'tickets.title', 'tickets.closed_time', 'tickets.created_at',
                'ticket_sources.name as source_name', 'ticket_priority.priority as priority_name',
                'ticket_status.status as status_name', 'plants.plant_name')
            ->whereIn('tickets.plant_id', $plant_final)
            ->where('tickets.status', '!=', 6)
            ->orderBy('tickets.created_at', 'DESC')
            ->get();

        foreach ($tickets as $key => $ticket) {
            $agents_array = [];
            $ticket_agents = TicketAgent::where('ticket_id', '=', $ticket->id)->get();
            //dd($ticket_agents);
            foreach ($ticket_agents as $key => $ticket_agent) {
                $ticket_agents_name = User::where('id', '=', $ticket_agent->employee_id)->first();
                array_push($agents_array, $ticket_agents_name->name);
            }
            $ticket->agents = implode(',', $agents_array);
        }

        $tickets_feedback = DB::table('tickets')
            ->join('ticket_sources', 'tickets.source', 'ticket_sources.id')
            ->join('ticket_priority', 'tickets.priority', 'ticket_priority.id')
            ->join('ticket_status', 'tickets.status', 'ticket_status.id')
            ->select('tickets.id', 'tickets.title', 'tickets.closed_time', 'tickets.created_at',
                'ticket_sources.name as source_name', 'ticket_priority.priority as priority_name',
                'ticket_status.status as status_name')
            ->whereIn('tickets.plant_id', $ticket_plant_arr)
            ->where('tickets.status', 6)
            ->where('tickets.user_approved', 'N')
            ->get();


        return view('admin.userDashboard', ['filter_data' => $filter_data, 'current' => $current, 'daily' => $daily, 'monthly' => $monthly, 'yearly' => $yearly, 'weather' => $weather, 'tickets' => $tickets, 'tickets_feedback' => $tickets_feedback, 'total' => $total]);
    }

    public function Plants(Request $request)
    {
//        try {
        $input = $request->all();

        //dd($request->plant_name);
        $plant_nam = !isset($request->plant_name) || $request->plant_name == null || $request->plant_name == "all" ? 'all' : $request->plant_name;
        $company = !isset($request->company) || $request->company == null || $request->company == "all" ? 'all' : $request->company;
        $plant_status = $request->plant_status == "all" ? '' : $request->plant_status;
        $plant_type = $request->plant_type == "all" ? '' : $request->plant_type;
        $system_type = $request->system_type == "all" ? '' : $request->system_type;
        $province = $request->province == "all" ? '' : $request->province;
        $city = $request->city == "all" ? '' : $request->city;
        $plants_input = $request->plants == "all" ? '' : $request->plants;

        $plant_names = [];
        $plant_name = [];
        $company_arr = array();

        if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
            //dd('Auth::user()->roles == 1');
            $plant_names = Plant::pluck('id');
            $plant_names = $plant_names->toArray();

            $plants = Plant::all();
            $plant_type_id = Plant::groupBy('plant_type')->pluck('plant_type');
            $system_type_id = Plant::groupBy('system_type')->pluck('system_type');

            $filter_data['company_array'] = Company::all();
            $filter_data['province_array'] = Plant::select('province')->where('province', '!=', NULL)->groupBy('province')->get();
            $filter_data['city_array'] = Plant::select('city')->where('city', '!=', NULL)->groupBy('city')->get();

            $system_types = array();
            $plant_types = array();
            foreach ($plants as $plant) {
                $system_types[] = $plant->system_type;
                $plant_types[] = $plant->plant_type;
            }
            $filter_data['system_type'] = SystemType::whereIn('id', $system_types)->get();
            $filter_data['plant_type'] = PlantType::whereIn('id', $plant_types)->get();

            $filter_data['plants'] = Plant::get(['id', 'plant_name', 'company_id']);
        } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
            //dd('Auth::user()->roles == 3');
            $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
            $plant_names = $plant_names->toArray();

            if (empty($plant_names) && Auth::user()->roles == 3) {
                return redirect()->route('admin.build.plant');
            } else if (empty($plant_names) && Auth::user()->roles == 4) {
                return redirect()->route('admin.plants');
            }

            $plants = Plant::whereIn('id', $plant_names)->get();
            $plant_type_id = Plant::groupBy('plant_type')->whereIn('id', $plant_names)->pluck('plant_type');
            $system_type_id = Plant::groupBy('system_type')->whereIn('id', $plant_names)->pluck('system_type');

            $filter_data['province_array'] = Plant::select('province')->where('province', '!=', NULL)->whereIn('id', $plant_names)->groupBy('province')->get();
            $filter_data['city_array'] = Plant::select('city')->where('city', '!=', NULL)->whereIn('id', $plant_names)->groupBy('city')->get();

            $system_types = array();
            $plant_types = array();
            foreach ($plants as $plant) {
                $system_types[] = $plant->system_type;
                $plant_types[] = $plant->plant_type;
            }
            $filter_data['system_type'] = SystemType::whereIn('id', $system_types)->get();
            $filter_data['plant_type'] = PlantType::whereIn('id', $plant_types)->get();
            $filter_data['plants'] = Plant::whereIn('id', $plant_names)->get();
        }

        if ($plant_nam == 'all') {

            $plant_name = $plant_names;

            //$input['plant_name'] = $plant_name;
        } else {

            $plant_name = $plant_nam;

            $input['plant_name'] = $plant_name;
        }

        if ($company == 'all') {

            $company_arr = UserCompany::where('user_id', Auth::user()->id)->pluck('company_id')->toArray();
            $company_arr = (array)$company_arr;
            $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
            $plant_name = array_intersect($plant_name, $company_plant_arr);

            if (isset($request->company)) {

                $input['company'] = $company_arr;
                //$input['plant_name'] = $plant_name;
            }
        } else {

            $company_arr = $company;
            $company_arr = (array)$company_arr;
            $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
            $plant_name = array_intersect($plant_name, $company_plant_arr);

            if (isset($request->company)) {

                $input['company'] = $company_arr;
                $input['plant_name'] = $plant_name;
            }
        }

        Session::put(['filter' => $input]);

        $plant_name = (array)$plant_name;
        //$company_arr = (array)$company_arr;
        //return [$plant_name, $company_arr];

        $where_array = array();
        if ($system_type) {
            $where_array['plants.system_type'] = $system_type;
        }
        if ($plant_type) {
            $where_array['plants.plant_type'] = $plant_type;
        }
        if ($plant_status) {

            if ($plant_status == 'fault') {

                $where_array['plants.alarmLevel'] = 1;
            }

            if ($plant_status == 'Y' || $plant_status == 'N') {

                $where_array['plants.is_online'] = $plant_status;
            }
        }
        if ($province) {
            $where_array['plants.province'] = $province;
        }
        if ($city) {
            $where_array['plants.city'] = $city;
        }

        $plants_dashboard = Plant::with(['latest_processed_current_variables', 'latest_fault_alarm_log', 'latest_yearly_processed_plant_detail', 'latest_daily_processed_plant_detail'])->where($where_array)->whereIn('id', $plant_name)->get();
//        return $plant_name;

        $plants_dashboard = $plants_dashboard->map(function ($plant_dashboard) {

            $plant_dashboard['plant_type'] = PlantType::find($plant_dashboard->plant_type)->type;
            $plant_dashboard['system_type'] = SystemType::find($plant_dashboard->system_type)->type;
            $conv_gen = $this->unitConversion((double)$plant_dashboard->yearly_expected_generation, 'kWh');
            $conv_gen_1 = $plant_dashboard->latest_yearly_processed_plant_detail != null ? $this->unitConversion((double)$plant_dashboard->latest_yearly_processed_plant_detail->yearlyGeneration, 'kWh') : [0, 'kWh'];
            $plant_dashboard['yearly_expected_generation'] = round($conv_gen[0], 2) . ' ' . $conv_gen[1];
            $plant_dashboard['yearly_processed_detail'] = round($conv_gen_1[0], 2) . ' ' . $conv_gen_1[1];

            return $plant_dashboard;
        });

        $online = Plant::where($where_array)->whereIn('id', $plant_name)->where('is_online', '!=', 'N')->count();
        $offline = Plant::where($where_array)->whereIn('id', $plant_name)->where('is_online', 'N')->count();
        $alarmLevel = Plant::where($where_array)->whereIn('id', $plant_name)->where('alarmLevel', '!=', 0)->count();

        $plants_donut_graph = collect([[
            "value" => $online,
            "name" => "Online",
        ],
            [
                "value" => $offline,
                "name" => "Offline",
            ],
            [
                "value" => $alarmLevel,
                "name" => "Fault",
            ]]);

        $on_grid = Plant::where($where_array)->whereIn('id', $plant_name)->whereIn('system_type', [1, 2])->count();
        $off_grid = Plant::where($where_array)->whereIn('id', $plant_name)->where('system_type', 3)->count();
        $hybrid = Plant::where($where_array)->whereIn('id', $plant_name)->whereIn('system_type', [4, 5])->count();

        $max_capacity = Plant::max('capacity');
        $capacity_chunk = ceil((double)$max_capacity / 5);
        $total_plant = Plant::where($where_array)->whereIn('id', $plant_name)->count();

        $capacity_1 = Plant::where($where_array)->whereIn('id', $plant_name)->whereBetween('capacity', [0, $capacity_chunk])->count();
        $capacity_2 = Plant::where($where_array)->whereIn('id', $plant_name)->whereBetween('capacity', [$capacity_chunk + 1, $capacity_chunk * 2])->count();
        $capacity_3 = Plant::where($where_array)->whereIn('id', $plant_name)->whereBetween('capacity', [($capacity_chunk * 2) + 1, $capacity_chunk * 3])->count();
        $capacity_4 = Plant::where($where_array)->whereIn('id', $plant_name)->whereBetween('capacity', [($capacity_chunk * 3) + 1, $capacity_chunk * 4])->count();
        $capacity_5 = Plant::where($where_array)->whereIn('id', $plant_name)->whereBetween('capacity', [($capacity_chunk * 4) + 1, $capacity_chunk * 5])->count();

        Session::put(['capacity_chunk' => $capacity_chunk, 'capacity_1' => $capacity_1, 'capacity_2' => $capacity_2, 'capacity_3' => $capacity_3, 'capacity_4' => $capacity_4, 'capacity_5' => $capacity_5]);

        $plant_ids = array();

        $plantss = Plant::whereIn('id', $plant_name)->where($where_array)->get();

        foreach ($plantss as $key => $plant) {
            array_push($plant_ids, $plant->id);
        }

        $plant_city = Plant::select('city')->where($where_array)->whereIn('id', $plant_name)->groupBy('city')->get();
//        return $plant_city;

        $minus_3_hours = date('Y-m-d H:i:s', strtotime('-3 hours', strtotime(date('Y-m-d H:i:s'))));
//            return $minus_3_hours;
        $weather = Weather::whereIn('city', $plant_city)->whereBetween('created_at', [$minus_3_hours, date('Y-m-d H:i:s')])->get();
//            return $plant_city;

        $date = date('Y');

        $tickets = DB::table('tickets')
            ->join('ticket_sources', 'tickets.source', 'ticket_sources.id')
            ->join('ticket_priority', 'tickets.priority', 'ticket_priority.id')
            ->join('ticket_status', 'tickets.status', 'ticket_status.id')
            ->join('plants', 'tickets.plant_id', 'plants.id')
            ->select('tickets.id', 'tickets.title', 'tickets.closed_time', 'tickets.created_at',
                'ticket_sources.name as source_name', 'ticket_priority.priority as priority_name',
                'ticket_status.status as status_name', 'plants.plant_name')
            ->whereIn('tickets.plant_id', $plant_name)
            ->where('tickets.status', '!=', 6)
            ->orderBy('tickets.created_at', 'DESC')
            ->get();

        foreach ($tickets as $key => $ticket) {
            $agents_array = [];
            $ticket_agents = TicketAgent::where('ticket_id', '=', $ticket->id)->get();
            //dd($ticket_agents);
            foreach ($ticket_agents as $key => $ticket_agent) {
                $ticket_agents_name = User::where('id', '=', $ticket_agent->employee_id)->first();
                array_push($agents_array, $ticket_agents_name->name);
            }
            $ticket->agents = implode(',', $agents_array);
        }

        $data = [

            'plants_donut_graph' => $plants_donut_graph,
            //'plants' => $plants,
            'total_plant' => $total_plant,
            'plants_dashboard' => $plants_dashboard,
            'filter_data' => $filter_data,
            'online' => $online,
            'offline' => $offline,
            'alarmLevel' => $alarmLevel,
            'on_grid' => $on_grid,
            'off_grid' => $off_grid,
            'hybrid' => $hybrid,
            'capacity_1' => $capacity_1,
            'capacity_2' => $capacity_2,
            'capacity_3' => $capacity_3,
            'capacity_4' => $capacity_4,
            'capacity_5' => $capacity_5,
            'weather' => $weather,
            'tickets' => $tickets
        ];

        return view('admin.plant.plants', $data);
    }

    public function plantStatusGraphs(Request $request)
    {
        try {
            $requestFilterData = $request->all();

            $plant_nam = !isset($request->plant[0]) || $request->plant[0] == null || $request->plant == "all" ? 'all' : $request->plant;
            $company = !isset($request->company[0]) || $request->company[0] == null || $request->company == "all" ? 'all' : $request->company;
            $plant_status = $request->plant_status == "all" ? '' : $request->plant_status;
            $plant_type = $request->plant_type == "all" ? '' : $request->plant_type;
            $system_type = $request->system_type == "all" ? '' : $request->system_type;
            $province = $request->province == "all" ? '' : $request->province;
            $city = $request->city == "all" ? '' : $request->city;
            $plants_input = $request->plants == "all" ? '' : $request->plants;

            $plants = array();
            $filterData = array();
            $plant_names = [];
            $plant_name = [];
            $company_arr = array();

            $plantArray = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id')->toArray();
            $filterData['plant_array'] = Plant::whereIn('id', $plantArray)->get(['id', 'plant_name', 'company_id', 'plant_type', 'system_type']);
            $filterData['company_array'] = Company::join('user_companies', 'companies.id', 'user_companies.company_id')
                ->select('companies.id', 'companies.company_name')
                ->where('user_companies.user_id', Auth::user()->id)
                ->get();

            $filterData['province_array'] = Plant::select('province')->whereIn('id', $plantArray)->where('province', '!=', NULL)->groupBy('province')->get();
            $filterData['city_array'] = Plant::select('city')->whereIn('id', $plantArray)->where('city', '!=', NULL)->groupBy('city')->get();

            $systemTypes = array();
            $plantTypes = array();
            foreach ($filterData['plant_array'] as $plant) {
                $systemTypes[] = $plant->system_type;
                $plantTypes[] = $plant->plant_type;
            }

            $filterData['system_type_array'] = SystemType::whereIn('id', $systemTypes)->get();
            $filterData['plant_type_array'] = PlantType::whereIn('id', $plantTypes)->get();
            $filterData['status_array'] = ['online', 'offline', 'fault'];

            if (count($plantArray) > 0 && count($filterData['plant_array']) == 1) {
                return redirect()->route('admin.plant.details', ['id' => $filterData['plant_array'][0]->id]);
            }

            if ($plant_nam == 'all') {

                $plant_name = $plantArray;
            } else {

                $plant_name = $plant_nam;

                $requestFilterData['plant'] = $plant_name;
            }

            if ($company == 'all') {

                $company_arr = UserCompany::where('user_id', Auth::user()->id)->pluck('company_id')->toArray();
                $company_arr = (array)$company_arr;
                $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
                $plant_name = array_intersect($plant_name, $company_plant_arr);

                if (isset($request->company)) {

                    $requestFilterData['company'] = $company_arr;
                }
            } else {

                $company_arr = $company;
                $company_arr = (array)$company_arr;
                $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
                $plant_name = array_intersect($plant_name, $company_plant_arr);

                if (isset($request->company)) {

                    $requestFilterData['company'] = $company_arr;
                    $requestFilterData['plant'] = $plant_name;
                }
            }

            $selectedFilterData = $requestFilterData;

            $plant_name = (array)$plant_name;

            $where_array = array();
            if ($system_type) {
                $where_array['plants.system_type'] = $system_type;
            }
            if ($plant_type) {
                $where_array['plants.plant_type'] = $plant_type;
            }
            if ($plant_status) {

                if ($plant_status == 'fault') {

                    $where_array['plants.alarmLevel'] = 1;
                } else if ($plant_status == 'online') {

                    $where_array['plants.is_online'] = 'Y';
                } else if ($plant_status == 'offline') {

                    $where_array['plants.is_online'] = 'N';
                }
            }
            if ($province) {
                $where_array['plants.province'] = $province;
            }
            if ($city) {
                $where_array['plants.city'] = $city;
            }

            $finalPlantArray = Plant::where($where_array)->whereIn('id', $plant_name)->pluck('id')->toArray();
            $statusArray = array();

            $statusArray['offline'] = Plant::whereIn('id', $finalPlantArray)->where('is_online', 'N')->count();
            $statusArray['online'] = Plant::whereIn('id', $finalPlantArray)->where('is_online', '!=', 'N')->count();
            $statusArray['alarm'] = Plant::whereIn('id', $finalPlantArray)->where('is_online', '!=', 'N')->where('alarmLevel', 1)->where('faultLevel', 0)->count();
            $statusArray['fault'] = Plant::whereIn('id', $finalPlantArray)->where('is_online', '!=', 'N')->where('faultLevel', 1)->count();
            $statusArray['total'] = Plant::whereIn('id', $finalPlantArray)->count();
            $plantStatusGraphData = $this->plantStatusGraph($statusArray);
        } catch (\Exception $ex) {

            return $ex->getMessage();
            // return redirect()->back()->with('exception', $ex->getMessage());
        }
//        $data = [
//            'plantStatusGraphData' => $plantStatusGraphData,
//        ];
        return $plantStatusGraphData;
    }

    public function plantPowerGraphs(Request $request)
    {
        try {
            $requestFilterData = $request->all();
            $plant_nam = !isset($request->plant[0]) || empty($request->plant[0]) || $request->plant[0] == null || $request->plant == "all" ? 'all' : $request->plant;
            $company = !isset($request->company[0]) || $request->company[0] == null || $request->company == "all" ? 'all' : $request->company;
            $plant_status = $request->plant_status == "all" ? '' : $request->plant_status;
            $plant_type = $request->plant_type == "all" ? '' : $request->plant_type;
            $system_type = $request->system_type == "all" ? '' : $request->system_type;
            $province = $request->province == "all" ? '' : $request->province;
            $city = $request->city == "all" ? '' : $request->city;
            $plants_input = $request->plants == "all" ? '' : $request->plants;

            $plants = array();
            $filterData = array();
            $plant_names = [];
            $plant_name = [];
            $company_arr = array();

            $plantArray = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id')->toArray();
            $filterData['plant_array'] = Plant::whereIn('id', $plantArray)->get(['id', 'plant_name', 'company_id', 'plant_type', 'system_type']);
            $filterData['company_array'] = Company::join('user_companies', 'companies.id', 'user_companies.company_id')
                ->select('companies.id', 'companies.company_name')
                ->where('user_companies.user_id', Auth::user()->id)
                ->get();

            $filterData['province_array'] = Plant::select('province')->whereIn('id', $plantArray)->where('province', '!=', NULL)->groupBy('province')->get();
            $filterData['city_array'] = Plant::select('city')->whereIn('id', $plantArray)->where('city', '!=', NULL)->groupBy('city')->get();

            $systemTypes = array();
            $plantTypes = array();
            foreach ($filterData['plant_array'] as $plant) {
                $systemTypes[] = $plant->system_type;
                $plantTypes[] = $plant->plant_type;
            }

            $filterData['system_type_array'] = SystemType::whereIn('id', $systemTypes)->get();
            $filterData['plant_type_array'] = PlantType::whereIn('id', $plantTypes)->get();
            $filterData['status_array'] = ['online', 'offline', 'fault'];

            if (count($plantArray) > 0 && count($filterData['plant_array']) == 1) {
                return redirect()->route('admin.plant.details', ['id' => $filterData['plant_array'][0]->id]);
            }

            if ($plant_nam == 'all') {

                $plant_name = $plantArray;
            } else {

                $plant_name = $plant_nam;

                $requestFilterData['plant'] = $plant_name;
            }

            if ($company == 'all') {

                $company_arr = UserCompany::where('user_id', Auth::user()->id)->pluck('company_id')->toArray();
                $company_arr = (array)$company_arr;
                $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
                $plant_name = array_intersect($plant_name, $company_plant_arr);

                if (isset($request->company)) {

                    $requestFilterData['company'] = $company_arr;
                }
            } else {

                $company_arr = $company;
                $company_arr = (array)$company_arr;
                $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
                $plant_name = array_intersect($plant_name, $company_plant_arr);

                if (isset($request->company)) {

                    $requestFilterData['company'] = $company_arr;
                    $requestFilterData['plant'] = $plant_name;
                }
            }

            $selectedFilterData = $requestFilterData;

            $plant_name = (array)$plant_name;

            $where_array = array();
            if ($system_type) {
                $where_array['plants.system_type'] = $system_type;
            }
            if ($plant_type) {
                $where_array['plants.plant_type'] = $plant_type;
            }
            if ($plant_status) {

                if ($plant_status == 'fault') {

                    $where_array['plants.alarmLevel'] = 1;
                } else if ($plant_status == 'online') {

                    $where_array['plants.is_online'] = 'Y';
                } else if ($plant_status == 'offline') {

                    $where_array['plants.is_online'] = 'N';
                }
            }
            if ($province) {
                $where_array['plants.province'] = $province;
            }
            if ($city) {
                $where_array['plants.city'] = $city;
            }

            $finalPlantArray = Plant::where($where_array)->whereIn('id', $plant_name)->pluck('id')->toArray();
            $powerValue = 0;
            $capacityValue = 0;
            $generationArray = array();

            foreach ((array)$finalPlantArray as $key => $plantId) {

                $plantCurrentDataLogTime = ProcessedCurrentVariable::where('plant_id', $plantId)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $plantId)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');

                $plantFinalCurrentDataDateTime = $this->previousTenMinutesDateTime($plantCurrentDataLogTime);

                $powerValue += ProcessedCurrentVariable::where('plant_id', $plantId)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $plantFinalCurrentDataDateTime)->exists() ? ProcessedCurrentVariable::where('plant_id', $plantId)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $plantFinalCurrentDataDateTime)->orderBy('collect_time', 'DESC')->first()->current_generation : 0;
            }
            $todayValue = 0;
            $yesterdayValue = 0;
            $dailyGenerationValuePlant = 0;
            $dailySavingValuePlant = 0;

            foreach ($finalPlantArray as $arrData) {

                $todayValue += DailyProcessedPlantDetail::where('plant_id', $arrData)->whereDate('created_at', date('Y-m-d'))->exists() ? DailyProcessedPlantDetail::where('plant_id', $arrData)->whereDate('created_at', date('Y-m-d'))->orderBy('updated_at', 'DESC')->first()->dailyGeneration : 0;
                $yesterdayValue += DailyProcessedPlantDetail::where('plant_id', $arrData)->whereDate('created_at', date('Y-m-d', strtotime("-1 days")))->exists() ? DailyProcessedPlantDetail::where('plant_id', $arrData)->whereDate('created_at', date('Y-m-d', strtotime("-1 days")))->orderBy('updated_at', 'DESC')->first()->dailyGeneration : 0;
                $dailyGenerationValuePlant += DailyProcessedPlantDetail::where('plant_id', $arrData)->whereDate('created_at', date('Y-m-d'))->exists() ? DailyProcessedPlantDetail::where('plant_id', $arrData)->whereDate('created_at', date('Y-m-d'))->orderBy('updated_at', 'DESC')->first()->dailyGeneration : 0;
                $dailySavingValuePlant += DailyProcessedPlantDetail::where('plant_id', $arrData)->whereDate('created_at', date('Y-m-d'))->exists() ? DailyProcessedPlantDetail::where('plant_id', $arrData)->whereDate('created_at', date('Y-m-d'))->orderBy('updated_at', 'DESC')->first()->dailySaving : 0;
            }
            $currentPower = $this->unitConversion($powerValue, 'kW');
            $generationDaily = $this->unitConversion($dailyGenerationValuePlant, 'kWh');
            $generationMonthly = $this->unitConversion(MonthlyProcessedPlantDetail::whereIn('plant_id', $finalPlantArray)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('monthlyGeneration'), 'kWh');
            $generationYearly = $this->unitConversion(YearlyProcessedPlantDetail::whereIn('plant_id', $finalPlantArray)->whereYear('created_at', date('Y'))->sum('yearlyGeneration'), 'kWh');
            $generationTotal = $this->unitConversion(TotalProcessedPlantDetail::whereIn('plant_id', $finalPlantArray)->sum('plant_total_generation'), 'kWh');
            $capacityValue = Plant::whereIn('id', (array)$finalPlantArray)->sum('capacity');
            $totalCapacity = $this->unitConversion($capacityValue, 'kWp');
            $generationArray['daily'] = round($generationDaily[0], 2) . ' ' . $generationDaily[1];
            $generationArray['monthly'] = round($generationMonthly[0], 2) . ' ' . $generationMonthly[1];
            $generationArray['yearly'] = round($generationYearly[0], 2) . ' ' . $generationYearly[1];
            $generationArray['total'] = round($generationTotal[0], 2) . ' ' . $generationTotal[1];
            $generationArray['current_power'] = round($currentPower[0], 2) . ' ' . $currentPower[1];
            $generationArray['total_capacity'] = round($totalCapacity[0], 2) . ' ' . $totalCapacity[1];
            $plantPowerGraphData = $this->plantPowerGraph($powerValue, $capacityValue);
            $data = [
                'plantPowerData' => $plantPowerGraphData,
                'generationArray' => $generationArray
            ];
        } catch (\Exception $ex) {

            return $ex->getMessage();
            // return redirect()->back()->with('exception', $ex->getMessage());
        }
        return $data;
    }

    public function plantSavingGraphs(Request $request)
    {
        try {
            $requestFilterData = $request->all();

            $plant_nam = !isset($request->plant[0]) || $request->plant[0] == null || $request->plant == "all" ? 'all' : $request->plant;
            $company = !isset($request->company[0]) || $request->company[0] == null || $request->company == "all" ? 'all' : $request->company;
            $plant_status = $request->plant_status == "all" ? '' : $request->plant_status;
            $plant_type = $request->plant_type == "all" ? '' : $request->plant_type;
            $system_type = $request->system_type == "all" ? '' : $request->system_type;
            $province = $request->province == "all" ? '' : $request->province;
            $city = $request->city == "all" ? '' : $request->city;
            $plants_input = $request->plants == "all" ? '' : $request->plants;
            $plants = array();
            $filterData = array();
            $plant_names = [];
            $plant_name = [];
            $company_arr = array();

            $plantArray = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id')->toArray();
            $filterData['plant_array'] = Plant::whereIn('id', $plantArray)->get(['id', 'plant_name', 'company_id', 'plant_type', 'system_type']);
            $filterData['company_array'] = Company::join('user_companies', 'companies.id', 'user_companies.company_id')
                ->select('companies.id', 'companies.company_name')
                ->where('user_companies.user_id', Auth::user()->id)
                ->get();

            $filterData['province_array'] = Plant::select('province')->whereIn('id', $plantArray)->where('province', '!=', NULL)->groupBy('province')->get();
            $filterData['city_array'] = Plant::select('city')->whereIn('id', $plantArray)->where('city', '!=', NULL)->groupBy('city')->get();

            $systemTypes = array();
            $plantTypes = array();
            foreach ($filterData['plant_array'] as $plant) {
                $systemTypes[] = $plant->system_type;
                $plantTypes[] = $plant->plant_type;
            }

            $filterData['system_type_array'] = SystemType::whereIn('id', $systemTypes)->get();
            $filterData['plant_type_array'] = PlantType::whereIn('id', $plantTypes)->get();
            $filterData['status_array'] = ['online', 'offline', 'fault'];

            if (count($plantArray) > 0 && count($filterData['plant_array']) == 1) {
                return redirect()->route('admin.plant.details', ['id' => $filterData['plant_array'][0]->id]);
            }

            if ($plant_nam == 'all') {

                $plant_name = $plantArray;
            } else {

                $plant_name = $plant_nam;

                $requestFilterData['plant'] = $plant_name;
            }

            if ($company == 'all') {

                $company_arr = UserCompany::where('user_id', Auth::user()->id)->pluck('company_id')->toArray();
                $company_arr = (array)$company_arr;
                $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
                $plant_name = array_intersect($plant_name, $company_plant_arr);

                if (isset($request->company)) {

                    $requestFilterData['company'] = $company_arr;
                }
            } else {

                $company_arr = $company;
                $company_arr = (array)$company_arr;
                $company_plant_arr = Plant::whereIn('company_id', $company_arr)->pluck('id')->toArray();
                $plant_name = array_intersect($plant_name, $company_plant_arr);

                if (isset($request->company)) {

                    $requestFilterData['company'] = $company_arr;
                    $requestFilterData['plant'] = $plant_name;
                }
            }

            $selectedFilterData = $requestFilterData;

            $plant_name = (array)$plant_name;

            $where_array = array();
            if ($system_type) {
                $where_array['plants.system_type'] = $system_type;
            }
            if ($plant_type) {
                $where_array['plants.plant_type'] = $plant_type;
            }
            if ($plant_status) {

                if ($plant_status == 'fault') {

                    $where_array['plants.alarmLevel'] = 1;
                } else if ($plant_status == 'online') {

                    $where_array['plants.is_online'] = 'Y';
                } else if ($plant_status == 'offline') {

                    $where_array['plants.is_online'] = 'N';
                }
            }
            if ($province) {
                $where_array['plants.province'] = $province;
            }
            if ($city) {
                $where_array['plants.city'] = $city;
            }
            $savingArray = array();

            $finalPlantArray = Plant::where($where_array)->whereIn('id', $plant_name)->pluck('id')->toArray();
            $dailySavingValuePlant = 0;

            foreach ($finalPlantArray as $arrData) {
                $dailySavingValuePlant += DailyProcessedPlantDetail::where('plant_id', $arrData)->whereDate('created_at', date('Y-m-d'))->exists() ? DailyProcessedPlantDetail::where('plant_id', $arrData)->whereDate('created_at', date('Y-m-d'))->orderBy('updated_at', 'DESC')->first()->dailySaving : 0;
            }
            $currency = Setting::where('perimeter', 'currency')->pluck('value')[0];
            $savingDaily = $this->unitConversion($dailySavingValuePlant, $currency);
            $savingMonthly = $this->unitConversion(MonthlyProcessedPlantDetail::whereIn('plant_id', $finalPlantArray)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('monthlySaving'), $currency);
            $savingYearly = $this->unitConversion(YearlyProcessedPlantDetail::whereIn('plant_id', $finalPlantArray)->whereYear('created_at', date('Y'))->sum('yearlySaving'), $currency);
            $savingTotal = $this->unitConversion(TotalProcessedPlantDetail::whereIn('plant_id', $finalPlantArray)->sum('plant_total_saving'), $currency);

            $savingArray['daily'] = round($savingDaily[0], 2) . ' ' . $savingDaily[1];
            $savingArray['monthly'] = round($savingMonthly[0], 2) . ' ' . $savingMonthly[1];
            $savingArray['yearly'] = round($savingYearly[0], 2) . ' ' . $savingYearly[1];
            $savingArray['total'] = round($savingTotal[0], 2) . ' ' . $savingTotal[1];
            $plantSavingGraphData = $this->plantSavingGraph($finalPlantArray);
            $data = [
                'plantSavingData' => $plantSavingGraphData,
                'savingArray' => $savingArray
            ];
        } catch (\Exception $ex) {

            return $ex->getMessage();
            // return redirect()->back()->with('exception', $ex->getMessage());
        }

        return $data;
    }

    private function plantPowerGraph($powerValue, $capacityValue)
    {

        $data['total_capacity_value'] = $capacityValue;

        $currentPowerArray = ['Total Power', 'Installed Capacity'];

        $plantPowerGraph = [];
        $legendArray = [];
        $value = 0;

        foreach ($currentPowerArray as $key => $current) {

            $legendArray[] = $current;

            if ($current == 'Total Power') {

                ${"file" . $key} = collect([
                    "value" => round($powerValue, 2),
                    "color" => '#F6A944',
                    "name" => $current,
                ]);

                $plantPowerGraph[] = ${"file" . $key};
            } else if ($current == 'Installed Capacity') {

                if ($powerValue > $capacityValue) {

                    $value = $powerValue;
                    $capacityValue = 0;
                } else {

                    $value = $capacityValue;
                    $capacityValue = $value - $powerValue;
                }

                ${"file" . $key} = collect([
                    "value" => round($capacityValue, 2),
                    "color" => '#86BD4D',
                    "name" => $current,
                ]);

                $plantPowerGraph[] = ${"file" . $key};
            }
        }

        $fileNull = collect([
            "value" => round($value, 2),
            "name" => null,
            "itemStyle" => [
                "opacity" => 0
            ],
            "tooltip" => [
                "show" => false
            ]
        ]);

        $plantPowerGraph[] = $fileNull;

        $data['plant_power_graph'] = $plantPowerGraph;
        $data['total_value'] = $value;
        $data['power_value'] = $powerValue;
        $data['capacity_value'] = $capacityValue;
        $data['legend_array'] = $legendArray;

        return $data;
    }

    private function plantConsumptionGraph($actualValue, $totalValue)
    {

        $currentConsumptionArray = ['Current Time', 'Total Time'];

        $plantConsumptionGraph = [];
        $legendArray = [];
        $value = 0;

        foreach ($currentConsumptionArray as $key => $current) {

            $legendArray[] = $current;

            if ($current == 'Current Time') {

                ${"file" . $key} = collect([
                    "value" => round($actualValue, 2),
                    "color" => '#F6A944',
                    "name" => $current,
                ]);

                $plantConsumptionGraph[] = ${"file" . $key};
            } else if ($current == 'Total Time') {

                $value = $totalValue;
                $totalValue = $totalValue - $actualValue;

                ${"file" . $key} = collect([
                    "value" => round(abs($totalValue), 2),
                    "color" => '#86BD4D',
                    "name" => $current,
                ]);

                $plantConsumptionGraph[] = ${"file" . $key};
            }
        }

        $fileNull = collect([
            "value" => round($value, 2),
            "name" => null,
            "itemStyle" => [
                "opacity" => 0
            ],
            "tooltip" => [
                "show" => false
            ]
        ]);

        $plantConsumptionGraph[] = $fileNull;

        $data['plant_consumption_graph'] = $plantConsumptionGraph;
        $data['total_value'] = $value;
        $data['current_time_value'] = $actualValue;
        $data['total_time_value'] = $totalValue;
        $data['legend_array'] = $legendArray;

        return $data;
    }

    /*private function plant_saving_graph($finalPlantArray) {

        $currentSavingArray = ['Today Saving', 'Today Expected Saving'];

        $plantSavingGraph = [];
        $legendArray = [];
        $value = 0;
        $totalValue = 0;
        $expectedValue = 0;
        $savingValue = 1;
        $savingArray = [];

        foreach($currentSavingArray as $key => $current) {

            $legendArray[] = $current;

            if($current == 'Today Saving') {

                $totalValue = DailyProcessedPlantDetail::whereDate('created_at', date('Y-m-d'))->whereIn('plant_id', (array)$finalPlantArray)->sum('dailySaving');
                if( $totalValue != 0) {
                    ${"file" . $key} = collect([
                        "value" => round($totalValue, 2),
                        "name" => $current,
                    ]);
                }
                else
                {
                    ${"file" . $key} = collect([
                        "value" => 0,
                        "name" => $current,
                    ]);
                }
                 array_push($savingArray,$totalValue);
                $plantSavingGraph[] = ${"file" . $key};
            }

            else if($current == 'Today Expected Saving') {

                $value = TotalProcessedPlantDetail::whereIn('plant_id', (array)$finalPlantArray)->sum('plant_total_saving');
                $expectedValue = $value - $totalValue;
                if( $totalValue != 0) {
                    ${"file" . $key} = collect([
                        "value" => round($expectedValue, 2),
                        "name" => $current,
                    ]);
                }
                else
                {
                    ${"file" . $key} = collect([
                        "value" => null,
                        "name" => $current,
                    ]);
                }
                array_push($savingArray,round($expectedValue, 2));
                $plantSavingGraph[] = ${"file" . $key};
            }
        }

        $fileNull = collect([
            "value" => round($value, 2),
            "name" => null,
            "itemStyle" => [
                "opacity" => 0
            ],
            "tooltip" => [
                "show" => false
            ]
        ]);
        if(array_sum($savingArray) == 0) {
            $savingValue = 0;
        } else {
            $savingValue = 1;
        }

        $plantSavingGraph[] = $fileNull;

        $data['plant_saving_graph'] = $plantSavingGraph;
        $data['legend_array'] = $legendArray;
        $data['savingValue'] = $savingValue;

        return $data;
    }*/

    private function plantSavingGraph($finalPlantArray)
    {

        $currentSavingArray = ['Today Saving', 'Today Expected Saving'];

        $plantSavingGraph = [];
        $legendArray = [];
        $value = 0;
        $todayValue = 0;
        $expectedValue = 0;

        foreach ($currentSavingArray as $key => $current) {

            $legendArray[] = $current;

            if ($current == 'Today Saving') {

                $todayValue = DailyProcessedPlantDetail::whereDate('created_at', date('Y-m-d'))->whereIn('plant_id', (array)$finalPlantArray)->sum('dailySaving');

                ${"file" . $key} = collect([
                    "value" => round($todayValue, 2),
                    "name" => $current,
                ]);

                $plantSavingGraph[] = ${"file" . $key};
            } else if ($current == 'Today Expected Saving') {

                $expectedValue = Plant::whereIn('id', (array)$finalPlantArray)->sum('daily_expected_saving');

                $data['total_expected_value'] = $expectedValue;

                if ($todayValue >= $expectedValue) {

                    $value = $todayValue;
                    $expectedValue = 0;
                } else {

                    $value = $expectedValue;
                    $expectedValue = $value - $todayValue;
                }

                ${"file" . $key} = collect([
                    "value" => round($expectedValue, 2),
                    "name" => $current,
                ]);

                $plantSavingGraph[] = ${"file" . $key};
            }
        }

        $fileNull = collect([
            "value" => round($value, 2),
            "name" => null,
            "itemStyle" => [
                "opacity" => 0
            ],
            "tooltip" => [
                "show" => false
            ]
        ]);

        $plantSavingGraph[] = $fileNull;

        $data['plant_saving_graph'] = $plantSavingGraph;
        $data['total_value'] = $value;
        $data['daily_value'] = $todayValue;
        $data['expected_value'] = $expectedValue;
        $data['legend_array'] = $legendArray;

        return $data;
    }

    private function plantTodayYesterdayGraph($todayValue, $yesterdayValue)
    {

        $data['total_yesterday_value'] = $yesterdayValue;

        $todayYesterdayArray = ['Today', 'Yesterday'];

        $plantTodayYesterdayGraph = [];
        $legendArray = [];
        $value = 0;

        foreach ($todayYesterdayArray as $key => $current) {

            $legendArray[] = $current;

            if ($current == 'Today') {

                ${"file" . $key} = collect([
                    "value" => round($todayValue, 2),
                    "name" => $current,
                ]);

                $plantTodayYesterdayGraph[] = ${"file" . $key};
            } else if ($current == 'Yesterday') {

                if ($todayValue >= $yesterdayValue) {

                    $value = $todayValue;
                    $yesterdayValue = 0;
                } else {

                    $value = $yesterdayValue;
                    $yesterdayValue = (double)$value - (double)$todayValue;
                }


                ${"file" . $key} = collect([
                    "value" => round($yesterdayValue, 2),
                    "name" => $current,
                ]);

                $plantTodayYesterdayGraph[] = ${"file" . $key};
            }
        }

        $fileNull = collect([
            "value" => round($value, 2),
            "name" => null,
            "itemStyle" => [
                "opacity" => 0
            ],
            "tooltip" => [
                "show" => false
            ]
        ]);

        $plantTodayYesterdayGraph[] = $fileNull;

        $data['plant_today_yesterday_graph'] = $plantTodayYesterdayGraph;
        $data['total_value'] = $value;
        $data['today_value'] = $todayValue;
        $data['yesterday_value'] = $yesterdayValue;
        $data['legend_array'] = $legendArray;

        return $data;
    }

    private function plantStatusGraph($plantStatusArray)
    {

        $statusArray = ['Online', 'Offline', 'Alarm', 'Fault'];

        $plantStatusGraph = [];
        $legendArray = [];
        $value = $plantStatusArray['total'];
        $alarmArray = [];
        $alarmValue = 1;
        foreach ($statusArray as $key => $current) {

            $legendArray[] = $current;

            if ($current == 'Online') {

                ${"file" . $key} = collect([
                    "value" => abs(($plantStatusArray['online'] - $plantStatusArray['fault']) - $plantStatusArray['alarm']),
                    "name" => $current,
                ]);
                if (abs(($plantStatusArray['online'] - $plantStatusArray['fault']) - $plantStatusArray['alarm']) != 0) {
                    $plantStatusGraph[] = ${"file" . $key};
                } else {
                    ${"file" . $key} = collect([
                        "value" => 0,
                        "name" => $current,
                    ]);
                    $plantStatusGraph[] = ${"file" . $key};
                }
                array_push($alarmArray, abs(($plantStatusArray['online'] - $plantStatusArray['fault']) - $plantStatusArray['alarm']));
            } else if ($current == 'Offline') {

                ${"file" . $key} = collect([
                    "value" => $plantStatusArray['offline'],
                    "name" => $current,
                ]);
                if ($plantStatusArray['offline'] != 0) {

                    $plantStatusGraph[] = ${"file" . $key};
                } else {
                    ${"file" . $key} = collect([
                        "value" => null,
                        "name" => $current,
                    ]);
                    $plantStatusGraph[] = ${"file" . $key};
                }
                array_push($alarmArray, $plantStatusArray['offline']);
            } else if ($current == 'Fault') {

                ${"file" . $key} = collect([
                    "value" => $plantStatusArray['fault'],
                    "name" => $current,
                ]);
                if ($plantStatusArray['fault'] != 0) {

                    $plantStatusGraph[] = ${"file" . $key};
                } else {
                    ${"file" . $key} = collect([
                        "value" => null,
                        "name" => $current,
                    ]);
                    $plantStatusGraph[] = ${"file" . $key};

                }
                array_push($alarmArray, $plantStatusArray['fault']);
            } else if ($current == 'Alarm') {

                ${"file" . $key} = collect([
                    "value" => $plantStatusArray['alarm'],
                    "name" => $current,
                ]);
                if ($plantStatusArray['alarm'] != 0) {
                    $plantStatusGraph[] = ${"file" . $key};
                } else {
                    ${"file" . $key} = collect([
                        "value" => null,
                        "name" => $current,
                    ]);
                    $plantStatusGraph[] = ${"file" . $key};
                }
                array_push($alarmArray, $plantStatusArray['alarm']);
            }
        }

        $fileNull = collect([
            "value" => round($value, 2),
            "name" => null,
            "itemStyle" => [
                "opacity" => 0
            ],
            "tooltip" => [
                "show" => false
            ]
        ]);
        if (array_sum($alarmArray) == 0) {
            $alarmValue = 0;
        } else {
            $alarmValue = 1;
        }

//        for($i=0;$i<4;$i++)
//        {
//            if($plantStatusGraph[$i]['value'] == 0)
//            {
//                $alarmValue = 0;
//            }
//            else
//            {
//                $alarmValue = 2;
//            }
//        }
        $plantStatusGraph[] = $fileNull;

        $data['plant_status_graph'] = $plantStatusGraph;
        $data['legend_array'] = $legendArray;
        $data['alarmValue'] = $alarmValue;

        return $data;
    }

    public function plantActualExpectedGraph(Request $request)
    {

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;

        if ($time == 'day') {
            $date = date('Y-m-d', $requestDate);
        } else if ($time == 'month') {
            $date = date('Y-m', $requestDate);
        } else if ($time == 'year') {
            $date = $request->date;
        }

        $actualExpectedArray = ['Actual', 'Expected'];

        $plantActualExpectedGraph = [];
        $legendArray = [];
        $actualValue = 0;
        $expectedValue = 0;
        $percentageExpectedValue = 0;
        $value = 0;

        foreach ($actualExpectedArray as $key => $current) {

            $legendArray[] = $current;

            if ($time == 'day') {

                if ($current == 'Actual') {

                    $actualValue = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->orderBy('updated_at', 'DESC')->first()->dailyGeneration : 0;

                    ${"file" . $key} = collect([
                        "value" => round($actualValue, 2),
                        "name" => $current,
                    ]);

                    $plantActualExpectedGraph[] = ${"file" . $key};
                } else if ($current == 'Expected') {

                    $expectedData = ExpectedGenerationLog::where('plant_id', $plantID)->whereDate('created_at', '<=', $date)->orderBy('created_at', 'DESC')->first();
                    $data['total_expected_value'] = $percentageExpectedValue = $expectedDataValue = $expectedData ? $expectedData->daily_expected_generation : 0;


                    if ($actualValue > $expectedDataValue) {

                        $value = $actualValue;
                        $expectedValue = 0;
                    } else {

                        $value = $expectedDataValue;
                        $expectedValue = $expectedDataValue - $actualValue;
                    }

                    ${"file" . $key} = collect([
                        "value" => round($expectedValue, 2),
                        "name" => $current,
                    ]);

                    $plantActualExpectedGraph[] = ${"file" . $key};

                    $fileNull = collect([
                        "value" => round($value, 2),
                        "name" => null,
                        "itemStyle" => [
                            "opacity" => 0
                        ],
                        "tooltip" => [
                            "show" => false
                        ]
                    ]);

                    $plantActualExpectedGraph[] = $fileNull;
                }
            } else if ($time == 'month') {

                if ($current == 'Actual') {

                    $actualValue = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '%')->exists() ? MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '%')->orderBy('updated_at', 'DESC')->first()->monthlyGeneration : 0;

                    ${"file" . $key} = collect([
                        "value" => round($actualValue, 2),
                        "name" => $current,
                    ]);

                    $plantActualExpectedGraph[] = ${"file" . $key};
                } else if ($current == 'Expected') {

                    $explode_data = explode('-', $date);
                    $mon = $explode_data[1];
                    $yer = $explode_data[0];
                    $expectedDataValue = 0;

                    $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);

                    for ($i = 1; $i <= $dd; $i++) {

                        if ($i < 10) {
                            $i = '0' . $i;
                        }

                        $expectedDataValue += ExpectedGenerationLog::where('plant_id', $plantID)->where('created_at', '<=', $date . '-' . $i . ' 23:59:59')->exists() ? ExpectedGenerationLog::where('plant_id', $plantID)->where('created_at', '<=', $date . '-' . $i . ' 23:59:59')->orderBy('created_at', 'DESC')->first()->daily_expected_generation : 0;
                    }

                    $data['total_expected_value'] = $percentageExpectedValue = $expectedDataValue;

                    if ($actualValue > $expectedDataValue) {

                        $value = $actualValue;
                        $expectedValue = 0;
                    } else {

                        $value = $expectedDataValue;
                        $expectedValue = $expectedDataValue - $actualValue;
                    }

                    ${"file" . $key} = collect([
                        "value" => round($expectedValue, 2),
                        "name" => $current,
                    ]);

                    $plantActualExpectedGraph[] = ${"file" . $key};

                    $fileNull = collect([
                        "value" => round($value, 2),
                        "name" => null,
                        "itemStyle" => [
                            "opacity" => 0
                        ],
                        "tooltip" => [
                            "show" => false
                        ]
                    ]);

                    $plantActualExpectedGraph[] = $fileNull;
                }
            } else if ($time == 'year') {

                if ($current == 'Actual') {

                    $actualValue = YearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->exists() ? YearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->orderBy('updated_at', 'DESC')->first()->yearlyGeneration : 0;

                    ${"file" . $key} = collect([
                        "value" => round($actualValue, 2),
                        "name" => $current,
                    ]);

                    $plantActualExpectedGraph[] = ${"file" . $key};
                } else if ($current == 'Expected') {

                    $expectedDataValue = 0;

                    for ($i = 1; $i <= 12; $i++) {

                        if ($i < 10) {

                            $i = '0' . $i;

                            $ddd = cal_days_in_month(CAL_GREGORIAN, $i, $date);

                            for ($j = 1; $j <= $ddd; $j++) {

                                if ($j < 10) {

                                    $j = '0' . $j;
                                }

                                $expectedDataValue += ExpectedGenerationLog::where('plant_id', $plantID)->where('created_at', '<=', $date . '-' . $i . '-' . $j . ' 23:59:59')->exists() ? ExpectedGenerationLog::where('plant_id', $plantID)->where('created_at', '<=', $date . '-' . $i . '-' . $j . ' 23:59:59')->orderBy('created_at', 'DESC')->first()->daily_expected_generation : 0;
                            }
                        }
                    }

                    $data['total_expected_value'] = $percentageExpectedValue = $expectedDataValue;


                    if ($actualValue > $expectedDataValue) {

                        $value = $actualValue;
                        $expectedValue = 0;
                    } else {

                        $value = $expectedDataValue;
                        $expectedValue = $expectedDataValue - $actualValue;
                    }

                    ${"file" . $key} = collect([
                        "value" => round($expectedValue, 2),
                        "name" => $current,
                    ]);

                    $plantActualExpectedGraph[] = ${"file" . $key};

                    $fileNull = collect([
                        "value" => round($value, 2),
                        "name" => null,
                        "itemStyle" => [
                            "opacity" => 0
                        ],
                        "tooltip" => [
                            "show" => false
                        ]
                    ]);

                    $plantActualExpectedGraph[] = $fileNull;
                }
            }
        }

        $actualValueConverted = $this->unitConversion($actualValue, 'kWh');
        $expectedValueConverted = $this->unitConversion($percentageExpectedValue, 'kWh');

        $data['plant_actual_expected_graph'] = $plantActualExpectedGraph;
        $data['total_value'] = $value;
        $data['actual_value'] = $actualValue;
        $data['actual_percentage'] = $percentageExpectedValue > 0 ? round((($actualValue / $percentageExpectedValue) * 100), 2) : round($actualValue * 100, 2);
        $data['expected_value'] = $expectedValue;
        $data['actual_converted_value'] = round($actualValueConverted[0], 2) . ' ' . $actualValueConverted[1];
        $data['expected_converted_value'] = round($expectedValueConverted[0], 2) . ' ' . $expectedValueConverted[1];
        $data['legend_array'] = $legendArray;

        return $data;
    }

    public function plantEnvironmentalBenefitsGraph(Request $request)
    {

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;

        if ($time == 'day') {
            $date = date('Y-m-d', $requestDate);
        } else if ($time == 'month') {
            $date = date('Y-m', $requestDate);
        } else if ($time == 'year') {
            $date = $request->date;
        }

        $envPlanting = Setting::where('perimeter', 'env_planting')->pluck('value')[0];
        $envReduction = Setting::where('perimeter', 'env_reduction')->pluck('value')[0];

        $environmentArray = array();

        if ($time == 'day') {

            $dailyGeneration = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->sum('dailyGeneration');

            $treeValue = $dailyGeneration * $envPlanting;
            $co2Value = $dailyGeneration * $envReduction;

            $environmentArray['tree_value'] = round($treeValue, 2);
            $environmentArray['co2_value'] = round($co2Value, 2);

            return $environmentArray;
        } else if ($time == 'month') {

            $monthlyGeneration = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-%')->sum('monthlyGeneration');

            $treeValue = $monthlyGeneration * $envPlanting;
            $co2Value = $monthlyGeneration * $envReduction;

            $environmentArray['tree_value'] = round($treeValue, 2);
            $environmentArray['co2_value'] = round($co2Value, 2);

            return $environmentArray;
        } else if ($time == 'year') {

            $yearlyGeneration = YearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->sum('yearlyGeneration');

            $treeValue = $yearlyGeneration * $envPlanting;
            $co2Value = $yearlyGeneration * $envReduction;

            $environmentArray['tree_value'] = round($co2Value, 2);
            $environmentArray['co2_value'] = round($co2Value, 2);

            return $environmentArray;
        }

    }

    public function plantHistoryGraph(Request $request)
    {

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;
        $plantMeterType = $request->plantMeterType;
        $plantHistoryGraphYAxis = [];

        if ($time == 'day') {
            $date = date('Y-m-d', $requestDate);
        } else if ($time == 'month') {
            $date = date('Y-m', $requestDate);
        } else if ($time == 'year') {
            $date = $request->date;
        }

        $plantData = Plant::findOrFail($plantID);
        $plantEMIData = Plant::with('latest_inverter_emi_details')->where('id', $plantID)->first();

        $historyArray = json_decode($request->historyCheckBoxArray);
        $historyArray = (array)$historyArray;
//        return json_encode(in_array("soc", $historyArray));

        if (in_array("generation", $historyArray) || in_array("consumption", $historyArray) || in_array("grid", $historyArray) || in_array("buy", $historyArray) || in_array("sell", $historyArray) || in_array("soc", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => $time == 'day' ? 'kW' : 'kWh',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#333333',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }

        if (in_array("saving", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'PKR',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#333333',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }

        if (in_array("irradiance", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => $time == 'day' ? 'W/m' . json_decode('"\u00B2"') : 'kWh/m' . json_decode('"\u00B2"'),
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#333333',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }
        if (in_array("soc", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => '%',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#333333',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }

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

        if (strtotime($date) == strtotime(date('Y-m-d'))) {

            $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
            $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
            $currentGeneration = ProcessedCurrentVariable::where('plant_id', $plantID)->whereBetween('collect_time', [date($date . ' 00:00:00'), $finalCurrentDataDateTime])->groupBy('collect_time')->get();
        } else {

            $currentGeneration = ProcessedCurrentVariable::where('plant_id', $plantID)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])->groupBy('collect_time')->get();
        }

        foreach ($historyArray as $key => $current) {

            $todayLogData = [];
            $todayLogTime = [];
            $todayLogDataSum = 0;
            $graphColor = '';

            if ($time == 'day') {

                $tooltipDate = date('d-m-Y', strtotime($date));

                $graphType = 'line';

                foreach ($currentGeneration as $key => $todayLog) {

                    $todayLogTime[] = date('H:i', strtotime($todayLog->collect_time));

                    if ($current == 'generation') {

                        $graphColor = '#F6A944';
                        $todayLogDataSum = $todayLog->current_generation;
                    } else if ($current == 'consumption') {

                        $graphColor = '#46C1AB';
                        $todayLogDataSum = $todayLog->current_consumption / 1000;
                    } else if ($current == 'grid') {

                        $graphColor = '#E38595';

                        if ($todayLog->grid_type == '-ve') {

                            $todayLogDataSum = (-1) * $todayLog->current_grid;
                        } else {

                            $todayLogDataSum = $todayLog->current_grid;
                        }
                    } else if ($current == 'buy') {

                        $graphColor = '#8FC34D';
                        $todayLogDataSum = $todayLog->grid_type == '+ve' ? $todayLog->current_grid : 0;
                    } else if ($current == 'sell') {

                        $graphColor = '#435EBE';
                        $todayLogDataSum = $todayLog->grid_type == '-ve' ? $todayLog->current_grid : 0;
                    } else if ($current == 'saving') {

                        $graphColor = '#009FFD';
                        $todayLogDataSum = $todayLog->current_saving;
                    } else if ($current == 'irradiance') {

                        $graphColor = '#F933C8';
                        $todayLogDataSum = $todayLog->current_irradiance;
                    } else if ($current == 'soc') {

                        $graphColor = '#605bf4';
                        $todayLogDataSum = $todayLog->battery_capacity;
                    } else if ($current == 'battery-charge') {
                        $todayLog->battery_power = (int)$todayLog->battery_power;
//                        return gettype((int)$todayLog->battery_power);
                        $graphColor = '#f2b610';
                        if ($todayLog->battery_power < 0) {
                            $todayLogDataSum = $todayLog->battery_power;
                            $todayLogDataSum = $todayLogDataSum / 1000;
                        } elseif ($todayLog->battery_power > 0) {
//                            return 'okkkkkkk';
                            $todayLogDataSum = null;
                        }
//                        return gettype($todayLog->battery_power);
//                        return $todayLog->battery_power;
                    } else if ($current == 'battery-discharge') {
                        $todayLog->battery_power = (int)$todayLog->battery_power;
                        $graphColor = '#31bfbf';

                        if ($todayLog->battery_power > 0) {
                            $todayLogDataSum = $todayLog->battery_power;
                            $todayLogDataSum = $todayLogDataSum / 1000;
                        } elseif ($todayLog->battery_power < 0) {
                            $todayLogDataSum = null;
                        }
                    } else if ($current == 'battery-power') {

                        $graphColor = '#45c745';
                        $todayLogDataSum = $todayLog->battery_power;
                        $todayLogDataSum = $todayLogDataSum / 1000;
                    }

                    $todayLogData[] = round($todayLogDataSum, 2);
                }
//                return $todayLogDataSum;
            } else if ($time == 'month') {

                $tooltipDate = date('m-Y', strtotime($date));

                $graphType = 'bar';

                $explodeData = explode('-', $date);
                $mon = $explodeData[1];
                $yer = $explodeData[0];

                $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);

                for ($i = 1; $i <= $dd; $i++) {

                    if ($i < 10) {
                        $i = '0' . $i;
                    }

                    $todayLogTime[] = $i;

                    if ($current == 'generation') {

                        $graphColor = '#F6A944';
                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailyGeneration : 0;
                    } else if ($current == 'consumption') {

                        $graphColor = '#46C1AB';
                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailyConsumption : 0;
                    } else if ($current == 'grid') {

                        $graphColor = '#E38595';
                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailyGridPower : 0;
                    } else if ($current == 'buy') {

                        $graphColor = '#8FC34D';
                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailyBoughtEnergy : 0;
                    } else if ($current == 'sell') {

                        $graphColor = '#435EBE';
                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailySellEnergy : 0;
                    } else if ($current == 'saving') {

                        $graphColor = '#009FFD';
                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailySaving : 0;
                    } else if ($current == 'irradiance') {

                        $graphColor = '#F933C8';
                        $todayLogDataSum = DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->daily_irradiance : 0;
                    } else if ($current == 'battery-charge') {

                        $graphColor = '#f2b610';
                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->daily_charge_energy : 0;
                    } else if ($current == 'battery-discharge') {

                        $graphColor = '#31bfbf';
                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->daily_discharge_energy : 0;
                    }

                    $todayLogData[] = round($todayLogDataSum, 2);
                }
            } else if ($time == 'year') {

                $tooltipDate = $date;

                $graphType = 'bar';

                for ($i = 1; $i <= 12; $i++) {

                    if ($i < 10) {
                        $i = '0' . $i;
                    }

                    $todayLogTime[] = substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3);

                    if ($current == 'generation') {

                        $graphColor = '#F6A944';
                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGeneration');
                    } else if ($current == 'consumption') {

                        $graphColor = '#46C1AB';
                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyConsumption');
                    } else if ($current == 'grid') {

                        $graphColor = '#E38595';
                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGridPower');
                    } else if ($current == 'buy') {

                        $graphColor = '#8FC34D';
                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyBoughtEnergy');
                    } else if ($current == 'sell') {

                        $graphColor = '#435EBE';
                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySellEnergy');
                    } else if ($current == 'saving') {

                        $graphColor = '#009FFD';
                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySaving');
                    } else if ($current == 'irradiance') {

                        $graphColor = '#F933C8';
                        $todayLogDataSum = MonthlyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthly_irradiance');
                    } else if ($current == 'battery-charge') {

                        $graphColor = '#f2b610';
                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthly_charge_energy');
                    } else if ($current == 'battery-discharge') {

                        $graphColor = '#31bfbf';
                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthly_discharge_energy');
                    }

                    $todayLogData[] = round($todayLogDataSum, 2);
                }
            }

            if ($graphType == 'line') {

                if ($current == "generation") {

                    $legendArray[] = "Generation";

                    $historyObject = collect([

                        "name" => "Generation",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "data" => $todayLogData,
                        "areaStyle" => (object)[
//                            "color" => 'rgb(246 169 68 / 49%)',
                            "opacity" => 0.1// color of the background
                        ],
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "consumption") {

                    $legendArray[] = "Consumption";

                    $historyObject = collect([

                        "name" => "Consumption",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "data" => $todayLogData,
                        "areaStyle" => (object)[
//                            "color" => 'rgb(70 193 171 / 49%)',
                            "opacity" => 0.1// color of the background
                        ],
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "grid") {

                    $legendArray[] = "Grid";

                    $historyObject = collect([

                        "name" => "Grid",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "color" => $graphColor,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "showSymbol" => false,
                        "data" => $todayLogData,
                        "areaStyle" => (object)[
//                            "color" => 'rgb(227 133 149 / 49%)', // color of the background
                            "opacity" => 0.1],
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "buy") {

                    $legendArray[] = "Buy";

                    $historyObject = collect([

                        "name" => "Buy",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "data" => $todayLogData,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "areaStyle" => (object)[
//                            "color" => 'rgb(143 195 77 / 49%)', // color of the background
                            "opacity" => 0.1],
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "sell") {

                    $legendArray[] = "Sell";

                    $historyObject = collect([

                        "name" => "Sell",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "data" => $todayLogData,
                        "areaStyle" => (object)[
//                            "color" => 'rgb(49 115 218 / 49%)', // color of the background
                            "opacity" => 0.1],
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "irradiance") {

                    $legendArray[] = "Irradiance";

                    $historyObject = collect([

                        "name" => "Irradiance",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "data" => $todayLogData,
                        "areaStyle" => (object)[],
                        "yAxisIndex" => count($historyArray) == 1 ? 0 : 1,
                    ]);
                } else if ($current == "saving") {

                    $legendArray[] = "Cost Saving";

                    $historyObject = collect([

                        "name" => "Cost Saving",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "data" => $todayLogData,
                        "areaStyle" => (object)[
//                            "color" => 'rgb(0 159 253 / 49%)', // color of the background
                            "opacity" => 0.1],
                        "yAxisIndex" => count($historyArray) == 1 ? 0 : (in_array('irradiance', $historyArray) ? 0 : 1),
                    ]);
                } else if ($current == "soc") {
//                    return $historyArray;

                    $legendArray[] = "SOC";

                    $historyObject = collect([

                        "name" => "SOC",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "data" => $todayLogData,
                        "areaStyle" => (object)[
//                            "color" => 'rgb(96 91 244 / 49%)', // color of the background
                            "opacity" => 0.1],
                        "yAxisIndex" => count($historyArray) == 1 ? 0 : (in_array('soc', $historyArray) ? 1 : 0),
                    ]);
                } else if ($current == "battery-power") {

                    $legendArray[] = "Battery Power";

                    $historyObject = collect([

                        "name" => "Battery Power",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "data" => $todayLogData,
                        "areaStyle" => (object)[
//                            "color" => 'rgb(69 199 69 / 49%)', // color of the background
                            "opacity" => 0.1],
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "battery-charge") {

                    $legendArray[] = "Battery Charge";
                    $array2 = array_map(function ($value) {
                        return (int)$value === 0 ? NULL : $value;
                    }, $todayLogData);
//                    return $array2;

                    $historyObject = collect([

                        "name" => "Battery Charge",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "data" => $array2,
                        "areaStyle" => (object)[
//                            "color" => 'rgb(242 182 16 / 49%)', // color of the background
                            "opacity" => 0.1],
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "battery-discharge") {
                    $array2 = array_map(function ($value) {
                        return (int)$value === 0 ? NULL : $value;
                    }, $todayLogData);

                    $legendArray[] = "Battery Discharge";

                    $historyObject = collect([

                        "name" => "Battery Discharge",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "data" => $array2,
                        "areaStyle" => (object)[
//                            "color" => 'rgb(49 191 191 / 49%)', // color of the background
                            "opacity" => 0.1],
                        "yAxisIndex" => 0,
                    ]);
                }
            } else if ($graphType == 'bar') {

                if ($current == "generation") {

                    $legendArray[] = "Generation";

                    $historyObject = collect([

                        "name" => "Generation",
                        "type" => $graphType,
                        "color" => $graphColor,
                        'barGap' => '0%',
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "consumption") {

                    $legendArray[] = "Consumption";

                    $historyObject = collect([

                        "name" => "Consumption",
                        "type" => $graphType,
                        "color" => $graphColor,
                        'barGap' => '0%',
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "grid") {

                    $legendArray[] = "Grid";

                    $historyObject = collect([

                        "name" => "Grid",
                        "type" => $graphType,
                        "color" => $graphColor,
                        'barGap' => '0%',
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "buy") {

                    $legendArray[] = "Buy";

                    $historyObject = collect([

                        "name" => "Buy",
                        "type" => $graphType,
                        "color" => $graphColor,
                        'barGap' => '0%',
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "sell") {

                    $legendArray[] = "Sell";

                    $historyObject = collect([

                        "name" => "Sell",
                        "type" => $graphType,
                        "color" => $graphColor,
                        'barGap' => '0%',
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "irradiance") {

                    $legendArray[] = "Irradiance";

                    $historyObject = collect([

                        "name" => "Irradiance",
                        "type" => $graphType,
                        "color" => $graphColor,
                        'barGap' => '0%',
                        "data" => $todayLogData,
                        "yAxisIndex" => count($historyArray) == 1 ? 0 : 1,
                    ]);
                } else if ($current == "saving") {

                    $legendArray[] = "Cost Saving";

                    $historyObject = collect([

                        "name" => "Cost Saving",
                        "type" => $graphType,
                        "color" => $graphColor,
                        'barGap' => '0%',
                        "data" => $todayLogData,
                        "yAxisIndex" => count($historyArray) == 1 ? 0 : (in_array('irradiance', $historyArray) ? 0 : 1),
                    ]);
                } else if ($current == "battery-charge") {
                    $legendArray[] = "Battery Charge";
                    $historyObject = collect([

                        "name" => "Battery Charge",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "data" => $todayLogData,
                        "areaStyle" => (object)["color" => 'rgb(242 182 16 / 49%)', // color of the background
                            "opacity" => 0.5],
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "battery-discharge") {

                    $legendArray[] = "Battery Discharge";

                    $historyObject = collect([

                        "name" => "Battery Discharge",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "data" => $todayLogData,
                        "areaStyle" => (object)["color" => 'rgb(49 191 191 / 49%)', // color of the background
                            "opacity" => 0.5],
                        "yAxisIndex" => 0,
                    ]);
                }
            }

            $plantHistoryGraph[] = $historyObject;
        }

        $data['plant_history_graph'] = $plantHistoryGraph;
        $data['time_type'] = $time;
        $data['time_details'] = [];
        if ($time == 'day') {

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

            $data['time_array'] = $todayLogTime;
//            return $data['time_array'];
        } else {

            $data['time_array'] = $todayLogTime;
//            return $data['time_array'];
        }
        $date12 = date('Y-m', strtotime($date));
        $date13 = date('Y', strtotime($date));
        if ($time == 'day') {
            for ($i = 0; $i < count($data['time_array']); $i++) {
                array_push($data['time_details'], $date . ' ' . $data['time_array'][$i]);
            }
        } elseif ($time == 'month') {
            for ($i = 0; $i < count($data['time_array']); $i++) {
                array_push($data['time_details'], $date12 . '-' . $data['time_array'][$i]);
            }
        } else {
            for ($i = 0; $i < count($data['time_array']); $i++) {
                array_push($data['time_details'], $data['time_array'][$i] . '-' . $date13);
            }
        }
//        return $data['time_details'];

        $data['legend_array'] = $legendArray;
        $data['tooltip_date'] = $tooltipDate;

        if ($time == 'day') {

            if (DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->exists()) {

                $dataResponse = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->first();

                $totalGeneration = $dataResponse->dailyGeneration;
                $totalConsumption = $dataResponse->dailyConsumption;
                $totalGrid = $dataResponse->dailyGridPower;
                $totalBuy = $dataResponse->dailyBoughtEnergy;
                $totalSell = $dataResponse->dailySellEnergy;
                $totalSaving = $dataResponse->dailySaving;
                $totalChargeEnergy = $dataResponse->daily_charge_energy;
                $totalDischargeEnergy = $dataResponse->daily_discharge_energy;
            }

            if (DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->exists()) {

                $dataResponse = DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->first();

                $totalIrradiation = $dataResponse->daily_irradiance;
                $data['total_irradiation'] = round($totalIrradiation, 2);
            }
        }

        if ($time == 'month') {

            if (MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '%')->exists()) {

                $dataResponse = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '%')->first();

                $totalGeneration = $dataResponse->monthlyGeneration;
                $totalConsumption = $dataResponse->monthlyConsumption;
                $totalGrid = $dataResponse->monthlyGridPower;
                $totalBuy = $dataResponse->monthlyBoughtEnergy;
                $totalSell = $dataResponse->monthlySellEnergy;
                $totalSaving = $dataResponse->monthlySaving;
                $totalChargeEnergy = $dataResponse->monthly_charge_energy;
                $totalDischargeEnergy = $dataResponse->monthly_discharge_energy;
            }

            if (MonthlyProcessedPlantEMIDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '%')->exists()) {

                $dataResponse = MonthlyProcessedPlantEMIDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '%')->first();

                $totalIrradiation = $dataResponse->monthly_irradiance;
                $data['total_irradiation'] = round($totalIrradiation, 2);
            }
        }

        if ($time == 'year') {

            if (YearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->exists()) {

                $dataResponse = YearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->first();

                $totalGeneration = $dataResponse->yearlyGeneration;
                $totalConsumption = $dataResponse->yearlyConsumption;
                $totalGrid = $dataResponse->yearlyGridPower;
                $totalBuy = $dataResponse->yearlyBoughtEnergy;
                $totalSell = $dataResponse->yearlySellEnergy;
                $totalSaving = $dataResponse->yearlySaving;
                $totalChargeEnergy = $dataResponse->yearly_charge_energy;
                $totalDischargeEnergy = $dataResponse->yearly_discharge_energy;
            }

            if (YearlyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->exists()) {

                $dataResponse = YearlyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->first();

                $totalIrradiation = $dataResponse->yearly_irradiance;
                $data['total_irradiation'] = round($totalIrradiation, 2);
            }
        }

        $totalGenerationConverted = $this->unitConversion($totalGeneration, 'kWh');
        $totalConsumptionConverted = $this->unitConversion($totalConsumption, 'kWh');
        $totalGridConverted = $this->unitConversion($totalGrid, 'kWh');
        $totalBuyConverted = $this->unitConversion($totalBuy, 'kWh');
        $totalSellConverted = $this->unitConversion($totalSell, 'kWh');
        $totalSavingConverted = $this->unitConversion($totalSaving, 'PKR');
        $totalChargedEnergy = $this->unitConversion($totalChargeEnergy, 'kWh');
        $totalDischargedEnergy = $this->unitConversion($totalDischargeEnergy, 'kWh');

        $data['total_generation'] = round($totalGenerationConverted[0], 2) . ' ' . $totalGenerationConverted[1];
        $data['total_charge'] = round($totalChargedEnergy[0], 2) . ' ' . $totalChargedEnergy[1];
        $data['total_discharge'] = round($totalGenerationConverted[0], 2) . ' ' . $totalGenerationConverted[1];
        $data['total_consumption'] = round($totalDischargedEnergy[0], 2) . ' ' . $totalDischargedEnergy[1];
        $data['total_grid'] = round($totalGridConverted[0], 2) . ' ' . $totalGridConverted[1];
        $data['total_buy'] = round($totalBuyConverted[0], 2) . ' ' . $totalBuyConverted[1];
        $data['total_sell'] = round($totalSellConverted[0], 2) . ' ' . $totalSellConverted[1];
        $data['total_saving'] = round($totalSavingConverted[0], 2) . ' ' . $totalSavingConverted[1];
        $data['y_axis_array'] = $plantHistoryGraphYAxis;

        return $data;
    }

    public function plantWeatherGraph(Request $request)
    {
        date_default_timezone_set('Asia/Karachi');
        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;
        $plantMeterType = $request->plantMeterType;
        $plantHistoryGraphYAxis = [];

        if ($time == 'day') {
            $date = date('Y-m-d', $requestDate);
        } else if ($time == 'month') {
            $date = date('Y-m', $requestDate);
        } else if ($time == 'year') {
            $date = $request->date;
        }

        $plantData = Plant::findOrFail($plantID);
        $plantEMIData = Plant::with('latest_inverter_emi_details')->where('id', $plantID)->first();

        $historyArray = ['generation'];
        $historyArray = (array)$historyArray;
//        return json_encode(in_array("soc", $historyArray));

        if (in_array("generation", $historyArray) || in_array("consumption", $historyArray) || in_array("grid", $historyArray) || in_array("buy", $historyArray) || in_array("sell", $historyArray) || in_array("soc", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => $time == 'day' ? 'kW' : 'kWh',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#333333',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }

        if (in_array("saving", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'PKR',
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#333333',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }

        if (in_array("irradiance", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => $time == 'day' ? 'W/m' . json_decode('"\u00B2"') : 'kWh/m' . json_decode('"\u00B2"'),
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#333333',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }
        if (in_array("soc", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => '%',
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#333333',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }

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

        if (strtotime($date) == strtotime(date('Y-m-d'))) {

            $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
            $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
            $currentGeneration = ProcessedCurrentVariable::where('plant_id', $plantID)->whereBetween('collect_time', [date($date . ' 00:00:00'), $finalCurrentDataDateTime])->groupBy('collect_time')->get();
        } else {

            $currentGeneration = ProcessedCurrentVariable::where('plant_id', $plantID)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])->groupBy('collect_time')->get();
        }
        $cloudsDetailData = [];

        foreach ($historyArray as $key => $current) {

            $todayLogData = [];
            $todayLogTime = [];

            $todayLogDataSum = 0;
            $graphColor = '';

            if ($time == 'day') {

                $tooltipDate = date('d-m-Y', strtotime($date));

                $graphType = 'line';

                foreach ($currentGeneration as $key => $todayLog) {

                    $todayLogTime[] = date('H:i', strtotime($todayLog->collect_time));

                    if ($current == 'generation') {

                        $graphColor = '#F6A944';
                        $todayLogDataSum = $todayLog->current_generation;
                    }
//                    else if ($current == 'consumption') {
//
//                        $graphColor = '#46C1AB';
//                        $todayLogDataSum = $todayLog->current_consumption / 1000;
//                    } else if ($current == 'grid') {
//
//                        $graphColor = '#E38595';
//
//                        if ($todayLog->grid_type == '-ve') {
//
//                            $todayLogDataSum = (-1) * $todayLog->current_grid;
//                        } else {
//
//                            $todayLogDataSum = $todayLog->current_grid;
//                        }
//                    } else if ($current == 'buy') {
//
//                        $graphColor = '#8FC34D';
//                        $todayLogDataSum = $todayLog->grid_type == '+ve' ? $todayLog->current_grid : 0;
//                    } else if ($current == 'sell') {
//
//                        $graphColor = '#435EBE';
//                        $todayLogDataSum = $todayLog->grid_type == '-ve' ? $todayLog->current_grid : 0;
//                    } else if ($current == 'saving') {
//
//                        $graphColor = '#009FFD';
//                        $todayLogDataSum = $todayLog->current_saving;
//                    } else if ($current == 'irradiance') {
//
//                        $graphColor = '#F933C8';
//                        $todayLogDataSum = $todayLog->current_irradiance;
//                    } else if ($current == 'soc') {
//
//                        $graphColor = '#605bf4';
//                        $todayLogDataSum = $todayLog->battery_capacity;
//                    } else if ($current == 'battery-charge') {
//                        $todayLog->battery_power = (int)$todayLog->battery_power;
////                        return gettype((int)$todayLog->battery_power);
//                        $graphColor = '#f2b610';
//                        if ($todayLog->battery_power < 0) {
//                            $todayLogDataSum = $todayLog->battery_power;
//                            $todayLogDataSum = $todayLogDataSum / 1000;
//                        } elseif ($todayLog->battery_power > 0) {
////                            return 'okkkkkkk';
//                            $todayLogDataSum = null;
//                        }
////                        return gettype($todayLog->battery_power);
////                        return $todayLog->battery_power;
//                    } else if ($current == 'battery-discharge') {
//                        $todayLog->battery_power = (int)$todayLog->battery_power;
//                        $graphColor = '#31bfbf';
//
//                        if ($todayLog->battery_power > 0) {
//                            $todayLogDataSum = $todayLog->battery_power;
//                            $todayLogDataSum = $todayLogDataSum / 1000;
//                        } elseif ($todayLog->battery_power < 0) {
//                            $todayLogDataSum = null;
//                        }
//                    } else if ($current == 'battery-power') {
//
//                        $graphColor = '#45c745';
//                        $todayLogDataSum = $todayLog->battery_power;
//                        $todayLogDataSum = $todayLogDataSum / 1000;
//                    }

                    $todayLogData[] = round($todayLogDataSum, 2);
                }
//                return $todayLogDataSum;
            } else if ($time == 'month') {

                $tooltipDate = date('m-Y', strtotime($date));

                $graphType = 'bar';

                $explodeData = explode('-', $date);
                $mon = $explodeData[1];
                $yer = $explodeData[0];

                $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);

                for ($i = 1; $i <= $dd; $i++) {

                    if ($i < 10) {
                        $i = '0' . $i;
                    }

                    $todayLogTime[] = $i;

                    if ($current == 'generation') {

                        $graphColor = '#F6A944';
                        $todayLogDataSum = DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->exists() ? DailyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '-' . $i . '%')->orderBy('updated_at', 'DESC')->first()->dailyGeneration : 0;
                    }
//                    else if ($current == 'consumption') {
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

                    $todayLogData[] = round($todayLogDataSum, 2);
                }
            } else if ($time == 'year') {

                $tooltipDate = $date;

                $graphType = 'bar';

                for ($i = 1; $i <= 12; $i++) {

                    if ($i < 10) {
                        $i = '0' . $i;
                    }

                    $todayLogTime[] = substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3);

                    if ($current == 'generation') {

                        $graphColor = '#F6A944';
                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGeneration');
                    }
//                    else if ($current == 'consumption') {
//
//                        $graphColor = '#46C1AB';
//                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyConsumption');
//                    } else if ($current == 'grid') {
//
//                        $graphColor = '#E38595';
//                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGridPower');
//                    } else if ($current == 'buy') {
//
//                        $graphColor = '#8FC34D';
//                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyBoughtEnergy');
//                    } else if ($current == 'sell') {
//
//                        $graphColor = '#435EBE';
//                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySellEnergy');
//                    } else if ($current == 'saving') {
//
//                        $graphColor = '#009FFD';
//                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySaving');
//                    } else if ($current == 'irradiance') {
//
//                        $graphColor = '#F933C8';
//                        $todayLogDataSum = MonthlyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthly_irradiance');
//                    } else if ($current == 'battery-charge') {
//
//                        $graphColor = '#f2b610';
//                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthly_charge_energy');
//                    } else if ($current == 'battery-discharge') {
//
//                        $graphColor = '#31bfbf';
//                        $todayLogDataSum = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthly_discharge_energy');
//                    }

                    $todayLogData[] = round($todayLogDataSum, 2);
                }
            }

            if ($graphType == 'line') {

//                return $todayLogData;

                if ($current == "generation") {


                    $legendArray[] = "Generation";

                    $historyObject = collect([

                        "name" => "Generation",
                        "type" => $graphType,
                        "smooth" => true,
                        "silent" => true,
                        "emphasis" => (object)[
                            "focus" => 'series'
                        ],
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "data" => $todayLogData,
                        "areaStyle" => (object)[
//                            "color" => 'rgb(246 169 68 / 49%)',
                            "opacity" => 0.1// color of the background
                        ],
                        "yAxisIndex" => 0,
                    ]);
                }
//                else if ($current == "consumption") {
//
//                    $legendArray[] = "Consumption";
//
//                    $historyObject = collect([
//
//                        "name" => "Consumption",
//                        "type" => $graphType,
//                        "barCategoryGap" => '-130%',
//                        "smooth" => true,
//                        "silent" => true,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "color" => $graphColor,
//                        "showSymbol" => false,
//                        "data" => $todayLogData,
//                        "areaStyle" => (object)[
////                            "color" => 'rgb(70 193 171 / 49%)',
//                            "opacity" => 0.1// color of the background
//                        ],
//                        "yAxisIndex" => 0,
//                        "z" => 10
//                    ]);
//                } else if ($current == "grid") {
//
//
//                    $legendArray[] = "Grid";
//
//                    $historyObject = collect([
//
//                        "name" => "Grid",
//                        "type" => $graphType,
//                        "barCategoryGap" => '-130%',
//                        "smooth" => true,
//                        "silent" => true,
//                        "color" => $graphColor,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "showSymbol" => false,
//                        "data" => $todayLogData,
//                        "areaStyle" => (object)[
////                            "color" => 'rgb(227 133 149 / 49%)', // color of the background
//                            "opacity" => 0.1],
//                        "yAxisIndex" => 0,
//                        "z" => 10
//                    ]);
//                } else if ($current == "buy") {
//
//
//                    $legendArray[] = "Buy";
//
//                    $historyObject = collect([
//
//                        "name" => "Buy",
//                        "type" => $graphType,
//                        "barCategoryGap" => '-130%',
//                        "smooth" => true,
//                        "silent" => true,
//                        "color" => $graphColor,
//                        "showSymbol" => false,
//                        "data" => $todayLogData,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "areaStyle" => (object)[
////                            "color" => 'rgb(143 195 77 / 49%)', // color of the background
//                            "opacity" => 0.1],
//                        "yAxisIndex" => 0,
//                        "z" => 10
//                    ]);
//                } else if ($current == "sell") {
//
//                    $legendArray[] = "Sell";
//
//                    $historyObject = collect([
//
//                        "name" => "Sell",
//                        "type" => $graphType,
//                        "barCategoryGap" => '-130%',
//                        "smooth" => true,
//                        "silent" => true,
//                        "color" => $graphColor,
//                        "showSymbol" => false,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "data" => $todayLogData,
//                        "areaStyle" => (object)[
////                            "color" => 'rgb(49 115 218 / 49%)', // color of the background
//                            "opacity" => 0.1],
//                        "yAxisIndex" => 0,
//                        "z" => 10
//                    ]);
//                } else if ($current == "irradiance") {
//
//                    $legendArray[] = "Irradiance";
//
//                    $historyObject = collect([
//
//                        "name" => "Irradiance",
//                        "type" => $graphType,
//                        "barCategoryGap" => '-130%',
//                        "smooth" => true,
//                        "silent" => true,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "color" => $graphColor,
//                        "showSymbol" => false,
//                        "data" => $todayLogData,
//                        "z" => 10,
//                        "areaStyle" => (object)[],
//                        "yAxisIndex" => count($historyArray) == 1 ? 0 : 1,
//                    ]);
//                } else if ($current == "saving") {
//
//
//                    $legendArray[] = "Cost Saving";
//
//                    $historyObject = collect([
//
//                        "name" => "Cost Saving",
//                        "type" => $graphType,
//                        "barCategoryGap" => '-130%',
//                        "smooth" => true,
//                        "silent" => true,
//                        "color" => $graphColor,
//                        "showSymbol" => false,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "data" => $todayLogData,
//                        "areaStyle" => (object)[
////                            "color" => 'rgb(0 159 253 / 49%)', // color of the background
//                            "opacity" => 0.1],
//                        "yAxisIndex" => count($historyArray) == 1 ? 0 : (in_array('irradiance', $historyArray) ? 0 : 1),
//                        "z" => 10,
//
//                    ]);
//                } else if ($current == "soc") {
////                    return $historyArray;
//
//                    $legendArray[] = "SOC";
//
//                    $historyObject = collect([
//
//                        "name" => "SOC",
//                        "type" => $graphType,
//                        "barCategoryGap" => '-130%',
//                        "smooth" => true,
//                        "silent" => true,
//                        "color" => $graphColor,
//                        "showSymbol" => false,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "data" => $todayLogData,
//                        "areaStyle" => (object)[
////                            "color" => 'rgb(96 91 244 / 49%)', // color of the background
//                            "opacity" => 0.1],
//                        "yAxisIndex" => count($historyArray) == 1 ? 0 : (in_array('soc', $historyArray) ? 1 : 0),
//                        "z" => 10,
//                    ]);
//                } else if ($current == "battery-power") {
//
//                    $legendArray[] = "Battery Power";
//
//                    $historyObject = collect([
//
//                        "name" => "Battery Power",
//                        "barCategoryGap" => '-130%',
//                        "type" => $graphType,
//                        "smooth" => true,
//                        "silent" => true,
//                        "color" => $graphColor,
//                        "showSymbol" => false,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "data" => $todayLogData,
//                        "areaStyle" => (object)[
////                            "color" => 'rgb(69 199 69 / 49%)', // color of the background
//                            "opacity" => 0.1],
//                        "yAxisIndex" => 0,
//                        "z" => 10,
//                    ]);
//                } else if ($current == "battery-charge") {
//
//
//                    $legendArray[] = "Battery Charge";
//                    $array2 = array_map(function ($value) {
//                        return (int)$value === 0 ? NULL : $value;
//                    }, $todayLogData);
////                    return $array2;
//
//                    $historyObject = collect([
//
//                        "name" => "Battery Charge",
//                        "barCategoryGap" => '-130%',
//                        "type" => $graphType,
//                        "smooth" => true,
//                        "silent" => true,
//                        "color" => $graphColor,
//                        "showSymbol" => false,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "data" => $array2,
//                        "areaStyle" => (object)[
////                            "color" => 'rgb(242 182 16 / 49%)', // color of the background
//                            "opacity" => 0.1],
//                        "yAxisIndex" => 0,
//                        "z" => 10,
//                    ]);
//                } else if ($current == "battery-discharge") {
//
//
//                    $array2 = array_map(function ($value) {
//                        return (int)$value === 0 ? NULL : $value;
//                    }, $todayLogData);
//
//                    $legendArray[] = "Battery Discharge";
//
//                    $historyObject = collect([
//
//                        "name" => "Battery Discharge",
//                        "type" => $graphType,
//                        "smooth" => true,
//                        "silent" => true,
//                        "color" => $graphColor,
//                        "barCategoryGap" => '-130%',
//                        "showSymbol" => false,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "data" => $array2,
//                        "areaStyle" => (object)[
////                            "color" => 'rgb(49 191 191 / 49%)', // color of the background
//                            "opacity" => 0.1],
//                        "yAxisIndex" => 0,
//                        "z" => 10,
//                    ]);
//                }
            } else if ($graphType == 'bar') {

                if ($current == "generation") {

                    $legendArray[] = "Generation";

                    $historyObject = collect([

                        "name" => "Generation",
                        "type" => $graphType,
                        "color" => $graphColor,
                        'barGap' => '0%',
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                }
//                else if ($current == "consumption") {
//
//                    $legendArray[] = "Consumption";
//
//                    $historyObject = collect([
//
//                        "name" => "Consumption",
//                        "type" => $graphType,
//                        "color" => $graphColor,
//                        'barGap' => '0%',
//                        "data" => $todayLogData,
//                        "yAxisIndex" => 0,
//                    ]);
//                } else if ($current == "grid") {
//
//                    $legendArray[] = "Grid";
//
//                    $historyObject = collect([
//
//                        "name" => "Grid",
//                        "type" => $graphType,
//                        "color" => $graphColor,
//                        'barGap' => '0%',
//                        "data" => $todayLogData,
//                        "yAxisIndex" => 0,
//                    ]);
//                } else if ($current == "buy") {
//
//                    $legendArray[] = "Buy";
//
//                    $historyObject = collect([
//
//                        "name" => "Buy",
//                        "type" => $graphType,
//                        "color" => $graphColor,
//                        'barGap' => '0%',
//                        "data" => $todayLogData,
//                        "yAxisIndex" => 0,
//                    ]);
//                } else if ($current == "sell") {
//
//                    $legendArray[] = "Sell";
//
//                    $historyObject = collect([
//
//                        "name" => "Sell",
//                        "type" => $graphType,
//                        "color" => $graphColor,
//                        'barGap' => '0%',
//                        "data" => $todayLogData,
//                        "yAxisIndex" => 0,
//                    ]);
//                } else if ($current == "irradiance") {
//
//                    $legendArray[] = "Irradiance";
//
//                    $historyObject = collect([
//
//                        "name" => "Irradiance",
//                        "type" => $graphType,
//                        "color" => $graphColor,
//                        'barGap' => '0%',
//                        "data" => $todayLogData,
//                        "yAxisIndex" => count($historyArray) == 1 ? 0 : 1,
//                    ]);
//                } else if ($current == "saving") {
//
//                    $legendArray[] = "Cost Saving";
//
//                    $historyObject = collect([
//
//                        "name" => "Cost Saving",
//                        "type" => $graphType,
//                        "color" => $graphColor,
//                        'barGap' => '0%',
//                        "data" => $todayLogData,
//                        "yAxisIndex" => count($historyArray) == 1 ? 0 : (in_array('irradiance', $historyArray) ? 0 : 1),
//                    ]);
//                } else if ($current == "battery-charge") {
//                    $legendArray[] = "Battery Charge";
//                    $historyObject = collect([
//
//                        "name" => "Battery Charge",
//                        "type" => $graphType,
//                        "smooth" => true,
//                        "silent" => true,
//                        "color" => $graphColor,
//                        "showSymbol" => false,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "data" => $todayLogData,
//                        "areaStyle" => (object)["color" => 'rgb(242 182 16 / 49%)', // color of the background
//                            "opacity" => 0.5],
//                        "yAxisIndex" => 0,
//                    ]);
//                } else if ($current == "battery-discharge") {
//
//                    $legendArray[] = "Battery Discharge";
//
//                    $historyObject = collect([
//
//                        "name" => "Battery Discharge",
//                        "type" => $graphType,
//                        "smooth" => true,
//                        "silent" => true,
//                        "color" => $graphColor,
//                        "showSymbol" => false,
//                        "emphasis" => (object)[
//                            "focus" => 'series'
//                        ],
//                        "data" => $todayLogData,
//                        "areaStyle" => (object)["color" => 'rgb(49 191 191 / 49%)', // color of the background
//                            "opacity" => 0.5],
//                        "yAxisIndex" => 0,
//                    ]);
//                }
            }

            $plantHistoryGraph[] = $historyObject;
        }

//        $plantHistoryGraph[] = $historyCloud;


        $data['time_type'] = $time;
        $data['time_details'] = [];
        if ($time == 'day') {
            $dateTimeArray = [];
//            for ($m = 0; $m < 22; $m++) {
//                if ($m < 10) {
//                    if ($m == 0) {
//                        $dateTimeArray[] = '0' . $m . ':00';
//                    } else if ($m == 10) {
//                        $m = $m + 1;
//                        $dateTimeArray[] = $m . ':00';
//                    } else {
//                        $m = $m + 1;
//                        $dateTimeArray[] = $m . ':00';
//                    }
//                } else {
//                    $m = $m + 1;
//                    $dateTimeArray[] = $m . ':00';
//                }
//
//            }
//            return $todayLogTime;

//            $data['time_data_array'] = $dateTimeArray;

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

            $data['time_data_array'] = $todayLogTime;
            $minus_1_hours = date('Y-m-d H:i:s', strtotime('-1 hours', strtotime(date('Y-m-d H:i:s'))));
            $weatherArrayData = [];
            $weatherTimeArray = [];
//            $oneHourDifferenceArray = array(date('Y-m-d 00:00'), date('Y-m-d 01:00'), date('Y-m-d 02:00'), date('Y-m-d 03:00'), date('Y-m-d 04:00'), date('Y-m-d 05:00'), date('Y-m-d 06:00'), date('Y-m-d 07:00'), date('Y-m-d 08:00'), date('Y-m-d 09:00'), date('Y-m-d 10:00'), date('Y-m-d 11:00'), date('Y-m-d 12:00'), date('Y-m-d 13:00'), date('Y-m-d 14:00'), date('Y-m-d 15:00'), date('Y-m-d 16:00'), date('Y-m-d 17:00'), date('Y-m-d 18:00'), date('Y-m-d 19:00'), date('Y-m-d 20:00'), date('Y-m-d 21:00'), date('Y-m-d 22:00'), date('Y-m-d 23:00'), date('Y-m-d 24:00'));
            $oneHourDifferenceArray = array(date('00:00'), date('01:00'), date('02:00'), date('03:00'), date('04:00'), date('05:00'), date('06:00'), date('07:00'), date('08:00'), date('09:00'), date('10:00'), date('11:00'), date('12:00'), date('13:00'), date('14:00'), date('15:00'), date('16:00'), date('Y-m-d 17:00'), date('Y-m-d 18:00'), date('Y-m-d 19:00'), date('20:00'), date('21:00'), date('22:00'), date('23:00'), date('24:00'));

//            for ($k = 0; $k < count($oneHourDifferenceArray); $k++) {
//                $weather = Weather::where('city', $plantData->city)->whereTime('created_at', $oneHourDifferenceArray[$k])->whereDate('created_at', date('Y-m-d'))->first();
//                if ($weather) {
//                    array_push($weatherArrayData, ['icon' => $weather->icon, 'time' => $oneHourDifferenceArray[$k]]);
////                    array_push($weatherArrayData,$oneHourDifferenceArray[$k]);
//                }
//                else
//                {
//                    array_push($weatherArrayData, ['icon' => '', 'time' => null]);
//                }
//            }
//            $weatherCloudsData = [];
//            for($j=0;$j<count($weatherArrayData);$j++)
//            {
//                $splitWeatherTimeBeforeIndex = explode(":", $weatherArrayData[$j]['time']);
//                if($j+1 < count($weatherArrayData)) {
//                    $splitWeatherTimeAfterIndex = explode(":", $weatherArrayData[$j + 1]['time']);
//                }
//                else
//                {
//                    $splitWeatherTimeAfterIndex = 0;
//                }
//                $timeDifference = (int)$splitWeatherTimeAfterIndex[0] - (int)$splitWeatherTimeBeforeIndex[0];
//                if($timeDifference > 0)
//                {
//                    $index = $j;
//                    for($h=0;$h<$timeDifference;$h++)
//                    {
//                        if($j+1 < count($weatherArrayData)) {
//                            $index = $index + 1;
//                            $inserted = array('icon' => '', 'time' => null);
//                            $weatherArrayData = $this->insert($weatherArrayData, $j + 1, $inserted);
//                        }
//                    }
//
////                    return $inserted;
////                    array_splice( $weatherArrayData, $j+1, 2, $inserted );
////                   return  $weatherCloudsData;
//                }
//
//
//            }
//            return $weatherArrayData;
//            return [$timeDifference,$splitWeatherTimeBeforeIndex,$splitWeatherTimeAfterIndex];
//            $data['time_array'] = $todayLogTime;
//            $arrayCounts = count($data['time_array']) - count($weatherArrayData);
//            for ($l = 0; $l < $arrayCounts; $l++) {
//                array_push($weatherArrayData, ['icon' => '', 'time' => null]);
//            }
//            for ($a = 0; $a < count($weatherArrayData); $a++) {
//                if ($weatherArrayData[$a]['time'] != null) {
//                        $defaultCloudsValue = 5;
//                        $symbol = 'image://http://openweathermap.org/img/w/' . $weatherArrayData[$a]['icon'] . '.png';
//                    } else {
//                        $defaultCloudsValue = null;
//                        $symbol = '';
//                    }
//                $cloudsData = [
//                    "value" => $defaultCloudsValue,
////                    "symbol" => 'path://M416 128c-.6 0-1.1.2-1.6.2 1.1-5.2 1.6-10.6 1.6-16.2 0-44.2-35.8-80-80-80-24.6 0-46.3 11.3-61 28.8C256.4 24.8 219.3 0 176 0 114.1 0 64 50.1 64 112c0 7.3.8 14.3 2.1 21.2C27.8 145.8 0 181.5 0 224c0 53 43 96 96 96h320c53 0 96-43 96-96s-43-96-96-96zm-32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-192 96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm128 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zM64 448c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32z',
//                    "symbol" => $symbol,
//                    "symbolSize" => [50, 50],
//
//                ];
//                array_push($cloudsDetailData, $cloudsData);
//            }
//            return [$cloudsDetailData,$weatherTimeArray];
//            if(count())

//            for ($m = 0; $m < count($weatherArrayData); $m++) {
//
//            }
            $data['time_array'] = $todayLogTime;
//            $arrayDifferenceData = array_diff($weatherArrayData,$data['time_array']);
//            return $arrayDifferenceData;
            $finalArrayTime = array('00:00', '02:00', '04:00', '06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00');
            $matchArraytime = [];
            for ($l = 0; $l < count($data['time_array']); $l++) {
                $explodeResult = explode(":", $data['time_array'][$l]);
                $dateTimeArray[] = $explodeResult[0] . ':00';
                if (in_array($explodeResult[0] . ':00', $oneHourDifferenceArray)) {
                    if (!in_array($explodeResult[0] . ':00', $matchArraytime)) {
                        $weather = Weather::where('city', $plantData->city)->whereTime('created_at', $explodeResult[0] . ':00')->whereDate('created_at', date('Y-m-d'))->first();
                        array_push($matchArraytime, $explodeResult[0] . ':00');
                        if ($weather) {
                            $defaultCloudsValue = 1;
                            $symbol = 'image://http://openweathermap.org/img/w/' . $weather['icon'] . '.png';
                        } else {
                            $defaultCloudsValue = null;
                            $symbol = '';
                        }
                    } else {
                        $defaultCloudsValue = null;
                        $symbol = '';
                    }

                } else {
                    $defaultCloudsValue = null;
                    $symbol = '';
                }
                $cloudsData = [
                    "value" => $defaultCloudsValue,
//                    "symbol" => 'path://M416 128c-.6 0-1.1.2-1.6.2 1.1-5.2 1.6-10.6 1.6-16.2 0-44.2-35.8-80-80-80-24.6 0-46.3 11.3-61 28.8C256.4 24.8 219.3 0 176 0 114.1 0 64 50.1 64 112c0 7.3.8 14.3 2.1 21.2C27.8 145.8 0 181.5 0 224c0 53 43 96 96 96h320c53 0 96-43 96-96s-43-96-96-96zm-32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-192 96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm128 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zM64 448c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32z',
                    "symbol" => $symbol,
                    "symbolSize" => [40, 40],

                ];
                array_push($cloudsDetailData, $cloudsData);
            }
//            for ($l = 0; $l < count($data['time_array']); $l++) {
//                $explodeResult = explode(":", $data['time_array'][$l]);
//                $dateTimeArray[] = $explodeResult[0] . ':00';
//            }
//            return $cloudsDetailData;
            $data['time_array'] = $dateTimeArray;
            $historyCloud = collect([

                "name" => "glyph",
                "type" => "pictorialBar",
                "symbolSize" => 20,
                "barGap" => '100%',
                "symbolPosition" => 'end',
                "symbolOffset" => [35, '-160'],
                "data" => $cloudsDetailData
            ]);
            array_push($plantHistoryGraph, $historyCloud);
            $data['plant_history_graph'] = $plantHistoryGraph;

//            return $data['time_array'];
        } else {

            $data['time_array'] = $todayLogTime;
//            return $data['time_array'];
        }
//        return $plantHistoryGraph;

        $date12 = date('Y-m', strtotime($date));
        $date13 = date('Y', strtotime($date));
//        return 'okkkkkkkkkkkkkkkkkkkk';
//        return [$data['time_array'],count($data['time_array'])];
        if ($time == 'day') {

            for ($i = 0; $i < count($data['time_data_array']); $i++) {
                array_push($data['time_details'], $date . ' ' . $data['time_data_array'][$i]);
            }
        } elseif ($time == 'month') {
            for ($i = 0; $i < count($data['time_array']); $i++) {
                array_push($data['time_details'], $date12 . '-' . $data['time_array'][$i]);
            }
        } else {
            for ($i = 0; $i < count($data['time_array']); $i++) {
                array_push($data['time_details'], $data['time_array'][$i] . '-' . $date13);
            }
        }
//        return $data['time_details'];


        $data['legend_array'] = $legendArray;
        $data['tooltip_date'] = $tooltipDate;


        if ($time == 'day') {
//            return 'okkkkkkkk' . json_encode($data['time_array']);
//            for ($m = 0; $m < 22; $m++) {
//                if ($m < 10) {
//                    if ($m == 0) {
//                        $todayLogTime[] = '0' . $m . ':00';
//                    } else if ($m == 10) {
//                        $m = $m + 1;
//                        $todayLogTime[] = $m . ':00';
//                    } else {
//                        $m = $m + 1;
//                        $todayLogTime[] = $m . ':00';
//                    }
//                } else {
//                    $m = $m + 1;
//                    $todayLogTime[] = $m . ':00';
//                }
//
//            }
////            return $todayLogTime;
//
//            $data['time_array'] = $todayLogTime;
            if (DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->exists()) {

                $dataResponse = DailyProcessedPlantDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->orderBy('created_at', 'DESC')->first();

                $totalGeneration = $dataResponse->dailyGeneration;
                $totalConsumption = $dataResponse->dailyConsumption;
                $totalGrid = $dataResponse->dailyGridPower;
                $totalBuy = $dataResponse->dailyBoughtEnergy;
                $totalSell = $dataResponse->dailySellEnergy;
                $totalSaving = $dataResponse->dailySaving;
                $totalChargeEnergy = $dataResponse->daily_charge_energy;
                $totalDischargeEnergy = $dataResponse->daily_discharge_energy;
            }

            if (DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->exists()) {

                $dataResponse = DailyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereDate('created_at', $date)->first();

                $totalIrradiation = $dataResponse->daily_irradiance;
                $data['total_irradiation'] = round($totalIrradiation, 2);
            }

        }
//        return $data['time_array'];

        if ($time == 'month') {
            $monthlyWeatherArray = [];

            $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);

            for ($i = 1; $i <= $dd; $i++) {
                $symbol = '';

                if ($i < 10) {
                    $i = '0' . $i;
                }
//                return $date . '-' . $i;
                $weather = DailyWeatherModel::where('city', $plantData->city)->whereDate('date', $date . '-' . $i)->first();
                $symbol = '';
                $defaultCloudsValue = null;
                if ($weather['icon']) {
//                    $symbol = $weather['icon'];
                    $defaultCloudsValue = 1;
                    $symbol = 'image://http://openweathermap.org/img/w/' . $weather['icon'] . '.png';
                }
//                return $weather;
//                for($q=0;$q<count($weather);$q++)
//                {
//                    array_push($monthlyWeatherArray,$weather[$q]['icon']);
//                }
//                $counts = array_count_values($monthlyWeatherArray);
//                arsort($counts);
//                $top_with_count = array_slice($counts, 0, 1, true);
//                $explodeData = explode('-', $date);
//                $mon = $explodeData[1];
//                $yer = $explodeData[0];
//                $top = array_keys($top_with_count);
//                if($top)
//                {
//                    $defaultCloudsValue = 50;
//                    $symbol = 'image://http://openweathermap.org/img/w/' . $top[0] . '.png';
//                }
//                else
//                {
//                    $defaultCloudsValue = null;
//                    $symbol = '';
//                }
                $cloudsData = [
                    "value" => $defaultCloudsValue,
//                    "symbol" => 'path://M416 128c-.6 0-1.1.2-1.6.2 1.1-5.2 1.6-10.6 1.6-16.2 0-44.2-35.8-80-80-80-24.6 0-46.3 11.3-61 28.8C256.4 24.8 219.3 0 176 0 114.1 0 64 50.1 64 112c0 7.3.8 14.3 2.1 21.2C27.8 145.8 0 181.5 0 224c0 53 43 96 96 96h320c53 0 96-43 96-96s-43-96-96-96zm-32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-192 96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm128 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zM64 448c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm64-96c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32z',
                    "symbol" => $symbol,
                    "symbolSize" => [40, 40],

                ];
                array_push($cloudsDetailData, $cloudsData);
            }
//            return $cloudsDetailData;
            $historyCloud = collect([

                "name" => "glyph",
                "type" => "pictorialBar",
                "symbolSize" => 20,
                "barGap" => '100%',
                "symbolPosition" => 'end',
                "symbolOffset" => [0, '-200'],
                "data" => $cloudsDetailData
            ]);
            array_push($plantHistoryGraph, $historyCloud);
            $data['plant_history_graph'] = $plantHistoryGraph;
            if (MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '%')->exists()) {

                $dataResponse = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '%')->first();

                $totalGeneration = $dataResponse->monthlyGeneration;
                $totalConsumption = $dataResponse->monthlyConsumption;
                $totalGrid = $dataResponse->monthlyGridPower;
                $totalBuy = $dataResponse->monthlyBoughtEnergy;
                $totalSell = $dataResponse->monthlySellEnergy;
                $totalSaving = $dataResponse->monthlySaving;
                $totalChargeEnergy = $dataResponse->monthly_charge_energy;
                $totalDischargeEnergy = $dataResponse->monthly_discharge_energy;
            }

            if (MonthlyProcessedPlantEMIDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '%')->exists()) {

                $dataResponse = MonthlyProcessedPlantEMIDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', $date . '%')->first();

                $totalIrradiation = $dataResponse->monthly_irradiance;
                $data['total_irradiation'] = round($totalIrradiation, 2);
            }
        }

        if ($time == 'year') {
            $data['plant_history_graph'] = $plantHistoryGraph;

            if (YearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->exists()) {

                $dataResponse = YearlyProcessedPlantDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->first();

                $totalGeneration = $dataResponse->yearlyGeneration;
                $totalConsumption = $dataResponse->yearlyConsumption;
                $totalGrid = $dataResponse->yearlyGridPower;
                $totalBuy = $dataResponse->yearlyBoughtEnergy;
                $totalSell = $dataResponse->yearlySellEnergy;
                $totalSaving = $dataResponse->yearlySaving;
                $totalChargeEnergy = $dataResponse->yearly_charge_energy;
                $totalDischargeEnergy = $dataResponse->yearly_discharge_energy;
            }

            if (YearlyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->exists()) {

                $dataResponse = YearlyProcessedPlantEMIDetail::where('plant_id', $plantID)->whereYear('created_at', $date)->first();

                $totalIrradiation = $dataResponse->yearly_irradiance;
                $data['total_irradiation'] = round($totalIrradiation, 2);
            }
        }

        $totalGenerationConverted = $this->unitConversion($totalGeneration, 'kWh');
        $totalConsumptionConverted = $this->unitConversion($totalConsumption, 'kWh');
        $totalGridConverted = $this->unitConversion($totalGrid, 'kWh');
        $totalBuyConverted = $this->unitConversion($totalBuy, 'kWh');
        $totalSellConverted = $this->unitConversion($totalSell, 'kWh');
        $totalSavingConverted = $this->unitConversion($totalSaving, 'PKR');
        $totalChargedEnergy = $this->unitConversion($totalChargeEnergy, 'kWh');
        $totalDischargedEnergy = $this->unitConversion($totalDischargeEnergy, 'kWh');

        $data['total_generation'] = round($totalGenerationConverted[0], 2) . ' ' . $totalGenerationConverted[1];
        $data['total_charge'] = round($totalChargedEnergy[0], 2) . ' ' . $totalChargedEnergy[1];
        $data['total_discharge'] = round($totalGenerationConverted[0], 2) . ' ' . $totalGenerationConverted[1];
        $data['total_consumption'] = round($totalDischargedEnergy[0], 2) . ' ' . $totalDischargedEnergy[1];
        $data['total_grid'] = round($totalGridConverted[0], 2) . ' ' . $totalGridConverted[1];
        $data['total_buy'] = round($totalBuyConverted[0], 2) . ' ' . $totalBuyConverted[1];
        $data['total_sell'] = round($totalSellConverted[0], 2) . ' ' . $totalSellConverted[1];
        $data['total_saving'] = round($totalSavingConverted[0], 2) . ' ' . $totalSavingConverted[1];
        $data['y_axis_array'] = $plantHistoryGraphYAxis;
//        return $data['time_array'];

        return $data;
    }

    public function plantENVGraph(Request $request)
    {

        $time = $request->time;
        $id = $request->id;
        $id = (int)$id;
        $dates = strtotime($request->date);

        if ($time == 'day') {

            $date = date('Y-m-d', $dates);
            if ($id != 0) {
                $dailyGene = DailyProcessedPlantDetail::where('plant_id', $id)->whereDate('created_at', $date)->sum('dailyGeneration');
            } else {
                $dailyGene = DailyProcessedPlantDetail::whereDate('created_at', $date)->sum('dailyGeneration');
            }

            $dailyGene = $dailyGene ? $dailyGene : 0;
            $dailyGeneration = $dailyGene ? $this->unitConversion((double)$dailyGene, 'kWh') : [0, 'kWh'];

            return [round($dailyGeneration[0], 2) . ' ' . $dailyGeneration[1], $dailyGene];
        } else if ($time == 'month') {

            $date = date('Y-m', $dates);
            if ($id != 0) {
                $monthlyGene = MonthlyProcessedPlantDetail::where('plant_id', $id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGeneration');
            } else {
                $monthlyGene = MonthlyProcessedPlantDetail::where('created_at', 'LIKE', $date . '%')->sum('monthlyGeneration');
            }

            $monthlyGene = $monthlyGene ? $monthlyGene : 0;
            $monthlyGeneration = $monthlyGene ? $this->unitConversion((double)$monthlyGene, 'kWh') : [0, 'kWh'];

            return [round($monthlyGeneration[0], 2) . ' ' . $monthlyGeneration[1], $monthlyGene];
        } else if ($time == 'year') {

            $date = $request->date;
            if ($id != 0) {
                $yearlyGene = YearlyProcessedPlantDetail::where('plant_id', $id)->where('created_at', 'LIKE', $date . '%')->sum('yearlyGeneration');
            } else {
                $yearlyGene = YearlyProcessedPlantDetail::where('created_at', 'LIKE', $date . '%')->sum('yearlyGeneration');
            }

            $yearlyGene = $yearlyGene ? $yearlyGene : 0;
            $yearlyGeneration = $yearlyGene ? $this->unitConversion((double)$yearlyGene, 'kWh') : [0, 'kWh'];

            return [round($yearlyGeneration[0], 2) . ' ' . $yearlyGeneration[1], $yearlyGene];
        }

    }

    function insert($array, $index, $val)
    { //function decleration
        $temp = array(); // this temp array will hold the value
        $size = count($array); //because I am going to use this more than one time
        // Validation -- validate if index value is proper (you can omit this part)
        if (!is_int($index) || $index < 0 || $index > $size) {
            echo "Error: Wrong index at Insert. Index: " . $index . " Current Size: " . $size;
            echo "<br/>";
            return false;
        }
        //here is the actual insertion code
        //slice part of the array from 0 to insertion index
        $temp = array_slice($array, 0, $index);//e.g index=5, then slice will result elements [0-4]
        //add the value at the end of the temp array// at the insertion index e.g 5
        array_push($temp, $val);
        //reconnect the remaining part of the array to the current temp
        $temp = array_merge($temp, array_slice($array, $index, $size));
        $array = $temp;//swap// no need for this if you pass the array cuz you can simply return $temp, but, if u r using a class array for example, this is useful.

        return $array; // you can return $temp instead if you don't use class array
    }

    public function plantEMIGraph(Request $request)
    {

        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;
        $plantEMIGraphYAxis = [];

        $date = date('Y-m-d', $requestDate);

        $emiArray = json_decode($request->emiCheckBoxArray);

        if (in_array("pv_temperature", $emiArray) || in_array("ambient_temperature", $emiArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => '°C',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#ffffff',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantEMIGraphYAxis[] = $yAxisObject;
        }

        if (in_array("wind_speed", $emiArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'm/s',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#ffffff',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantEMIGraphYAxis[] = $yAxisObject;
        }

        if (in_array("irradiance", $emiArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'W/m' . json_decode('"\u00B2"'),
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#ffffff',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantEMIGraphYAxis[] = $yAxisObject;
        }

        $plantEMIGraph = [];
        $legendArray = [];
        $graphType = '';
        $tooltipDate = date('Y-m-d');
        $currentEMI = [];

        if (strtotime($date) == strtotime(date('Y-m-d'))) {

            $currentDataLogTime = InverterEMIDetail::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->exists() ? InverterEMIDetail::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
            $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
            $currentEMI = InverterEMIDetail::where('plant_id', $plantID)->whereBetween('collect_time', [date($date . ' 00:00:00'), $finalCurrentDataDateTime])->groupBy('collect_time')->get();
        } else {

            $currentEMI = InverterEMIDetail::where('plant_id', $plantID)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])->groupBy('collect_time')->get();
        }

        foreach ($emiArray as $key => $current) {

            $todayLogData = [];
            $todayLogTime = [];
            $todayLogDataSum = 0;
            $graphColor = '';

            $tooltipDate = date('d-m-Y', strtotime($date));

            $graphType = 'line';

            foreach ($currentEMI as $key => $todayLog) {

                $todayLogTime[] = date('H:i', strtotime($todayLog->collect_time));

                if ($current == "pv_temperature") {

                    $graphColor = '#09DEB9';
                    $todayLogDataSum = $todayLog->pv_temperature;
                } else if ($current == "ambient_temperature") {

                    $graphColor = '#44F656';
                    $todayLogDataSum = $todayLog->temperature;
                } else if ($current == "irradiance") {

                    $graphColor = '#F933C8';
                    $todayLogDataSum = $todayLog->radiant_line;
                } else if ($current == "wind_speed") {

                    $graphColor = '#FF1E45';
                    $todayLogDataSum = $todayLog->wind_speed;
                }

                $todayLogData[] = round($todayLogDataSum, 2);
            }

            if ($graphType == 'line') {

                if ($current == "pv_temperature") {

                    $legendArray[] = "PV Temperature";

                    $emiObject = collect([

                        "name" => "PV Temperature",
                        "type" => $graphType,
                        "smooth" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "ambient_temperature") {

                    $legendArray[] = "Ambient Temperature";

                    $emiObject = collect([

                        "name" => "Ambient Temperature",
                        "type" => $graphType,
                        "smooth" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "irradiance") {

                    $legendArray[] = "Irradiance";

                    $emiObject = collect([

                        "name" => "Irradiance",
                        "type" => $graphType,
                        "smooth" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "data" => $todayLogData,
                        "yAxisIndex" => count($emiArray) == 1 ? 0 : 1,
                    ]);
                } else if ($current == "wind_speed") {

                    $legendArray[] = "Wind Speed";

                    $emiObject = collect([

                        "name" => "Wind Speed",
                        "type" => $graphType,
                        "smooth" => true,
                        "color" => $graphColor,
                        "showSymbol" => false,
                        "data" => $todayLogData,
                        "yAxisIndex" => count($emiArray) == 1 ? 0 : (in_array('irradiance', $emiArray) ? 0 : 1),
                    ]);
                }
            }

            $plantEMiGraph[] = $emiObject;
        }

        $data['plant_emi_graph'] = $plantEMiGraph;

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

        $data['time_array'] = $todayLogTime;

        $tooltipDate = date('d-m-Y', strtotime($date));

        $data['legend_array'] = $legendArray;
        $data['tooltip_date'] = $tooltipDate;
        $data['y_axis_array'] = $plantEMIGraphYAxis;

        return $data;
    }

    public function plantAlertGraph(Request $request)
    {

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;

        if ($time == 'day') {
            $date = date('Y-m-d', $requestDate);
        } else if ($time == 'month') {
            $date = date('Y-m', $requestDate);
        } else if ($time == 'year') {
            $date = $request->date;
        }

        $alertArray = ['Alarm', 'Fault'];

        $plantAlertGraph = [];
        $legendArray = [];
        $alarmValue = 0;
        $faultValue = 0;
        $value = 0;

        foreach ($alertArray as $key => $current) {

            $legendArray[] = $current;

            if ($time == 'day') {

                if ($current == 'Alarm') {

                    $alarmValue = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
                        ->select('fault_alarm_log.*')
                        ->where('fault_and_alarms.type', 'Alarm')
                        ->where('fault_alarm_log.plant_id', $plantID)
                        ->whereDate('fault_alarm_log.created_at', $date)
                        ->count();

                    ${"file" . $key} = collect([
                        "value" => $alarmValue,
                        "name" => $current,
                    ]);

                    $plantAlertGraph[] = ${"file" . $key};
                } else if ($current == 'Fault') {

                    $faultValue = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
                        ->select('fault_alarm_log.*')
                        ->where('fault_and_alarms.type', 'Fault')
                        ->where('fault_alarm_log.plant_id', $plantID)
                        ->whereDate('fault_alarm_log.created_at', $date)
                        ->count();

                    ${"file" . $key} = collect([
                        "value" => $faultValue,
                        "name" => $current,
                    ]);

                    $plantAlertGraph[] = ${"file" . $key};
                }

                $value = $alarmValue + $faultValue;
            } else if ($time == 'month') {

                if ($current == 'Alarm') {

                    $alarmValue = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
                        ->select('fault_alarm_log.*')
                        ->where('fault_and_alarms.type', 'Alarm')
                        ->where('fault_alarm_log.plant_id', $plantID)
                        ->where('fault_alarm_log.created_at', 'LIKE', $date . '-%')
                        ->count();

                    ${"file" . $key} = collect([
                        "value" => $alarmValue,
                        "name" => $current,
                    ]);

                    $plantAlertGraph[] = ${"file" . $key};
                } else if ($current == 'Fault') {

                    $faultValue = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
                        ->select('fault_alarm_log.*')
                        ->where('fault_and_alarms.type', 'Fault')
                        ->where('fault_alarm_log.plant_id', $plantID)
                        ->where('fault_alarm_log.created_at', 'LIKE', $date . '-%')
                        ->count();

                    ${"file" . $key} = collect([
                        "value" => $faultValue,
                        "name" => $current,
                    ]);

                    $plantAlertGraph[] = ${"file" . $key};
                }

                $value = $alarmValue + $faultValue;
            } else if ($time == 'year') {

                if ($current == 'Alarm') {

                    $alarmValue = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
                        ->select('fault_alarm_log.*')
                        ->where('fault_and_alarms.type', 'Alarm')
                        ->where('fault_alarm_log.plant_id', $plantID)
                        ->whereYear('fault_alarm_log.created_at', $date)
                        ->count();

                    ${"file" . $key} = collect([
                        "value" => $alarmValue,
                        "name" => $current,
                    ]);

                    $plantAlertGraph[] = ${"file" . $key};
                } else if ($current == 'Fault') {

                    $faultValue = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
                        ->select('fault_alarm_log.*')
                        ->where('fault_and_alarms.type', 'Fault')
                        ->where('fault_alarm_log.plant_id', $plantID)
                        ->whereYear('fault_alarm_log.created_at', $date)
                        ->count();

                    ${"file" . $key} = collect([
                        "value" => $faultValue,
                        "name" => $current,
                    ]);

                    $plantAlertGraph[] = ${"file" . $key};
                }

                $value = $alarmValue + $faultValue;
            }
        }

        $fileNull = collect([
            "value" => $value,
            "name" => null,
            "itemStyle" => [
                "opacity" => 0
            ],
            "tooltip" => [
                "show" => false
            ]
        ]);

        $plantAlertGraph[] = $fileNull;

        $data['plant_alert_graph'] = $plantAlertGraph;
        $data['total_value'] = $value;
        $data['alarm_value'] = $alarmValue;
        $data['fault_value'] = $faultValue;
        $data['legend_array'] = $legendArray;

        return $data;
    }

    public function plantInverterGraph(Request $request)
    {

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $tooltipDate = strtotime($request->date);
        $serialNo = $request->serialNo;
        $plantID = $request->plantID;

        $plantInverterGraph = [];
        $legendArray = [];
        $plantInverterGraphYAxis = [];

        if ($time == 'day') {

            $date = date('Y-m-d', $requestDate);
            $tooltipDate = date('d-m-Y', strtotime($date));
        }

        $meterTypeValue = '1';

        if (Plant::where('id', $plantID)->first()->meter_type == 'Solis') {

            $meterTypeValue = 'Inverter';
        } else {

            $meterTypeValue = '1';
        }

        $plantAllInvertersArray = SiteInverterDetail::where('plant_id', $plantID)->where('dv_inverter_type', $meterTypeValue)->get();

        $inverterArray = $request->inverterArray;

        if (in_array("output_power", $inverterArray) || in_array("dc_power", $inverterArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'kW',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#fff',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantInverterGraphYAxis[] = $yAxisObject;
        }

        if (in_array("normalize_power", $inverterArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => '%',
                "max" => 100,
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#fff',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantInverterGraphYAxis[] = $yAxisObject;
        }

        $currentDataLogTime = InverterDetail::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->exists() ? InverterDetail::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');

        foreach ($inverterArray as $key1 => $inv) {

            foreach ($plantAllInvertersArray as $key => $invArray) {

                $nameString = '';

                if ($inv == 'output_power') {

                    $legendArray[] = $nameString = 'Output Power-' . $invArray->dv_inverter_serial_no;
                } else if ($inv == 'dc_power') {

                    $legendArray[] = $nameString = 'DC Power-' . $invArray->dv_inverter_serial_no;
                } else if ($inv == 'normalize_power') {

                    $legendArray[] = $nameString = 'Normalize Power-' . $invArray->dv_inverter_serial_no;
                }

                $todayLogData = [];
                $todayLogTime = [];
                $todayLogDataSum = 0;
                $graphColor = '';
                $graphType = 'line';

                if (strtotime($date) == strtotime(date('Y-m-d'))) {

                    $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
                    $currentGeneration = InverterDetail::where('plant_id', $plantID)->where('dv_inverter', $invArray->dv_inverter)->whereBetween('collect_time', [date($date . ' 00:00:00'), $finalCurrentDataDateTime])->groupBy('collect_time')->get();
                } else {

                    $currentGeneration = InverterDetail::where('plant_id', $plantID)->where('dv_inverter', $invArray->dv_inverter)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])->groupBy('collect_time')->get();
                }

                foreach ($currentGeneration as $key => $todayLog) {

                    $todayLogTime[] = date('H:i', strtotime($todayLog->collect_time));

                    if ($inv == 'output_power') {

                        $graphColor = '#fff';
                        $todayLogDataSum = $todayLog->inverterPower;
                    } else if ($inv == 'dc_power') {

                        $graphColor = '#fff';
                        $todayLogDataSum = $todayLog->mpptPower;
                    } else if ($inv == 'normalize_power') {

                        $graphColor = '#fff';
                        $todayLogDataSum = ($todayLog->inverterPower / $invArray->dv_installed_dc_power) * 100;
                    }

                    $todayLogData[] = round($todayLogDataSum, 2);
                }

                if ($graphType == 'line') {

                    if ($inv == 'output_power' || $inv == 'dc_power') {

                        $inverterObject = collect([

                            "name" => $nameString,
                            "type" => $graphType,
                            "smooth" => true,
                            //"color"=> $graphColor,
                            "showSymbol" => false,
                            "data" => $todayLogData,
                            'yAxisIndex' => 0
                        ]);
                    }
                    if ($inv == 'normalize_power') {

                        $inverterObject = collect([

                            "name" => $nameString,
                            "type" => $graphType,
                            "smooth" => true,
                            //"color"=> $graphColor,
                            "showSymbol" => false,
                            "data" => $todayLogData,
                            'yAxisIndex' => count($inverterArray) == 1 ? 0 : 1,
                        ]);
                    }
                }

                $plantInverterGraph[] = $inverterObject;
            }
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

        $data['time_array'] = $todayLogTime;

        $data['plant_inverter_graph'] = $plantInverterGraph;
        $data['time_type'] = $time;
        $data['time_array'] = $todayLogTime;
        $data['legend_array'] = $legendArray;
        $data['y_axis_array'] = $plantInverterGraphYAxis;
        $data['tooltip_date'] = $tooltipDate;

        return $data;
    }

    public function plantPVGraph(Request $request)
    {

        $requestDate = strtotime($request->date);
        $tooltipDate = strtotime($request->date);
        $serialNo = $request->serialNo;
        $plantID = $request->plantID;
        $pvNumberArray = $request->pvValuesArray;
        $pvArray = $request->pvCheckBoxArray;


        $date = date('Y-m-d', $requestDate);
        $tooltipDate = date('d-m-Y', strtotime($date));

        $plantPVGraph = [];
        $legendArray = [];
        $plantPVGraphYAxis = [];

        if (in_array("pv_current", $pvArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'A',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#fff',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantPVGraphYAxis[] = $yAxisObject;
        }

        if (in_array("pv_voltage", $pvArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'V',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#fff',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantPVGraphYAxis[] = $yAxisObject;
        }

        if (in_array("pv_power", $pvArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'kW',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'transparent'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => '#fff',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantPVGraphYAxis[] = $yAxisObject;
        }

        $inverterMPPTGraphData = [];

        if (strtotime($date) == strtotime(date('Y-m-d'))) {

            $currentDataLogTime = InverterMPPTDetail::where(['plant_id' => $plantID, 'dv_inverter' => $serialNo])->whereDate('collect_time', date('Y-m-d'))->exists() ? InverterMPPTDetail::where(['plant_id' => $plantID, 'dv_inverter' => $serialNo])->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC')->first()->collect_time : date('Y-m-d 00:10:00');
            $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
            $inverterMPPTGraphData = InverterMPPTDetail::where(['plant_id' => $plantID, 'dv_inverter' => $serialNo])->whereIn('mppt_number', $pvNumberArray)->whereDate('collect_time', $date)->whereBetween('collect_time', [date($date . ' 00:00:00'), $finalCurrentDataDateTime])->exists() ? InverterMPPTDetail::where(['plant_id' => $plantID, 'dv_inverter' => $serialNo])->whereIn('mppt_number', $pvNumberArray)->whereBetween('collect_time', [date($date . ' 00:00:00'), $finalCurrentDataDateTime])->get() : [];
        } else {

            $inverterMPPTGraphData = InverterMPPTDetail::where(['plant_id' => $plantID, 'dv_inverter' => $serialNo])->whereIn('mppt_number', $pvNumberArray)->whereDate('collect_time', $date)->exists() ? InverterMPPTDetail::where(['plant_id' => $plantID, 'dv_inverter' => $serialNo])->whereIn('mppt_number', $pvNumberArray)->whereDate('collect_time', $date)->get() : [];
        }

        foreach ($pvNumberArray as $key1 => $pvNumber) {

            foreach ($pvArray as $key2 => $pvA) {

                $todayLogData = array();
                $todayLogTime = array();
                $todayLogDataSum = 0;
                $graphColor = '';
                $graphType = 'line';

                $nameString = '';

                if ($pvA == 'pv_current') {

                    $legendArray[] = $nameString = 'PV' . $pvNumber . ' Current(A)';

                    foreach ($inverterMPPTGraphData as $key => $graphData) {

                        if ($graphData->mppt_number == $pvNumber) {

                            //if($graphData->mppt_current > 0) {

                            $todayLogTime[] = date('H:i', strtotime($graphData->collect_time));
                            $todayLogDataSum = $graphData->mppt_current;
                            $todayLogData[] = round($todayLogDataSum, 2);
                            //}
                        }
                    }
                } else if ($pvA == 'pv_voltage') {

                    $legendArray[] = $nameString = 'PV' . $pvNumber . ' Voltage(V)';

                    foreach ($inverterMPPTGraphData as $key => $graphData) {

                        if ($graphData->mppt_number == $pvNumber) {

                            //if($graphData->mppt_voltage > 0) {

                            $todayLogTime[] = date('H:i', strtotime($graphData->collect_time));
                            $todayLogDataSum = $graphData->mppt_voltage;
                            $todayLogData[] = round($todayLogDataSum, 2);
                            //}
                        }
                    }
                } else if ($pvA == 'pv_power') {

                    $legendArray[] = $nameString = 'PV' . $pvNumber . ' Power(kW)';

                    foreach ($inverterMPPTGraphData as $key => $graphData) {

                        if ($graphData->mppt_number == $pvNumber) {

                            //if($graphData->mppt_power > 0) {

                            $todayLogTime[] = date('H:i', strtotime($graphData->collect_time));
                            $todayLogDataSum = $graphData->mppt_power;
                            $todayLogData[] = round($todayLogDataSum, 2);
                            //}
                        }
                    }
                }

                if ($graphType == 'line') {

                    if ($pvA == 'pv_current') {

                        $pvObject = collect([

                            "name" => $nameString,
                            "type" => $graphType,
                            "smooth" => true,
                            //"color"=> $graphColor,
                            "showSymbol" => false,
                            "data" => $todayLogData,
                            'yAxisIndex' => 0
                        ]);
                    } else if ($pvA == 'pv_voltage') {

                        $pvObject = collect([

                            "name" => $nameString,
                            "type" => $graphType,
                            "smooth" => true,
                            //"color"=> $graphColor,
                            "showSymbol" => false,
                            "data" => $todayLogData,
                            'yAxisIndex' => count($pvArray) == 1 ? 0 : (in_array("pv_power", $pvArray) ? 0 : 1),
                        ]);
                    } else if ($pvA == 'pv_power') {

                        $pvObject = collect([

                            "name" => $nameString,
                            "type" => $graphType,
                            "smooth" => true,
                            //"color"=> $graphColor,
                            "showSymbol" => false,
                            "data" => $todayLogData,
                            'yAxisIndex' => count($pvArray) == 1 ? 0 : 1,
                        ]);
                    }

                    $plantPVGraph[] = $pvObject;
                }
            }
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

        $data['time_array'] = $todayLogTime;

        $data['plant_pv_graph'] = $plantPVGraph;
        $data['time_array'] = $todayLogTime;
        $data['legend_array'] = $legendArray;
        $data['y_axis_array'] = $plantPVGraphYAxis;
        $data['tooltip_date'] = $tooltipDate;
        $data['pv_array'] = $tooltipDate;

        return $data;

    }

    public function buildPlant()
    {
        $companies = [];

        if (Auth::user()->roles == 1) {

            $companies = Company::all();
        } else if (Auth::user()->roles == 3) {

            $companies = Company::where('id', Auth::user()->company_id)->get();
        } else if (Auth::user()->roles != 1 || Auth::user()->roles != 3) {

            return redirect()->back()->with('error', 'You have no access of build plant!');
        }

        $plantMeterType = PlantMeterType::where('is_active', 'Y')->get();
        $system_type = SystemType::all();
        $plant_type = PlantType::all();
        $plants = PlantSite::select('site_id')->get();
        $plant_site_exist = array();
        foreach ($plants as $key => $plant) {
            array_push($plant_site_exist, $plant->site_id);
        }

        $is_build = 1;

        $data = [

            'companies' => $companies,
            'plants' => $plant_site_exist,
            'system_types' => $system_type,
            'plant_types' => $plant_type,
            'is_build' => $is_build,
            'plantMeterType' => $plantMeterType
        ];

        return view('admin.plant.build-plant', $data);
    }

    public function getSiteIDs(Request $request)
    {

        $data = $_POST['data'];

        $data = json_decode($data, true);

        if ($data['vendor'] == 'SunGrow') {

            $plantsArray = Plant::whereIn('meter_type', ['SunGrow'])->pluck('id')->toArray();
            $plantOccupiedSitesArray = PlantSite::whereIn('plant_id', $plantsArray)->pluck('site_id')->toArray();
//            return [$data['appkey'], $data['user_account'], $data['user_password']];
            $sunGrowController = new SunGrowController();
            $tokenAndUserIDResponse = $sunGrowController->getTokenAndUserID($data['appkey'], $data['user_account'], $data['user_password']);
//            return json_encode($tokenAndUserIDResponse);
            $plantListResponse = $sunGrowController->getPlantList($data['appkey'], $tokenAndUserIDResponse[0], $tokenAndUserIDResponse[1]);

            foreach ($plantListResponse as $key => $site) {

                if (in_array($site->ps_id, $plantOccupiedSitesArray)) {

                    unset($plantListResponse[$key]);
                }
            }

            return json_encode($plantListResponse);
        } else if ($data['vendor'] == 'Huawei') {

            $plantsArray = Plant::whereIn('meter_type', ['Huawei'])->pluck('id')->toArray();
//            return $plantsArray;
            $plantOccupiedSitesArray = PlantSite::whereIn('plant_id', $plantsArray)->pluck('site_id')->toArray();

            $huaweiAPIBaseURL = Setting::where('perimeter', 'huawei_api_base_url')->exists() ? Setting::where('perimeter', 'huawei_api_base_url')->first()->value : '';

            $huaweiController = new HuaweiController();
            $tokenSessionData = array();

            $plantListResponse = (object)['failCode' => 305];

            $tokenSessionDataResponse = $huaweiController->getTokenAndSessionID($huaweiAPIBaseURL, $data['username'], $data['system_code'], 'BUILD_PLANT');

            if (isset($tokenSessionDataResponse[0]->failCode) && ($tokenSessionDataResponse[0]->failCode == 20001)) {

                return response()->json(['errorStatus' => 1]);
            }

            $tokenSessionData[] = $tokenSessionDataResponse[1];
            $tokenSessionData[] = $tokenSessionDataResponse[2];

            $plantListResponse = $huaweiController->getPlantList($huaweiAPIBaseURL, $tokenSessionData);

            if (isset($plantListResponse->failCode) && ($plantListResponse->failCode == 305 || $plantListResponse->failCode == 306)) {

                return response()->json(['cookieError' => 1]);
            }

            $plantListResponse = isset($plantListResponse->data) ? $plantListResponse->data : [];
//            return ['siteData' => $plantListResponse, 'plantSiteData' => $plantOccupiedSitesArray];
            foreach ($plantListResponse as $key => $site) {
                if (in_array($site->stationCode, $plantOccupiedSitesArray)) {
                    unset($plantListResponse[$key]);
                }
            }

            return json_encode($plantListResponse);
        } else if ($data['vendor'] == 'Solis') {

            $solisAPIBaseURL = Setting::where('perimeter', 'solis_api_base_url')->exists() ? Setting::where('perimeter', 'solis_api_base_url')->first()->value : '';

            $plantsArray = Plant::where('meter_type', 'Solis')->pluck('id')->toArray();
            $plantOccupiedSitesArray = PlantSite::whereIn('plant_id', $plantsArray)->pluck('site_id')->toArray();

            $passwordHash = hash('sha256', $data['password']);

            $solisController = new SolisController();
            $orgTokenResponse = $solisController->getOrgToken($solisAPIBaseURL, $data['app_id'], $data['app_secret'], $data['username'], $data['password'], $data['org_id']);
//            $orgIDResponse = $solisController->getOrgID($solisAPIBaseURL, $orgTokenResponse);
//            return $orgIDResponse;
//            $tokenResponse = $solisController->getToken($solisAPIBaseURL, $data['app_id'], $data['app_secret'], $data['username'], $passwordHash, $orgIDResponse);
            $plantListResponse = $solisController->getPlantList($solisAPIBaseURL, $orgTokenResponse);

            if (isset($plantListResponse) && isset($plantListResponse->stationList)) {

                foreach ($plantListResponse->stationList as $key => $site) {

                    if (in_array($site->id, $plantOccupiedSitesArray)) {

                        unset($plantListResponse->stationList[$key]);
                    }
                }
            }

            return json_encode($plantListResponse->stationList);
        }
    }

    public function getSiteInverters(Request $request)
    {

        $data = $_POST['data'];

        $data = json_decode($data, true);

        if ($data['vendor'] == 'SunGrow') {

            $sunGrowController = new SunGrowController();
            $tokenAndUserIDResponse = $sunGrowController->getTokenAndUserID($data['appkey'], $data['user_account'], $data['user_password']);
            $inverterListResponse = $sunGrowController->getSiteDeviceList($data['appkey'], $tokenAndUserIDResponse[0], $data['site_id']);

            return json_encode($inverterListResponse);
        } else if ($data['vendor'] == 'Huawei') {

            $huaweiAPIBaseURL = Setting::where('perimeter', 'huawei_api_base_url')->exists() ? Setting::where('perimeter', 'huawei_api_base_url')->first()->value : '';

            $huaweiController = new HuaweiController();
            $tokenSessionData = array();

            $plantListResponse = (object)['failCode' => 305];

            $tokenSessionDataResponse = $huaweiController->getTokenAndSessionID($huaweiAPIBaseURL, $data['username'], $data['system_code'], 'BUILD_PLANT');

            if (isset($tokenSessionDataResponse[0]->failCode) && ($tokenSessionDataResponse[0]->failCode == 20001)) {

                return response()->json(['errorStatus' => 1]);
            }

            $tokenSessionData[] = $tokenSessionDataResponse[1];
            $tokenSessionData[] = $tokenSessionDataResponse[2];

            $deviceListResponse = $huaweiController->getSiteDeviceList($huaweiAPIBaseURL, $tokenSessionData, $data['site_id']);

            if (isset($deviceListResponse->failCode) && ($deviceListResponse->failCode == 305 || $deviceListResponse->failCode == 306)) {

                return response()->json(['cookieError' => 1]);
            }

            $deviceListResponse = isset($deviceListResponse->data) ? $deviceListResponse->data : [];

            return json_encode($deviceListResponse);
        } else if ($data['vendor'] == 'Solis') {

            $solisAPIBaseURL = Setting::where('perimeter', 'solis_api_base_url')->exists() ? Setting::where('perimeter', 'solis_api_base_url')->first()->value : '';

            $solisController = new SolisController();
            $tokenResponse = $solisController->getToken($solisAPIBaseURL, $data['app_id'], $data['app_secret'], $data['username'], $data['password'], $data['org_id']);
            $inverterListResponse = $solisController->getSiteDeviceList($solisAPIBaseURL, $tokenResponse, $data['site_id']);

            return json_encode($inverterListResponse);
        }
    }

    public function buildPlantLatLong(Request $request)
    {

        $site_array = $request->site_id_arr;

        $token = '';
        $lat_arr = [];
        $long_arr = [];
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

        if ($token) {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://67.23.248.117:8089/api/sites/list?size=10000&startIndex=0&sortProperty&sortOrder&isOnline",
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

                    foreach ($site_array as $site_arr) {

                        if ($plant_data->siteId == $site_arr) {

                            $lat_arr[] = $plant_data->lat;
                            $long_arr[] = $plant_data->long;
                        }
                    }

                }
            }
        }

        return [$lat_arr, $long_arr];
    }

    public function storePlant(Request $request)
    {
        $meterType = '';

        if ($request->plant_meter_type == 3) {

            $meterType = 'Huawei';
        } else if ($request->plant_meter_type == 4) {

            $meterType = 'SunGrow';
        } else if ($request->plant_meter_type == 5) {

            $meterType = 'Solis';
        }

        $plant = new Plant();

        $plant->company_id = $request->company_id;
        $plant->plant_name = $request->plant_name;
        $plant->timezone = $request->timezone;
        $plant->phone = $request->phone;
        $plant->location = $request->location;
        $plant->loc_lat = $request->loc_lat;
        $plant->loc_long = $request->loc_long;
        $plant->city = $request->city;
        $plant->province = $request->province;
        $plant->phone = $request->phone;
        $plant->capacity = $request->capacity;
        $plant->benchmark_price = $request->benchmark_price;
        $plant->plant_type = $request->plant_type;
        $plant->system_type = $request->system_type;
        $plant->meter_type = $meterType;
        $plant->meter_serial_no = $request->meter_serial_no;
        $plant->ratio_factor = $request->ratio_factor;
        $plant->angle = $request->angle;
        $plant->net_meter_date = $request->plant_net_meter_date;
        $plant->build_date = $request->plant_build_date;
        $plant->data_collect_date = $request->plant_build_date;
        $plant->azimuth = $request->azimuth;
        $plant->expected_generation = $request->expected_generation;
        $plant->daily_expected_saving = (double)($request->expected_generation) * (double)($request->benchmark_price);
        $plant->api_key = $request->led_api_key;

        if ($request->has('plant_has_emi')) {

            $plant->plant_has_emi = 'Y';
        }
        if ($request->system_type == 2) {

            if ($request->meter_type == 'present') {

                $plant->plant_has_grid_meter = 'Y';
            } else {

                $plant->plant_has_grid_meter = 'N';
            }
        }

        $plant->created_by = Auth::user()->id;
        $plant->location = $request->location;
        $plant->yearly_expected_generation = $request->expected_generation * 365;

        $plant_pic = '';

        if ($files = $request->file('plant_pic')) {

            $plant_pic = date("dmyHis.") . gettimeofday()["usec"] . '_' . $files->getClientOriginalName();
            $files->move(public_path('plant_photo'), $plant_pic);
            $plant->plant_pic = $plant_pic;
        }

        $plant->save();

        $plant_site = new PlantSite();

        $plant_site->plant_id = $plant->id;
        $plant_site->site_id = $request->site_id;
        $plant_site->online_status = 'Y';
        $plant_site->created_by = Auth::user()->id;

        $plant_site->save();

        //Expected Generation Log
        $expected_generation['plant_id'] = $plant->id;
        $expected_generation['daily_expected_generation'] = $request->expected_generation;
        $expected_generation['created_at'] = date('Y-m-d H:i:s');
        $expected_generation['updated_at'] = date('Y-m-d H:i:s');

        $expected_generation_exist = ExpectedGenerationLog::where('plant_id', $plant->id)->whereDate('created_at', date('Y-m-d'))->first();

        if ($expected_generation_exist) {

            $expected_generation_log = $expected_generation_exist->fill($expected_generation)->save();
        } else {

            $expected_generation_log = ExpectedGenerationLog::create($expected_generation);
        }

        foreach ($_POST as $key => $value) {

            if (strpos($key, 'inverter_') === 0) {

                $siteInverterDetail['plant_id'] = $plant->id;
                $siteInverterDetail['site_id'] = $request->site_id;
                $siteInverterDetail['dv_inverter'] = substr($key, 9);
                if ($meterType == 'Solis') {

                    $siteInverterDetail['dv_inverter_type'] = 'INVERTER';
                } else {

                    $siteInverterDetail['dv_inverter_type'] = 1;
                }
                $siteInverterDetail['dv_installed_dc_power'] = $value;
                $siteInverterDetail['created_at'] = date('Y-m-d H:i:s');
                $siteInverterDetail['updated_at'] = date('Y-m-d H:i:s');

                $siteDataExist = SiteInverterDetail::where('plant_id', $plant->id)->where('site_id', $request->site_id)->where('dv_inverter', substr($request->$key, 9))->first();

                if ($siteDataExist) {

                    $siteData = $siteDataExist->fill($siteInverterDetail)->save();
                } else {

                    $siteData = SiteInverterDetail::create($siteInverterDetail);
                }
            }
        }

        return redirect()->route('admin.plant.details', ['id' => $plant->id]);


    }

    public function plantInverterDetail($id, $inverterSerialNo)
    {
//        return [$id,$inverterSerialNo];

//        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {
//
//            $plant_arr = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id');
//            $plant_arr = $plant_arr->toArray();
//
//            if (!empty($plant_arr) && (!in_array($id, $plant_arr))) {
//                return redirect()->back()->with('error', 'You have no access of that Inverter!');
//            }
//        }
//        if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
//
//            $plant_arr = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
//            $plant_arr = $plant_arr->toArray();
//
//            if (!empty($plant_arr) && !in_array((string)$id, $plant_arr)) {
//                return redirect()->back()->with('error', 'You have no access of that Inverter!');
//            } else if (empty($plant_arr)) {
//                return redirect()->back()->with('error', 'You have no access of that Inverter!');
//            }
//        }

        $total_daily_generation = 0;
        $total_monthly_generation = 0;
        $total_yearly_generation = 0;

        $where_array = array();
        if (Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['plants.company_id'] = $company_id;
        }
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        if ($page == 'refresh') {
            $controller = new PlantSiteDataController();
            $controller->saltecSiteListData();
        }
//        return [$id,$where_array];

        $plant = Plant::with(['inverterserialno', 'daily_inverter_detail', 'monthly_inverter_detail', 'yearly_inverter_detail'])->where('id', $id)->first();
        $inverterData = [];
        $daily_inverter_data = DailyInverterDetail::where('dv_inverter', $inverterSerialNo)->whereDate('created_at', date('Y-m-d'))->sum('daily_generation');
        $monthly_inverter_data = MonthlyInverterDetail::where('dv_inverter', $inverterSerialNo)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('monthly_generation');
        $yearly_inverter_data = YearlyInverterDetail::where('dv_inverter', $inverterSerialNo)->whereYear('created_at', date('Y'))->sum('yearly_generation');
        $pvVoltage1 = InverterMPPTDetail::where('dv_inverter', $inverterSerialNo)->where('mppt_number',1)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
        $pvCurrent1 = InverterMPPTDetail::where('dv_inverter', $inverterSerialNo)->where('mppt_number',1)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
        $pvVoltage2 = InverterMPPTDetail::where('dv_inverter', $inverterSerialNo)->where('mppt_number',2)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
        $pvCurrent2 = InverterMPPTDetail::where('dv_inverter', $inverterSerialNo)->where('mppt_number',2)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
        $pvVoltage3 = InverterMPPTDetail::where('dv_inverter', $inverterSerialNo)->where('mppt_number',3)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
        $pvCurrent3 = InverterMPPTDetail::where('dv_inverter', $inverterSerialNo)->where('mppt_number',3)->whereDate('collect_time', date('Y-m-d'))->latest()->first();
        $totalGeneration = YearlyInverterDetail::where('dv_inverter', $inverterSerialNo)->sum('yearly_generation');
        $inverter = InverterSerialNo::where('dv_inverter',$inverterSerialNo)->first();
        $inverter['serial_no'] = $inverter->dv_inverter_serial_no != null ? $inverter->dv_inverter_serial_no : '';
        $inverter['daily_generation'] = number_format($daily_inverter_data, 2);
        $inverter['monthly_generation'] = number_format($monthly_inverter_data, 2);
        $inverter['annual_generation'] = number_format($yearly_inverter_data, 2);
        $inverter['total_generation'] = number_format($totalGeneration, 2);
        $inverterCurrentDataLogTime = InverterDetail::where('dv_inverter', $inverterSerialNo)->whereDate('collect_time', date('Y-m-d'))->exists() ? InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time','desc')->first()->collect_time : date('Y-m-d 00:10:00');

        $inverterFinalCurrentDataDateTime = $this->previousTenMinutesDateTime($inverterCurrentDataLogTime);
        $inverterDetailObject = InverterDetail::where('dv_inverter', $inverterSerialNo)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $inverterFinalCurrentDataDateTime)->orderBy('collect_time', 'DESC')->first();
        if($inverterDetailObject) {
            $inverter['ac_output_power'] = (string)$inverterDetailObject->inverterPower;
        }
        else
        {
            $inverter['ac_output_power'] = '0';
        }
        //                $inverterTotalACOutputPower += $inverterDetailObject && $inverterDetailObject->inverterPower ? $inverterDetailObject->inverterPower : 0;
//                $invertersOutputPowerConverted = $this->unitConversion($inverterTotalACOutputPower, 'kW');
        $inverter['r_voltage1']  =  $inverterDetailObject && isset($inverterDetailObject->phase_voltage_r) ? (string)round($inverterDetailObject->phase_voltage_r, 2) : (string)0;
        $inverter['r_voltage2'] =  $inverterDetailObject && isset($inverterDetailObject->phase_voltage_s) ? (string)round($inverterDetailObject->phase_voltage_s, 2) : (string)0;
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
        array_push($inverterData,$inverter);;
        $plant['inverters'] = $inverterData;
//        return $plant->inverters;
//        return $plant;

//        return 'okkkkkkkkkkk'.date('Y-m-d');
        $total_daily_gen = DailyInverterDetail::where('dv_inverter', $inverterSerialNo)->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->sum('daily_generation');
        $total_daily_gen_arr = $this->unitConversion($total_daily_gen, 'kWh');
        $total_daily_generation = round($total_daily_gen_arr[0], 2) . '' . $total_daily_gen_arr[1];

        $total_monthly_gen = MonthlyInverterDetail::where('dv_inverter', $inverterSerialNo)->whereMonth('created_at', date('m'))->sum('monthly_generation');
        $total_monthly_gen_arr = $this->unitConversion($total_monthly_gen, 'kWh');
        $total_monthly_generation = round($total_monthly_gen_arr[0], 2) . '' . $total_monthly_gen_arr[1];

        $total_yearly_gen = YearlyInverterDetail::where('dv_inverter', $inverterSerialNo)->whereYear('created_at', date('Y'))->sum('yearly_generation');
        $total_yearly_gen_arr = $this->unitConversion($total_yearly_gen, 'kWh');
        $total_yearly_generation = round($total_yearly_gen_arr[0], 2) . '' . $total_yearly_gen_arr[1];

        $total_gen_sum = Inverter::where('dv_inverter', $inverterSerialNo)->sum('total_generation');
        $total_generation_sum_arr = $this->unitConversion($total_gen_sum, 'kWh');
        $total_generation_sum = round($total_generation_sum_arr[0], 2) . '' . $total_generation_sum_arr[1];

        $inverter_previous_data = InverterSerialNo::where('dv_inverter_serial_no', $inverterSerialNo)->get();
//        return $inverter_previous_data;

        foreach ($inverter_previous_data as $key => $invt) {

            $dc_power_data = Inverter::where('plant_id', $id)->where('siteId', $invt->site_id)->where('dv_inverter', $invt->dv_inverter)->pluck('dc_power');
            $inverter_previous_data[$key]->dc_power = count($dc_power_data) > 0 && isset($dc_power_data[0]) ? $dc_power_data[0] : 0;

            $daily_generation_data = DailyInverterDetail::where('plant_id', $id)->where('siteId', $invt->site_id)->where('dv_inverter', $invt->dv_inverter)->whereDay('created_at', date('d', strtotime("-1 days")))->pluck('daily_generation');
            $daily_generation_data_arr = count($daily_generation_data) > 0 && isset($daily_generation_data[0]) ? $this->unitConversion($daily_generation_data[0], 'kWh') : [0, 'kWh'];
            $inverter_previous_data[$key]->daily_generation = round($daily_generation_data_arr[0], 2) . '' . $daily_generation_data_arr[1];

            $monthly_generation_data = MonthlyInverterDetail::where('plant_id', $id)->where('siteId', $invt->site_id)->where('dv_inverter', $invt->dv_inverter)->whereMonth('created_at', date('m', strtotime("-1 months")))->pluck('monthly_generation');
            $monthly_generation_data_arr = count($monthly_generation_data) > 0 && isset($monthly_generation_data[0]) ? $this->unitConversion($monthly_generation_data[0], 'kWh') : [0, 'kWh'];
            $inverter_previous_data[$key]->monthly_generation = round($monthly_generation_data_arr[0], 2) . '' . $monthly_generation_data_arr[1];

            $yearly_generation_data = YearlyInverterDetail::where('plant_id', $id)->where('siteId', $invt->site_id)->where('dv_inverter', $invt->dv_inverter)->whereYear('created_at', date('Y', strtotime("-1 years")))->pluck('yearly_generation');
            $yearly_generation_data_arr = count($yearly_generation_data) > 0 && isset($yearly_generation_data[0]) ? $this->unitConversion($yearly_generation_data[0], 'kWh') : [0, 'kWh'];
            $inverter_previous_data[$key]->yearly_generation = round($yearly_generation_data_arr[0], 2) . '' . $yearly_generation_data_arr[1];

            $work_state_data = DB::table('fault_and_alarms')
                ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                ->select('fault_and_alarms.description')
                ->where('fault_alarm_log.plant_id', $id)
                ->where('fault_alarm_log.siteId', $invt->site_id)
                ->where('fault_alarm_log.dv_inverter', $invt->dv_inverter)
                ->orderBy('fault_alarm_log.created_at', 'DESC')
                ->first();

            $inverter_previous_data[$key]->workstate = $work_state_data && isset($work_state_data->description) ? $work_state_data->description : '-----';
        }
//        return $inverter_previous_data;

//        if ($plant == null) {
//            return redirect('/home');
//        }
//        return $plant;
//        return ['plant' => $plant, 'inverter_previous_data' => $inverter_previous_data, 'total_daily_generation' => $total_daily_generation, 'total_monthly_generation' => $total_monthly_generation, 'total_yearly_generation' => $total_yearly_generation, 'total_generation_sum' => $total_generation_sum];
//        return ['plant' => $id, 'inverter_previous_data' => $inverter_previous_data, 'total_daily_generation' => $total_daily_generation, 'total_monthly_generation' => $total_monthly_generation, 'total_yearly_generation' => $total_yearly_generation, 'total_generation_sum' => $total_generation_sum];
        return view('admin.plant.plantdetail', ['plant' => $plant, 'inverter_previous_data' => $inverter_previous_data, 'total_daily_generation' => $total_daily_generation, 'total_monthly_generation' => $total_monthly_generation, 'total_yearly_generation' => $total_yearly_generation, 'total_generation_sum' => $total_generation_sum]);
    }

    public function plantBatteryDetails($id)
    {

        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            $plant_arr = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id');
            $plant_arr = $plant_arr->toArray();

            if (!empty($plant_arr) && (!in_array($id, $plant_arr))) {
                return redirect()->back()->with('error', 'You have no access of that Inverter!');
            }
        }
        if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {

            $plant_arr = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
            $plant_arr = $plant_arr->toArray();

            if (!empty($plant_arr) && !in_array((string)$id, $plant_arr)) {
                return redirect()->back()->with('error', 'You have no access of that Inverter!');
            } else if (empty($plant_arr)) {
                return redirect()->back()->with('error', 'You have no access of that Inverter!');
            }
        }

        $total_daily_generation = 0;
        $total_monthly_generation = 0;
        $total_yearly_generation = 0;

        $where_array = array();
        if (Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['plants.company_id'] = $company_id;
        }
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        if ($page == 'refresh') {
            $controller = new PlantSiteDataController();
            $controller->saltecSiteListData();
        }

        $plant = Plant::with(['inverters', 'inverterserialno', 'daily_inverter_detail', 'monthly_inverter_detail', 'yearly_inverter_detail'])->where('id', $id)->where($where_array)->first();

        $total_daily_gen = DailyInverterDetail::where('plant_id', $id)->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->sum('daily_generation');
        $total_daily_gen_arr = $this->unitConversion($total_daily_gen, 'kWh');
        $total_daily_generation = round($total_daily_gen_arr[0], 2) . '' . $total_daily_gen_arr[1];

        $total_monthly_gen = MonthlyInverterDetail::where('plant_id', $id)->whereMonth('created_at', date('m'))->sum('monthly_generation');
        $total_monthly_gen_arr = $this->unitConversion($total_monthly_gen, 'kWh');
        $total_monthly_generation = round($total_monthly_gen_arr[0], 2) . '' . $total_monthly_gen_arr[1];

        $total_yearly_gen = YearlyInverterDetail::where('plant_id', $id)->whereYear('created_at', date('Y'))->sum('yearly_generation');
        $total_yearly_gen_arr = $this->unitConversion($total_yearly_gen, 'kWh');
        $total_yearly_generation = round($total_yearly_gen_arr[0], 2) . '' . $total_yearly_gen_arr[1];

        $total_gen_sum = Inverter::where('plant_id', $id)->sum('total_generation');
        $total_generation_sum_arr = $this->unitConversion($total_gen_sum, 'kWh');
        $total_generation_sum = round($total_generation_sum_arr[0], 2) . '' . $total_generation_sum_arr[1];

        $inverter_previous_data = InverterSerialNo::where('plant_id', $id)->get();

        foreach ($inverter_previous_data as $key => $invt) {

            $dc_power_data = Inverter::where('plant_id', $id)->where('siteId', $invt->site_id)->where('dv_inverter', $invt->dv_inverter)->pluck('dc_power');
            $inverter_previous_data[$key]->dc_power = count($dc_power_data) > 0 && isset($dc_power_data[0]) ? $dc_power_data[0] : 0;

            $daily_generation_data = DailyInverterDetail::where('plant_id', $id)->where('siteId', $invt->site_id)->where('dv_inverter', $invt->dv_inverter)->whereDay('created_at', date('d', strtotime("-1 days")))->pluck('daily_generation');
            $daily_generation_data_arr = count($daily_generation_data) > 0 && isset($daily_generation_data[0]) ? $this->unitConversion($daily_generation_data[0], 'kWh') : [0, 'kWh'];
            $inverter_previous_data[$key]->daily_generation = round($daily_generation_data_arr[0], 2) . '' . $daily_generation_data_arr[1];

            $monthly_generation_data = MonthlyInverterDetail::where('plant_id', $id)->where('siteId', $invt->site_id)->where('dv_inverter', $invt->dv_inverter)->whereMonth('created_at', date('m', strtotime("-1 months")))->pluck('monthly_generation');
            $monthly_generation_data_arr = count($monthly_generation_data) > 0 && isset($monthly_generation_data[0]) ? $this->unitConversion($monthly_generation_data[0], 'kWh') : [0, 'kWh'];
            $inverter_previous_data[$key]->monthly_generation = round($monthly_generation_data_arr[0], 2) . '' . $monthly_generation_data_arr[1];

            $yearly_generation_data = YearlyInverterDetail::where('plant_id', $id)->where('siteId', $invt->site_id)->where('dv_inverter', $invt->dv_inverter)->whereYear('created_at', date('Y', strtotime("-1 years")))->pluck('yearly_generation');
            $yearly_generation_data_arr = count($yearly_generation_data) > 0 && isset($yearly_generation_data[0]) ? $this->unitConversion($yearly_generation_data[0], 'kWh') : [0, 'kWh'];
            $inverter_previous_data[$key]->yearly_generation = round($yearly_generation_data_arr[0], 2) . '' . $yearly_generation_data_arr[1];

            $work_state_data = DB::table('fault_and_alarms')
                ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                ->select('fault_and_alarms.description')
                ->where('fault_alarm_log.plant_id', $id)
                ->where('fault_alarm_log.siteId', $invt->site_id)
                ->where('fault_alarm_log.dv_inverter', $invt->dv_inverter)
                ->orderBy('fault_alarm_log.created_at', 'DESC')
                ->first();

            $inverter_previous_data[$key]->workstate = $work_state_data && isset($work_state_data->description) ? $work_state_data->description : '-----';
        }

        if ($plant == null) {
            return redirect('/home');
        }
//        return $plant;
//        return ['plant' => $plant, 'inverter_previous_data' => $inverter_previous_data, 'total_daily_generation' => $total_daily_generation, 'total_monthly_generation' => $total_monthly_generation, 'total_yearly_generation' => $total_yearly_generation, 'total_generation_sum' => $total_generation_sum];

        return view('admin.plant.plant-battery-detail', ['plant' => $plant, 'inverter_previous_data' => $inverter_previous_data, 'total_daily_generation' => $total_daily_generation, 'total_monthly_generation' => $total_monthly_generation, 'total_yearly_generation' => $total_yearly_generation, 'total_generation_sum' => $total_generation_sum]);
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

    public function plantInverterGraphs(Request $request, $msn, $time, $date)
    {

        $time = $request->time;
        $dates = strtotime($request->date);

        if ($time == 'day') {
            $date = date('Y-m-d', $dates);
        } else if ($time == 'month') {
            $date = date('Y-m', $dates);
        } else if ($time == 'year') {
            $date = $request->date;
        }

        $inverter_detail = InverterSerialNo::where('dv_inverter_serial_no', $msn)->first();
        $today_log_data = [];
        $today_log_time = [];

        $early_sunrise = Weather::whereDate('created_at', Date('Y-m-d'))->orderBy('sunrise', 'ASC')->first();
        $sunrise = $early_sunrise ? explode(':', $early_sunrise->sunrise) : explode(':', '06:00:AM');
        $sunrise_hour = $sunrise[0];
        $sunrise_min = $sunrise[1];

        $current_generation_start_time = InverterDetail::select('created_at')->where('plant_id', $inverter_detail->plant_id)->where('siteId', $inverter_detail->site_id)->where('dv_inverter', $inverter_detail->dv_inverter)->where('created_at', '>', $date . ' ' . $sunrise_hour . ':' . $sunrise_min)->where('daily_generation', '>', 0)->orderBy('created_at', 'ASC')->first();
        $start_date = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->created_at)) : '05:00:00';

        if ($time == 'day') {

            if ($inverter_detail->plant_id) {

                $current_generation = InverterDetail::select('created_at')->where('plant_id', $inverter_detail->plant_id)->where('siteId', $inverter_detail->site_id)->where('dv_inverter', $inverter_detail->dv_inverter)->whereBetween('created_at', [date($date . ' ' . $start_date), date($date . ' 23:59:00')])->groupBy('created_at')->get();
                if (!empty($current_generation) && count($current_generation) > 0) {
                    foreach ($current_generation as $key => $today_log) {
                        $today_log_time[] = date('H:i', strtotime($today_log->created_at));
                        $today_log_data_sum = InverterDetail::where('plant_id', $inverter_detail->plant_id)->where('siteId', $inverter_detail->site_id)->where('dv_inverter', $inverter_detail->dv_inverter)->where('created_at', $today_log->created_at)->sum('daily_generation');
                        $today_log_data[] = $key > 0 && $today_log_data[$key - 1] > $today_log_data_sum ? round($today_log_data[$key - 1], 2) : round($today_log_data_sum, 2);
                    }
                }

                $today_log_data_sum = DailyInverterDetail::where('plant_id', $inverter_detail->plant_id)->where('siteId', $inverter_detail->site_id)->where('dv_inverter', $inverter_detail->dv_inverter)->whereDate('created_at', $date)->sum('daily_generation');
                $today_log_data_sum_arr = $today_log_data_sum ? $this->unitConversion((double)$today_log_data_sum, 'kWh') : [0, 'kWh'];

                $generation_log['today_energy_generation'] = $today_log_data_sum;
                $generation_log['today_energy_gene'] = round($today_log_data_sum_arr[0], 2) . ' ' . $today_log_data_sum_arr[1];
                $generation_log['max_energy_generation'] = isset($today_log_data) && !empty($today_log_data) ? max($today_log_data) : '';
                $generation_log['today_generation'] = isset($today_log_data) && !empty($today_log_data) ? $today_log_data : [];
                $generation_log['today_time'] = isset($today_log_time) && !empty($today_log_time) ? $today_log_time : [];

                return $generation_log;
            }
        } else if ($time == 'month') {

            $explode_data = explode('-', $date);
            $mon = $explode_data[1];
            $yer = $explode_data[0];

            $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);
            for ($i = 1; $i <= $dd; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $today_log_data_sum = DailyInverterDetail::where('plant_id', $inverter_detail->plant_id)->where('siteId', $inverter_detail->site_id)->where('dv_inverter', $inverter_detail->dv_inverter)->whereDate('created_at', $date . '-' . $i)->sum('daily_generation');

                $today_log_data[] = (object)[
                    "y" => $today_log_data_sum ? round($today_log_data_sum, 2) : 0,
                    "x" => (int)$i,
                ];
            }
        } else if ($time == 'year') {

            for ($i = 1; $i <= 12; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $today_log_data_sum = MonthlyInverterDetail::where('plant_id', $inverter_detail->plant_id)->where('siteId', $inverter_detail->site_id)->where('dv_inverter', $inverter_detail->dv_inverter)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthly_generation');

                $today_log_data[] = (object)[
                    "y" => $today_log_data_sum > 0 ? round($today_log_data_sum, 2) : 0,
                    "label" => substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3),
                    "tooltip" => date('F', mktime(0, 0, 0, $i, 10)),
                ];
            }
        }

        if ($time == 'month') {

            $monthly_gen = MonthlyInverterDetail::where('plant_id', $inverter_detail->plant_id)->where('siteId', $inverter_detail->site_id)->where('dv_inverter', $inverter_detail->dv_inverter)->where('created_at', 'LIKE', $date . '%')->sum('monthly_generation');
            $today_log_data_sum_arr = $monthly_gen ? $this->unitConversion((double)$monthly_gen, 'kWh') : [0, 'kWh'];

            $today_log_data1['today_log_data'] = $today_log_data;
            $today_log_data1['today_energy_generation'] = $monthly_gen ? $monthly_gen : 0;
            $today_log_data1['today_energy_gene'] = round($today_log_data_sum_arr[0], 2) . ' ' . $today_log_data_sum_arr[1];

            return $today_log_data1;
        } else if ($time == 'year') {

            $yearly_gen = YearlyInverterDetail::where('plant_id', $inverter_detail->plant_id)->where('siteId', $inverter_detail->site_id)->where('dv_inverter', $inverter_detail->dv_inverter)->whereYear('created_at', $date)->sum('yearly_generation');
            $today_log_data_sum_arr = $yearly_gen ? $this->unitConversion((double)$yearly_gen, 'kWh') : [0, 'kWh'];

            $today_log_data1['today_log_data'] = $today_log_data;
            $today_log_data1['today_energy_generation'] = $yearly_gen ? $yearly_gen : 0;
            $today_log_data1['today_energy_gene'] = round($today_log_data_sum_arr[0], 2) . ' ' . $today_log_data_sum_arr[1];

            return $today_log_data1;
        }

    }

    public function userPlantDetail($id = 0)
    {
        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            $plant_arr = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id');
            $plant_arr = $plant_arr->toArray();

            if (!empty($plant_arr) && (!in_array($id, $plant_arr))) {
                return redirect()->back()->with('error', 'You have no access of that plant!');
            }
        }
        if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {

            $plant_arr = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
            $plant_arr = $plant_arr->toArray();

            if (!empty($plant_arr) && !in_array((string)$id, $plant_arr)) {
                return redirect()->back()->with('error', 'You have no access of that plant!');
            } else if (empty($plant_arr)) {
                return redirect()->back()->with('error', 'You have no access of that plant!');
            }
        }

        \Session::put(['plantHeaderID' => $id]);

        $where_array = array();
        if (Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] = $company_id;
        }

        $plant = Plant::where('id', $id)->first();
//        return $plant;
        $batteryValues = StationBattery::where('plant_id', $id)->latest()->first();
        $plantBatteryAh = isset($plant) ? (int)$plant['battery_ah'] : 0;
        $plantBatteryVoltage = isset($plant) ? (double)$plant['battery_voltage'] : 0;
        $batteryDOD = isset($plant) ? (int)$plant['battery_dod'] : 0;
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
//        return $inverterConsumptionData[0];
//        else
//        {
//
//        }
//         ? $inverterConsumptionData[0] = 1 : $inverterConsumptionData[0];
//        return $inverterConsumptionData[0];
        $inverterRatedPower = $this->unitConversion($batteryRatedPower, 'W');
        if ($inverterRatedPower[0] == 0) {
            $inverterRatedPower[0] = 1;
        }
        $batteryBackup = ($batteryRemaining / $inverterConsumptionData[0]);
//        return $batteryRemaining;
        $batteryBackupMaxLoad = ($batteryRemaining / (0.9 * $inverterRatedPower[0]));
//        return $inverterConsumptionData[0];
        $batteryBackupFormula = round($batteryBackup, 2);
        $batteryBackupMaxLoadFormula = round($batteryBackupMaxLoad, 2);

//        return $batteryBackup;

//        return $batteryRemainingFormula;

        if (!$plant) {
            return redirect()->back()->with('error', 'No plant found!');
        }
//        return $plant;
        $peakTeriffRate = $plant->peak_teriff_rate;
        $benchMarkPrice = $plant->benchmark_price;

        $plant['system_type_id'] = $plant->system_type;
        $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
        $plant['system_type'] = SystemType::find($plant->system_type)->type;

        $pl_sites_array = PlantSite::where('plant_id', $id)->pluck('site_id')->toArray();
        $plant_inverters = InverterSerialNo::whereIn('site_id', $pl_sites_array)->get();

        $plant != null ? Session::put(['plant_name' => $plant->plant_name]) : '';

        $current_generation = 0;
        $current_consumption = 0;
        $current_grid = 0;
        $current_grid_type = '';
        $current = array();
        $daily = array();
        $monthly = array();
        $yearly = array();
        $total = array();
        $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $id)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
        $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
//        return $finalCurrentDataDateTime;
        $current_data = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type', 'comm_failed', 'battery_capacity', 'battery_type', 'battery_capacity', 'battery_power', 'collect_time', 'created_at')->where('plant_id', $id)->whereDate('collect_time', '=', date('Y-m-d'))->orderBy('collect_time', 'desc')->latest()->first();
//        return $current_data;
        if (!empty($current_data)) {
            $current_generation = $current_data ? (double)$current_data->current_generation : 0;
            $current_consumption = $current_data ? (double)$current_data->current_consumption : 0;
            $current_grid = $current_data ? (double)$current_data->current_grid : 0;
            $current_grid_type = $current_data ? $current_data->grid_type : '';
            $current['date'] = $current_data ? $current_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
            $current['comm_fail'] = $current_data ? (int)$current_data->comm_failed : 0;
            $current['battery_power'] = $current_data->battery_power;
            $current['battery_capacity'] = $current_data->battery_capacity;
            $current['battery_type'] = $current_data->battery_type;
            $current['battery_capacity'] = $current_data->battery_capacity;
        } else {
            $current_generation = 0;
            $current_consumption = 0;
            $current_grid = 0;
            $current_grid_type = '';

            $current['date'] = date('Y-m-d H:i:s');
            $current['comm_fail'] = 0;
            $current['battery_power'] = 0;
            $current['battery_capacity'] = 0;
            $current['battery_type'] = 0;
            $current['battery_capacity'] = '0%';
        }

        $plant_sites_array = PlantSite::where('plant_id', $id)->pluck('site_id');

        $daily_data = DailyProcessedPlantDetail::select('dailyGeneration', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_charge_energy', 'daily_discharge_energy', 'created_at')->where('plant_id', $id)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
//        return $daily_data;
        if (!empty($daily_data)) {
            $daily_generation = $daily_data ? (double)$daily_data->dailyGeneration : 0;
            $daily_consumption = $daily_data ? (double)$daily_data->dailyConsumption : 0;
            $daily_grid = $daily_data ? (double)$daily_data->dailyGridPower : 0;
            $daily_bought_energy = $daily_data ? (double)$daily_data->dailyBoughtEnergy : 0;
            $daily_sell_energy = $daily_data ? (double)$daily_data->dailySellEnergy : 0;
            $daily_saving = $daily_data ? (double)$daily_data->dailySaving : 0;
            $daily_charge_energy = $daily_data ? (double)$daily_data->daily_charge_energy : 0;
            $daily_discharge_energy = $daily_data ? (double)$daily_data->daily_discharge_energy : 0;
            $peak_hours_savings = $daily_data ? (double)$daily_data->daily_peak_hours_battery_discharge * (int)$peakTeriffRate : 0;
            $generation_saving = $daily_data ? (double)$daily_data->dailyGeneration * (int)$benchMarkPrice : 0;
            $totalCostSaving = $daily_data ? (double)$daily_data->dailySaving : 0;
            $daily['date'] = $daily_data ? $daily_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
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
            $daily['date'] = date('Y-m-d H:i:s');
        }
//        return $generation_saving;

        $monthly_data = MonthlyProcessedPlantDetail::select('monthlyGeneration', 'monthly_peak_hours_discharge_energy', 'monthly_peak_hours_grid_import', 'monthly_peak_hours_consumption', 'monthlyConsumption', 'monthlyGridPower', 'monthlyBoughtEnergy', 'monthlySellEnergy', 'monthlySaving', 'monthly_charge_energy', 'monthly_discharge_energy', 'created_at')->where('plant_id', $id)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
        $monthly_generation = $monthly_data ? (double)$monthly_data->monthlyGeneration : 0;
        $monthly_consumption = $monthly_data ? (double)$monthly_data->monthlyConsumption : 0;
        $monthly_grid = $monthly_data ? (double)$monthly_data->monthlyGridPower : 0;
        $monthly_bought_energy = $monthly_data ? (double)$monthly_data->monthlyBoughtEnergy : 0;
        $monthly_sell_energy = $monthly_data ? (double)$monthly_data->monthlySellEnergy : 0;
        $monthly_saving = $monthly_data ? (double)$monthly_data->monthlySaving : 0;
        $monthly_charge_energy = $monthly_data ? (double)$monthly_data->monthly_charge_energy : 0;
        $monthly_discharge_energy = $monthly_data ? (double)$monthly_data->monthly_discharge_energy : 0;
        $monthly_peak_hours_savings = $monthly_data ? (double)$monthly_data->monthly_peak_hours_discharge_energy * (int)$peakTeriffRate : 0;
        $monthly_generation_saving = $monthly_data ? (double)$monthly_data->monthlyGeneration * (int)$benchMarkPrice : 0;
        $monthlyTotalCostSaving = $monthly_data ? (double)$monthly_data->monthlySaving : 0;

        $monthly['date'] = $monthly_data ? $monthly_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        $yearly_data = YearlyProcessedPlantDetail::select('yearlyGeneration', 'yearly_peak_hours_discharge_energy', 'yearly_peak_hours_grid_import', 'yearly_peak_hours_consumption', 'yearlyConsumption', 'yearlyGridPower', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $id)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
        $yearly_generation = $yearly_data ? (double)$yearly_data->yearlyGeneration : 0;
        $yearly_consumption = $yearly_data ? (double)$yearly_data->yearlyConsumption : 0;
        $yearly_grid = $yearly_data ? (double)$yearly_data->yearlyGridPower : 0;
        $yearly_bought_energy = $yearly_data ? (double)$yearly_data->yearlyBoughtEnergy : 0;
        $yearly_charge_energy = $yearly_data ? (double)$yearly_data->yearly_charge_energy : 0;
        $yearly_discharge_energy = $yearly_data ? (double)$yearly_data->yearly_discharge_energy : 0;
        $yearly_sell_energy = $yearly_data ? (double)$yearly_data->yearlySellEnergy : 0;
        $yearly_saving = $yearly_data ? (double)$yearly_data->yearlySaving : 0;
        $yearly_peak_hours_savings = $yearly_data ? (double)$yearly_data->yearly_peak_hours_discharge_energy * (int)$peakTeriffRate : 0;
        $yearly_generation_saving = $yearly_data ? (double)$yearly_data->yearlyGeneration * (int)$benchMarkPrice : 0;
        $yearlyTotalCostSaving = $yearly_data ? (double)$yearly_data->yearlySaving : 0;
        $yearly['date'] = $yearly_data ? $yearly_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');

        $total_data = TotalProcessedPlantDetail::where('plant_id', $id)->first();
        $total_generation = $total_data ? (double)$total_data->plant_total_generation : 0;
        $total_consumption = $total_data ? (double)$total_data->plant_total_consumption : 0;
        $total_grid = $total_data ? (double)$total_data->plant_total_grid : 0;
        $total_bought_energy = $total_data ? (double)$total_data->plant_total_buy_energy : 0;
        $total_sell_energy = $total_data ? (double)$total_data->plant_total_sell_energy : 0;
        $total_saving = $total_data ? (double)$total_data->plant_total_saving : 0;
        $total_charge_energy = $total_data ? (double)$total_data->plant_total_charge_energy : 0;
        $total_discharge_energy = $total_data ? (double)$total_data->plant_total_discharge_energy : 0;

        $curr_gen_arr = $this->unitConversion($current_generation, 'kW');
        $curr_con_arr = $this->unitConversion($current_consumption, 'W');
        $curr_grid_arr = $this->unitConversion($current_grid, 'kW');
        $battery_power_data = $this->unitConversion($current['battery_power'], 'W');
        $current['generation'] = round($curr_gen_arr[0], 2) . ' ' . $curr_gen_arr[1];
        $current['consumption'] = round($curr_con_arr[0], 2) . ' ' . $curr_con_arr[1];
        $current['grid'] = round($curr_grid_arr[0], 2) . ' ' . $curr_grid_arr[1];
        $current['battery_power_data'] = round($battery_power_data[0], 2) . ' ' . $battery_power_data[1];
        $current['grid_type'] = $current_grid_type;

        $daily_gen_arr = $this->unitConversion($daily_generation, 'kWh');
        $daily_con_arr = $this->unitConversion($daily_consumption, 'kWh');
        $daily_grid_arr = $this->unitConversion($daily_grid, 'kWh');
        $daily_buy_arr = $this->unitConversion($daily_bought_energy, 'kWh');
        $daily_sell_arr = $this->unitConversion($daily_sell_energy, 'kWh');
        $daily_revenue_arr = $this->unitConversion($daily_saving, 'PKR');
        $daily_charge_arr = $this->unitConversion($daily_charge_energy, 'kWh');
        $daily_discharge_arr = $this->unitConversion($daily_discharge_energy, 'kWh');
        $daily_net_grid_arr = $this->unitConversion(((double)$daily_bought_energy - (double)$daily_sell_energy), 'kWh');
        $daily['generation'] = round($daily_gen_arr[0], 2) . ' ' . $daily_gen_arr[1];
        $daily['consumption'] = round($daily_con_arr[0], 2) . ' ' . $daily_con_arr[1];
        $daily['grid'] = round($daily_grid_arr[0], 2) . ' ' . $daily_grid_arr[1];
        $daily['boughtEnergy'] = round($daily_buy_arr[0], 2) . ' ' . $daily_buy_arr[1];
        $daily['sellEnergy'] = round($daily_sell_arr[0], 2) . ' ' . $daily_sell_arr[1];
        $daily['netGrid'] = round($daily_net_grid_arr[0], 2) . ' ' . $daily_net_grid_arr[1];
        $daily['netGridSign'] = ((double)$daily_bought_energy - (double)$daily_sell_energy) < 0 ? '-' : '';
        $daily['revenue'] = round($daily_revenue_arr[0], 2) . '' . $daily_revenue_arr[1];
        $daily['dailyChargeEnergy'] = round($daily_charge_arr[0], 2) . '' . $daily_charge_arr[1];
        $daily['dailyDischargeEnergy'] = round($daily_discharge_arr[0], 2) . '' . $daily_discharge_arr[1];
        $daily['dailyPeakHoursSaving'] = round($peak_hours_savings, 2);
//        return $daily['dailyPeakHoursSaving'];
        $daily['dailyGenerationSaving'] = round($generation_saving, 2);
        $daily['dailyTotalSaving'] = round($daily['dailyPeakHoursSaving'] + $daily['dailyGenerationSaving']);

        $monthly_charge_ener = $this->unitConversion($monthly_charge_energy, 'kWh');
        $monthly_discharge_ener = $this->unitConversion($monthly_discharge_energy, 'kWh');
        $monthly_gen_arr = $this->unitConversion($monthly_generation, 'kWh');
        $monthly_con_arr = $this->unitConversion($monthly_consumption, 'kWh');
        $monthly_grid_arr = $this->unitConversion($monthly_grid, 'kWh');
        $monthly_buy_arr = $this->unitConversion($monthly_bought_energy, 'kWh');
        $monthly_sell_arr = $this->unitConversion($monthly_sell_energy, 'kWh');
        $monthly_revenue_arr = $this->unitConversion($monthly_saving, 'PKR');
        $monthly_net_grid_arr = $this->unitConversion(((double)$monthly_bought_energy - (double)$monthly_sell_energy), 'kWh');
        $monthly['generation'] = round($monthly_gen_arr[0], 2) . ' ' . $monthly_gen_arr[1];
        $monthly['consumption'] = round($monthly_con_arr[0], 2) . ' ' . $monthly_con_arr[1];
        $monthly['grid'] = round($monthly_grid_arr[0], 2) . ' ' . $monthly_grid_arr[1];
        $monthly['boughtEnergy'] = round($monthly_buy_arr[0], 2) . ' ' . $monthly_buy_arr[1];
        $monthly['sellEnergy'] = round($monthly_sell_arr[0], 2) . ' ' . $monthly_sell_arr[1];
        $monthly['netGrid'] = round($monthly_net_grid_arr[0], 2) . ' ' . $monthly_net_grid_arr[1];
        $monthly['netGridSign'] = ((double)$monthly_bought_energy - (double)$monthly_sell_energy) < 0 ? '-' : '';
        $monthly['revenue'] = round($monthly_revenue_arr[0], 2) . '' . $monthly_revenue_arr[1];
        $monthly['monthlyChargeEnergy'] = round($monthly_charge_ener[0], 2) . '' . $monthly_charge_ener[1];
        $monthly['monthlyDischargeEnergy'] = round($monthly_discharge_ener[0], 2) . '' . $monthly_discharge_ener[1];
        $monthly['monthlyPeakHoursSaving'] = round($monthly_peak_hours_savings, 2);
        $monthly['monthlyGenerationSaving'] = round($monthly_generation_saving, 2);
        $monthly['monthlyTotalSaving'] = round($monthly['monthlyPeakHoursSaving'] + $monthly['monthlyGenerationSaving']);
//        return $monthly;

        $yearly_charge_arr = $this->unitConversion($yearly_charge_energy, 'kWh');
        $yearly_discharge_arr = $this->unitConversion($yearly_discharge_energy, 'kWh');
        $yearly_gen_arr = $this->unitConversion($yearly_generation, 'kWh');
        $yearly_con_arr = $this->unitConversion($yearly_consumption, 'kWh');
        $yearly_grid_arr = $this->unitConversion($yearly_grid, 'kWh');
        $yearly_buy_arr = $this->unitConversion($yearly_bought_energy, 'kWh');
        $yearly_sell_arr = $this->unitConversion($yearly_sell_energy, 'kWh');
        $yearly_revenue_arr = $this->unitConversion($yearly_saving, 'PKR');
        $yearly_net_grid_arr = $this->unitConversion(((double)$yearly_bought_energy - (double)$yearly_sell_energy), 'kWh');
        $yearly['generation'] = round($yearly_gen_arr[0], 2) . ' ' . $yearly_gen_arr[1];
        $yearly['consumption'] = round($yearly_con_arr[0], 2) . ' ' . $yearly_con_arr[1];
        $yearly['grid'] = round($yearly_grid_arr[0], 2) . ' ' . $yearly_grid_arr[1];
        $yearly['boughtEnergy'] = round($yearly_buy_arr[0], 2) . ' ' . $yearly_buy_arr[1];
        $yearly['sellEnergy'] = round($yearly_sell_arr[0], 2) . ' ' . $yearly_sell_arr[1];
        $yearly['netGrid'] = round($yearly_net_grid_arr[0], 2) . ' ' . $yearly_net_grid_arr[1];
        $yearly['netGridSign'] = ((double)$yearly_bought_energy - (double)$yearly_sell_energy) < 0 ? '-' : '';
        $yearly['revenue'] = round($yearly_revenue_arr[0], 2) . '' . $yearly_revenue_arr[1];
        $yearly['yearlyChargeEnergy'] = round($yearly_charge_arr[0], 2) . '' . $yearly_charge_arr[1];
        $yearly['yearlyDischargeEnergy'] = round($yearly_discharge_arr[0], 2) . '' . $yearly_discharge_arr[1];
        $yearly['yearlyPeakHoursSaving'] = round($yearly_peak_hours_savings, 2);
        $yearly['yearlyGenerationSaving'] = round($yearly_generation_saving, 2);
        $yearly['yearlyTotalCostSaving'] = round($yearly['yearlyPeakHoursSaving'] + $yearly['yearlyGenerationSaving']);

        $total_gen_arr = $this->unitConversion($total_generation, 'kWh');
        $total_con_arr = $this->unitConversion($total_consumption, 'kWh');
        $total_grid_arr = $this->unitConversion($total_grid, 'kWh');
        $total_buy_arr = $this->unitConversion($total_bought_energy, 'kWh');
        $total_sell_arr = $this->unitConversion($total_sell_energy, 'kWh');
        $total_charge_arr = $this->unitConversion($total_charge_energy, 'kWh');
        $total_discharge_arr = $this->unitConversion($total_discharge_energy, 'kWh');
        $total_revenue_arr = $this->unitConversion($total_saving, 'PKR');
        $total_net_grid_arr = $this->unitConversion(((double)$total_bought_energy - (double)$total_sell_energy), 'kWh');
        $total['generation'] = round($total_gen_arr[0], 2) . ' ' . $total_gen_arr[1];
        $total['consumption'] = round($total_con_arr[0], 2) . ' ' . $total_con_arr[1];
        $total['grid'] = round($total_grid_arr[0], 2) . ' ' . $total_grid_arr[1];
        $total['boughtEnergy'] = round($total_buy_arr[0], 2) . ' ' . $total_buy_arr[1];
        $total['sellEnergy'] = round($total_sell_arr[0], 2) . ' ' . $total_sell_arr[1];
        $total['netGrid'] = round($total_net_grid_arr[0], 2) . ' ' . $total_net_grid_arr[1];
        $total['netGridSign'] = ((double)$total_bought_energy - (double)$total_sell_energy) < 0 ? '-' : '';
        $total['revenue'] = round($total_revenue_arr[0], 2) . '' . $total_revenue_arr[1];
        $total['totalChargeEnergy'] = round($total_charge_arr[0], 2) . '' . $total_charge_arr[1];
        $total['totalDischargeEnergy'] = round($total_discharge_arr[0], 2) . '' . $total_discharge_arr[1];

        $minus_3_hours = date('Y-m-d H:i:s', strtotime('-3 hours', strtotime(date('Y-m-d H:i:s'))));
        $weather = Weather::where('city', $plant->city)->whereBetween('created_at', [$minus_3_hours, date('Y-m-d H:i:s')])->first();


        $daily_inverter_details = array();

        for ($i = 0; $i < count($plant_inverters); $i++) {

            $daily_inverter_details_obj = (object)[];

            $daily_inverter_details_obj_gen = DailyInverterDetail::where('plant_id', '=', $id)->where('siteId', $plant_inverters[$i]->site_id)->where('dv_inverter', $plant_inverters[$i]->dv_inverter)->whereDate('created_at', '=', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
            $site_id_status = PlantSite::where('plant_id', '=', $id)->where('site_id', $plant_inverters[$i]->site_id)->first();
            $fault_upd = FaultAlarmLog::where('plant_id', '=', $id)->where('siteId', $plant_inverters[$i]->site_id)->where('dv_inverter', $plant_inverters[$i]->dv_inverter)->orderBy('created_at', 'DESC')->first();

            $daily_inverter_details_obj->last_alert = $fault_upd ? $fault_upd->created_at : '-----';
            $daily_inverter_details_obj->serial_no = $plant_inverters[$i]->dv_inverter_serial_no;
            $daily_inverter_details_obj->type = 'inverter';
            $daily_inverter_details_obj->daily_generation = $daily_inverter_details_obj_gen ? $daily_inverter_details_obj_gen->daily_generation : 0;
            $daily_inverter_details_obj->site_status = $site_id_status && $site_id_status->online_status ? $site_id_status->online_status : 'N';
            $daily_inverter_details_obj->site_id = $plant_inverters[$i]->site_id;
            $daily_inverter_details_obj->battery_remaining = '--';
            $daily_inverter_details_obj->battery_capacity = '--';
            $daily_inverter_details[] = $daily_inverter_details_obj;

        }
        $plantBattery = StationBattery::where('plant_id', '=', $id)->groupBy('plant_id')->first();
        if ($plantBattery) {
            $plant_battery_details_obj = (object)[];
            $plant_battery_details_obj->site_id = '--';
            $plant_battery_details_obj->serial_no = '--';
            $plant_battery_details_obj->daily_generation = '--';
            $plant_battery_details_obj->type = 'battery';
            $plant_battery_details_obj->site_status = '0';
            $plant_battery_details_obj->site_status = '0';
            $plant_battery_details_obj->battery_remaining = $batteryRemaining;
            $plant_battery_details_obj->battery_capacity = $batteryCapacity;
            $plant_battery_details_obj->last_alert = '-----';
            $daily_inverter_details[] = $plant_battery_details_obj;
        }

        $daily_inverter_details = collect($daily_inverter_details);
        $daily_inverter_details = $daily_inverter_details->groupBy('site_id');


        $tickets = DB::table('tickets')
            ->join('ticket_sources', 'tickets.source', 'ticket_sources.id')
            ->join('ticket_priority', 'tickets.priority', 'ticket_priority.id')
            ->join('ticket_status', 'tickets.status', 'ticket_status.id')
            ->select('tickets.id', 'tickets.title', 'tickets.closed_time', 'tickets.created_at',
                'ticket_sources.name as source_name', 'ticket_priority.priority as priority_name',
                'ticket_status.status as status_name')
            ->where('tickets.plant_id', $id)
            ->where('tickets.status', '!=', 6)
            ->orderBy('tickets.created_at', 'DESC')
            ->get();

        foreach ($tickets as $key => $ticket) {
            $agents_array = [];
            $ticket_agents = TicketAgent::where('ticket_id', '=', $ticket->id)->get();
            //dd($ticket_agents);
            foreach ($ticket_agents as $key => $ticket_agent) {
                $ticket_agents_name = User::where('id', '=', $ticket_agent->employee_id)->first();
                array_push($agents_array, $ticket_agents_name->name);
            }
            $ticket->agents = implode(',', $agents_array);
        }
//        $plantValues = Plant::where('id',)
//        return $plant;

        $dataArray = [

            'plant' => $plant,
            'current' => $current,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly' => $yearly,
            'weather' => $weather,
            'daily_inverter_details' => $daily_inverter_details,
            'tickets' => $tickets,
            'total' => $total,
            'battery_remaining' => $batteryRemaining,
            'battery_backup_formula' => $batteryBackupFormula,
            'battery_backup_max_load' => $batteryBackupMaxLoadFormula
        ];
//        return $daily_inverter_details;

        return view('admin.plant.userPlantDetails', $dataArray);
    }
//    public function userPlantDetail($id = 0)
//    {
//        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {
//
//            $plant_arr = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id');
//            $plant_arr = $plant_arr->toArray();
//
//            if (!empty($plant_arr) && (!in_array($id, $plant_arr))) {
//                return redirect()->back()->with('error', 'You have no access of that plant!');
//            }
//        }
//        if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
//
//            $plant_arr = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
//            $plant_arr = $plant_arr->toArray();
//
//            if (!empty($plant_arr) && !in_array((string)$id, $plant_arr)) {
//                return redirect()->back()->with('error', 'You have no access of that plant!');
//            } else if (empty($plant_arr)) {
//                return redirect()->back()->with('error', 'You have no access of that plant!');
//            }
//        }
//
//        $where_array = array();
//        if (Auth::user()->roles != 1 && Auth::user()->roles != 2) {
//
//            $company_id = Auth::user()->company_id;
//            $where_array['company_id'] = $company_id;
//        }
//
//        $plant = Plant::with('latest_inverter_emi_details')->where('id', $id)->first();
//
//        if (!$plant) {
//            return redirect()->back()->with('error', 'No plant found!');
//        }
//
//        $plant['system_type_id'] = $plant->system_type;
//        $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
//        $plant['system_type'] = SystemType::find($plant->system_type)->type;
//
//        $pl_sites_array = PlantSite::where('plant_id', $id)->pluck('site_id')->toArray();
//
//        $plant_inverters = InverterSerialNo::whereIn('site_id', $pl_sites_array)->get();
//
//        $invertersDailyGeneration = DailyProcessedPlantDetail::where('plant_id', $id)->whereDate('created_at', date('Y-m-d'))->sum('dailyGeneration');
//        $invertersDailyGenerationConverted = $this->unitConversion($invertersDailyGeneration, 'kWh');
//
//        $invertersMonthlyGeneration = MonthlyProcessedPlantDetail::where('plant_id', $id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->sum('monthlyGeneration');
//        $invertersMonthlyGenerationConverted = $this->unitConversion($invertersMonthlyGeneration, 'kWh');
//
//        $invertersAnnualGeneration = YearlyProcessedPlantDetail::where('plant_id', $id)->whereYear('created_at', date('Y'))->sum('yearlyGeneration');
//        $invertersAnnualGenerationConverted = $this->unitConversion($invertersAnnualGeneration, 'kWh');
//
//        $invertersTotalGeneration = TotalProcessedPlantDetail::where('plant_id', $id)->sum('plant_total_generation');
//        $invertersTotalGenerationConverted = $this->unitConversion($invertersTotalGeneration, 'kWh');
//
//        $plant->setAttribute('daily_generation', round($invertersDailyGenerationConverted[0], 2) . ' ' . $invertersDailyGenerationConverted[1]);
//        $plant->setAttribute('monthly_generation', round($invertersMonthlyGenerationConverted[0], 2) . ' ' . $invertersMonthlyGenerationConverted[1]);
//        $plant->setAttribute('annual_generation', round($invertersAnnualGenerationConverted[0], 2) . ' ' . $invertersAnnualGenerationConverted[1]);
//        $plant->setAttribute('total_generation', round($invertersTotalGenerationConverted[0], 2) . ' ' . $invertersTotalGenerationConverted[1]);
//
//        $plant != null ? Session::put(['plant_name' => $plant->plant_name]) : '';
//        $inverterTotalACOutputPower = 0;
//        $inverterTotalDCPower = 0;
//
//        $mpptTotalNumber = 0;
//
//        if ($plant->meter_type == 'Huawei') {
//
//            $mpptTotalNumber = 24;
//        } else if ($plant->meter_type == 'SunGrow') {
//
//            $mpptTotalNumber = 10;
//        } else if ($plant->meter_type == 'Solis') {
//
//            $mpptTotalNumber = 3;
//        }
//
//        if ($plant_inverters && count($plant_inverters) > 0) {
//
//            foreach ($plant_inverters as $key => $inverter) {
//
//                $inverter->setAttribute('pv_values', InverterMPPTDetail::where(['plant_id' => $id, 'dv_inverter' => $inverter->dv_inverter])->whereDate('collect_time', date('Y-m-d'))->exists() ? InverterMPPTDetail::where(['plant_id' => $id, 'dv_inverter' => $inverter->dv_inverter])->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'DESC')->limit($mpptTotalNumber)->get() : []);
//
//                $todayGeneration = DailyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('created_at', date('Y-m-d'))->first();
//                $todayGenerationConverted = $todayGeneration ? $this->unitConversion($todayGeneration->daily_generation, 'kWh') : [0, 'kWh'];
//
//                $yesterdayGeneration = DailyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDay('created_at', date('d', strtotime("-1 days")))->first();
//                $yesterdayGenerationConverted = $yesterdayGeneration ? $this->unitConversion($yesterdayGeneration->daily_generation, 'kWh') : [0, 'kWh'];
//
//                $lastMonthGeneration = MonthlyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereMonth('created_at', date('m', strtotime("-1 months")))->first();
//                $lastMonthGenerationConverted = $lastMonthGeneration ? $this->unitConversion($lastMonthGeneration->monthly_generation, 'kWh') : [0, 'kWh'];
//
//                $lastYearGeneration = YearlyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereYear('created_at', date('Y', strtotime("-1 years")))->first();
//                $lastYearGenerationConverted = $lastYearGeneration ? $this->unitConversion($lastYearGeneration->yearly_generation, 'kWh') : [0, 'kWh'];
//
//                $totalGeneration = YearlyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->sum('yearly_generation');
//                $totalGenerationConverted = $this->unitConversion($totalGeneration, 'kWh');
//
//                $inverterCurrentDataLogTime = InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->exists() ? InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
//
//                $inverterFinalCurrentDataDateTime = $this->previousTenMinutesDateTime($inverterCurrentDataLogTime);
//
//                $inverterDetailObject = InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $inverterFinalCurrentDataDateTime)->orderBy('collect_time', 'DESC')->first();
//                $powerConverted = $inverterDetailObject && isset($inverterDetailObject->inverterPower) ? $this->unitConversion($inverterDetailObject->inverterPower, 'kW') : [0, 'kW'];
//                $inverterTotalACOutputPower += $inverterDetailObject && $inverterDetailObject->inverterPower ? $inverterDetailObject->inverterPower : 0;
//
//                $siteInverterDetailObject = SiteInverterDetail::where('site_id', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->first();
//                $installedDCPowerConverted = $siteInverterDetailObject && isset($siteInverterDetailObject->dv_installed_dc_power) ? $this->unitConversion($siteInverterDetailObject->dv_installed_dc_power, 'kW') : [0, 'kW'];
//
//                $work_state_data = DB::table('fault_and_alarms')
//                    ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
//                    ->select('fault_and_alarms.description')
//                    ->where('fault_alarm_log.plant_id', $id)
//                    ->where('fault_alarm_log.siteId', $inverter->site_id)
//                    ->where('fault_alarm_log.dv_inverter', $inverter->dv_inverter)
//                    ->where('fault_alarm_log.status', 'Y')
//                    ->orderBy('fault_alarm_log.created_at', 'DESC')
//                    ->first();
//                $inverterStatusDetail = InverterSerialNo::where('plant_id', $id)->where('site_id', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->first();
//                $inverter->setAttribute('start_time', $inverterDetailObject && isset($inverterDetailObject->start_time) ? $inverterDetailObject->start_time : null);
//                $inverter->setAttribute('inverter_status', $inverterStatusDetail && isset($inverterStatusDetail->status) ? $inverterStatusDetail->status : '-----');
//
//                $inverterTotalDCPower += $inverterDetailObject && isset($inverterDetailObject->mpptPower) ? $inverterDetailObject->mpptPower : 0;
//                $inverter->setAttribute('dc_input_power', $inverterDetailObject && isset($inverterDetailObject->mpptPower) ? round($inverterDetailObject->mpptPower, 2) : 0);
//                $inverter->setAttribute('installed_dc_input_power', round($installedDCPowerConverted[0], 2) . ' ' . $installedDCPowerConverted[1]);
//                $inverter->setAttribute('today_generation', round($todayGenerationConverted[0], 2) . ' ' . $todayGenerationConverted[1]);
//                $inverter->setAttribute('yesterday_generation', round($yesterdayGenerationConverted[0], 2) . ' ' . $yesterdayGenerationConverted[1]);
//                $inverter->setAttribute('last_month_generation', round($lastMonthGenerationConverted[0], 2) . ' ' . $lastMonthGenerationConverted[1]);
//                $inverter->setAttribute('last_year_generation', round($lastYearGenerationConverted[0], 2) . ' ' . $lastYearGenerationConverted[1]);
//                $inverter->setAttribute('total_generation', round($totalGenerationConverted[0], 2) . ' ' . $totalGenerationConverted[1]);
//                $inverter->setAttribute('up_time', $inverterDetailObject && isset($inverterDetailObject->inverterUptime) ? round($inverterDetailObject->inverterUptime, 2) : 0);
//                $inverter->setAttribute('temperature', $inverterDetailObject && isset($inverterDetailObject->inverterTemperature) ? $inverterDetailObject->inverterTemperature : 0);
//                $inverter->setAttribute('inverter_state', $inverterDetailObject && isset($inverterDetailObject->inverterState) ? $inverterDetailObject->inverterState : '-----');
//                $inverter->setAttribute('inverter_state_code', $inverterDetailObject && isset($inverterDetailObject->inverterStateCode) ? $inverterDetailObject->inverterStateCode : 0);
//                $inverter->setAttribute('power', $powerConverted);
//                $inverter->setAttribute('phase_voltage_r', $inverterDetailObject && isset($inverterDetailObject->phase_voltage_r) ? round($inverterDetailObject->phase_voltage_r, 2) : 0);
//                $inverter->setAttribute('phase_voltage_s', $inverterDetailObject && isset($inverterDetailObject->phase_voltage_s) ? round($inverterDetailObject->phase_voltage_s, 2) : 0);
//                $inverter->setAttribute('phase_voltage_t', $inverterDetailObject && isset($inverterDetailObject->phase_voltage_t) ? round($inverterDetailObject->phase_voltage_t, 2) : 0);
//                $inverter->setAttribute('phase_current_r', $inverterDetailObject && isset($inverterDetailObject->phase_current_r) ? round($inverterDetailObject->phase_current_r, 2) : 0);
//                $inverter->setAttribute('phase_current_s', $inverterDetailObject && isset($inverterDetailObject->phase_current_s) ? round($inverterDetailObject->phase_current_s, 2) : 0);
//                $inverter->setAttribute('phase_current_t', $inverterDetailObject && isset($inverterDetailObject->phase_current_t) ? round($inverterDetailObject->phase_current_t, 2) : 0);
//                $inverter->setAttribute('frequency', $inverterDetailObject && isset($inverterDetailObject->frequency) ? round($inverterDetailObject->frequency, 2) : 0);
//                $inverter->setAttribute('total_pv_number', (InverterMPPTDetail::where(['plant_id' => $id, 'dv_inverter' => $inverter->dv_inverter])->exists() ? InverterMPPTDetail::select(DB::raw('Max(mppt_number) as mppt_number'))->where(['plant_id' => $id, 'dv_inverter' => $inverter->dv_inverter])->where('mppt_voltage', '!=', 0)->groupBy('collect_time')->first()->mppt_number : 0));
//            }
//        }
//
//        $invertersOutputPowerConverted = $this->unitConversion($inverterTotalACOutputPower, 'kW');
//        $plant->setAttribute('ac_output_power', round($invertersOutputPowerConverted[0], 2) . ' ' . $invertersOutputPowerConverted[1]);
//
//        $current_generation = 0;
//        $current_consumption = 0;
//        $current_grid = 0;
//        $current_grid_type = '';
//        $current = array();
//        $currentDataValues = array();
//        $daily = array();
//        $monthly = array();
//        $yearly = array();
//        $total = array();
//        $weather = array();
//
//        $currentDataValues['dc_power'] = $inverterTotalDCPower;
//
//        $totalDCPowerConverted = $inverterTotalDCPower ? $this->unitConversion($inverterTotalDCPower, 'kW') : [0, 'kW'];
//        $current['dc_power'] = round($totalDCPowerConverted[0], 2) . ' ' . $totalDCPowerConverted[1];
//
//        $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $id)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
//
//        $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
//
//        $current_data = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type', 'comm_failed', 'created_at')->where('plant_id', $id)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $finalCurrentDataDateTime)->orderBy('collect_time', 'desc')->first();
//        $current_generation = $current_data ? (double)$current_data->current_generation : 0;
//        $current_consumption = $current_data ? (double)$current_data->current_consumption : 0;
//        $current_grid = $current_data ? (double)$current_data->current_grid : 0;
//        $current_grid_type = $current_data ? $current_data->grid_type : '';
//        $current['date'] = $current_data ? $current_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
//        $current['comm_fail'] = $current_data ? (int)$current_data->comm_failed : 0;
//
//        $plant_sites_array = PlantSite::where('plant_id', $id)->pluck('site_id');
//
//        $daily_data = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'dailyIrradiance', 'created_at')->where('plant_id', $id)->whereDate('created_at', date('Y-m-d'))->orderBy('updated_at', 'DESC')->first();
//        $daily_generation = $daily_data ? (double)$daily_data->dailyGeneration : 0;
//        $daily_consumption = $daily_data ? (double)$daily_data->dailyConsumption : 0;
//        $daily_grid = $daily_data ? (double)$daily_data->dailyGridPower : 0;
//        $daily_bought_energy = $daily_data ? (double)$daily_data->dailyBoughtEnergy : 0;
//        $daily_sell_energy = $daily_data ? (double)$daily_data->dailySellEnergy : 0;
//        $daily_saving = $daily_data ? (double)$daily_data->dailySaving : 0;
//        $daily_irradiance = DailyProcessedPlantEMIDetail::where('plant_id', $id)->whereDate('created_at', date('Y-m-d'))->exists() ? DailyProcessedPlantEMIDetail::where('plant_id', $id)->whereDate('created_at', date('Y-m-d'))->orderBy('updated_at', 'DESC')->first()->daily_irradiance : 0;
//        $daily['date'] = $daily_data ? $daily_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
//
//        $monthly_data = MonthlyProcessedPlantDetail::select('monthlyGeneration', 'monthlyConsumption', 'monthlyGridPower', 'monthlyBoughtEnergy', 'monthlySellEnergy', 'monthlySaving', 'monthlyIrradiance', 'created_at')->where('plant_id', $id)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
//        $monthly_generation = $monthly_data ? (double)$monthly_data->monthlyGeneration : 0;
//        $monthly_consumption = $monthly_data ? (double)$monthly_data->monthlyConsumption : 0;
//        $monthly_grid = $monthly_data ? (double)$monthly_data->monthlyGridPower : 0;
//        $monthly_bought_energy = $monthly_data ? (double)$monthly_data->monthlyBoughtEnergy : 0;
//        $monthly_sell_energy = $monthly_data ? (double)$monthly_data->monthlySellEnergy : 0;
//        $monthly_saving = $monthly_data ? (double)$monthly_data->monthlySaving : 0;
//        $monthly_irradiance = MonthlyProcessedPlantEMIDetail::where('plant_id', $id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->exists() ? MonthlyProcessedPlantEMIDetail::where('plant_id', $id)->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->first()->monthly_irradiance : 0;
//        $monthly['date'] = $monthly_data ? $monthly_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
//
//        $yearly_data = YearlyProcessedPlantDetail::select('yearlyGeneration', 'yearlyConsumption', 'yearlyGridPower', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearlySaving', 'yearlyIrradiance', 'created_at')->where('plant_id', $id)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
//        $yearly_generation = $yearly_data ? (double)$yearly_data->yearlyGeneration : 0;
//        $yearly_consumption = $yearly_data ? (double)$yearly_data->yearlyConsumption : 0;
//        $yearly_grid = $yearly_data ? (double)$yearly_data->yearlyGridPower : 0;
//        $yearly_bought_energy = $yearly_data ? (double)$yearly_data->yearlyBoughtEnergy : 0;
//        $yearly_sell_energy = $yearly_data ? (double)$yearly_data->yearlySellEnergy : 0;
//        $yearly_saving = $yearly_data ? (double)$yearly_data->yearlySaving : 0;
//        $yearly_irradiance = YearlyProcessedPlantEMIDetail::where('plant_id', $id)->whereYear('created_at', date('Y'))->exists() ? YearlyProcessedPlantEMIDetail::where('plant_id', $id)->whereYear('created_at', date('Y'))->first()->yearly_irradiance : 0;
//        $yearly['date'] = $yearly_data ? $yearly_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
//
//        $total_data = TotalProcessedPlantDetail::where('plant_id', $id)->first();
//        $total_generation = $total_data ? (double)$total_data->plant_total_generation : 0;
//        $total_consumption = $total_data ? (double)$total_data->plant_total_consumption : 0;
//        $total_grid = $total_data ? (double)$total_data->plant_total_grid : 0;
//        $total_bought_energy = $total_data ? (double)$total_data->plant_total_buy_energy : 0;
//        $total_sell_energy = $total_data ? (double)$total_data->plant_total_sell_energy : 0;
//        $total_saving = $total_data ? (double)$total_data->plant_total_saving : 0;
//        $total_irradiance = TotalProcessedPlantEMIDetail::where('plant_id', $id)->exists() ? TotalProcessedPlantEMIDetail::where('plant_id', $id)->first()->total_irradiance : 0;
//
//        $curr_gen_arr = $this->unitConversion($current_generation, 'kW');
//        $curr_con_arr = $this->unitConversion($current_consumption, 'kW');
//        $curr_grid_arr = $this->unitConversion($current_grid, 'kW');
//        $currentDataValues['comm_fail'] = $current_data ? (int)$current_data->comm_failed : 0;
//        $currentDataValues['generation'] = $current_generation;
//        $currentDataValues['consumption'] = $current_consumption;
//        $currentDataValues['grid'] = $current_grid;
//        $currentDataValues['grid_type'] = $current_grid_type;
//        $current['generation'] = round($curr_gen_arr[0], 2) . ' ' . $curr_gen_arr[1];
//        $current['consumption'] = round($curr_con_arr[0], 2) . ' ' . $curr_con_arr[1];
//        $current['grid'] = round($curr_grid_arr[0], 2) . ' ' . $curr_grid_arr[1];
//        $current['grid_type'] = $current_grid_type;
//
//        $daily_gen_arr = $this->unitConversion($daily_generation, 'kWh');
//        $daily_con_arr = $this->unitConversion($daily_consumption, 'kWh');
//        $daily_grid_arr = $this->unitConversion($daily_grid, 'kWh');
//        $daily_buy_arr = $this->unitConversion($daily_bought_energy, 'kWh');
//        $daily_sell_arr = $this->unitConversion($daily_sell_energy, 'kWh');
//        $daily_revenue_arr = $this->unitConversion($daily_saving, 'PKR');
//        $daily_net_grid_arr = $this->unitConversion(((double)$daily_bought_energy - (double)$daily_sell_energy), 'kWh');
//        $daily['generation'] = round($daily_gen_arr[0], 2) . ' ' . $daily_gen_arr[1];
//        $daily['consumption'] = round($daily_con_arr[0], 2) . ' ' . $daily_con_arr[1];
//        $daily['boughtEnergy'] = round($daily_buy_arr[0], 2) . ' ' . $daily_buy_arr[1];
//        $daily['sellEnergy'] = round($daily_sell_arr[0], 2) . ' ' . $daily_sell_arr[1];
//        $daily['irradiance'] = round($daily_irradiance, 2);
//        $daily['revenue'] = round($daily_revenue_arr[0], 2) . '' . $daily_revenue_arr[1];
//
//        $monthly_gen_arr = $this->unitConversion($monthly_generation, 'kWh');
//        $monthly_con_arr = $this->unitConversion($monthly_consumption, 'kWh');
//        $monthly_grid_arr = $this->unitConversion($monthly_grid, 'kWh');
//        $monthly_buy_arr = $this->unitConversion($monthly_bought_energy, 'kWh');
//        $monthly_sell_arr = $this->unitConversion($monthly_sell_energy, 'kWh');
//        $monthly_revenue_arr = $this->unitConversion($monthly_saving, 'PKR');
//        $monthly_net_grid_arr = $this->unitConversion(((double)$monthly_bought_energy - (double)$monthly_sell_energy), 'kWh');
//        $monthly['generation'] = round($monthly_gen_arr[0], 2) . ' ' . $monthly_gen_arr[1];
//        $monthly['consumption'] = round($monthly_con_arr[0], 2) . ' ' . $monthly_con_arr[1];
//        $monthly['boughtEnergy'] = round($monthly_buy_arr[0], 2) . ' ' . $monthly_buy_arr[1];
//        $monthly['sellEnergy'] = round($monthly_sell_arr[0], 2) . ' ' . $monthly_sell_arr[1];
//        $monthly['revenue'] = round($monthly_revenue_arr[0], 2) . '' . $monthly_revenue_arr[1];
//        $monthly['irradiance'] = round($monthly_irradiance, 2);
//
//        $yearly_gen_arr = $this->unitConversion($yearly_generation, 'kWh');
//        $yearly_con_arr = $this->unitConversion($yearly_consumption, 'kWh');
//        $yearly_grid_arr = $this->unitConversion($yearly_grid, 'kWh');
//        $yearly_buy_arr = $this->unitConversion($yearly_bought_energy, 'kWh');
//        $yearly_sell_arr = $this->unitConversion($yearly_sell_energy, 'kWh');
//        $yearly_revenue_arr = $this->unitConversion($yearly_saving, 'PKR');
//        $yearly_net_grid_arr = $this->unitConversion(((double)$yearly_bought_energy - (double)$yearly_sell_energy), 'kWh');
//        $yearly['generation'] = round($yearly_gen_arr[0], 2) . ' ' . $yearly_gen_arr[1];
//        $yearly['consumption'] = round($yearly_con_arr[0], 2) . ' ' . $yearly_con_arr[1];
//        $yearly['boughtEnergy'] = round($yearly_buy_arr[0], 2) . ' ' . $yearly_buy_arr[1];
//        $yearly['sellEnergy'] = round($yearly_sell_arr[0], 2) . ' ' . $yearly_sell_arr[1];
//        $yearly['revenue'] = round($yearly_revenue_arr[0], 2) . '' . $yearly_revenue_arr[1];
//        $yearly['irradiance'] = round($yearly_irradiance, 2);
//
//        $total_gen_arr = $this->unitConversion($total_generation, 'kWh');
//        $total_con_arr = $this->unitConversion($total_consumption, 'kWh');
//        $total_grid_arr = $this->unitConversion($total_grid, 'kWh');
//        $total_buy_arr = $this->unitConversion($total_bought_energy, 'kWh');
//        $total_sell_arr = $this->unitConversion($total_sell_energy, 'kWh');
//        $total_revenue_arr = $this->unitConversion($total_saving, 'PKR');
//        $total_net_grid_arr = $this->unitConversion(((double)$total_bought_energy - (double)$total_sell_energy), 'kWh');
//        $total['generation'] = round($total_gen_arr[0], 2) . ' ' . $total_gen_arr[1];
//        $total['consumption'] = round($total_con_arr[0], 2) . ' ' . $total_con_arr[1];
//        $total['boughtEnergy'] = round($total_buy_arr[0], 2) . ' ' . $total_buy_arr[1];
//        $total['sellEnergy'] = round($total_sell_arr[0], 2) . ' ' . $total_sell_arr[1];
//        $total['revenue'] = round($total_revenue_arr[0], 2) . '' . $total_revenue_arr[1];
//        $total['irradiance'] = round($total_irradiance, 2);
//
//        for ($i = 1; $i <= 4; $i++) {
//
//            $weather['dayName' . $i] = date('D', strtotime(date('Y-m-d')));
//        }
//
//        //$weather = Weather::where('city',$plant->city)->whereBetween('created_at',[$minus3Hours,date('Y-m-d H:i:s')])->get();
//        //$weather_1 = Weather::where('city',$plant->city)->whereBetween('created_at',[$minus3Hours,date('Y-m-d H:i:s')])->get();
//        //$weather_2 = Weather::where('city',$plant->city)->whereBetween('created_at',[$minus3Hours,date('Y-m-d H:i:s')])->get();
//        //$weather_3 = Weather::where('city',$plant->city)->whereBetween('created_at',[$minus3Hours,date('Y-m-d H:i:s')])->get();
//
//        $envArray = array();
//
//        $envPlanting = Setting::where('perimeter', 'env_planting')->pluck('value')[0];
//        $envReduction = Setting::where('perimeter', 'env_reduction')->pluck('value')[0];
//
//        $plantTotalGeneration = TotalProcessedPlantDetail::where('plant_id', $plant->id)->sum('plant_total_generation');
//
//        $envArray['tree'] = round($plantTotalGeneration * $envPlanting, 2);
//        $envArray['c02'] = round($plantTotalGeneration * $envReduction, 2);
//
//        $maxCronJobID = ProcessedCurrentVariable::whereDate('collect_time', date('Y-m-d'))->max('processed_cron_job_id');
//        $maxCronJobIDYesterday = ProcessedCurrentVariable::whereDate('created_at', date('Y-m-d', strtotime("-1 days")))->max('processed_cron_job_id');
//        $powerValue = 0;
//        $capacityValue = 0;
//        $powerArray = array();
//        $alertArray = array();
//        $consumptionArray = array();
//
//        $consumptionArray['current_consumption'] = $current['consumption'];
//        $consumptionArray['actual_value'] = strtotime(date('Y-m-d 23:59:59'));
//        $consumptionArray['total_value'] = strtotime(date('Y-m-d H:i:s'));
//
//        //if($maxCronJobID && $maxCronJobID > 0) {
//
//        $powerValue = ProcessedCurrentVariable::where('plant_id', $id)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $finalCurrentDataDateTime)->exists() ? ProcessedCurrentVariable::where('plant_id', $id)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $finalCurrentDataDateTime)->orderBy('collect_time', 'DESC')->first()->current_generation : 0;
//        //}
//
//        $capacityValue = Plant::where('id', $id)->sum('capacity');
//
//        $currentPower = $this->unitConversion($powerValue, 'kW');
//        $totalCapacity = $this->unitConversion($capacityValue, 'kWp');
//
//        $powerArray['current_power'] = round($currentPower[0], 2) . ' ' . $currentPower[1];
//        $powerArray['total_capacity'] = round($totalCapacity[0], 2) . ' ' . $totalCapacity[1];
//
//        $alertArray['alarm'] = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
//            ->select('fault_alarm_log.*')
//            ->where('fault_alarm_log.plant_id', $id)
//            ->where('fault_alarm_log.status', 'Y')
//            ->where('fault_and_alarms.type', 'Alarm')
//            ->count();
//
//        $alertArray['fault'] = FaultAlarmLog::join('fault_and_alarms', 'fault_alarm_log.fault_and_alarm_id', 'fault_and_alarms.id')
//            ->select('fault_alarm_log.*')
//            ->where('fault_alarm_log.plant_id', $id)
//            ->where('fault_alarm_log.status', 'Y')
//            ->where('fault_and_alarms.type', 'Fault')
//            ->count();
//
//        $plantPowerGraphData = $this->plantPowerGraph($powerValue, $capacityValue);
//        $plantConsumptionGraphData = $this->plantConsumptionGraph($consumptionArray['actual_value'], $consumptionArray['total_value']);
//        $weatherData = [];
//        $dateTime = new \DateTime();
//        $date = $dateTime->format('Y-m-d');
//        $plantData = Plant::where('id', $id)->first();
//        if ($plantData) {
//            $plantCity = $plantData->city;
//        } else {
//            $plantCity = '';
//        }
//
//        for ($i = 0; $i < 4; $i++) {
//
//            $weather['todayMin'] = Weather::whereDate('created_at', $date)->where('city', $plantCity)->min('temperature_min');
//            $weather['todayMax'] = Weather::whereDate('created_at', $date)->where('city', $plantCity)->max('temperature_max');
//            $weather['sunrise'] = Weather::whereDate('created_at', $date)->where('city', $plantCity)->max('sunrise');
//            $weather['sunset'] = Weather::whereDate('created_at', $date)->where('city', $plantCity)->max('sunset');
//            $weather['icon'] = Weather::whereDate('created_at', $date)->where('city', $plantCity)->exists() ? Weather::whereDate('created_at', $date)->where('city', $plantCity)->first()->icon : '01d';
//            $dowMap = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
//            $dow_numeric = $dateTime->format('w');
//            $dowMap[$dow_numeric];
//            $weather['day'] = $dowMap[$dow_numeric];
//            array_push($weatherData, $weather);
//            $date = $dateTime->modify("+1 days")->format('Y-m-d');
//        }
//
//        $plantAllInvertersArray = SiteInverterDetail::where('plant_id', $id)->where('dv_inverter_type', 1)->pluck('dv_inverter')->toArray();
//        $autoloadTime = Setting::where('perimeter', 'autoload_seconds')->pluck('value')[0];
////        return $plantPowerGraphData;
//
//        $data = [
//
//            'plant' => $plant,
//            'current' => $current,
//            'currentDataValues' => $currentDataValues,
//            'daily' => $daily,
//            'monthly' => $monthly,
//            'yearly' => $yearly,
//            'plantPowerGraphData' => $plantPowerGraphData,
//            'powerArray' => $powerArray,
//            'alertArray' => $alertArray,
//            'total' => $total,
//            'plantInverters' => $plant_inverters,
//            'weather' => $weather,
//            'envArray' => $envArray,
//            'consumptionArray' => $consumptionArray,
//            'plantConsumptionGraphData' => $plantConsumptionGraphData,
//            'weatherDetails' => $weatherData,
//            'plantAllInvertersArray' => $plantAllInvertersArray,
//            'autoloadTime' => $autoloadTime
//        ];
////        return $data;
//
//        return view('admin.plant.userPlantDetails', $data);
//    }
//    public function plantsSavingGraph(Request $request)
//    {
//        $total_data = TotalProcessedPlantDetail::where('plant_id', $id)->first();
//        $total_generation = $total_data ? (double)$total_data->plant_total_generation : 0;
//        $total_consumption = $total_data ? (double)$total_data->plant_total_consumption : 0;
//        $total_grid = $total_data ? (double)$total_data->plant_total_grid : 0;
//        $total_bought_energy = $total_data ? (double)$total_data->plant_total_buy_energy : 0;
//        $total_sell_energy = $total_data ? (double)$total_data->plant_total_sell_energy : 0;
//        $total_saving = $total_data ? (double)$total_data->plant_total_saving : 0;
//        $daily_revenue_arr = $this->unitConversion($daily_saving, 'PKR');
//        $daily_gen_arr = $this->unitConversion($daily_generation, 'kWh');
//        $monthly_data = MonthlyProcessedPlantDetail::select('monthlyGeneration', 'monthlyConsumption', 'monthlyGridPower', 'monthlyBoughtEnergy', 'monthlySellEnergy', 'monthlySaving', 'monthlyIrradiance', 'created_at')->where('plant_id', $request->id)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
//        $monthly_generation = $monthly_data ? (double)$monthly_data->monthlyGeneration : 0;
//        $monthly_gen_arr = $this->unitConversion($monthly_generation, 'kWh');
//        $yearly_data = YearlyProcessedPlantDetail::select('yearlyGeneration', 'yearlyConsumption', 'yearlyGridPower', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearlySaving', 'yearlyIrradiance', 'created_at')->where('plant_id', $request->id)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
//        $yearly_generation = $yearly_data ? (double)$yearly_data->yearlyGeneration : 0;
//        $yearly_gen_arr = $this->unitConversion($yearly_generation, 'kWh');
//        $total_data = TotalProcessedPlantDetail::where('plant_id', $request->id)->first();
//        $total_generation = $total_data ? (double)$total_data->plant_total_generation : 0;
//        $total_gen_arr = $this->unitConversion($total_generation, 'kWh');
//        $data =
//            [
//                'daily' => round($daily_gen_arr[0], 2) . ' ' . $daily_gen_arr[1],
//                'monthly' => round($monthly_gen_arr[0], 2) . ' ' . $monthly_gen_arr[1],
//                'yearly' => round($yearly_gen_arr[0], 2) . ' ' . $yearly_gen_arr[1],
//                'total' => round($total_gen_arr[0], 2) . ' ' . $total_gen_arr[1]
//            ];
//        return $data;
//    }


    public function plantEnergyFlowData(Request $request)
    {
        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            $plant_arr = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id');
            $plant_arr = $plant_arr->toArray();

            if (!empty($plant_arr) && (!in_array($request->id, $plant_arr))) {
                return ('You have no access of that plant!');
            }
        }
        if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {

            $plant_arr = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
            $plant_arr = $plant_arr->toArray();

            if (!empty($plant_arr) && !in_array((string)$request->id, $plant_arr)) {
                return ('You have no access of that plant!');
            } else if (empty($plant_arr)) {
                return ('You have no access of that plant!');
            }
        }

        $where_array = array();
        if (Auth::user()->roles != 1 && Auth::user()->roles != 2) {

            $company_id = Auth::user()->company_id;
            $where_array['company_id'] = $company_id;
        }

        $plant = Plant::with('latest_inverter_emi_details')->where('id', $request->id)->first();

        if (!$plant) {
            return ('No plant found!');
        }
        $inverterTotalDCPower = 0;
        $inverterTotalACOutputPower = 0;

        $plant['system_type_id'] = $plant->system_type;
        $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
        $plant['system_type'] = SystemType::find($plant->system_type)->type;

        $pl_sites_array = PlantSite::where('plant_id', $request->id)->pluck('site_id')->toArray();

        $plant_inverters = InverterSerialNo::whereIn('site_id', $pl_sites_array)->get();
        if ($plant_inverters && count($plant_inverters) > 0) {

            foreach ($plant_inverters as $key => $inverter) {
                $inverterCurrentDataLogTime = InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->exists() ? InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');

                $inverterFinalCurrentDataDateTime = $this->previousTenMinutesDateTime($inverterCurrentDataLogTime);
                $inverterDetailObject = InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $inverterFinalCurrentDataDateTime)->orderBy('collect_time', 'DESC')->first();
                $inverterTotalDCPower += $inverterDetailObject && isset($inverterDetailObject->mpptPower) ? $inverterDetailObject->mpptPower : 0;
                $lastMonthGeneration = MonthlyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereMonth('created_at', date('m', strtotime("-1 months")))->first();
                $lastMonthGenerationConverted = $lastMonthGeneration ? $this->unitConversion($lastMonthGeneration->monthly_generation, 'kWh') : [0, 'kWh'];

                $lastYearGeneration = YearlyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereYear('created_at', date('Y', strtotime("-1 years")))->first();
                $lastYearGenerationConverted = $lastYearGeneration ? $this->unitConversion($lastYearGeneration->yearly_generation, 'kWh') : [0, 'kWh'];

                $totalGeneration = YearlyInverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->sum('yearly_generation');
                $totalGenerationConverted = $this->unitConversion($totalGeneration, 'kWh');

                $inverterCurrentDataLogTime = InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->exists() ? InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');

                $inverterFinalCurrentDataDateTime = $this->previousTenMinutesDateTime($inverterCurrentDataLogTime);

                $inverterDetailObject = InverterDetail::where('siteId', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $inverterFinalCurrentDataDateTime)->orderBy('collect_time', 'DESC')->first();
                $powerConverted = $inverterDetailObject && isset($inverterDetailObject->inverterPower) ? $this->unitConversion($inverterDetailObject->inverterPower, 'kW') : [0, 'kW'];
                $inverterTotalACOutputPower += $inverterDetailObject && $inverterDetailObject->inverterPower ? $inverterDetailObject->inverterPower : 0;

                $siteInverterDetailObject = SiteInverterDetail::where('site_id', $inverter->site_id)->where('dv_inverter', $inverter->dv_inverter)->first();
                $installedDCPowerConverted = $siteInverterDetailObject && isset($siteInverterDetailObject->dv_installed_dc_power) ? $this->unitConversion($siteInverterDetailObject->dv_installed_dc_power, 'kW') : [0, 'kW'];

            }
        }
        $current = array();
        $currentDataValues = array();
        $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $request->id)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $request->id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
        $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
        $current_data = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type', 'comm_failed', 'created_at')->where('plant_id', $request->id)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $finalCurrentDataDateTime)->orderBy('collect_time', 'desc')->first();
        $current_generation = $current_data ? (double)$current_data->current_generation : 0;
        $current_consumption = $current_data ? (double)$current_data->current_consumption : 0;
        $current_grid = $current_data ? (double)$current_data->current_grid : 0;
        $current_grid_type = $current_data ? $current_data->grid_type : '';
        $currentDataValues['dc_power'] = $inverterTotalDCPower;
        $currentDataValues['comm_fail'] = $current_data ? (int)$current_data->comm_failed : 0;
        $currentDataValues['generation'] = $current_generation;
        $currentDataValues['consumption'] = $current_consumption;
        $currentDataValues['grid'] = $current_grid;
        $currentDataValues['grid_type'] = $current_grid_type;


//        $current_data = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type', 'comm_failed', 'created_at')->where('plant_id', $request->id)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $finalCurrentDataDateTime)->orderBy('collect_time', 'desc')->first();
//        $current_generation = $current_data ? (double)$current_data->current_generation : 0;
//        $current_consumption = $current_data ? (double)$current_data->current_consumption : 0;
//        $current_grid = $current_data ? (double)$current_data->current_grid : 0;
//        $current_grid_type = $current_data ? $current_data->grid_type : '';
//        $current['date'] = $current_data ? $current_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
//        $current['comm_fail'] = $current_data ? (int)$current_data->comm_failed : 0;
        $curr_gen_arr = $this->unitConversion($current_generation, 'kW');
        $curr_con_arr = $this->unitConversion($current_consumption, 'kW');
        $curr_grid_arr = $this->unitConversion($current_grid, 'kW');
        $totalDCPowerConverted = $inverterTotalDCPower ? $this->unitConversion($inverterTotalDCPower, 'kW') : [0, 'kW'];
        $current['dc_power'] = round($totalDCPowerConverted[0], 2) . ' ' . $totalDCPowerConverted[1];
        $current['date'] = $current_data ? $current_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
        $current['comm_fail'] = $current_data ? (int)$current_data->comm_failed : 0;
        $current['generation'] = round($curr_gen_arr[0], 2) . ' ' . $curr_gen_arr[1];
        $current['consumption'] = round($curr_con_arr[0], 2) . ' ' . $curr_con_arr[1];
        $current['grid'] = round($curr_grid_arr[0], 2) . ' ' . $curr_grid_arr[1];
        $current['grid_type'] = $current_grid_type;
        return ['energyData' => $current, 'currentDataValues' => $currentDataValues];
    }

    public function plantConsumptionPeakHoursGraph(Request $request)
    {
        date_default_timezone_set('Asia/Karachi');

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;
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
        $currentProcessedData = DailyProcessedPlantDetail::select('daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
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
                $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
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

                $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_peak_hours_discharge_energy', 'yearly_peak_hours_grid_import', 'yearly_peak_hours_consumption', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
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
        $data = ['legendArray' => $legendArray, 'logData' => $todayLogData, 'batteryDischarge' => $batteryDischargeEnergy, 'gridImport' => $gridImport, 'consumption' => $consumption];
        return $data;
    }

    public function plantSolarUtilizationGraph(Request $request)
    {
        date_default_timezone_set('Asia/Karachi');

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;
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
        $current = ['battery-charging', 'load', 'grid_export'];
        $gridExport = 0;
        $load = 0;
        $batteryCharging = 0;
        $dailyChargingEnergy = 0;
        $generation = 0;
//        $bought
//        $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_charge_energy', 'daily_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', date('Y-m-d'))->whereTime('created_at', '>=', $peakStartDate)->whereTime('created_at', '<=', $peakEndDate)->get();
        $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
//        return $currentProcessedData;
        if ($currentProcessedData) {
            $dailyChargingEnergy = $currentProcessedData->daily_charge_energy;
            $gridExport = $currentProcessedData->dailySellEnergy;
            $generation = $currentProcessedData->dailyGeneration;
            $load = $currentProcessedData->dailyConsumption;
        }
        foreach ($current as $key => $currentData) {

            $todayLogDataSum = array();
            if ($time == 'day') {

                if ($currentData == 'battery-charging') {

//                    $graphColor = '#F6A944';
//                    $todayLogData[] = 'Daily Discharge';
//                    $todayLogData[] = ['value' => (double)$dailyDischargeEnergy .'kW', 'name' => 'Daily Discharge:'. (double)$dailyDischargeEnergy .' '.'kW'];
//                    array_push($todayLogData,'Daily Discharge');
                    array_push($todayLogData, ['value' => (double)$dailyChargingEnergy, 'name' => 'Battery Charging: ' . (double)$dailyChargingEnergy . ' ' . 'kWh']);
//                    $batteryDischargeEnergy = (double)$dailyChargingEnergy;
//                    $todayLogDataSum = ['value' => $dailyDischargeEnergy, 'name' => 'Daily Discharge Eneregy'];
                } else if ($currentData == 'load') {

//                    $graphColor = '#46C1AB';
//                    $todayLogData[] = 'Grid Import';
//                    $todayLogData[] = 'name:' .'Daily Discharge:'. (double)$dailyDischargeEnergy .' '.'kW';
//                    $todayLogData[] = 'Grid Import';
//                    $todayLogData[] = ['value' => (double)$gridImport .'kW', 'name' => 'Daily Discharge:'. (double)$gridImport .' '.'kW'];
                    array_push($todayLogData, ['value' => (double)$load, 'name' => 'Load: ' . (double)$load . ' ' . 'kWh']);
//                    $gridImport = (double)$gridImport;
                } else if ($currentData == 'grid_export') {

//                    $graphColor = '#46C1AB';
//                    $todayLogData[] = 'Grid Import';
//                    $todayLogData[] = 'name:' .'Daily Discharge:'. (double)$dailyDischargeEnergy .' '.'kW';
//                    $todayLogData[] = 'Grid Import';
//                    $todayLogData[] = ['value' => (double)$gridImport .'kW', 'name' => 'Daily Discharge:'. (double)$gridImport .' '.'kW'];
                    array_push($todayLogData, ['value' => (double)$gridExport, 'name' => 'Grid Export: ' . (double)$gridExport . ' ' . 'kWh']);
//                    $gridImport = (double)$gridImport;
                }

//                $todayLogData[] = $todayLogDataSum;
//            }
//                return $todayLogDataSum;
            } else if ($time == 'month') {
                $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
                $monthlyChargeValue = 0;
                $monthlyGridExportValue = 0;
                $monthlyLoadValue = 0;
                if ($monthlyProcessedData) {
                    $monthlyGridExportValue = $monthlyProcessedData->monthlySellEnergy;
                    $monthlyChargeValue = $monthlyProcessedData->monthly_charge_energy;
                    $monthlyLoadValue = $monthlyProcessedData->monthlyConsumption;
                    $generation = $monthlyProcessedData->monthlyGeneration;
                }
                if ($currentData == 'battery-charging') {

//                    $graphColor = '#F6A944';
                    array_push($todayLogData, ['value' => (double)$monthlyChargeValue, 'name' => 'Battery Charging: ' . (double)$monthlyChargeValue . ' ' . 'kWh']);
//                    $batteryDischargeEnergy = (double)$monthlyDischargeValue;
//                    $todayLogDataSum = ['value' => $dailyDischargeEnergy, 'name' => 'Daily Discharge Eneregy'];
                } else if ($currentData == 'load') {

//                    $graphColor = '#46C1AB';
                    array_push($todayLogData, ['value' => (double)$monthlyLoadValue, 'name' => 'Load: ' . (double)$monthlyLoadValue . ' ' . 'kWh']);
//                    $gridImport = (double)$monthlyGridValue;
                } else if ($currentData == 'grid_export') {

//                    $graphColor = '#46C1AB';
                    array_push($todayLogData, ['value' => (double)$monthlyGridExportValue, 'name' => 'Grid Export: ' . (double)$monthlyGridExportValue . ' ' . 'kWh']);
//                    $gridImport = (double)$monthlyGridValue;
                }
            } else if ($time == 'year') {

                $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_peak_hours_discharge_energy', 'yearlyGeneration', 'yearly_peak_hours_grid_import', 'yearlyConsumption', 'yearly_peak_hours_consumption', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
                $yearlyChargeValue = 0;
                $yearlyGridExportValue = 0;
                $yearlyLoadValue = 0;
                if ($yearlyProcessedData) {
                    $yearlyChargeValue = $yearlyProcessedData->yearly_charge_energy;
                    $yearlyGridExportValue = $yearlyProcessedData->yearlySellEnergy;
                    $yearlyLoadValue = $yearlyProcessedData->yearlyConsumption;
                    $generation = $yearlyProcessedData->yearlyGeneration;
                }
                if ($currentData == 'battery-charging') {

//                    $graphColor = '#F6A944';
                    array_push($todayLogData, ['value' => (double)$yearlyChargeValue, 'name' => 'Battery Charging: ' . (double)$yearlyChargeValue . ' ' . 'kWh']);
//                    $batteryDischargeEnergy = (double)$yearlyDischargeValue;
//                    $todayLogDataSum = ['value' => $dailyDischargeEnergy, 'name' => 'Daily Discharge Eneregy'];
                } else if ($currentData == 'load') {

//                    $graphColor = '#46C1AB';
                    array_push($todayLogData, ['value' => (double)$yearlyLoadValue, 'name' => 'Load: ' . (double)$yearlyLoadValue . ' ' . 'kWh']);
//                    $gridImport = (double)$yearlyGridValue;
                } else if ($currentData == 'grid_export') {

//                    $graphColor = '#46C1AB';
                    array_push($todayLogData, ['value' => (double)$yearlyGridExportValue, 'name' => 'Grid Export: ' . (double)$yearlyGridExportValue . ' ' . 'kWh']);
//                    $gridImport = (double)$yearlyGridValue;
                }
            }
        }
//        if ($time == 'day') {
////            $t_m . ': ' . round($yearly_gen_arr[0], 2) . ' ' . $yearly_gen_arr[1]
////            $legendArray = ['Battery Discharge' => (double)$dailyDischargeEnergy . 'KW', 'Grid Import' => $gridImport . 'KW'];
//            $legendArray[] = 'Battery Discharge' . ': ' . (double)$dailyDischargeEnergy . ' ' . 'KW';
//            $legendArray[] = 'Grid Import' . ': ' . (double)$gridImport . ' ' . 'KW';
//        } else if ($time == 'month') {
//            $legendArray = ['Battery Discharge' => (double)$monthlyDischargeValue . 'KW', 'Grid Import' => $monthlyGridValue . 'KW'];
//        } else if ($time == 'year') {
//            $legendArray = ['Battery Discharge' => (double)$yearlyDischargeValue . 'KW', 'Grid Import' => $yearlyGridValue . 'KW'];
//        }
        $data = ['generation' => $generation, 'logData' => $todayLogData];
        return $data;
    }

    public function plantEnergySourceGraph(Request $request)
    {
        date_default_timezone_set('Asia/Karachi');

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;
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
        $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_discharge_energy', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
//        return $currentProcessedData;
        if ($currentProcessedData) {
            $batteryDischarging = $currentProcessedData->daily_discharge_energy;
            $gridImport = $currentProcessedData->dailyBoughtEnergy;
            $consumption = $currentProcessedData->dailyConsumption;
            $solar = $currentProcessedData->dailyGeneration;
        }
        foreach ($current as $key => $currentData) {

            $todayLogDataSum = array();
            if ($time == 'day') {

                if ($currentData == 'battery-discharging') {

//                    $graphColor = '#F6A944';
//                    $todayLogData[] = 'Daily Discharge';
//                    $todayLogData[] = ['value' => (double)$dailyDischargeEnergy .'kW', 'name' => 'Daily Discharge:'. (double)$dailyDischargeEnergy .' '.'kW'];
//                    array_push($todayLogData,'Daily Discharge');
                    array_push($todayLogData, ['value' => (double)$batteryDischarging, 'name' => 'Battery Discharging: ' . (double)$batteryDischarging . ' ' . 'kWh']);
//                    $batteryDischargeEnergy = (double)$dailyChargingEnergy;
//                    $todayLogDataSum = ['value' => $dailyDischargeEnergy, 'name' => 'Daily Discharge Eneregy'];
                } else if ($currentData == 'solar') {

//                    $graphColor = '#46C1AB';
//                    $todayLogData[] = 'Grid Import';
//                    $todayLogData[] = 'name:' .'Daily Discharge:'. (double)$dailyDischargeEnergy .' '.'kW';
//                    $todayLogData[] = 'Grid Import';
//                    $todayLogData[] = ['value' => (double)$gridImport .'kW', 'name' => 'Daily Discharge:'. (double)$gridImport .' '.'kW'];
                    array_push($todayLogData, ['value' => (double)$solar, 'name' => 'Solar: ' . (double)$solar . ' ' . 'kWh']);
//                    $gridImport = (double)$gridImport;
                } else if ($currentData == 'grid_import') {

//                    $graphColor = '#46C1AB';
//                    $todayLogData[] = 'Grid Import';
//                    $todayLogData[] = 'name:' .'Daily Discharge:'. (double)$dailyDischargeEnergy .' '.'kW';
//                    $todayLogData[] = 'Grid Import';
//                    $todayLogData[] = ['value' => (double)$gridImport .'kW', 'name' => 'Daily Discharge:'. (double)$gridImport .' '.'kW'];
                    array_push($todayLogData, ['value' => (double)$gridImport, 'name' => 'Grid Import: ' . (double)$gridImport . ' ' . 'kWh']);
//                    $gridImport = (double)$gridImport;
                }

//                $todayLogData[] = $todayLogDataSum;
//            }
//                return $todayLogDataSum;
            } else if ($time == 'month') {
                $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
                $monthlyDischargeValue = 0;
                $monthlyGridImportValue = 0;
                $monthlySolar = 0;
                if ($monthlyProcessedData) {
                    $consumption = $monthlyProcessedData->monthlyConsumption;
                    $monthlyDischargeValue = $monthlyProcessedData->monthly_discharge_energy;
                    $monthlyGridImportValue = $monthlyProcessedData->monthlyBoughtEnergy;
                    $monthlySolar = $monthlyProcessedData->monthlyGeneration;
                }
                if ($currentData == 'battery-discharging') {

//                    $graphColor = '#F6A944';
                    array_push($todayLogData, ['value' => (double)$monthlyDischargeValue, 'name' => 'Battery Discharging: ' . (double)$monthlyDischargeValue . ' ' . 'kWh']);
//                    $batteryDischargeEnergy = (double)$monthlyDischargeValue;
//                    $todayLogDataSum = ['value' => $dailyDischargeEnergy, 'name' => 'Daily Discharge Eneregy'];
                } else if ($currentData == 'solar') {

//                    $graphColor = '#46C1AB';
                    array_push($todayLogData, ['value' => (double)$monthlySolar, 'name' => 'Solar: ' . (double)$monthlySolar . ' ' . 'kWh']);
//                    $gridImport = (double)$monthlyGridValue;
                } else if ($currentData == 'grid_import') {

//                    $graphColor = '#46C1AB';
                    array_push($todayLogData, ['value' => (double)$monthlyGridImportValue, 'name' => 'Grid Import: ' . (double)$monthlyGridImportValue . ' ' . 'kWh']);
//                    $gridImport = (double)$monthlyGridValue;
                }
            } else if ($time == 'year') {

                $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_peak_hours_discharge_energy', 'yearlyGeneration', 'yearly_peak_hours_grid_import', 'yearlyConsumption', 'yearly_peak_hours_consumption', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
                $yearlyDischargeValue = 0;
                $yearlyGridImportValue = 0;
                $yearlySolar = 0;
                if ($yearlyProcessedData) {
                    $yearlyDischargeValue = $yearlyProcessedData->yearly_discharge_energy;
                    $yearlyGridImportValue = $yearlyProcessedData->yearlyBoughtEnergy;
                    $yearlySolar = $yearlyProcessedData->yearlyGeneration;
                    $consumption = $yearlyProcessedData->yearlyConsumption;
                }
                if ($currentData == 'battery-discharging') {

//                    $graphColor = '#F6A944';
                    array_push($todayLogData, ['value' => (double)$yearlyDischargeValue, 'name' => 'Battery Discharging: ' . (double)$yearlyDischargeValue . ' ' . 'kWh']);
//                    $batteryDischargeEnergy = (double)$yearlyDischargeValue;
//                    $todayLogDataSum = ['value' => $dailyDischargeEnergy, 'name' => 'Daily Discharge Eneregy'];
                } else if ($currentData == 'solar') {

//                    $graphColor = '#46C1AB';
                    array_push($todayLogData, ['value' => (double)$yearlySolar, 'name' => 'Solar: ' . (double)$yearlySolar . ' ' . 'kWh']);
//                    $gridImport = (double)$yearlyGridValue;
                } else if ($currentData == 'grid_import') {

//                    $graphColor = '#46C1AB';
                    array_push($todayLogData, ['value' => (double)$yearlyGridImportValue, 'name' => 'Grid Import: ' . (double)$yearlyGridImportValue . ' ' . 'kWh']);
//                    $gridImport = (double)$yearlyGridValue;
                }
            }
        }
//        if ($time == 'day') {
////            $t_m . ': ' . round($yearly_gen_arr[0], 2) . ' ' . $yearly_gen_arr[1]
////            $legendArray = ['Battery Discharge' => (double)$dailyDischargeEnergy . 'KW', 'Grid Import' => $gridImport . 'KW'];
//            $legendArray[] = 'Battery Discharge' . ': ' . (double)$dailyDischargeEnergy . ' ' . 'KW';
//            $legendArray[] = 'Grid Import' . ': ' . (double)$gridImport . ' ' . 'KW';
//        } else if ($time == 'month') {
//            $legendArray = ['Battery Discharge' => (double)$monthlyDischargeValue . 'KW', 'Grid Import' => $monthlyGridValue . 'KW'];
//        } else if ($time == 'year') {
//            $legendArray = ['Battery Discharge' => (double)$yearlyDischargeValue . 'KW', 'Grid Import' => $yearlyGridValue . 'KW'];
//        }
        $data = ['generation' => $consumption, 'logData' => $todayLogData];
        return $data;
    }

    public function plantCostSavingData(Request $request)
    {
        date_default_timezone_set('Asia/Karachi');

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;
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
        $benchMarkPrice = 0;
        $peakTeriffRate = 0;
        $plantData = Plant::where('id', $plantID)->first();
        if ($plantData) {
            $benchMarkPrice = $plantData->benchmark_price;
            $peakTeriffRate = $plantData->peak_teriff_rate;
        }
        $todayLogData = [];
        $current = ['peak-hours-savings', 'generation-saving'];
        $peakHoursSaving = 0;
        $generationSaving = 0;
        $totalSaving = 0;
        $currentProcessedData = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'daily_discharge_energy', 'daily_charge_energy', 'daily_peak_hours_consumption', 'daily_peak_hours_grid_buy', 'daily_peak_hours_battery_discharge', 'created_at')->where('plant_id', $plantID)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
        if ($currentProcessedData) {
            $peakHoursSaving = (double)$currentProcessedData->daily_peak_hours_battery_discharge * (int)$peakTeriffRate;
            $generationSaving = $currentProcessedData->dailyGeneration * (int)$benchMarkPrice;
            $totalSaving = $peakHoursSaving + $generationSaving;
        }
        foreach ($current as $key => $currentData) {

            $todayLogDataSum = array();
            if ($time == 'day') {

                if ($currentData == 'peak-hours-savings') {
                    array_push($todayLogData, ['value' => (double)$peakHoursSaving, 'name' => 'Peak Hours Saving: ' . (double)$peakHoursSaving . ' ' . 'PKR']);
                } else if ($currentData == 'generation-saving') {
                    array_push($todayLogData, ['value' => (double)$generationSaving, 'name' => 'Generation Saving: ' . (double)$generationSaving . ' ' . 'PKR']);
                }
            } else if ($time == 'month') {
                $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
                $monthlyHoursSaving = 0;
                $monthlyGenerationSaving = 0;
                if ($monthlyProcessedData) {
                    $monthlyHoursSaving = (double)$monthlyProcessedData->monthly_peak_hours_discharge_energy * (int)$peakTeriffRate;
                    $monthlyGenerationSaving = $monthlyProcessedData->monthlyGeneration * (int)$benchMarkPrice;
                    $totalSaving = $monthlyHoursSaving + $monthlyGenerationSaving;
                }
                if ($currentData == 'peak-hours-savings') {
                    array_push($todayLogData, ['value' => (double)$monthlyHoursSaving, 'name' => 'Peak Hours Saving: ' . (double)$monthlyHoursSaving . ' ' . 'PKR']);
                } else if ($currentData == 'generation-saving') {
                    array_push($todayLogData, ['value' => (double)$monthlyGenerationSaving, 'name' => 'Generation Saving: ' . (double)$monthlyGenerationSaving . ' ' . 'PKR']);
                }
            } else if ($time == 'year') {

                $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearlySaving', 'yearly_peak_hours_discharge_energy', 'yearlyGeneration', 'yearly_peak_hours_grid_import', 'yearlyConsumption', 'yearly_peak_hours_consumption', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearly_charge_energy', 'yearly_discharge_energy', 'yearlySaving', 'yearly_charge_energy', 'yearly_discharge_energy', 'created_at')->where('plant_id', $plantID)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
                $yearlyPeakHours = 0;
                $yearlyGenerationSaving = 0;
                $yearlySolar = 0;
                if ($yearlyProcessedData) {
                    $yearlyPeakHours = $yearlyProcessedData->yearly_peak_hours_discharge_energy * (int)$peakTeriffRate;
                    $yearlyGenerationSaving = $yearlyProcessedData->yearlyGeneration * (int)$benchMarkPrice;
                    $totalSaving = $yearlyPeakHours + $yearlyGenerationSaving;
                }
                if ($currentData == 'peak-hours-savings') {
                    array_push($todayLogData, ['value' => (double)$yearlyPeakHours, 'name' => 'Peak Hours Saving: ' . (double)$yearlyPeakHours . ' ' . 'PKR']);
                } else if ($currentData == 'generation-saving') {

                    array_push($todayLogData, ['value' => (double)$yearlyGenerationSaving, 'name' => 'Generation Saving: ' . (double)$yearlyGenerationSaving . ' ' . 'PKR']);
                }
            }
        }
        $data = ['totalSaving' => $totalSaving, 'logData' => $todayLogData];
        return $data;
    }

    public function gridOutagesVoltage(Request $request)
    {
        date_default_timezone_set('Asia/Karachi');

        $time = $request->time;
        $requestDate = strtotime($request->date);
        $plantID = $request->plantID;
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
            $data = ['outagesHours' => $outagesHours];
        } else if ($time == 'month') {
            $monthlyProcessedData = MonthlyProcessedPlantDetail::where('plant_id', $plantID)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
            $monthlyOutagesHours = 0;
            if ($monthlyProcessedData) {
                $monthlyOutagesHours = date('H:i', strtotime($monthlyProcessedData->monthly_outage_grid_voltage));
            }
            $data = ['outagesHours' => $monthlyOutagesHours];

        } else {
            $yearlyProcessedData = YearlyProcessedPlantDetail::select('yearly_outage_grid_voltage', 'created_at')->where('plant_id', $plantID)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
            $yearlyOutagesHours = 0;
            if ($yearlyProcessedData) {
                $yearlyOutagesHours = date('H:i', strtotime($yearlyProcessedData->yearly_outage_grid_voltage));
            }
            $data = ['outagesHours' => $yearlyOutagesHours];


        }
        return $data;
    }

    public function plantsPowerGraph(Request $request)
    {
        $powerArray = array();
        $powerValue = 0;
        $capacityValue = Plant::where('id', $request->id)->sum('capacity');
        $totalCapacity = $this->unitConversion($capacityValue, 'kWp');
        $powerArray['total_capacity'] = round($totalCapacity[0], 2) . ' ' . $totalCapacity[1];
        $currentDataLogTime = ProcessedCurrentVariable::where('plant_id', $request->id)->whereDate('collect_time', date('Y-m-d'))->exists() ? ProcessedCurrentVariable::where('plant_id', $request->id)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
        $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
        $powerValue = ProcessedCurrentVariable::where('plant_id', $request->id)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $finalCurrentDataDateTime)->exists() ? ProcessedCurrentVariable::where('plant_id', $request->id)->whereDate('collect_time', date('Y-m-d'))->where('collect_time', '<=', $finalCurrentDataDateTime)->orderBy('collect_time', 'DESC')->first()->current_generation : 0;
        $currentPower = $this->unitConversion($powerValue, 'kW');
        $powerArray['current_power'] = round($currentPower[0], 2) . ' ' . $currentPower[1];
        $plantPowerGraphData = $this->plantPowerGraph($powerValue, $capacityValue);
        $data = [
            'plantPowerGraphGraph' => $plantPowerGraphData,
            'powerArray' => $powerArray
        ];
        return $data;
    }

    public function plantsGenerationGraph(Request $request)
    {
        $daily_data = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'dailyIrradiance', 'created_at')->where('plant_id', $request->id)->whereDate('created_at', date('Y-m-d'))->orderBy('updated_at', 'DESC')->first();
        $daily_generation = $daily_data ? (double)$daily_data->dailyGeneration : 0;
        $daily_gen_arr = $this->unitConversion($daily_generation, 'kWh');
        $monthly_data = MonthlyProcessedPlantDetail::select('monthlyGeneration', 'monthlyConsumption', 'monthlyGridPower', 'monthlyBoughtEnergy', 'monthlySellEnergy', 'monthlySaving', 'monthlyIrradiance', 'created_at')->where('plant_id', $request->id)->where('created_at', 'LIKE', date('Y-m') . '%')->orderBy('created_at', 'DESC')->first();
        $monthly_generation = $monthly_data ? (double)$monthly_data->monthlyGeneration : 0;
        $monthly_gen_arr = $this->unitConversion($monthly_generation, 'kWh');
        $yearly_data = YearlyProcessedPlantDetail::select('yearlyGeneration', 'yearlyConsumption', 'yearlyGridPower', 'yearlyBoughtEnergy', 'yearlySellEnergy', 'yearlySaving', 'yearlyIrradiance', 'created_at')->where('plant_id', $request->id)->whereYear('created_at', date('Y'))->orderBy('created_at', 'DESC')->first();
        $yearly_generation = $yearly_data ? (double)$yearly_data->yearlyGeneration : 0;
        $yearly_gen_arr = $this->unitConversion($yearly_generation, 'kWh');
        $total_data = TotalProcessedPlantDetail::where('plant_id', $request->id)->first();
        $total_generation = $total_data ? (double)$total_data->plant_total_generation : 0;
        $total_gen_arr = $this->unitConversion($total_generation, 'kWh');
        $data =
            [
                'daily' => round($daily_gen_arr[0], 2) . ' ' . $daily_gen_arr[1],
                'monthly' => round($monthly_gen_arr[0], 2) . ' ' . $monthly_gen_arr[1],
                'yearly' => round($yearly_gen_arr[0], 2) . ' ' . $yearly_gen_arr[1],
                'total' => round($total_gen_arr[0], 2) . ' ' . $total_gen_arr[1]
            ];
        return $data;
    }

    public function plantInverterMPPTNumber(Request $request)
    {

        $plantID = $request->plantID;
        $serialNo = $request->serialNo;
        $mpptNumber = 0;

        if (InverterMPPTDetail::where(['plant_id' => $plantID, 'dv_inverter' => $serialNo])->exists()) {

            $mpptNumber = InverterMPPTDetail::select(DB::raw('Max(mppt_number) as mppt_number'))->where(['plant_id' => $plantID, 'dv_inverter' => $serialNo])->where('mppt_voltage', '!=', 0)->orderBy('collect_time', 'DESC')->groupBy('collect_time')->first()->mppt_number;
        }

        return response()->json(['mpptNumber' => $mpptNumber]);
    }

    public function dashboardEnergyGraph(Request $request)
    {

        $filter = json_decode($request->filter, true);
        $plants_name = json_decode($request->plant_name, true);
        $filter_arr = [];
        $plant_name = [];
        $time = $request->time;
        $dates = strtotime($request->date);
        $pre_date = date('Y-m-d');
        $graph_today_date = date('Y-m-d');
        $graph_yesterday_date = date('Y-m-d');

        foreach ($filter as $key => $flt) {

            $filter_arr[$key] = $flt;
        }

        foreach ($plants_name as $pl_name) {

            $plant_name[] = $pl_name;
        }

        if (empty($plant_name)) {

            if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                $plant_names = Plant::pluck('id');
                $plant_names = $plant_names->toArray();
            } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                $plant_names = $plant_names->toArray();
            }

            $plant_name = $plant_names;
        }

        $plants_id = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id');

        if ($time == 'day') {
            $date = date('Y-m-d', $dates);
            $pre_date = date('Y-m-d', strtotime(("-1 days"), $dates));
            $graph_today_date = date('d-m-Y', strtotime($date));
            $graph_yesterday_date = date('d-m-Y', strtotime($pre_date));
        } else if ($time == 'month') {
            $date = date('Y-m', $dates);
            $pre_date = date('Y-m', strtotime(("-1 months"), $dates));
            $graph_today_date = date('m-Y', strtotime($date));
            $graph_yesterday_date = date('m-Y', strtotime($pre_date));
        } else if ($time == 'year') {
            $date = $request->date;
            $pre_date = $date - 1;
            $graph_today_date = $date;
            $graph_yesterday_date = $pre_date;
        }

        $today_log_data = [];
        $today_log_time = [];
        $yesterday_log_time = [];
        $yesterday_log_data = [];
        $unique_time_arr = [];
        $today_log_data_sum = 0;
        $plant_energy_graph = array();
        $tooltip_name_today = '';
        $tooltip_name_yesterday = '';

        $current_generation_start_time = ProcessedCurrentVariable::select('collect_time')->whereIn('plant_id', $plants_id)->whereDate('collect_time', $date)->where('totalEnergy', '>', 0)->orderBy('collect_time', 'ASC')->first();
        $start_date_time = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->created_at)) : '05:00:00';

        if ($time == 'day') {

            $tooltip_name_today = 'Today Generation';
            $tooltip_name_yesterday = 'Yesterday Generation';

            $current_generation = ProcessedCurrentVariable::select('collect_time')->whereIn('plant_id', $plants_id)->whereBetween('collect_time', [date($date . ' ' . $start_date_time), date($date . ' 23:59:59')])->groupBy('collect_time')->get();
            $yesterday_generation = ProcessedCurrentVariable::select('collect_time')->whereIn('plant_id', $plants_id)->whereBetween('collect_time', [date($pre_date . ' ' . $start_date_time), date($pre_date . ' 23:59:59')])->groupBy('collect_time')->get();

            foreach ($current_generation as $key => $today_log) {
                $today_log_time[] = date('H:i', strtotime($today_log->collect_time));
            }

            foreach ($yesterday_generation as $key => $yesterday_log) {
                $yesterday_log_time[] = date('H:i', strtotime($yesterday_log->collect_time));
            }

            $unique_time = array_unique(array_merge($today_log_time, $yesterday_log_time), SORT_REGULAR);

            foreach ($unique_time as $key => $arr) {

                $unique_time_arr[] = $arr;
            }

            sort($unique_time_arr);

            foreach ($unique_time_arr as $key => $arr) {

                $today_log_data_sum = ProcessedCurrentVariable::whereIn('plant_id', $plants_id)->where('collect_time', 'LIKE', $date . ' ' . $arr . '%')->sum('totalEnergy');
                $today_log_data[] = $key > 0 && $today_log_data[$key - 1] > $today_log_data_sum ? round($today_log_data[$key - 1], 2) : round($today_log_data_sum, 2);

                if ($date == date('Y-m-d') && strtotime(date('H:i', strtotime($arr))) >= strtotime(date('H:i'))) {

                    $today_log_data[$key] = null;
                }

                $yesterday_log_data_sum = ProcessedCurrentVariable::whereIn('plant_id', $plants_id)->where('collect_time', 'LIKE', $pre_date . ' ' . $arr . '%')->sum('totalEnergy');
                $yesterday_log_data[] = $key > 0 && $yesterday_log_data[$key - 1] > $yesterday_log_data_sum ? round($yesterday_log_data[$key - 1], 2) : round($yesterday_log_data_sum, 2);

            }
        } else if ($time == 'month') {

            $tooltip_name_today = 'Daily Generation';
            $tooltip_name_yesterday = 'Daily Generation';

            $explode_data = explode('-', $date);
            $mon = $explode_data[1];
            $yer = $explode_data[0];

            $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);
            for ($i = 1; $i <= $dd; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $unique_time_arr[] = $i;

                $today_log_data_sum = DailyProcessedPlantDetail::whereIn('plant_id', $plants_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyGeneration');

                $today_log_data[] = $today_log_data_sum ? round($today_log_data_sum, 2) : 0;

                /*if($i > date('d')) {

                    $today_log_data[$i] = null;
                }*/

                $yesterday_log_data_sum = DailyProcessedPlantDetail::whereIn('plant_id', $plants_id)->where('created_at', 'LIKE', $pre_date . '-' . $i . '%')->sum('dailyGeneration');

                $yesterday_log_data[] = $yesterday_log_data_sum ? round($yesterday_log_data_sum, 2) : 0;

            }
        } else if ($time == 'year') {

            $tooltip_name_today = 'Monthly Generation';
            $tooltip_name_yesterday = 'Monthly Generation';

            for ($i = 1; $i <= 12; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $unique_time_arr[] = substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3);

                $today_log_data_sum = MonthlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGeneration');

                $today_log_data[] = $today_log_data_sum > 0 ? round($today_log_data_sum, 2) : 0;

                /*if($i > date('m')) {

                    $today_log_data[$i] = null;
                }*/

                $yesterday_log_data_sum = MonthlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->whereYear('created_at', $pre_date)->whereMonth('created_at', $i)->sum('monthlyGeneration');

                $yesterday_log_data[] = $yesterday_log_data_sum > 0 ? round($yesterday_log_data_sum, 2) : 0;

            }
        }

        $today_energy = collect([
            "name" => $tooltip_name_today,
            "type" => 'line',
            "smooth" => true,
            "color" => '#4d8867',
            "showSymbol" => false,
            "data" => $today_log_data
        ]);

        $yesterday_energy = collect([
            "name" => $tooltip_name_yesterday,
            "type" => 'line',
            "smooth" => true,
            "color" => '#eb9898',
            "showSymbol" => false,
            "data" => $yesterday_log_data
        ]);

        $plant_energy_graph[] = $today_energy;
        $plant_energy_graph[] = $yesterday_energy;

        if ($time == 'day') {

            $today_log_conv = isset($today_log_data) && !empty($today_log_data) ? $this->unitConversion((double)max($today_log_data), 'kWh') : [0, 'kWh'];
            $yesterday_log_conv = isset($yesterday_log_data) && !empty($yesterday_log_data) ? $this->unitConversion((double)max($yesterday_log_data), 'kWh') : [0, 'kWh'];
        } else if ($time == 'month') {

            $monthly_gen = MonthlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGeneration');
            $pre_monthly_gen = MonthlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->where('created_at', 'LIKE', $pre_date . '%')->sum('monthlyGeneration');

            $today_log_conv = $this->unitConversion((double)$monthly_gen, 'kWh');
            $yesterday_log_conv = $this->unitConversion((double)$pre_monthly_gen, 'kWh');
        } else if ($time == 'year') {

            $yearly_gen = YearlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->whereYear('created_at', $date)->sum('yearlyGeneration');
            $pre_yearly_gen = YearlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->whereYear('created_at', $pre_date)->sum('yearlyGeneration');

            $today_log_conv = $this->unitConversion((double)$yearly_gen, 'kWh');
            $yesterday_log_conv = $this->unitConversion((double)$pre_yearly_gen, 'kWh');
        }

        $total_gen = TotalProcessedPlantDetail::whereIn('plant_id', $plants_id)->sum('plant_total_generation');

        $total_gen_conv = $this->unitConversion((double)$total_gen, 'kWh');

        $generation_log['plant_total_generation'] = round($total_gen_conv[0], 2) . '' . $total_gen_conv[1];
        $generation_log['total_today'] = round($today_log_conv[0], 2) . '' . $today_log_conv[1];
        $generation_log['total_yesterday'] = round($yesterday_log_conv[0], 2) . '' . $yesterday_log_conv[1];
        $generation_log['today_date'] = $graph_today_date;
        $generation_log['yesterday_date'] = $graph_yesterday_date;
        $generation_log['today_time'] = isset($unique_time_arr) && !empty($unique_time_arr) ? $unique_time_arr : array();
        $generation_log['plant_energy_graph'] = $plant_energy_graph;

        return $generation_log;
    }

    public function dashboardAlertGraph(Request $request)
    {

        $time = $request->time;
        $dates = strtotime($request->date);
        $graph_today_date = date('Y-m-d');

        if ($request->from_url == 'dashboard') {

            $filter = json_decode($request->filter, true);
            $plants_name = json_decode($request->plant_name, true);
            $filter_arr = [];
            $plant_name = [];
            $plants_sites = [];

            if (!empty($filter)) {

                foreach ($filter as $key => $flt) {

                    $filter_arr[$key] = $flt;
                }
            }


            foreach ($plants_name as $pl_name) {

                $plant_name[] = $pl_name;
            }

            if (empty($plant_name)) {

                if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                    $plant_names = Plant::pluck('id');
                    $plant_names = $plant_names->toArray();
                } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                    $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                    $plant_names = $plant_names->toArray();
                }

                $plant_name = $plant_names;
            }

            $plants_id = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id');
            $plants_sites = PlantSite::whereIn('plant_id', $plants_id)->pluck('site_id')->toArray();
        }

        if ($request->from_url == 'plant') {

            $plant_id = $request->plant_id;
            $plants_sites = PlantSite::where('plant_id', $plant_id)->pluck('site_id')->toArray();
        }

        if ($time == 'day') {
            $date = date('Y-m-d', $dates);
            $graph_today_date = date('d-m-Y', strtotime($date));
        } else if ($time == 'month') {
            $date = date('Y-m', $dates);
            $graph_today_date = date('m-Y', strtotime($date));
        } else if ($time == 'year') {
            $date = $request->date;
            $graph_today_date = $date;
        }

        $today_log_time = [];
        $unique_time_arr = [];
        $fault_log_data_arr = [];
        $alarm_log_data_arr = [];
        $rtu_log_data_arr = [];
        $plant_alert_graph = array();

        if ($time == 'day') {

            $unique_time_arr = ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '23:59'];
            if ($request->from_url == 'plant') {

                $unique_time_arr_display = ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '23:59'];
            } else {

                $unique_time_arr_display = ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '23:59'];
            }
            if ($unique_time_arr) {

                for ($i = 0; $i < (count($unique_time_arr) - 1); $i++) {

                    $res_fault = DB::table('fault_and_alarms')
                        ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                        ->select('fault_and_alarms.*', 'fault_alarm_log.fault_and_alarm_id',
                            'fault_alarm_log.created_at', 'fault_alarm_log.siteId')
                        ->whereIn('fault_alarm_log.siteId', $plants_sites)
                        ->whereBetween('fault_alarm_log.created_at', [$date . ' ' . $unique_time_arr[$i] . ':00', $date . ' ' . $unique_time_arr[$i + 1] . ':00'])
                        ->where('fault_and_alarms.type', 'Fault')
                        ->count();

                    $res_alarm = DB::table('fault_and_alarms')
                        ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                        ->select('fault_and_alarms.*', 'fault_alarm_log.fault_and_alarm_id',
                            'fault_alarm_log.created_at', 'fault_alarm_log.siteId')
                        ->whereIn('fault_alarm_log.siteId', $plants_sites)
                        ->whereBetween('fault_alarm_log.created_at', [$date . ' ' . $unique_time_arr[$i] . ':00', $date . ' ' . $unique_time_arr[$i + 1] . ':00'])
                        ->where('fault_and_alarms.type', 'Alarm')
                        ->count();

                    $res_rtu = DB::table('fault_and_alarms')
                        ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                        ->select('fault_and_alarms.*', 'fault_alarm_log.fault_and_alarm_id',
                            'fault_alarm_log.created_at', 'fault_alarm_log.siteId')
                        ->whereIn('fault_alarm_log.siteId', $plants_sites)
                        ->whereBetween('fault_alarm_log.created_at', [$date . ' ' . $unique_time_arr[$i] . ':00', $date . ' ' . $unique_time_arr[$i + 1] . ':00'])
                        ->where('fault_and_alarms.type', 'RTU')
                        ->count();

                    $fault_log_data_arr[] = $res_fault;
                    $alarm_log_data_arr[] = $res_alarm;
                    $rtu_log_data_arr[] = $res_rtu;

                }
            }
        } else if ($time == 'month') {

            $explode_data = explode('-', $date);
            $mon = $explode_data[1];
            $yer = $explode_data[0];

            $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);
            for ($i = 1; $i <= $dd; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $unique_time_arr_display[] = $i;

                $res_fault = DB::table('fault_and_alarms')
                    ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                    ->select('fault_and_alarms.*', 'fault_alarm_log.fault_and_alarm_id',
                        'fault_alarm_log.created_at', 'fault_alarm_log.siteId')
                    ->whereIn('fault_alarm_log.siteId', $plants_sites)
                    ->where('fault_alarm_log.created_at', 'LIKE', $date . '-' . $i . '%')
                    ->where('fault_and_alarms.type', 'Fault')
                    ->count();

                $res_alarm = DB::table('fault_and_alarms')
                    ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                    ->select('fault_and_alarms.*', 'fault_alarm_log.fault_and_alarm_id',
                        'fault_alarm_log.created_at', 'fault_alarm_log.siteId')
                    ->whereIn('fault_alarm_log.siteId', $plants_sites)
                    ->where('fault_alarm_log.created_at', 'LIKE', $date . '-' . $i . '%')
                    ->where('fault_and_alarms.type', 'Alarm')
                    ->count();

                $res_rtu = DB::table('fault_and_alarms')
                    ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                    ->select('fault_and_alarms.*', 'fault_alarm_log.fault_and_alarm_id',
                        'fault_alarm_log.created_at', 'fault_alarm_log.siteId')
                    ->whereIn('fault_alarm_log.siteId', $plants_sites)
                    ->where('fault_alarm_log.created_at', 'LIKE', $date . '-' . $i . '%')
                    ->where('fault_and_alarms.type', 'RTU')
                    ->count();

                $fault_log_data_arr[] = $res_fault;
                $alarm_log_data_arr[] = $res_alarm;
                $rtu_log_data_arr[] = $res_rtu;
            }
        } else if ($time == 'year') {

            for ($i = 1; $i <= 12; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $unique_time_arr_display[] = substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3);

                $res_fault = DB::table('fault_and_alarms')
                    ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                    ->select('fault_and_alarms.*', 'fault_alarm_log.fault_and_alarm_id',
                        'fault_alarm_log.created_at', 'fault_alarm_log.siteId')
                    ->whereIn('fault_alarm_log.siteId', $plants_sites)
                    ->whereYear('fault_alarm_log.created_at', $date)
                    ->whereMonth('fault_alarm_log.created_at', $i)
                    ->where('fault_and_alarms.type', 'Fault')
                    ->count();

                $res_alarm = DB::table('fault_and_alarms')
                    ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                    ->select('fault_and_alarms.*', 'fault_alarm_log.fault_and_alarm_id',
                        'fault_alarm_log.created_at', 'fault_alarm_log.siteId')
                    ->whereIn('fault_alarm_log.siteId', $plants_sites)
                    ->whereYear('fault_alarm_log.created_at', $date)
                    ->whereMonth('fault_alarm_log.created_at', $i)
                    ->where('fault_and_alarms.type', 'Alarm')
                    ->count();

                $res_rtu = DB::table('fault_and_alarms')
                    ->join('fault_alarm_log', 'fault_and_alarms.id', 'fault_alarm_log.fault_and_alarm_id')
                    ->select('fault_and_alarms.*', 'fault_alarm_log.fault_and_alarm_id',
                        'fault_alarm_log.created_at', 'fault_alarm_log.siteId')
                    ->whereIn('fault_alarm_log.siteId', $plants_sites)
                    ->whereYear('fault_alarm_log.created_at', $date)
                    ->whereMonth('fault_alarm_log.created_at', $i)
                    ->where('fault_and_alarms.type', 'RTU')
                    ->count();

                $fault_log_data_arr[] = $res_fault;
                $alarm_log_data_arr[] = $res_alarm;
                $rtu_log_data_arr[] = $res_rtu;

            }
        }

        $fault_data = collect([
            "name" => 'Fault',
            "type" => 'bar',
            "color" => '#0f75bc',
            'barGap' => '0%',
            "data" => $fault_log_data_arr
        ]);

        $alarm_data = collect([
            "name" => 'Alarm',
            "type" => 'bar',
            "color" => '#68ad86',
            'barGap' => '0%',
            "data" => $alarm_log_data_arr
        ]);

        $rtu_data = collect([
            "name" => 'RTU',
            "type" => 'bar',
            "color" => '#ff9768',
            'barGap' => '0%',
            "data" => $rtu_log_data_arr
        ]);

        $plant_alert_graph[] = $fault_data;
        $plant_alert_graph[] = $alarm_data;
        $plant_alert_graph[] = $rtu_data;

        $generation_log['total_fault'] = array_sum($fault_log_data_arr);
        $generation_log['total_alarm'] = array_sum($alarm_log_data_arr);
        $generation_log['total_rtu'] = array_sum($rtu_log_data_arr);
        $generation_log['today_date'] = $graph_today_date;
        $generation_log['today_time'] = isset($unique_time_arr_display) && !empty($unique_time_arr_display) ? $unique_time_arr_display : array();
        $generation_log['plant_alert_graph'] = $plant_alert_graph;

        return $generation_log;
    }

    public function mainDashboardExpectedGenerationGraph(Request $request)
    {

        $filter = json_decode($request->filter, true);
        $plants_name = json_decode($request->plant_name, true);
        $filter_arr = [];
        $plant_name = [];
        $time = $request->time;
        $dates = strtotime($request->date);
        $pre_date = date('Y-m-d');
        $graph_today_date = date('Y-m-d');
        $graph_yesterday_date = date('Y-m-d');

        foreach ($filter as $key => $flt) {

            $filter_arr[$key] = $flt;
        }

        foreach ($plants_name as $pl_name) {

            $plant_name[] = $pl_name;
        }

        if (empty($plant_name)) {

            if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                $plant_names = Plant::pluck('id');
                $plant_names = $plant_names->toArray();
            } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                $plant_names = $plant_names->toArray();
            }

            $plant_name = $plant_names;
        }


        $plants_id = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id')->toArray();
//        return $plants_id;
        if ($time == 'day') {
            $date = date('Y-m-d', $dates);
            $graph_today_date = date('d-m-Y', strtotime($date));
        } else if ($time == 'month') {
            $date = date('Y-m', $dates);
            $graph_today_date = date('m-Y', strtotime($date));
        } else if ($time == 'year') {
            $date = $request->date;
            $graph_today_date = $date;
        }

        $today_log_data = [];
        $today_log_time = [];
        $yesterday_log_time = [];
        $yesterday_log_data = [];
        $sum_data = 0;
        $plant_energy_graph = array();
        $tooltip_name_today = '';
        $tooltip_name_yesterday = '';
        $graph_type = '';

        $current_generation_start_time = ProcessedCurrentVariable::select('collect_time')->whereIn('plant_id', $plants_id)->whereDate('collect_time', $date)->where('totalEnergy', '>', 0)->orderBy('collect_time', 'ASC')->first();
        $start_date_time = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->created_at)) : '05:00:00';
//        return [date($date . ' ' . $start_date_time), date($date . ' 23:59:59')];
        if ($time == 'day') {

            $tooltip_name_today = 'Actual Generation';
            $tooltip_name_yesterday = 'Expected Generation';
            $graph_type = 'line';

            $current_generation = ProcessedCurrentVariable::select('collect_time')->whereIn('plant_id', $plants_id)->whereBetween('collect_time', [date($date . ' ' . $start_date_time), date($date . ' 23:59:59')])->groupBy('collect_time')->get();
            foreach ($current_generation as $key => $today_log) {
                $today_log_time[] = date('H:i', strtotime($today_log->collect_time));
                $yesterday_log_time[] = date('H:i', strtotime($today_log->collect_time));
                $today_log_data_sum = ProcessedCurrentVariable::whereIn('plant_id', $plants_id)->where('collect_time', $today_log->collect_time)->sum('totalEnergy');
                // $today_log_data[] = $today_log_data_sum;
                $today_log_data[] = $key > 0 && $today_log_data[$key - 1] > $today_log_data_sum ? round($today_log_data[$key - 1], 2) : round($today_log_data_sum, 2);
            }

            $arr_sum = [];

            foreach ($plants_id as $pl_id) {

                $yesterday_log_data_sum = ExpectedGenerationLog::where('plant_id', $pl_id)->whereDate('created_at', '<=', $date)->orderBy('created_at', 'DESC')->first();
                $arr_sum[] = $yesterday_log_data_sum ? $yesterday_log_data_sum->daily_expected_generation : 0;
            }

            $count_arr = count($today_log_data) > 0 ? count($today_log_data) : 1;
            $yesterday_log_data = array_fill(0, $count_arr, round(array_sum($arr_sum), 2));
        } else if ($time == 'month') {

            $tooltip_name_today = 'Actual Generation';
            $tooltip_name_yesterday = 'Expected Generation';
            $graph_type = 'bar';

            $explode_data = explode('-', $date);
            $mon = $explode_data[1];
            $yer = $explode_data[0];

            $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);

            for ($i = 1; $i <= $dd; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $arr_sum = [];

                $today_log_time[] = $i;

                $today_log_data_sum = DailyProcessedPlantDetail::whereIn('plant_id', $plants_id)->where('created_at', 'LIKE', $date . '-' . $i . '%')->sum('dailyGeneration');

                $today_log_data[] = $today_log_data_sum ? round($today_log_data_sum, 2) : 0;

                foreach ($plants_id as $pl_id) {

                    $yesterday_log_data_sum = ExpectedGenerationLog::where('plant_id', $pl_id)->where('collect_time', '<=', $date . '-' . $i . ' 23:59:59')->orderBy('collect_time', 'DESC')->first();
                    $arr_sum[] = $yesterday_log_data_sum ? $yesterday_log_data_sum->daily_expected_generation : 0;
                }

                $yesterday_log_data[] = array_sum($arr_sum) ? round(array_sum($arr_sum), 2) : 0;
            }
        } else if ($time == 'year') {

            $tooltip_name_today = 'Actual Generation';
            $tooltip_name_yesterday = 'Expected Generation';
            $graph_type = 'bar';

            for ($i = 1; $i <= 12; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $today_log_time[] = substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3);

                $arr_sum = [];

                $dd = cal_days_in_month(CAL_GREGORIAN, $i, $date);

                $today_log_data_sum = MonthlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlyGeneration');

                $today_log_data[] = $today_log_data_sum > 0 ? round($today_log_data_sum, 2) : 0;

                foreach ($plants_id as $pl_id) {

                    for ($j = 1; $j <= $dd; $j++) {

                        if ($j < 10) {
                            $j = '0' . $j;
                        }

                        $yesterday_log_data_sum = ExpectedGenerationLog::where('plant_id', $pl_id)->where('created_at', '<=', $date . '-' . $i . '-' . $j . ' 23:59:59')->orderBy('created_at', 'DESC')->first();
                        $arr_sum[] = $yesterday_log_data_sum ? ($yesterday_log_data_sum->daily_expected_generation) : 0;
                    }
                }

                $yesterday_log_data[] = array_sum($arr_sum) ? round(array_sum($arr_sum), 2) : 0;

            }
        }

        if ($graph_type == 'line') {

            $today_energy = collect([
                "name" => $tooltip_name_today,
                "type" => $graph_type,
                "smooth" => true,
                "color" => '#4d8867',
                "showSymbol" => false,
                "data" => $today_log_data
            ]);

            $yesterday_energy = collect([
                "name" => $tooltip_name_yesterday,
                "type" => $graph_type,
                "smooth" => true,
                "color" => '#0f75bc',
                "showSymbol" => false,
                "data" => $yesterday_log_data
            ]);
        } else if ($graph_type == 'bar') {

            $today_energy = collect([
                "name" => $tooltip_name_today,
                "type" => $graph_type,
                "color" => '#4d8867',
                'barGap' => '0%',
                "data" => $today_log_data
            ]);

            $yesterday_energy = collect([
                "name" => $tooltip_name_yesterday,
                "type" => $graph_type,
                "color" => '#0f75bc',
                'barGap' => '0%',
                "data" => $yesterday_log_data
            ]);
        }

        $plant_energy_graph[] = $today_energy;
        $plant_energy_graph[] = $yesterday_energy;

        if ($time == 'day') {

            $today_log_conv = isset($today_log_data) && !empty($today_log_data) ? $this->unitConversion((double)max($today_log_data), 'kWh') : [0, 'kWh'];
            $yesterday_log_conv = isset($yesterday_log_data) && !empty($yesterday_log_data) ? $this->unitConversion((double)max($yesterday_log_data), 'kWh') : [0, 'kWh'];
        } else if ($time == 'month') {

            $monthly_gen = MonthlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGeneration');
            $pre_monthly_gen = array_sum($yesterday_log_data);

            $today_log_conv = $this->unitConversion((double)$monthly_gen, 'kWh');
            $yesterday_log_conv = $this->unitConversion((double)$pre_monthly_gen, 'kWh');
        } else if ($time == 'year') {

            $yearly_gen = YearlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->whereYear('created_at', $date)->sum('yearlyGeneration');
            $pre_yearly_gen = array_sum($yesterday_log_data);

            $today_log_conv = $this->unitConversion((double)$yearly_gen, 'kWh');
            $yesterday_log_conv = $this->unitConversion((double)$pre_yearly_gen, 'kWh');
        }

        $total_gen = TotalProcessedPlantDetail::whereIn('plant_id', $plants_id)->sum('plant_total_generation');

        $total_gen_conv = $this->unitConversion((double)$total_gen, 'kWh');

        $generation_log['plant_total_generation'] = round($total_gen_conv[0], 2) . '' . $total_gen_conv[1];
        $generation_log['total_today'] = round($today_log_conv[0], 2) . '' . $today_log_conv[1];
        $generation_log['total_yesterday'] = round($yesterday_log_conv[0], 2) . '' . $yesterday_log_conv[1];
        $generation_log['today_date'] = $graph_today_date;
        $generation_log['yesterday_date'] = $graph_today_date;
        $generation_log['today_time'] = isset($today_log_time) && !empty($today_log_time) ? $today_log_time : array();
        $generation_log['plant_energy_graph'] = $plant_energy_graph;

        return $generation_log;
    }

    public function dashboardExpectedGenerationGraph(Request $request)
    {

        $filter = json_decode($request->filter, true);
        $plants_name = json_decode($request->plant_name, true);
        $filter_arr = [];
        $plant_name = [];
        $time = $request->time;
        $dates = strtotime($request->date);
        $pre_date = date('Y-m-d');

        foreach ($filter as $key => $flt) {

            $filter_arr[$key] = $flt;
        }

        foreach ($plants_name as $pl_name) {

            $plant_name[] = $pl_name;
        }

        if (empty($plant_name)) {

            if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                $plant_names = Plant::pluck('id');
                $plant_names = $plant_names->toArray();
            } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                $plant_names = $plant_names->toArray();
            }

            $plant_name = $plant_names;
        }

        $plants_id = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id')->toArray();

        if ($time == 'day') {
            $date = date('Y-m-d', $dates);
            $pre_date = date('Y-m-d', strtotime(("-1 days"), $dates));
        } else if ($time == 'month') {
            $date = date('Y-m', $dates);
            $pre_date = date('Y-m', strtotime(("-1 months"), $dates));
        } else if ($time == 'year') {
            $date = $request->date;
            $pre_date = date('Y', strtotime(("-1 years"), $dates));
        }

        $today_log_data = [];
        $today_log_time = [];
        $yesterday_log_time = [];
        $yesterday_log_data = [];
        $sum_data = 0;

        if ($time == 'year') {

            $date = $request->date;

            $exp_ac_graph = ['Actual', 'Expected'];
            $legend_array = [];
            $ac_data = 0;
            $actual_percentage = 0;
            $ExpGene = 0;
            $yearlyGene = 0;
            $names = '';
            $expected_value = '';

            foreach ($exp_ac_graph as $t_m) {

                if ($t_m == 'Expected') {

                    $arr_sum = [];

                    for ($i = 1; $i <= 12; $i++) {

                        if ($i < 10) {
                            $i = '0' . $i;
                        }

                        $dd = cal_days_in_month(CAL_GREGORIAN, $i, $date);

                        foreach ($plants_id as $pl_id) {

                            for ($j = 1; $j <= $dd; $j++) {

                                if ($j < 10) {
                                    $j = '0' . $j;
                                }

                                $yesterday_log_data_sum = ExpectedGenerationLog::where('plant_id', $pl_id)->where('created_at', '<=', $date . '-' . $i . '-' . $j . ' 23:59:59')->orderBy('created_at', 'DESC')->first();
                                $arr_sum[] = $yesterday_log_data_sum ? ($yesterday_log_data_sum->daily_expected_generation) : 0;
                            }
                        }
                    }

                    $ac_data = $ExpGene = array_sum($arr_sum);
                    $exp_gen_arr = $this->unitConversion($ExpGene, 'kWh');
                    $expected = $names = $legend_array[] = $t_m . ': ' . round($exp_gen_arr[0], 2) . ' ' . $exp_gen_arr[1];
                    $expected_value = round($exp_gen_arr[0], 2) . ' ' . $exp_gen_arr[1];
                } else if ($t_m == 'Actual') {

                    $ac_data = $yearlyGene = YearlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->where('created_at', 'LIKE', $date . '%')->sum('yearlyGeneration');
                    $yearly_gen_arr = $this->unitConversion($yearlyGene, 'kWh');
                    $names = $legend_array[] = $t_m . ': ' . round($yearly_gen_arr[0], 2) . ' ' . $yearly_gen_arr[1];
                }

                ${"file" . $t_m} = collect([
                    "value" => round($ac_data, 2),
                    "name" => $names,
                ]);

                $exp_ac_graph[] = ${"file" . $t_m};

            }

            if ($ExpGene > 0) {

                $actual_percentage = round(((double)$yearlyGene / (double)$ExpGene) * 100, 2);
            }

            $data['exp_ac_graph'] = $exp_ac_graph;
            $data['legend_array'] = $legend_array;
            $data['percentage'] = $actual_percentage;
            $data['expected'] = $expected;
            $data['expected_value'] = $expected_value;

            return $data;
        }
    }

    public function dashboardSavingGraph(Request $request)
    {

        $filter = json_decode($request->filter, true);
        $plants_name = json_decode($request->plant_name, true);
        $filter_arr = [];
        $plant_name = [];
        $time = $request->time;
        $dates = strtotime($request->date);
        $pre_date = date('Y-m-d');
        $graph_today_date = date('Y-m-d');

        foreach ($filter as $key => $flt) {

            if ($key == 'alarmLevel') {

                $key = $key . ' !=';
                $filter_arr[$key] = 0;
            } else {

                $filter_arr[$key] = $flt;
            }
        }

        foreach ($plants_name as $pl_name) {

            $plant_name[] = $pl_name;
        }

        if (empty($plant_name)) {

            if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                $plant_names = Plant::pluck('id');
                $plant_names = $plant_names->toArray();
            } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                $plant_names = $plant_names->toArray();
            }

            $plant_name = $plant_names;
        }

        $plants_ids = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id');

        if ($time == 'day') {
            $date = date('Y-m-d', $dates);
            $graph_today_date = date('d-m-Y', strtotime($date));
        } else if ($time == 'month') {
            $date = date('Y-m', $dates);
            $graph_today_date = date('m-Y', strtotime($date));
        } else if ($time == 'year') {
            $date = $request->date;
            $graph_today_date = $date;
        }

        $today_log_data = [];
        $today_log_time = [];
        $plant_saving_graph = array();
        $tooltip_name_today = '';
        $graph_type = '';

        $plants = Plant::whereIn('id', $plants_ids)->get(['id', 'benchmark_price']);
        $current_generation_start_time = ProcessedCurrentVariable::select('created_at')->whereIn('plant_id', $plants_ids)->whereDate('collect_time', $date)->where('totalEnergy', '>', 0)->orderBy('collect_time', 'ASC')->first();
        $start_date = $current_generation_start_time ? date('H:i:s', strtotime($current_generation_start_time->created_at)) : '05:00:00';

        $today_log_time = [];

        if ($time == 'day') {

            $graph_type = 'line';

            $current_generation = ProcessedCurrentVariable::select('created_at')->whereIn('plant_id', $plants_ids)->whereBetween('collect_time', [date($date . ' ' . $start_date), date($date . ' 23:59:59')])->groupBy('collect_time')->get();

            foreach ($current_generation as $key1 => $today_log) {

                $today_log_time[] = date('H:i', strtotime($today_log->created_at));
                $temp_log_data_sum = [];

                $today_log_data_sum = ProcessedCurrentVariable::whereIn('plant_id', $plants_ids)->where('created_at', $today_log->created_at)->sum('current_saving');

                $temp_log_data_sum = $today_log_data_sum ? (double)$today_log_data_sum : 0;

                $today_log_data[] = $key1 > 0 && $temp_log_data_sum == 0 ? round($today_log_data[$key1 - 1], 2) : round($temp_log_data_sum, 2);

            }

        } else if ($time == 'month') {

            $graph_type = 'bar';

            $explode_data = explode('-', $date);
            $mon = $explode_data[1];
            $yer = $explode_data[0];

            $dd = cal_days_in_month(CAL_GREGORIAN, $mon, $yer);
            for ($i = 1; $i <= $dd; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $today_log_time[] = $i;

                $today_log_data_sum = DailyProcessedPlantDetail::whereIn('plant_id', $plants_ids)->whereDate('created_at', $date . '-' . $i)->sum('dailySaving');

                $today_log_data[] = round($today_log_data_sum, 2);
            }
        } else if ($time == 'year') {

            $graph_type = 'bar';

            for ($i = 1; $i <= 12; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $today_log_time[] = substr(date('F', mktime(0, 0, 0, $i, 10)), 0, 3);

                $today_log_data_sum = MonthlyProcessedPlantDetail::whereIn('plant_id', $plants_ids)->whereYear('created_at', $date)->whereMonth('created_at', $i)->sum('monthlySaving');

                $today_log_data[] = round($today_log_data_sum, 2);
            }
        }

        if ($graph_type == 'line') {

            $today_saving = collect([
                "name" => $tooltip_name_today,
                "type" => $graph_type,
                "smooth" => true,
                "color" => '#0f75bc',
                "showSymbol" => false,
                "data" => $today_log_data
            ]);

        } else if ($graph_type == 'bar') {

            $today_saving = collect([
                "name" => $tooltip_name_today,
                "type" => $graph_type,
                "color" => '#0f75bc',
                'barGap' => '0%',
                "data" => $today_log_data
            ]);
        }

        $plant_saving_graph[] = $today_saving;

        if ($time == 'day') {

            $today_log_conv = isset($today_log_data) && !empty($today_log_data) ? (double)max($today_log_data) : 0;
        } else if ($time == 'month') {

            $monthly_gen = MonthlyProcessedPlantDetail::whereIn('plant_id', $plants_ids)->where('created_at', 'LIKE', $date . '%')->sum('monthlySaving');

            $today_log_conv = (double)$monthly_gen;
        } else if ($time == 'year') {

            $yearly_gen = YearlyProcessedPlantDetail::whereIn('plant_id', $plants_ids)->whereYear('created_at', $date)->sum('yearlySaving');

            $today_log_conv = (double)$yearly_gen;
        }

        $total_sav = TotalProcessedPlantDetail::whereIn('plant_id', $plants_ids)->sum('plant_total_saving');

        $total_sav_conv = $this->unitConversion((double)$total_sav, 'PKR');

        $generation_log['plant_total_saving'] = round($total_sav_conv[0], 2) . '' . $total_sav_conv[1];
        $generation_log['total_today'] = $today_log_conv;
        $generation_log['today_date'] = $graph_today_date;
        $generation_log['today_time'] = isset($today_log_time) && !empty($today_log_time) ? $today_log_time : array();
        $generation_log['plant_saving_graph'] = $plant_saving_graph;

        return $generation_log;

    }

    public function dashboardENVGraph(Request $request)
    {

        $filter = json_decode($request->filter, true);
        $plants_name = json_decode($request->plant_name, true);
        $filter_arr = [];
        $plant_name = [];
        $time = $request->time;
        $dates = strtotime($request->date);

        foreach ($filter as $key => $flt) {

            $filter_arr[$key] = $flt;
        }

        foreach ($plants_name as $pl_name) {

            $plant_name[] = $pl_name;
        }

        if (empty($plant_name)) {

            if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {
                $plant_names = Plant::where('id', ['102', '113'])->pluck('id');
                $plant_names = $plant_names->toArray();
            } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                $plant_names = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
                $plant_names = $plant_names->toArray();
            }

            $plant_name = $plant_names;
        }

        $plants_id = Plant::where($filter_arr)->whereIn('id', $plant_name)->pluck('id');

        if ($time == 'day') {

            $date = date('Y-m-d', $dates);

            $dailyGene = DailyProcessedPlantDetail::whereIn('plant_id', $plants_id)->whereDate('created_at', $date)->sum('dailyGeneration');
            $dailyGene = $dailyGene ? $dailyGene : 0;
            $dailyGeneration = $dailyGene ? $this->unitConversion((double)$dailyGene, 'kWh') : [0, 'kWh'];

            return [round($dailyGeneration[0], 2) . ' ' . $dailyGeneration[1], $dailyGene];
        } else if ($time == 'month') {

            $date = date('Y-m', $dates);

            $monthlyGene = MonthlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->where('created_at', 'LIKE', $date . '%')->sum('monthlyGeneration');
            $monthlyGene = $monthlyGene ? $monthlyGene : 0;
            $monthlyGeneration = $monthlyGene ? $this->unitConversion((double)$monthlyGene, 'kWh') : [0, 'kWh'];

            return [round($monthlyGeneration[0], 2) . ' ' . $monthlyGeneration[1], $monthlyGene];
        } else if ($time == 'year') {

            $date = $request->date;

            $yearlyGene = YearlyProcessedPlantDetail::whereIn('plant_id', $plants_id)->where('created_at', 'LIKE', $date . '%')->sum('yearlyGeneration');
            $yearlyGene = $yearlyGene ? $yearlyGene : 0;
            $yearlyGeneration = $yearlyGene ? $this->unitConversion((double)$yearlyGene, 'kWh') : [0, 'kWh'];

            return [round($yearlyGeneration[0], 2) . ' ' . $yearlyGeneration[1], $yearlyGene];
        }

    }

    public function plantprofile($id = 0)
    {
        $where_array = array();
        $where_com_array = array();
        if (Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] = $company_id;
            $where_com_array['id'] = $company_id;
        }
        $plant = Plant::where('id', $id)->where($where_array)->first();
        if ($plant == null) {
            return redirect('/home');
        }

        $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
        $plant['system_type'] = SystemType::find($plant->system_type)->type;

        $companies = Company::where($where_com_array)->get();
        $roles = Role::all();
        $plants = Plant::where($where_array)->get();
        // dd($plant);
        $users = Plant::find($id)
            ->users()
            ->where('plant_user.is_active', 'Y')
            ->where('users.roles', '!=', '1')
            ->where($where_array)
            ->get();

        // dd($users);

        return view('admin.plant.plantprofile', ['plant' => $plant, 'companies' => $companies, 'roles' => $roles, 'plants' => $plants, 'users' => $users]);
    }

    public function editPlant($id = 0)
    {
        $isPlant = Plant::findOrFail($id);

        if (!($isPlant)) {

            return redirect()->back()->with('error', 'Invalid plant ID');
        }

        $companies = [];

        if (Auth::user()->roles == 1 || Auth::user()->roles == 2) {

            $companies = Company::all();
        } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {

            $plant_arr = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
            $plant_arr = $plant_arr->toArray();

            if (!empty($plant_arr) && !in_array((string)$id, $plant_arr)) {
                return redirect()->back()->with('error', 'You have no access of that plant!');
            } else if (empty($plant_arr)) {
                return redirect()->back()->with('error', 'You have no access of that plant!');
            }

            $companies = Company::where('id', Auth::user()->company_id)->get();
        } else if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            return redirect()->back()->with('error', 'You have no access to edit plant!');
        }

        $system_type = SystemType::all();
        $plant_type = PlantType::all();
        $plants = Plant::select('siteId')->get();
        $plant_site_exist = array();
        foreach ($plants as $key => $plant) {
            array_push($plant_site_exist, $plant->siteId);
        }
        // $plant_site_exist = implode(',',$plant_site_exist);
        $plant_sites = $this->saltecSiteListData() ? $this->saltecSiteListData() : (object)[];
        $is_build = 1;

        $plant_details = Plant::with(['plant_sites', 'plant_mppts'])->where('id', $id)->get();
        $plant_details = $plant_details ? $plant_details[0] : (object)[];
        $plant_sites_arr = PlantSite::where('plant_id', $id)->pluck('site_id');

        return view('admin.plant.editplant', ['plant_details' => $plant_details, 'plant_sites_arr' => $plant_sites_arr, 'companies' => $companies, 'plant_sites' => $plant_sites, 'plants' => $plant_site_exist, 'system_types' => $system_type, 'plant_types' => $plant_type, 'is_build' => $is_build]);
    }

    public function updatePlant(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make($request->all(), [
            'plant_name' => 'required',
            'plant_type' => 'required',
            'capacity' => 'required',
            'timezone' => 'required',
            'company_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'message' => $validator->errors()->first(),
                'error_status' => 1
            ]);
        }

        try {

            $plant = Plant::findOrFail($id);

            $plant->company_id = $request->company_id;
            $plant->plant_name = $request->plant_name;
            $plant->timezone = $request->timezone;
            $plant->phone = $request->phone;
            $plant->location = $request->location;
            $plant->loc_lat = $request->loc_lat;
            $plant->loc_long = $request->loc_long;
            $plant->city = $request->city;
            $plant->province = $request->province;
            $plant->phone = $request->phone;
            $plant->capacity = $request->capacity;
            $plant->benchmark_price = $request->benchmark_price;
            $plant->plant_type = $request->plant_type;
            $plant->system_type = $request->system_type;
//            $plant->meter_type = $request->meter_type;
//            $plant->meter_serial_no = $request->meter_serial_no;
            $plant->ratio_factor = $request->ratio_factor;
            $plant->angle = $request->angle;
            $plant->azimuth = $request->azimuth;
            $plant->expected_generation = $request->expected_generation;
            $plant->api_key = $request->led_api_key;
            $plant->updated_by = Auth::user()->id;
            $plant->updated_by_at = date('Y-m-d H:i:s');
            $plant->location = $request->location;

            if ($files = $request->file('plant_pic')) {

                $plant_pic = date("dmyHis.") . gettimeofday()["usec"] . '_' . $files->getClientOriginalName();
                $files->move(public_path('plant_photo'), $plant_pic);
                $plant->plant_pic = $plant_pic;
            }

            $plant->save();

            $dlt_data = PlantSite::where('plant_id', $id)->delete();
            $dlt_data_1 = PlantMPPT::where('plant_id', $id)->delete();

//            $plant_site = new PlantSite();
//            $plant_site_array = $request->siteId;
//
//            for ($i = 0; $i < count($plant_site_array); $i++) {

            $plant_site = new PlantSite();

            $plant_site->plant_id = $id;
            $plant_site->site_id = $request->site_id;
            $plant_site->online_status = 'Y';
            $plant_site->updated_by = Auth::user()->id;
            $plant_site->updated_by_at = date('Y-m-d H:i:s');

            $plant_site->save();
//            }

//            $json_mppt = json_decode($request->mppt_str, true);
//
//            foreach ($json_mppt as $key => $mppt) {
//
//                $plant_mppt = new PlantMPPT();
//
//                $plant_mppt->plant_id = $id;
//                $plant_mppt->total_mppt = $request->total_mppt;
//                $plant_mppt->string = $key;
//                $plant_mppt->string_mppt = $mppt;
//
//                $plant_mppt->save();
//            }

            $plantAllUsers = PlantUser::where('plant_id', $id)->get();

            foreach ($plantAllUsers as $key => $user) {

                $userCompanies = UserCompany::where('user_id', $user->user_id)->get();

                if (!$userCompanies->contains('company_id', $plant->company_id)) {

                    $addUserCompany = new UserCompany();

                    $addUserCompany->user_id = $user->user_id;
                    $addUserCompany->company_id = $plant->company_id;

                    $addUserCompany->save();
                }
            }

            $expected_generation['plant_id'] = $id;
            $expected_generation['daily_expected_generation'] = $request->expected_generation;
            $expected_generation['created_at'] = date('Y-m-d H:i:s');
            $expected_generation['updated_at'] = date('Y-m-d H:i:s');

            $expected_generation_exist = ExpectedGenerationLog::where('plant_id', $id)->whereDate('created_at', date('Y-m-d'))->first();
            $expected_generation_exist_1 = ExpectedGenerationLog::where('plant_id', $id)->where('daily_expected_generation', $request->expected_generation)->first();

            if ($expected_generation_exist || $expected_generation_exist_1) {

                if ($expected_generation_exist) {

                    $exp_gen = ExpectedGenerationLog::findOrFail($expected_generation_exist->id);
                } else if ($expected_generation_exist_1) {

                    $exp_gen = ExpectedGenerationLog::findOrFail($expected_generation_exist_1->id);
                }

                $exp_gen->plant_id = $id;
                $exp_gen->created_at = date('Y-m-d H:i:s');
                $exp_gen->updated_at = date('Y-m-d H:i:s');
                $exp_gen->daily_expected_generation = $request->expected_generation;

                $exp_gen->save();
            } else {

                $expected_generation_log = ExpectedGenerationLog::create($expected_generation);
            }

            $arr_sum = [];
            $date = date('Y');

            for ($i = 1; $i <= 12; $i++) {

                if ($i < 10) {
                    $i = '0' . $i;
                }

                $dd = cal_days_in_month(CAL_GREGORIAN, $i, $date);

                for ($j = 1; $j <= $dd; $j++) {

                    if ($j < 10) {
                        $j = '0' . $j;
                    }

                    $yesterday_log_data_sum = ExpectedGenerationLog::where('plant_id', $id)->where('created_at', '<=', $date . '-' . $i . '-' . $j . ' 23:59:59')->orderBy('created_at', 'DESC')->first();
                    $arr_sum[] = $yesterday_log_data_sum ? ($yesterday_log_data_sum->daily_expected_generation) : 0;
                }
            }

            $e_g = Plant::findOrFail($id);
            $e_g->yearly_expected_generation = array_sum($arr_sum);
            $e_g->save();

            return response()->json([
                'plant_id' => $id,
                'message' => 'Plant updated successfully!',
                'error_status' => 0
            ]);
        } catch (Exception $ex) {

            return response()->json([
                'message' => $ex->getMessage(),
                'class' => 'alert-danger'
            ]);
        }
    }

    public function get_weather()
    {
//        for($i = 1; $i <=  date('t'); $i++)
//        {
//            // add the date to the dates array
//            $dates[] = date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
//        }
//
//// show the dates array
//       return $dates;

        $cities = Plant::select("city")
            ->groupBy('city')
            ->get();

        $Weather = Weather::whereNotIn('city', [$cities])->get();
        if (count($cities) > 0) {
//            return $cities;
            foreach ($cities as $key => $city) {
                if ($city->city == 'Karachi City') {
                    $city->city = 'Karachi';
                }
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://api.openweathermap.org/data/2.5/forecast?q=" . $city->city . "&appid=dc8dd8343213903cdce7005937a7ca4d&units=metric",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                $city_weather = json_decode($response);
//                return json_encode($city_weather);
//                 dd($city_weather);
//                 exit();
                $weather = [];
                if ($city_weather && isset($city_weather->list) && $city_weather->list) {
                    foreach ($city_weather->list as $key => $value) {
                        // dd($value);
                        $weather['city'] = $city->city;
                        $weather['condition'] = $value->weather[0]->main;
                        $weather['temperature'] = round($value->main->temp);
                        $weather['temperature_min'] = round($value->main->temp_min);
                        $weather['temperature_max'] = round($value->main->temp_max);
                        $weather['created_at'] = $value->dt_txt;
                        $weather['updated_at'] = $value->dt_txt;
                        $weather['get_sunrise'] = $city_weather->city->sunrise;
                        $weather['get_sunset'] = $city_weather->city->sunset;
                        $weather['sunrise'] = gmdate("h:i:A", $city_weather->city->sunrise + 18000);
                        $weather['sunset'] = gmdate("h:i:A", $city_weather->city->sunset + 18000);
                        $weather['icon'] = $value->weather[0]->icon;
                        // dd($weather);

                        $weather_exits = Weather::where('created_at', $weather['created_at'])->where('city', $city_weather->city->name)->get();
//                        return $weather_exits;
                        if (count($weather_exits) > 0) {
                            $new_weather = Weather::findOrFail($weather_exits[0]['id']);
                            $res = $new_weather->fill($weather)->save();
                        } else {
                            $res = Weather::create($weather);
                        }
                    }
                }
                $dates = [];
                $monthlyWeatherArray = [];

                for ($i = 1; $i <= date('t'); $i++) {
                    // add the date to the dates array
                    $dates[] = date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
                }
                for ($m = 0; $m < count($dates); $m++) {
                    $weather = Weather::where('city', $city->city)->where('created_at', 'LIKE', $dates[$m] . '%')->get();
//                return $city->city;
                    for ($q = 0; $q < count($weather); $q++) {
                        array_push($monthlyWeatherArray, $weather[$q]['icon']);
                    }
                    $counts = array_count_values($monthlyWeatherArray);
                    arsort($counts);
                    $top_with_count = array_slice($counts, 0, 1, true);
//                    $explodeData = explode('-', $date);
//                    $mon = $explodeData[1];
//                    $yer = $explodeData[0];
                    $top = array_keys($top_with_count);
                    $icon = '';
                    if ($top) {
                        $icon = $top[0];
                    }
                    $dailyWeatherExists = DailyWeatherModel::where(['city' => $city->city, 'date' => $dates[$m]])->first();
                    if ($dailyWeatherExists) {
                        $dailyWeatherExists->city = $city->city;
                        $dailyWeatherExists->date = $dates[$m];
                        $dailyWeatherExists->icon = $icon;
                        $dailyWeatherExists->update();

                    } else {
                        $dailyWeatherDetail = new DailyWeatherModel();
                        $dailyWeatherDetail->city = $city->city;
                        $dailyWeatherDetail->date = $dates[$m];
                        $dailyWeatherDetail->icon = $icon;
                        $dailyWeatherDetail->save();

                    }
                }
            }
        }
    }

    public function get_city($province)
    {

        if (Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] = $company_id;
        }
        $cities = Plant::select('city')->where('province', $province)->where($where_array)->groupBy('city')->get();
        if (count($cities) > 0) {
            $city_opt = '<option value="all">City</option>';
            foreach ($cities as $key => $city) {
                $city_opt .= '<option value="' . $city->city . '">' . $city->city . '</option>';
            }
        }
        echo $city_opt;
        exit;

    }

    private function saltecSiteListData()
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
        if ($res) {
            $token = $res->data;
        }
        // echo '<pre>';print_r($token);exit;

        if (isset($token) && !empty($token)) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://67.23.248.117:8089/api/sites/list?size=&startIndex=&sortProperty&sortOrder&isOnline",
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
            $plant_list = json_decode($response1);

            return $plant_list->data;
        }
    }

    private function huaweiSiteListData()
    {

        $huaweiController = new HuaweiController();
        $authData = $huaweiController->getTokenAndSessionID();

        $plantSiteListData = (object)[];

        $plantSiteListCurl = curl_init();

        curl_setopt_array($plantSiteListCurl, array(

            CURLOPT_URL => 'https://sg5.fusionsolar.huawei.com/thirdData/getStationList',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($plantSiteListData),
            CURLOPT_HTTPHEADER => array(
                'XSRF-TOKEN: ' . $authData[0],
                'Accept: application/json',
                'Content-Type: application/json',
                'Cookie: JSESSIONID=' . $authData[1] . '; XSRF-TOKEN=eyJhbGciOiJIUzI1NiJ9.eyJyYW5kb21LZXkiOiI3NDNmNDI3ZC1iYWQyLTRhZGMtYWMzZi01N2I5MDg2ZGI5NDEifQ.wXYnbs2FOh_RX_CKlqkjtj6cRDeFc3n4JxXhVLWOFm4; web-auth=true'
            ),
        ));

        $plantSiteListResponse = curl_exec($plantSiteListCurl);

        curl_close($plantSiteListCurl);

        $plantSiteListResponseData = json_decode($plantSiteListResponse);

        return $plantSiteListResponseData->data;
    }

    private function sunGrowSiteListData()
    {

        $csrfToken = "";
        $userID = "";
        $appKey = "970C445528B8EB0C10450F82D8B08A14";

        $loginCurl = curl_init();

        $loginData = [
            "appkey" => $appKey,
            "user_account" => "farrukh043@yahoo.com",
            "user_password" => "ayezah2019",
            "login_type" => "1"
        ];

        curl_setopt_array($loginCurl, array(
            CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/v1/userService/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($loginData),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'sys_code: 901',
                'lang: _en_US',
                'x-access-key: x0qapvhjzzhvy9byj0j38b3en9nacwk9'
            ),
        ));

        $response = curl_exec($loginCurl);
        curl_close($loginCurl);

        $siteListResponse = json_decode($response);
        $siteListFinalData = $siteListResponse->result_data;

        $csrfToken = $siteListFinalData->token;
        $userID = $siteListFinalData->user_id;

        $siteListCurl = curl_init();

        $siteListData = [
            "appkey" => $appKey,
            "token" => $csrfToken,
            "user_id" => $userID
        ];

        curl_setopt_array($siteListCurl, array(
            CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/v1/powerStationService/getPsList',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($siteListData),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'sys_code: 901',
                'lang: _en_US'
            ),
        ));

        $response = curl_exec($siteListCurl);

        curl_close($siteListCurl);

        $siteListResponse = json_decode($response);

        $siteListFinalData = $siteListResponse->result_data->pageList;

        return $siteListFinalData;
    }

    public function previousTenMinutesDateTime($date)
    {

        $currentDataDateTime = new \DateTime($date);
        $currentDataDateTime->modify('-10 minutes');
        $finalCurrentDataDateTime = $currentDataDateTime->format('Y-m-d H:i:s');

        return $finalCurrentDataDateTime;
    }

    public function exportinverterCsv(Request $request)
    {
        $inverterArray = json_decode($request->inverterArray, true);
        $time = $request->time;
        $requestDate = strtotime($request->Date);
        $tooltipDate = strtotime($request->Date);
        $serialNo = $request->serialNo;
        $plantID = $request->plantID;

        $plantInverterGraph = [];
        $legendArray = [];
        $plantInverterGraphYAxis = [];
        $plantid = $request->plantID;
        $daTe = $request->Date;
        $currentGeneration = [];
        if (in_array('output_power', $inverterArray) && in_array('dc_power', $inverterArray)) {
            $currentGeneration = InverterDetail::where('plant_id', $plantid)->whereDate('collect_time', $daTe)->get();
            $columns = array('Id', 'Plant_Id', 'Site_ID', 'Dv_Inverter', 'DC_Power', 'Output_Power', 'Collect_Time');
        } elseif (in_array('output_power', $inverterArray)) {
            $currentGeneration = InverterDetail::Select('id', 'plant_id', 'siteId', 'dv_inverter', 'inverterPower', 'collect_time')->where('plant_id', $plantid)->whereDate('collect_time', $daTe)->get();
            $columns = array('Id', 'Plant_Id', 'Site_ID', 'Dv_Inverter', 'Output_Power', 'Collect_Time');
        } elseif (in_array('dc_power', $inverterArray)) {
            $currentGeneration = InverterDetail::Select('id', 'plant_id', 'siteId', 'dv_inverter', 'mpptPower', 'collect_time')->where('plant_id', $plantid)->whereDate('collect_time', $daTe)->get();
            $columns = array('Id', 'Plant_Id', 'Site_ID', 'Dv_Inverter', 'DC_Power', 'Collect_Time');
        } else {

            if ($time == 'day') {

                $date = date('Y-m-d', $requestDate);
            }

            $meterTypeValue = '1';

            if (Plant::where('id', $plantID)->first()->meter_type == 'Solis') {

                $meterTypeValue = 'Inverter';
            } else {

                $meterTypeValue = '1';
            }

            $plantAllInvertersArray = SiteInverterDetail::where('plant_id', $plantID)->where('dv_inverter_type', $meterTypeValue)->get();

            $currentDataLogTime = InverterDetail::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->exists() ? InverterDetail::where('plant_id', $plantID)->whereDate('collect_time', date('Y-m-d'))->orderBy('collect_time', 'desc')->first()->collect_time : date('Y-m-d 00:10:00');
            $normalizedArray = [];
            foreach ($inverterArray as $key1 => $inv) {
                foreach ($plantAllInvertersArray as $key => $invArray) {
                    $todayLogData = [];
                    $todayLogTime = [];
                    $todayLogDataSum = 0;

                    if (strtotime($date) == strtotime(date('Y-m-d'))) {

                        $finalCurrentDataDateTime = $this->previousTenMinutesDateTime($currentDataLogTime);
                        $currentGeneration = InverterDetail::where('plant_id', $plantID)->where('dv_inverter', $invArray->dv_inverter)->whereBetween('collect_time', [date($date . ' 00:00:00'), $finalCurrentDataDateTime])->groupBy('collect_time')->get();
                    } else {

                        $currentGeneration = InverterDetail::where('plant_id', $plantID)->where('dv_inverter', $invArray->dv_inverter)->whereBetween('collect_time', [date($date . ' 00:00:00'), date($date . ' 23:59:59')])->groupBy('collect_time')->get();
                    }

                    foreach ($currentGeneration as $key => $todayLog) {

                        $todayLogTime[] = date('H:i', strtotime($todayLog->collect_time));

                        if ($inv == 'output_power') {
                            $todayLogDataSum = $todayLog->inverterPower;
                        } else if ($inv == 'dc_power') {

                            $todayLogDataSum = $todayLog->mpptPower;
                        } else if ($inv == 'normalize_power') {
                            $todayLogDataSum = ($todayLog->inverterPower / $invArray->dv_installed_dc_power) * 100;
                        }
                        $normalizePower = round($todayLogDataSum, 2);
                        array_push($normalizedArray, ['id' => $todayLog->id, 'plant_id' => $todayLog->plant_id, 'siteId' => $todayLog->siteId, 'dv_inverter' => $todayLog->dv_inverter, 'collect_time' => $todayLog->collect_time, 'normalize_power' => $normalizePower]);

                    }

                }
            }
            $currentGeneration = $normalizedArray;
            $columns = array('Id', 'Plant_Id', 'Site_ID', 'Dv_Inverter', 'Normalize Power', 'Collect_Time');
        }
        $fileName = 'InverterGraph' . $daTe . '.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $row = [];


        $callback = function () use ($currentGeneration, $columns) {

            $file = array_map('unlink', glob('/path/to/*.txt'));
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($currentGeneration as $task) {
                $row['Id'] = $task['id'];
                $row['Plant_Id'] = $task['plant_id'];
                $row['Site_ID'] = $task['siteId'];
                $row['Dv_Inverter'] = $task['dv_inverter'];
                if (isset($task['mpptPower'])) {
                    $row['DC_Power'] = $task['mpptPower'];
                }
                if (isset($task['inverterPower'])) {
                    $row['Output_Power'] = $task['inverterPower'];
                }
                if (isset($task['normalize_power'])) {
                    $row['Normalize_Power'] = $task['normalize_power'];
                }
                $row['Collect_Time'] = $task['collect_time'];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function plantInverterDetails($id = 101)
    {
        return view('admin.plant.plant-inverter-detail');
    }

    public function PlantsData(Request $request)
    {
        $plants = [];
        // $plants_dashboard = ->where($where_array)->whereIn('id', $plant_name)->get();

        if (Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            $plant_arr = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id');
            $plant_arr = (!empty($plant_arr)) ? $plant_arr->toArray() : $plant_arr;
        } else if (Auth::user()->roles == 3 || Auth::user()->roles == 4) {

            $plant_arr = Plant::where('company_id', Auth::user()->company_id)->pluck('id');
            $plant_arr = (!empty($plant_arr)) ? $plant_arr->toArray() : $plant_arr;
        } else {
            $plant_arr = Plant::pluck('id');
            $plant_arr = (!empty($plant_arr)) ? $plant_arr->toArray() : $plant_arr;
        }

        if (count($plant_arr) > 0) {
            $plants = Plant::with(['latest_processed_current_variables', 'latest_fault_alarm_log', 'latest_yearly_processed_plant_detail'])->whereIn('id', $plant_arr)->orderBy('plant_name', 'ASC')->get();
        }

        if (count($plant_arr) > 0 && count($plants) == 1) {
            return redirect()->route('admin.plant.details', ['id' => $plants[0]->id]);
        }

        if (count($plant_arr) > 0) {
            $plants = $plants->map(function ($plant) {
                $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
                $plant['system_type'] = SystemType::find($plant->system_type)->type;
                $conv_gen = $this->unitConversion((double)$plant->yearly_expected_generation, 'kWh');
                $conv_gen_1 = $plant->latest_yearly_processed_plant_detail != null ? $this->unitConversion((double)$plant->latest_yearly_processed_plant_detail->yearlyGeneration, 'kWh') : [0, 'kWh'];
                $plant['yearly_expected_generation'] = round($conv_gen[0], 2) . ' ' . $conv_gen[1];
                $plant['yearly_processed_detail'] = round($conv_gen_1[0], 2) . ' ' . $conv_gen_1[1];

                return $plant;
            });
        }

        return view('admin.plant.plants-data', ['plants' => $plants]);
    }
}
