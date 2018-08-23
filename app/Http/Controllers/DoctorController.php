<?php

namespace App\Http\Controllers;

use App\Http\Requests\Doctor\SignUpInfoDetailRequest;
use App\Http\Requests\Doctor\SignUpInfoEditRequest;
use App\Http\Requests\Doctor\SignUpListRequest;
use App\Http\Requests\Doctor\SignUpRequest;
use App\Http\Requests\Doctor\UserAwardListRequest;
use App\Http\Requests\Doctor\UserLoginRequest;
use App\Http\Requests\Manager\SignUpInfoReviewListRequest;
use App\Http\Requests\Manager\SignUpInfoReviewRequest;
use App\Models\DoctorSignUp\DoctorModel;
use App\Models\DoctorSignUp\HospitalModel;
use App\Models\DoctorSignUp\UserModel;
use App\Models\Manager\SignUpInfoReview;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Session\Session;

class DoctorController extends Controller
{
    /**
     * @param SignUpListRequest $request
     * @return \Illuminate\Http\JsonResponse
     * PC报名信息列表
     */
    public function signUpList(SignUpListRequest $request)
    {
        $info = $request->all();

        $signup_query = DoctorModel::query();

        if (isset($info['name']) && $info['name']){
            $signup_query->where('name',$info['name']);
        }
        if (isset($info['job_title']) && !is_null($info['job_title'])){
            $signup_query->where('job_title',$info['job_title']);
        }
        if (isset($info['hospital_name']) && !is_null($info['hospital_name'])){
            $signup_query->where('hospital_name','like','%'.$info['hospital_name'].'%');
        }
        if (isset($info['department']) && $info['department']){
            $signup_query->where('department','like','%'.$info['department'].'%');
        }
        if (isset($info['status']) && !is_null($info['status'])){
            $signup_query->where('status',$info['status']);
        }

        $total = $signup_query->count();

        $limit = (isset($info['length']) && !is_null($info['length'])) ? $info['length'] : 10;
        $offset = (isset($info['cur_page']) && !is_null($info['cur_page'])) ? ($info['cur_page']-1)*$limit : 0;

        $sign_up = $signup_query->offset($offset)->limit($limit)->get();

        foreach ($sign_up as &$v){
            $v['job_title'] = $this->configJobTitle($v['job_title']);
            $v['wanted_award'] = $this->configAward($v['wanted_award']);
            $v['doctor_other_info'] = json_decode($v['doctor_other_info'],true);
        }

        $data = ['total'=>$total,'data'=>$sign_up];

        return Common::jsonFormat('200','获取成功',$data);

    }

    public function userSignUpList(Request $request)
    {
        $signup_query = DoctorModel::where('phone_number',$request['phone_number'])
            ->orderBy('updated_at','desc')
            ->get(['id','wanted_award','status']);

//        foreach ($signup_query as &$v){
//            $v['wanted_award'] =
//        }
    }

    /**
     * @param SignUpRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 荣耀医者 报名注册
     */
    public function signUp(SignUpRequest $request)
    {
        $info = $request->all();

        try{
            $check = DoctorModel::where('phone_number',$info['phone_number'])->first();
            if ($check){
                return Common::jsonFormat('500','此用户已参加报名');
            }

            $doctor = new DoctorModel();
            $doctor->name = $info['name'];
            $doctor->sex = $info['sex'];
            $doctor->age = $info['age'];
            $doctor->wanted_award = $info['wanted_award'];
            $doctor->working_year = $info['working_year'];
            $doctor->hospital_id = $info['hospital_id'];
            $doctor->hospital_name = $info['hospital_name'];
            $doctor->department = $info['department'];
            $doctor->job_title = $info['job_title'];
            $doctor->phone_number = $info['phone_number'];
            $doctor->medical_certificate_no = $info['medical_certificate_no'];
            $doctor->email = $info['email'];
            $doctor->full_face_photo = $info['full_face_photo'];

            $doctor->doctor_other_info = json_encode($info['doctor_other_info']);

            $doctor->status = 1; //初始，待审核状态-----1待审核，2已通过，3未通过
            $doctor->save();

            return Common::jsonFormat('200','报名成功');

        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','报名失败');
        }
    }

