<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\User;
use App\Http\Models\Company;
use App\Http\Models\Plant;
use App\Http\Models\PlantSite;
use App\Http\Models\FaultAlarmLog;
use App\Http\Models\FaultAndAlarm;
use App\Http\Models\PlantUser;
use App\Http\Models\Inverter;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use View;


class AlertCenter extends Controller
{
    public function allalerts(Request $request)
    {
        $where_array = array();

        Session::put(['is_filter'=> 0]);
        Session::put(['alert_filter'=> '']);
        Session::put(['date'=> '']);

        $types = FaultAndAlarm::select('type')->groupBy('type')->get();
        $importances = FaultAndAlarm::select('severity')->groupBy('severity')->get();
        $alarm_codes = FaultAndAlarm::select('alarm_code')->groupBy('alarm_code')->get();

        if(Auth::user()->roles == 1 || Auth::user()->roles == 2) {

            $plant_arr = Plant::pluck('id')->toArray();

            $plants = Plant::whereIn('id', $plant_arr)->get(['id', 'plant_name']);
            $site_ids = PlantSite::whereIn('plant_id', $plant_arr)->get();

        }
        else if(Auth::user()->roles == 3 || Auth::user()->roles == 4 || Auth::user()->roles == 5 || Auth::user()->roles == 6) {

            if(Auth::user()->roles == 5 || Auth::user()->roles == 6) {
                $plant_arr = PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id')->toArray();

                $plants = Plant::whereIn('id', $plant_arr)->get(['id', 'plant_name']);
                $site_ids = PlantSite::whereIn('plant_id', $plant_arr)->get();
            }
            else if(Auth::user()->roles == 3 || Auth::user()->roles == 4) {
                $plant_arr = Plant::where('company_id', Auth::user()->company_id)->pluck('id')->toArray();

                $plants = Plant::whereIn('id', $plant_arr)->get(['id', 'plant_name']);
                $site_ids = PlantSite::whereIn('plant_id', $plant_arr)->get();
            }

        }

        if(!empty($request->types) || $request->types != null || !empty($request->severity) || $request->severity != null || !empty($request->alarm_code) || $request->alarm_code != null || !empty($request->plant_id) || $request->plant_id != null || !empty($request->site_id) || $request->site_id != null) {

            if(!empty($request->types) || $request->types != null){

                if(is_array($request->types)) {

                    $where_array['fault_and_alarms.type'] = $request->types;
                }
                else {

                    $where_array['fault_and_alarms.type'] = explode(',', $request->types);
                    $request->merge(['types' => explode(',', $request->types)]);
                }
            }
            if(!empty($request->severity) || $request->severity != null){

                if(is_array($request->severity)) {

                    $where_array['fault_and_alarms.severity'] = $request->severity;
                }

                else {

                    $where_array['fault_and_alarms.severity'] = explode(',', $request->severity);
                    $request->merge(['severity' => explode(',', $request->severity)]);
                }
            }
            if(!empty($request->alarm_code) || $request->alarm_code != null){

                if(is_array($request->alarm_code)) {

                    $where_array['fault_and_alarms.alarm_code'] = $request->alarm_code;
                }

                else {

                    $where_array['fault_and_alarms.alarm_code'] = explode(',', $request->alarm_code);
                    $request->merge(['alarm_code' => explode(',', $request->alarm_code)]);
                }


            }
            if(!empty($request->plant_id) || $request->plant_id != null){

                if(is_array($request->plant_id)) {

                    $where_array['fault_alarm_log.plant_id'] = $request->plant_id;
                }

                else {

                    $where_array['fault_alarm_log.plant_id'] = explode(',', $request->plant_id);
                    $request->merge(['plant_id' => explode(',', $request->plant_id)]);
                }

            }
            if(!empty($request->site_id) || $request->site_id != null){

                if(is_array($request->site_id)) {

                    $where_array['fault_alarm_log.siteId'] = $request->site_id;
                }

                else {

                    $where_array['fault_alarm_log.siteId'] = explode(',', $request->site_id);
                    $request->merge(['site_id' => explode(',', $request->site_id)]);
                }
            }
        }

        Session::put(['alert_filter'=> $request->all()]);
        Session::put(['is_filter'=> 1]);
        Session::put(['where_array'=> '']);

        if(!empty($where_array)) {

            if(!empty($request->plant_id) || $request->plant_id != null) {

                $ch_arr = array_intersect($plant_arr, $where_array['fault_alarm_log.plant_id']);
                $where_array['fault_alarm_log.plant_id'] = $ch_arr;


                $faults = DB::table('fault_and_alarms')
                        ->join('fault_alarm_log', 'fault_and_alarms.id','fault_alarm_log.fault_and_alarm_id')
                        ->join('plants', 'fault_alarm_log.plant_id','plants.id')
                        ->select('fault_alarm_log.*', 'fault_and_alarms.type', 'fault_and_alarms.severity', 'fault_and_alarms.alarm_code', 'fault_and_alarms.description', 'fault_and_alarms.correction_action',
                                'plants.plant_name', 'plants.id as plant_id');

                foreach ($where_array as $key => $arr) {
                    $faults->whereIn($key, $arr);
                }

                $faults = $faults->orderBy('fault_alarm_log.created_at', 'DESC')
                                    ->orderBy('fault_alarm_log.updated_at', 'ASC')
                                ->paginate(10);
            }

            else {

                $faults = DB::table('fault_and_alarms')
                        ->join('fault_alarm_log', 'fault_and_alarms.id','fault_alarm_log.fault_and_alarm_id')
                        ->join('plants', 'fault_alarm_log.plant_id','plants.id')
                        ->select('fault_alarm_log.*', 'fault_and_alarms.type', 'fault_and_alarms.severity', 'fault_and_alarms.alarm_code', 'fault_and_alarms.description', 'fault_and_alarms.correction_action',
                                'plants.plant_name', 'plants.id as plant_id');

                foreach ($where_array as $key => $arr) {
                    $faults->whereIn($key, $arr);
                }

                $faults = $faults->whereIn('fault_alarm_log.plant_id', $plant_arr)
                                ->orderBy('fault_alarm_log.created_at', 'DESC')
                                ->orderBy('fault_alarm_log.updated_at', 'ASC')
                                ->paginate(10);
            }
        }

        else {

            $faults = DB::table('fault_and_alarms')
                    ->join('fault_alarm_log', 'fault_and_alarms.id','fault_alarm_log.fault_and_alarm_id')
                    ->join('plants', 'fault_alarm_log.plant_id','plants.id')
                    ->select('fault_alarm_log.*', 'fault_and_alarms.type', 'fault_and_alarms.severity', 'fault_and_alarms.alarm_code', 'fault_and_alarms.description', 'fault_and_alarms.correction_action',
                            'plants.plant_name', 'plants.id as plant_id')
                    ->whereIn('fault_alarm_log.plant_id', $plant_arr)
                    ->orderBy('fault_alarm_log.created_at', 'DESC')
                    ->orderBy('fault_alarm_log.updated_at', 'ASC')
                    ->paginate(10);
        }

        if(empty($faults)) {

            return redirect()->back()->with('error', 'No Alerts to show!');
        }

        if ($request->from == 'pagination') {

            return view('admin.alert_center_faults', array('faults' => $faults))->render();
        }

        return view('admin.alert_center',compact('faults','types','importances','alarm_codes','plants','site_ids'));
    }

