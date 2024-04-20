<?php


namespace App\Http\Controllers\Api;

use App\Mail\ForgotPassword;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Models\Setting;


class ForgotPasswordController extends ResponseController
{
    public function generateForgotPassCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        $web = isset($request->web) && $request->web == 'web' ? 'web' : '';

        if($validator->fails()) {
            if($web == 'web'){
                Session::flash('message', 'Something went wrong! Please try again');
                Session::flash('alert-class', 'alert-danger');
                return redirect()->back();
            }else{
                return $this->sendError(0, "Something went wrong! Please try again", $validator->errors()->all());
            }
        }

        $email = $request->get('email');
        $userDetails = User::where('email', '=', $email)->selectRaw("id,name")->first();
        // dd($web, $request->all());
        if (!empty($userDetails)) {
            $str = Str::random(60);
            $resetUrl = url("/reset-password/" . $str);
            $smtp_credentials = Setting::findOrFail(1);

            Mail::to($email)->send(new ForgotPassword($userDetails->name, $resetUrl));
            User::where('id', '=', $userDetails->id)->update([
                "reset_token" => $str
            ]);

            if($web == 'web'){
                Session::flash('message', 'Password reset email sent! Please check your inbox');
                Session::flash('alert-class', 'alert-success');
                return redirect()->back();
            }else{
                return $this->sendResponse(1, 'Password reset email sent! Please check your inbox', null);
            }
        } else {
            if($web == 'web'){
                Session::flash('message', 'Email does not exist');
                Session::flash('alert-class', 'alert-danger');
                return redirect()->back();
            }else{
                return $this->sendResponse(0, 'Email does not exist', null);
            }

        }
    }

    public function validateEmailToken($url)
    {
        $validate = User::where('reset_token', '=', $url)->first();

        if (!empty($validate)) {
            $userID = $validate->id;
            return view('email.change-password', compact('userID'));
        } else {
            return abort(401);
        }
    }

    public function changePassword(Request $request)
    {
        $userID = $request->input('userID');
        $password = $request->input('newPassword');

        $userDetails = User::find($userID);
        $userDetails->password = Hash::make($password);
        $userDetails->reset_token = null;
        $userDetails->save();

        Session::flash('message', 'Your password has been changed. Please login with your new credentials');
        Session::flash('alert-class', 'alert-success');
        return redirect('login');
    }
}
