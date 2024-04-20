<?php





namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Validator;

use App\Http\Models\User;
use App\Http\Models\UserCompany;

use App\Http\Models\Company;

use App\Http\Models\Plant;

use App\Http\Models\PlantUser;

use Spatie\Permission\Models\Role;
use App\Http\Controllers\Api\PlantSiteDataController;



class UserController extends Controller

{

    public function allusers(Request $request)

    {

        if(Auth::user()->roles == 3){

            $filter_data['company_array'] = Company::where('id', Auth::user()->company_id)->get(['id', 'company_name']);
            $filter_data['role_array'] = Role::whereNotIn('id',[1,2])->where('is_hidden', 0)->get();
            $filter_data['plants'] = Plant::where('company_id', auth()->user()->company_id)->get();

            $roles = Role::whereNotIn('id',[1,2])->where('is_hidden', 0)->get();
            $companies = Company::where('id', Auth::user()->company_id)->get(['id', 'company_name']);
            $plants = Plant::where('company_id', auth()->user()->company_id)->get();
            $plant_arrays = Plant::where('company_id', auth()->user()->company_id)->pluck('id');
            $plant_arrays = $plant_arrays->toArray();

            $auth_plant_arr = PlantUser::whereIn('plant_id', $plant_arrays)->pluck('user_id');

        }else if(Auth::user()->roles == 1){

            $filter_data['company_array'] = Company::get(['id', 'company_name']);
            $filter_data['role_array'] = Role::where('is_hidden', 0)->get();
            $filter_data['plants'] = Plant::all();

            $roles = Role::where('is_hidden', 0)->get();
            $companies = Company::all();
            $plants = Plant::all();

        }

        $input = $request->all();
        $user_plant = array();
        $companyArray = Company::pluck('id')->toArray();

        Session::put(['filter'=> $input]);

        $companyIDs = array();
        $plantIDs = array();
        $roleIDs = array();
        $userIDs = array();

        $f_company = isset($request->company) ? $request->company : 'all';
        $f_plant   = isset($request->plant_name) ? $request->plant_name : 'all';
        $f_role     = $request->role == '' || $request->role == "all" ? 'all' : $request->role;

        $companyIDs[] = $f_company;
        $roleIDs[] = $f_role;

        if($f_company == 'all') {

            $companyIDs = Company::pluck('id')->toArray();
        }

        if($f_role == 'all') {

            $roleIDs = Role::pluck('id')->toArray();
        }

        if($f_plant == 'all') {

            $plantIDs = Plant::pluck('id')->toArray();
        }
        else {

            $plantIDs = $f_plant;
        }

        $userCompanyIDs = UserCompany::whereIn('company_id', $companyIDs)->groupBy('user_id')->pluck('user_id')->toArray();
        $userPlantIDs = PlantUser::whereIn('plant_id', $plantIDs)->groupBy('user_id')->pluck('user_id')->toArray();
        $userRoleIDs = User::whereIn('roles', $roleIDs)->pluck('id')->toArray();

        $userIDs = array_intersect($userCompanyIDs, $userPlantIDs, $userRoleIDs);

        /*$users = User::with([
            'plant_user' => function ($q1) use ($plantIDs) {
                return $q1->with('plant')->whereIn('plant_id', $plantIDs);
            }
            , 'user_companies' => function ($q2) use ($companyIDs) {
                return $q2->with('company')->whereIn('company_id', $companyIDs);
            }
            , 'role' => function ($q3) use ($roleIDs) {
                return $q3->whereIn('id', $roleIDs);
            }])->get();*/

        $users = User::with([
                'plant_user' => function ($q1) {
                    return $q1->with('plant');
                }
                , 'user_companies' => function ($q2) {
                    return $q2->with('company');
                }
                , 'role'])->whereIn('id', $userIDs)->get();

        return view('admin.users', ['users'=>$users, 'filter_data'=>$filter_data,'companies'=>$companies,'roles'=>$roles,'plants'=>$plants, 'companyArray' => $companyArray]);
    }



    public function adduser(Request $request)

