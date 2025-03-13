<?php


namespace Liwanyi\Utils2\Pay\Gateways\Wechat;

use Liwanyi\Utils2\Pay\Gateways\Wechat;

/**
 * 微信扫码支付网关
 * Class ScanGateway
 * @package Pay\Gateways\Wechat
 */
class ScanGateway extends Wechat
{

    /**
     * 当前操作类型
     * @return string
     */
    protected function getTradeType()
    {
        return 'NATIVE';
    }

    /**
     * 应用并返回参数
     * @param array $options
     * @return mixed
     * @throws \Pay\Exceptions\GatewayException
     */
    public function apply(array $options = [])
    {
        $data = $this->preOrder($options);
        return empty($data['code_url']) ? false : $data['code_url'];
    }
}
