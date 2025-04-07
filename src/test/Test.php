<?php

namespace Liwanyi\Utils2\test;
// 一定要进行加载引入
require_once __DIR__ . '/../../vendor/autoload.php';

use Liwanyi\Utils2\Math;
use Liwanyi\Utils2\Pay\Pay;
use Liwanyi\Utils2\Refund;

class Test
{
    public function test()
    {
        $longword = "Supercalifragilisticexpialidocious";
        echo wordwrap($longword, 10, "\n", true);exit();
        $data = (new Pay(['a'=>1]))->driver('Wechat');
        var_dump($data);exit();

    }

}

$result = (new Test())->test();
var_dump($result);