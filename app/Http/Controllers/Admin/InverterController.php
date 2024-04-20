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
use App\Http\Models\PlantUser;
use App\Http\Models\Inverter;
use Spatie\Permission\Models\Role;

class InverterController extends Controller
{
    public function allinverters()
    {
        return view('admin.inverter.inverters');
    }



}