    /**
     * @param SignUpInfoEditRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 荣耀医者 报名信息修改
     */
    public function signUpInfoEdit(SignUpInfoEditRequest $request)
    {
        $info = $request->all();

        try{
            $check = DoctorModel::find($info['id']);
            if (!$check){
                return Common::jsonFormat('500','这条信息不存在哟');
            }

            //报名信息只有待审核1，审核不通过3时，才允许修改；审核通过2是不允许修改的
            if ($check['status'] == 2){
                return Common::jsonFormat('500','不允许修改哟');
            }
            $sign_up = $check;

            if ($info['name']){
                $sign_up->name = $info['name'];
            }
            if (!is_null($info['sex'])){
                $sign_up->sex = $info['sex'];
            }
            if (!is_null($info['age'])){
                $sign_up->age = $info['age'];
            }
            if ($info['wanted_award']){
                $sign_up->wanted_award = $info['wanted_award'];
            }
            if (!is_null($info['working_year'])){
                $sign_up->working_year = $info['working_year'];
            }
            if (!is_null($info['hospital_id'])){
                $sign_up->hospital_id = $info['hospital_id'];
            }
            if ($info['hospital_name']){
                $sign_up->hospital_name = $info['hospital_name'];
            }
            if ($info['department']){
                $sign_up->department = $info['department'];
            }
            if ($info['job_title']){
                $sign_up->job_title = $info['job_title'];
            }
            if ($info['phone_number']){
                $sign_up->phone_number = $info['phone_number'];
            }
            if ($info['medical_certificate_no']){
                $sign_up->medical_certificate_no = $info['medical_certificate_no'];
            }
            if ($info['email']){
                $sign_up->email = $info['email'];
            }
            if ($info['full_face_photo']){
                $sign_up->full_face_photo = $info['full_face_photo'];
            }

            if (!is_null($info['doctor_other_info'])){
                foreach ($info['doctor_other_info'] as $k => $v){
                    //如果传入的字段与记录中json中的字段不符，报错终止执行
                    if (!isset($sign_up->doctor_other_info->$k)){
                        return Common::jsonFormat('500','服务器内部错误');
                    }
                    $sign_up->doctor_other_info->$k = $v;
                }
            }

            $sign_up->save();

            return Common::jsonFormat('200','修改成功');

        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','修改失败');
        }
    }

