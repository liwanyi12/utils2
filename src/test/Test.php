<?php

namespace Liwanyi\Utils2\test;

use Liwanyi\Utils2;
use Liwanyi\Utils2\Redis;



class Test{
    public function test(){
        return [
            ['name'=>'张三','price'=>210,'num'=>9],
            ['name'=>'张三','price'=>225,'num'=>8],
            ['name'=>'张三','price'=>226,'num'=>7],
            ['name'=>'张三','price'=>227,'num'=>6],
            ['name'=>'张三','price'=>228,'num'=>5],
            ['name'=>'张三','price'=>229,'num'=>4],
        ];
    }
}
$result = (new Test())->test();
Utils2\ArrayHelper::arraySortByKey($result,'price',SORT_DESC);
print_r($result);











