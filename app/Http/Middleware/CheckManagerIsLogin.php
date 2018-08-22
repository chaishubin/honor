<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Common;
use Closure;
use Illuminate\Support\Facades\Log;

class CheckManagerIsLogin
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
        $cookie_token = $request->cookie('manager_token');
//        $tt = $request->session()->all();
////        $cc = $request->cookies;
//        dd($tt);die;
//        var_dump($tt);die;
        $session_token = $request->session()->exists($cookie_token);

        Log::info('cookie:'.$cookie_token);
        Log::info('session:'.$session_token);
        if (!$cookie_token || !$session_token){
            Log::info('管理员尝试非法登录，其尝试的manager_token是：'.$cookie_token);
            return Common::jsonFormat('500','server reject !');
        }

        return $next($request);
    }
}
