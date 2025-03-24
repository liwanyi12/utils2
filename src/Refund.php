<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;

use Liwanyi\Utils2\Pay\Pay;

class Refund
{
    protected $order;
    protected $config;


    public function __construct($order, $config)
    {
        $this->order = $order;
        $this->config = $config;
    }

    public function refundMoney() // 默认h5
    {
        $site = [
            "app_id" => $this->config['app_id'],// appid
            "app_secret" => $this->config['app_secret'],// appsecret
            "mch_id" => $this->config['mch_id'],// 商户字符串
            "mch_key" => $this->config['mch_key'],// 32位字符串
        ];
        $type = $this->config['type']; // 支付类型
        $url = $_SERVER['HTTP_HOST'];
        $payment_config = [$type => array_merge([
            'debug' => false, // 是否开启沙箱模式
            'cache_path' => './backup',
            'notify_url' => $this->config['notify_url'], // 异步地址
            'return_url' => $url,
            'ssl_cer' => $this->config['cert'], // 证书在服务器的路径
            'ssl_key' => $this->config['key'],
        ], $site)];

        $options = [
            'transaction_id' => $this->order['transaction_id'], // 订单的贸易号（从微信获取）
            'out_trade_no' =>$this->order['out_trade_no'] , // 退款订单号
            'out_refund_no' => Common::createOrderNo(), // 自定义退款订单号
            'total_fee' => $this->order['total_fee'] * 100,// 全部的金额
            'refund_fee' =>$this->order['refund_fee'] * 100, // 退款的金额
        ];

        $pay = new Pay($payment_config);
        $code = $pay->driver($type)->gateway($this->config['method'])->refund($options);
        if ($code['return_code'] == 'SUCCESS' && $code['return_msg'] == 'OK') {
            return true;
        }
        return false;
    }
}