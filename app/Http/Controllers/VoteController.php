<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoteController extends Controller
{
    public function userVote(Request $request)
    {
        $info = $request->all();

        try{

        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','投票失败');
        }
    }
}
