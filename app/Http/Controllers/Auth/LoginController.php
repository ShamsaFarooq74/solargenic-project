<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Models\Plant;
use App\Http\Models\PlantUser;
use App\Http\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
//    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        Session::put('backUrl', URL::previous());
    }

    protected function authenticated(Request $request, $user)
    {

//        if(Session::get('backUrl')) {
//
//            return Redirect::to(Session::get('backUrl'));
//        }
//
//        else {
//
//            return Redirect::to($this->redirectTo);
//        }

        $userId = Auth::user()->id;
        $plant_ids = PlantUser::where('user_id', $userId)->pluck('plant_id');
        $userPlants = Plant::whereIn('id', $plant_ids)->pluck('system_type');
        $noOfPlants = PlantUser::where('user_id', $userId)->count();

        if(Auth::user()->roles == 1)
        {
//            return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => 'admin/Plants','type' => 'single'], 200);
            $backUrl = route('admin.dashboard');
            return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => $backUrl], 200);

        }
        if(Auth::user()->roles == 5 )
        {
            if($noOfPlants == 1){
                $plant_id = PlantUser::where('user_id', $userId)->first('plant_id');
                $plant = Plant::where('id',$plant_id->plant_id)->first();

                if($plant->system_type == 4){
                    $type = 'hybrid';
                }else{
                    $type = 'bel';
                }
                $backUrl = url('admin/'.$type.'/user-plant-detail/'.$plant_id->plant_id);
            }else{
                $plant_ids = \App\Http\Models\PlantUser::where('user_id', Auth::user()->id)->pluck('plant_id');
                $userPlants = \App\Http\Models\Plant::whereIn('id', $plant_ids)->pluck('system_type');
                $arrayData = count(array_unique(json_decode(json_encode($userPlants),true)));
                $type = array_unique(json_decode(json_encode($userPlants)));
                if($arrayData == 1){
                    if($type[0] == 4){
                        $backUrl = route('admin.plants');
                    }else{
                        $backUrl = route('user.dashboard');
                    }
                }else{
                    $backUrl = route('user.dashboard');
                }
            }

            return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => $backUrl], 200);

        }
        if(Auth::user()->roles == 6 )
        {
            $backUrl = route('admin.plants.data',['type' => 'bel']);
            return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => $backUrl], 200);
        }
        else if ((count(array_unique(json_decode(json_encode($userPlants), true))) === 1)) {
            if ($userPlants) {
                if ($userPlants[0] == 4) {
//                    return redirect()->route('admin.plants');
//                    return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => 'admin/Plants','type' => 'single'], 200);
                    $backUrl = route('admin.plants');
                    return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => $backUrl], 200);
                } else {
//                    return redirect()->route('admin.dashboard');
//                    return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => 'admin/dashboard','type' => 'single'], 200);
                    $backUrl = route('admin.dashboard');
                    return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => $backUrl], 200);
                }
            }
        } else {
             $backUrl = route('admin.plants');
            return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => $backUrl], 200);
//            return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => 'admin/Plants','type' => 'double'], 200);
//            return redirect()->route('admin.plants');
        }

    }


//    public function authenticated($request , $user){
//        if($user->role=='super_admin'){
//            return redirect()->route('admin.dashboard') ;
//        }elseif($user->role=='brand_manager'){
//            return redirect()->route('brands.dashboard') ;
//        }
//    }
    protected function attemptLogin(Request $request)
    {
        $remember = $request->input('remember_me');

        return $this->guard()->attempt(
            $this->credentials($request), $remember
        );
    }
//    public function checkLoginCredentials()
//    {
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://gateway.isolarcloud.com.hk/v1/userService/login',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS =>'{
//    "appkey": "970C445528B8EB0C10450F82D8B08A14",
//    "user_account": "farrukh043@yahoo.com",
//    "user_password": "ayezah2019",
//    "login_type": "1"
//}',
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json',
//                'sys_code: 901',
//                'lang: _en_US'
//            ),
//        ));
//
//        $response = curl_exec($curl);
//        return $response;
//
////        curl_close($curl);
//    }


    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

    protected function sendFailedLoginResponse(Request $request)
    {
//        throw ValidationException::withMessages([
//            'ip' => [trans('auth.ipFailed')],
//        ]);
//    }
//        $validator = Validator::make($request->all(), [
//            'ip' => 'exists:users'
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(['status' => false, 'message' => 'Sorry! you do not have permission to access this website'], 200);
//        }
//        $ip = '123';
//        if(!User::where('ip',$ip)->exists())
//        {
//            return response()->json(['status' => false, 'message' => 'credentails.'], 200);
//        }
//        else
//        {
        return response()->json(['status' => false, 'message' => 'These credentials do not match our records.'], 200);
//        }


    }
}
