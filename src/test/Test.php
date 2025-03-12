<?php
namespace Liwanyi\Utils2\test;
// 一定要进行加载引入
require_once __DIR__ . '/../../vendor/autoload.php';

use Liwanyi\Utils2\ArrayHelper;
use Liwanyi\Utils2\StringHelper;

class Test{
    
    public function test(){
        $data = [
            '1'=>'榨干啊',
            '2'=>'里斯'
        ];
        return ArrayHelper::getFirstValue($data);

//        return StringHelper::explodeString('123456');
    }
}
$result = (new Test())->test();
var_dump($result);