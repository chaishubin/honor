<?php

namespace App\Http\Controllers;

use App\Http\Requests\Manager\ManagerAddRequest;
use App\Http\Requests\Manager\ManagerDeleteRequest;
use App\Http\Requests\Manager\ManagerListRequest;
use App\Http\Requests\Manager\ManagerLoginRequest;
use App\Http\Requests\Manager\ManagerLogoutRequest;
use App\Http\Requests\Manager\TimeSettingRequest;
use App\Models\Manager\ManagerModel;
use App\Models\Manager\SettingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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

//            return response('manager')->cookie('manager_token',$manager_token,10);
            return Common::jsonFormat('200','登录成功');
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
        $start_time = strtotime($data['sign_up_time']['start']);
        $end_time = strtotime($data['sign_up_time']['end']);
        $cur_time = time();
        if ($cur_time > $start_time && $cur_time < $end_time){
            $data['can_sign_up'] = 1;
        }else{
            $data['can_sign_up'] = 0;
        }
        
        return Common::jsonFormat('200','获取成功',$data);

    }
}
