<?php
declare(strict_types=1);

namespace Liwanyi\Utils2;

use Cassandra\RetryPolicy;

class ArrayHelper
{

    /**
     * 获取数组第一个值  reset() array_values($arr)[0] Arr::first() 都可以获取到第一个数组的值
     * @param object|array $data
     * @return mixed
     */
    public static function getFirstValue(object|array $data): mixed
    {
        return reset($data);
    }


    /**
     * 把字符串打散为数组
     * @param string $separator
     * @param string $string
     * @return string|string[]
     */
    public static function explodeValue(string $separator, string $string)
    {
        if (empty($data)) {
            return $string;
        }

        return explode($separator, $string);
    }

    public static function implodeValue(array|string $separator = "", ?array $array)
    {
        if (empty($array) || $array == '') {
            return $array;
        }

        return implode($separator, $array);
    }



}










