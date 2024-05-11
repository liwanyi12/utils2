<?php

namespace Liwanyi\Utils2\test;

use Liwanyi\Utils2\Redis;

$Test = new Redis();
//$a = $Test->setValue('prize',30);
//echo $a;
//$b = $Test->getValue('prize');
//var_dump($b);
$Test->addLocation('mylist','116.397128','39.916527','张三',);
$Test->addLocation('mylist','116.397128','39.716527','王五',);
$Test->addLocation('mylist','116.397128','39.816527','李四',);
var_dump($Test);












