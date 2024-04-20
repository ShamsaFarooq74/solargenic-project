<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class isSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {

            if(Auth::check()) {

                if(auth()->user()->roles == 1){

                    return $next($request);
                }

                return redirect()->back()->with('error',"You have no right of that module!");

            }

            else {

                return redirect()->route('login');
            }
        }

        catch(Exception $ex) {

            return redirect()->back()->with('error', "login is required for this!");
        }
    }
}
