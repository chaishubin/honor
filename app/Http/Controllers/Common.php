<?php

namespace App\Http\Controllers;

use Gregwar\Captcha\CaptchaBuilder;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class Common
 * @package App\Http\Controllers
 * 公共方法类
 */
class Common
{
    /**
     * @param $status
     * @param $msg
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     * 格式化响应json格式的方法
     */
    public static function jsonFormat($status,$msg,$data='')
    {
        if (!$data && $data !== []){
            return response()->json([
                'status' => $status,
                'msg' => $msg,
            ]);
        }

        return response()->json([
            'status' => $status,
            'msg' => $msg,
            'data' => $data
        ]);
    }


    /**
     * @return string
     * 创建20位bigint的id
     */
    public static function createBigIntId()
    {
        $id = time().str_pad(rand(1,999),3,0).str_pad(rand(1,99),2,0);
        return $id;
    }


    /**
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     * 上传图片
     */
    public function uploadImg(Request $request)
    {
        if ($request->isMethod('POST')){
            $file = $request->file('file');
            if (count($file) > 1){ //多图上传
                $url = [];
                foreach ($file as $k => $v){
                    if ($v->isValid()){
                        $realPath = $v->getRealPath();
                        $tmpName = $v->getFileName();
                        $extName = $v->getClientOriginalExtension();

                        $fdsk = new Fdfs();
                        $url[] = $fdsk->upload($realPath,$extName);

//                        $filename = date('Y-m-d-H-i-s').'-'.uniqid().'.'.$extName;
//                        $url[] = $v->storeAs('uploads',$filename);

                    }else{
                        return Common::jsonFormat('500','图片上传出错,请重试！');
                    }
                }
                if($url && count($url) == count($file)) {
                    $img_url = [];
                    foreach ($url as $v){
                        $img_url[] = $v['url'];
                    }
                    return Common::jsonFormat('200','上传成功',$img_url);
//                    return Common::jsonFormat('200','上传成功',$url); //调试使用，若使用laravel本身的storeAs方法，其上传成功之后直接返回url，不同于fdfs返回一个包含url的数组
                } else {
                    return Common::jsonFormat('500','上传失败');
                }

            }else{ //单图上传
                $file = $file[0];
                if ($file->isValid()){
                    $realPath = $file->getRealPath();
                    $tmpName = $file->getFileName();
                    $extName = $file->getClientOriginalExtension();

                    $fdsk = new Fdfs();
                    $url = $fdsk->upload($realPath,$extName);

//                    $filename = date('Y-m-d-H-i-s').'-'.uniqid().'.'.$extName;
//                    $url = $file->storeAs('uploads',$filename);

                    if($url) {
                        return Common::jsonFormat('200','上传成功',$url['url']);
//                        return Common::jsonFormat('200','上传成功',$url); //调试使用，若使用laravel本身的storeAs方法，其上传成功之后直接返回url，不同于fdfs返回一个包含url的数组
                    } else {
                        return Common::jsonFormat('500','上传失败');
                    }
                }else{
                    return Common::jsonFormat('500','图片上传出错,请重试！');
                }
            }

        }else{
            return Common::jsonFormat('500','Method方法只能为POST');
        }
    }

    /**
     * @param Request $request
     * @return string
     * excel导出
     */
    public function excelExport(Request $request)
    {
        $file_name = date('Y-m-d-H-i-s').'-'.uniqid();
        $overseas = new OverseasController;
        $order_list = $overseas->orderList($request);
        $cell_data = $order_list->original['data'][0];

        $data = ['id','用户id','订单号','商品id','订单金额','定金','优惠券','实付金额','支付方式','下单时间','支付时间','订单状态','病人姓名','关系','性别','生日','电话','邮箱','创建日期','修改日期','用户名','商品名'];
        array_unshift($cell_data,$data);

        Excel::create($file_name, function($excel) use ($cell_data){
            $excel->sheet('sheet1', function($sheet) use ($cell_data){
                $sheet->rows($cell_data);
            });
        })->store('xls');

        $realPath = storage_path('app/exports/'.$file_name.'.xls');
        $fdsk = new Fdfs();
        $url = $fdsk->upload($realPath,'xls');
        return $url['url'];
    }

