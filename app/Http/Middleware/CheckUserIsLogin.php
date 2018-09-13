<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Common;
use Closure;
use Illuminate\Support\Facades\Log;

class CheckUserIsLogin
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
        $cookie_token = $request->cookie('user_token');
        $session_token = $request->session()->exists($cookie_token);

        if (!$cookie_token || !$session_token){
            Log::info('用户尝试非法登录，其尝试的user_token是：'.$cookie_token);
            return Common::jsonFormat('500','server reject !');
        }

        return $next($request);
    }
}
