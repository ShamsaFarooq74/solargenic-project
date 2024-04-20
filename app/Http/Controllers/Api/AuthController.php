<?php


namespace App\Http\Controllers\Api;

use App\Http\Models\Notification;
use App\Http\Models\PlantUser;
use App\Http\Models\UserDevice;
use App\Http\Models\PlatformVersion;
use App\Http\Models\YearlyProcessedPlantDetail;
use App\Http\Models\Plant;
use App\Http\Models\ExpectedGenerationLog;
use App\Http\Models\MonthlyProcessedPlantDetail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends ResponseController
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
        }

        $request['password'] = Hash::make($request['password']);
        // dd($request->except(["confirm_password"]));
        $user = User::create($request->except(["confirm_password"]));
        if ($user) {
            $user->assignRole('User');
            $user['token'] = $user->createToken('token')->accessToken;
            $message = "Registration successful";
            $user = User::find($user->id);
            //inserting user device record
            UserDevice::updateOrCreate(["serial" => $request['serial']], array_merge($request->except(['name', 'phone', 'password', 'login_with']), ['status' => 'A', 'user_id' => $user->id]));
            return $this->sendResponse(1, $message, $user);
        } else {
            $error = "Something went wrong! Please try again";
            return $this->sendError(0, $error, null, 401);
        }

    }

    //login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required',
            'platform' => 'required',
        ]);

        $platform = $request['platform'];

        if ($platform == 'iOS') {
            $validator = Validator::make($request->all(), [
                'app_version' => 'required',
            ]);
        } elseif ($platform == 'android') {
            $validator = Validator::make($request->all(), [
                'app_version' => 'required',
            ]);
        }

        // dd($request->all());
        if ($validator->fails()) {
            return $this->sendError(0, "Sorry! Might be required fields are not found or empty.", $validator->errors()->all());
        }

        $response = $this->check_version($request->all());

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            $error = "Invalid credentials! Please try again";
            return $this->sendError(0, $error, null, 401);
        }
        $user = $request->user();
        $userId = Auth::user()->id;
        $plant_ids = PlantUser::where('user_id', $userId)->pluck('plant_id');
        $PlantsCount = Plant::whereIn('id',$plant_ids)->count();
        $userPlants = Plant::whereIn('id', $plant_ids)->pluck('system_type');
        $resulArray = count(array_unique(json_decode(json_encode($userPlants))));
        $is_hybrid = 'B';
        if ($resulArray == 1) {
            if (in_array(4, json_decode(json_encode($userPlants), true))) {
                $is_hybrid = 'H';
            } elseif (in_array(2, json_decode(json_encode($userPlants), true))) {
                $is_hybrid = 'G';
            }elseif (in_array(1, json_decode(json_encode($userPlants), true))){
                $is_hybrid = 'G';
            }
        }

