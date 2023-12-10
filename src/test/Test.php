<?php

namespace Liwanyi\Utils2\test;

use Liwanyi\Utils2\Lock;
use Liwanyi\Utils2\Redis;

require "./vendor/autoload.php";
$Test = new Redis();
$a = $Test->setValue('prize',30);
echo $a;
$b = $Test->getValue('prize');
var_dump($b);