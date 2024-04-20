<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Setting;
use App\Http\Models\CronJobTime;
use Spatie\Permission\Models\Role;

class SettingController extends Controller
{

    public function cronJobDetal()
    {
        if (Auth::user()->roles != '1') {
            return redirect('/home');
        }
        $CronJobDetail = CronJobTime::orderBy('created_at',"Desc")->paginate('20');
        return view('admin.cron_job_detail', ["CronJobDetail" => $CronJobDetail]);
    }
    public function allSetting(Request $request)
    {
        if(Auth::user()->roles != '1'){
            return redirect('/home');
        }
        $setting = Setting::first();
        return view('admin.setting',['setting' => $setting]);
    }

    public function updateSetting(Request $request)
    {
        if(Auth::user()->roles != '1'){
            return redirect('/home');
        }

        $input = $request->all();
        $setting = Setting::find($input['id']);
        // dd($input,$setting);
        $response =  $setting->fill($input)->save();

        if($response){
            Session::flash('message', 'Congratulation! Updated settings.');
            Session::flash('alert-class', 'alert-success');
            return redirect()->back();
        }else{
            Session::flash('message', 'Sorry! Settings not updated');
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();
        }
    }

}
