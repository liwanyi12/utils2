<?php



namespace Liwanyi\Utils2\Pay\Gateways\Wechat;

use Liwanyi\Utils2\Pay\Gateways\Wechat;

/**
 * 下载微信电子面单
 * Class BillGateway
 * @package Pay\Gateways\Wechat
 */
class BillGateway extends Wechat
{

    /**
     * 当前操作类型
     * @return string
     */
    protected function getTradeType()
    {
        return '';
    }

    /**
     * 应用并返回参数
     * @param array $options
     * @return bool|string
     */
    public function apply(array $options)
    {
        unset($this->config['trade_type']);
        unset($this->config['notify_url']);
        $this->config = array_merge($this->config, $options);
        $this->config['sign'] = $this->getSign($this->config);
        return $this->post($this->gateway_bill, $this->toXml($this->config));
    }
}