<?php


namespace Liwanyi\Utils2\Pay\Gateways\Alipay;

use Liwanyi\Utils2\Pay\Gateways\Alipay;

/**
 * 支付宝转账网关
 * Class TransferGateway
 * @package Pay\Gateways\Alipay
 */
class TransferGateway extends Alipay
{

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'alipay.fund.trans.toaccount.transfer';
    }

    /**
     * 当前接口产品码
     * @return string
     */
    protected function getProductCode()
    {
        return '';
    }

    /**
     * 应用并返回参数
     * @param array $options
     * @return array|bool
     * @throws \Pay\Exceptions\GatewayException
     */
    public function apply(array $options = [])
    {
        return $this->getResult($options, $this->getMethod());
    }
}
