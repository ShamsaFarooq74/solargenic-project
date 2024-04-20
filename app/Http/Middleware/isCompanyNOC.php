<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class isCompanyNOC
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

                if(auth()->user()->roles == 4){

                    return $next($request);
                }

                else {

                    return redirect()->back()->with('error',"You have no right of that module!");
                }

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
