<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class EnableCrossRequestMiddleware
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
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        Log::info('ss'.$origin);

        $response = $next($request);
        $allow_origin = [
            'http://localhost:8080',
            'http://ceshih5.honour.huobanys.cn'
        ];
        if (in_array($origin, $allow_origin)) {
            $response->header('Access-Control-Allow-Origin',$origin);
            $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
            $response->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;

    }
}
