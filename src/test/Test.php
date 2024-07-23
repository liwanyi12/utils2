<?php

namespace Liwanyi\Utils2\test;

use Liwanyi\Utils2;
use Liwanyi\Utils2\Redis;
class Test{
    public function test(){
        $redis = new Redis();
       return $redis->hset('name','liwanyi',1);
    }

}
$result = (new Test())->test();
var_dump($result);










