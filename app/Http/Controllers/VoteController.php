<?php

namespace App\Http\Controllers;

use App\Http\Requests\Manager\ExpertListRequest;
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
            $check_expert = $this->checkExpert($voters['phone_number']);
            $voters_type = $check_expert === 'pass' ? 2 : 1; //pass是专家，否则就是大众

            $vote_relation = new VoteRelationModel();
            $vote_relation->candidate_id = $info['candidate_id'];
            $vote_relation->voters_id = $voters['id'];
            $vote_relation->voters_type = $voters_type;
            $vote_relation->vote_time = time();
            $vote_relation->voters_ip = Common::getClientIp();
            $res = $vote_relation->save();

            if ($res){
                //将投票信息写入redis
                
            }

        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','投票失败');
        }
    }


    /**
     * @param ExpertListRequest $request
     * @return string
     * 检查传入的手机号是不是有效的专家，有效包括是专家身份，且未被删除
     */
    public function checkExpert(ExpertListRequest $request)
    {
        $info = $request->all();

        $manager = new ManagerController();

        $expert_list = $manager->expertList($request)->content();
        $expert_list = json_decode($expert_list,true)['data']['data'];

        foreach ($expert_list as $v){
            if ($info['phone_number'] == $v['phone_number']){
                return 'pass';
            }
        }

        return 'reject';
    }
}
