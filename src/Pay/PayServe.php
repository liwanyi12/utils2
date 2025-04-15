<?php

namespace Liwanyi\Utils2\Pay;

class PayServe
{
    const CHANNEL_WECHAT = 'wechat';
    const CHANNEL_ALIPAY = 'alipay';

    // 微信支持的网关映射
    const WECHAT_GATEWAYS = [
        'app'       => 'App',        // APP支付
        'bank'      => 'Bank',       // 银行卡支付
        'bill'      => 'Bill',       // 账单支付
        'miniapp'   => 'Miniapp',    // 小程序支付
        'mp'        => 'Mp',         // 公众号支付
        'pos'       => 'Pos',        // 刷卡支付
        'scan'      => 'Scan',       // 扫码支付
        'transfer'  => 'Transfer',   // 企业付款
        'wap'       => 'Wap',        // H5支付
        'web'       => 'Web'         // PC网站支付
    ];

    const ALIPAY_GATEWAYS = [
        'app' => 'App',   // APP支付
        'wap' => 'Wap',   // 手机网站支付
        'web' => 'Web'    // 电脑网站支付
    ];

    /**
     * 统一支付入口
     * @param array  $config      支付配置
     * @param array  $order       订单数据
     * @param string $notify_url  异步通知地址
     * @param string $body/subject 订单描述
     * @param string $channel     支付渠道 wechat/alipay
     * @param string $gateway     支付网关类型
     * @return mixed
     * @throws \Exception
     */
    public function unifiedPay(
        $config,
        $order,
        $notify_url,
        $body,
        $channel = self::CHANNEL_WECHAT,
        $gateway = null
    ) {
        // 参数基础校验
        $this->validateParams($channel, $gateway);

        // 获取支付配置
        $paymentConfig = $this->getConfig($config, $notify_url, $channel, $gateway);

        // 初始化支付实例
        $pay = new Pay($paymentConfig);
        $driver = $pay->driver($channel);

        // 构建网关参数
        $gateway = $this->getGatewayName($channel, $gateway);
        $options = $this->buildPaymentOptions($channel, $gateway, $order, $body, $notify_url);

        try {
            // 特殊网关处理
            switch ($gateway) {
                case 'Transfer': // 微信企业付款
                    return $driver->gateway($gateway)->pay($options);
                case 'Pos':     // 刷卡支付
                    return $driver->gateway($gateway)->pay([
                        'auth_code' => $order['auth_code'],
                        ...$options
                    ]);
                default:        // 常规支付
                    return $driver->gateway($gateway)->apply($options);
            }
        } catch (\Exception $e) {
            throw new \Exception("Payment Error: " . $e->getMessage(), 500);
        }
    }

    /**
     * 构建支付配置
     */
    protected function getConfig($customConfig, $notifyUrl, $channel, $gateway)
    {
        $baseConfig = [
            'debug'       => $customConfig['debug'] ?? false,
            'cache_path'  => $customConfig['cache_path'] ?? './runtime/pay_cache',
            'notify_url'  => $notifyUrl,
            'return_url'  => $customConfig['return_url'] ?? $_SERVER['HTTP_REFERER'],
        ];

        // 微信配置
        $wechatConfig = [
            'app_id'      => $customConfig['wechat_app_id'],
            'mch_id'      => $customConfig['wechat_mch_id'],
            'mch_key'     => $customConfig['wechat_mch_key'],
            'ssl_cer'     => $customConfig['wechat_ssl_cer'],
            'ssl_key'    => $customConfig['wechat_ssl_key'],
            'sub_appid'   => $customConfig['sub_appid'] ?? '', // 子商户配置
        ];

        // 支付宝配置
        $alipayConfig = [
            'app_id'            => $customConfig['alipay_app_id'],
            'ali_public_key'   => $customConfig['alipay_public_key'],
            'rsa_private_key'   => $customConfig['alipay_private_key'],
            'sandbox'           => $customConfig['alipay_sandbox'] ?? false,
        ];

        // 合并渠道配置
        $channelConfig = [
            self::CHANNEL_WECHAT => array_merge($baseConfig, $wechatConfig),
            self::CHANNEL_ALIPAY => array_merge($baseConfig, $alipayConfig)
        ];

        return [$channel => $channelConfig[$channel]];
    }

