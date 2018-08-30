<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Overtrue\EasySms\EasySms;

class SmsController extends Controller
{
    public static function sendMessage(Request $request, $phone_number)
    {
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'yuntongxun',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'yuntongxun' => [
                    'app_id' => '8aaf070865796a57016584a5b6b209b1',
                    'account_sid' => '8a216da858ce0b3c0158d858552007ae',
                    'account_token' => '935f27ac69d840c9acc4d795224045c4',
                    'is_sub_account' => false,
                ],
            ],
        ];

        if (!$phone_number){
            return false;
        }

        try {
            $easySms = new EasySms($config);

            //4 位随机验证码
            $code = str_pad(rand(1,9999),4,rand(0,9));

            $request->session()->put('sms_code',$code);

            $easySms->send($phone_number, [
                'template' => '321042', //荣耀医者2018 荣耀医者2018 验证码
                'data' => [$code],
            ]);

            return true;

        }catch (\Exception $e){
            Log::info($e->getException('yuntongxun'));
            return false;
        }
    }
}
