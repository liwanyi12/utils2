<?php

namespace Liwanyi\Utils2;

class Date
{
    /**
     * 获取两个日期相差多少天
     *
     * @param string $day1 日期1
     * @param string $day2 日期2
     * @return int
     */
    public static function diff(string $day1, string $day2 = ''): int
    {
        if (!$day2) {
            $day2 = date('Y-m-d');
        }

        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            [$second1, $second2] = [$second2, $second1];
        }

        return intval(ceil(($second1 - $second2) / 86400));
    }

    /**
     * 返回截止到今天晚上零点之前的秒数
     * @return int 秒数
     */
    public static function secondEndToday(): int
    {
        list($y, $m, $d) = explode('-', date('Y-m-d'));
        return mktime(23, 59, 59, intval($m), intval($d), intval($y)) - time();
    }
}