    {

        if(Auth::user()->roles != '1' && Auth::user()->roles != '3'){

            return redirect('/home');

        }

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'email' => 'required|string|email|unique:users',

            'username' => 'required|string|unique:users',

            'password' => 'required',

            'confirm_password' => 'required|same:password',

            'user_type' => 'required',

        ]);



        if ($validator->fails()) {

            return redirect()->back()->with('error', $validator->errors()->first());

        }



        if($files=$request->file('profile_pic')){

            $profile_pic = $files->getClientOriginalName();

            $files->move(public_path('user_photo'),$profile_pic);

        }



        $user = new User();

        $input =  $request->all();

        $input['profile_pic'] = isset($profile_pic) && !empty($profile_pic) ? $profile_pic : '';

        $input['password'] = Hash::make($input['password']);

        $input['roles'] = $input['user_type'];
        $user = User::create($input);

        if($user){

            if($user->roles == 1 || $user->roles == 2) {

                $input['company_id'] = Company::pluck('id')->toArray();
            }

            foreach ($input['company_id'] as $single_com_id) {

                $plantuser = new UserCompany();

                $plantuser->user_id = $user->id;

                $plantuser->company_id = $single_com_id;

                $plantuser->save();

            }

            if($user->roles == 1 || $user->roles == 2) {

                $input['plant_id'] = Plant::pluck('id')->toArray();
            }

            foreach ($input['plant_id'] as $single_plant_id) {

                $plantuser = new PlantUser();

                $plant_input['user_id'] = $user->id;

                $plant_input['plant_id'] = $single_plant_id;

                $responce = $plantuser->fill($plant_input)->save();

            }

            return redirect()->back()->with('success', 'User added successfully');

        }else{

            Session::flash('error', 'Sorry! User not added');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back()->with('error', 'Sorry! User not added');

        }

    }



    public function updateuser(Request $request)

    {

        if(Auth::user()->roles != '1' && Auth::user()->roles != '3'){

            return redirect('/home');

        }



        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'email' => 'required',

            'user_type' => 'required',

        ]);



        if ($validator->fails()) {

            Session::flash('message', 'Sorry! Might be required fields are empty.');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back()->with('error', 'Sorry! Might be required fields are empty.');

        }



        if($files=$request->file('profile_pic')){

            $profile_pic = $files->getClientOriginalName();

            $files->move(public_path('user_photo'),$profile_pic);

        }

        $input = $request->except('password', 'password_confirmation');

        if(isset($profile_pic) && !empty($profile_pic)){

            $input['profile_pic'] = $profile_pic;

        }

        if($input['user_type'] <= 2){

            $input['company_id'] = NULL;

        }

        $input['roles'] = $input['user_type'];

        if($request->password != null) {

            $input['password'] = Hash::make($request->password);
        }

        $user = User::findOrFail($input['user_id']);

        $response =  $user->fill($input)->save();

        if($response){

            $user_com = UserCompany::where('user_id',$input['user_id'])->delete();
            $plant_user = PlantUser::where('user_id',$input['user_id'])->delete();

            // dd($plant_user);

            if($input['user_type'] == 1 || $input['user_type'] == 2) {

                $input['company_id'] = Company::pluck('id')->toArray();
            }

            foreach ($input['company_id'] as $single_com_id) {

                $plantuser = new UserCompany();

                $plantuser->user_id = $input['user_id'];

                $plantuser->company_id = $single_com_id;

                $plantuser->save();

            }

            if($input['user_type'] == 1 || $input['user_type'] == 2) {

                $input['plant_id'] = Plant::pluck('id')->toArray();
            }

            foreach ($input['plant_id'] as $single_plant_id) {

                $plant_input['user_id'] = $input['user_id'];

                $plant_input['plant_id'] = $single_plant_id;

                $plantuser = PlantUser::where('user_id',$input['user_id'])->where('plant_id',$single_plant_id)->first();

                if($plantuser){

                    $responce = $plantuser->fill($plant_input)->save();

                }else{

                    $responce = PlantUser::create($plant_input);

                }

            }


            // //     Session::flash('message', 'Congratulation! User updated successfully');

            // //     Session::flash('alert-class', 'alert-success');

            // //     return redirect()->back();

            // // }else{

            // //     Session::flash('message', 'Sorry! User updated but plant not update.');

            // //     Session::flash('alert-class', 'alert-danger');

            // //     return redirect()->back();

            // // }

            // }else{

                Session::flash('message', 'Congratulation! User updated.');

                Session::flash('alert-class', 'alert-success');

                return redirect()->back()->with('success', 'Congratulation! User updated.');

        }else{

            Session::flash('message', 'Sorry! User not updated');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back()->with('error', 'Sorry! User not updated');

        }

    }



    public function deleteuser(Request $request)

    {

        if(Auth::user()->roles != '1' && Auth::user()->roles != '3'){

            return redirect('/home');

        }



        $id = $request->id;

        $user = User::findOrFail($id)->delete();

        $plant_user = PlantUser::where('user_id',$id)->delete();

        if($user || $plant_user){

            Session::flash('message', 'User deleted successfully');

            Session::flash('alert-class', 'alert-success');

            return redirect()->back()->with('success', 'User deleted successfully');

        }else{

            Session::flash('message', 'Sorry! User not delete.');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back()->with('error', 'Sorry! User not delete.');

        }

    }





    public function blockUser($id)

    {

        if(Auth::user()->is_admin != 'Y' && Auth::user()->roles != '1'){

            return redirect('/home');

        }



        $user = User::findOrFail($id);

        $input['is_active'] = 'N';

        $response =  $user->fill($input)->save();

        if($response){

            Session::flash('message', 'User Blocked successfully');

            Session::flash('alert-class', 'alert-success');

            return redirect()->back();

        }else{

            Session::flash('message', 'Sorry! User not block.');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back();

        }

    }



    public function unblockUser($id)

    {

        if(Auth::user()->is_admin != 'Y' && Auth::user()->roles != '1'){

            return redirect('/home');

        }



        $user = User::findOrFail($id);

        $input['is_active'] = 'Y';

        $response =  $user->fill($input)->save();

        if($response){

            Session::flash('message', 'User Un-blocked successfully');

            Session::flash('alert-class', 'alert-success');

            return redirect()->back();

        }else{

            Session::flash('message', 'Sorry! User not un-block.');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back();

        }



    }



    public function my_account($id)

    {

        $user = User::find($id);

        // dd($user->company->company_name);

        return view('admin.profile',compact('user'));

    }



    public function update_profile(Request $request)

    {

        $validator = Validator::make($request->all(), [

            'name' => 'required',

            'email' => 'required',

            'username' => 'required',

        ]);



        if ($validator->fails()) {

            Session::flash('message', 'Sorry! Might be required fields are empty.');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back();

        }



        if($files=$request->file('user_photo')){

            $profile_pic = $files->getClientOriginalName();

            $files->move(public_path('user_photo'),$profile_pic);

        }



        $input = $request->all();

        if(isset($profile_pic) && !empty($profile_pic)){

            $input['profile_pic'] = $profile_pic;

        }



        $user = User::findOrFail($input['user_id']);

        $response =  $user->fill($input)->save();

        if($response){

            Session::flash('message', 'Congratulation! Profile updated.');

            Session::flash('alert-class', 'alert-success');

            return redirect()->back();

        }else{

            Session::flash('message', 'Sorry! Profile not updated');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back();

        }

    }



    public function update_password(Request $request)

    {

        $validator = Validator::make($request->all(), [

            'current_password' => 'required',

            'new_password' => 'required',

            'confirm_password' => 'required|same:new_password'

        ]);



        if ($validator->fails()) {

            Session::flash('message', 'Sorry! Might be required fields are empty or Confirm password does not match.');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back();

        }





        $request['password'] = $request['current_password'];

        $credentials = request(['email', 'password']);

        // dd($credentials);

        if(!Auth::attempt($credentials)){

            Session::flash('message', 'Invalid credentials! Please try again');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back();

        }

        $user = $request->user();

        $input['password'] = Hash::make($request['new_password']);

        // dd($input);

        $response =  $user->fill($input)->save();

        if($response){

            Session::flash('message', 'Congratulation! Password updated successfully.');

            Session::flash('alert-class', 'alert-success');

            return redirect()->back();

        }else{

            Session::flash('message', 'Sorry! Password not update');

            Session::flash('alert-class', 'alert-danger');

            return redirect()->back();

        }

    }

    public function getUserCompanyPlant(Request $request) {

        $company_id = array_column($request->company_id, 'company_id');

        $company_list = Plant::whereIn('company_id', $company_id)->get(['id', 'plant_name']);

        return $company_list;
    }

}

