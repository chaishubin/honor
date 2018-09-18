<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeixinController extends Controller
{
    private $appid = 'wxf3d9a8f9eda2476a';
    private $secret = '55ef38723202f3c7173191b62a640046';
    private $token = 'U6Kv055IF';
    private $aeskey = '2vVCdSJBnNChx1Z03VyookNjWJPkS8NTGo7TfC9bi8F';
    private $base_uri = 'https://api.weixin.qq.com';


    /**
     * @return mixed
     * 获取access_token
     */
    public function getAccessToken() {
//        $data = $this->getFile($this->accessTokenFile);
        $data = Cache::get('wx_token');
        if(time() - $data['time'] > 0){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->secret}";

            $client = new Client();
            $res = $client->request('GET',$url)->getBody()->getContents();
            $access_token = json_decode($res,true)['access_token'];
            if(isset($access_token)){
                $data['access_token']  = $access_token;
                $data['time'] = time() + 7200;
//                $this->setFile($this->accessTokenFile,json_encode($data));
                Cache::put('wx_token',json_encode($data));
            }
        }else{
            $access_token = $data['access_token'];
        }
        return $access_token;
    }


    /**
     * @return mixed
     * 获取微信 JsapiTicket
     */
    public function getJsapiTicket() {
        $access_token = $this->getAccessToken();
//        $jsapi_ticket = $this->getFile($this->jsapiTicketFile);
        $jsapi_ticket = Cache::get('wx_jsapiTicket');
        if(time() - $jsapi_ticket['time'] > 0) {
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
//            $re = $this->httpGet($url);
//            $this->preArr($re);
            $client = new Client();
            $res = $client->request('GET',$url)->getBody()->getContents();
//            $jsapi_ticket = $re['ticket'];
            $jsapi_ticket = json_decode($res,true)['ticket'];
            if(isset($jsapi_ticket)){
                $data['jsapi_ticket'] = $jsapi_ticket;
                $data['time'] = time() + 7200;
//                $this->setFile($this->jsapiTicketFile, json_encode($data));
                Cache::put('wx_jsapiTicket',json_encode($data));
            }
        }else{
            $jsapi_ticket = $jsapi_ticket['jsapi_ticket'];
        }
        return $jsapi_ticket;
    }

    /**
     * [getSignpackage description] 获取签名
     * @return [type] [description]
     */
    public function getSignpackage(){
        $jsapi_ticket = $this->getJsapiTicket();    // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
//        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url = "https://rongyao2018.huobanys.com";
        Log::info('nurl'.$url);
//        $noncestr = $this->createNonceStr();
        $noncestr = Common::randomStr('32');
        $timestamp = time();

        Log::info('n$jsapi_ticket'.$jsapi_ticket);
        Log::info('n$noncestr'.$noncestr);
        Log::info('n$timestamp'.$timestamp);
        $string1 = "jsapi_ticket={$jsapi_ticket}&noncestr={$noncestr}&timestamp={$timestamp}&url={$url}";
        $signature = sha1($string1);
        $signPackage = array(
            'appId'     => $this->appid,
            'nonceStr'  => $noncestr,
            'timestamp' => $timestamp,
            'signature' => $signature,
        );
        return $signPackage;
    }


}
