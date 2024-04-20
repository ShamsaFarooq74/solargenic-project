<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
//use App\Http\Models\User;
use App\Http\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
    protected $redirectTo = '/home';

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

    protected function attemptLogin(Request $request)
    {
        $remember = $request->input('remember_me');

        return $this->guard()->attempt(
            $this->credentials($request), $remember
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ip = '123';
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'ip' => $ip
        ];
//        $credentials = $request->only('email', 'password');
//        $credentials = array_add($credentials, 'ip', $ip);
        return $credentials;
//        return ['email' => $request->get('email'), 'password'=>$request->get('password'),'ip' => $ip];
    }

    protected function authenticated(Request $request, $user)
    {

        $backUrl = '' . Session::get('backUrl');

        return response()->json(['status' => true, 'message' => 'Login successful', 'redirect' => $backUrl], 200);
    }

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
        $ip = '123';
        if(!User::where('ip',$ip)->exists())
        {
            return response()->json(['status' => false, 'message' => 'credentails.'], 200);
        }
        else
        {
            return response()->json(['status' => false, 'message' => 'These credentials do not match our records.'], 200);
        }


    }

    protected function validateLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip' => 'exists:users'
        ]);
    }
}