//        $noOfPlants = PlantUser::where('user_id', $userId)->count();
        if($PlantsCount == 1) {
            $plant_id = PlantUser::where('user_id', $userId)->first('plant_id');
            $user['plant_id'] = $plant_id->plant_id;
        }else{
            $user['plant_id'] = "";
        }
        $user['token'] = $user->createToken('token')->accessToken;
        $user['PlantsCount'] = $PlantsCount;
        $user['type'] = $is_hybrid;
        UserDevice::updateOrCreate(["serial" => $request['serial']], array_merge($request->except(['name', 'phone', 'password', 'login_with']), ['status' => 'A', 'user_id' => $user->id]));
        if ($user) {
            $user["notificationCount"] = Notification::where("notification_type",'!=' , "Ticket")->where("notification_type",'!=' , "Custom")->where(['read_status'=>'N','user_id' => $user->id])->count();

            if ($user->is_active == 'N') {
                $status = -1;
                $message = "Sorry! Your account is blocked. You cant login. ";
                return $this->sendResponse($status, $message, $user);
            }
            else {

                return $this->sendResponse($response['status'],$response['message'], $user);
            }

        } else {
            $message = "Login unsuccessful";
            return $this->sendError(0, $message);
        }
    }

    private function check_version($request)
    {

        $response = array();

        $platform = strtolower($request['platform']);

        $version = $request['app_version'];
        $version = str_replace(".", "", $version);

        $data = PlatformVersion::where('platform', $platform)->first();

        $min_optional = $data['min_optional'];
        $min_optional = str_replace(".", "", $min_optional);
        $max_optional = $data['max_optional'];
        $max_optional = str_replace(".", "", $max_optional);
        $min_force = $data['min_force'];
        $min_force = str_replace(".", "", $min_force);
        $max_force = $data['max_force'];
        $max_force = str_replace(".", "", $max_force);
        // dd( $from_version, $to_version,$min_version, $max_version);
        if ($version <= $max_force && $version >= $min_force) {
            $response['status'] = -5;
            $response['message'] = "Update is available to download. Downloading the latest update you will get the latest features, improvements and bug fixes.";
            return $response;
        } elseif ($version <= $max_optional && $version >= $min_optional) {
            $response['status'] = -4;
            $response['message'] = "Update is available to download.
            Downloading the latest update you will get the latest features,improvements and bug fixes.";
            return $response;
        } else {
            $response['status'] = 1;
            $response['message'] = "Login Successful";
            return $response;
        }
    }
    public function updateProfile(Request $request)
    {
        $userID = $request->user()->id;
        $user = User::find($userID);
        if ($userID) {
            // get user details
            $user = User::where('id', $userID)->first();

            $data = array();
            // checks on each param
            if ($request['new_password']) {
                if (Hash::check($request['password'], $user->password)) {
                    //password is correct use your logic here
                    $data = array();
                    $data['password'] = Hash::make($request['new_password']);
                } else {
                    return $this->sendResponse(0, 'Password does not match!', null);

                }
            }
            if ($request['name']) {
                $data['name'] = $request['name'];
            }
            if ($request['phone']) {
                $data['phone'] = $request['phone'];
            }

            // updating data
            $user->update($data);

            // pic updation
            if ($request->has('profile_pic')) {
                $format = '.png';
                $entityBody = $request->file('profile_pic');// file_get_contents('php://input');

                $imageName = $user->id . time() . $format;
                $directory = "/user_photo/";
                $path = base_path() . "/public" . $directory;

                $entityBody->move($path, $imageName);

                $response = $directory . $imageName;

                $user->profile_pic = $response;
                $user->save();
            }

            $message = "Profile updated successfully";
            return $this->sendResponse(1, $message, $user);

        } else {
            return $this->sendError(0, "User Id not found!", null);
        }
    }

    //logout
    public function logout(Request $request)
    {

        $isUser = $request->user()->token()->revoke();
        if ($isUser) {
            if ($request->has("serial")) {
                UserDevice::where("serial", $request["serial"])->update(["status" => "D"]);
            }
            return $this->sendResponse(1, 'Successfully logged out', null);
        } else {
            return $this->sendError(0, "Something went wrong!", null);
        }


    }

    // About us
    public function about_us()
    {
//        $total_cities = DB::select("SELECT  COUNT(DISTINCT city) city_count FROM plants");
//        $total_capacity = DB::select(" SELECT SUM(capacity) as capacity  FROM plants");
//        $plants = Plant::where('id' , '!=' , '')->get();
//        $annual_generation = 0;
//        foreach($plants as $plant){
//            $yearly_processed_data = YearlyProcessedPlantDetail::where('plant_id',$plant->id)->whereBetween('created_at',[date('Y-01-01 0:00:00'),date('Y-m-d 23:59:00')])->first();
//            $yearly_generation = $yearly_processed_data->yearlyGeneration;
//            $annual_generation=$annual_generation+$yearly_generation;
//        }
//
//        $annual_generation = ((double)$annual_generation / 1000000);
//
//        $actual_generation=[];
//        for($i = 1; $i <= 12; $i++){
//            $m = $i < 10 ? '0'.$i : $i;
//            $day_in_month = cal_days_in_month(CAL_GREGORIAN,date($i),date('Y'));
//            $actual_sum = 0;
//            $revenue_sum = 0;
//            foreach ($plants as $key => $plant) {
//
//                $daily_expected_sum = 0;
//                for($j = 1; $j <= $day_in_month; $j++){
//                    $d = $j < 10 ? '0'.$j : $j;
//                    $monthlyExpected = ExpectedGenerationLog::where('plant_id',$plant->id)->where('created_at','<=',date('Y-'.$m.'-'.$d.' 23:59:00'))->orderBy('created_at','desc')->first();
//                    $daily_expected_sum += $monthlyExpected != null ? $monthlyExpected->daily_expected_generation : 0;
//                }
//                $monthlyGeneration = MonthlyProcessedPlantDetail::where('plant_id',$plant->id)->whereMonth('created_at', '=', date($i))->first();
//                $daily_actual_sum = $monthlyGeneration != null ? $monthlyGeneration->monthlyGeneration : 0;
//                $actual_sum += $daily_actual_sum;
//                $revenue_sum  += $daily_actual_sum * $plant->benchmark_price;
//
//            }
//            $actual_generation[$i] = $actual_sum;
//        }
//
//        $reduction =  round(array_sum($actual_generation) * 0.000646155 , 2);

        $about_us=array('detail'=>'Beacon Energy Limited (BEL) is a solar services and Solar EPC Company. A project of the Beaconhouse Group, BEL aims to provide affordable renewable energy to businesses and homeowners alike.

Solar projects demand powerful, reliable, and scalable infrastructure in meeting those requirements. BEL provides project development & financing support, design & construction services, as well as O&M and complete system integration.

With over 45 years of experience in the service industry, The Beaconhouse Group has put reliability, dependability and quality customer service at the very heart of BEL. Working with only the best equipment suppliers in the world, BEL aims to move Pakistan to a 21st century energy paradigm by offering its end users a decentralized, efficient and environmentally friendly energy production network.',
            'city'=>"21", 'capacity'=>"14.53" , 'generation'=> "20.15", 'co2_reduction'=> "13.02",
            'facebook'=> 'https://www.facebook.com/',
            'instagram'=> 'https://www.instagram.com/',
            'youtube'=> 'https://www.youtube.com/',
            'linkedin'=> 'https://www.linkedin.com/',
            'website_url'=> 'https://belenergise.com/'  );
        return $this->sendResponse(1, 'About Us', $about_us);

    }
}
