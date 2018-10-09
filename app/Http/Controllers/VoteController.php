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

            if ($voters_type == 1){ // 大众
                $vote_user_relation = VoteRelationModel::where('voters_id',$voters['id'])->first();
                if ($vote_user_relation){ // 若查询到记录，说明该用户已经至少投过一次票了，普通用户只能参与投票一次
                    return Common::jsonFormat('500','您只能投一票，您已经参与过投票了');
                }
            }else{ // 专家
                $count_vote = VoteRelationModel::where('voters_id',$voters['id'])->count();

                if ($count_vote >= 10){ // 专家在每个奖项有10次投票机会
                    return Common::jsonFormat('500','您在该奖项的投票次数已用完');
                }

                //专家对一个用户也只能投一票
                $expert_vote_relation = VoteRelationModel::where(['voters_id' => $voters['id'], 'candidate_id' => $info['candidate_id']])->first();

                if ($expert_vote_relation){
                    return Common::jsonFormat('500','您已经对此用户投过票了，不要重复投票');
                }
            }

            //将投票信息写入投票-选举人关系表中
            $vote_relation = new VoteRelationModel();
            $vote_relation->candidate_id = $info['candidate_id']; //候选人id，即doctor表中的id
            $vote_relation->voters_id = $voters['id']; //当前登陆用户，在user表中的id
            $vote_relation->award_id = $info['award_id'];
            $vote_relation->voters_type = $voters_type;
            $vote_relation->vote_time = time();
            $vote_relation->voters_ip = ip2long(Common::getClientIp());
            $res = $vote_relation->save();

            if ($res){
                //判断redis中是否存在此key 存在返回1，不存在返回0
                if (Redis::exists('rongyao2018:vote:'.$info['candidate_id'].':'.$info['award_id'])){ //存在,进行 更新操作
                    if ($voters_type == 2){ //专家
                        //专家票数加一
                        Redis::hincrby('rongyao2018:vote:'.$info['candidate_id'].':'.$info['award_id'],'expert_votes',1);
                    }else{ // 大众
                        //大众票数加一
                        Redis::hincrby('rongyao2018:vote:'.$info['candidate_id'].':'.$info['award_id'],'public_votes',1);
                    }
                }else{ //不存在，进行新增操作
                    $vote = VoteModel::where(['candidate_id' => $info['candidate_id'], 'award_id' => $info['award_id']])->first();
                    //此处是为了应对，redis中的数据丢失，但仍在表中备份的情况，此时把数据库中的数据再写入redis中
                    if ($vote){
                        //存入hash类型的redis
                        Redis::hmset('rongyao2018:vote:'.$info['candidate_id'].':'.$info['award_id'],['public_votes' => $vote['public_votes'], 'expert_votes' => $vote['expert_votes']]);
                    }else{
                        if ($voters_type == 2){ //专家
                            $public_votes = 0;
                            $expert_votes = 1;
                        }else{ // 大众
                            $public_votes = 1;
                            $expert_votes = 0;
                        }

                        //存入hash类型的redis
                        Redis::hmset('rongyao2018:vote:'.$info['candidate_id'].':'.$info['award_id'],['public_votes' => $public_votes, 'expert_votes' => $expert_votes]);
                    }

                }
//                $res = Redis::hgetall('rongyao2018:vote:'.$info['candidate_id'].':'.$info['award_id']);
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
     * 候选人投票列表 PC端的列表、H5端用户未登录的列表
     */
    public function candidateVoteList(CandidateVoteListRequest $request)
    {
        $info = $request->all();
        $sort = (isset($info['is_pc']) && $info['is_pc'] == 'true') ? 'score' : 'public_votes';

        $doctor = DoctorModel::query();
        $doctor->where(['status' => 2, 'wanted_award' => $info['award_id']]); // 报名状态为2，只取审核已通过的
        if (isset($info['doctor_name']) && !is_null($info['doctor_name'])){
            $doctor->where('name','like', '%'.$info['doctor_name'].'%');
        }

        //如果是pc端，搜索条件传入了省份，则要筛选
        if (isset($info['province']) && !is_null($info['province'])){
            //根据省份id的前三位匹配出医院表中，地区id前三位相匹配的医院id
            $hospital = HospitalModel::where('district_id', 'like', substr($info['province'], 0, 3) . '%')->get(['id']);

            $doctor->whereIn('hospital_id', $hospital)->first();
        }

        $result = [];
        $doctor->chunk(100, function($res) use (&$result) {
            $doctor_class = new DoctorController();
            //遍历把redis中的票数信息插入每条记录中
            foreach ($res as $k => $v){
                $public_votes = Redis::hget('rongyao2018:vote:'.$v['id'].':'.$v['wanted_award'],'public_votes');
                $expert_votes = Redis::hget('rongyao2018:vote:'.$v['id'].':'.$v['wanted_award'],'expert_votes');
                $score = $public_votes + ($expert_votes * 4);

                $result[$k]['id'] = $v['id'];
                $result[$k]['full_face_photo'] = $v['full_face_photo'];
                $result[$k]['name'] = $v['name'];
                $result[$k]['hospital_name'] = $v['hospital_name'];
                $result[$k]['department'] = $v['department'];

                //拼接号职称的 全称
                $job_title = json_decode($v['job_title'], true);
                $first = '';
                if (isset($job_title['first'])){
                    $first = $doctor_class->configJobTitle($job_title['first']);
                }
                $second = '';
                if (isset($job_title['second'])){
                    $second = ' · '.$doctor_class->configJobTitle($job_title['second']);
                }

                $result[$k]['job_title'] = $first.$second;
                $result[$k]['public_votes'] = $public_votes;
                $result[$k]['expert_votes'] = $expert_votes;
                $result[$k]['score'] = $score;
                //根据遍历记录中的医院id，查出对应的地区名称
                $hospital = HospitalModel::where('id', $v['hospital_id'])->first(['district_address']);
                //截取出地区名称中的省份
                $result[$k]['province'] = mb_substr($hospital['district_address'],3,2);
            }
        });

        //对数据按照public_votes或score
        $sort_field = array_column($result,$sort);
        array_multisort($sort_field,SORT_DESC,$result);

        $limit = (isset($info['length']) && !is_null($info['length'])) ? $info['length'] : 3;
        $offset = (isset($info['cur_page']) && !is_null($info['cur_page'])) ? ($info['cur_page']-1)*$limit : 0;

        $data['total'] = count($result);
        $data['data'] = array_slice($result,0,5);
        $data['params'] = $result;
        //$data = ['total' => count($result), 'data' => array_slice($result,$offset,$limit)];
        return Common::jsonFormat('200', '获取成功',$data);
    }

    /**
     * @param CandidateVoteListRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 候选人投票列表 H5端已登录用户的列表
     */
    public function loginedCandidateVoteList(CandidateVoteListRequest $request)
    {
        $info = $request->all();

        $cookie_user_token = $request->cookie('user_token');
        $voters = UserModel::where('access_token',$cookie_user_token)->first();
        if (!$voters){
            return Common::jsonFormat('500','用户信息不正确');
        }

        //闭包函数，用来检测当前登录用户是否已经对医生投过票
        $check_is_voted = function($candidate_id) use ($voters)
        {
            $vote_relation = VoteRelationModel::where(['voters_id' => $voters['id'], 'candidate_id' => $candidate_id])->first();
            if ($vote_relation){
                return true;
            }else{
                return false;
            }
        };


        $doctor = DoctorModel::query();
        $doctor->where(['status' => 2, 'wanted_award' => $info['award_id']]); // 报名状态为2，只取审核已通过的
        if (isset($info['doctor_name']) && !is_null($info['doctor_name'])){
            $doctor->where('name','like', '%'.$info['doctor_name'].'%');
        }

        $result = [];
        $doctor->chunk(100, function($res) use (&$result,$check_is_voted) {
            $doctor_class = new DoctorController();
            //遍历把redis中的票数信息插入每条记录中
            foreach ($res as $k => $v){
                $public_votes = Redis::hget('rongyao2018:vote:'.$v['id'].':'.$v['wanted_award'],'public_votes');

                $result[$k]['id'] = $v['id'];
                $result[$k]['full_face_photo'] = $v['full_face_photo'];
                $result[$k]['name'] = $v['name'];
                $result[$k]['hospital_name'] = $v['hospital_name'];
                $result[$k]['department'] = $v['department'];

                //拼接号职称的 全称
                $job_title = json_decode($v['job_title'], true);
                $first = $doctor_class->configJobTitle($job_title['first']);
                $second = '';
                if ($job_title['second']){
                    $second = ' · '.$doctor_class->configJobTitle($job_title['second']);
                }

                $result[$k]['job_title'] = $first.$second;
                $result[$k]['public_votes'] = $public_votes;
                $result[$k]['is_voted'] = $check_is_voted($v['id']);
            }
        });

        //对数据按照public_votes或score
        $sort_field = array_column($result,'public_votes');
        array_multisort($sort_field,SORT_DESC,$result);

        $limit = (isset($info['length']) && !is_null($info['length'])) ? $info['length'] : 10;
        $offset = (isset($info['cur_page']) && !is_null($info['cur_page'])) ? ($info['cur_page']-1)*$limit : 0;

        $data = ['total' => count($result), 'data' => array_slice($result,$offset,$limit)];

        return Common::jsonFormat('200', '获取成功',$data);
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