    function fetch_data(Request $request)
    {
        if($request->ajax())
        {
            /*$faults = DB::table('fault_and_alarms')
                        ->join('fault_alarm_log', 'fault_and_alarms.id','fault_alarm_log.fault_and_alarm_id')
                        ->join('plants', 'fault_alarm_log.plant_id','plants.id')
                        ->select('fault_alarm_log.*', 'fault_and_alarms.description', 'fault_and_alarms.description', 'fault_and_alarms.correction_action',
                                'plants.plant_name', 'plants.id as plant_id')
                        //->whereIn('fault_alarm_log.siteId', $plants_sites)
                        //->where('fault_alarm_log.created_at', 'LIKE', $date.'-'.$i.'%')
                        //->where('fault_and_alarms.type', 'Fault')
                        ->paginate(10);
            return view('admin.alert_center_faults', compact('faults'))->render();*/
            return "Hello";
        }
    }

    public function getAlertFilters() {

        $types = FaultAndAlarm::select('type')->groupBy('type')->get();
        $importances = FaultAndAlarm::select('severity')->groupBy('severity')->get();
        $alarm_codes = FaultAndAlarm::select('alarm_code')->groupBy('alarm_code')->get();
        $plants = Plant::all();
        $site_ids = PlantSite::select('site_id')->groupBy('site_id')->get();
        $date = FaultAlarmLog::select('created_at')->groupBy('created_at')->get();

    }


}
