<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class VoteController extends Controller
{
    public function userVote(Request $request)
    {
        $info = $request->all();

        try{
            $redis = Redis::set('name','chaishubin');

            $res = Redis::get('name');

            echo $res;

        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','投票失败');
        }
    }
}
