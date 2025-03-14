<?php


namespace Liwanyi\Utils2\Pay\Gateways\Wechat;

use Liwanyi\Utils2\Pay\Exceptions\GatewayException;

use Liwanyi\Utils2\Pay\Gateways\Wechat;

/**
 * 微信企业打款网关
 * Class TransferGateway
 * @package Pay\Gateways\Wechat
 */
class TransferGateway extends Wechat
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
     * 应用并返回数据
     * @param array $options
     * @return array
     * @throws GatewayException
     */
    public function apply(array $options = [])
    {
        $options['mchid'] = $this->config['mch_id'];
        $options['mch_appid'] = $this->userConfig->get('app_id');
        unset($this->config['appid']);
        unset($this->config['mch_id']);
        unset($this->config['sign_type']);
        unset($this->config['trade_type']);
        unset($this->config['notify_url']);
        $this->config = array_merge($this->config, $options);
        $this->config['sign'] = $this->getSign($this->config);
        $data = $this->fromXml($this->post(
            $this->gateway_transfer, $this->toXml($this->config),
            [
                'ssl_cer' => $this->userConfig->get('ssl_cer', ''),
                'ssl_key' => $this->userConfig->get('ssl_key', ''),
            ]
        ));
        if (!isset($data['return_code']) || $data['return_code'] !== 'SUCCESS' || $data['result_code'] !== 'SUCCESS') {
            $error = 'GetResultError:' . $data['return_msg'];
            $error .= isset($data['err_code_des']) ? ' - ' . $data['err_code_des'] : '';
        }
        if (isset($error)) {
            throw new GatewayException($error, 20001, $data);
        }
        return $data;
    }
}