    /**
     * @param SignUpInfoDetailRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 荣耀医者 报名信息详情
     */
    public function signUpInfoDetail(SignUpInfoDetailRequest $request)
    {
        $info = $request->all();

        $check = DoctorModel::find($info['id']);
        if (!$check){
            return Common::jsonFormat('500','此条信息不存在哟');
        }

        $data = $check;
        $data['doctor_other_info'] = json_decode($data['doctor_other_info'],true);

        return Common::jsonFormat('200','获取成功',$check);
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
            $review = new SignUpInfoReview();
            $review->user_id = 999;
            $review->info_id = $info['info_id'];
            $review->status = $info['status'];
            $review->content = $info['content'];
            $review->review_way = $info['review_way'];
            $review->save();

            return Common::jsonFormat('200','审核成功');
        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','审核失败');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 用户注册、登录、发送短信验证码
     * 注意：客户端登录成功之后，需要客户端把user_token存入cookie中
     */
    public function userLogin(UserLoginRequest $request)
    {
        $info = $request->all();

        //获取session中的captcha
        $session_captcha = $request->session()->get('captcha'.$info['phone_number'],'captcha');


        if (!$request->session()->has('sms_flag'.$info['phone_number'])){
            $request->session()->put('sms_flag'.$info['phone_number'],'false');
        }

        $flag = $request->session()->get('sms_flag'.$info['phone_number']);

        if ($session_captcha != $info['pic_code']){
            return Common::jsonFormat('500','请输入正确的验证码哟');
        }else{
            if ($info['sms_code'] == ''){
                //发送短信验证码
                $res = SmsController::sendMessage($request, $info['phone_number']);
                if ($res){
                    $request->session()->put('sms_flag'.$info['phone_number'],'true');
                    return Common::jsonFormat('200','短信验证码发送成功');
                }else{
                    return Common::jsonFormat('500','短信验证码发送失败');
                }
            }else{
                if ($flag === 'true'){
                    //获取session中的sms_code
                    $session_sms_code = $request->session()->get('sms_code'.$info['phone_number']);
                    //对比短信验证码是否正确
                    if ($info['sms_code'] == $session_sms_code){

                        //用户信息写入表
                        $check = UserModel::where('phone_number',$info['phone_number'])->first();

                        //注册--新增
                        if (!$check){
                            try{
                                $user = new UserModel();
                                $user->phone_number = $info['phone_number'];
                                $user_token = $user->access_token = Common::createSessionKey();
                                $user->reg_time = time();
                                $user->status = 1;//默认启用
                                $user->save();

                                //把access_token 存入session
                                $request->session()->put($user_token,$info['phone_number']);

                                //通过图片验证码之后就清除其session，防止在下一次http请求仍然生效
                                $request->session()->forget('captcha'.$info['phone_number']);

                                return Common::jsonFormat('200','注册成功',$user_token);
                            } catch (\Exception $e){
                                Log::error($e);

                                //通过图片验证码之后就清除其session，防止在下一次http请求仍然生效
                                $request->session()->forget('captcha'.$info['phone_number']);

                                return Common::jsonFormat('500','注册失败');
                            }
                        }else{ //登录--更新
                            try{
                                $user = $check;
                                $user_token = $user->access_token = Common::createSessionKey();
                                $user->save();

                                //把access_token 存入session
                                $request->session()->put($user_token,$info['phone_number']);

                                //通过图片验证码之后就清除其session，防止在下一次http请求仍然生效
                                $request->session()->forget('captcha'.$info['phone_number']);

                                return Common::jsonFormat('200','登录成功',$user_token);
                            } catch (\Exception $e){
                                Log::error($e);
                                //通过图片验证码之后就清除其session，防止在下一次http请求仍然生效
                                $request->session()->forget('captcha'.$info['phone_number']);

                                return Common::jsonFormat('500','登录失败');
                            }
                        }

                    }else{
                        //通过图片验证码之后就清除其session，防止在下一次http请求仍然生效
                        $request->session()->forget('captcha'.$info['phone_number']);

                        return Common::jsonFormat('500','短信验证码不正确');
                    }
                }else{
                    //通过图片验证码之后就清除其session，防止在下一次http请求仍然生效
                    $request->session()->forget('captcha'.$info['phone_number']);

                    return Common::jsonFormat('500','请先获取验证码');
                }
            }
        }
    }


    /**
     * @param UserAwardListRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 用户退出登录
     */
    public function userLogout(UserAwardListRequest $request)
    {
        $info = $request->all();

        $check = UserModel::where('phone_number',$info['phone_number'])->first();
        $session_token = $request->session()->get($check['access_token']);

        if (!$check || $check['access_token'] != $info['user_token'] || $session_token != $check['phone_number']){
            return Common::jsonFormat('500','用户信息不正确');
        }


        try{
            $user = $check;
            //把用户的user_token清空
            $user->access_token = '';
            $user->save();

            //清空session中的 user_token , sms_flag , sms_code
            $request->session()->forget([$check['access_token'],'sms_flag','sms_code'.$info['phone_number']]);

            return Common::jsonFormat('200','退出成功');
        } catch (\Exception $e){
            Log::error($e);
            return Common::jsonFormat('500','退出失败');
        }

    }



    /**
     * @return \Illuminate\Http\JsonResponse|string
     * 用户已报名、未报名奖项列表
     */
    public function userAwardList(UserAwardListRequest $request)
    {
        $info = $request->all();

        $doctor = DoctorModel::where('phone_number',$info['phone_number'])->get(['wanted_award']);

        //把所有奖项列表赋给此变量
        $all = $this->configAward();

        $all_ids = [];
        foreach ($all as &$v){
            $all_ids[] = $v['id'];
        }

        $registed = [];
        $registed_not = $all;
        //遍历这个医生所有已报名的记录
        foreach ($doctor as &$v){
            //如果查到记录中奖项的id不在奖项配置文件列表的id中，则跳出循环
            if (!in_array($v['wanted_award'],$all_ids)){
                continue;
            }

            //取医生已报名列表中的一条记录，去遍历奖项配置文件，通过id对比两边奖项是否匹配
            //   匹配的话，就插入新的已报名数组中，并且从一个所有奖项的数组中剔除这条记录
            foreach ($all as $ak => $av){
                if ($v['wanted_award'] == $av['id']){
                    $registed[$ak]['id']=$av['id'];
                    $registed[$ak]['name']=$av['name'];

                    unset($registed_not[$ak]);
                }
            }
        }

        $data[] = ['registed'=>$registed,'registed_not'=>$registed_not];

        return Common::jsonFormat('200','获取成功',$data);
    }

    /**
     * @return array|string
     * 荣耀医生奖项配置
     */
    public function configAward($id='')
    {

        $info = [
            ['id' => '101', 'name' => '人文情怀奖'],
            ['id' => '102', 'name' => '中华医药贡献奖'],
            ['id' => '103', 'name' => '美丽天使奖'],
            ['id' => '104', 'name' => '金牌团队奖'],
            ['id' => '105', 'name' => '青年创新奖'],
            ['id' => '106', 'name' => '科普影响力'],
            ['id' => '107', 'name' => '金柳叶刀奖'],
            ['id' => '108', 'name' => '基层好医生奖'],
            ['id' => '109', 'name' => '专科精英奖-医疗服务创新奖'],
            ['id' => '110', 'name' => '专科精英奖-女性'],
            ['id' => '111', 'name' => '专科精英奖-口腔卫生'],
            ['id' => '112', 'name' => '专科精英奖-妇幼'],
            ['id' => '113', 'name' => '最受老百姓欢迎医生奖']
        ];

        //根据id返回具体的name值
        if ($id){
            foreach ($info as $v){
                if ($id == $v['id']){
                    return $v['name'];
                }
            }
            return '';
        }

        return $info;
    }

    /**
     * @return array|string
     * 荣耀医生职称配置
     */
    public function configJobTitle($id='')
    {
        $info = [
            ['id' => '101', 'name' => '主任医师'],
            ['id' => '102', 'name' => '副主任医师'],
            ['id' => '103', 'name' => '主治医师'],
            ['id' => '104', 'name' => '主管护师'],
            ['id' => '105', 'name' => '副主任护师'],
            ['id' => '106', 'name' => '主任护师'],
            ['id' => '107', 'name' => '教授'],
            ['id' => '108', 'name' => '副教授']
        ];

        //根据id返回具体的name值
        if ($id){
            foreach ($info as $v){
                if ($id == $v['id']){
                    return $v['name'];
                }
            }
            return '';
        }

        return $info;
    }

    public function testHospital(Request $request)
    {
        $info = $request->all();

        if (!$info['district_name']){
            return false;
        }

        try{
            $tdistrict = DB::table('tdistrict')->where('district_name','like','%'.$info['district_name'].'%')->limit(100)->get();

            $res = json_decode(json_encode($tdistrict,256),true);

//            var_dump($res);die;


            return Common::jsonFormat('200','success',$res);


        } catch (\Exception $e){
            Log::error($e);
            return 'error'.$e;
        }
    }


}
