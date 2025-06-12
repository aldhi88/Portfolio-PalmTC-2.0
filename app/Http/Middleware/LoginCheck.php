<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $id = 1;
        Auth::loginUsingId($id);
        return $next($request);
        // session_start();
        // if(!isset($_SESSION['id_session'])){
        //     Auth::logout();
        //     return redirect(env('PORTAL_URL'));
        // }else{
        //     if(!Auth::check()){
        //         $id = $_SESSION['login_user'];
        //         Auth::loginUsingId($id);
        //     }
        //     return $next($request);
        // }


    }
}
