<?php



namespace Liwanyi\Utils2\Pay\Gateways\Alipay;

use Liwanyi\Utils2\Pay\Gateways\Alipay;

/**
 * 手机WAP支付网关
 * Class WapGateway
 * @package Pay\Gateways\Alipay
 */
class WapGateway extends Alipay
{

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'alipay.trade.wap.pay';
    }

    /**
     * 当前接口产品码
     * @return string
     */
    protected function getProductCode()
    {
        return 'QUICK_WAP_WAY';
    }

    /**
     * 应用并返回参数
     * @param array $options
     * @return string
     */
    public function apply(array $options = [])
    {
        parent::apply($options);
        return $this->buildPayHtml();
    }
}
