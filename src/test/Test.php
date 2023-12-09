<?php

namespace Liwanyi\Utils2\test;

use Liwanyi\Utils2\Lock;

require "./vendor/autoload.php";
$Test = new Lock();
$a = $Test->Lock('prize',30);
var_dump($a);