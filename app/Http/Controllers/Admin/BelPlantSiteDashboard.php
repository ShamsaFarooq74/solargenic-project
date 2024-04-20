<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\PlantSiteDataController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HardwareAPIData\HuaweiController;
use App\Http\Controllers\HardwareAPIData\SolisController;
use App\Http\Controllers\HardwareAPIData\SunGrowController;
use App\Http\Models\AllPlantsCumulativeData;
use App\Http\Models\AllPlantsCumulativeDataHistory;
use App\Http\Models\Company;
use App\Http\Models\CronJobTime;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\DailyProcessedPlantEMIDetail;
use App\Http\Models\DailyWeatherModel;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\Inverter;
use App\Http\Models\InverterDetail;
use App\Http\Models\InverterDetailHistory;
use App\Http\Models\InverterEMIDetail;
use App\Http\Models\InverterEnergyLog;
use App\Http\Models\InverterEnergyLogHistory;
use App\Http\Models\InverterMPPTDetail;
use App\Http\Models\InverterSerialNo;
use App\Http\Models\InverterVersionInformation;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\MonthlyProcessedPlantEMIDetail;
use App\Http\Models\Plant;
use App\Http\Models\PlantMeterType;
use App\Http\Models\ProcessedCurrentVariableHistory;
use App\Http\Models\SolarEnergyUtilization;
use App\Http\Models\PlantMPPT;
use App\Http\Models\PlantSite;
use App\Http\Models\PlantType;
use App\Http\Models\PlantUser;
use App\Http\Models\ProcessedCurrentVariable;
use App\Http\Models\Setting;
use App\Http\Models\SiteInverterDetail;
use App\Http\Models\StationBattery;
use App\Http\Models\StationBatteryHistory;
use App\Http\Models\StationBatteryData;
use App\Http\Models\SystemType;
use App\Http\Models\TicketAgent;
use App\Http\Models\TotalProcessedPlantDetail;
use App\Http\Models\User;
use App\Http\Models\UserCompany;
use App\Http\Models\Weather;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\YearlyProcessedPlantEMIDetail;
use App\Http\Traits\sungrowRealTimeAnimationData;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class BelPlantSiteDashboard extends Controller
{
    public function userPlantSiteDetail($id, $inverterSiteId)
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

        if (!$plant) {
            return redirect()->back()->with('error', 'No plant found!');
        }

        $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
        $plant['system_type'] = SystemType::find($plant->system_type)->type;

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
        $plantSiteData = array();
        $current_data = ProcessedCurrentVariable::select('current_generation', 'current_consumption', 'current_grid', 'grid_type', 'comm_failed', 'created_at','collect_time')->where('plant_id', $id)->orderBy('collect_time', 'desc')->first();

        $current_generation = isset($current_data) ? (double)$current_data->current_generation : 0;

        $current_consumption = isset($current_data) ? (double)$current_data->current_consumption : 0;
        $current_grid = isset($current_data) ? (double)$current_data->current_grid : 0;
        $current_grid_type = isset($current_data) ? $current_data->grid_type : '';
        $current['date'] = isset($current_data) ? $current_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
        $curr_gen_arr = $this->unitConversion($current_generation, 'kW');
        if($plant->meter_type == 'Solis'){
            $curr_con_arr = $this->unitConversion($current_consumption, 'W');
        }else{
            $curr_con_arr = $this->unitConversion($current_consumption, 'kW');
        }
        $curr_grid_arr = $this->unitConversion($current_grid, 'kW');
        $current['generation'] = round($curr_gen_arr[0], 2) . ' ' . $curr_gen_arr[1];
        $current['consumption'] = round($curr_con_arr[0], 2) . ' ' . $curr_con_arr[1];
        $current['grid'] = round($curr_grid_arr[0], 2) . ' ' . $curr_grid_arr[1];
        $current['grid_type'] = $current_grid_type;
        $current['comm_fail'] = isset($current_data) ? (int)$current_data->comm_failed : 0;
        $siteId = $inverterSiteId;

        $minus_3_hours = date('Y-m-d H:i:s', strtotime('-3 hours', strtotime(date('Y-m-d H:i:s'))));
        if ($plant->city == 'Karachi City') {
            $plant->city = 'Karachi';
        }
        $inverterDetail = InverterDetail::where('plant_id',$id)->where('siteId',$siteId)->latest()->first();
        $weather = Weather::where('city', $plant->city)->whereBetween('created_at', [$minus_3_hours, date('Y-m-d H:i:s')])->first();

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
                if($ticket_agents_name && $ticket_agents_name->name){
                    array_push($agents_array, $ticket_agents_name->name);
                }
            }
            $ticket->agents = implode(',', $agents_array);
        }
        $weatherData = [];
        $weather = [];
        $dateTime = new \DateTime();
        $date = $dateTime->format('Y-m-d');
        $weather_Date = $dateTime->format('Y-m-d H') . ":00:00";
        $weatherStarttime = $dateTime->modify("-2 Hours")->format('Y-m-d H') . ":00:00";
        $plantData = Plant::where('id', $id)->first();
        if ($plantData) {
            $plantCity = $plantData->city;
        } else {
            $plantCity = '';
        }
        for ($i = 1; $i <= 5; $i++) {

            $weather['dayName' . $i] = date('D', strtotime(date('Y-m-d')));
        }
        for ($i = 0; $i < 5; $i++) {
            $currentTime = date('H') . ':00:00';
            $weather_Date = $date . ' ' . $currentTime;
            $time = strtotime($weather_Date) - (2 * 60 * 60);
            $weatherStarttime = date("Y-m-d H", $time) . ":00:00";
            $weather['todayMin'] = Weather::whereDate('created_at', $date)->where('city', $plantCity)->min('temperature_min');
            $weather['todayMax'] = Weather::whereDate('created_at', $date)->where('city', $plantCity)->max('temperature_max');
            $weather['sunrise'] = Weather::whereDate('created_at', $date)->where('city', $plantCity)->max('sunrise');
            $weather['sunset'] = Weather::whereDate('created_at', $date)->where('city', $plantCity)->max('sunset');

            $weathetTimeBet = [$weatherStarttime, $weather_Date];
            $weather['icon'] = Weather::whereBetween('created_at', $weathetTimeBet)->where('city', $plantCity)->exists() ? Weather::whereBetween('created_at', $weathetTimeBet)->where('city', $plantCity)->first()->icon : '01d';
            $dowMap = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
            $dow_numeric = $dateTime->format('w');
            $dowMap[$dow_numeric];
            // return [$dowMap,$dow_numeric,"Dsdsds",$dowMap[$dow_numeric]];
            $weather['day'] = $dowMap[$dow_numeric];
            array_push($weatherData, $weather);
            $date = $dateTime->modify("+1 days")->format('Y-m-d');
        }
        $saltecLiveData = DB::table('saltec_push_response')->where('site_id', $siteId)->where('device_type','!=','')->whereDate('collect_time',Date('Y-m-d'))->orderBy('collect_time',"desc")->first();
        $plantMPMMSiteData = [];
        $plantMSGWSiteData = [];
        $finalDataArray = [];
        if ($saltecLiveData) {
            $response = json_decode($saltecLiveData->response);
            $final_processed_data = $response->data;
            $plant_inverter_final_data = $final_processed_data;
            if (isset($final_processed_data) && $final_processed_data) {

                foreach ($final_processed_data as $key => $plant_final_processed_data) {

                    if ($plant_final_processed_data->DeviceType == "MCMT" || $plant_final_processed_data->DeviceType == "MGCW" || $plant_final_processed_data->DeviceType == "MGCE") {
                        $plantSiteData =  $plant_final_processed_data;
                    }
                    if($plant_final_processed_data->DeviceType == "MGCE" || $plant_final_processed_data->DeviceType == "MGCW"){
                        $salteclatestData = DB::table('saltec_push_response')
                        ->where('site_id', $siteId)
                        ->where('device_type', $plant_final_processed_data->DeviceType)
                        ->orderBy('collect_time', 'desc');
                        if ($plant_final_processed_data->DeviceType == 'MGCE') {
                            $salteclatestData->skip(1); // offset by 1 to get the second last response
                        }
                        $salteclatestData = $salteclatestData->take(1) // get only 1 result
                            ->value('response');
                            if ($salteclatestData) {
                            
                                $latestResponse = json_decode($salteclatestData);
                                $latest_final_processed_data = $latestResponse->data;
                    
                                if (isset($latest_final_processed_data) && $latest_final_processed_data) {
                                    $MGCEResponse = [];
                                    foreach ($latest_final_processed_data as $key18 => $plant_latest_final_processed_data) {
                    
                                        if($plant_latest_final_processed_data->DeviceType == "MGCE" || $plant_latest_final_processed_data->DeviceType == "MGCW") {
                                            $MGCEResponse =  (object)array_merge((array)$MGCEResponse, (array)$plant_latest_final_processed_data);
                                            $plantSiteData = $MGCEResponse;
                                        }
                                        if($plant_latest_final_processed_data->DeviceType == "MSGW" ){
                                            // return [json_encode($plant_latest_final_processed_data->DeviceType),json_encode($plant_latest_final_processed_data)];
                                            // return json_encode($plant_latest_final_processed_data->L1Voltage);
                                            if(isset($plant_latest_final_processed_data->L1Voltage)){

                                            $plantSiteData->l1Voltage = $plant_latest_final_processed_data->L1Voltage;
                                            $plantSiteData->l2Voltage = $plant_latest_final_processed_data->L2Voltage;
                                            $plantSiteData->l3Voltage = $plant_latest_final_processed_data->L3Voltage;

                                            }
                                        }
                                    }
                                }
                            }
                    }
                    if($plant_final_processed_data->DeviceType == "MPMM"){
                        $plantMPMMSiteData =  $plant_final_processed_data;
                        $finalDataArray['FaultCode'] = 'N/A'; 
                    }
                    if($plant_final_processed_data->DeviceType == "MSGW"){
                        $plantMSGWSiteData =  $plant_final_processed_data;
                        $finalDataArray['FaultCode'] = isset($plantMSGWSiteData->FaultCode) ? $plantMSGWSiteData->FaultCode: 'N/A' ; 
                    }
                    if($plant_final_processed_data->DeviceType == "MH2M"){
                        $plantMH2MSiteData =  $plant_final_processed_data;
                        $finalDataArray['FaultCode'] = isset($plantMH2MSiteData->Fault_Code) ? $plantMH2MSiteData->Fault_Code: 'N/A';
                    }
                }
            }
        }

        $daily_data = DailyProcessedPlantDetail::select('dailyGeneration', 'dailyConsumption', 'dailyGridPower', 'dailyBoughtEnergy', 'dailySellEnergy', 'dailySaving', 'created_at')->where('plant_id', $id)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'DESC')->first();
        $daily_generation = $daily_data ? (double)$daily_data->dailyGeneration : 0;
        $daily_consumption = $daily_data ? (double)$daily_data->dailyConsumption : 0;
        $daily_grid = $daily_data ? (double)$daily_data->dailyGridPower : 0;
        $daily_bought_energy = $daily_data ? (double)$daily_data->dailyBoughtEnergy : 0;
        $daily_sell_energy = $daily_data ? (double)$daily_data->dailySellEnergy : 0;
        $daily_saving = $daily_data ? (double)$daily_data->dailySaving : 0;
        $daily['date'] = $daily_data ? $daily_data->created_at->format(date('Y-m-d H:i:s')) : date('Y-m-d H:i:s');
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

        $dataArray = [

            'plant' => $plant,
            'current' => $current,
            'daily' => $daily,
            'weather' => $weather,
            'weatherDetails' => $weatherData,
            'inverterDetail' => $inverterDetail,
            'tickets' => $tickets,
            'saltecLiveData' => $plantSiteData,
            'saltecMPMMLiveData' => $plantMPMMSiteData,
            'plantMSGWSiteData' => $plantMSGWSiteData,
            'finalDataArray' => $finalDataArray,
        ];
        return view('system_dashboard', $dataArray);
    } 
    public function siteDashboardGraph(Request $request){

        // return $request->all();

        $date = date('Y-m-d', strtotime($request->date));
        $historyArray = $request->historyCheckBoxArray;
        $historyArray = (array)$historyArray;

        if (in_array("l1_grid_power", $historyArray) || in_array("l2_grid_power", $historyArray) || in_array("l3_grid_power", $historyArray) || in_array("total_grid_power", $historyArray) || in_array("l1_load_power", $historyArray) || in_array("l2_load_power", $historyArray) || in_array("l3_load_power", $historyArray) || in_array("total_load_power", $historyArray) || in_array("total_pv_power", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'kW',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'white'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => 'white',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
                
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }


        if (in_array("l1_grid_frequency", $historyArray) || in_array("l2_grid_frequency", $historyArray) || in_array("l3_grid_frequency", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'HZ',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'white'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => 'white',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }
        if (in_array("wifi_signal_strength", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'dBm',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'white'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => 'white',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }
        if (in_array("l1_grid_current", $historyArray) || in_array("l2_grid_current", $historyArray) || in_array("l3_grid_current", $historyArray)) {

            $yAxisObject = collect([

                "type" => 'value',
                "name" => 'A',
                "splitNumber" => 4,
                "minInterval" => 1,
                "fill" => true,
                "splitLine" => [
                    "lineStyle" => [
                        "color" => 'white'
                    ]
                ],
                "axisLine" => [
                    "show" => true,
                    "lineStyle" => [
                        "color" => 'white',
                    ]
                ],
                "axisTick" => [
                    "show" => true,
                    "alignWithLabel" => true
                ],
            ]);

            $plantHistoryGraphYAxis[] = $yAxisObject;
        }
        
    
        $plantHistoryGraph = [];
        $legendArray = [];
        $generationValue = 0;
        $consumptionValue = 0;
        $buyValue = 0;
        $sellValue = 0;
        $savingValue = 0;
        $graphType = 'line';
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

            $currentGeneration = InverterDetail::where(['plant_id'=> $request->plantID, 'siteId'=>$request->plantSiteId])->whereDate('collect_time', Date('Y-m-d'))->groupBy('collect_time')->get();
        } else {

            $currentGeneration = InverterDetailHistory::where(['plant_id'=> $request->plantID, 'siteId'=>$request->plantSiteId])->whereDate('collect_time', $date)->groupBy('collect_time')->get();
        }


        foreach ($historyArray as $key => $current) {

            $todayLogData = [];
            $todayLogTime = [];
            $todayLogDataSum = 0;
            $todayLogConsumedPv = 0;
            $graphColor = '';

                $tooltipDate = date('d-m-Y', strtotime($date));



                if ($current == "l1_grid_power") {

                    $todayLogData = $currentGeneration->pluck('output_power_l1');
                    $legendArray[] = "L1 Grid Power";

                    $historyObject = collect([

                        "name" => "L1 Grid Power",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "l2_grid_power") {

                    $todayLogData = $currentGeneration->pluck('output_power_l2');

                    $legendArray[] = "L2 Grid Power";

                    $historyObject = collect([

                        "name" => "L2 Grid Power",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "l3_grid_power") {

                    $todayLogData = $currentGeneration->pluck('output_power_l3');

                    $legendArray[] = "L3 Grid Power";

                    $historyObject = collect([

                        "name" => "L3 Grid Power",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "total_grid_power") {

                    $todayLogData = $currentGeneration->pluck('totalGridPower');

                    $legendArray[] = "Total Grid Power";

                    $historyObject = collect([

                        "name" => "Total Grid Power",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "l1_grid_current") {

                    $todayLogData = $currentGeneration->pluck('phase_current_r');

                    $legendArray[] = "L1 Grid Current";

                    $historyObject = collect([

                        "name" => "L1 Grid Current",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 1,
                    ]);
                } else if ($current == "l2_grid_current") {
                    
                    $todayLogData = $currentGeneration->pluck('phase_current_s');

                    $legendArray[] = "L2 Grid Current";

                    $historyObject = collect([

                        "name" => "L2 Grid Current",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 1,
                    ]);
                } else if ($current == "l3_grid_current") {

                    $todayLogData = $currentGeneration->pluck('phase_current_t');

                    $legendArray[] = "L3 Grid Current";

                    $historyObject = collect([

                        "name" => "L3 Grid Current",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 1,
                    ]);
                } else if ($current == "l1_load_power") {

                    $legendArray[] = "L1 Load Power";

                    $historyObject = collect([

                        "name" => "L1 Load Power",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "l2_load_power") {

                    $legendArray[] = "L2 Load Power";

                    $historyObject = collect([

                        "name" => "L2 Load Power",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "l3_load_power") {

                    $legendArray[] = "L3 Load Power";

                    $historyObject = collect([

                        "name" => "L3 Load Power",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "total_load_power") {

                    $todayLogData = $currentGeneration->pluck('total_output_power');

                    $legendArray[] = "Total Load Power";

                    $historyObject = collect([

                        "name" => "Total Load Power",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "total_pv_power") {

                    $todayLogData = $currentGeneration->pluck('totalInverterPower');

                    $legendArray[] = "Total PV Power";

                    $historyObject = collect([

                        "name" => "Total PV Power",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                } else if ($current == "wifi_signal_strength") {
                    
                    $todayLogData = $currentGeneration->pluck('wifi_strength');

                    $legendArray[] = "Wifi Signal Strength";

                    $historyObject = collect([

                        "name" => "Wifi Signal Strength",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" =>  count($historyArray) == 1 ? 0 : (in_array('irradiance', $historyArray) ? 0 : 1),
                    ]);
                } else if ($current == "total_generated_energy") {

                    $todayLogData = $currentGeneration->pluck('inverterEnergy');

                    $legendArray[] = "Total Generated Energy";

                    $historyObject = collect([

                        "name" => "Total Generated Energy",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => 0,
                    ]);
                }else if ($current == "l1_grid_frequency") {

                    $todayLogData = $currentGeneration->pluck('grid_frequency_l1');

                    $legendArray[] = "L1 Grid Frequency";

                    $historyObject = collect([

                        "name" => "L1 Grid Frequency",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => count($historyArray) == 1 ? 0 : 1,
                    ]);
                }else if ($current == "l2_grid_frequency") {

                    $todayLogData = $currentGeneration->pluck('grid_frequency_l2');

                    $legendArray[] = "L2 Grid Frequency";

                    $historyObject = collect([

                        "name" => "L2 Grid Frequency",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => count($historyArray) == 1 ? 0 : 1,
                    ]);
                }else if ($current == "l3_grid_frequency") {

                    $todayLogData = $currentGeneration->pluck('grid_frequency_l3');

                    $legendArray[] = "L3 Grid Frequency";

                    $historyObject = collect([

                        "name" => "L3 Grid Frequency",
                        "type" => $graphType,
                        "smooth" => true,
                        "data" => $todayLogData,
                        "yAxisIndex" => count($historyArray) == 1 ? 0 : 1,
                    ]);
                }

            $plantHistoryGraph[] = $historyObject;
        }

        $data['plant_history_graph'] = $plantHistoryGraph;
        $data['time_details'] = [];

        if (!empty($todayLogTime)) {

            $startTime = new \DateTime(date('Y-m-d ' . end($todayLogTime), strtotime($date)));
        } else {

            $startTime = new \DateTime(date('Y-m-d 00:00', strtotime($date)));
        }

        $endTime = new \DateTime(date('Y-m-d 23:55', strtotime($date)));
        $timeStep = 5;
        $collectTimeData = $currentGeneration->pluck('collect_time');

        foreach($collectTimeData as $collectTime){

            $todayLogTime[] = Date('H:i',strtotime($collectTime));
        }

        $data['time_array'] = $todayLogTime;
        $data['legend_array'] = $legendArray;
        $data['tooltip_date'] = $tooltipDate;
        $data['y_axis_array'] = $plantHistoryGraphYAxis;

        return $data;
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
