<?php



namespace Liwanyi\Utils2\Pay\Gateways\Alipay;

use Liwanyi\Utils2\Pay\Gateways\Alipay;

/**
 * 支付宝电子面单下载
 * Class BillGateway
 * @package Pay\Gateways\Alipay
 */
class BillGateway extends Alipay
{

    /**
     * 当前接口方法
     * @return string
     */
    protected function getMethod()
    {
        return 'alipay.data.dataservice.bill.downloadurl.query';
    }


    /**
     * 应用并返回参数
     * @return array|bool
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