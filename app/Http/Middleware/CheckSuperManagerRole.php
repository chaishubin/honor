<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Common;
use App\Models\Manager\ManagerModel;
use Closure;

class CheckSuperManagerRole
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

        $manager = ManagerModel::where('access_token',$cookie_token)->first();

        //超级管理员1，普通管理员2
        if ($manager['role'] != 1){
            return Common::jsonFormat('500','server role reject');
        }
        return $next($request);
    }
}