    /**
     * 构建支付参数
     */
    private function buildPaymentOptions($channel, $gateway, $order, $body, $notifyUrl)
    {
        $baseParams = [
            'out_trade_no'  => $order['order_no'],
            'total_amount'   => $this->formatAmount($channel, $order['pay_price']),
            'body'          => $body,
            'subject'       => $body,
            'notify_url'    => $notifyUrl
        ];

        // 微信参数处理
        if ($channel === self::CHANNEL_WECHAT) {
            $extendParams = [
                'total_fee' => $this->formatAmount($channel, $order['pay_price']),
                'openid'    => $order['openid'] ?? '',
                'scene_info'=> $this->getWechatSceneInfo($gateway)
            ];

            // 特殊网关参数
            switch ($gateway) {
                case 'H5':
                    $extendParams['type'] = 'Wap';
                    break;
                case 'Miniapp':
                    $extendParams['sub_appid'] = $config['sub_appid'] ?? '';
                    break;
            }
        }

        // 支付宝参数处理
        if ($channel === self::CHANNEL_ALIPAY) {
            $extendParams = [
                'product_code' => $this->getAlipayProductCode($gateway),
                'quit_url'     => $order['quit_url'] ?? ''
            ];
        }

        return array_merge($baseParams, $extendParams ?? []);
    }

    /**
     * 获取微信场景信息
     */
    private function getWechatSceneInfo($gateway)
    {
        $sceneMap = [
            'Wap' => ['h5_info' => ['type' => 'Wap']],
            'Web' => ['h5_info' => ['type' => 'Web']]
        ];

        return isset($sceneMap[$gateway]) ? json_encode($sceneMap[$gateway]) : '';
    }

    /**
     * 获取支付宝产品码
     */
    private function getAlipayProductCode($gateway)
    {
        $codeMap = [
            'App' => 'QUICK_MSECURITY_PAY',
            'Wap' => 'QUICK_WAP_WAY',
            'Web' => 'FAST_INSTANT_TRADE_PAY'
        ];

        return $codeMap[$gateway] ?? 'QUICK_WAP_WAY';
    }

    /**
     * 金额格式化
     */
    private function formatAmount($channel, $amount)
    {
        return $channel === self::CHANNEL_WECHAT
            ? (int)($amount * 100)  // 微信单位：分
            : (float)sprintf('%.2f', $amount); // 支付宝单位：元
    }

    /**
     * 参数校验
     */
    private function validateParams($channel, &$gateway)
    {
        $gateways = $channel === self::CHANNEL_WECHAT
            ? array_keys(self::WECHAT_GATEWAYS)
            : array_keys(self::ALIPAY_GATEWAYS);

        // 设置默认网关
        if (empty($gateway)) {
            $gateway = $channel === self::CHANNEL_WECHAT ? 'miniapp' : 'wap';
        }

        if (!in_array($gateway, $gateways)) {
            throw new \InvalidArgumentException("Unsupported gateway for {$channel}");
        }
    }

    /**
     * 获取规范化的网关名称
     */
    private function getGatewayName($channel, $gateway)
    {
        $map = $channel === self::CHANNEL_WECHAT
            ? self::WECHAT_GATEWAYS
            : self::ALIPAY_GATEWAYS;

        return $map[$gateway] ?? $gateway;
    }




