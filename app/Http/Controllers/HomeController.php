<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Auth;



class HomeController extends Controller

{

    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct()

    {

    }



    /**

     * Show the application dashboard.

     *

     * @return \Illuminate\Contracts\Support\Renderable

     */

    public function index()

    {

        $this->middleware('auth');

        if(Auth::user()->roles == 1 || Auth::user()->roles == 2 || Auth::user()->roles == 3 || Auth::user()->roles == 4) {

            return redirect()->route('admin.dashboard');
        }

        else if(Auth::user()->roles == 5){

            return redirect()->route('admin.plants');
        }

        else {

            return redirect()->route('admin.plants');
        }

    }

    public function newDashboard()

    {

        return view('admin.new_dashboard');

    }

    public function term_and_condition()

    {

        return view('auth.term_and_condition');

    }



    public function privacy_policy()

    {

        return view('auth.privacy_policy');

    }

}

