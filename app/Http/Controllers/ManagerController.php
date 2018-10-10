<?php

namespace App\Http\Controllers;

use App\Http\Requests\Manager\ExpertAddRequest;
use App\Http\Requests\Manager\ExpertDeleteRequest;
use App\Http\Requests\Manager\ExpertEditRequest;
use App\Http\Requests\Manager\ExpertListRequest;
use App\Http\Requests\Manager\ManagerAddRequest;
use App\Http\Requests\Manager\ManagerDeleteRequest;
use App\Http\Requests\Manager\ManagerDetailRequest;
use App\Http\Requests\Manager\ManagerEditRequest;
use App\Http\Requests\Manager\ManagerListRequest;
use App\Http\Requests\Manager\ManagerLoginRequest;
use App\Http\Requests\Manager\ManagerLogoutRequest;
use App\Http\Requests\Manager\SignUpInfoReviewListRequest;
use App\Http\Requests\Manager\SignUpInfoReviewRequest;
use App\Http\Requests\Manager\TimeSettingRequest;
use App\Models\DoctorSignUp\DoctorModel;
use App\Models\Manager\ExpertModel;
use App\Models\Manager\ManagerModel;
use App\Models\Manager\SettingModel;
use App\Models\Manager\SignUpInfoReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Psy\TabCompletion\Matcher\CommandsMatcher;

class ManagerController extends Controller
{
    /**
     * @param ManagerListRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 管理员列表
     */
    public function managerList(ManagerListRequest $request)
    {
        $info = $request->all();

        $manager_query = ManagerModel::query();

        if (!is_null($info['nickname'])){
            $manager_query->where('nickname','like','%'.$info['nickname'].'%');
        }
        if (!is_null($info['account'])){
            $manager_query->where('account',$info['account']);
        }
        if (!is_null($info['role'])){
            $manager_query->where('role',$info['role']);
        }

        $limit = !is_null($info['length']) ? $info['length'] : 10;
        $offset = !is_null($info['cur_page']) ? ($info['cur_page']-1)*$limit : 0;
        $total = $manager_query->count();

        $res = $manager_query->offset($offset)->limit($limit)->orderBy('created_at','desc')->get(['id','nickname','account','role','note']);

        $data = ['total' => $total, 'data' => $res];

        return Common::jsonFormat('200','获取成功',$data);
    }

    /**
     * @param ManagerAddRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 添加管理员
     */
    public function managerAdd(ManagerAddRequest $request)
    {
        $info = $request->all();

        try{
            $check = ManagerModel::where('nickname',$info['nickname'])->orWhere('account',$info['account'])->first();

            if ($check){
                return Common::jsonFormat('500','此管理员已经存在哟');
            }

            $manager = new ManagerModel();
            $manager->nickname = $info['nickname'];
            $manager->account = $info['account'];
            $manager->password = Common::mymd5_4($info['password']);
            $manager->role = $info['role'];
            $manager->note = $info['note'];
            $manager->save();

            return Common::jsonFormat('200','添加成功');

        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','添加失败');
        }
    }

    /**
     * @param ManagerEditRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 管理员编辑
     */
    public function managerEdit(ManagerEditRequest $request)
    {
        $info = $request->all();
        try{
            $manager = ManagerModel::find($info['id']);
            if (!$manager){
                return Common::jsonFormat('500','此管理员不存在哟');
            }

            $result = ManagerModel::query()->where('account',$info['account'])->where('id','!=',$info['id'])->first();
            $aa = $result->toArray();
            \Log::info($aa);
            if ($result){
                \Log::info('ok');
                return Common::jsonFormat('500','该管理员账号已存在');
            }else{
                \Log::info('no');
            }

            if (isset($info['nickname']) && !is_null($info['nickname'])){
                $manager->nickname = $info['nickname'];
            }
            if (isset($info['account']) && !is_null($info['account'])){
                $manager->account = $info['account'];
            }
            if (isset($info['password']) && !is_null($info['password'])){
                $manager->password = Common::mymd5_4($info['password']);
            }
            if (isset($info['role']) && !is_null($info['role'])){
                $manager->role = $info['role'];
            }
            if (isset($info['note']) && !is_null($info['note'])){
                $manager->note = $info['note'];
            }
            $manager->save();
            return Common::jsonFormat('200','修改成功');
        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','修改失败');
        }
    }

