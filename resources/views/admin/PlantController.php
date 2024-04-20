<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Plant;
use App\Http\Models\PlantDetail;
use App\Http\Models\DailyProcessedPlantDetail;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\DailyInverterDetail;
use App\Http\Models\MonthlyInverterDetail;
use App\Http\Models\YearlyInverterDetail;
use App\Http\Models\InverterDetail;
use App\Http\Models\GenerationLog;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\Company;
use App\Http\Models\PlantUser;
use App\Http\Models\Notification;
use App\Http\Models\SystemType;
use App\Http\Models\PlantType;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\Weather;
use Spatie\Permission\Models\Role;
use \GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlantController extends Controller
{

    public function __construct()
    {
        date_default_timezone_set("Asia/Karachi");
        Session::put(['plant_name'=> '']);

    }

    public function allPlants(Request $request)    {

        $this->get_weather();
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        if($page == 'refresh'){
            app('App\Http\Controllers\Api\PlantController')->plant_site_data();
        }
        $input = $request->all();
        Session::put(['filter'=> $input]);

        $plant_type   = $request->plant_type == "all" ? '' : $request->plant_type;
        $system_type  = $request->system_type == "all" ? '' : $request->system_type ;
        $capacity     = $request->capacity == "all" ? '' : $request->capacity;
        $province     = $request->province == "all" ? '' :$request->province;
        $city         = $request->city == "all" ? '' : $request->city;
        $plants_input = $request->plants == "all" ? '' : $request->plants;
        // dd($plants_input, Session::get('filter'));

        $where = ''; $where_array = array();
        if($system_type){
            $where .= "plants.system_type = '$system_type'";
            $where_array['plants.system_type'] = $system_type;
        }
        if($plant_type){
            $where .= $where ? " AND " : '';
            $where .= "plants.plant_type = '$plant_type'";
            $where_array['plants.plant_type'] = $plant_type;
        }
        if($capacity){
            $where .= $where ? " AND " : '';
            $where .= "plants.capacity = $capacity";
            $where_array['plants.capacity'] = $capacity;
        }
        if($province){
            $where .= $where ? " AND " : '';
            $where .= "plants.province = '$province'";
            $where_array['plants.province'] = $province;
        }
        if($city){
            $where .= $where ? " AND " : '';
            $where .= "plants.city = '$city'";
            $where_array['plants.city'] = $city;
        }
        
        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }

        if($plants_input){
            $plants = Plant::where($where_array)->whereIn('id',$plants_input)->get();
            
            $plants = $plants->map(function ($plant) {
                $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
                $plant['system_type'] = SystemType::find($plant->system_type)->type;
                return $plant;
            });

            $filter_data['capacity_array'] = Plant::select('capacity')->where($where_array)->whereIn('id',$plants_input)->groupBy('capacity')->get();
            $filter_data['province_array'] = Plant::select('province')->where($where_array)->whereIn('id',$plants_input)->groupBy('province')->get();
            $filter_data['city_array'] = Plant::select('city')->where($where_array)->whereIn('id',$plants_input)->groupBy('city')->get();

            if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
                $system_types = array();
                $plant_types = array();
                foreach ($plants as $plant){
                    $system_types[] = $plant->system_type;
                    $plant_types[] = $plant->plant_type;

                }
                $filter_data['system_type'] = SystemType::whereIn('id',$system_types)->get();
                $filter_data['plant_type'] = PlantType::whereIn('id',$plant_types)->get();
                $filter_data['plants'] = Plant::where($where_array)->get();
            }else{
                $filter_data['system_type'] = SystemType::all();
                $filter_data['plant_type'] = PlantType::all();
                $filter_data['plants'] = Plant::all();
            }

            $online = Plant::where($where_array)->where('is_online','Y')->whereIn('id',$plants_input)->count();
            $offline = Plant::where($where_array)->where('is_online','N')->whereIn('id',$plants_input)->count();
            $alarmLevel = Plant::where($where_array)->where('alarmLevel','!=','0')->whereIn('id',$plants_input)->count();
            // dd($online,$offline,$alarmLevel);

            $on_grid = Plant::where($where_array)->whereIn('system_type',[1,2])->whereIn('id',$plants_input)->count();
            $off_grid = Plant::where($where_array)->where('system_type',3)->whereIn('id',$plants_input)->count();
            $hybrid = Plant::where($where_array)->whereIn('system_type', [4,5])->whereIn('id',$plants_input)->count();

            $capacity_0_10 = Plant::where($where_array)->whereBetween('capacity', [0, 10])->whereIn('id',$plants_input)->count();
            $capacity_10_20 = Plant::where($where_array)->whereBetween('capacity', [11, 20])->whereIn('id',$plants_input)->count();
            $capacity_20_30 = Plant::where($where_array)->whereBetween('capacity', [21, 30])->whereIn('id',$plants_input)->count();
            $capacity_30_40 = Plant::where($where_array)->whereBetween('capacity', [31,40])->whereIn('id',$plants_input)->count();
            $capacity_40_50 = Plant::where($where_array)->whereBetween('capacity', [41, 50])->whereIn('id',$plants_input)->count();
        }else{
            $plants = Plant::where($where_array)->get();
            $plants = $plants->map(function ($plant) {
                $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
                $plant['system_type'] = SystemType::find($plant->system_type)->type;
                return $plant;
            });

            $filter_data['capacity_array'] = Plant::select('capacity')->where($where_array)->groupBy('capacity')->get();
            $filter_data['province_array'] = Plant::select('province')->where($where_array)->groupBy('province')->get();
            $filter_data['city_array'] = Plant::select('city')->where($where_array)->groupBy('city')->get();

            if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
                $system_types = array();
                $plant_types = array();
                foreach ($plants as $plant){
                    $system_types[] = $plant->system_type;
                    $plant_types[] = $plant->plant_type;
                }
                $filter_data['system_type'] = SystemType::whereIn('id',$system_types)->get();
                $filter_data['plant_type'] = PlantType::whereIn('id',$plant_types)->get();
                $filter_data['plants'] = Plant::where($where_array)->get();
                
            }else{
                $filter_data['system_type'] = SystemType::all();
                $filter_data['plant_type'] = PlantType::all();
                $filter_data['plants'] = Plant::all();
            }

            $online = Plant::where($where_array)->where('is_online','Y')->count();
            $offline = Plant::where($where_array)->where('is_online','N')->count();
            $alarmLevel = Plant::where($where_array)->where('alarmLevel','!=','0')->count();

            $on_grid = Plant::where($where_array)->whereIn('system_type',[1,2])->count();
            $off_grid = Plant::where($where_array)->where('system_type',3)->count();
            $hybrid = Plant::where($where_array)->whereIn('system_type', [4,5])->count();

            $capacity_0_10 = Plant::where($where_array)->whereBetween('capacity', [0, 10])->count();
            $capacity_10_20 = Plant::where($where_array)->whereBetween('capacity', [11, 20])->count();
            $capacity_20_30 = Plant::where($where_array)->whereBetween('capacity', [21, 30])->count();
            $capacity_30_40 = Plant::where($where_array)->whereBetween('capacity', [31,40])->count();
            $capacity_40_50 = Plant::where($where_array)->whereBetween('capacity', [41, 50])->count();
        }
        
        $plant_ids = array();
        foreach ($plants as $plant) {
            array_push($plant_ids, $plant->id);
        }
        // dd($plant_ids);
        $today_log_data=[];$today_log_time=[];
        $current_generation = GenerationLog::select('created_at')->whereIn('plant_id',$plant_ids)->whereBetween('created_at', [date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->groupBy('created_at')->get();
        foreach ($current_generation as $key => $today_log) {
            $today_log_time[] = date('H:i',strtotime($today_log->created_at));
            $today_log_data_sum = GenerationLog::whereIn('plant_id',$plant_ids)->where('created_at',$today_log->created_at)->sum('totalEnergy');
            $today_log_data[] = $key > 0 && $today_log_data_sum <= 0 ? $today_log_data[$key-1] : $today_log_data_sum;
        }

        $yesterday_log_time=[];$yesterday_log_data=[];
        $yesterday_generation = GenerationLog::select('created_at')->whereIn('plant_id',$plant_ids)->whereBetween('created_at', [date('Y-m-d 00:00:00',strtotime("-1 Days")),date('Y-m-d 23:59:00',strtotime("-1 Days"))])->groupBy('created_at')->get();
        foreach ($yesterday_generation as $key => $yesterday_log) {
            $yesterday_log_time[] = date('H:i',strtotime($yesterday_log->created_at));
            $yesterday_log_data_sum = GenerationLog::whereIn('plant_id',$plant_ids)->where('created_at',$yesterday_log->created_at)->sum('totalEnergy');
            $yesterday_log_data[] = $key > 0 && $yesterday_log_data_sum <= 0 ? $yesterday_log_data[$key-1] : $yesterday_log_data_sum;
        }

        $generation_log['today_energy_generation'] = isset($today_log_data) && !empty($today_log_data) ? max($today_log_data) : '';
        $generation_log['today_generation'] = isset($today_log_data) && !empty($today_log_data) ? implode(',',$today_log_data) : '';
        $generation_log['today_time'] = isset($today_log_time) && !empty($today_log_time) ? implode(',',$today_log_time) : '';
        $generation_log['yesterday_energy_generation'] = isset($yesterday_log_data) && !empty($yesterday_log_data) ? max($yesterday_log_data) : '';
        $generation_log['yesterday_generation'] = isset($yesterday_log_data) && !empty($yesterday_log_data) ? implode(',',$yesterday_log_data) : '';
        $generation_log['yesterday_time'] = isset($yesterday_log_time) && !empty($yesterday_log_time) ? implode(',',$yesterday_log_time) : '';

        // -------
//        $today_log_data=[];$today_log_time=[];
//        $testcurrent_generation = GenerationLog::select('created_at')->whereIn('plant_id',$plant_ids)->whereBetween('created_at', [date('Y-m-d 00:00:00',strtotime("-1 Days")),date('Y-m-d 23:59:00',strtotime("-1 Days"))])->groupBy('created_at')->get();
//        foreach ($testcurrent_generation as $key => $today_log) {
//            $today_log_time[] = date('H:i',strtotime($today_log->created_at));
//            $today_log_data_sum = GenerationLog::whereIn('plant_id',$plant_ids)->where('created_at',$today_log->created_at)->sum('totalEnergy');
//            $today_log_data[] = $key > 0 && $today_log_data_sum <= 0 ? $today_log_data[$key-1] : $today_log_data_sum;
//            if($key > 0 && $today_log_data_sum <= 0){
//                $test_today_log_data[] =$today_log_data[$key-1];
//            }else{
//                $test_day_log_data[] =$today_log_data_sum;
//            }
//        }
//        $yesterday_log_data=[];
//        $testyesterday_generation = GenerationLog::select('created_at')->whereIn('plant_id',$plant_ids)->whereBetween('created_at', [date('Y-m-d 00:00:00',strtotime("-2 Days")),date('Y-m-d 23:59:00',strtotime("-2 Days"))])->groupBy('created_at')->get();
//        foreach ($testyesterday_generation as $key => $yesterday_log) {
//            $yesterday_log_time[] = date('H:i',strtotime($yesterday_log->created_at));
//            $yesterday_log_data_sum = GenerationLog::whereIn('plant_id',$plant_ids)->where('created_at',$yesterday_log->created_at)->sum('totalEnergy');
//            $yesterday_log_data[] = $key > 0 && $yesterday_log_data_sum <= 0 ? $yesterday_log_data[$key-1] : $yesterday_log_data_sum;
//        }
//        $generation_log['test_zero_today_generation'] = isset($test_day_log_data) && !empty($test_day_log_data) ? implode(',',$test_day_log_data) : '';
//        $generation_log['test_today_energy_generation'] = isset($today_log_data) && !empty($today_log_data) ? max($today_log_data) : '';
//        $generation_log['test_today_generation'] = isset($today_log_data) && !empty($today_log_data) ? implode(',',$today_log_data) : '';
//        $generation_log['test_today_time'] = isset($today_log_time) && !empty($today_log_time) ? implode(',',$today_log_time) : '';
//        $generation_log['test_yesterday_energy_generation'] = isset($yesterday_log_data) && !empty($yesterday_log_data) ? max($yesterday_log_data) : '';
//        $generation_log['test_yesterday_generation'] = isset($yesterday_log_data) && !empty($yesterday_log_data) ? implode(',',$yesterday_log_data) : '';
//        $generation_log['test_yesterday_time'] = isset($yesterday_log_time) && !empty($yesterday_log_time) ? implode(',',$yesterday_log_time) : '';

        //---------


        $energy_bought = YearlyProcessedPlantDetail::whereIn('plant_id',$plant_ids)->whereBetween('created_at',[date('Y-01-01 00:00:00'),date('Y-m-d H:i:s')])->sum('yearlyBoughtEnergy');
        $energy_sell = YearlyProcessedPlantDetail::whereIn('plant_id',$plant_ids)->whereBetween('created_at',[date('Y-01-01 00:00:00'),date('Y-m-d H:i:s')])->sum('yearlySellEnergy');

        for($i = 1; $i <= 12; $i++){
            $m = $i < 10 ? '0'.$i : $i;
            $day_in_month = cal_days_in_month(CAL_GREGORIAN,date($i),date('Y'));
            $actual_sum = 0;
            $revenue_sum = 0;
            $expected_sum = 0;
            foreach ($plants as $key => $plant) {
                $daily_actual_sum = 0;
                $daily_expected_sum = 0;
                for($j = 1; $j <= $day_in_month; $j++){
                    $d = $j < 10 ? '0'.$j : $j;
                    // $monthlyGeneration  = GenerationLog::whereIn('plant_id',$plant_ids)->whereBetween('created_at', [date('Y-'.$m.'-'.$d.' 00:00:00'),date('Y-'.$m.'-'.$d.' 23:59:00')])->where('plant_id',$plant->id)->orderBy('totalEnergy','desc')->first();
                    
                    // $daily_actual_sum += $monthlyGeneration != null ? $monthlyGeneration->totalEnergy : 0;

                    $monthlyExpected = ExpectedGenerationLog::where('plant_id',$plant->id)->where('created_at','<=',date('Y-'.$m.'-'.$d.' 23:59:00'))->orderBy('created_at','desc')->first();
                    $daily_expected_sum += $monthlyExpected != null ? $monthlyExpected->daily_expected_generation : 0;
                }
                $expected_sum += $daily_expected_sum;

                
                $monthlyGeneration = MonthlyProcessedPlantDetail::where('plant_id',$plant->id)->whereMonth('created_at', '=', date($i))->first();
                
                $daily_actual_sum = $monthlyGeneration != null ? $monthlyGeneration->monthlyGeneration : 0;
                $actual_sum += $daily_actual_sum;
                $revenue_sum  += $daily_actual_sum * $plant->benchmark_price;

            }
            $actual_generation[$i] = $actual_sum;
            $expected_generation[$i] = $expected_sum;
            $revenue[$i] = $revenue_sum;

            $sdate = date('Y-'.$i.'-01 00:00:00');
            $endate = date('Y-'.$i.'-31 23:59:00');
            $fault[$i]  = FaultAlarmLog::whereIn('plant_id',$plant_ids)->whereBetween('created_at',[$sdate,$endate])->where('type','Fault')->count();
            $warning[$i]  = FaultAlarmLog::whereIn('plant_id',$plant_ids)->whereBetween('created_at',[$sdate,$endate])->where('type','Warning')->count();

            $alarm[$i]  = FaultAlarmLog::whereIn('plant_id',$plant_ids)->whereBetween('created_at',[$sdate,$endate])->where('type','Alarm')->count();
            $rtu[$i]  = FaultAlarmLog::whereIn('plant_id',$plant_ids)->whereBetween('created_at',[$sdate,$endate])->where('alarm_source','RTU')->count();
        }

        // dd($revenue, array_sum($revenue));
        // dd($actual_generation);
        $yearlyrevenue = array_sum($revenue);
        $revenue_max = $revenue ? $this->number_round_off(max($revenue)) : 0;
        $revenue = implode(',', $revenue);
        // dd($actual_generation,$yearlyrevenue,$revenue);

        $expected_max = $expected_generation ? $this->number_round_off(max($expected_generation)) : 0;
        // dd($expected_max);
        $reduction = array_sum($actual_generation) * 0.000646155;
        $planted = array_sum($actual_generation) * 0.00131;

        
        $actual_generation = implode(',',$actual_generation);
        $expected_generation = implode(',',$expected_generation);
        
        /*Start Faults and Alerts*/
        $faults_and_alerts['fault_max'] = isset($fault) && !empty($fault) ? $this->number_round_off(max($fault)) : '';
        $faults_and_alerts['fault'] = isset($fault) && !empty($fault) ? implode(',',$fault) : '';
        $faults_and_alerts['warning'] = isset($warning) && !empty($warning) ? implode(',',$warning) : '';
        $faults_and_alerts['alarm'] = isset($alarm) && !empty($alarm) ? implode(',',$alarm) : '';
        $faults_and_alerts['rtu'] = isset($rtu) && !empty($rtu) ? implode(',',$rtu) : '';
        /*End Faults and Alerts*/

        $plant_city = Plant::select('city')->groupBy('city')->get();
        $minus_3_hours = date('Y-m-d H:i:s',strtotime('-3 hours', strtotime(date('Y-m-d H:i:s'))));
        $weather = Weather::whereIn('city',$plant_city)->whereBetween('created_at',[$minus_3_hours,date('Y-m-d H:i:s')])->get();

        $dates['plant'] = Plant::select('updated_at as date')->orderBy('updated_at')->first();
        $dates['generation_log'] = GenerationLog::select('lastUpdated as date')->orderBy('id','desc')->first();
        $dates['process_plant'] = $dates['generation_log'];//DailyProcessedPlantDetail::select('lastUpdated as date')->orderBy('id','desc')->first();
        $dates['faults_and_alert'] = FaultAlarmLog::select('lastUpdated as date')->orderBy('id','desc')->first();

        return view('admin.dashboard', ['plants'=>$plants,'filter_data' => $filter_data, 'online' => $online, 'offline' => $offline, 'alarmLevel' => $alarmLevel,'faults_and_alerts' => $faults_and_alerts ,'on_grid' => $on_grid, 'off_grid' => $off_grid, 'hybrid' => $hybrid, 'capacity_0_10' => $capacity_0_10, 'capacity_10_20' => $capacity_10_20, 'capacity_20_30' => $capacity_20_30, 'capacity_30_40' => $capacity_30_40, 'capacity_40_50' => $capacity_40_50,'energy_bought' => $energy_bought, 'energy_sell' => $energy_sell,'generation_log' => $generation_log , 'revenue' => $revenue,'revenue_max' => $revenue_max,'yearlyrevenue' => $yearlyrevenue,'reduction' => $reduction, 'planted' => $planted, 'actual_generation' => $actual_generation,'expected_generation' => $expected_generation, 'expected_max'=> $expected_max ,'weather' => $weather, 'dates' => $dates]);
    }

    public function Plants(Request $request)
    {
        $input = $request->all();
        Session::put(['filter'=> $input]);

        $plant_type   = $request->plant_type == "all" ? '' : $request->plant_type;
        $system_type  = $request->system_type == "all" ? '' : $request->system_type ;
        $capacity     = $request->capacity == "all" ? '' : $request->capacity;
        $province     = $request->province == "all" ? '' :$request->province;
        $city         = $request->city == "all" ? '' : $request->city;
        $plants_input = $request->plants == "all" ? '' : $request->plants;
        // dd($input, Session::get('filter'));

        $where = ''; $where_array = array();
        if($system_type){
            $where_array['plants.system_type'] = $system_type;
        }
        if($plant_type){
            $where .= $where ? " AND " : '';
            $where_array['plants.plant_type'] = $plant_type;
        }
        if($capacity){
            $where .= $where ? " AND " : '';
            $where_array['plants.capacity'] = $capacity;
        }
        if($province){
            $where .= $where ? " AND " : '';
            $where_array['plants.province'] = $province;
        }
        if($city){
            $where .= $where ? " AND " : '';
            $where_array['plants.city'] = $city;
        }
        // if($plants_input){
        //     $where .= $where ? " AND " : '';
        //     // $where .= "plants.id = '$plants_input'";
        //     $where_array['plants.id'] = $plants_input;
        // }

        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }
        if($plants_input){
            $plants = Plant::where($where_array)->whereIn('id',$plants_input)->get();
        }else{
            $plants = Plant::where($where_array)->get();
        }
        // dd($plants_input,$plants);
        
        $plants = $plants->map(function ($plant) {
            $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
            $plant['system_type'] = SystemType::find($plant->system_type)->type;
            return $plant;
        });

        $filter_data['capacity_array'] = Plant::select('capacity')->where($where_array)->groupBy('capacity')->get();
        $filter_data['province_array'] = Plant::select('province')->where($where_array)->groupBy('province')->get();
        $filter_data['city_array'] = Plant::select('city')->where($where_array)->groupBy('city')->get();
        
        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $system_types = array();
            $plant_types = array();
            foreach ($plants as $plant){
                $system_types[] = $plant->system_type;
                $plant_types[] = $plant->plant_type;
            }
            $filter_data['system_type'] = SystemType::whereIn('id',$system_types)->get();
            $filter_data['plant_type'] = PlantType::whereIn('id',$plant_types)->get();
            $filter_data['plants'] = Plant::where($where_array)->get();

        }else{
            $filter_data['system_type'] = SystemType::all();
            $filter_data['plant_type'] = PlantType::all();
            $filter_data['plants'] = Plant::all();
        }
        return view('admin.plant.plants', ['plants'=>$plants,'filter_data' => $filter_data]);
    }

    public function buildPlant()
    {
        if(Auth::user()->roles != 1 && Auth::user()->roles != 3)
        {
            return redirect('/home'); 
        }
        $where_array = array();
        $where_com_array = array();
        
        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
            $where_com_array['id'] =  $company_id;
        }
        
        $companies = Company::where($where_com_array)->get();
        $system_type = SystemType::all();
        $plant_type = PlantType::all();
        $plants = Plant::select('siteId')->where($where_array)->get();
        $plant_site_exist = array();
        foreach ($plants as $key => $plant) {
            array_push($plant_site_exist,$plant->siteId);
        }
        // $plant_site_exist = implode(',',$plant_site_exist);
        $plant_sites = $this->plant_site_data();
        //dd($plant_sites);
        return view('admin.plant.buildplant', ['companies'=>$companies, 'plant_sites' => $plant_sites,'plants' => $plant_site_exist,'system_types'=>$system_type,'plant_types'=>$plant_type]);
    }

    public function storePlant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plant_name' => 'required',
            'plant_type' => 'required',
            'capacity' => 'required',
            'timezone' => 'required',
            'company_id' => 'required',
            'loc_lat' => 'required',
            'loc_long' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('message', 'Sorry! Might be required fields are empty or email / username already exist.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/build-plant');
        }

        if($files=$request->file('plant_pic')){
            $plant_pic = $files->getClientOriginalName();
            $files->move(public_path('plant_photo'),$plant_pic);
        }

        $input =  $request->all();
        $input['plant_pic'] = isset($plant_pic) && !empty($plant_pic) ? $plant_pic : '';
        $input['is_online'] = $input['isOnline'] == 1 ? 'Y' : 'N';
        $input['created_at'] = Date('Y-m-d H:i:s');
        $input['updated_at'] = Date('Y-m-d H:i:s');
        // dd($input);
        $plant = Plant::create($input);
        if($plant){
            Session::flash('message', 'Plant build successfully');
            Session::flash('alert-class', 'alert-success');
            return redirect('home');
        }else{
            Session::flash('message', 'Sorry! User not added');
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/build-plant');
        }
    }

    public function plantDetail($id=0)
    {
        $where_array = array();
        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['plants.company_id'] =  $company_id;
        }
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        if($page == 'refresh'){
            app('App\Http\Controllers\Api\PlantController')->plant_site_data();
        }

        $plant = Plant::with(['inverters','daily_inverter_detail','monthly_inverter_detail'])->where('id', $id)->where($where_array)->first();
        if($plant == null)
        {
          return redirect('/home');  
        }
        // $inverters = $plant->inverters;
        // dd($plant);
        return view('admin.plant.plantdetail',['plant' => $plant]);
    }

    /*public function userPlantDetail($id=0)
    {
        $where_array = array();
        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }

        $page = isset($_GET['page']) ? $_GET['page'] : '';
        if($page == 'refresh'){
            app('App\Http\Controllers\Api\PlantController')->plant_site_data();
            $this->get_weather();
        }

        $plant = Plant::with(['inverters', 'logger','plant_details', 'inverter_details'])->where('id', $id)->where($where_array)->first();
        if($plant == null){
            return redirect('/home');
        }
        
        $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
        $plant['system_type'] = SystemType::find($plant->system_type)->type;
            
        $plant != null ? Session::put(['plant_name'=> $plant->plant_name]) : '' ;
        $current_generation = GenerationLog::select('created_at')->where('plant_id',$id)->whereBetween('created_at', [date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->groupBy('created_at')->get();
        foreach ($current_generation as $key => $today_gen) {
            $today_log_time_data[] = date('H:i',strtotime($today_gen->created_at));
            $today_log_data[] = GenerationLog::where('plant_id',$id)->where('created_at',$today_gen->created_at)->sum('current_generation');
        }

        $today_log =  isset($today_log_data) && !empty($today_log_data) ? implode(',', $today_log_data) : 0;
        $today_log_time =  isset($today_log_time_data) && !empty($today_log_time_data) ? implode(',', $today_log_time_data) : 0;

        // dd($today_log,$today_log_time);

        // dd($yesterday_generation);
        $dailyGeneration = DailyProcessedPlantDetail::select('dailyGeneration')->where('plant_id',$id)->whereBetween('created_at', [date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->first();
        $dailyGeneration = $dailyGeneration == null ? '' : $dailyGeneration->dailyGeneration;
        // dd($dailyGeneration);
        $monthlyGeneration = MonthlyProcessedPlantDetail::select('monthlyGeneration')->where('plant_id',$id)->whereBetween('created_at', [date('Y-m-01 00:00:00'),date('Y-m-d 23:59:00')])->first();
        $monthlyGeneration = $monthlyGeneration == null ? '' : $monthlyGeneration->monthlyGeneration;

        $yearlyGeneration = YearlyProcessedPlantDetail::select('yearlyGeneration')->where('plant_id',$id)->whereBetween('created_at', [date('Y-01-01 00:00:00'),date('Y-m-d H:i:s')])->first();
        $yearlyGeneration = $yearlyGeneration == null ? '' : $yearlyGeneration->yearlyGeneration;

        // dd($dailyGeneration,$monthlyGeneration, $yearlyGeneration);

        $minus_3_hours = date('Y-m-d H:i:s',strtotime('-3 hours', strtotime(date('Y-m-d H:i:s'))));
        $weather = Weather::where('city',$plant->city)->whereBetween('created_at',[$minus_3_hours,date('Y-m-d H:i:s')])->first();

        for($i = 1; $i <= 12; $i++){
            $i < 10 ? $i = '0'.$i : $i;
            $sdate = date('Y-'.$i.'-01 00:00:00');
            $endate = date('Y-'.$i.'-31 23:59:00');
            $monthlyGeneration  = MonthlyProcessedPlantDetail::whereBetween('created_at',[$sdate,$endate])->sum('monthlyGeneration');

            $generation[$i] = $monthlyGeneration ? round($monthlyGeneration,2) : 0;
        }
        $generation = $generation ? 'Generation,'.implode(',',$generation) : 0;

        // dd(date('Y-m-d H:i:s',strtotime('-6 minutes', strtotime(date('Y-m-d H:i:s')))));

        return view('admin.plant.userPlantdetail',['plant' => $plant, 'today_log' => $today_log,'today_log_time' => $today_log_time,'dailyGeneration' => $dailyGeneration,'monthlyGeneration' => $monthlyGeneration,'yearlyGeneration' => $yearlyGeneration,'weather' =>$weather,'generation' => $generation]);
    }*/

    public function userPlantDetail($id=0)
    {
        $where_array = array();
        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }

        $page = isset($_GET['page']) ? $_GET['page'] : '';
        if($page == 'refresh'){
            app('App\Http\Controllers\Api\PlantController')->plant_site_data();
            $this->get_weather();
        }

        $plant = Plant::with(['inverters', 'logger','plant_details', 'inverter_details'])->where('id', $id)->first();
        if($plant == null){
            return redirect('/home');
        }
        
        $plant['plant_type'] = PlantType::find($plant->plant_type)->type;
        $plant['system_type'] = SystemType::find($plant->system_type)->type;


        $plant != null ? Session::put(['plant_name'=> $plant->plant_name]) : '' ;
        $current_generation = GenerationLog::select('created_at')->where('plant_id',$id)->whereBetween('created_at', [date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->groupBy('created_at')->get();
        foreach ($current_generation as $key => $today_log) {
            $today_log_time[] = date('H:i',strtotime($today_log->created_at));
            $today_log_data[] = GenerationLog::where('plant_id',$id)->where('created_at',$today_log->created_at)->sum('current_generation');
        }

        $powerGeneration['today_log_time'] = isset($today_log_time) && !empty($today_log_time) ? implode(',', $today_log_time) : 0;
        $powerGeneration['today_log_data'] = isset($today_log_data) && !empty($today_log_data) ? implode(',', $today_log_data) : 0;
        
        
        $daily_data  = GenerationLog::whereBetween('created_at', [date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->where('plant_id',$id)->orderBy('created_at','desc')->get();
        $daily = $current = array();
        if(count($daily_data) > 0){            
            $current['generation'] = round($daily_data[0]->current_generation,2);        
            $current['consumption'] = round($daily_data[0]->current_consumption,2);        
            $current['grid'] = round($daily_data[0]->current_grid,2);        
            $current['date'] = $daily_data[0]->created_at;        
            
            $daily['generation'] = round($daily_data[0]->totalEnergy,2);        
            $daily['consumption'] = round($daily_data->sum('current_consumption'),2);
            $daily['grid'] = round($daily_data->sum('current_grid'),2);
            $daily['revenue'] = round($daily['generation'] * $plant->benchmark_price,2);

        }
        
        $monthly = array();
        $generation = $consumption = $grid = 0;
        for ($i=1; $i <= date('d'); $i++) { 
            $monthly_data  = GenerationLog::whereBetween('created_at', [date('Y-m-'.$i.' 00:00:00'),date('Y-m-'.$i.' 23:59:00')])->where('plant_id',$id)->orderBy('totalEnergy','desc')->get();
            if(count($monthly_data) > 0){
                // $generation += $monthly_data[0]->totalEnergy;        
                $consumption += $monthly_data->sum('current_consumption');
                $grid += $monthly_data->sum('current_grid');
            }

            $monthly_gen_data  = MonthlyProcessedPlantDetail::where('plant_id',$id)->whereMonth('created_at', '=', date($i))->first();

            if($monthly_gen_data != null){
                $generation = $monthly_gen_data->monthlyGeneration;
            }        
            
        }
        
        $monthly['generation'] = round($generation,2);        
        $monthly['consumption'] = round($consumption,2);
        $monthly['grid'] = round($grid,2);
        $monthly['revenue'] = round($monthly['generation'] * $plant->benchmark_price,2);

        $generation = $consumption = $grid = array();
        for($i = 1; $i <= 12; $i++){
            $m = $i < 10 ? '0'.$i : $i;
            $day_in_month = cal_days_in_month(CAL_GREGORIAN,date($i),date('Y'));
            $daily_generation = $daily_consumption = $daily_grid = 0;
            for($j = 1; $j <= $day_in_month; $j++){
                $d = $j < 10 ? '0'.$j : $j;
                $yearly_data = GenerationLog::whereBetween('created_at', [date('Y-'.$m.'-'.$d.' 00:00:00'),date('Y-'.$m.'-'.$d.' 23:59:00')])->where('plant_id',$id)->orderBy('totalEnergy','desc')->get();
                if(count($yearly_data) > 0){
                    // $daily_generation += $yearly_data[0]->totalEnergy;        
                    $daily_consumption += $yearly_data->sum('current_consumption');
                    $daily_grid += $yearly_data->sum('current_grid');
                }
            }

            $monthly_gen_data  = MonthlyProcessedPlantDetail::where('plant_id',$id)->whereMonth('created_at', '=', date($i))->first();

            if($monthly_gen_data != null){
                $daily_generation += $monthly_gen_data->monthlyGeneration;
            } 
            
            $generation[] = $daily_generation;
            $consumption[] = $daily_consumption;
            $grid[] = $daily_grid;            
        }
        
        $yearly['generation'] = round(array_sum($generation),2);        
        $yearly['consumption'] = round(array_sum($consumption),2);
        $yearly['grid'] = round(array_sum($grid),2);
        $yearly['revenue'] = round($yearly['generation'] * $plant->benchmark_price,2);
        
        $minus_3_hours = date('Y-m-d H:i:s',strtotime('-3 hours', strtotime(date('Y-m-d H:i:s'))));
        $weather = Weather::where('city',$plant->city)->whereBetween('created_at',[$minus_3_hours,date('Y-m-d H:i:s')])->first();


        $generation_max = $generation ? $this->number_round_off(max($generation)) : 0;
        $generation_history = implode(',',$generation);
        // dd($daily,$monthly,$yearly);

        $dailyGeneration = DailyProcessedPlantDetail::select('dailyGeneration')->where('plant_id',$id)->whereBetween('created_at', [date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->first();
        $Energy['daily_bought'] = $dailyGeneration == null ? '' : $dailyGeneration->dailyBoughtEnergy;
        $Energy['daily_sell'] = $dailyGeneration == null ? '' : $dailyGeneration->dailySellEnergy;
        
        $monthlyGeneration = MonthlyProcessedPlantDetail::select('monthlyGeneration')->where('plant_id',$id)->whereBetween('created_at', [date('Y-m-01 00:00:00'),date('Y-m-d 23:59:00')])->first();
        $Energy['monthly_bought'] = $monthlyGeneration == null ? '' : $monthlyGeneration->monthlyBoughtEnergy;
        $Energy['monthly_sell'] = $monthlyGeneration == null ? '' : $monthlyGeneration->monthlySellEnergy;

        $yearlyGeneration = YearlyProcessedPlantDetail::select('yearlyGeneration')->where('plant_id',$id)->whereBetween('created_at', [date('Y-01-01 00:00:00'),date('Y-m-d H:i:s')])->first();
        $Energy['yearly_bought'] = $yearlyGeneration == null ? '' : $yearlyGeneration->yearlyBoughtEnergy;
        $Energy['yearly_sell'] = $yearlyGeneration == null ? '' : $yearlyGeneration->yearlySellEnergy;

        return view('admin.plant.userPlantdetail',['plant' => $plant, 'powerGeneration' => $powerGeneration,'current' => $current,'daily' => $daily,'monthly' => $monthly,'yearly' => $yearly,'weather' =>$weather,'generation_history' => $generation_history,'generation_max' => $generation_max,'Energy'=> $Energy]);
    }

    public function plantprofile($id=0)
    {
        $where_array = array();
        $where_com_array = array();
        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
            $where_com_array['id'] =  $company_id;
        }
        $plant = Plant::where('id',$id)->where($where_array)->first();
        if($plant == null)
        {
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
           ->where('plant_user.is_active','Y')
           ->where('users.roles','!=' ,'1')
           ->where($where_array)
           ->get();

        // dd($users);

        return view('admin.plant.plantprofile',['plant' => $plant,'companies' => $companies,'roles' => $roles,'plants' => $plants, 'users' => $users]);
    }

    public function editPlant($id=0)
    {
        // if(Auth::user()->roles != 1 && Auth::user()->roles != 3)
        // {
        //     return redirect('/home'); 
        // }

        $where_array = array();
        $where_com_array = array();
        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
            $where_com_array['id'] =  $company_id;
        }
        $plant = Plant::find($id);
        // dd($plant);
        if($plant == null)
        {
            return redirect('/home');  
        }
        $system_type = SystemType::all();
        $plant_type = PlantType::all();
        $companies = Company::where($where_com_array)->get();
        $plants = Plant::select('siteId')->where($where_array)->get();
        $plant_site_exist = array();
        foreach ($plants as $key => $single_plant) {
            array_push($plant_site_exist,$single_plant->siteId);
        }
        $plant_sites = $this->plant_site_data();
        if(Auth::user()->roles == 1){
            $roles = Role::all();
        }else{
            $roles = Role::whereIn('id',[3,4,5,6])->get();
        }

        // dd($plant->company);
        $users = Plant::find($id)
           ->users()
           ->where('plant_user.is_active','Y')
           ->where('users.roles','!=' ,'1')
           ->where($where_array)
           ->get();
        // $plant['company_logo'] = $plant->company ? $plant->company->logo: '';
        // dd($plants);
        $all_plants = Plant::where($where_array)->get();

        return view('admin.plant.editplant', ['companies'=>$companies, 'plant_sites' => $plant_sites,'plants' => $plant_site_exist , 'plant' => $plant, 'system_types' => $system_type, 'plant_types' => $plant_type,'users' => $users,'roles' => $roles, 'all_plants' => $all_plants]);
    }

    public function updatePlant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plant_name' => 'required',
            'plant_type' => 'required',
            'capacity' => 'required',
            'timezone' => 'required',
            'company_id' => 'required',
            'loc_lat' => 'required',
            'loc_long' => 'required',
        ]);

        // dd($request->all());
        if ($validator->fails()) {
            Session::flash('message', 'Sorry! Might be required fields are empty or email / username already exist.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/edit-plant');
        }

        if($files=$request->file('plant_pic')){
            $plant_pic = $files->getClientOriginalName();
            $files->move(public_path('plant_photo'),$plant_pic);
        }

        $input =  $request->all();
        if(isset($plant_pic) && !empty($plant_pic)){
            $input['plant_pic'] =  $plant_pic;
        }
        $id = $input['plant_id'];
        $input['updated_at'] = Date('Y-m-d H:i:s');
        $input['capacity'] = (int)$input['capacity'];
        $input['benchmark_price'] = (int)$input['benchmark_price'];
        // dd($input);
        $plant = Plant::find($id);
        $response = $plant->fill($input)->save();

        $insert['plant_id'] = $plant->id;
        $insert['siteId'] = $plant->siteId;
        $insert['daily_expected_generation'] = (int)$input['expected_generation'];
        $insert['created_at'] = date('Y-m-d H:i:s');
        $plant = ExpectedGenerationLog::create($insert);
        
        if($response){
            Session::flash('message', 'Plant updated successfully');
            Session::flash('alert-class', 'alert-success');
            return redirect('admin/edit-plant/'.$id);
        }else{
            Session::flash('message', 'Sorry! User not added');
            Session::flash('alert-class', 'alert-danger');
            return redirect('admin/edit-plant/'.$id);
        }
    }

    public function act_exp_gen($duration)
    {
        $where_array = array();
        $filter = Session::get('filter');
        if($filter){
            $plant_type   = $filter['plant_type'] == "all" ? '' : $filter['plant_type'];
            $system_type  = $filter['system_type'] == "all" ? '' : $filter['system_type'];
            $capacity     = $filter['capacity'] == "all" ? '' : $filter['capacity'];
            $province     = $filter['province'] == "all" ? '' :$filter['province'];
            $city         = $filter['city'] == "all" ? '' : $filter['city'];
            $plants_input = $filter['plants'] == "all" ? '' : $filter['plants'];
            // dd($plants_input, Session::get('filter'));

            $where = ''; $where_array = array();
            if($system_type){
                $where_array['plants.system_type'] = $system_type;
            }
            if($plant_type){
                $where_array['plants.plant_type'] = $plant_type;
            }
            if($capacity){
                $where_array['plants.capacity'] = $capacity;
            }
            if($province){
                $where_array['plants.province'] = $province;
            }
            if($city){
                $where_array['plants.city'] = $city;
            }
        }

        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }

        if(isset($plants_input) && !empty($plants_input)){
            $plants= Plant::where($where_array)->whereIn('id',$plants_input)->get();
        }else{
            $plants= Plant::where($where_array)->get();
        }

        if($duration == 'Daily'){
            
            foreach ($plants as $key => $plant) {
                $exp_data = ExpectedGenerationLog::where('plant_id',$plant->id)->orderBy('created_at','desc')->first();
                $expected_data[$key] = $exp_data->daily_expected_generation;
            }

            $hourly_expected = round(array_sum($expected_data),2);
            // dd($hourly_expected);

            for ($i=0; $i < 24; $i++) {
                $hours[$i] = $i+1;
                $expected[$i] = round($hourly_expected,2);
            }
            $hour = date('H') <= 23 ? date('H') +1 : date('H');
            $hourly_actual = array();
            $lastupdate_time = 0;
            // dd($hour);
            for ($i=0; $i < $hour; $i++) {
                $i < 10 ? $h = '0'.$i : $h = $i;
                $actual_sum = 0;
                foreach ($plants as $key => $plant) {
                    $hourly_actual_current  = GenerationLog::whereBetween('created_at', [date('Y-m-d '.$h.':00:00'),date('Y-m-d '.$h.':59:00')])->where('plant_id',$plant->id)->orderBy('created_at','desc')->first();
                    $actual_sum += $hourly_actual_current != null ? $hourly_actual_current->totalEnergy : 0;
                    $lastupdate_time = $hourly_actual_current != null ? date('H:i',strtotime($hourly_actual_current->created_at)) : 0;
                }

                if($i > 0 && $actual_sum <= 0){
                    $hourly_actual[$i] = $hourly_actual[$i-1];
                }else{
                    $hourly_actual[$i] = round($actual_sum,2);
                }
            }

            // dd($lastupdate_time);

            if(isset($expected) && !empty($expected) && isset($hourly_actual) && !empty($hourly_actual)){
                if(max($expected) > max($hourly_actual)){
                    $expected_max = $this->number_round_off((max($expected)));
                }else{
                    $expected_max = $this->number_round_off((max($hourly_actual)));
                }
            }

            $data[0] = isset($hours) && !empty($hours) ? implode(',', $hours) : '';
            $data[1] = isset($hourly_actual) && !empty($hourly_actual) ? implode(',', $hourly_actual) : '';
            $data[2] = isset($expected) && !empty($expected) ? implode(',', $expected) : '';
            $data[3] = $expected_max ? $expected_max : '';
            $data[4] = $lastupdate_time;

            echo json_encode($data); exit;

        }else if($duration == 'Monthly'){

            foreach ($plants as $key => $plant) {
                $exp_data = ExpectedGenerationLog::where('plant_id',$plant->id)->orderBy('created_at','desc')->first();
                $expected_data[$key] = $exp_data->daily_expected_generation;
            }

            $hourly_expected = round(array_sum($expected_data),2);


            $day_in_month = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
            for ($i=1; $i <= $day_in_month ; $i++) {
                $i < 10 ? $d = '0'.$i : $d =$i;
                $days[$i] = $d;
                $actual_sum = 0;
                $expected_sum = 0;

                foreach ($plants as $key => $plant) {
                    $actual_gen_current = GenerationLog::whereBetween('created_at', [date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->where('plant_id',$plant->id)->orderBy('totalEnergy','desc')->first();

                    $actual_sum += $actual_gen_current != null ? $actual_gen_current->totalEnergy : 0;

                    $daily_expected_gen = ExpectedGenerationLog::where('plant_id',$plant->id)->where('created_at','<=',date('Y-m-'.$d.' 23:59:00'))->orderBy('created_at','desc')->first();
                    $expected_sum += $daily_expected_gen != null ? $daily_expected_gen->daily_expected_generation : 0;
                }

                $actual_gen[$i] = round($actual_sum,2);
                $expected_gen[$i] = round($expected_sum,2);
            }

            // dd($actual_gen,$expected_gen);
            if(isset($expected_gen) && !empty($expected_gen) && isset($actual_gen) && !empty($actual_gen)){
                if(max($expected_gen) > max($actual_gen)){
                    $expected_max = $this->number_round_off((max($expected_gen)));
                }else{
                    $expected_max = $this->number_round_off((max($actual_gen)));
                }
            }

            $data[0] = isset($days) && !empty($days) ? implode(',', $days) : '';
            $data[1] = isset($actual_gen) && !empty($actual_gen) ? 'Actual,'.implode(',', $actual_gen) : '';
            $data[2] = isset($expected_gen) && !empty($expected_gen) ? 'Expected,'.implode(',', $expected_gen) : '';
            $data[3] = $expected_max ? $expected_max : '';
            echo json_encode($data); exit;

        } else if($duration == 'Yearly'){
            for($i = 1; $i <= 12; $i++){
                $m = $i < 10 ? '0'.$i : $i;
                $day_in_month = cal_days_in_month(CAL_GREGORIAN,date($i),date('Y'));
                $actual_sum = 0;
                $expected_sum = 0;
                foreach ($plants as $key => $plant) {
                    $daily_actual_sum = 0;
                    $daily_expected_sum = 0;
                    for($j = 1; $j <= $day_in_month; $j++){
                        $d = $j < 10 ? '0'.$j : $j;
                        // $monthlyGeneration  = GenerationLog::whereBetween('created_at', [date('Y-'.$m.'-'.$d.' 00:00:00'),date('Y-'.$m.'-'.$d.' 23:59:00')])->where('plant_id',$plant->id)->orderBy('totalEnergy','desc')->first();
                        // $daily_actual_sum += $monthlyGeneration != null ? $monthlyGeneration->totalEnergy : 0;

                        $monthlyExpected = ExpectedGenerationLog::where('plant_id',$plant->id)->where('created_at','<=',date('Y-'.$m.'-'.$d.' 23:59:00'))->orderBy('created_at','desc')->first();
                        $daily_expected_sum += $monthlyExpected != null ? $monthlyExpected->daily_expected_generation : 0;
                    }

                    $monthlyGeneration = MonthlyProcessedPlantDetail::where('plant_id',$plant->id)->whereMonth('created_at', '=', date($i))->first();
                
                    $actual_sum += $monthlyGeneration != null ? $monthlyGeneration->monthlyGeneration : 0;
                    // $actual_sum += $daily_actual_sum;
                    $expected_sum += $daily_expected_sum;

                }
                $actual_generation[$i] = $actual_sum;
                $expected_generation[$i] = $expected_sum;
            }

            // dd($actual_generation,$expected_generation);

            $expected_max = isset($expected_generation) && !empty($expected_generation) ? $this->number_round_off((max($expected_generation))) : 0;

            $data[0] = 'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec';
            $data[1] = isset($actual_generation) && !empty($actual_generation) ? 'Actual,'.implode(',', $actual_generation) : '';
            $data[2] = isset($expected_generation) && !empty($expected_generation) ? 'Expected,'.implode(',', $expected_generation) : '';
            $data[3] = $expected_max ? $expected_max : '';
            echo json_encode($data); exit;
        }
    }

    public function revenue($duration)
    {
        $where_array = array();
        $filter = Session::get('filter');
        if($filter){
            $plant_type   = $filter['plant_type'] == "all" ? '' : $filter['plant_type'];
            $system_type  = $filter['system_type'] == "all" ? '' : $filter['system_type'];
            $capacity     = $filter['capacity'] == "all" ? '' : $filter['capacity'];
            $province     = $filter['province'] == "all" ? '' :$filter['province'];
            $city         = $filter['city'] == "all" ? '' : $filter['city'];
            $plants_input = $filter['plants'] == "all" ? '' : $filter['plants'];
            // dd($plants_input, Session::get('filter'));

            $where = ''; $where_array = array();
            if($system_type){
                $where_array['plants.system_type'] = $system_type;
            }
            if($plant_type){
                $where_array['plants.plant_type'] = $plant_type;
            }
            if($capacity){
                $where_array['plants.capacity'] = $capacity;
            }
            if($province){
                $where_array['plants.province'] = $province;
            }
            if($city){
                $where_array['plants.city'] = $city;
            }
        }

        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }
        if(isset($plants_input) && !empty($plants_input)){
            $plants = Plant::where($where_array)->whereIn('id',$plants_input)->get();
        }else{
            $plants = Plant::where($where_array)->get();
        }

        if($duration == 'Daily'){
            
            for ($i=1; $i <= 24 ; $i++) { 
                $hours[$i] = $i;
            }
            $hour = date('H') <= 23 ? date('H') +1 : date('H');
            $revenue = array();
            $lastupdate_time = 0;
            for ($i=0; $i < $hour; $i++) {
                $i < 10 ? $h = '0'.$i : $h = $i;
                $actual_sum = 0;
                foreach ($plants as $key => $plant) {
                    $hourly_actual_current  = GenerationLog::whereBetween('created_at', [date('Y-m-d '.$h.':00:00'),date('Y-m-d '.$h.':59:00')])->where('plant_id',$plant->id)->orderBy('created_at','desc')->first();
                    $actual_sum += $hourly_actual_current != null ? $hourly_actual_current->totalEnergy * $plant->benchmark_price : 0;

                    $lastupdate_time = $hourly_actual_current != null ? date('H:i',strtotime($hourly_actual_current->created_at)) : 0;
                }
                if($i > 0 && $actual_sum <= 0){
                    $revenue[$i] = $revenue[$i-1];
                }else{
                    $revenue[$i] = round($actual_sum,2) ;
                }
            }

            $revenue_max = isset($revenue) && !empty($revenue) ? $this->number_round_off((max($revenue))) : 0;

            $data[0] = isset($hours) && !empty($hours) ? implode(',', $hours) : '';
            $data[1] = isset($revenue) && !empty($revenue) ? number_format(max($revenue),2) : 0;
            $data[2] = isset($revenue) && !empty($revenue) ? implode(',', $revenue) : '';
            $data[3] = $revenue_max;
            $data[4] = $lastupdate_time;
            echo json_encode($data); exit;

        }
        if($duration == 'Monthly'){
            $day_in_month = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
            $revenue = array();
            for ($i=1; $i <= $day_in_month ; $i++) {
                $i < 10 ? $d = '0'.$i : $d =$i;
                $days[$i] = $d;
                $actual_sum = 0;
                foreach ($plants as $key => $plant) {
                    $actual_gen_current = GenerationLog::whereBetween('created_at', [date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->where('plant_id',$plant->id)->orderBy('totalEnergy','desc')->first();

                    $actual_sum += $actual_gen_current != null ? $actual_gen_current->totalEnergy * $plant->benchmark_price : 0;
                }

                $revenue[$i] = round($actual_sum,2);
            }
            $revenue_max = isset($revenue) && !empty($revenue) ? $this->number_round_off((max($revenue))) : 0;
            $data[0] = isset($days) && !empty($days) ? implode(',', $days) : '';
            $data[1] = isset($revenue) && !empty($revenue) ? number_format(array_sum($revenue),2) : 0;
            $data[2] = isset($revenue) && !empty($revenue) ? 'Cost Saving,'.implode(',', $revenue) : '';
            $data[3] = $revenue_max;
            echo json_encode($data); exit;

        } else if($duration == 'Yearly'){
            $revenue = array();
            for($i = 1; $i <= 12; $i++){
                $m = $i < 10 ? '0'.$i : $i;
                $day_in_month = cal_days_in_month(CAL_GREGORIAN,date($i),date('Y'));
                $actual_sum = 0;
                $expected_sum = 0;
                foreach ($plants as $key => $plant) {
                    $daily_actual_sum = 0;
                    // for($j = 1; $j <= $day_in_month; $j++){
                    //     $d = $j < 10 ? '0'.$j : $j;
                    //     $monthlyGeneration  = GenerationLog::whereBetween('created_at', [date('Y-'.$m.'-'.$d.' 00:00:00'),date('Y-'.$m.'-'.$d.' 23:59:00')])->where('plant_id',$plant->id)->orderBy('totalEnergy','desc')->first();
                    //     $daily_actual_sum += $monthlyGeneration != null ? $monthlyGeneration->totalEnergy : 0;
                    // }

                    $monthlyGeneration = MonthlyProcessedPlantDetail::where('plant_id',$plant->id)->whereMonth('created_at', '=', date($i))->first();
                
                    $daily_actual_sum = $monthlyGeneration != null ? $monthlyGeneration->monthlyGeneration : 0;
                    $actual_sum += $daily_actual_sum * $plant->benchmark_price;

                }
                $revenue[$i] = $actual_sum;
            }
            // dd($revenue);
            $revenue_max = isset($revenue) && !empty($revenue) ? $this->number_round_off((max($revenue))) : 0;


            $data[0] = 'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec';
            $data[1] = isset($revenue) && !empty($revenue) ? number_format(array_sum($revenue),2) : 0;
            $data[2] = isset($revenue) && !empty($revenue) ? 'Cost Saving,'.implode(',', $revenue) : '';
            $data[3] = $revenue_max;
            echo json_encode($data); exit;
        }
    }

    public function tree_planting($duration)
    {
        if($duration == 'Daily'){
            $dailyGeneration = $this->generation_calculation($duration);
            $dailyGeneration = round(max($dailyGeneration) * 0.00131,2);
            echo $dailyGeneration;exit;
        }else if($duration == 'Monthly'){
            $monthlyGeneration = $this->generation_calculation($duration);
            $monthlyGeneration = round(array_sum($monthlyGeneration) * 0.00131,2);
            echo $monthlyGeneration;exit;
        }else if($duration == 'Yearly'){
            $yearlyGeneration = $this->generation_calculation($duration);
            $yearlyGeneration = round(array_sum($yearlyGeneration) * 0.00131,2);
            echo $yearlyGeneration;exit;
        }
    }

    public function emission_reduction($duration)
    {
        if($duration == 'Daily'){
            $dailyGeneration = $this->generation_calculation($duration);
            $dailyGeneration = round(max($dailyGeneration) * 0.000646155,2);
            echo $dailyGeneration;exit;
        }else if($duration == 'Monthly'){
            $monthlyGeneration = $this->generation_calculation($duration);
            $monthlyGeneration = round(array_sum($monthlyGeneration) * 0.000646155,2);
            echo $monthlyGeneration;exit;
        }else if($duration == 'Yearly'){
            $yearlyGeneration = $this->generation_calculation($duration);
            $yearlyGeneration = round(array_sum($yearlyGeneration) * 0.000646155,2);
            echo $yearlyGeneration;exit;
        }
    }

    public function energy_bought_sell($duration)
    {
        $where_array = array();
        $filter = Session::get('filter');
        if($filter){
            $plant_type   = $filter['plant_type'] == "all" ? '' : $filter['plant_type'];
            $system_type  = $filter['system_type'] == "all" ? '' : $filter['system_type'];
            $capacity     = $filter['capacity'] == "all" ? '' : $filter['capacity'];
            $province     = $filter['province'] == "all" ? '' :$filter['province'];
            $city         = $filter['city'] == "all" ? '' : $filter['city'];
            $plants_input = $filter['plants'] == "all" ? '' : $filter['plants'];
            // dd($plants_input, Session::get('filter'));

            $where = ''; $where_array = array();
            if($system_type){
                $where_array['plants.system_type'] = $system_type;
            }
            if($plant_type){
                $where_array['plants.plant_type'] = $plant_type;
            }
            if($capacity){
                $where_array['plants.capacity'] = $capacity;
            }
            if($province){
                $where_array['plants.province'] = $province;
            }
            if($city){
                $where_array['plants.city'] = $city;
            }
        }

        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }
        if(isset($plants_input) && !empty($plants_input)) {
            $plants = Plant::where($where_array)->whereIn('id',$plants_input)->get();
        }else{
            $plants = Plant::where($where_array)->get();
        }
        $plant_ids = array();
        foreach ($plants as $plant) {
            array_push($plant_ids, $plant->id);
        }
        // dd($plant_ids);

        // $energy_bought = YearlyProcessedPlantDetail::whereIn('plant_id',$plant_ids)->sum('yearlyBoughtEnergy');
        // $energy_sell = YearlyProcessedPlantDetail::whereIn('plant_id',$plant_ids)->sum('yearlySellEnergy');

        if($duration == 'Daily'){
            $dailybought = round(dailyProcessedPlantDetail::whereBetween('created_at',[date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->whereIn('plant_id',$plant_ids)->sum('dailyBoughtEnergy'),2);
            $dailysell = round(dailyProcessedPlantDetail::whereBetween('created_at',[date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->whereIn('plant_id',$plant_ids)->sum('dailySellEnergy'),2);
            echo $dailybought.'|'.$dailysell;exit;
        }else if($duration == 'Monthly'){
            $monthlybought = round(monthlyProcessedPlantDetail::whereBetween('created_at',[date('Y-m-01 00:00:00'),date('Y-m-31 23:59:00')])->whereIn('plant_id',$plant_ids)->sum('monthlyBoughtEnergy'),2);
            $monthlysell = round(monthlyProcessedPlantDetail::whereBetween('created_at',[date('Y-m-01 00:00:00'),date('Y-m-31 23:59:00')])->whereIn('plant_id',$plant_ids)->sum('monthlySellEnergy'),2);
            echo $monthlybought.'|'.$monthlysell;exit;
        }else if($duration == 'Yearly'){
            $yearlybought = round(YearlyProcessedPlantDetail::whereBetween('created_at',[date('Y-01-01 00:00:00'),date('Y-12-31 23:59:00')])->whereIn('plant_id',$plant_ids)->sum('yearlyBoughtEnergy'),2);
            $yearlysell = round(YearlyProcessedPlantDetail::whereBetween('created_at',[date('Y-m-d 00:00:00'),date('Y-m-d 23:59:00')])->whereIn('plant_id',$plant_ids)->sum('yearlySellEnergy'),2);
            echo $yearlybought.'|'.$yearlysell;exit;
        }
    }

    public function history(Request $request)
    {
        $input = $request->all();
        Session::put($request->all());
        $duration=  $input['time'];
        $parameter = $input['parameter'];
        $plant_id = $input['plant_id'];
        if($duration == 'Daily'){
            $lastupdate_time = 0;
            for ($i=0; $i <= 24; $i++) {
                $i < 10 ? $h = '0'.$i : $h = $i;
                $hours[$i] = $h;

                if($parameter == 'Generation'){
                    $hourly_val  = GenerationLog::where('plant_id',$plant_id)->whereBetween('created_at', [date('Y-m-d '.$h.':00:00'),date('Y-m-d '.$h.':59:00')])->orderBy('totalEnergy','desc')->first();
                    $hourly[$i] = $hourly_val != null ? $hourly_val->totalEnergy : 0;
                    $lastupdate_time = $hourly_val != null ? date('H:i',strtotime($hourly_val->created_at)) : 0;
                }else if($parameter == 'Consumption'){
                    $hourly[$i]  = GenerationLog::where('plant_id',$plant_id)->whereBetween('created_at', [date('Y-m-d '.$h.':00:00'),date('Y-m-d '.$h.':59:00')])->sum('current_consumption');
                }else if($parameter == 'Grid'){
                    $hourly[$i]  = GenerationLog::where('plant_id',$plant_id)->whereBetween('created_at', [date('Y-m-d '.$h.':00:00'),date('Y-m-d '.$h.':59:00')])->sum('current_grid');
                }
            }

            if(isset($hourly) && !empty($hourly)){
                $max_val = $this->number_round_off((max($hourly)));
            }
            $data[0] = isset($hours) && !empty($hours) ? implode(',', $hours) : '';
            $data[1] = isset($hourly) && !empty($hourly) ? implode(',', $hourly) : '';
            $data[2] = $max_val ? $max_val : '';
            $data[3] = $lastupdate_time;
            echo json_encode($data); exit;

        } else if($duration == 'Monthly'){
            $day_in_month = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));

            for ($i=1; $i <= $day_in_month ; $i++) {
                $i < 10 ? $d = '0'.$i : $d =$i;
                $days[$i] = $d;

                if($parameter == 'Generation'){
                    $daily[$i]  = DailyProcessedPlantDetail::where('plant_id',$plant_id)->whereBetween('created_at', [date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->sum('dailyGeneration');
                }else if($parameter == 'Consumption'){
                    $daily[$i]  = DailyProcessedPlantDetail::where('plant_id',$plant_id)->whereBetween('created_at', [date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->sum('dailyConsumption');
                }else if($parameter == 'Grid'){
                    $daily[$i]  = DailyProcessedPlantDetail::where('plant_id',$plant_id)->whereBetween('created_at', [date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->sum('dailyGridPower');
                }
            }
            $data[0] = isset($days) && !empty($days) ? implode(',', $days) : '';
            $data[1] = isset($daily) && !empty($daily) ? implode(',', $daily) : '';
            echo json_encode($data); exit;

        } else if($duration == 'Yearly'){
            for($i = 1; $i <= 12; $i++){
                $i < 10 ? $m = '0'.$i : $m =$i;
                if($parameter == 'Generation'){
                    $monthly[$i]  = MonthlyProcessedPlantDetail::where('plant_id',$plant_id)->whereBetween('created_at', [date('Y-'.$m.'-d 00:00:00'),date('Y-'.$m.'-d 23:59:00')])->sum('monthlyGeneration');
                }else if($parameter == 'Consumption'){
                    $monthly[$i]  = MonthlyProcessedPlantDetail::where('plant_id',$plant_id)->whereBetween('created_at', [date('Y-'.$m.'-d 00:00:00'),date('Y-'.$m.'-d 23:59:00')])->sum('monthlyConsumption');
                }else if($parameter == 'Grid'){
                    $monthly[$i]  = MonthlyProcessedPlantDetail::where('plant_id',$plant_id)->whereBetween('created_at', [date('Y-'.$m.'-d 00:00:00'),date('Y-'.$m.'-d 23:59:00')])->sum('monthlyGridPower');
                }
            }

            $data[0] = 'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec';
            $data[1] = isset($monthly) && !empty($monthly) ? implode(',', $monthly) : '';
            echo json_encode($data); exit;
        }
    }

    public function get_weather(){

        $cities = Plant::select("city")
        ->groupBy('city')
        ->get();
        $Weather = Weather::whereNotIn('city',[$cities])->delete();
        if(count($cities) > 0){
            foreach ($cities as $key => $city) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://api.openweathermap.org/data/2.5/forecast?q=".$city->city."&appid=dc8dd8343213903cdce7005937a7ca4d&units=metric",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                $city_weather = json_decode($response);
                // dd($city_weather);
                if($city_weather){
                    foreach ($city_weather->list as $key => $value) {
                        // dd($value);
                        $weather['city'] = $city_weather->city->name;
                        $weather['condition'] = $value->weather[0]->main;
                        $weather['temperature'] = round($value->main->temp);
                        $weather['created_at'] = $value->dt_txt;
                        $weather['updated_at'] = $value->dt_txt;
                        $weather['get_sunrise'] = $city_weather->city->sunrise;
                        $weather['get_sunset'] = $city_weather->city->sunset;
                        $weather['sunrise'] = gmdate("h:i:A",$city_weather->city->sunrise+18000);
                        $weather['sunset'] = gmdate("h:i:A",$city_weather->city->sunset+18000);
                        $weather['icon'] = $value->weather[0]->icon;
                        // dd($weather);

                        $weather_exits = Weather::where('created_at',$weather['created_at'])->where('city',$city_weather->city->name)->get();
                        if(count($weather_exits) > 0){
                            $new_weather = Weather::findOrFail($weather_exits[0]['id']);
                            $res = $new_weather->fill($weather)->save();
                        }else{
                            $res = Weather::create($weather);
                        }
                    }
                }
            }
        }
    }

    public function alert($duration)
    {
        if($duration == 'Daily'){
            for ($i=0; $i <= 24; $i++) {
                $i < 10 ? $i = '0'.$i : $i;
                $hours[$i] = $i;
                $fault[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-d '.$i.':00:00'),date('Y-m-d '.$i.':59:00')])->where('type','Fault')->count();
                $warning[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-d '.$i.':00:00'),date('Y-m-d '.$i.':59:00')])->where('type','warning')->count();
            }
            $data[0] = isset($hours) && !empty($hours) ? implode(',', $hours) : '';
            $data[1] = isset($fault) && !empty($fault) ? 'Fault,'.implode(',', $fault) : '';
            $data[2] = isset($warning) && !empty($warning) ? 'Warning,'.implode(',', $warning) : '';
            echo json_encode($data); exit;

        }
        if($duration == 'Monthly'){
            $day_in_month = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
            for ($i=1; $i <= $day_in_month ; $i++) {
                $i < 10 ? $d = '0'.$i : $d =$i;
                $days[$i] = $d;
                $fault[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->where('type','Fault')->count();
                $warning[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->where('type','warning')->count();
            }
            $data[0] = isset($days) && !empty($days) ? implode(',', $days) : '';
            $data[1] = isset($fault) && !empty($fault) ? 'Fault,'.implode(',', $fault) : '';
            $data[2] = isset($warning) && !empty($warning) ? 'Warning,'.implode(',', $warning) : '';
            echo json_encode($data); exit;

        } else if($duration == 'Yearly'){
            for($i = 1; $i <= 12; $i++){
                $i < 10 ? $i = '0'.$i : $i;
                $sdate = date('Y-'.$i.'-01 00:00:00');
                $endate = date('Y-'.$i.'-31 23:59:00');

                $fault[$i]  = FaultAlarmLog::whereBetween('created_at',[$sdate,$endate])->where('type','Fault')->count();
                $warning[$i]  = FaultAlarmLog::whereBetween('created_at',[$sdate,$endate])->where('type','warning')->count();
            }
            $data[0] = 'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec';
            $data[1] = isset($fault) && !empty($fault) ? 'Fault,'.implode(',', $fault) : '';
            $data[2] = isset($warning) && !empty($warning) ? 'Warning,'.implode(',', $warning) : '';
            echo json_encode($data); exit;
        }
    }

    public function faults_and_warning($duration)
    {
        $where_array = array();
        $filter = Session::get('filter');
        if($filter){
            $plant_type   = $filter['plant_type'] == "all" ? '' : $filter['plant_type'];
            $system_type  = $filter['system_type'] == "all" ? '' : $filter['system_type'];
            $capacity     = $filter['capacity'] == "all" ? '' : $filter['capacity'];
            $province     = $filter['province'] == "all" ? '' :$filter['province'];
            $city         = $filter['city'] == "all" ? '' : $filter['city'];
            $plants_input = $filter['plants'] == "all" ? '' : $filter['plants'];
            // dd($plants_input, Session::get('filter'));

            $where = ''; $where_array = array();
            if($system_type){
                $where_array['plants.system_type'] = $system_type;
            }
            if($plant_type){
                $where_array['plants.plant_type'] = $plant_type;
            }
            if($capacity){
                $where_array['plants.capacity'] = $capacity;
            }
            if($province){
                $where_array['plants.province'] = $province;
            }
            if($city){
                $where_array['plants.city'] = $city;
            }
        }
        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }

        if(isset($plants_input) && !empty($plants_input)){
            $plants = Plant::where($where_array)->whereIn('id',$plants_input)->get();
        }else{
            $plants = Plant::where($where_array)->get();
        }

        $plant_ids = array();
        foreach ($plants as $plant) {
            array_push($plant_ids, $plant->id);
        }

        if($duration == 'Daily'){
            for ($i=0; $i <= 24; $i++) {
                $i < 10 ? $i = '0'.$i : $i;
                $hours[$i] = $i;
                $fault[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-d '.$i.':00:00'),date('Y-m-d '.$i.':59:00')])->where('type','Fault')->whereIn('plant_id',$plant_ids)->count();
            }
            $fault_max = isset($fault) && !empty($fault) ? $this->number_round_off((max($fault))) : 0;

            $data[0] = isset($hours) && !empty($hours) ? implode(',', $hours) : '';
            $data[1] = isset($fault) && !empty($fault) ? 'Fault,'.implode(',', $fault) : '';
            $data[2] = $fault_max;
            echo json_encode($data); exit;

        }
        if($duration == 'Monthly'){
            $day_in_month = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
            for ($i=1; $i <= $day_in_month ; $i++) {
                $i < 10 ? $d = '0'.$i : $d =$i;
                $days[$i] = $d;
                $fault[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->whereIn('plant_id',$plant_ids)->where('type','Fault')->count();
            }
            $fault_max = isset($fault) && !empty($fault) ? $this->number_round_off((max($fault))) : 0;

            $data[0] = isset($days) && !empty($days) ? implode(',', $days) : '';
            $data[1] = isset($fault) && !empty($fault) ? 'Fault,'.implode(',', $fault) : '';
            $data[2] = $fault_max;

            echo json_encode($data); exit;

        } else if($duration == 'Yearly'){
            for($i = 1; $i <= 12; $i++){
                $i < 10 ? $i = '0'.$i : $i;
                $sdate = date('Y-'.$i.'-01 00:00:00');
                $endate = date('Y-'.$i.'-31 23:59:00');

                $fault[$i]  = FaultAlarmLog::whereBetween('created_at',[$sdate,$endate])->whereIn('plant_id',$plant_ids)->where('type','Fault')->count();
            }
            $fault_max = isset($fault) && !empty($fault) ? $this->number_round_off((max($fault))) : 0;

            $data[0] = 'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec';
            $data[1] = isset($fault) && !empty($fault) ? 'Fault,'.implode(',', $fault) : '';
            $data[2] = $fault_max;
            echo json_encode($data); exit;
        }
    }

    public function faults_and_warning_option($duration_option)
    {   
        $where_array = array();
        $filter = Session::get('filter');
        if($filter){
            $plant_type   = $filter['plant_type'] == "all" ? '' : $filter['plant_type'];
            $system_type  = $filter['system_type'] == "all" ? '' : $filter['system_type'];
            $capacity     = $filter['capacity'] == "all" ? '' : $filter['capacity'];
            $province     = $filter['province'] == "all" ? '' :$filter['province'];
            $city         = $filter['city'] == "all" ? '' : $filter['city'];
            $plants_input = $filter['plants'] == "all" ? '' : $filter['plants'];
            // dd($plants_input, Session::get('filter'));

            $where = ''; $where_array = array();
            if($system_type){
                $where_array['plants.system_type'] = $system_type;
            }
            if($plant_type){
                $where_array['plants.plant_type'] = $plant_type;
            }
            if($capacity){
                $where_array['plants.capacity'] = $capacity;
            }
            if($province){
                $where_array['plants.province'] = $province;
            }
            if($city){
                $where_array['plants.city'] = $city;
            }
        }

        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }
        if(isset($plants_input) && !empty($plants_input)){
            $plants = Plant::where($where_array)->whereIn('id',$plants_input)->get();
        }else{
            $plants = Plant::where($where_array)->get();
        }

        $plant_ids = array();
        foreach ($plants as $plant) {
            array_push($plant_ids, $plant->id);
        }

        $duration_option = explode('_',$duration_option);
        if($duration_option[0] == 'Daily'){
            for ($i=0; $i <= 24; $i++) {
                $i < 10 ? $i = '0'.$i : $i;
                $hours[$i] = $i;
                if($duration_option[1] == 'fault'){
                    $result[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-d '.$i.':00:00'),date('Y-m-d '.$i.':59:00')])->whereIn('plant_id',$plant_ids)->where('type','fault')->count();
                    $color = '#1A3AD8,#C8B400';
                    $string = 'Fault';
                }else if($duration_option[1] == 'alarm'){
                    $result[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-d '.$i.':00:00'),date('Y-m-d '.$i.':59:00')])->whereIn('plant_id',$plant_ids)->where('type','alarm')->count();
                    $color = '#C90ABD,#1A3AD8';
                    $string = 'Alarm';
                }else if($duration_option[1] == 'rtu'){
                    $result[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-d '.$i.':00:00'),date('Y-m-d '.$i.':59:00')])->whereIn('plant_id',$plant_ids)->where('alarm_source','RTU')->count();
                    $color = '#C8B400,#C90ABD';
                    $string = 'RTU';
                }
            }
            $result_max = isset($result) && !empty($result) ? $this->number_round_off((max($result))) : 0;

            $data[0] = isset($hours) && !empty($hours) ? implode(',', $hours) : '';
            $data[1] = isset($result) && !empty($result) ? $string.','.implode(',', $result) : '';
            $data[2] = $color ;
            $data[3] = $result_max;
            echo json_encode($data); exit;

        }
        if($duration_option[0] == 'Monthly'){
            $day_in_month = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
            for ($i=1; $i <= $day_in_month ; $i++) {
                $i < 10 ? $d = '0'.$i : $d =$i;
                $days[$i] = $i;
                if($duration_option[1] == 'fault'){
                    $result[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->whereIn('plant_id',$plant_ids)->where('type','fault')->count();
                    $color = '#1A3AD8,#C8B400';
                    $string = 'Fault';
                }else if($duration_option[1] == 'alarm'){
                    $result[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->whereIn('plant_id',$plant_ids)->where('type','alarm')->count();
                    $color = '#C90ABD,#1A3AD8';
                    $string = 'Alarm';
                }else if($duration_option[1] == 'rtu'){
                    $result[$i]  = FaultAlarmLog::whereBetween('created_at',[date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->whereIn('plant_id',$plant_ids)->where('alarm_source','RTU')->count();
                    $color = '#C8B400,#C90ABD';
                    $string = 'RTU';
                }
            }
            $result_max = isset($result) && !empty($result) ? $this->number_round_off((max($result))) : 0;
            $data[0] = isset($days) && !empty($days) ? implode(',', $days) : '';
            $data[1] = isset($result) && !empty($result) ? $string.','.implode(',', $result) : '';
            $data[2] = $color;
            $data[3] = $result_max;
            echo json_encode($data); exit;

        } else if($duration_option[0] == 'Yearly'){
            for($i = 1; $i <= 12; $i++){
                $i < 10 ? $i = '0'.$i : $i;
                $sdate = date('Y-'.$i.'-01 00:00:00');
                $endate = date('Y-'.$i.'-31 23:59:00');

                if($duration_option[1] == 'fault'){
                    $result[$i]  = FaultAlarmLog::whereBetween('created_at',[$sdate,$endate])->whereIn('plant_id',$plant_ids)->where('type','fault')->count();
                    $color = '#1A3AD8,#C8B400';
                    $string = 'Fault';
                }else if($duration_option[1] == 'alarm'){
                    $result[$i]  = FaultAlarmLog::whereBetween('created_at',[$sdate,$endate])->whereIn('plant_id',$plant_ids)->where('type','alarm')->count();
                    $color = '#C90ABD,#1A3AD8';
                    $string = 'Alarm';
                }else if($duration_option[1] == 'rtu'){
                    $result[$i]  = FaultAlarmLog::whereBetween('created_at',[$sdate,$endate])->whereIn('plant_id',$plant_ids)->where('alarm_source','RTU')->count();
                    $color = '#C8B400,#C90ABD';
                    $string = 'RTU';
                }
            }
            $result_max = isset($result) && !empty($result) ? $this->number_round_off((max($result))) : 0;
            $data[0] = 'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec';
            $data[1] = isset($result) && !empty($result) ? $string.','.implode(',', $result) : '';
            $data[2] = $color;
            $data[3] = $result_max;
            echo json_encode($data); exit;
        }
    }

    public function plant_site_data()
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
        // echo '<pre>';print_r($token);exit;

        if(isset($token) && !empty($token)){

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

    public function get_city($province){

        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }
        $cities = Plant::select('city')->where('province',$province)->where($where_array)->groupBy('city')->get();
        if(count($cities) > 0){
            $city_opt = '<option value="all">City</option>';
            foreach ($cities as $key => $city) {
                $city_opt .= '<option value="'.$city->city.'">'.$city->city.'</option>';
            }
        }
        echo $city_opt;exit;

    }

    private function number_round_off($num){
        // dd($num);
        if($num == 0){
            return 0;
        }else if($num < 5){
            $new_num = [0,1,2,3,4];
            return implode(',',$new_num);
        }else if($num < 10){
            $new_num = [0,2,4,6,8,10];
            return implode(',',$new_num);
        }else if($num < 499){
            $new_num = round(($num/4) / 10) * 10;
        }else if($num < 999){
            $new_num = round(($num/4) / 100) * 100;
        }else if($num < 9999){
            $new_num = round(($num/4) / 100) * 100;
        }else if($num < 39999){
            $new_num = round(($num/4) / 1000) * 1000;
        }else if($num < 99999){
            $new_num = round(($num/4) / 10000) * 10000;
        }else {
            $new_num = round(($num/4) / 100000) * 100000;
        }
        // dd($new_num);
        for($i=1; $i <= 5; $i++) {
            $i == 1 ? $array[$i] = 0 : $array[$i] = $array[$i-1] + $new_num;
        }
        // dd($array);
        return implode(',',$array);
    }
    
    private function generation_calculation($duration){
        $where_array = array();
        $filter = Session::get('filter');
        if($filter){
            $plant_type   = $filter['plant_type'] == "all" ? '' : $filter['plant_type'];
            $system_type  = $filter['system_type'] == "all" ? '' : $filter['system_type'];
            $capacity     = $filter['capacity'] == "all" ? '' : $filter['capacity'];
            $province     = $filter['province'] == "all" ? '' :$filter['province'];
            $city         = $filter['city'] == "all" ? '' : $filter['city'];
            $plants_input = $filter['plants'] == "all" ? '' : $filter['plants'];
            $where = ''; $where_array = array();
            if($system_type){
                $where_array['plants.system_type'] = $system_type;
            }
            if($plant_type){
                $where_array['plants.plant_type'] = $plant_type;
            }
            if($capacity){
                $where_array['plants.capacity'] = $capacity;
            }
            if($province){
                $where_array['plants.province'] = $province;
            }
            if($city){
                $where_array['plants.city'] = $city;
            }
        }

        if(Auth::user()->roles != 1 && Auth::user()->roles != 2) {
            $company_id = Auth::user()->company_id;
            $where_array['company_id'] =  $company_id;
        }
        if(isset($plants_input) && !empty($plants_input)){
            $plants = Plant::where($where_array)->whereIn('id',$plants_input)->get();
        }else{
            $plants = Plant::where($where_array)->get();
        }

        if($duration == 'Daily'){
            $hour = date('H') <= 23 ? date('H') +1 : date('H');
            $hourly_actual = array();
            for ($i=0; $i < $hour; $i++) {
                $i < 10 ? $h = '0'.$i : $h = $i;
                $actual_sum = 0;
                foreach ($plants as $key => $plant) {
                    $hourly_actual_current  = GenerationLog::whereBetween('created_at', [date('Y-m-d '.$h.':00:00'),date('Y-m-d '.$h.':59:00')])->where('plant_id',$plant->id)->orderBy('created_at','desc')->first();
                    $actual_sum += $hourly_actual_current != null ? $hourly_actual_current->totalEnergy : 0;
                }

                if($i > 0 && $actual_sum <= 0){
                    $hourly_actual[$i] = $hourly_actual[$i-1];
                }else{
                    $hourly_actual[$i] = round($actual_sum,2);
                }
            }
            return $hourly_actual;

        }else if($duration == 'Monthly'){

            $day_in_month = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
            $actual_gen = array();
            for ($i=1; $i <= $day_in_month ; $i++) {
                $i < 10 ? $d = '0'.$i : $d =$i;
                $days[$i] = $d;
                $actual_sum = 0;
                foreach ($plants as $key => $plant) {
                    $actual_gen_current = GenerationLog::whereBetween('created_at', [date('Y-m-'.$d.' 00:00:00'),date('Y-m-'.$d.' 23:59:00')])->where('plant_id',$plant->id)->orderBy('totalEnergy','desc')->first();

                    $actual_sum += $actual_gen_current != null ? $actual_gen_current->totalEnergy : 0;
                }

                $actual_gen[$i] = round($actual_sum,2);
            }
            return $actual_gen;

        } else if($duration == 'Yearly'){
            $actual_generation = array();
            for($i = 1; $i <= 12; $i++){
                $m = $i < 10 ? '0'.$i : $i;
                $day_in_month = cal_days_in_month(CAL_GREGORIAN,date($i),date('Y'));
                $actual_sum = 0;
                foreach ($plants as $key => $plant) {
                    $daily_actual_sum = 0;
                    // for($j = 1; $j <= $day_in_month; $j++){
                    //     $d = $j < 10 ? '0'.$j : $j;
                    //     $monthlyGeneration  = GenerationLog::whereBetween('created_at', [date('Y-'.$m.'-'.$d.' 00:00:00'),date('Y-'.$m.'-'.$d.' 23:59:00')])->where('plant_id',$plant->id)->orderBy('totalEnergy','desc')->first();
                    //     $daily_actual_sum += $monthlyGeneration != null ? $monthlyGeneration->totalEnergy : 0;
                    // }

                    $monthlyGeneration = MonthlyProcessedPlantDetail::where('plant_id',$plant->id)->whereMonth('created_at', '=', date($i))->first();
                
                    $actual_sum += $monthlyGeneration != null ? $monthlyGeneration->monthlyGeneration : 0;
                }

                $actual_generation[$i] = $actual_sum;
            }

           return $actual_generation;
        }
    }
}
