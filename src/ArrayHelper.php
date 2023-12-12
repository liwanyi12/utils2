<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;

use Cassandra\RetryPolicy;

class ArrayHelper
{

    /**
     * 获取数组第一个值  reset() array_values($arr)[0] Arr::first() 都可以获取到第一个数组的值
     * @param array $data
     * @return mixed
     */
    public static function getFirstValue(array $data): mixed
    {
        return reset($data);
    }


}










