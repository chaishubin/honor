<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vote\UserVoteRequest;
use App\Models\DoctorSignUp\UserModel;
use App\Models\Vote\VoteRelationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class VoteController extends Controller
{
    public function userVote(UserVoteRequest $request)
    {
        $info = $request->all();

        try{
            $cookie_user_token = $request->cookie('user_token');
            $voters = UserModel::where('access_token',$cookie_user_token)->first();
            if (!$voters){
                return Common::jsonFormat('500','用户信息不正确');
            }

            $vote_relation = new VoteRelationModel();
            $vote_relation->candidate_id = $info['candidate_id'];
            $vote_relation->voters_id = $voters['id'];
            $vote_relation->voters_type = 8;
            $vote_relation->vote_time = time();
            $vote_relation->voters_ip = Common::getClientIp();
            $vote_relation->save();

        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','投票失败');
        }
    }


    public function checkExpert(Request $request)
    {
        $info = $request->all();

    }
}
