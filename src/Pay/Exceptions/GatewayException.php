<?php

/**
 * 微信/支付宝支付
 * ============================================================================
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: Atom
 * GatewayException.php  2019/01/01/21/021 9:53
 */

namespace Liwanyi\Utils2\Pay\Exceptions;

/**
 * 支付网关异常类
 * Class GatewayException
 * @package Pay\Exceptions
 */
class GatewayException extends Exception
{
    /**
     * error raw data.
     * @var array
     */
    public $raw = [];

    /**
     * GatewayException constructor.
     * @param string $message
     * @param int $code
     * @param array $raw
     */
    public function __construct($message, $code, $raw = [])
    {
        parent::__construct($message, intval($code));
        $this->raw = $raw;
    }
}