    /**
     * @param ManagerDetailRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 管理员详情
     */
    public function managerDetail(ManagerDetailRequest $request)
    {
        $info = $request->all();

        $manager = ManagerModel::find($info['id'])->toArray();
        unset($manager['password']);
        unset($manager['access_token']);
        if (!$manager){
            return Common::jsonFormat('500','此管理员不存在哟');
        }
        return Common::jsonFormat('200','获取成功',$manager);
    }

    /**
     * @param ManagerDeleteRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 管理员删除
     */
    public function managerDelete(ManagerDeleteRequest $request)
    {
        $info = $request->all();

        try{
            foreach ($info['id'] as $v){
                $check = ManagerModel::find($v);
                if (!$check){
                    return Common::jsonFormat('500','删除失败，部分管理员不存在哟');
                }
            }
            $res = ManagerModel::whereIn('id',$info['id'])->delete();

            return Common::jsonFormat('200','删除成功');
        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','删除失败');
        }
    }

    /**
     * @param ManagerLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 管理员登录
     */
    public function managerLogin(ManagerLoginRequest $request)
    {
        $info = $request->all();
        $account = $info['account'];
        $password = Common::mymd5_4($info['password']);

        try{
            $manager = ManagerModel::where([['nickname' , $account], ['password' , $password]])->orWhere([['account' , $account], ['password' , $password]])->first();

            if (!$manager){
                return Common::jsonFormat('500','用户名或密码不正确');
            }

            //更新token
            $manager_token = $manager->access_token = Common::createSessionKey();
            $manager->save();

            //存入session
            $request->session()->put($manager_token,'manager_token'.$manager['id']);

            $data['nickname'] = $manager['nickname'];
            $data['role'] = $manager['role'];
            $data['manager_token'] = $manager_token;

            return Common::jsonFormat('200','登录成功',$data);
        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','登录失败');
        }
    }

    /**
     * @param ManagerLogoutRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 管理员退出登录
     */
    public function managerLogout(ManagerLogoutRequest $request)
    {
        $info = $request->all();

        try {

            $manager = ManagerModel::where('access_token', $info['manager_token'])->first();
            $session_token = $request->session()->get($info['manager_token']);
            $session_token = substr($session_token, 13);

            if (!$manager || $manager['id'] != $session_token) {
                return Common::jsonFormat('500', '用户信息不正确');
            }

            //清空其session中的token
            $request->session()->forget($info['manager_token']);

            //把数据表中access_token置空
            $manager->access_token = '';
            $manager->save();

            return Common::jsonFormat('200', '退出成功');
        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','退出失败');
        }
    }

