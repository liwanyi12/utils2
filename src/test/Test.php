<?php

namespace Liwanyi\Utils2\test;
// 一定要进行加载引入
require_once __DIR__ . '/../../vendor/autoload.php';

use Liwanyi\Utils2\Common;
use Liwanyi\Utils2\Json;
use Liwanyi\Utils2\Redis;

class Test
{

    public function test()
    {
        $result = (new Common())->createOrderNo();
        var_dump($result);
    }

//    public function ToPay($config = [], $total_price = 0, $notify_url = '', $return_url = '', $ssl_cer = '', $ssl_key = '', $cache_path = './backup',)
//    {
//
//        $mch_config = [
//            'app_id' => 'wx5caca2f298cadb66',
//            'appsecret' => 'df627d62c3c654e06dd9aa33786dd1ed',
//            'mch_id' => '1677993324',
//            'mch_key' => 'xBiPsCZbS9yn0JtvkWdD83rYL6TaueoX',
//        ];
//
//        if (!$ssl_cer || !$ssl_key) {
//            throw new \RuntimeException('请把证件上传地址');
//        }
//
//        $payment_config = ['wechat' => array_merge([
//            'debug' => false,
//            'cache_path' => './backup',
//            'notify_url' => $notify_url,
//            'return_url' => $return_url,
//            'ssl_cer' => '/www/wwwroot/new.xinxiangbm.com/addons/epay/certs/apiclient_cert.pem',
//            'ssl_key' => '/www/wwwroot/new.xinxiangbm.com/addons/epay/certs/apiclient_key.pem',
//        ], $mch_config)];
//
//        $total_fee = $total_price * 100;
//        $pay_fee = $total_price * 100;
//        $options = [
//            'transaction_id' => '4200002641202503137641919192',
//            'out_refund_no' => '2025031357505210',
//            'total_fee' => $total_fee,
//            'refund_fee' => $pay_fee,
//        ];
//
//        $pay = new \Liwanyi\Utils2\Pay\Pay($payment_config);
////        $code = $pay->driver('wechat')->gateway('Mp')->refund($options);
//        $code = $pay->driver('wechat')->gateway('Mp')->apply($options);
//        var_dump($code);
//    }

}

$result = (new Test())->test();
var_dump($result);