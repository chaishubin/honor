<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Common;
use App\Models\Manager\ManagerModel;
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
        $session_token = $request->session()->exists($cookie_token);

        //存储manager_token
        $request->session()->put('manager_token',$cookie_token);

        if (!$cookie_token || !$session_token){
            Log::info('管理员尝试非法登录，其尝试的manager_token是：'.$cookie_token);
            return Common::jsonFormat('500','server reject !');
        }

        $manager = ManagerModel::where('access_token',$cookie_token)->first();
        //如果没根据 manager_token 查到用户登陆信息，说明用户可能在其他端登录了，此时要清空cookie，让其重新登陆
        if (!$manager){
            return response('该账号已在其他端登录，请重新登录')->cookie('user_token','');
        }

        return $next($request);
    }
}
