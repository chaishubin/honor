<?php

namespace App\Http\Controllers;

use App\Http\Requests\Manager\ExpertListRequest;
use App\Http\Requests\Vote\CandidateVoteListRequest;
use App\Http\Requests\Vote\UserVoteRequest;
use App\Models\DoctorSignUp\DoctorModel;
use App\Models\DoctorSignUp\HospitalModel;
use App\Models\DoctorSignUp\UserModel;
use App\Models\Vote\VoteModel;
use App\Models\Vote\VoteRelationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class VoteController extends Controller
{
    /**
     * @param UserVoteRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 用户投票
     */
    public function userVote(UserVoteRequest $request)
    {
        $info = $request->all();

        try{
            $cookie_user_token = $request->cookie('user_token');
            $voters = UserModel::where('access_token',$cookie_user_token)->first();
            if (!$voters){
                return Common::jsonFormat('500','用户信息不正确');
            }

//            $voters = UserModel::where('access_token','4ecdfcbe1dcf482eb9bf5e1a0a761091')->first();
//            $info['award_id'] = 101;

            $check_expert = $this->checkExpert($voters['phone_number']);
            $voters_type = $check_expert === 'pass' ? 2 : 1; //pass是专家2，否则就是大众1

            $vote_relation = VoteRelationModel::where('voters_id',$voters['id'])->get();
            if ($voters_type == 1){ // 大众
                if ($vote_relation){ // 若查询到记录，说明该用户已经至少投过一次票了，普通用户只能参与投票一次
                    return Common::jsonFormat('500','您只能投一票，您已经参与过投票了');
                }
            }else{ // 专家
                $count_vote = VoteRelationModel::where('voters_id',$voters['id'])->count();

                if ($count_vote >= 10){ // 专家在每个奖项有10次投票机会
                    return Common::jsonFormat('500','您在该奖项的投票次数已用完');
                }

            }

            //将投票信息写入投票-选举人关系表中
            $vote_relation = new VoteRelationModel();
            $vote_relation->candidate_id = $info['candidate_id'];
            $vote_relation->voters_id = $voters['id'];
            $vote_relation->award_id = $info['award_id'];
            $vote_relation->voters_type = $voters_type;
            $vote_relation->vote_time = time();
            $vote_relation->voters_ip = ip2long(Common::getClientIp());
            $res = $vote_relation->save();

            if ($res){
                //判断redis中是否存在此key 存在返回1，不存在返回0
                if (Redis::exists('rongyao2018:vote:'.$voters['candidate_id'].':'.$info['award_id'])){ //存在,进行 更新操作
                    if ($voters_type == 2){ //专家
                        //专家票数加一
                        Redis::hincrby('rongyao2018:vote:'.$voters['candidate_id'].':'.$info['award_id'],'expert_votes',1);
                    }else{ // 大众
                        //大众票数加一
                        Redis::hincrby('rongyao2018:vote:'.$voters['candidate_id'].':'.$info['award_id'],'public_votes',1);
                    }
                }else{ //不存在，进行新增操作
                    if ($voters_type == 2){ //专家
                        $public_votes = 0;
                        $expert_votes = 1;
                    }else{ // 大众
                        $public_votes = 1;
                        $expert_votes = 0;
                    }

                    //存入hash类型的redis
                    Redis::hmset('rongyao2018:vote:'.$voters['candidate_id'].':'.$info['award_id'],['public_votes' => $public_votes, 'expert_votes' => $expert_votes, 'score' => 0]);
                }
//                $res = Redis::hgetall('rongyao2018:vote:'.$voters['candidate_id'].':'.$info['award_id']);
//                dd($res);die;
                return Common::jsonFormat('200','投票成功');
            }
            return Common::jsonFormat('500','投票失败');
        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','投票失败');
        }
    }

    /**
     * @param CandidateVoteListRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 候选人投票列表
     */
    public function candidateVoteList(CandidateVoteListRequest $request)
    {
        $info = $request->all();
        $sort = (isset($info['is_pc']) && $info['is_pc'] == 'true') ? 'score' : 'public_votes';
        $vote = VoteModel::where('award_id',$info['award_id'])->orderBy($sort,'desc')->get();

        $list = [];
        if ($vote){
            $h5where = [];
            if (isset($info['doctor_name']) && !is_null($info['doctor_name'])){
                $h5where[] = ['name','like', '%'.$info['doctor_name'].'%'];
            }
            foreach ($vote as $k => $v){
                $doctor_info = UserModel::where(['id' => $v['candidate_id']])->first()->signUpInfo()->where(['wanted_award' => $info['award_id']])->where($h5where)->first();

                //如果是pc端，搜索条件传入了省份，则要筛选
                if (isset($info['is_pc']) && $info['is_pc'] == 'true'){
                    if (isset($info['province']) && !is_null($info['province'])){
                        //根据省份id的前三位匹配出医院表中，地区id前三位相匹配的医院id
                        $hospital = HospitalModel::where('district_id','like',substr($info['province'],0,3).'%')->get(['id']);
                        //如果没查到医院，就返回空
                        if ($hospital){
                            $doctor_info = UserModel::where(['id' => $v['candidate_id']])->first()->signUpInfo()->where(['wanted_award' => $info['award_id']])->whereIn('hospital_id',$hospital)->first();
                            Log::info($doctor_info);
                        }else{
                            $doctor_info = [];
                        }
                    }else{
                        $doctor_info = UserModel::where(['id' => $v['candidate_id']])->first()->signUpInfo()->where(['wanted_award' => $info['award_id']])->first();
                    }
                }

                //没查到相关信息，就跳出循环，不计入此条记录
                if (!$doctor_info){
                    continue;
                }
                $list[$k] = $doctor_info;
                $list[$k]['votes'] = $v['public_votes'];
            }
        }
        return Common::jsonFormat('200','获取成功',$list);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 专家奖项列表及剩余票数
     */
    public function expertAwardListWithVotes(Request $request)
    {
        $doctor_class = new DoctorController();
        $award_list = $doctor_class->configAward();

        $cookie_user_token = $request->cookie('user_token');
        $voters = UserModel::where('access_token',$cookie_user_token)->first();
        if (!$voters){
            return Common::jsonFormat('500','用户信息不正确');
        }

//        $voters = UserModel::where('access_token','4ecdfcbe1dcf482eb9bf5e1a0a761091')->first();
//        $info['award_id'] = 101;

        $check_expert = $this->checkExpert($voters['phone_number']);
        if ($check_expert != 'pass'){ // pass 代表专家
            return Common::jsonFormat('500','您没有权限');
        }

        //遍历奖项列表 ， 往每条记录中插入剩余票数
        foreach ($award_list as &$v){
            $count_votes = VoteRelationModel::where(['voters_id' => $voters['id'], 'award_id' => $v['id']])->count();
            $v['votes'] = 10 - $count_votes; //剩余票数
        }

        return Common::jsonFormat('200','获取成功',$award_list);
    }


    /**
     * @param $phone_number
     * @return string
     * 检查传入的手机号是不是有效的专家，有效包括是专家身份，且未被删除
     */
    public function checkExpert($phone_number)
    {
        $manager = new ManagerController();

        $expert_list_request = new ExpertListRequest();
        $expert_list = $manager->expertList($expert_list_request)->content();
        $expert_list = json_decode($expert_list,true)['data']['data'];

        foreach ($expert_list as $v){
            if ($phone_number == $v['phone_number']){
                return 'pass';
            }
        }
        return 'reject';
    }
}