    /**
     * @param TimeSettingRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 后台时间限制相关设置
     */
    public function timeSetting(TimeSettingRequest $request)
    {
        $info = $request->all();

        try{
            $setting = SettingModel::where('name','time_limit')->first();

            if ($setting){ //如果查找到了，进行更新操作
                $setting->value = $info['time_limit'];
                $setting->save();

                return Common::jsonFormat('200','设置成功');
            }else{ // 未查到，进行插入操作
                $setting = new SettingModel();
                $setting->name = 'time_limit';
                $setting->value = $info['time_limit'];
                $setting->save();

                return Common::jsonFormat('200','设置成功');
            }

        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','设置失败');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 时间限制列表
     */
    public function timeSettingList(Request $request)
    {
        $setting = SettingModel::where('name','time_limit')->first();
        $data = json_decode($setting['value'],true);

        $cur_time = time();
        $sign_up_start_time = strtotime($data['sign_up_time']['start']);
        $sign_up_end_time = strtotime($data['sign_up_time']['end']);
        $vote_start_time = strtotime($data['vote_time']['start']);
        $vote_end_time = strtotime($data['vote_time']['end']);


        if ($cur_time >= $sign_up_start_time && $cur_time < $sign_up_end_time){
            $data['can_sign_up'] = 1; //可报名
        }elseif ($cur_time < $sign_up_start_time){
            $data['can_sign_up'] = 2; //未开始
        }else{
            $data['can_sign_up'] = 3; //已过期
        }

        if ($cur_time >= $vote_start_time && $cur_time < $vote_end_time){
            $data['can_vote'] = 1; //可投票
        }elseif ($cur_time < $vote_start_time){
            $data['can_vote'] = 2; //未开始
        }else{
            $data['can_vote'] = 3; //已结束
        }

        return Common::jsonFormat('200','获取成功',$data);
    }

    /**
     * @param ExpertListRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 专家列表
     */
    public function expertList(ExpertListRequest $request)
    {
        $info = $request->all();

        $expert = ExpertModel::query();
        $expert->where('status',1);
        if (isset($info['phone_number']) && !is_null($info['phone_number'])){
            $expert->where('phone_number', $info['phone_number']);
        }
        if (isset($info['name']) && !is_null($info['name'])){
            $expert->where('name', $info['name']);
        }

        $total = $expert->count();
        $limit = isset($info['length']) && !is_null($info['length']) ? $info['length'] : 10;
        $offset = isset($info['cur_page']) && !is_null($info['cur_page']) ? ($info['cur_page']-1)*$limit : 0;

        $res = $expert->offset($offset)->limit($limit)->orderBy('created_at','desc')->get();

        $data = ['total'=>$total, 'data'=>$res];

        return Common::jsonFormat('200','获取成功',$data);
    }

    /**
     * @param ExpertAddRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 专家添加
     */
    public function expertAdd(ExpertAddRequest $request)
    {
        $info = $request->all();

        try{
            $check = ExpertModel::where('phone_number', $info['phone_number'])->orWhere('name',$info['name'])->first();
            if ($check){
                return Common::jsonFormat('500','此专家已经存在哟');
            }

            $expert = new ExpertModel();
            $expert->phone_number = $info['phone_number'];
            $expert->name = $info['name'];
            $expert->status = 1; //默认1，有效
            $expert->save();

            return Common::jsonFormat('200','添加成功');

        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','添加失败');
        }
    }

    /**
     * @param ExpertEditRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 专家编辑
     */
    public function exportEdit(ExpertEditRequest $request)
    {
        $info = $request->all();
        try{
            $expert = ExpertModel::find($info['id']);
            if (!$expert){
                return Common::jsonFormat('500','该专家不存在哟');
            }

            $result = ExpertModel::query()->where('phone_number',$info['phone_number'])->first();
            if ($result){
                return Common::jsonFormat('500','该手机号已注册');
            }

            if (isset($info['phone_number']) && !is_null($info['phone_number'])){
                $expert->phone_number = $info['phone_number'];
            }
            if (isset($info['name']) && !is_null($info['name'])){
                $expert->name = $info['name'];
            }
            $expert->save();
            return Common::jsonFormat('200','修改成功');
        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','修改失败');
        }
    }

    /**
     * @param ExpertDeleteRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 专家删除
     */
    public function expertDelete(ExpertDeleteRequest $request)
    {
        $info = $request->all();

        try{
            $expert = ExpertModel::find($info['id']);
            if (!$expert){
                return Common::jsonFormat('500','此专家不存在哟');
            }
            $expert->status = 0; // 此处为软删除，0即为删除
            $expert->save();

            return Common::jsonFormat('200','删除成功');
        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','删除失败');
        }
    }

    /**
     * @param ExpertDeleteRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 专家详情
     */
    public function expertDetail(ExpertDeleteRequest $request)
    {
        $info = $request->all();

        $expert = ExpertModel::find($info['id']);
        if (!$expert){
            return Common::jsonFormat('500','此专家不存在哟');
        }

        return Common::jsonFormat('200','获取成功',$expert);
    }

    /**
     * @param SignUpInfoReviewListRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 审核内容列表
     */
    public function signUpInfoReviewList(SignUpInfoReviewListRequest $request)
    {
        $info = $request->all();

        $review = SignUpInfoReview::where('info_id',$info['info_id'])->orderBy('created_at','desc')->get();
        foreach ($review as &$v){
            $v['operate_person'] = $v->manager['nickname'];
        }

        return Common::jsonFormat('200','获取成功',$review);
    }

    /**
     * @param SignUpInfoReviewRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 报名审核
     */
    public function signUpInfoReview(SignUpInfoReviewRequest $request)
    {
        $info = $request->all();

        try{

            $cookie_manager_token = $request->cookie('manager_token');
            $session_manager_id = $request->session()->get($cookie_manager_token);
            DB::transaction(function () use ($info,$session_manager_id) {

                foreach ($info['info_id'] as $v){
                    //在报名信息审核表 中新增数据
                    $review = new SignUpInfoReview();
                    $review->user_id = substr($session_manager_id,13);
                    $review->info_id = $v;
                    $review->status = $info['status'];
                    $review->content = isset($info['content']) ? $info['content'] : '';
                    $review->review_way = $info['review_way'];
                    $res1 = $review->save();

                    //更新doctor表中的status值
                    $doctor = DoctorModel::find($v);
                    $doctor->status = $info['status'];
                    $res2 = $doctor->save();

                    //两张表都写入成功，发送短信
                    if ($info['status'] == 2 && $res1 & $res2){
                        $doctor_class = new DoctorController();
                        $content = $doctor_class->configAward($doctor['wanted_award']);
                        $sms_res = SmsController::sendNotice($doctor['phone_number'],$content);
                        Log::info('审核短信发送状态是：'.$sms_res);
                    }
                }

            });

            return Common::jsonFormat('200','审核成功');
        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','审核失败');
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 数据统计
     */
    public function statisticalGraph()
    {
        $today_date_time = date('Y-m-d H:i:s',strtotime('today'));
        $tomorrow_date_time = date('Y-m-d H:i:s',strtotime('tomorrow'));
        $cur_timestamp = time();

        $doctor = new DoctorModel();
        $doctor_count = $doctor->count(); //报名总数
        $doctor_check_pending_count = $doctor->where('status',1)->count(); //待审核总数
        $doctor_check_pass_count = $doctor->where('status',2)->count(); //已通过总数
        $doctor_check_reject_count = $doctor->where('status',3)->count(); //未通过总数
        $doctor_today_avg_count = $doctor->whereBetween('created_at',[$today_date_time,$tomorrow_date_time])->count(); //日平均报名数

        $data = [
            'doctor_count' => $doctor_count,
            'doctor_check_pending_count' => $doctor_check_pending_count,
            'doctor_check_pass_count' => $doctor_check_pass_count,
            'doctor_check_reject_count' => $doctor_check_reject_count,
            'doctor_today_avg_count' => $doctor_today_avg_count,
        ];

//        $doctor_day_avg_count = function ($date_range) use ($doctor) {
//            $doctor->whereBetween('created_at',[$date_range['start'],$date_range['end']])->get(); //日平均报名数
//        };
        

        return Common::jsonFormat('200','获取成功',$data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 测试环境 设置cookie
     * 为了登陆后启用其他接口使用
     */
    public function testSetCookie(Request $request)
    {
        return response('cookie设置成功')->cookie('manager_token',$request['manager_token']);
    }
}