    /**
     * 统一退款接口
     * @param array  $config      支付配置
     * @param array  $refundData  退款数据
     * @param string $channel     支付渠道 wechat/alipay
     * @return mixed
     * @throws \Exception
     */
    public function unifiedRefund(
        $config,
        $refundData,
        $channel = self::CHANNEL_WECHAT
    ) {
        // 参数校验
        $this->validateRefundParams($refundData, $channel);

        // 获取支付配置
        $paymentConfig = $this->getConfig(
            $config,
            $refundData['notify_url'] ?? '', // 退款通知地址
            $channel,
            'refund' // 特殊网关标识
        );

        // 初始化支付实例
        $pay = new Pay($paymentConfig);
        $driver = $pay->driver($channel);

        try {
            // 构造退款参数
            $options = $this->buildRefundOptions($channel, $refundData);

            // 执行退款
            return $driver->gateway('refund')->refund($options);
        } catch (\Exception $e) {
            throw new \Exception("Refund failed: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * 构建退款参数
     */
    private function buildRefundOptions($channel, $refundData)
    {
        $baseParams = [
            'out_trade_no'  => $refundData['order_no'],       // 原支付订单号
            'out_refund_no' => $refundData['refund_no'],       // 退款单号
            'total_amount'  => $this->formatAmount($channel, $refundData['total_amount']), // 原订单金额
            'refund_amount' => $this->formatAmount($channel, $refundData['refund_amount']),// 退款金额
            'refund_reason' => $refundData['reason'] ?? '用户申请退款',
        ];

        // 渠道特定参数
        $channelParams = [];
        if ($channel === self::CHANNEL_WECHAT) {
            $channelParams = [
                'total_fee' => $this->formatAmount($channel, $refundData['total_amount']),
                'refund_fee' => $this->formatAmount($channel, $refundData['refund_amount']),
                'notify_url' => $refundData['notify_url'] ?? '',
                'type' => $refundData['type'] ?? null // 退款账户类型：REFUND_SOURCE_UNSETTLED_FUNDS等
            ];
        }

        if ($channel === self::CHANNEL_ALIPAY) {
            $channelParams = [
                'refund_currency' => 'CNY',
                'operator_id' => $refundData['operator'] ?? '',
                'store_id' => $refundData['store_id'] ?? '',
            ];
        }

        return array_merge($baseParams, $channelParams);
    }

    /**
     * 退款参数校验
     */
    private function validateRefundParams($refundData, $channel)
    {
        $required = [
            'order_no',      // 原支付订单号
            'refund_no',     // 退款单号
            'total_amount',  // 原订单金额
            'refund_amount'  // 退款金额
        ];

        foreach ($required as $field) {
            if (empty($refundData[$field])) {
                throw new \InvalidArgumentException("Missing required refund field: {$field}");
            }
        }

        // 金额校验
        if ($refundData['refund_amount'] > $refundData['total_amount']) {
            throw new \InvalidArgumentException("Refund amount cannot exceed total amount");
        }

        // 微信部分退款校验
        if ($channel === self::CHANNEL_WECHAT &&
            $refundData['refund_amount'] < $refundData['total_amount'] &&
            empty($refundData['refund_id'])
        ) {
            throw new \InvalidArgumentException("Partial refund requires refund_id for WeChat");
        }
    }


    //// 微信小程序支付
    //$payResult = (new PayServe())->unifiedPay(
    //    $config,
    //    [
    //        'order_no' => 'WX20230801123456',
    //        'pay_price' => 99.99,
    //        'openid' => 'o7mZt5XXXXX'
    //    ],
    //    'https://yourdomain.com/notify',
    //    '小程序商品购买',
    //    PayServe::CHANNEL_WECHAT,
    //    'miniapp'
    //);
    //
    //// 支付宝APP支付
    //$payResult = (new PayServe())->unifiedPay(
    //    $config,
    //    [
    //        'order_no' => 'ALI202308019876',
    //        'pay_price' => 199.99
    //    ],
    //    'https://yourdomain.com/notify',
    //    'APP会员充值',
    //    PayServe::CHANNEL_ALIPAY,
    //    'app'
    //);
    //
    //// 微信H5支付
    //$h5Params = (new PayServe())->unifiedPay(
    //    $config,
    //    [
    //        'order_no' => 'WXH520230801',
    //        'pay_price' => 66.66
    //    ],
    //    'https://yourdomain.com/notify',
    //    'H5商城订单',
    //    PayServe::CHANNEL_WECHAT,
    //    'wap'
    //);
}