    /**
     * @return array|false|null|string
     * 获取客户端ip
     */
    public static function getClientIp() {
        $unknown = 'unknown';
        if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        /*
        处理多层代理的情况
        或者使用正则方式：$ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
        */
        if (false !== strpos($ip, ','))
            $ip = reset(explode(',', $ip));
        return $ip;
    }

    /**
     * 密码加密
     * @param $data
     * @return string
     */
    public static function mymd5_4($data) {
        //先得到密码的密文
        $data = md5($data);
        //再把密文中的英文母全部转为大写
        $data = strtoupper($data);
        //最后再进行一次MD5运算并返回
        return strtoupper(md5($data));
    }


    /**
     * @param $len
     * @param bool $isnum
     * @return bool|string
     * 生成随机字符串
     */
    public static function randomStr($len,$isnum=false)
    {
        $str = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';//62个字符
        $strlen = strlen($str);
        if($isnum)
        {
            $str = '1234567890';
            $strlen = strlen($str);
        }
        while($len > $strlen){
            $str .= $str;
            $strlen += strlen($str);
        }
        $str = str_shuffle($str);
        return substr($str,0,$len);
    }

    public static function createSessionKey($upper = FALSE, $hyphen = "") {
        $charid = md5(uniqid(mt_rand(), true));
        if ($upper) {
            $charid = strtoupper($charid);
        }
        $session_key = substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        return $session_key;
    }

    /**
     * @param int $size  此参数为二维码的尺寸，是像素值
     * @param string $info 此参数为二维码的内容，内容为文字时，扫描展示的是此文字；
     *       内容为http://或https://开头的网址时，打开的就是此网址
     * @return mixed
     *
     */
    public static function createQrCode($size,$info)
    {
        $size = $size ?: 400;
        $info = $info ?: "success";
        $res = QrCode::size($size)->color(50,255,100)->generate($info);

        return $res;
    }

    /**
     * @return string
     * 根据不同的环境，包括本地、测试、正式，返回对应环境的接口地址
     */
    public static function environmentUrl()
    {
        $host = $_SERVER['HTTP_HOST'];
        $local = '/^localhost.*|^127\.0\.0\.1.*/';
        $test = '/\.cn$/';
        $production = '/\.com$/';

        if (preg_match($local,$host)){
            return 'local';
        }elseif (preg_match($test,$host)){
            return 'testing';
        }elseif (preg_match($production,$host)){
            return 'production';
        }else{
            return '';
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * 生成并输出图片验证码
     */
    public function showCaptcha(Request $request)
    {
        $builder = new CaptchaBuilder();

        $builder->build()->save('captcha.jpg');

        $phrase = $builder->getPhrase();

        //把生成的验证码的值存入session中
        $request->session()->put('captcha',$phrase);

        return $builder->inline();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Config\Repository|mixed
     * 获取微信公众号配置（绿通微信）----只允许正式域名且为手机设备
     */
    public function getWechatConfig(Request $request)
    {
//        Cache::forget('wx_jsapiTicket');
//     Cache::forget('wx_token');

        $weixin = new WeixinController();
        $data = $weixin->getSignpackage();
        return $data;

       /** $url = $request->url();
        $url = urldecode($url);
        Log::info('url'.$url);

        $userAgent = $request->userAgent();

        $url_match = '/rongyao2018.huobanys.com/';
        $agent_match = '/phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone/';

//        if (preg_match($agent_match,$userAgent) && preg_match($url_match,$url)) {
//        if (preg_match($agent_match,$userAgent)) {

            try{
                $appid = config('wechat')['appid'];
                $curtime = time();
                $noncestr = self::randomStr('32');
                $weixin = new WeixinController();
                $jsapi_ticket = $weixin->getJsapiTicket();
                $string1 = 'jsapi_ticket=' . $jsapi_ticket . '&noncestr=' . $noncestr . '&timestamp=' . $curtime . '&url=' . $url;

                $data = [];
                $data['appId'] = $appid;
                $data['timestamp'] = $curtime;
                $data['nonceStr'] = $noncestr;
                $data['signature'] = sha1($string1);

                return $data;
            } catch (\Exception $e){
                Log::error($e);
            }

//        }else{
//            return 'server reject';
//        }
        */
    }

}