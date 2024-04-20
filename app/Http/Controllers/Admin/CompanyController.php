<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Company;
use App\Http\Models\Plant;
use App\Http\Models\PlantUser;
use DB;

class CompanyController extends Controller
{
    public function allcompanies(Request $request)
    {
        $input = $request->all();
        $user_plant = array();

        Session::put(['filter'=> $input]);

        $plant_nam   = $request->plant_name == null || $request->plant_name == "all" ? 'all': $request->plant_name;
        $company     = $request->company == "all" ? 0 : $request->company;
        $user     = $request->user == "all" ? 0 : $request->user;

        if($plant_nam == 'all') {
            $plant_name[] = 0;
        }
        else {

            $plant_name = $plant_nam;
        }

        $comp = '';
        $usr = '';
        $pl_n = '';

        if($company != 0) {
            $comp = '=';
        }
        else {
            $comp = '!=';
        }

        if($user != 0) {
            $usr = '=';
        }
        else {
            $usr = '!=';
        }

        $filter_data['company_array'] = Company::get(['id', 'company_name']);
        $filter_data['user_array'] = DB::table('users')
            ->join('plant_user', 'users.id', 'plant_user.user_id')
            ->select('plant_user.user_id', 'plant_user.plant_id', 'users.*')
            ->groupBy('user_id')
            ->get();

        $filter_data['plants'] = Plant::all();

        if($plant_nam == 'all') {

            if($user != 0) {

                $user_plant = PlantUser::where('user_id', $usr, $user)->pluck('plant_id');

                $companies = Company::with([
                    'plant' => function ($q1) use ($usr, $user, $user_plant) {
                        return $q1->with([
                            'plant_user' => function ($q2) use ($usr, $user) {
                                return $q2->with('user')->where('user_id', $usr, $user)->groupBy('user_id');
                            }
                        ])->whereIn('id', $user_plant);
                    }
                ])->where('id', $comp, $company)->get();
            }

            else {

                $companies = Company::where('is_deleted','N')->with([
                    'plant' => function ($q1) use ($usr, $user) {
                        return $q1->with([
                            'plant_user' => function ($q2) use ($usr, $user) {
                                return $q2->with('user')->where('user_id', $usr, $user);
                            }
                        ]);
                    }
                ])->where('id', $comp, $company)->get();
            }
        }

        else {

            $companies = Company::where('is_deleted','N')->with([
                'plant' => function ($q1) use ($plant_name, $usr, $user) {
                    return $q1->with([
                        'plant_user' => function ($q2) use ($usr, $user) {
                            return $q2->with('user')->where('user_id', $usr, $user);
                        }
                    ])->whereIn('id', $plant_name);
                }
            ])->where('id', $comp, $company)->get();

        }
        return view('admin.companies', ['companies'=>$companies, 'filter_data'=>$filter_data]);
    }

    public function allcompaniesfilter(Request $request) {

        $company_id = $request->company;
        $plant_arr = $request->plant_name;
        $user_id = $request->user;

        if($plant_arr == null && $user_id == null) {

            $plants = Plant::where('company_id', $company_id)->get();
            $plants_arr = $plants->pluck('id');
            $users = DB::table('plants')
                        ->leftJoin('plant_user', 'plants.id', 'plant_user.plant_id')
                        ->leftJoin('users', 'plant_user.user_id', 'users.id')
                        ->whereIn('plants.id', $plants_arr)
                        ->get();

            $user_arr = $users->groupBy('name');
        }

        return [$plants, $user_arr];

    }

    public function addcompany(Request $request)
    {
        if(Auth::user()->roles == 5){
            return redirect('/home');
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'contact_number' => 'required',
            'company_name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('admin/all-company')->with('error', 'Sorry! Might be required fields are empty.');
        }


        if($files=$request->file('profile_pic')){
            $logo = date("dmyHis.").gettimeofday()["usec"].'_'.$files->getClientOriginalName();
            $files->move(base_path('/company_logo'),$logo);
        }

        $company = new Company();
        $input =  $request->all();
        $input['admin_id'] = auth()->user()->id;
        $input['logo'] = isset($logo) && !empty($logo) ? $logo : NULL;
        $responce = $company->fill($input)->save();
        if($responce){
            return redirect()->back()->with('success', 'Congratulation! Company added successfully.');
//            return redirect('admin/all-company')->with('success', 'Congratulation! Company added successfully.');
        }else{
            return redirect()->back()->with('error', 'Some error occured');
        }
    }

    public function updatecompany(Request $request)
    {
        if(Auth::user()->roles == 5){
            return redirect('/home');
        }
        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
            'contact_number' => 'required',
            'email' => 'required',
        ]);
//        return $request;

        if ($validator->fails()) {
            return redirect('admin/all-company')->with('error', 'Sorry! Might be required fields are empty.');
        }


        if($files=$request->file('profile_pic')){
            $logo = date("dmyHis.").gettimeofday()["usec"].'_'.$files->getClientOriginalName();
            $files->move(base_path('/company_logo'),$logo);
        }

        $input =  $request->all();
        if(isset($logo) && !empty($logo)){
            $input['logo'] = $logo;
        }
        $company = Company::findOrFail($input['company_id']);
        $response =  $company->fill($input)->save();
        if($response){
            return redirect()->back()->with('success', 'Congratulation! Company updated successfully.');
        }else{
            return redirect()->back()->with('error', 'Some error occured');
        }
    }

    public function deletecompany(Request $request)
    {
        $company = Company::findOrFail($request->id);
        $company->is_deleted = 'Y';
        $company->update();
        if($company)
        {
            return ['status' => true];
        }
        else
        {
            return ['status' => false];
        }

    }
    public function getCompany(Request $request)
    {

        return Company::where('id',$request->companyId)->first();

    }


}
